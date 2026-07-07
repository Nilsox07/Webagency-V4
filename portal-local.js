(function () {
  'use strict';

  var csrfToken = '';
  var CARE_LABEL = { 'care-s': 'Schutz S · 49 €/Mon.', 'care-m': 'Schutz M · 99 €/Mon.', 'care-l': 'Schutz L · 249 €/Mon.' };
  var PAKET_LABEL = { basis: 'Start', start: 'Start', pro: 'Wachstum', wachstum: 'Wachstum', platin: 'Platzhirsch', platzhirsch: 'Platzhirsch' };
  var STATUS_TEXT = {
    angebot_bestaetigt: 'Ihr Angebot ist bestätigt — wir legen los. Im Moment müssen Sie nichts tun.',
    inhalte_liefern: 'Bitte liefern Sie Ihre Stichpunkte, Bilder und Ihr Logo im Bereich „Website bearbeiten".',
    design_laeuft: 'Wir gestalten gerade Ihr Design. Der erste Entwurf kommt bald zur Abstimmung.',
    finalisierung: 'Letzter Feinschliff vor dem Onlinegang. Bitte halten Sie finale Freigaben bereit.',
    live: 'Ihre Website ist online. Ab jetzt kümmert sich Sartu um Betrieb, Updates und Sicherheit.'
  };

  function $(id) { return document.getElementById(id); }
  function api(url, options) {
    options = options || {}; options.credentials = 'same-origin';
    options.headers = Object.assign({ Accept: 'application/json' }, options.headers || {});
    if (csrfToken && String(options.method || 'GET').toUpperCase() !== 'GET') options.headers['X-CSRF-Token'] = csrfToken;
    return fetch(url, options).then(function (r) {
      return r.json().catch(function () { return {}; }).then(function (data) {
        if (data.csrf) csrfToken = data.csrf;
        if (!r.ok || data.ok === false) throw new Error(data.error || ('HTTP ' + r.status));
        return data;
      });
    });
  }
  function post(url, data) {
    return api(url, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(data || {}) });
  }
  function cap(s) { s = String(s || ''); return s ? s.charAt(0).toUpperCase() + s.slice(1) : ''; }
  function setText(id, t) { var el = $(id); if (el) el.textContent = t; }
  function setBadge(id, t) { var el = $(id); if (!el) return; if (t) { el.textContent = t; el.classList.remove('hidden'); } else { el.classList.add('hidden'); } }

  // ---- Tab-Wechsel ----
  function initTabs() {
    var tabs = document.querySelectorAll('#ptTabs .tab');
    tabs.forEach(function (t) {
      t.addEventListener('click', function () {
        tabs.forEach(function (x) { x.classList.remove('is-on'); });
        t.classList.add('is-on');
        var name = t.getAttribute('data-tab');
        document.querySelectorAll('.pt-pane').forEach(function (p) { p.classList.add('hidden'); });
        var pane = $('pane-' + name); if (pane) pane.classList.remove('hidden');
      });
    });
  }

  function fill(profile, project) {
    profile = profile || {}; project = project || {};
    var firstName = (profile.name || profile.email || '').split(' ')[0] || '';
    setText('hello', firstName ? 'Hallo ' + firstName : 'Hallo');
    setText('ptUser', profile.firma || profile.email || '');
    setText('projTitle', project.titel || 'Ihr Website-Projekt');
    var paket = PAKET_LABEL[String(project.paket || '').toLowerCase()] || (project.paket ? cap(project.paket) : '');
    setBadge('paketBadge', paket ? 'Paket: ' + paket : '');
    var live = project.phase === 'live';
    setBadge('statusBadge', live ? 'online' : (project.phase ? 'in Arbeit' : ''));
    setText('kvPaket', paket || '—');
    var care = CARE_LABEL[project.care_stufe] || (project.care_stufe || '—');
    setText('kvCare', care);
    setText('tCare', 'aktiv');
    setText('tCareSub', care !== '—' ? care : 'gehört zu Ihrem Paket');
    if (project.besucher_30d != null) setText('tVisits', String(project.besucher_30d));
    var st = STATUS_TEXT[/^korrektur/.test(project.phase || '') ? 'design_laeuft' : project.phase];
    if (st) setText('nowText', st);
  }

  function show() { $('gate').classList.add('hidden'); $('app').classList.remove('hidden'); }

  // ---- Rechnungen ----
  var INV_STATUS = { offen: 'offen', bezahlt: 'bezahlt', storniert: 'storniert' };
  function renderInvoices(list, paymentsEnabled) {
    var box = $('invoiceList');
    if (!box) return;
    if (!list.length) { box.innerHTML = '<p class="muted">Ihre Rechnungen erscheinen hier zum Download, sobald sie erstellt sind.</p>'; return; }
    box.innerHTML = '';
    list.forEach(function (inv) {
      var row = document.createElement('div'); row.className = 'pt-kv pt-invrow';
      var left = document.createElement('span');
      left.innerHTML = '<strong>' + (inv.nummer || '—') + '</strong> · ' + inv.betrag + ' · <span class="badge">' + (INV_STATUS[inv.status] || inv.status) + '</span>';
      var right = document.createElement('span'); right.className = 'pt-invactions';
      var pdf = document.createElement('a'); pdf.className = 'btn btn-ghost btn-sm'; pdf.href = 'api/portal/invoice-file.php?id=' + encodeURIComponent(inv.id) + '&typ=pdf'; pdf.target = '_blank'; pdf.rel = 'noopener'; pdf.textContent = 'PDF';
      var xml = document.createElement('a'); xml.className = 'btn btn-ghost btn-sm'; xml.href = 'api/portal/invoice-file.php?id=' + encodeURIComponent(inv.id) + '&typ=xml'; xml.textContent = 'E-Rechnung';
      right.appendChild(pdf); right.appendChild(xml);
      if (inv.bezahlbar && paymentsEnabled) {
        var pay = document.createElement('button'); pay.className = 'btn btn-primary btn-sm'; pay.textContent = 'Bezahlen';
        pay.addEventListener('click', function () { payInvoice(inv.id, pay); });
        right.appendChild(pay);
      }
      row.appendChild(left); row.appendChild(right);
      box.appendChild(row);
    });
  }
  function payInvoice(id, btn) {
    if (new URLSearchParams(window.location.search).get('preview') === '1') return;
    btn.disabled = true;
    post('api/portal/pay.php', { invoice_id: id }).then(function (d) {
      if (d.checkout_url) { window.location.href = d.checkout_url; }
    }).catch(function (e) { btn.disabled = false; alert(e.message); });
  }
  function loadInvoices() {
    api('api/portal/invoices.php').then(function (d) { renderInvoices(d.invoices || [], !!d.payments_enabled); }).catch(function () {});
  }

  function init() {
    initTabs();
    var logout = $('logoutBtn');
    if (logout) logout.addEventListener('click', function () { post('api/auth/logout.php', {}).finally(function () { window.location.href = 'login.php'; }); });

    // Sicherer Vorschau-Modus: nur Demo-Daten, keine echten — für Design-Review ohne Login.
    if (new URLSearchParams(window.location.search).get('preview') === '1') {
      show();
      fill({ name: 'Max Mustermann', firma: 'Muster Bäckerei GmbH', email: 'max@muster.de' },
           { titel: 'muster-baeckerei.de', paket: 'wachstum', care_stufe: 'care-m', phase: 'live', besucher_30d: 412 });
      renderInvoices([
        { id: 'i1', nummer: '2026-0001', betrag: '3.290,00 €', status: 'offen', bezahlbar: true },
        { id: 'i2', nummer: '2025-0044', betrag: '490,00 €', status: 'bezahlt', bezahlbar: false }
      ], true);
      return;
    }

    api('api/portal/projects.php').then(function (data) {
      var projects = data.projects || [];
      show();
      fill(data.profile || {}, projects[0] || {});
      updateBriefingCard();
      loadInvoices();
    }).catch(function () { window.location.href = 'login.php'; });
  }

  // Cockpit-Karte: offenes Angebot -> "Angebot ansehen", sonst -> "Briefing starten".
  function updateBriefingCard() {
    var card = $('briefingCard');
    if (!card) return;
    api('api/portal/offer.php').then(function (d) {
      if (d.has_offer && d.offer && d.offer.status === 'gesendet') {
        card.setAttribute('href', 'angebot.php');
        var ey = card.querySelector('.eyebrow'); if (ey) ey.textContent = 'Ihr Angebot liegt vor';
        var p = card.querySelectorAll('p'); if (p.length) p[p.length - 1].innerHTML = 'Prüfen Sie Ihr Angebot und beauftragen Sie uns verbindlich. <strong>Angebot ansehen &rarr;</strong>';
      }
    }).catch(function () {});
  }

  init();
})();
