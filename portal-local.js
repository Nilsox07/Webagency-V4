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

  function init() {
    initTabs();
    var logout = $('logoutBtn');
    if (logout) logout.addEventListener('click', function () { post('api/auth/logout.php', {}).finally(function () { window.location.href = 'login.php'; }); });

    // Sicherer Vorschau-Modus: nur Demo-Daten, keine echten — für Design-Review ohne Login.
    if (new URLSearchParams(window.location.search).get('preview') === '1') {
      show();
      fill({ name: 'Max Mustermann', firma: 'Muster Bäckerei GmbH', email: 'max@muster.de' },
           { titel: 'muster-baeckerei.de', paket: 'wachstum', care_stufe: 'care-m', phase: 'live', besucher_30d: 412 });
      return;
    }

    api('api/portal/projects.php').then(function (data) {
      var projects = data.projects || [];
      show();
      fill(data.profile || {}, projects[0] || {});
    }).catch(function () { window.location.href = 'login.php'; });
  }

  init();
})();
