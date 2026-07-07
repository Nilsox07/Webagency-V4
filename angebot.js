(function () {
  'use strict';

  // Angebot ansehen + verbindlich beauftragen (Button-Lösung §312j BGB).
  var API = 'api/portal/offer.php';
  var csrf = '';
  var state = { offer: null, agbVersion: '', preview: false };

  function $(id) { return document.getElementById(id); }
  function el(tag, cls, txt) { var e = document.createElement(tag); if (cls) e.className = cls; if (txt != null) e.textContent = txt; return e; }
  function euro(n) { return (n == null) ? '—' : Number(n).toLocaleString('de-DE') + ' €'; }
  function showErr(m) { var e = $('err'); if (!e) return; e.textContent = m; e.classList.remove('hidden'); setTimeout(function () { e.classList.add('hidden'); }, 6000); }

  var PAKET = { start: 'Start', wachstum: 'Wachstum', platzhirsch: 'Platzhirsch', basis: 'Start', pro: 'Wachstum', platin: 'Platzhirsch' };
  var CARE = { 'care-s': 'Rundum-Schutz S', 'care-m': 'Rundum-Schutz M', 'care-l': 'Rundum-Schutz L' };

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

  function kv(label, value) {
    var row = el('div', 'pt-kv');
    row.appendChild(el('span', null, label));
    row.appendChild(el('strong', null, value));
    return row;
  }

  function renderOffer(offer) {
    var box = $('offerBox'); box.innerHTML = '';
    if (offer.status === 'angenommen') { showAccepted(); return; }

    var card = el('div', 'card');
    card.appendChild(el('h2', null, offer.titel || 'Ihr Website-Projekt'));
    card.appendChild(kv('Paket', PAKET[String(offer.paket || '').toLowerCase()] || offer.paket || '—'));
    card.appendChild(kv('Einmalpreis', euro(offer.preis_einmalig)));
    if (offer.care_stufe) card.appendChild(kv('Laufender Schutz', (CARE[offer.care_stufe] || offer.care_stufe) + (offer.care_preis != null ? ' · ' + euro(offer.care_preis) + '/Monat' : '')));
    if (offer.korrekturrunden != null) card.appendChild(kv('Korrekturrunden', String(offer.korrekturrunden)));
    if (offer.liefertext) card.appendChild(kv('Fertigstellung', offer.liefertext));
    if (offer.gueltig_bis) card.appendChild(kv('Angebot gültig bis', offer.gueltig_bis));
    if (offer.umfang) {
      card.appendChild(el('p', 'eyebrow', 'Leistungsumfang'));
      var u = el('p', 'muted'); u.style.whiteSpace = 'pre-line'; u.textContent = offer.umfang; card.appendChild(u);
    }
    if (offer.hinweis) { var h = el('p', 'muted'); h.style.whiteSpace = 'pre-line'; h.textContent = offer.hinweis; card.appendChild(h); }
    box.appendChild(card);

    var accept = el('div', 'card');
    accept.appendChild(el('h3', null, 'Verbindlich beauftragen'));
    var lab = el('label', 'ob-agb');
    var cb = el('input'); cb.type = 'checkbox'; cb.id = 'agbCheck';
    lab.appendChild(cb);
    var span = el('span');
    span.innerHTML = 'Ich habe die <a href="agb.php" target="_blank" rel="noopener">AGB</a> und die Widerrufsbelehrung gelesen und stimme zu. Mir ist bewusst, dass ich als Verbraucher mit der Beauftragung die Ausführung vor Ablauf der Widerrufsfrist ausdrücklich wünsche.';
    lab.appendChild(span);
    accept.appendChild(lab);

    var btn = el('button', 'btn btn-primary'); btn.id = 'acceptBtn'; btn.type = 'button'; btn.textContent = 'Zahlungspflichtig beauftragen';
    btn.style.marginTop = '14px';
    btn.addEventListener('click', function () {
      if (!cb.checked) { showErr('Bitte bestätigen Sie zuerst die AGB.'); return; }
      if (state.preview) { showAccepted(); return; }
      btn.disabled = true;
      post({ action: 'accept', agb: true }).then(function () { showAccepted(); })
        .catch(function (e) { showErr('Fehler: ' + e.message); btn.disabled = false; });
    });
    accept.appendChild(btn);
    accept.appendChild(el('p', 'ob-help', 'Es entstehen jetzt keine Kosten — die Rechnung (inkl. Anzahlung) erhalten Sie separat.'));
    box.appendChild(accept);
  }

  function showAccepted() { $('app').classList.add('hidden'); $('accepted').classList.remove('hidden'); }
  function showApp() { $('gate').classList.add('hidden'); $('app').classList.remove('hidden'); }

  function init() {
    var logout = $('logoutBtn');
    if (logout) logout.addEventListener('click', function () { api('api/auth/logout.php', { method: 'POST' }).catch(function () {}).then(function () { window.location.href = 'login.php'; }); });

    if (new URLSearchParams(window.location.search).get('preview') === '1') {
      state.preview = true;
      showApp();
      renderOffer({ titel: 'Website muster-baeckerei.de', paket: 'wachstum', preis_einmalig: 3290, care_stufe: 'care-m', care_preis: 99, korrekturrunden: 2, liefertext: 'in 7–14 Werktagen', gueltig_bis: '31.07.2026', umfang: 'Mehrseitige Website (bis 8 Seiten)\nTexte & Bildauswahl inklusive\nSuchmaschinen-Grundoptimierung\nBarrierefreiheit nach BFSG', status: 'gesendet' });
      return;
    }

    api(API).then(function (d) {
      state.agbVersion = d.agb_version || '';
      if (!d.has_offer) { $('gate').innerHTML = '<h2>Kein Angebot</h2><p class="muted">Aktuell liegt kein Angebot vor. <a href="portal.php">Zum Portal</a></p>'; return; }
      showApp();
      renderOffer(d.offer);
    }).catch(function () { window.location.href = 'login.php'; });
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();
})();
