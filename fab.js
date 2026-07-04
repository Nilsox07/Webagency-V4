/* ============================================================
   Sartu · Dezenter Entscheidungshelfer
   ------------------------------------------------------------
   Kein Popup, kein Countdown, kein automatisches Oeffnen.
   Der Helfer erscheint erst nach dem ersten Scrollen als kompakter
   Button und zeigt erst nach Klick drei passende naechste Schritte.

   ENTFERNEN: <script src="fab.js"> aus den Seiten nehmen.
   ============================================================ */
(function () {
  'use strict';
  if (typeof window === 'undefined' || typeof document === 'undefined') return;

  var page = (location.pathname.split('/').pop() || '').toLowerCase().replace(/\.(php|html)$/i, '');
  var excluded = ['anfrage', 'briefing', 'admin', 'login', 'portal', 'auth-callback'];
  if (excluded.indexOf(page) !== -1) return;

  function action(title, text, href, primary) {
    return { title: title, text: text, href: href, primary: Boolean(primary) };
  }

  var contexts = {
    standard: {
      title: 'Was hilft Ihnen jetzt?',
      text: 'Wählen Sie den nächsten sinnvollen Schritt. Kein Formular öffnet automatisch.',
      actions: [
        action('Preise einschätzen', 'Pakete und laufende Kosten ansehen.', 'preise.php'),
        action('Optionen vergleichen', 'Baukasten, Agentur, WordPress und mehr.', 'vergleiche.php'),
        action('Empfehlung starten', 'In 2 Minuten zur passenden Richtung.', 'anfrage.php', true)
      ]
    },
    leistung: {
      title: 'Welche Leistung passt?',
      text: 'Wenn Sie noch unsicher sind, starten Sie mit Preis, Branche oder Empfehlung.',
      actions: [
        action('Preise ansehen', 'Festpreise und Pakete vergleichen.', 'preise.php'),
        action('Nach Branche sortieren', 'Handwerk, Praxis, Kanzlei oder Dienstleistung.', 'branchen.php'),
        action('Empfehlung starten', 'Kurze Anfrage mit passender Einordnung.', 'anfrage.php', true)
      ]
    },
    ratgeber: {
      title: 'Gelesen, und jetzt?',
      text: 'Nutzen Sie den nächsten Schritt, der zu Ihrer Recherche passt.',
      actions: [
        action('Kosten klären', 'Preisrahmen und Paketlogik verstehen.', 'ratgeber-website-kosten.php'),
        action('Lösungen vergleichen', 'Die wichtigsten Website-Optionen fair einordnen.', 'vergleiche.php'),
        action('Projekt einschätzen', 'Unverbindlich Richtung und Umfang prüfen.', 'anfrage.php', true)
      ]
    },
    branche: {
      title: 'Von Branche zu Website',
      text: 'Wenn die Richtung passt, helfen diese Schritte bei Struktur und Angebot.',
      actions: [
        action('Leistungen ansehen', 'Webdesign, SEO, Texte und Wartung einordnen.', 'leistungen.php'),
        action('Lokale Sichtbarkeit', 'Google-Profil und lokale Suchanfragen prüfen.', 'leistung-lokales-seo.php'),
        action('Website anfragen', 'Branche und Ziel kurz einordnen lassen.', 'anfrage.php', true)
      ]
    },
    vergleich: {
      title: 'Vergleich fertig?',
      text: 'Danach geht es meist um Budget, Qualität oder konkrete Empfehlung.',
      actions: [
        action('Preise ansehen', 'Was die Umsetzung realistisch kostet.', 'preise.php'),
        action('Qualität prüfen', 'Abnahme und Checks vor dem Onlinegang.', 'qualitaet.php'),
        action('Empfehlung holen', 'Passende Lösung ohne Verkaufsdruck klären.', 'anfrage.php', true)
      ]
    },
    qualitaet: {
      title: 'Qualität prüfen',
      text: 'Wenn der Prozess passt, sind Ablauf, Preis und Anfrage die nächsten Punkte.',
      actions: [
        action('Ablauf ansehen', 'So entsteht die Website Schritt für Schritt.', 'ablauf.php'),
        action('Preise prüfen', 'Festpreis-Pakete und Rundum-Schutz ansehen.', 'preise.php'),
        action('Projekt starten', 'Transparent und unverbindlich anfragen.', 'anfrage.php', true)
      ]
    }
  };

  function contextForPage(name) {
    if (name.indexOf('leistung-') === 0 || name === 'leistungen') return contexts.leistung;
    if (name.indexOf('ratgeber') === 0) return contexts.ratgeber;
    if (name.indexOf('branche') === 0 || name === 'branchen') return contexts.branche;
    if (name.indexOf('vergleich') === 0 || name === 'vergleiche') return contexts.vergleich;
    if (name === 'qualitaet') return contexts.qualitaet;
    return contexts.standard;
  }

  function escapeHtml(value) {
    return String(value).replace(/[&<>"']/g, function (ch) {
      return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[ch];
    });
  }

  function buildAction(item) {
    return '' +
      '<a class="fab-choice' + (item.primary ? ' is-primary' : '') + '" href="' + escapeHtml(item.href) + '">' +
        '<strong>' + escapeHtml(item.title) + '</strong>' +
        '<span>' + escapeHtml(item.text) + '</span>' +
      '</a>';
  }

  function build() {
    if (document.querySelector('.fab-helper')) return;

    var ctx = contextForPage(page);
    var panelId = 'fabHelperPanel';
    var helper = document.createElement('div');
    helper.className = 'fab-helper';
    helper.innerHTML =
      '<div class="fab-panel" id="' + panelId + '" hidden>' +
        '<div class="fab-panel-head">' +
          '<p>' + escapeHtml(ctx.title) + '</p>' +
          '<button class="fab-close" type="button" aria-label="Entscheidungshilfe schließen">×</button>' +
        '</div>' +
        '<p class="fab-panel-text">' + escapeHtml(ctx.text) + '</p>' +
        '<div class="fab-choices">' + ctx.actions.map(buildAction).join('') + '</div>' +
      '</div>' +
      '<button class="fab" type="button" aria-expanded="false" aria-controls="' + panelId + '">' +
        '<span class="fab-ico" aria-hidden="true">' +
          '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' +
            '<path d="M9 18h6"/><path d="M10 22h4"/><path d="M12 2a7 7 0 0 0-4 12.7c.6.4 1 1.1 1 1.8V17h6v-.5c0-.7.4-1.4 1-1.8A7 7 0 0 0 12 2z"/>' +
          '</svg>' +
        '</span>' +
        '<span class="fab-label">Nächster Schritt</span>' +
      '</button>';

    var panel = helper.querySelector('.fab-panel');
    var toggle = helper.querySelector('.fab');
    var close = helper.querySelector('.fab-close');

    function setOpen(open) {
      panel.hidden = !open;
      helper.classList.toggle('is-open', open);
      toggle.setAttribute('aria-expanded', String(open));
    }

    toggle.addEventListener('click', function () { setOpen(panel.hidden); });
    close.addEventListener('click', function () {
      setOpen(false);
      toggle.focus();
    });
    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') setOpen(false);
    });
    document.addEventListener('click', function (event) {
      if (!helper.contains(event.target)) setOpen(false);
    });

    function reveal() {
      if (helper.classList.contains('is-in')) return;
      if (window.scrollY < Math.min(520, window.innerHeight * 0.65)) return;
      helper.classList.add('is-in');
      window.removeEventListener('scroll', reveal);
    }

    document.body.appendChild(helper);
    window.addEventListener('scroll', reveal, { passive: true });
    reveal();
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', build);
  else build();
})();
