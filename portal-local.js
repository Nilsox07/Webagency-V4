(function () {
  'use strict';

  var BASE_BEFORE = ['angebot_bestaetigt', 'inhalte_liefern', 'design_laeuft'];
  var BASE_AFTER = ['finalisierung', 'live'];
  var LABELS = {
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
  var ROUNDS = { basis: 2, pro: 3, platin: 4, start: 2, wachstum: 3, platzhirsch: 4 };
  var csrfToken = '';
  var NOW = {
    angebot_bestaetigt: 'Willkommen! Ihr Angebot ist bestätigt. Wir melden uns mit den nächsten Schritten. Im Moment müssen Sie nichts tun.',
    inhalte_liefern: 'Bitte senden Sie uns Ihre Texte, Bilder und Ihr Logo. Der Upload-Bereich folgt in Kürze. Bis dahin reicht eine E-Mail an uns.',
    design_laeuft: 'Wir gestalten gerade Ihr Design. Der erste Entwurf kommt bald zur Abstimmung.',
    korrektur: 'Ihr Entwurf liegt zur Durchsicht bereit. Bitte sammeln Sie Ihr Feedback und schicken Sie es uns gebündelt in einer Nachricht.',
    finalisierung: 'Letzter Feinschliff: Wir bereiten alles für den Onlinegang vor. Bitte halten Sie finale Freigaben bereit.',
    live: 'Geschafft, Ihre Website ist online. Ab jetzt kümmert sich Sartu um Betrieb, Updates und Sicherheit.'
  };

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
  function post(url, data) {
    return api(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data || {})
    });
  }
  function showErr(msg) {
    var err = document.getElementById('err');
    if (!err) return;
    err.textContent = msg;
    err.classList.remove('hidden');
  }
  function cap(s) {
    s = String(s || '');
    return s ? s.charAt(0).toUpperCase() + s.slice(1) : '';
  }
  function fmtDate(d) {
    try {
      return new Date(d).toLocaleDateString('de-DE', { day: '2-digit', month: 'long', year: 'numeric' });
    } catch (e) { return d || ''; }
  }
  function setBadge(id, text) {
    var el = document.getElementById(id);
    if (!el) return;
    if (text) {
      el.textContent = text;
      el.classList.remove('hidden');
    } else {
      el.classList.add('hidden');
    }
  }
  function phaseSequence(paket) {
    var key = String(paket || '').toLowerCase();
    var n = ROUNDS[key] || 4;
    var seq = BASE_BEFORE.slice();
    for (var i = 1; i <= n; i++) seq.push('korrektur_' + i);
    return seq.concat(BASE_AFTER);
  }

  function render(project) {
    document.getElementById('projTitle').textContent = project.titel || 'Ihr Website-Projekt';
    setBadge('paketBadge', project.paket ? 'Paket: ' + cap(project.paket) : '');
    setBadge('careBadge', project.care_stufe || '');
    setBadge('terminBadge', project.liefertermin ? 'Liefertermin: ' + fmtDate(project.liefertermin) : '');

    var seq = phaseSequence(project.paket);
    var curr = seq.indexOf(project.phase);
    if (curr < 0) curr = 0;
    var tl = document.getElementById('timeline');
    tl.innerHTML = '';
    seq.forEach(function (phase, i) {
      var step = document.createElement('div');
      step.className = 'tl-step ' + (i < curr ? 'is-done' : (i === curr ? 'is-active' : ''));
      step.innerHTML = '<div class="tl-dot">' + (i < curr ? '✓' : String(i + 1)) + '</div><div class="tl-label">' + (LABELS[phase] || phase) + '</div>';
      tl.appendChild(step);
    });

    var nowKey = /^korrektur_/.test(project.phase) ? 'korrektur' : project.phase;
    document.getElementById('nowText').textContent = NOW[nowKey] || 'Wir halten Sie auf dem Laufenden.';
    var note = document.getElementById('noteCard');
    if (project.notiz_kunde) {
      document.getElementById('noteText').textContent = project.notiz_kunde;
      note.classList.remove('hidden');
    } else {
      note.classList.add('hidden');
    }
  }

  function init() {
    api('api/portal/projects.php').then(function (data) {
      document.getElementById('loading').classList.add('hidden');
      var profile = data.profile || {};
      var projects = data.projects || [];
      var firstName = (profile.name || profile.email || '').split(' ')[0] || '';
      document.getElementById('hello').textContent = firstName ? 'Hallo ' + firstName : 'Hallo';

      if (!projects.length) {
        document.getElementById('empty').classList.remove('hidden');
        return;
      }
      document.getElementById('app').classList.remove('hidden');
      var select = document.getElementById('projSwitch');
      if (projects.length > 1 && select) {
        select.classList.remove('hidden');
        projects.forEach(function (project, i) {
          var option = document.createElement('option');
          option.value = String(i);
          option.textContent = project.titel || ('Projekt ' + (i + 1));
          select.appendChild(option);
        });
        select.addEventListener('change', function () { render(projects[Number(select.value)]); });
      }
      render(projects[0]);
    }).catch(function () {
      window.location.href = 'login.php';
    });
  }

  var logout = document.getElementById('logoutBtn');
  if (logout) {
    logout.addEventListener('click', function () {
      post('api/auth/logout.php', {}).finally(function () { window.location.href = 'login.php'; });
    });
  }

  init();
})();
