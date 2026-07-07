(function () {
  'use strict';

  // Stufe-2-Briefing (Portal-Onboarding). Schritt-für-Schritt, Auto-Speichern,
  // Datei-Upload über die vorhandene Upload-Pipeline. Sammelt Rohmaterial für den Bau.

  var API = 'api/portal/briefing.php';
  var csrf = '';
  var schema = { steps: [] };
  var state = { answers: {}, project_id: '', step: 0, preview: false, status: 'offen' };
  var saveTimer = null;

  function $(id) { return document.getElementById(id); }
  function el(tag, cls, txt) { var e = document.createElement(tag); if (cls) e.className = cls; if (txt != null) e.textContent = txt; return e; }
  function saveState(t) { var s = $('obSaveState'); if (s) s.textContent = t || ''; }
  function showErr(m) { var e = $('err'); if (!e) return; e.textContent = m; e.classList.remove('hidden'); setTimeout(function () { e.classList.add('hidden'); }, 6000); }

  function api(url, options) {
    options = options || {}; options.credentials = 'same-origin';
    options.headers = Object.assign({ Accept: 'application/json' }, options.headers || {});
    if (csrf && String(options.method || 'GET').toUpperCase() !== 'GET') options.headers['X-CSRF-Token'] = csrf;
    return fetch(url, options).then(function (r) {
      return r.json().catch(function () { return {}; }).then(function (d) {
        if (d.csrf) csrf = d.csrf;
        if (!r.ok || d.ok === false) throw new Error(d.error || ('HTTP ' + r.status));
        return d;
      });
    });
  }
  function post(body) { return api(API, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(body) }); }

  function get(key) { return state.answers[key]; }
  function set(key, val) { state.answers[key] = val; queueSave(); }

  function queueSave() {
    if (state.preview) { saveState('Vorschau — nicht gespeichert'); return; }
    saveState('Speichert …');
    clearTimeout(saveTimer);
    saveTimer = setTimeout(function () {
      post({ action: 'save', answers: state.answers }).then(function () { saveState('Automatisch gespeichert ✓'); })
        .catch(function (e) { saveState('Fehler: ' + e.message); });
    }, 700);
  }
  function flush() {
    clearTimeout(saveTimer);
    if (state.preview) return Promise.resolve();
    return post({ action: 'save', answers: state.answers }).catch(function () {});
  }

  // ---- Feld-Steuerelemente ----
  function fieldVisible(field) {
    if (!field.when) return true;
    for (var k in field.when) { if (String(get(k) || '') !== String(field.when[k])) return false; }
    return true;
  }

  function textField(field, multiline) {
    var w = el('div', 'field');
    w.appendChild(el('label', null, field.label));
    if (field.help) w.appendChild(el('span', 'ob-help', field.help));
    var inp = document.createElement(multiline ? 'textarea' : 'input');
    if (!multiline) inp.type = field.type === 'email' ? 'email' : (field.type === 'tel' ? 'tel' : 'text');
    if (multiline) inp.rows = 4;
    if (field.max) inp.maxLength = field.max;
    if (field.placeholder) inp.placeholder = field.placeholder;
    var v = get(field.key); inp.value = v == null ? '' : v;
    inp.addEventListener('input', function () { set(field.key, inp.value); });
    w.appendChild(inp);
    return w;
  }

  function choiceField(field, multi) {
    var w = el('div', 'field');
    w.appendChild(el('label', null, field.label));
    if (field.help) w.appendChild(el('span', 'ob-help', field.help));
    var row = el('div', 'ob-chips');
    (field.options || []).forEach(function (opt) {
      var chip = el('button', 'ob-chip'); chip.type = 'button'; chip.textContent = opt.label;
      function active() {
        if (multi) { var arr = get(field.key); return Array.isArray(arr) && arr.indexOf(opt.value) >= 0; }
        return get(field.key) === opt.value;
      }
      if (active()) chip.classList.add('is-on');
      chip.addEventListener('click', function () {
        if (multi) {
          var arr = Array.isArray(get(field.key)) ? get(field.key).slice() : [];
          var i = arr.indexOf(opt.value);
          if (i >= 0) arr.splice(i, 1); else arr.push(opt.value);
          set(field.key, arr);
          chip.classList.toggle('is-on');
        } else {
          set(field.key, opt.value);
          Array.prototype.forEach.call(row.children, function (c) { c.classList.remove('is-on'); });
          chip.classList.add('is-on');
          renderStep(); // Folgefelder (when) können sich ändern
        }
      });
      row.appendChild(chip);
    });
    w.appendChild(row);
    return w;
  }

  function fileField(field, multi) {
    var w = el('div', 'field');
    w.appendChild(el('label', null, field.label));
    if (field.help) w.appendChild(el('span', 'ob-help', field.help));
    var list = el('div', 'ob-files');
    function paint() {
      list.innerHTML = '';
      var val = get(field.key);
      var items = multi ? (Array.isArray(val) ? val : []) : (val ? [val] : []);
      items.forEach(function (f, idx) {
        var row = el('div', 'ob-file');
        row.appendChild(el('span', null, (f && f.name) ? f.name : 'Datei'));
        var rm = el('button', 'ed-mini ed-mini-del'); rm.type = 'button'; rm.textContent = 'Entfernen';
        rm.addEventListener('click', function () {
          if (multi) { var a = get(field.key).slice(); a.splice(idx, 1); set(field.key, a); } else { set(field.key, ''); }
          paint();
        });
        row.appendChild(rm);
        list.appendChild(row);
      });
    }
    paint();
    w.appendChild(list);

    var pick = el('input'); pick.type = 'file'; pick.style.display = 'none';
    if (field.type === 'files') pick.multiple = true;
    var btn = el('button', 'btn btn-ghost btn-sm'); btn.type = 'button'; btn.textContent = multi ? 'Dateien hinzufügen' : 'Datei wählen';
    btn.addEventListener('click', function () { pick.click(); });
    pick.addEventListener('change', function () {
      var files = Array.prototype.slice.call(pick.files || []);
      files.forEach(function (file) {
        uploadFile(file, function (up) {
          if (multi) { var a = Array.isArray(get(field.key)) ? get(field.key).slice() : []; a.push(up); set(field.key, a); }
          else { set(field.key, up); }
          paint();
        });
      });
      pick.value = '';
    });
    w.appendChild(pick); w.appendChild(btn);
    return w;
  }

  function uploadFile(file, done) {
    if (state.preview) { done({ id: 'demo', name: file.name }); return; }
    saveState('Lädt Datei hoch …');
    var fd = new FormData();
    fd.append('file', file); fd.append('project_id', state.project_id); fd.append('typ', 'briefing');
    var headers = { Accept: 'application/json' }; if (csrf) headers['X-CSRF-Token'] = csrf;
    fetch('api/upload.php', { method: 'POST', credentials: 'same-origin', headers: headers, body: fd })
      .then(function (r) { return r.json().catch(function () { return {}; }).then(function (d) { if (!r.ok || d.ok === false) throw new Error(d.error || ('HTTP ' + r.status)); return d; }); })
      .then(function (d) { if (d.csrf) csrf = d.csrf; saveState('Datei hochgeladen ✓'); done({ id: d.upload.id, name: d.upload.original_name || file.name }); })
      .catch(function (e) { showErr('Upload fehlgeschlagen: ' + e.message); saveState(''); });
  }

  function buildField(field) {
    switch (field.type) {
      case 'textarea': return textField(field, true);
      case 'choice': return choiceField(field, false);
      case 'multi': return choiceField(field, true);
      case 'file': return fileField(field, false);
      case 'files': return fileField(field, true);
      default: return textField(field, false);
    }
  }

  // ---- Schritte ----
  function renderStep() {
    var steps = schema.steps || [];
    var step = steps[state.step]; if (!step) return;
    var body = $('obStepBody'); body.innerHTML = '';
    var card = el('div', 'card');
    card.appendChild(el('h2', null, step.title));
    if (step.help) card.appendChild(el('p', 'muted', step.help));
    step.fields.forEach(function (field) { if (fieldVisible(field)) card.appendChild(buildField(field)); });
    body.appendChild(card);

    var pct = Math.round(((state.step + 1) / steps.length) * 100);
    $('obBar').style.width = pct + '%';
    $('obStepLabel').textContent = 'Schritt ' + (state.step + 1) + ' von ' + steps.length + ' · ' + step.title;
    $('obBack').disabled = state.step === 0;
    $('obNext').textContent = state.step === steps.length - 1 ? 'Briefing absenden' : 'Weiter';
  }

  function next() {
    var steps = schema.steps || [];
    if (state.step < steps.length - 1) {
      flush(); state.step++; window.scrollTo(0, 0); renderStep();
    } else {
      // Absenden
      if (state.preview) { showDone(); return; }
      $('obNext').disabled = true; saveState('Wird gesendet …');
      Promise.resolve(flush()).then(function () { return post({ action: 'submit', answers: state.answers }); })
        .then(function () { showDone(); })
        .catch(function (e) { showErr('Konnte nicht senden: ' + e.message); })
        .finally(function () { $('obNext').disabled = false; });
    }
  }
  function back() { if (state.step > 0) { flush(); state.step--; window.scrollTo(0, 0); renderStep(); } }

  function showApp() { $('gate').classList.add('hidden'); $('app').classList.remove('hidden'); $('done').classList.add('hidden'); }
  function showDone() { $('app').classList.add('hidden'); $('done').classList.remove('hidden'); }

  function init() {
    try { schema = JSON.parse($('briefingSchema').textContent) || { steps: [] }; } catch (e) { schema = { steps: [] }; }
    var logout = $('logoutBtn');
    if (logout) logout.addEventListener('click', function () { api('api/auth/logout.php', { method: 'POST' }).catch(function () {}).then(function () { window.location.href = 'login.php'; }); });
    $('obNext').addEventListener('click', next);
    $('obBack').addEventListener('click', back);
    var again = $('editAgain'); if (again) again.addEventListener('click', function () { showApp(); state.step = 0; renderStep(); });

    if (new URLSearchParams(window.location.search).get('preview') === '1') {
      state.preview = true;
      state.answers = { firmenname: 'Muster Bäckerei', hat_logo: 'ja', stimmung: ['modern', 'warm'], leistungen: 'Brot & Brötchen\nKuchen & Torten' };
      showApp(); renderStep(); saveState('Vorschau-Modus — Ihre Eingaben werden nicht gespeichert.');
      return;
    }

    api(API).then(function (d) {
      if (!d.has_project) { $('gate').innerHTML = '<h2>Kein Projekt</h2><p class="muted">Sobald Ihr Projekt startet, geht es hier los.</p>'; return; }
      state.project_id = d.project_id; state.answers = d.answers || {}; state.status = d.status || 'offen';
      showApp(); renderStep();
      if (state.status === 'abgeschlossen') saveState('Bereits gesendet — Ergänzungen speichern sich automatisch.');
    }).catch(function () { window.location.href = 'login.php'; });
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();
})();
