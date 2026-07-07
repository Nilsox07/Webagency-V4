(function () {
  'use strict';

  // Selbst-Editor (Stufe 1): rendert Felder aus dem Schema, speichert automatisch,
  // Bild-Upload + Mediathek, Veröffentlichen. Design bleibt geschützt — der Kunde
  // füllt nur Felder, er baut kein Layout.

  var API = 'api/portal/content.php';
  var csrf = '';
  var state = { page: null, template: null, content: {}, media: [], palette: [], preview: false, project_id: '' };
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
    if (state.preview) { status('Vorschau-Modus — Änderungen werden nicht gespeichert.'); return; }
    pendingSave[section + '.' + field] = { section: section, field: field, wert: value };
    status('Speichert …');
    clearTimeout(saveTimer);
    saveTimer = setTimeout(flushSave, 700);
  }
  function flushSave() {
    var fields = Object.keys(pendingSave).map(function (k) { return pendingSave[k]; });
    if (!fields.length) return;
    pendingSave = {};
    post({ action: 'save', fields: fields }).then(function () {
      status('Automatisch gespeichert ✓');
    }).catch(function (e) { status('Konnte nicht speichern: ' + e.message); });
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
    input.addEventListener('input', function () { setVal(section, field.key, input.value); });
    w.appendChild(input);
    return w;
  }

  function colorControl(section, field) {
    var w = labelWrap(field);
    var row = el('div', 'ed-palette');
    var cur = getVal(section, field.key);
    state.palette.forEach(function (c) {
      var b = el('button', 'ed-swatch' + (cur === c.value ? ' is-on' : ''));
      b.type = 'button'; b.style.background = c.value; b.title = c.label;
      b.addEventListener('click', function () {
        setVal(section, field.key, c.value);
        Array.prototype.forEach.call(row.children, function (x) { x.classList.remove('is-on'); });
        b.classList.add('is-on');
      });
      row.appendChild(b);
    });
    w.appendChild(row);
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

  function render() {
    var root = document.getElementById('editorRoot');
    if (!root) return;
    root.innerHTML = '';
    if (!state.template) { root.appendChild(el('p', 'muted', 'Noch keine Website hinterlegt.')); return; }
    state.template.sections.forEach(function (section) {
      var card = el('div', 'card ed-section');
      card.appendChild(el('h3', null, section.label));
      if (section.help) card.appendChild(el('p', 'muted ed-sec-help', section.help));
      section.fields.forEach(function (field) { card.appendChild(buildField(section.key, field)); });
      root.appendChild(card);
    });
  }

  // ---- Mediathek / Upload ----
  function mediaById(id) { for (var i = 0; i < state.media.length; i++) if (state.media[i].id === id) return state.media[i]; return null; }

  function openMediaPicker(onChoose) {
    var overlay = el('div', 'ed-modal-bg');
    var box = el('div', 'ed-modal');
    box.appendChild(el('h3', null, 'Bild wählen'));
    var grid = el('div', 'ed-media-grid');
    function paintGrid() {
      grid.innerHTML = '';
      if (!state.media.length) grid.appendChild(el('p', 'muted', 'Noch keine Bilder. Laden Sie eins hoch.'));
      state.media.filter(function (m) { return (m.mime || '').indexOf('image/') === 0; }).forEach(function (m) {
        var t = el('button', 'ed-media-thumb'); t.type = 'button';
        var img = el('img'); img.src = m.url; img.alt = m.alt_text || ''; t.appendChild(img);
        t.addEventListener('click', function () { onChoose(m.id); close(); });
        grid.appendChild(t);
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
    var pub = document.getElementById('edPublish');
    if (pub) pub.addEventListener('click', function () {
      if (state.preview) { status('Vorschau-Modus — Veröffentlichen ist hier deaktiviert.'); return; }
      flushSave();
      pub.disabled = true; status('Veröffentlicht …');
      post({ action: 'publish' }).then(function () { status('Ihre Website ist aktualisiert und live ✓'); }).catch(function (e) { status('Fehler: ' + e.message); }).finally(function () { pub.disabled = false; });
    });
  }

  // ---- Start ----
  function boot(data) {
    state.page = data.page || null;
    state.template = data.template || null;
    state.content = data.content || {};
    state.media = data.media || [];
    state.palette = data.palette || [];
    state.project_id = data.page ? data.page.project_id : '';
    render();
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
          { key: 'hero', label: 'Kopfbereich', help: 'Das Erste, was Besucher sehen.', fields: [
            { key: 'headline', label: 'Überschrift', type: 'text', max: 90 },
            { key: 'subline', label: 'Unterzeile', type: 'textarea', max: 220 },
            { key: 'bild', label: 'Hauptbild', type: 'image' },
            { key: 'cta_text', label: 'Button-Text', type: 'text', max: 40 }
          ] },
          { key: 'leistungen', label: 'Leistungen', fields: [
            { key: 'titel', label: 'Titel', type: 'text', max: 90 },
            { key: 'items', label: 'Einträge', type: 'list', max_items: 12, item: [
              { key: 'titel', label: 'Titel', type: 'text', max: 80 },
              { key: 'text', label: 'Beschreibung', type: 'textarea', max: 300 }
            ] }
          ] },
          { key: 'oeffnungszeiten', label: 'Öffnungszeiten', fields: [
            { key: 'zeiten', label: 'Zeiten', type: 'hours' }
          ] },
          { key: 'design', label: 'Design', help: 'Nur die Akzentfarbe ist wählbar.', fields: [
            { key: 'akzentfarbe', label: 'Akzentfarbe', type: 'color' }
          ] }
        ]
      },
      content: {
        hero: { headline: 'Muster Bäckerei', subline: 'Frisch & handgemacht, seit 1985.', cta_text: 'Jetzt anrufen' },
        leistungen: { titel: 'Unser Angebot', items: [{ titel: 'Brot & Brötchen', text: 'Täglich frisch aus dem Ofen.' }] },
        oeffnungszeiten: { zeiten: { Montag: '7–18 Uhr', Samstag: '7–13 Uhr' } },
        design: { akzentfarbe: '#b45309' }
      },
      media: []
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
