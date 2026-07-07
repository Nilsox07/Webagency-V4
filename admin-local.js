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
  var OFFER_STATUS = { entwurf: 'Entwurf', gesendet: 'gesendet', angenommen: 'angenommen', abgelehnt: 'abgelehnt' };
  var state = { briefings: [], projects: [], profiles: [], offers: [], invoices: [], _demoBriefing: null };
  var INV_STATUS = { entwurf: 'Entwurf', offen: 'offen', bezahlt: 'bezahlt', storniert: 'storniert' };
  var BRIEFING2 = (function () { try { return JSON.parse(document.getElementById('briefing2Schema').textContent) || { steps: [] }; } catch (e) { return { steps: [] }; } })();
  function euro(n) { return (n == null || n === '') ? '—' : Number(n).toLocaleString('de-DE') + ' €'; }
  function optLabel(f, val) { var o = (f.options || []).filter(function (x) { return x.value === val; })[0]; return o ? o.label : val; }
  function fileLink(x) { var id = (x && typeof x === 'object') ? x.id : x; var nm = (x && typeof x === 'object' && x.name) ? x.name : 'Datei'; return '<a href="api/file.php?id=' + encodeURIComponent(id) + '" target="_blank" rel="noopener">' + esc(nm) + '</a>'; }
  function fmtAns(f, v) {
    if (f.type === 'multi') return (Array.isArray(v) ? v : []).map(function (x) { return esc(optLabel(f, x)); }).join(', ');
    if (f.type === 'choice') return esc(optLabel(f, v));
    if (f.type === 'file') return fileLink(v);
    if (f.type === 'files') return (Array.isArray(v) ? v : []).map(fileLink).join(' · ');
    return esc(String(v)).replace(/\n/g, '<br>');
  }
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
      api('api/admin/customers.php'),
      api('api/admin/offers.php'),
      api('api/admin/invoices.php')
    ]).then(function (out) {
      state.briefings = out[0].briefings || [];
      state.projects = out[1].projects || [];
      state.profiles = out[2].profiles || out[1].profiles || [];
      state.offers = out[3].offers || [];
      state.invoices = out[4].invoices || [];
      renderOverview();
      renderBriefings();
      renderOffers();
      renderInvoicesAdmin();
      renderProjects();
      renderCustomers(out[2].projects || []);
    }).catch(function (e) {
      showErr(e.message);
    });
  }

  function renderAll() {
    renderOverview();
    renderBriefings();
    renderOffers();
    renderInvoicesAdmin();
    renderProjects();
    renderCustomers();
  }

  function setOv(id, n) {
    var el = document.getElementById(id);
    if (el) el.textContent = String(n);
  }
  function renderOverview() {
    var neu = state.briefings.filter(function (b) { return (b.status || 'neu') === 'neu'; }).length;
    var kunden = state.profiles.filter(function (p) { return (p.role || 'customer') !== 'admin'; }).length;
    var offen = state.offers.filter(function (o) { return (o.status || '') === 'gesendet'; }).length;
    setOv('ovAnfragen', neu);
    setOv('ovAngebote', offen);
    setOv('ovProjekte', state.projects.length);
    setOv('ovKunden', kunden);
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

  var PAKET_PREIS = { start: 1290, wachstum: 3290, platzhirsch: 6490 };
  var PAKET_KORR = { start: 1, wachstum: 2, platzhirsch: 3 };
  var CARE_PREIS = { 'care-s': 49, 'care-m': 99, 'care-l': 249 };
  var PAKET_ALIAS = { basis: 'start', pro: 'wachstum', platin: 'platzhirsch', start: 'start', wachstum: 'wachstum', platzhirsch: 'platzhirsch' };

  function openBriefing(b) {
    var k = contactOf(b);
    var cfg = configOf(b);
    var anfrage = payloadOf(b).anfrage || {};
    var once = cfg.summe_einmalig ? Number(cfg.summe_einmalig).toLocaleString('de-DE') + ' €' : 'noch offen';
    var monthly = cfg.summe_monatlich ? Number(cfg.summe_monatlich).toLocaleString('de-DE') + ' €/Mon.' : 'noch offen';
    var pak0 = PAKET_ALIAS[String(cfg.paket || '').toLowerCase()] || 'start';
    function opt(v, l, sel) { return '<option value="' + v + '"' + (v === sel ? ' selected' : '') + '>' + l + '</option>'; }
    var pakOpts = opt('start', 'Start', pak0) + opt('wachstum', 'Wachstum', pak0) + opt('platzhirsch', 'Platzhirsch', pak0);
    var careOpts = opt('', 'kein Schutz', '') + opt('care-s', 'Schutz S', 'care-m') + opt('care-m', 'Schutz M', 'care-m') + opt('care-l', 'Schutz L', 'care-m');

    openModal(
      '<div class="spread"><h2 style="color:#fff">Website-Anfrage</h2><button class="btn btn-ghost btn-sm" id="aClose">Schließen</button></div>' +
      '<p class="muted">' + esc(fmtDate(b.created_at)) + ' · ' + esc(k.name || '-') + ' · ' + esc(k.email || '-') + '</p>' +
      '<div class="grid-2" style="gap:12px; margin:16px 0;">' +
        '<div class="card"><p class="eyebrow">Empfehlung</p><h3>' + esc(cfg.paket_name || cfg.paket || 'Individuell') + '</h3><p class="muted">' + esc(once) + ' · ' + esc(monthly) + '</p></div>' +
        '<div class="card"><p class="eyebrow">Kontakt</p><h3>' + esc(k.name || '-') + '</h3><p class="muted">' + esc(k.email || '-') + (k.telefon ? ' · ' + esc(k.telefon) : '') + '</p></div>' +
      '</div>' +
      '<div class="field"><label>Status</label><select id="aStatus">' + STATUS.map(function (s) { return '<option value="' + s + '"' + (s === b.status ? ' selected' : '') + '>' + s + '</option>'; }).join('') + '</select></div>' +
      '<hr style="border:none;border-top:1px solid rgba(255,255,255,.12); margin:18px 0;">' +
      '<h3 style="color:#fff; margin-bottom:4px;">Angebot erstellen &amp; senden</h3>' +
      '<p class="muted" style="margin-bottom:12px;">Der Kunde bekommt eine Mail, sieht das Angebot im Portal und beauftragt verbindlich. Erst dann entsteht das Projekt.</p>' +
      '<div class="field"><label>Projektname</label><input id="oTitel" type="text" value="' + esc((k.name || 'Kunde') + ' Website') + '"></div>' +
      '<div class="grid-2" style="gap:12px;">' +
        '<div class="field"><label>Paket</label><select id="oPaket">' + pakOpts + '</select></div>' +
        '<div class="field"><label>Einmalpreis (€)</label><input id="oPreis" type="number" value="' + (PAKET_PREIS[pak0] || 1290) + '"></div>' +
        '<div class="field"><label>Rundum-Schutz</label><select id="oCare">' + careOpts + '</select></div>' +
        '<div class="field"><label>Schutz (€/Monat)</label><input id="oCarePreis" type="number" value="99"></div>' +
        '<div class="field"><label>Korrekturrunden</label><input id="oKorr" type="number" value="' + (PAKET_KORR[pak0] || 1) + '"></div>' +
        '<div class="field"><label>Fertigstellung</label><input id="oLiefer" type="text" value="in 7–14 Werktagen"></div>' +
        '<div class="field"><label>Angebot gültig bis</label><input id="oGueltig" type="date"></div>' +
      '</div>' +
      '<div class="field"><label>Leistungsumfang (eine Zeile je Punkt)</label><textarea id="oUmfang" rows="4"></textarea></div>' +
      '<div class="row row-end"><button class="btn btn-ghost" id="aSave">Status speichern</button><button class="btn btn-primary" id="oSend">Angebot senden</button></div>' +
      '<details style="margin-top:18px;"><summary>Alle Angaben anzeigen</summary><pre style="white-space:pre-wrap; color:#d6f6e6; font-size:12px;">' + esc(JSON.stringify({ anfrage: anfrage, konfiguration: cfg }, null, 2)) + '</pre></details>'
    );
    document.getElementById('aClose').addEventListener('click', closeModal);
    document.getElementById('aSave').addEventListener('click', function () {
      send('api/admin/briefings.php', { id: b.id, status: document.getElementById('aStatus').value }, 'PATCH')
        .then(function () { closeModal(); return loadAll(); })
        .catch(function (e) { showErr(e.message); });
    });
    document.getElementById('oPaket').addEventListener('change', function () {
      var v = this.value;
      document.getElementById('oPreis').value = PAKET_PREIS[v] || '';
      document.getElementById('oKorr').value = PAKET_KORR[v] || '';
    });
    document.getElementById('oCare').addEventListener('change', function () {
      document.getElementById('oCarePreis').value = CARE_PREIS[this.value] || '';
    });
    document.getElementById('oSend').addEventListener('click', function () {
      var btn = this; btn.disabled = true;
      send('api/admin/offers.php', {
        briefing_id: b.id,
        email: k.email || b.kontakt_email || '',
        name: k.name || b.kontakt_name || '',
        titel: document.getElementById('oTitel').value || 'Website-Projekt',
        paket: document.getElementById('oPaket').value,
        preis_einmalig: document.getElementById('oPreis').value,
        care_stufe: document.getElementById('oCare').value,
        care_preis: document.getElementById('oCarePreis').value,
        korrekturrunden: document.getElementById('oKorr').value,
        liefertext: document.getElementById('oLiefer').value,
        gueltig_bis: document.getElementById('oGueltig').value,
        umfang: document.getElementById('oUmfang').value
      }).then(function (res) {
        closeModal();
        if (!res.mail_sent) showErr('Angebot erstellt. Die Mail konnte noch nicht versendet werden — bitte Mailversand prüfen.');
        return loadAll();
      }).catch(function (e) { btn.disabled = false; showErr(e.message); });
    });
  }

  function renderOffers() {
    var body = document.getElementById('angebotBody');
    if (!body) return;
    if (!state.offers.length) {
      body.innerHTML = '<tr><td colspan="5" class="muted">Noch keine Angebote.</td></tr>';
      return;
    }
    body.innerHTML = '';
    state.offers.forEach(function (o) {
      var tr = document.createElement('tr');
      tr.className = 'clickable';
      tr.innerHTML = '<td>' + esc(fmtDate(o.created_at)) + '</td><td>' + esc(o.name || o.email || '-') + '</td><td>' + esc(cap(o.paket)) + '</td><td>' + esc(euro(o.preis_einmalig)) + '</td><td><span class="badge">' + esc(OFFER_STATUS[o.status] || o.status) + '</span></td>';
      tr.addEventListener('click', function () { openOffer(o); });
      body.appendChild(tr);
    });
  }

  function openOffer(o) {
    var proto = '';
    if (o.status === 'angenommen') {
      proto = '<div class="card" style="margin-top:14px;"><p class="eyebrow">Verbindliche Annahme</p><p class="muted">Angenommen am ' + esc(fmtDate(o.angenommen_am)) + '<br>IP: ' + esc(o.angenommen_ip || '-') + ' · AGB-Version: ' + esc(o.agb_version || '-') + '</p></div>';
    }
    openModal(
      '<div class="spread"><h2 style="color:#fff">' + esc(o.titel || 'Angebot') + '</h2><button class="btn btn-ghost btn-sm" id="ofClose">Schließen</button></div>' +
      '<p class="muted">' + esc(fmtDate(o.created_at)) + ' · ' + esc(o.name || o.email || '-') + ' · <span class="badge">' + esc(OFFER_STATUS[o.status] || o.status) + '</span></p>' +
      '<div class="pt-kv"><span>Paket</span><strong>' + esc(cap(o.paket)) + '</strong></div>' +
      '<div class="pt-kv"><span>Einmalpreis</span><strong>' + esc(euro(o.preis_einmalig)) + '</strong></div>' +
      (o.care_stufe ? '<div class="pt-kv"><span>Rundum-Schutz</span><strong>' + esc(o.care_stufe) + (o.care_preis != null ? ' · ' + esc(euro(o.care_preis)) + '/Mon.' : '') + '</strong></div>' : '') +
      (o.korrekturrunden != null ? '<div class="pt-kv"><span>Korrekturrunden</span><strong>' + esc(String(o.korrekturrunden)) + '</strong></div>' : '') +
      (o.liefertext ? '<div class="pt-kv"><span>Fertigstellung</span><strong>' + esc(o.liefertext) + '</strong></div>' : '') +
      (o.gueltig_bis ? '<div class="pt-kv"><span>Gültig bis</span><strong>' + esc(o.gueltig_bis) + '</strong></div>' : '') +
      (o.umfang ? '<p class="eyebrow" style="margin-top:12px;">Leistungsumfang</p><p class="muted" style="white-space:pre-line;">' + esc(o.umfang) + '</p>' : '') +
      proto
    );
    document.getElementById('ofClose').addEventListener('click', closeModal);
  }

  function renderInvoicesAdmin() {
    var body = document.getElementById('rechnungBody');
    if (!body) return;
    if (!state.invoices.length) {
      body.innerHTML = '<tr><td colspan="5" class="muted">Noch keine Rechnungen.</td></tr>';
      return;
    }
    body.innerHTML = '';
    state.invoices.forEach(function (r) {
      var tr = document.createElement('tr');
      tr.className = 'clickable';
      tr.innerHTML = '<td>' + esc(r.nummer || '—') + '</td><td>' + esc(r.kunde || '-') + '</td><td>' + esc(fmtDate(r.ausgestellt_am)) + '</td><td>' + esc(r.betrag) + '</td><td><span class="badge">' + esc(INV_STATUS[r.status] || r.status) + '</span></td>';
      tr.addEventListener('click', function () { openInvoiceAdmin(r); });
      body.appendChild(tr);
    });
  }

  function openInvoiceAdmin(r) {
    openModal(
      '<div class="spread"><h2 style="color:#fff">Rechnung ' + esc(r.nummer || '') + '</h2><button class="btn btn-ghost btn-sm" id="riClose">Schließen</button></div>' +
      '<p class="muted">' + esc(r.kunde || '-') + ' · ' + esc(fmtDate(r.ausgestellt_am)) + ' · <span class="badge">' + esc(INV_STATUS[r.status] || r.status) + '</span></p>' +
      '<div class="pt-kv"><span>Betrag</span><strong>' + esc(r.betrag) + '</strong></div>' +
      '<div class="pt-kv"><span>Fällig</span><strong>' + esc(fmtDate(r.faellig_am)) + '</strong></div>' +
      '<div class="row" style="gap:8px;margin-top:14px;flex-wrap:wrap;">' +
        '<a class="btn btn-ghost btn-sm" href="api/portal/invoice-file.php?id=' + encodeURIComponent(r.id) + '&typ=pdf" target="_blank" rel="noopener">PDF</a>' +
        '<a class="btn btn-ghost btn-sm" href="api/portal/invoice-file.php?id=' + encodeURIComponent(r.id) + '&typ=xml">E-Rechnung (XML)</a>' +
        (r.status === 'offen' ? '<button class="btn btn-primary btn-sm" id="riPaid">Als bezahlt markieren</button>' : '') +
        (r.status !== 'storniert' ? '<button class="btn btn-ghost btn-sm" id="riCancel">Stornieren</button>' : '') +
      '</div>'
    );
    document.getElementById('riClose').addEventListener('click', closeModal);
    var paid = document.getElementById('riPaid');
    if (paid) paid.addEventListener('click', function () {
      send('api/admin/invoices.php', { id: r.id, action: 'mark_paid' }, 'PATCH').then(function () { closeModal(); return loadAll(); }).catch(function (e) { showErr(e.message); });
    });
    var cancel = document.getElementById('riCancel');
    if (cancel) cancel.addEventListener('click', function () {
      send('api/admin/invoices.php', { id: r.id, action: 'storniert' }, 'PATCH').then(function () { closeModal(); return loadAll(); }).catch(function (e) { showErr(e.message); });
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
      '<div class="row row-end"><button class="btn btn-primary" id="pSave">Projekt speichern</button></div>' +
      '<div id="pBriefing" style="margin-top:20px;"></div>'
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
    fillBriefing(p);
  }

  function fillBriefing(p) {
    var box = document.getElementById('pBriefing');
    if (!box) return;
    function render(answers, status) {
      if (!answers || !Object.keys(answers).length) {
        box.innerHTML = '<p class="eyebrow">Briefing</p><p class="muted">Noch kein Briefing ausgefüllt.</p>';
        return;
      }
      var html = '<p class="eyebrow">Briefing ' + (status === 'abgeschlossen' ? '· abgeschlossen' : '· in Arbeit') + '</p>';
      (BRIEFING2.steps || []).forEach(function (step) {
        var rows = '';
        step.fields.forEach(function (f) {
          var v = answers[f.key];
          if (v == null || (Array.isArray(v) && !v.length) || String(v).trim() === '') return;
          rows += '<div class="pt-kv"><span>' + esc(f.label) + '</span><strong>' + fmtAns(f, v) + '</strong></div>';
        });
        if (rows) html += '<h3 style="color:#fff;margin:16px 0 6px;">' + esc(step.title) + '</h3>' + rows;
      });
      box.innerHTML = html;
    }
    box.innerHTML = '<p class="eyebrow">Briefing</p><p class="muted">Lädt …</p>';
    if (state.preview) { render(state._demoBriefing || {}, 'abgeschlossen'); return; }
    api('api/admin/briefing.php?project_id=' + encodeURIComponent(p.id)).then(function (d) {
      if (!d.has_briefing) { box.innerHTML = '<p class="eyebrow">Briefing</p><p class="muted">Noch kein Briefing ausgefüllt.</p>'; return; }
      render(d.answers, d.status);
    }).catch(function () { box.innerHTML = '<p class="eyebrow">Briefing</p><p class="muted">Briefing konnte nicht geladen werden.</p>'; });
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
      ['anfragen', 'angebote', 'rechnungen', 'projekte', 'kunden'].forEach(function (name) {
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

  // Sicherer Vorschau-Modus: nur Demo-Daten, kein Login, keine echten Daten — für Design-Review.
  if (new URLSearchParams(window.location.search).get('preview') === '1') {
    state.preview = true;
    document.getElementById('gate').classList.add('hidden');
    document.getElementById('app').classList.remove('hidden');
    state.profiles = [
      { id: 1, name: 'Max Mustermann', email: 'max@muster.de', firma: 'Muster Bäckerei GmbH', telefon: '030 1234567', role: 'customer' },
      { id: 2, name: 'Sabine Beispiel', email: 'sabine@beispiel-hotel.de', firma: 'Hotel Beispiel', telefon: '', role: 'customer' }
    ];
    state.projects = [
      { id: 11, customer_id: 1, titel: 'muster-baeckerei.de', paket: 'wachstum', phase: 'live', liefertermin: '2026-06-30', notiz_kunde: '', notiz_intern: '' },
      { id: 12, customer_id: 2, titel: 'beispiel-hotel.de', paket: 'platzhirsch', phase: 'design_laeuft', liefertermin: '2026-07-21', notiz_kunde: '', notiz_intern: '' }
    ];
    state.briefings = [
      { id: 101, created_at: '2026-07-06T09:12:00', status: 'neu', kontakt_name: 'Peter Klein', kontakt_email: 'peter@klein-elektro.de', payload: { kontakt: { name: 'Peter Klein', email: 'peter@klein-elektro.de', telefon: '0170 9998877' }, konfiguration: { paket: 'start', paket_name: 'Start', summe_einmalig: 1290, summe_monatlich: 49 } } },
      { id: 102, created_at: '2026-07-05T16:40:00', status: 'in_bearbeitung', kontakt_name: 'Sabine Beispiel', kontakt_email: 'sabine@beispiel-hotel.de', payload: { kontakt: { name: 'Sabine Beispiel', email: 'sabine@beispiel-hotel.de' }, konfiguration: { paket: 'platzhirsch', paket_name: 'Platzhirsch', summe_einmalig: 6490, summe_monatlich: 249 } } }
    ];
    state.offers = [
      { id: 'o1', created_at: '2026-07-06T10:00:00', name: 'Peter Klein', email: 'peter@klein-elektro.de', paket: 'start', preis_einmalig: 1290, care_stufe: 'care-s', care_preis: 49, korrekturrunden: 1, liefertext: 'in 7 Werktagen', gueltig_bis: '2026-07-31', umfang: 'One-Pager\nTexte inklusive', status: 'gesendet' },
      { id: 'o2', created_at: '2026-07-04T14:00:00', name: 'Sabine Beispiel', email: 'sabine@beispiel-hotel.de', paket: 'platzhirsch', preis_einmalig: 6490, care_stufe: 'care-l', care_preis: 249, korrekturrunden: 3, liefertext: 'in 7–14 Werktagen', status: 'angenommen', angenommen_am: '2026-07-05T09:30:00', angenommen_ip: '84.12.x.x', agb_version: 'AGB-2026-07' }
    ];
    state._demoBriefing = { firmenname: 'Muster Bäckerei', branche: 'Bäckerei & Café', gegruendet: '1985', hauptziele: ['anfragen', 'termine'], hat_logo: 'ja', stimmung: ['modern', 'warm'], leistungen_inhalt: 'Brot & Brötchen\nKuchen & Torten', adresse: 'Hauptstraße 1\n12345 Musterstadt', telefon: '030 123456', kleinunternehmer: 'nein', suchbegriffe: 'Bäckerei Musterstadt, Sauerteigbrot' };
    state.invoices = [
      { id: 'r1', nummer: '2026-0001', kunde: 'Muster Bäckerei GmbH', ausgestellt_am: '2026-07-05', faellig_am: '2026-07-19', betrag: '3.290,00 €', status: 'offen' },
      { id: 'r2', nummer: '2026-0002', kunde: 'Hotel Beispiel', ausgestellt_am: '2026-07-02', faellig_am: '2026-07-16', betrag: '6.490,00 €', status: 'bezahlt' }
    ];
    renderAll();
  } else {
    requireAdmin().then(function (ok) {
      if (ok) loadAll();
    });
  }
})();
