(function () {
  'use strict';

  var PHASES = ['angebot_bestaetigt', 'inhalte_liefern', 'design_laeuft', 'korrektur_1', 'korrektur_2', 'korrektur_3', 'korrektur_4', 'finalisierung', 'live'];
  var PHASE_LABEL = {
    angebot_bestaetigt: 'Angebot bestätigt',
    inhalte_liefern: 'Inhalte liefern',
    design_laeuft: 'Design läuft',
    korrektur_1: 'Korrektur 1',
    korrektur_2: 'Korrektur 2',
    korrektur_3: 'Korrektur 3',
    korrektur_4: 'Korrektur 4',
    finalisierung: 'Finalisierung',
    live: 'Live'
  };
  var STATUS = ['neu', 'in_bearbeitung', 'umgewandelt', 'abgelehnt'];
  var state = { briefings: [], projects: [], profiles: [] };
  var csrfToken = '';

  var err = document.getElementById('err');
  var modalBg = document.getElementById('modalBg');
  var modalBox = document.getElementById('modalBox');

  function api(url, options) {
    options = options || {};
    options.credentials = 'same-origin';
    options.headers = Object.assign({ Accept: 'application/json' }, options.headers || {});
    if (csrfToken && String(options.method || 'GET').toUpperCase() !== 'GET') {
      options.headers['X-CSRF-Token'] = csrfToken;
    }
    return fetch(url, options).then(function (r) {
      return r.json().catch(function () { return {}; }).then(function (data) {
        if (data.csrf) csrfToken = data.csrf;
        if (!r.ok || data.ok === false) throw new Error(data.error || ('HTTP ' + r.status));
        return data;
      });
    });
  }
  function send(url, data, method) {
    return api(url, {
      method: method || 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data || {})
    });
  }
  function showErr(msg) {
    if (!err) return;
    err.textContent = msg;
    err.classList.remove('hidden');
    setTimeout(function () { err.classList.add('hidden'); }, 7000);
  }
  function esc(s) {
    return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
  }
  function fmtDate(d) {
    try {
      return new Date(d).toLocaleString('de-DE', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
    } catch (e) { return d || ''; }
  }
  function profileById(id) {
    return state.profiles.filter(function (p) { return p.id === id; })[0] || null;
  }
  function cap(s) {
    s = String(s || '');
    return s ? s.charAt(0).toUpperCase() + s.slice(1) : '';
  }
  function payloadOf(b) {
    return b && typeof b.payload === 'object' && b.payload ? b.payload : {};
  }
  function contactOf(b) {
    var p = payloadOf(b);
    return p.kontakt || { name: b.kontakt_name, email: b.kontakt_email };
  }
  function configOf(b) {
    return payloadOf(b).konfiguration || {};
  }
  function openModal(html) {
    modalBox.innerHTML = html;
    modalBg.classList.add('is-open');
  }
  function closeModal() {
    modalBg.classList.remove('is-open');
    modalBox.innerHTML = '';
  }

  if (modalBg) {
    modalBg.addEventListener('click', function (e) {
      if (e.target === modalBg) closeModal();
    });
  }

  function requireAdmin() {
    return api('api/auth/me.php').then(function (data) {
      var profile = data.profile || {};
      document.getElementById('gate').classList.add('hidden');
      if (!data.authenticated || profile.role !== 'admin') {
        document.getElementById('denied').classList.remove('hidden');
        setTimeout(function () { window.location.href = 'login.php'; }, 1600);
        return false;
      }
      document.getElementById('app').classList.remove('hidden');
      return true;
    }).catch(function () {
      window.location.href = 'login.php';
      return false;
    });
  }

  function loadAll() {
    return Promise.all([
      api('api/admin/briefings.php'),
      api('api/admin/projects.php'),
      api('api/admin/customers.php')
    ]).then(function (out) {
      state.briefings = out[0].briefings || [];
      state.projects = out[1].projects || [];
      state.profiles = out[2].profiles || out[1].profiles || [];
      renderBriefings();
      renderProjects();
      renderCustomers(out[2].projects || []);
    }).catch(function (e) {
      showErr(e.message);
    });
  }

  function renderBriefings() {
    var body = document.getElementById('anfragenBody');
    if (!body) return;
    if (!state.briefings.length) {
      body.innerHTML = '<tr><td colspan="4" class="muted">Noch keine Anfragen.</td></tr>';
      return;
    }
    body.innerHTML = '';
    state.briefings.forEach(function (b) {
      var k = contactOf(b);
      var tr = document.createElement('tr');
      tr.className = 'clickable';
      tr.innerHTML = '<td>' + esc(fmtDate(b.created_at)) + '</td><td>' + esc(k.name || b.kontakt_name || '-') + '</td><td>' + esc(k.email || b.kontakt_email || '-') + '</td><td><span class="badge">' + esc(b.status || 'neu') + '</span></td>';
      tr.addEventListener('click', function () { openBriefing(b); });
      body.appendChild(tr);
    });
  }

  function openBriefing(b) {
    var k = contactOf(b);
    var cfg = configOf(b);
    var anfrage = payloadOf(b).anfrage || {};
    var once = cfg.summe_einmalig ? Number(cfg.summe_einmalig).toLocaleString('de-DE') + ' €' : 'noch offen';
    var monthly = cfg.summe_monatlich ? Number(cfg.summe_monatlich).toLocaleString('de-DE') + ' €/Mon.' : 'noch offen';
    openModal(
      '<div class="spread"><h2 style="color:#fff">Website-Anfrage</h2><button class="btn btn-ghost btn-sm" id="aClose">Schließen</button></div>' +
      '<p class="muted">' + esc(fmtDate(b.created_at)) + ' · ' + esc(k.name || '-') + ' · ' + esc(k.email || '-') + '</p>' +
      '<div class="grid-2" style="gap:12px; margin:16px 0;">' +
        '<div class="card"><p class="eyebrow">Empfehlung</p><h3>' + esc(cfg.paket_name || cfg.paket || 'Individuell') + '</h3><p class="muted">' + esc(once) + ' · ' + esc(monthly) + '</p></div>' +
        '<div class="card"><p class="eyebrow">Kontakt</p><h3>' + esc(k.name || '-') + '</h3><p class="muted">' + esc(k.email || '-') + (k.telefon ? ' · ' + esc(k.telefon) : '') + '</p></div>' +
      '</div>' +
      '<div class="field"><label>Status</label><select id="aStatus">' + STATUS.map(function (s) { return '<option value="' + s + '"' + (s === b.status ? ' selected' : '') + '>' + s + '</option>'; }).join('') + '</select></div>' +
      '<div class="field"><label>Projektname</label><input id="aTitle" type="text" value="' + esc((k.name || 'Kunde') + ' Website') + '"></div>' +
      '<div class="row row-end"><button class="btn btn-ghost" id="aSave">Status speichern</button><button class="btn btn-primary" id="aConvert">In Projekt umwandeln</button></div>' +
      '<details style="margin-top:18px;"><summary>Alle Angaben anzeigen</summary><pre style="white-space:pre-wrap; color:#d6f6e6; font-size:12px;">' + esc(JSON.stringify({ anfrage: anfrage, konfiguration: cfg }, null, 2)) + '</pre></details>'
    );
    document.getElementById('aClose').addEventListener('click', closeModal);
    document.getElementById('aSave').addEventListener('click', function () {
      send('api/admin/briefings.php', { id: b.id, status: document.getElementById('aStatus').value }, 'PATCH')
        .then(function () { closeModal(); return loadAll(); })
        .catch(function (e) { showErr(e.message); });
    });
    document.getElementById('aConvert').addEventListener('click', function () {
      send('api/admin/convert.php', {
        briefing_id: b.id,
        email: k.email || b.kontakt_email || '',
        name: k.name || b.kontakt_name || '',
        titel: document.getElementById('aTitle').value || 'Website-Projekt',
        paket: cfg.paket || '',
        care_stufe: cfg.wartung_name || cfg.wartung || ''
      }).then(function (res) {
        closeModal();
        if (!res.invite_sent) showErr('Projekt erstellt. Die Login-Mail konnte noch nicht versendet werden. Bitte Mailversand prüfen.');
        return loadAll();
      }).catch(function (e) { showErr(e.message); });
    });
  }

  function renderProjects() {
    var body = document.getElementById('projekteBody');
    if (!body) return;
    if (!state.projects.length) {
      body.innerHTML = '<tr><td colspan="4" class="muted">Noch keine Projekte.</td></tr>';
      return;
    }
    body.innerHTML = '';
    state.projects.forEach(function (p) {
      var customer = profileById(p.customer_id) || {};
      var tr = document.createElement('tr');
      tr.className = 'clickable';
      tr.innerHTML = '<td>' + esc(p.titel || '-') + '</td><td>' + esc(customer.name || customer.email || '-') + '</td><td>' + esc(cap(p.paket)) + '</td><td><span class="badge">' + esc(PHASE_LABEL[p.phase] || p.phase) + '</span></td>';
      tr.addEventListener('click', function () { openProject(p); });
      body.appendChild(tr);
    });
  }

  function openProject(p) {
    var customer = profileById(p.customer_id) || {};
    openModal(
      '<div class="spread"><h2 style="color:#fff">' + esc(p.titel || 'Projekt') + '</h2><button class="btn btn-ghost btn-sm" id="pClose">Schließen</button></div>' +
      '<p class="muted">' + esc(customer.name || customer.email || '-') + '</p>' +
      '<div class="field"><label>Phase</label><select id="pPhase">' + PHASES.map(function (ph) { return '<option value="' + ph + '"' + (ph === p.phase ? ' selected' : '') + '>' + (PHASE_LABEL[ph] || ph) + '</option>'; }).join('') + '</select></div>' +
      '<div class="field"><label>Liefertermin</label><input id="pTermin" type="date" value="' + esc((p.liefertermin || '').slice(0, 10)) + '"></div>' +
      '<div class="field"><label>Notiz für Kunde</label><textarea id="pNotizK" rows="4">' + esc(p.notiz_kunde || '') + '</textarea></div>' +
      '<div class="field"><label>Interne Notiz</label><textarea id="pNotizI" rows="4">' + esc(p.notiz_intern || '') + '</textarea></div>' +
      '<div class="row row-end"><button class="btn btn-primary" id="pSave">Projekt speichern</button></div>'
    );
    document.getElementById('pClose').addEventListener('click', closeModal);
    document.getElementById('pSave').addEventListener('click', function () {
      send('api/admin/projects.php', {
        id: p.id,
        phase: document.getElementById('pPhase').value,
        liefertermin: document.getElementById('pTermin').value || null,
        notiz_kunde: document.getElementById('pNotizK').value || '',
        notiz_intern: document.getElementById('pNotizI').value || ''
      }, 'PATCH').then(function () { closeModal(); return loadAll(); }).catch(function (e) { showErr(e.message); });
    });
  }

  function renderCustomers(projectRows) {
    var body = document.getElementById('kundenBody');
    if (!body) return;
    var count = {};
    (projectRows || state.projects).forEach(function (p) { count[p.customer_id] = (count[p.customer_id] || 0) + 1; });
    if (!state.profiles.length) {
      body.innerHTML = '<tr><td colspan="5" class="muted">Noch keine Kunden.</td></tr>';
      return;
    }
    body.innerHTML = '';
    state.profiles.forEach(function (p) {
      var tr = document.createElement('tr');
      tr.className = 'clickable';
      tr.innerHTML = '<td>' + esc(p.name || '-') + '</td><td>' + esc(p.email || '-') + '</td><td>' + esc(p.firma || '-') + '</td><td>' + esc(p.role || 'customer') + '</td><td>' + (count[p.id] || 0) + '</td>';
      tr.addEventListener('click', function () { openCustomer(p); });
      body.appendChild(tr);
    });
  }

  function openCustomer(p) {
    openModal(
      '<div class="spread"><h2 style="color:#fff">' + esc(p.name || p.email || 'Kunde') + '</h2><button class="btn btn-ghost btn-sm" id="kClose">Schließen</button></div>' +
      '<p class="muted">' + esc(p.email || '') + ' · Rolle: ' + esc(p.role || 'customer') + '</p>' +
      '<div class="field"><label>Name</label><input id="kName" type="text" value="' + esc(p.name || '') + '"></div>' +
      '<div class="field"><label>Firma</label><input id="kFirma" type="text" value="' + esc(p.firma || '') + '"></div>' +
      '<div class="field"><label>Telefon</label><input id="kTel" type="text" value="' + esc(p.telefon || '') + '"></div>' +
      '<div class="row row-end"><button class="btn btn-primary" id="kSave">Stammdaten speichern</button></div>'
    );
    document.getElementById('kClose').addEventListener('click', closeModal);
    document.getElementById('kSave').addEventListener('click', function () {
      send('api/admin/customers.php', {
        id: p.id,
        name: document.getElementById('kName').value || '',
        firma: document.getElementById('kFirma').value || '',
        telefon: document.getElementById('kTel').value || ''
      }, 'PATCH').then(function () { closeModal(); return loadAll(); }).catch(function (e) { showErr(e.message); });
    });
  }

  Array.prototype.forEach.call(document.querySelectorAll('.tab'), function (btn) {
    btn.addEventListener('click', function () {
      Array.prototype.forEach.call(document.querySelectorAll('.tab'), function (b) { b.classList.toggle('is-on', b === btn); });
      ['anfragen', 'projekte', 'kunden'].forEach(function (name) {
        var section = document.getElementById('tab-' + name);
        if (section) section.classList.toggle('hidden', btn.getAttribute('data-tab') !== name);
      });
    });
  });

  var logout = document.getElementById('logoutBtn');
  if (logout) {
    logout.addEventListener('click', function () {
      send('api/auth/logout.php', {}).finally(function () { window.location.href = 'login.php'; });
    });
  }

  requireAdmin().then(function (ok) {
    if (ok) loadAll();
  });
})();
