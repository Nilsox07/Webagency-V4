(function () {
  'use strict';

  var emailForm = document.getElementById('emailForm');
  var codeForm = document.getElementById('codeForm');
  var err = document.getElementById('err');
  var sentNote = document.getElementById('sentNote');
  var sendBtn = document.getElementById('sendBtn');
  var verifyBtn = document.getElementById('verifyBtn');
  var currentEmail = '';

  function api(url, data) {
    return fetch(url, {
      method: 'POST',
      credentials: 'same-origin',
      headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
      body: JSON.stringify(data || {})
    }).then(function (r) {
      return r.json().catch(function () { return {}; }).then(function (json) {
        if (!r.ok || json.ok === false) throw json;
        return json;
      });
    });
  }
  function esc(s) {
    return String(s || '').replace(/[&<>"']/g, function (c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
  }
  function showErr(msg) {
    err.textContent = msg || 'Es ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut.';
    err.classList.remove('hidden');
  }
  function clearErr() {
    err.classList.add('hidden');
    err.textContent = '';
  }
  function route(profile) {
    window.location.href = profile && profile.role === 'admin' ? 'admin.php' : 'portal.php';
  }
  function setBusy(button, busy, label) {
    button.disabled = busy;
    button.innerHTML = busy ? '<span class="spinner"></span> Bitte warten...' : label;
  }

  fetch('api/auth/me.php', { credentials: 'same-origin', headers: { Accept: 'application/json' } })
    .then(function (r) { return r.json(); })
    .then(function (data) { if (data.authenticated) route(data.profile); })
    .catch(function () {});

  emailForm.addEventListener('submit', function (e) {
    e.preventDefault();
    clearErr();
    currentEmail = document.getElementById('email').value.trim().toLowerCase();
    if (!currentEmail) return;
    setBusy(sendBtn, true, 'Login-Link anfordern');
    api('api/auth/request-login.php', { email: currentEmail }).then(function (res) {
      setBusy(sendBtn, false, 'Login-Link anfordern');
      var debug = res.debug_code ? '<br><small>Debug-Code: <strong>' + esc(res.debug_code) + '</strong></small>' : '';
      sentNote.innerHTML = 'Wir haben einen Login-Link und einen 6-stelligen Code an <strong>' + esc(currentEmail) + '</strong> gesendet.' + debug;
      emailForm.classList.add('hidden');
      codeForm.classList.remove('hidden');
      document.getElementById('code').focus();
    }).catch(function (data) {
      setBusy(sendBtn, false, 'Login-Link anfordern');
      if (data && data.debug_code) {
        sentNote.innerHTML = 'Mailversand ist noch nicht eingerichtet. Debug-Code: <strong>' + esc(data.debug_code) + '</strong>';
        emailForm.classList.add('hidden');
        codeForm.classList.remove('hidden');
        document.getElementById('code').focus();
        return;
      }
      showErr(data && data.error);
    });
  });

  codeForm.addEventListener('submit', function (e) {
    e.preventDefault();
    clearErr();
    var code = document.getElementById('code').value.trim();
    if (code.length !== 6) {
      showErr('Bitte geben Sie den 6-stelligen Code ein.');
      return;
    }
    setBusy(verifyBtn, true, 'Code bestätigen & einloggen');
    api('api/auth/verify-login.php', { email: currentEmail, code: code }).then(function (data) {
      route(data.profile);
    }).catch(function (data) {
      setBusy(verifyBtn, false, 'Code bestätigen & einloggen');
      showErr(data && data.error);
    });
  });

  document.getElementById('backBtn').addEventListener('click', function () {
    codeForm.classList.add('hidden');
    emailForm.classList.remove('hidden');
    clearErr();
  });
})();
