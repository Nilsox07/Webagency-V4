(function () {
  'use strict';

  // Selbst-Editor (Stufe 1): rendert Felder aus dem Schema, speichert automatisch,
  // Bild-Upload + Mediathek, Veröffentlichen. Design bleibt geschützt — der Kunde
  // füllt nur Felder, er baut kein Layout.

  var API = 'api/portal/content.php';
  var csrf = '';
  var state = { page: null, template: null, content: {}, media: [], palette: [], versions: [], unpublished: false, preview: false, project_id: '' };
  var pendingSave = {}; // "section.field" -> value
  var saveTimer = null;

  function $(sel, el) { return (el || document).querySelector(sel); }
  function el(tag, cls, txt) { var e = document.createElement(tag); if (cls) e.className = cls; if (txt != null) e.textContent = txt; return e; }
  function status(msg) { var s = document.getElementById('edStatus'); if (s) s.textContent = msg; }

  function api(url, options) {
    options = options || {}; options.credentials = 'same-origin';
    options.headers = Object.assign({ Accept: 'application/json' }, options.headers || {});
    if (csrf && String(options.method || 'GET').toUpperCase() !== 'GET') options.headers['X-CSRF-Token'] = csrf;
    return fetch(url, options).then(function (r) {
      return r.json().catch(function () { return {}; }).then(function (data) {
        if (data.csrf) csrf = data.csrf;
        if (!r.ok || data.ok === false) throw new Error(data.error || ('HTTP ' + r.status));
        return data;
      });
    });
  }
  function post(body) {
    return api(API, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(body) });
  }

  // ---- Werte lesen/setzen ----
  function getVal(section, field) {
    return (state.content[section] && state.content[section][field] != null) ? state.content[section][field] : null;
  }
  function setVal(section, field, value) {
    if (!state.content[section]) state.content[section] = {};
    state.content[section][field] = value;
    queueSave(section, field, value);
  }
  function queueSave(section, field, value) {
    if (state.preview) { setUnpublished(true); status('Vorschau-Modus — Änderungen werden nicht gespeichert.'); return; }
    pendingSave[section + '.' + field] = { section: section, field: field, wert: value };
    status('Speichert …');
    clearTimeout(saveTimer);
    saveTimer = setTimeout(flushSave, 700);
  }
  function flushSave() {
    clearTimeout(saveTimer);
    var fields = Object.keys(pendingSave).map(function (k) { return pendingSave[k]; });
    if (!fields.length) return Promise.resolve();
    pendingSave = {};
    return post({ action: 'save', fields: fields }).then(function () {
      status('Automatisch gespeichert ✓');
      setUnpublished(true);
    }).catch(function (e) { status('Konnte nicht speichern: ' + e.message); throw e; });
  }

  function setUnpublished(v) {
    state.unpublished = v;
    var chip = document.getElementById('edUnpub');
    if (chip) chip.hidden = !v;
  }

  // ---- Feld-Steuerelemente ----
  function labelWrap(field) {
    var w = el('div', 'ed-field');
    var lab = el('label', 'ed-label', field.label);
    w.appendChild(lab);
    if (field.help) w.appendChild(el('span', 'ed-help', field.help));
    return w;
  }

  function textControl(section, field, multiline) {
    var w = labelWrap(field);
    var input = document.createElement(multiline ? 'textarea' : 'input');
    if (!multiline) input.type = (field.type === 'email') ? 'email' : (field.type === 'tel' ? 'tel' : (field.type === 'url' ? 'url' : 'text'));
    if (multiline) input.rows = 4;
    if (field.max) input.maxLength = field.max;
    if (field.placeholder) input.placeholder = field.placeholder;
    var v = getVal(section, field.key); input.value = (v == null) ? '' : v;
    var counter = field.max ? el('span', 'ed-count') : null;
    function paintCount() { if (counter) counter.textContent = input.value.length + ' / ' + field.max; }
    input.addEventListener('input', function () { setVal(section, field.key, input.value); paintCount(); });
    w.appendChild(input);
    if (counter) { paintCount(); w.appendChild(counter); }
    if (multiline) w.appendChild(el('span', 'ed-help', 'Tipp: **fett**, *kursiv*, Links werden automatisch erkannt.'));
    return w;
  }

  function normalizeHex(v) {
    v = String(v || '').trim();
    if (/^#[0-9a-fA-F]{3}$/.test(v)) { v = '#' + v[1] + v[1] + v[2] + v[2] + v[3] + v[3]; }
    return /^#[0-9a-fA-F]{6}$/.test(v) ? v.toLowerCase() : '#0f766e';
  }
  function isHex(v) { return /^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/.test(String(v || '')); }

  function colorControl(section, field) {
    var w = labelWrap(field);
    var cur = getVal(section, field.key) || '#0f766e';

    var top = el('div', 'ed-color');
    var picker = el('input'); picker.type = 'color'; picker.className = 'ed-color-picker'; picker.value = normalizeHex(cur);
    var hex = el('input'); hex.type = 'text'; hex.className = 'ed-color-hex'; hex.maxLength = 7; hex.value = cur; hex.setAttribute('aria-label', 'Hex-Code');
    top.appendChild(picker); top.appendChild(hex);

    var presets = el('div', 'ed-palette');
    function markPresets(v) {
      Array.prototype.forEach.call(presets.children, function (x) { x.classList.toggle('is-on', x.getAttribute('data-c') === v); });
    }
    function apply(v, from) {
      v = String(v).toLowerCase();
      setVal(section, field.key, v);
      if (from !== 'picker') picker.value = normalizeHex(v);
      if (from !== 'hex') hex.value = v;
      markPresets(v);
    }
    picker.addEventListener('input', function () { apply(picker.value, 'picker'); });
    hex.addEventListener('input', function () {
      var v = hex.value.trim(); if (v && v[0] !== '#') v = '#' + v;
      if (isHex(v)) apply(v, 'hex');
    });

    (state.palette || []).forEach(function (c) {
      var b = el('button', 'ed-swatch' + (cur === c.value ? ' is-on' : ''));
      b.type = 'button'; b.style.background = c.value; b.title = c.label; b.setAttribute('data-c', c.value);
      b.addEventListener('click', function () { apply(c.value, 'preset'); });
      presets.appendChild(b);
    });

    w.appendChild(top);
    w.appendChild(presets);
    return w;
  }

  var DAYS = ['Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag', 'Sonntag'];
  function hoursControl(section, field) {
    var w = labelWrap(field);
    var data = getVal(section, field.key); if (!data || typeof data !== 'object') data = {};
    var grid = el('div', 'ed-hours');
    DAYS.forEach(function (day) {
      var line = el('div', 'ed-hours-row');
      line.appendChild(el('span', 'ed-hours-day', day));
      var input = el('input'); input.type = 'text'; input.placeholder = '8–18 Uhr / geschlossen';
      input.value = data[day] || '';
      input.addEventListener('input', function () { data[day] = input.value; setVal(section, field.key, data); });
      line.appendChild(input);
      grid.appendChild(line);
    });
    w.appendChild(grid);
    return w;
  }

  function imageControl(section, field, current, onPick) {
    var w = labelWrap(field);
    var box = el('div', 'ed-image');
    function paint() {
      box.innerHTML = '';
      var ref = current();
      var m = ref ? mediaById(ref) : null;
      if (m) {
        var img = el('img'); img.src = m.url; img.alt = m.alt_text || ''; box.appendChild(img);
      } else {
        box.appendChild(el('span', 'ed-image-empty', 'Kein Bild'));
      }
      var btn = el('button', 'btn btn-ghost btn-sm'); btn.type = 'button'; btn.textContent = m ? 'Bild ändern' : 'Bild wählen';
      btn.addEventListener('click', function () { openMediaPicker(function (id) { onPick(id); paint(); }); });
      box.appendChild(btn);
      if (m) {
        var rm = el('button', 'btn btn-ghost btn-sm'); rm.type = 'button'; rm.textContent = 'Entfernen';
        rm.addEventListener('click', function () { onPick(null); paint(); });
        box.appendChild(rm);
      }
    }
    paint();
    w.appendChild(box);
    return w;
  }

  function listControl(section, field) {
    var w = labelWrap(field);
    var items = getVal(section, field.key); if (!Array.isArray(items)) items = [];
    var listEl = el('div', 'ed-list');

    function commit() { setVal(section, field.key, items); }
    function redraw() {
      listEl.innerHTML = '';
      items.forEach(function (item, idx) {
        var card = el('div', 'ed-list-item');
        var head = el('div', 'ed-list-head');
        head.appendChild(el('span', 'ed-list-nr', '#' + (idx + 1)));
        var tools = el('div', 'ed-list-tools');
        var up = el('button', 'ed-mini'); up.type = 'button'; up.textContent = '↑'; up.disabled = idx === 0;
        up.addEventListener('click', function () { var t = items[idx - 1]; items[idx - 1] = items[idx]; items[idx] = t; commit(); redraw(); });
        var down = el('button', 'ed-mini'); down.type = 'button'; down.textContent = '↓'; down.disabled = idx === items.length - 1;
        down.addEventListener('click', function () { var t = items[idx + 1]; items[idx + 1] = items[idx]; items[idx] = t; commit(); redraw(); });
        var del = el('button', 'ed-mini ed-mini-del'); del.type = 'button'; del.textContent = 'Löschen';
        del.addEventListener('click', function () { items.splice(idx, 1); commit(); redraw(); });
        tools.appendChild(up); tools.appendChild(down); tools.appendChild(del);
        head.appendChild(tools); card.appendChild(head);

        field.item.forEach(function (sub) {
          if (sub.type === 'image') {
            card.appendChild(imageControl(section, sub, function () { return item[sub.key] || null; }, function (id) { item[sub.key] = id; commit(); }));
          } else {
            var sw = labelWrap(sub);
            var inp = document.createElement(sub.type === 'textarea' ? 'textarea' : 'input');
            if (sub.type !== 'textarea') inp.type = 'text';
            if (sub.max) inp.maxLength = sub.max;
            if (sub.placeholder) inp.placeholder = sub.placeholder;
            inp.value = item[sub.key] || '';
            inp.addEventListener('input', function () { item[sub.key] = inp.value; commit(); });
            sw.appendChild(inp); card.appendChild(sw);
          }
        });
        listEl.appendChild(card);
      });
    }
    redraw();
    w.appendChild(listEl);

    var add = el('button', 'btn btn-ghost btn-sm'); add.type = 'button'; add.textContent = '+ Eintrag hinzufügen';
    add.addEventListener('click', function () {
      var max = field.max_items || 50;
      if (items.length >= max) { status('Maximal ' + max + ' Einträge.'); return; }
      items.push({}); commit(); redraw();
    });
    w.appendChild(add);
    return w;
  }

  function buildField(section, field) {
    switch (field.type) {
      case 'textarea': return textControl(section, field, true);
      case 'color': return colorControl(section, field);
      case 'hours': return hoursControl(section, field);
      case 'list': return listControl(section, field);
      case 'image': return imageControl(section, field, function () { return getVal(section, field.key); }, function (id) { setVal(section, field.key, id); });
      default: return textControl(section, field, false);
    }
  }

  var VERSION_LABEL = { vor_veroeffentlichung: 'vor einer Veröffentlichung', vor_rueckgaengig: 'vor einem Rückgängig' };
  function fmtWhen(s) {
    try { return new Date((s || '').replace(' ', 'T')).toLocaleString('de-DE', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' }); }
    catch (e) { return s || ''; }
  }

  // Ein-/Aus-Schalter (Checkbox als Schieber).
  function switchEl(labelText, checked, onChange) {
    var wrap = el('label', 'ed-switch');
    var cb = el('input'); cb.type = 'checkbox'; cb.checked = checked;
    cb.addEventListener('change', function () { onChange(cb.checked); });
    var slider = el('span', 'ed-switch-slider');
    wrap.appendChild(cb); wrap.appendChild(slider);
    if (labelText) wrap.appendChild(el('span', 'ed-switch-label', labelText));
    return wrap;
  }
  function isSectionOn(key) { return String(getVal(key, '__aktiv') || '1') !== '0'; }

  function sectionSwitch(section) {
    return switchEl('Anzeigen', isSectionOn(section.key), function (next) {
      if (!state.content[section.key]) state.content[section.key] = {};
      state.content[section.key]['__aktiv'] = next ? '1' : '0';
      if (state.preview) { setUnpublished(true); return; }
      post({ action: 'toggle_section', section: section.key, aktiv: next })
        .then(function () { setUnpublished(true); status('Gespeichert ✓'); })
        .catch(function (e) { status('Fehler: ' + e.message); });
    });
  }

  function firstText(item, subfields) {
    for (var i = 0; i < subfields.length; i++) {
      if (subfields[i].type !== 'image' && item[subfields[i].key]) return item[subfields[i].key];
    }
    return '';
  }

  function renderToggleSection(card, section) {
    var listField = null;
    section.fields.forEach(function (f) { if (f.type === 'list' && f.toggle_items) listField = f; });
    if (!listField) { card.appendChild(el('p', 'muted', 'Nichts zum Ein-/Ausblenden.')); return; }
    var items = getVal(section.key, listField.key); if (!Array.isArray(items)) items = [];
    if (!items.length) { card.appendChild(el('p', 'muted', 'Noch keine Einträge. Neue fügt Sartu für Sie hinzu.')); return; }
    var list = el('div', 'ed-toggle-list');
    items.forEach(function (item, idx) {
      var row = el('div', 'ed-toggle-row');
      row.appendChild(el('span', 'ed-toggle-label', firstText(item, listField.item) || ('Eintrag ' + (idx + 1))));
      var on = !(item && item._aktiv === false);
      row.appendChild(switchEl('', on, function (next) {
        item._aktiv = next;
        if (state.preview) { setUnpublished(true); return; }
        post({ action: 'toggle_item', section: section.key, field: listField.key, index: idx, aktiv: next })
          .then(function () { setUnpublished(true); status('Gespeichert ✓'); })
          .catch(function (e) { status('Fehler: ' + e.message); });
      }));
      list.appendChild(row);
    });
    card.appendChild(list);
  }

  function render() {
    var root = document.getElementById('editorRoot');
    if (!root) return;
    root.innerHTML = '';
    if (!state.template) { root.appendChild(el('p', 'muted', 'Noch keine Website hinterlegt.')); return; }
    var shown = 0;
    state.template.sections.forEach(function (section) {
      var mode = section.customer;
      if (mode !== 'edit' && mode !== 'toggle') return; // Marketing/SEO = Sartu, nicht im Kunden-Editor
      shown++;
      var card = el('div', 'card ed-section');
      var head = el('div', 'ed-section-head');
      head.appendChild(el('h3', null, section.label));
      if (section.hideable) head.appendChild(sectionSwitch(section));
      card.appendChild(head);
      var help = section.customer_help || section.help;
      if (help) card.appendChild(el('p', 'muted ed-sec-help', help));
      if (mode === 'edit') {
        section.fields.forEach(function (field) { card.appendChild(buildField(section.key, field)); });
      } else {
        renderToggleSection(card, section);
      }
      root.appendChild(card);
    });
    if (!shown) root.appendChild(el('p', 'muted', 'Für Ihre Website gibt es aktuell nichts selbst zu bearbeiten.'));
    renderVersions(root);
  }

  function renderVersions(root) {
    if (!state.versions || !state.versions.length) return;
    var card = el('div', 'card ed-section');
    card.appendChild(el('h3', null, 'Frühere Stände'));
    card.appendChild(el('p', 'muted ed-sec-help', 'Jede Veröffentlichung wird gesichert. Sie können jederzeit auf einen früheren Stand zurück.'));
    var list = el('div', 'ed-versions');
    state.versions.forEach(function (v) {
      var rowEl = el('div', 'ed-version');
      rowEl.appendChild(el('span', null, fmtWhen(v.created_at) + ' · Stand ' + (VERSION_LABEL[v.anlass] || v.anlass || '')));
      var btn = el('button', 'btn btn-ghost btn-sm'); btn.type = 'button'; btn.textContent = 'Wiederherstellen';
      btn.addEventListener('click', function () {
        if (state.preview) { status('Vorschau-Modus — Wiederherstellen ist hier deaktiviert.'); return; }
        if (!window.confirm('Diesen früheren Stand als Entwurf laden? Ihre aktuellen, nicht veröffentlichten Änderungen werden ersetzt.')) return;
        status('Stellt wieder her …');
        post({ action: 'restore', version_id: v.id }).then(function () { return reload(); }).then(function () {
          setUnpublished(true); status('Früherer Stand geladen ✓ — prüfen und dann veröffentlichen.');
        }).catch(function (e) { status('Fehler: ' + e.message); });
      });
      rowEl.appendChild(btn);
      list.appendChild(rowEl);
    });
    card.appendChild(list);
    root.appendChild(card);
  }

  // ---- Mediathek / Upload ----
  function mediaById(id) { for (var i = 0; i < state.media.length; i++) if (state.media[i].id === id) return state.media[i]; return null; }

  function saveAlt(id, alt) {
    var m = mediaById(id); if (m) m.alt_text = alt;
    if (state.preview) return Promise.resolve();
    return post({ action: 'alt', upload_id: id, alt_text: alt }).catch(function () {});
  }
  function deleteMedia(id) {
    state.media = state.media.filter(function (m) { return m.id !== id; });
    if (state.preview) return Promise.resolve();
    return post({ action: 'delete_media', upload_id: id }).catch(function () {});
  }

  function openMediaPicker(onChoose) {
    var overlay = el('div', 'ed-modal-bg');
    var box = el('div', 'ed-modal');
    box.appendChild(el('h3', null, 'Bild wählen'));
    var grid = el('div', 'ed-media-grid');
    var altTimer = {};
    function paintGrid() {
      grid.innerHTML = '';
      var imgs = state.media.filter(function (m) { return (m.mime || '').indexOf('image/') === 0; });
      if (!imgs.length) grid.appendChild(el('p', 'muted', 'Noch keine Bilder. Laden Sie eins hoch.'));
      imgs.forEach(function (m) {
        var cardEl = el('div', 'ed-media-card');
        var t = el('button', 'ed-media-thumb'); t.type = 'button';
        var img = el('img'); img.src = m.url; img.alt = m.alt_text || ''; t.appendChild(img);
        t.addEventListener('click', function () { onChoose(m.id); close(); });
        cardEl.appendChild(t);

        var alt = el('input'); alt.type = 'text'; alt.className = 'ed-media-alt'; alt.value = m.alt_text || '';
        alt.placeholder = 'Bildbeschreibung (Google & Barrierefreiheit)';
        alt.addEventListener('input', function () { clearTimeout(altTimer[m.id]); altTimer[m.id] = setTimeout(function () { saveAlt(m.id, alt.value); }, 600); });
        cardEl.appendChild(alt);

        var row = el('div', 'ed-media-row');
        var use = el('button', 'btn btn-primary btn-sm'); use.type = 'button'; use.textContent = 'Auswählen';
        use.addEventListener('click', function () { onChoose(m.id); close(); });
        var del = el('button', 'ed-mini ed-mini-del'); del.type = 'button'; del.textContent = 'Löschen';
        del.addEventListener('click', function () { if (window.confirm('Bild wirklich löschen?')) { deleteMedia(m.id); paintGrid(); } });
        row.appendChild(use); row.appendChild(del);
        cardEl.appendChild(row);
        grid.appendChild(cardEl);
      });
    }
    paintGrid();

    var actions = el('div', 'ed-modal-actions');
    var upl = el('input'); upl.type = 'file'; upl.accept = 'image/*'; upl.style.display = 'none';
    var uplBtn = el('button', 'btn btn-primary btn-sm'); uplBtn.type = 'button'; uplBtn.textContent = 'Bild hochladen';
    uplBtn.addEventListener('click', function () { upl.click(); });
    upl.addEventListener('change', function () {
      if (!upl.files || !upl.files[0]) return;
      uploadFile(upl.files[0], function (m) { state.media.unshift(m); paintGrid(); });
    });
    var closeBtn = el('button', 'btn btn-ghost btn-sm'); closeBtn.type = 'button'; closeBtn.textContent = 'Schließen';
    closeBtn.addEventListener('click', close);
    actions.appendChild(uplBtn); actions.appendChild(closeBtn);

    box.appendChild(grid); box.appendChild(upl); box.appendChild(actions);
    overlay.appendChild(box);
    overlay.addEventListener('click', function (e) { if (e.target === overlay) close(); });
    document.body.appendChild(overlay);
    function close() { document.body.removeChild(overlay); }
  }

  function uploadFile(file, done) {
    if (state.preview) {
      // Vorschau: lokal anzeigen, nicht hochladen.
      var reader = new FileReader();
      reader.onload = function () { done({ id: 'demo-' + Date.now(), url: reader.result, alt_text: file.name, mime: file.type }); };
      reader.readAsDataURL(file);
      return;
    }
    status('Lädt Bild hoch …');
    var fd = new FormData();
    fd.append('file', file);
    fd.append('project_id', state.project_id);
    fd.append('typ', 'media');
    var headers = { Accept: 'application/json' };
    if (csrf) headers['X-CSRF-Token'] = csrf;
    fetch('api/upload.php', { method: 'POST', credentials: 'same-origin', headers: headers, body: fd })
      .then(function (r) { return r.json().catch(function () { return {}; }).then(function (d) { if (!r.ok || d.ok === false) throw new Error(d.error || ('HTTP ' + r.status)); return d; }); })
      .then(function (d) { if (d.csrf) csrf = d.csrf; status('Bild hochgeladen ✓'); done(d.upload); })
      .catch(function (e) { status('Upload fehlgeschlagen: ' + e.message); });
  }

  // ---- Aktionen ----
  function initActions() {
    // "Unveröffentlichte Änderungen"-Hinweis neben den Buttons einfügen.
    var bar = document.querySelector('.pt-editbar-actions');
    if (bar && !document.getElementById('edUnpub')) {
      var chip = el('span', 'ed-unpub', 'Nicht veröffentlichte Änderungen');
      chip.id = 'edUnpub'; chip.hidden = true;
      bar.insertBefore(chip, bar.firstChild);
    }
    var pub = document.getElementById('edPublish');
    if (pub) pub.addEventListener('click', function () {
      if (state.preview) { status('Vorschau-Modus — Veröffentlichen ist hier deaktiviert.'); return; }
      pub.disabled = true; status('Veröffentlicht …');
      Promise.resolve(flushSave()).then(function () {
        return post({ action: 'publish' });
      }).then(function () {
        setUnpublished(false); status('Ihre Website ist aktualisiert und live ✓');
        return reload();
      }).catch(function (e) { status('Fehler: ' + e.message); }).finally(function () { pub.disabled = false; });
    });
  }

  // ---- Start ----
  function boot(data) {
    state.page = data.page || null;
    state.template = data.template || null;
    state.content = data.content || {};
    state.media = data.media || [];
    state.palette = data.palette || [];
    state.versions = data.versions || [];
    state.project_id = data.page ? data.page.project_id : '';
    render();
    setUnpublished(!!data.unpublished);
  }

  function reload() {
    return api(API).then(function (data) { if (data.has_project) boot(data); return data; });
  }

  function demoData() {
    return {
      page: { project_id: 'demo', vorlage: 'standard', is_published: false },
      palette: [
        { value: '#0f766e', label: 'Petrol' }, { value: '#1d4ed8', label: 'Blau' },
        { value: '#b45309', label: 'Bernstein' }, { value: '#be123c', label: 'Rot' },
        { value: '#7c3aed', label: 'Violett' }, { value: '#15803d', label: 'Grün' }, { value: '#334155', label: 'Schiefer' }
      ],
      template: {
        label: 'Standard', sections: [
          { key: 'leistungen', label: 'Leistungen / Angebot', customer: 'toggle', hideable: true,
            customer_help: 'Einzelne Leistungen können Sie aus- und einblenden. Neue Leistungen oder Textänderungen übernimmt Sartu.', fields: [
              { key: 'items', label: 'Einträge', type: 'list', toggle_items: true, item: [
                { key: 'titel', label: 'Titel', type: 'text', max: 80 }, { key: 'text', label: 'Beschreibung', type: 'textarea', max: 300 }
              ] }
            ] },
          { key: 'oeffnungszeiten', label: 'Öffnungszeiten', customer: 'edit', hideable: true, fields: [
            { key: 'zeiten', label: 'Zeiten', type: 'hours' },
            { key: 'hinweis', label: 'Hinweis', type: 'text', max: 160, placeholder: 'z. B. Betriebsurlaub 1.–14. August' }
          ] },
          { key: 'kontakt', label: 'Kontakt', customer: 'edit', fields: [
            { key: 'adresse', label: 'Adresse', type: 'textarea', max: 240 },
            { key: 'telefon', label: 'Telefon', type: 'tel', max: 60 },
            { key: 'email', label: 'E-Mail', type: 'email', max: 120 }
          ] },
          { key: 'impressum', label: 'Impressum', customer: 'edit', customer_help: 'Gesetzlich vorgeschrieben. Bitte aktuell halten.', fields: [
            { key: 'firmenname', label: 'Firmenname', type: 'text', max: 160 },
            { key: 'inhaber', label: 'Inhaber', type: 'text', max: 160 },
            { key: 'adresse', label: 'Anschrift', type: 'textarea', max: 240 }
          ] },
          { key: 'design', label: 'Design', customer: 'edit', help: 'Ihre Akzentfarbe — Wähler, Hex oder Vorschlag.', fields: [
            { key: 'akzentfarbe', label: 'Akzentfarbe', type: 'color' }
          ] }
        ]
      },
      content: {
        leistungen: { items: [
          { titel: 'Brot & Brötchen', text: 'Täglich frisch aus dem Ofen.' },
          { titel: 'Kuchen & Torten', text: 'Hausgemacht, auch auf Bestellung.' },
          { titel: 'Partyservice', text: 'Belegte Brötchen für Feiern.', _aktiv: false }
        ] },
        oeffnungszeiten: { zeiten: { Montag: '7–18 Uhr', Samstag: '7–13 Uhr' }, hinweis: 'An Feiertagen geschlossen' },
        kontakt: { adresse: 'Hauptstraße 1\n12345 Musterstadt', telefon: '030 123456', email: 'hallo@muster-baeckerei.de' },
        impressum: { firmenname: 'Muster Bäckerei GmbH', inhaber: 'Max Mustermann', adresse: 'Hauptstraße 1\n12345 Musterstadt' },
        design: { akzentfarbe: '#b45309' }
      },
      media: [],
      versions: [{ id: 'demo-v1', created_at: '2026-07-05 09:12:00', anlass: 'vor_veroeffentlichung' }],
      unpublished: false
    };
  }

  function init() {
    initActions();
    var isPreview = new URLSearchParams(window.location.search).get('preview') === '1';
    if (isPreview) {
      state.preview = true;
      var pv = document.getElementById('edPreview'); if (pv) pv.setAttribute('href', 'vorschau.php?preview=1');
      boot(demoData());
      status('Vorschau-Modus mit Beispiel-Inhalten — Änderungen werden nicht gespeichert.');
      return;
    }
    api(API).then(function (data) {
      if (!data.has_project) {
        var root = document.getElementById('editorRoot');
        if (root) root.innerHTML = '<div class="card"><p class="muted">Sobald Ihr Projekt startet, bearbeiten Sie hier Ihre Website.</p></div>';
        return;
      }
      boot(data);
    }).catch(function (e) {
      var root = document.getElementById('editorRoot');
      if (root) root.innerHTML = '<div class="card"><p class="muted">Editor konnte nicht geladen werden: ' + e.message + '</p></div>';
    });
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();
})();
