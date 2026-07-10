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
  var state = { briefings: [], projects: [], profiles: [], offers: [], invoices: [], aktionen: [], _demoBriefing: null };
  var INV_STATUS = { entwurf: 'Entwurf', offen: 'offen', bezahlt: 'bezahlt', storniert: 'storniert' };
  var AKTION_ZIELE = {
    'alle': 'Alle Pakete', 'paket:start': 'Paket Start', 'paket:wachstum': 'Paket Wachstum',
    'paket:platzhirsch': 'Paket Platzhirsch', 'addon:ki-assistent': 'KI-Chat-Assistent',
    'addon:seo-betreuung': 'SEO-Betreuung', 'addon:logo': 'Logo & Branding'
  };
  var AKTION_TYPEN = { 'prozent': 'Prozent-Rabatt (%)', 'fest': 'Fester Betrag (€)', 'gratis_monate': 'Gratis-Monate' };
  function aktionWertText(a) {
    var w = Number(a.wert || 0);
    if (a.typ === 'fest') return '-' + w.toLocaleString('de-DE') + ' €';
    if (a.typ === 'gratis_monate') return w + ' ' + (w === 1 ? 'Monat' : 'Monate') + ' gratis';
    return '-' + w + ' %';
  }
  function aktionZeitraum(a) {
    var s = a.start_am ? fmtDay(a.start_am) : '', e = a.end_am ? fmtDay(a.end_am) : '';
    if (s && e) return s + ' – ' + e;
    if (e) return 'bis ' + e;
    if (s) return 'ab ' + s;
    return 'unbegrenzt';
  }
  function fmtDay(d) { try { return new Date(d).toLocaleDateString('de-DE', { day: '2-digit', month: '2-digit', year: 'numeric' }); } catch (e) { return d || ''; } }
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
  // Antwort als Klartext (für den KI-Prompt, ohne HTML).
  function plainAns(f, v) {
    if (v == null) return '';
    if (f.type === 'multi') return (Array.isArray(v) ? v : []).map(function (x) { return optLabel(f, x); }).join(', ');
    if (f.type === 'choice') return optLabel(f, v);
    if (f.type === 'file') return v ? '[Datei vom Kunden hochgeladen]' : '';
    if (f.type === 'files') return (Array.isArray(v) && v.length) ? ('[' + v.length + ' Datei(en) vom Kunden hochgeladen]') : '';
    return String(v);
  }
  function slugify(s) {
    return String(s || 'kunde').toLowerCase()
      .replace(/ä/g, 'ae').replace(/ö/g, 'oe').replace(/ü/g, 'ue').replace(/ß/g, 'ss')
      .replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '').slice(0, 40) || 'kunde';
  }

  // Sartu-Website für den Herstellervermerk im Footer der Kundenseiten.
  // >>> BEI GO-LIVE auf 'https://sartu.de' ändern (siehe GO-LIVE-TODO.md). <<<
  var SARTU_URL = 'https://nils.nelten.de/SARTU';

  // --- Baustein-Bibliothek für den KI-Prompt: pro Seite/Funktion ein technischer Block ---
  var SEITEN_MODULE = {
    start: { label: 'Startseite', text: 'Hero mit klarer Aussage + Haupt-CTA, Kurzvorstellung, Überblick der Leistungen, Vertrauenselemente, Abschluss-CTA. Kerninfos (was, für wen, Kontakt) sofort sichtbar.' },
    ueber: { label: 'Über uns', text: 'Geschichte/Werte/USP aus dem Briefing, ggf. Team-Anriss. Authentisch, keine Floskeln, nichts erfinden.' },
    leistungen: { label: 'Leistungen', text: 'Je Leistung aus dem Briefing ein Abschnitt/Karte (Name, kurze Beschreibung, ggf. Preis). Struktur so, dass jede Leistung auch einzeln auffindbar ist.' },
    team: { label: 'Team', text: 'Personen aus dem Briefing (Name, Rolle, Foto via sc_bild). Nur echte Personen.' },
    referenzen: { label: 'Referenzen', text: 'NUR echte, vom Kunden gelieferte Referenzen/Projekte. Keine geliefert → Abschnitt weglassen, nichts erfinden.' },
    galerie: { label: 'Galerie', text: 'Responsive Bildergalerie aus den hochgeladenen Fotos, lazy-load, je Bild sc_bild + Alt-Text.' },
    faq: { label: 'FAQ', text: 'Fragen/Antworten aus dem Briefing als FAQ-Abschnitt + FAQPage-JSON-LD.' },
    blog: { label: 'Blog / News', text: 'Übersicht + Detailseiten. Beitragstexte als sc_richtext editierbar. Nur anlegen, wenn Inhalte/Absicht vorhanden.' },
    kontakt: { label: 'Kontakt', text: 'Adresse, Öffnungszeiten (sc_text) und Kontaktwege. Klar und vollständig. (Formular nur, wenn Funktion „Kontaktformular" gewählt.)' },
    karriere: { label: 'Karriere / Jobs', text: 'Offene Stellen aus dem Briefing; einfache Bewerbungs-Möglichkeit (Mail/Formular).' }
  };
  var FUNKTION_MODULE = {
    kontaktformular: { label: 'Kontaktformular', text: 'Formular (Name, E-Mail, Nachricht, Pflicht-Checkbox Datenschutz). Serverseitige Verarbeitung in kontakt-senden.php: Eingaben validieren, CSRF-Token, versteckter Honeypot gegen Spam, Versand per mail() an die Kontaktadresse aus dem Briefing, klare Erfolgs-/Fehlermeldung. Keine unnötige Speicherung personenbezogener Daten.' },
    termine: { label: 'Terminbuchung', text: 'Das im Briefing genannte Buchungstool einbinden (Consent-konform, lädt erst nach Einwilligung). Kein Tool genannt → „Termin anfragen"-Button, der auf das Kontaktformular führt. Keine erfundene Buchungslogik.' },
    newsletter: { label: 'Newsletter', text: 'Anmeldeformular mit Double-Opt-In, Anbindung an das im Briefing genannte Tool. Kein Tool genannt → weglassen.' },
    karte: { label: 'Karte / Anfahrt', text: 'Karten-Einbindung mit Zwei-Klick-/Consent-Lösung (§25 TDDDG: Karte lädt erst nach aktiver Einwilligung), Adresse aus dem Briefing. Alternativ statische Karte ohne Tracking.' },
    bewertungen: { label: 'Google-Bewertungen', text: 'NUR echte, vom Kunden freigegebene Bewertungen einbinden. Nichts erfinden. Nicht freigegeben → weglassen.' },
    downloads: { label: 'Downloads', text: 'Download-Bereich für vom Kunden bereitgestellte Dateien (PDF etc.).' },
    chat: { label: 'KI-Chat-Assistent', text: 'Einbindungs-Snippet des Sartu-KI-Assistenten (Platzhalter-ID, DSGVO-Hinweis). Keine eigene Chat-Logik bauen.' },
    mehrsprachig: { label: 'Mehrsprachigkeit', text: 'Sprachumschalter + korrekte hreflang-Angaben. Rechtstexte bleiben deutsch. Nur die tatsächlich bestellten Sprachen.' }
  };
  var PAKET_SCOPE = {
    start: 'Paket „Start": EINE Seite (One-Pager) — alle Inhalte als Abschnitte auf einer scrollbaren Seite (plus Pflichtseiten Impressum/Datenschutz).',
    wachstum: 'Paket „Wachstum": mehrseitig, bis zu 8 Seiten.',
    platzhirsch: 'Paket „Platzhirsch": mehrseitig, bis zu 20 Seiten (inkl. Team/Jobs möglich).'
  };

  // Baut aus Briefing + Auswahl einen fertigen, kopierbaren KI-Bau-Auftrag.
  // sel = { seiten: [values], funktionen: [values] } — nur diese werden gebaut.
  function buildKiPrompt(answers, project, sel) {
    answers = answers || {};
    sel = sel || { seiten: [], funktionen: [] };
    var firma = answers.firmenname || (project && project.titel) || 'Kunde';
    var siteId = slugify(project && project.titel ? project.titel : firma);
    var paket = PAKET_ALIAS[String((project && project.paket) || 'start').toLowerCase()] || 'start';

    var briefing = '';
    (BRIEFING2.steps || []).forEach(function (step) {
      var lines = '';
      (step.fields || []).forEach(function (f) {
        var v = answers[f.key];
        if (v == null || (Array.isArray(v) && !v.length) || String(v).trim() === '') return;
        var txt = plainAns(f, v);
        if (txt.trim() === '') return;
        lines += '- ' + f.label + ': ' + txt.replace(/\n+/g, ' / ') + '\n';
      });
      if (lines) briefing += '\n### ' + step.title + '\n' + lines;
    });
    if (!briefing) briefing = '\n(Noch keine Briefing-Antworten vorhanden.)\n';

    var seitenBlock = (sel.seiten || []).map(function (v) {
      var m = SEITEN_MODULE[v]; return m ? '### ' + m.label + '\n' + m.text : '';
    }).filter(Boolean).join('\n\n') || '(keine ausgewählt)';
    var funktBlock = (sel.funktionen || []).map(function (v) {
      var m = FUNKTION_MODULE[v]; return m ? '### ' + m.label + '\n' + m.text : '';
    }).filter(Boolean).join('\n\n') || '(keine ausgewählt)';

    return [
'Baue eine vollständige, individuelle Website in klassischem PHP (keine Frameworks, kein Baukasten).',
'WICHTIG: einzigartiges, maßgeschneidertes Design passend zu Branche und Stimmung — KEIN Vorlagen-/Template-Look.',
'Kunde: ' + firma + '  ·  Sartu-Site-ID: ' + siteId,
'Umfang: ' + (PAKET_SCOPE[paket] || PAKET_SCOPE.start),
'',
'## Briefing des Kunden (Inhalte)',
briefing,
'## NUR DIESE SEITEN BAUEN',
seitenBlock,
'',
'## NUR DIESE FUNKTIONEN BAUEN',
funktBlock,
'',
'## Was NICHT gebaut wird',
'Baue ausschließlich die oben aufgeführten Seiten und Funktionen. Alles andere wurde NICHT bestellt und gehört NICHT auf die Seite —',
'insbesondere KEIN Shop, kein Login-/Mitgliederbereich, keine laufende SEO-Betreuung/Keyword-Optimierung über die Basis hinaus,',
'keine zusätzlichen Funktionen oder Unterseiten. Im Zweifel weglassen.',
'',
'## Feste Sartu-Vorgaben (immer)',
'- Individuelles, sauberes, semantisches HTML/CSS. Responsiv und schnell.',
'- Basis-SEO (in jedem Paket enthalten): sinnvoller <title>, meta description, saubere Überschriften-Hierarchie, Alt-Texte.',
'- DSGVO-konform und barrierearm (BFSG): Kontraste, Fokus-Sichtbarkeit, Alt-Texte, Tastaturbedienung.',
'- Pflichtseiten Impressum & Datenschutz anlegen (Platzhalter, wo Daten fehlen — nichts erfinden).',
'- KEINE erfundenen Referenzen, Bewertungen, Kundenlogos oder Fake-Fotos. Nur echte/gelieferte Inhalte oder neutrale Platzhalter.',
'- Hersteller-Vermerk im Footer JEDER Seite (wie bei Agenturen): dezent „Webdesign von <a href=\"' + SARTU_URL + '\" rel=\"noopener\">Sartu</a>" — normaler Link (dofollow), unaufdringlich gestylt. Fest im Code, NICHT editierbar (kein sc_-Feld), der Kunde soll ihn nicht entfernen können.',
'',
'## Editierbare Stellen mit Sartu-Feldern markieren (Kunde ändert sie später selbst im Portal)',
'Die Datei sartu-edit.php liegt neben der Seite. Verwende diese Platzhalter:',
'  - Oben auf JEDER Seite:   <?php require __DIR__."/sartu-edit.php"; sc_site("' + siteId + '"); ?>',
'  - Editierbarer Text:      <?= sc_text("schluessel", "Standardtext", "Label fürs Portal") ?>',
'  - Mehrzeiliger Text:      <?= sc_richtext("schluessel", "Standardtext", "Label") ?>',
'  - Editierbares Bild:      <img src="<?= sc_bild("schluessel", "assets/standard.jpg", "Label") ?>" alt="...">',
'  - Editierbare Farbe:      sc_farbe("akzent", "#0f766e", "Akzentfarbe")   (als CSS-Variable einsetzen)',
'  - Unten auf JEDER Seite (Statistik):  <?= sc_track() ?>',
'Editierbar machen: Öffnungszeiten, Kontaktdaten (Telefon, E-Mail, Adresse), die Haupt-Textbausteine jeder Seite,',
'die Hauptbilder und die zentralen Farben. Jeder Schlüssel eindeutig, sprechendes Label.',
'NICHT editierbar (fest im Code): Layout, Seitenstruktur, Navigation, rechtliche Texte.',
'',
'## Liefere',
'Alle PHP-Seiten, eine gemeinsame CSS-Datei und am Ende eine Liste aller vergebenen sc_-Schlüssel mit Label und Typ.'
    ].join('\n');
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
      api('api/admin/invoices.php'),
      api('api/admin/aktionen.php')
    ]).then(function (out) {
      state.briefings = out[0].briefings || [];
      state.projects = out[1].projects || [];
      state.profiles = out[2].profiles || out[1].profiles || [];
      state.offers = out[3].offers || [];
      state.invoices = out[4].invoices || [];
      state.aktionen = out[5].aktionen || [];
      renderOverview();
      renderBriefings();
      renderOffers();
      renderInvoicesAdmin();
      renderProjects();
      renderCustomers(out[2].projects || []);
      renderAktionen();
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
    renderAktionen();
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
  var PAKET_KORR = { start: 1, wachstum: 1, platzhirsch: 2 };
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
      '<div class="field"><label>Aktion anwenden (optional)</label><select id="oAktion"><option value="">keine Aktion</option></select><span class="muted" id="oAktionNote" style="display:block;font-size:12px;margin-top:6px;"></span></div>' +
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

    // Aktions-Rabatt: nur laufende Prozent-/Fest-Aktionen, die aufs gewählte Paket passen.
    function aktionApplyPrice(preis, a) {
      var w = Number(a.wert || 0);
      if (a.typ === 'prozent') return Math.round(preis * (100 - Math.min(100, Math.max(0, w))) / 100);
      if (a.typ === 'fest') return Math.max(0, preis - w);
      return preis;
    }
    function applicableAktionen(paket) {
      var heute = new Date().toISOString().slice(0, 10);
      return state.aktionen.filter(function (a) {
        if (Number(a.aktiv) !== 1) return false;
        if (a.typ !== 'prozent' && a.typ !== 'fest') return false;
        if (a.start_am && String(a.start_am).slice(0, 10) > heute) return false;
        if (a.end_am && String(a.end_am).slice(0, 10) < heute) return false;
        return a.ziel === 'alle' || a.ziel === 'paket:' + paket;
      });
    }
    function rebuildAktionen() {
      var sel = document.getElementById('oAktion');
      if (!sel) return;
      var cur = sel.value;
      var list = applicableAktionen(document.getElementById('oPaket').value);
      sel.innerHTML = '<option value="">keine Aktion</option>' + list.map(function (a) {
        return '<option value="' + a.id + '">' + esc(a.name + ' (' + aktionWertText(a) + ')') + '</option>';
      }).join('');
      if (cur && list.some(function (a) { return a.id === cur; })) sel.value = cur;
      updateAktionNote();
    }
    function selectedAktion() {
      var id = document.getElementById('oAktion').value;
      return state.aktionen.filter(function (a) { return a.id === id; })[0] || null;
    }
    function updateAktionNote() {
      var note = document.getElementById('oAktionNote');
      var a = selectedAktion();
      var preis = Number(document.getElementById('oPreis').value || 0);
      if (!a || !preis) { note.textContent = ''; return; }
      var fin = aktionApplyPrice(preis, a);
      note.textContent = fin < preis
        ? '→ Aktionspreis: ' + fin.toLocaleString('de-DE') + ' € (regulär ' + preis.toLocaleString('de-DE') + ' €, −' + (preis - fin).toLocaleString('de-DE') + ' €)'
        : 'Diese Aktion senkt den Preis nicht.';
    }
    document.getElementById('oAktion').addEventListener('change', updateAktionNote);
    document.getElementById('oPreis').addEventListener('input', updateAktionNote);
    rebuildAktionen();

    document.getElementById('oPaket').addEventListener('change', function () {
      var v = this.value;
      document.getElementById('oPreis').value = PAKET_PREIS[v] || '';
      document.getElementById('oKorr').value = PAKET_KORR[v] || '';
      rebuildAktionen();
    });
    document.getElementById('oCare').addEventListener('change', function () {
      document.getElementById('oCarePreis').value = CARE_PREIS[this.value] || '';
    });
    document.getElementById('oSend').addEventListener('click', function () {
      var btn = this; btn.disabled = true;
      var regulaer = Number(document.getElementById('oPreis').value || 0);
      var akt = selectedAktion();
      var finalPreis = akt ? aktionApplyPrice(regulaer, akt) : regulaer;
      var hatRabatt = akt && finalPreis < regulaer;
      send('api/admin/offers.php', {
        briefing_id: b.id,
        email: k.email || b.kontakt_email || '',
        name: k.name || b.kontakt_name || '',
        titel: document.getElementById('oTitel').value || 'Website-Projekt',
        paket: document.getElementById('oPaket').value,
        preis_einmalig: hatRabatt ? finalPreis : regulaer,
        preis_regulaer: hatRabatt ? regulaer : '',
        aktion_label: hatRabatt ? (akt.name + ' ' + aktionWertText(akt)) : '',
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
      (o.preis_regulaer && Number(o.preis_regulaer) > Number(o.preis_einmalig)
        ? '<div class="pt-kv"><span>Regulär</span><strong style="text-decoration:line-through;opacity:.7;">' + esc(euro(o.preis_regulaer)) + '</strong></div>' +
          '<div class="pt-kv"><span>' + esc(o.aktion_label || 'Aktion') + '</span><strong style="color:#0f766e;">Aktionspreis ' + esc(euro(o.preis_einmalig)) + '</strong></div>'
        : '<div class="pt-kv"><span>Einmalpreis</span><strong>' + esc(euro(o.preis_einmalig)) + '</strong></div>') +
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
      '<div class="row row-end" style="gap:8px;"><button class="btn btn-ghost" id="pPrompt">🤖 Prompt für KI kopieren</button><button class="btn btn-primary" id="pSave">Projekt speichern</button></div>' +
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
    document.getElementById('pPrompt').addEventListener('click', function () {
      var ans = state.preview ? (state._demoBriefing || {}) : (state._briefingAnswers || {});
      showPromptBuilder(ans, p);
    });
    fillBriefing(p);
  }

  // Auswahl der Bausteine (vorausgewählt aus dem Briefing) → Prompt zusammensetzen.
  function showPromptBuilder(answers, project) {
    var wantSeiten = Array.isArray(answers.seiten) ? answers.seiten : [];
    var wantFunk = Array.isArray(answers.funktionen) ? answers.funktionen : [];
    function checks(map, want) {
      return Object.keys(map).map(function (k) {
        var on = want.indexOf(k) > -1;
        return '<label class="pb-chk"><input type="checkbox" data-k="' + k + '"' + (on ? ' checked' : '') + '> ' + esc(map[k].label) + '</label>';
      }).join('');
    }
    openModal(
      '<div class="spread"><h2 style="color:#fff">Prompt zusammenstellen</h2><button class="btn btn-ghost btn-sm" id="pbClose">Schließen</button></div>' +
      '<p class="muted">Vorausgewählt aus dem Briefing. Nur Angehaktes kommt in den Prompt — so bekommt der Kunde genau das Bestellte.</p>' +
      '<style>.pb-chk{display:inline-flex;align-items:center;gap:7px;background:rgba(255,255,255,.05);padding:7px 11px;border-radius:8px;margin:0 8px 8px 0;cursor:pointer;font-size:.92rem;}</style>' +
      '<h3 style="color:#fff;margin:14px 0 8px;">Seiten</h3><div id="pbSeiten">' + checks(SEITEN_MODULE, wantSeiten) + '</div>' +
      '<h3 style="color:#fff;margin:14px 0 8px;">Funktionen</h3><div id="pbFunk">' + checks(FUNKTION_MODULE, wantFunk) + '</div>' +
      '<div class="row row-end" style="margin-top:16px;"><button class="btn btn-primary" id="pbGo">Prompt erzeugen</button></div>'
    );
    document.getElementById('pbClose').addEventListener('click', closeModal);
    document.getElementById('pbGo').addEventListener('click', function () {
      function picked(id) {
        return Array.prototype.filter.call(document.querySelectorAll('#' + id + ' input:checked'), function () { return true; })
          .map(function (el) { return el.getAttribute('data-k'); });
      }
      var sel = { seiten: picked('pbSeiten'), funktionen: picked('pbFunk') };
      showPrompt(buildKiPrompt(answers, project, sel));
    });
  }

  function showPrompt(text) {
    openModal(
      '<div class="spread"><h2 style="color:#fff">KI-Bau-Prompt</h2><button class="btn btn-ghost btn-sm" id="prClose">Schließen</button></div>' +
      '<p class="muted">Aus dem Briefing dieses Kunden erzeugt. In Claude Code / Codex einfügen — die Seite entsteht individuell und mit editierbaren Feldern fürs Portal.</p>' +
      '<textarea id="prText" rows="16" style="width:100%;box-sizing:border-box;font-family:ui-monospace,Menlo,Consolas,monospace;font-size:12px;line-height:1.45;" readonly></textarea>' +
      '<div class="row row-end" style="margin-top:10px;"><button class="btn btn-primary" id="prCopy">In Zwischenablage kopieren</button></div>'
    );
    var ta = document.getElementById('prText');
    ta.value = text; // via value setzen: kein HTML-Escaping, <?php bleibt erhalten
    document.getElementById('prClose').addEventListener('click', closeModal);
    document.getElementById('prCopy').addEventListener('click', function () {
      var btn = this;
      function done() { btn.textContent = '✓ Kopiert'; setTimeout(function () { btn.textContent = 'In Zwischenablage kopieren'; }, 2000); }
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(ta.value).then(done, function () { ta.select(); document.execCommand('copy'); done(); });
      } else { ta.select(); document.execCommand('copy'); done(); }
    });
  }

  function fillBriefing(p) {
    var box = document.getElementById('pBriefing');
    if (!box) return;
    state._briefingAnswers = {};
    function render(answers, status) {
      state._briefingAnswers = answers || {};
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

  function renderAktionen() {
    var body = document.getElementById('aktionenBody');
    if (!body) return;
    if (!state.aktionen.length) {
      body.innerHTML = '<tr><td colspan="5" class="muted">Noch keine Aktionen. Legen Sie mit „+ Neue Aktion" eine an.</td></tr>';
      return;
    }
    var heute = new Date().toISOString().slice(0, 10);
    body.innerHTML = '';
    state.aktionen.forEach(function (a) {
      var laeuft = Number(a.aktiv) === 1 &&
        (!a.start_am || String(a.start_am).slice(0, 10) <= heute) &&
        (!a.end_am || String(a.end_am).slice(0, 10) >= heute);
      var badge = Number(a.aktiv) !== 1
        ? '<span class="badge">aus</span>'
        : (laeuft ? '<span class="badge badge-ok">läuft</span>' : '<span class="badge">geplant/vorbei</span>');
      var tr = document.createElement('tr');
      tr.className = 'clickable';
      tr.innerHTML = '<td>' + esc(a.name || '-') + '</td><td>' + esc(aktionWertText(a)) + '</td><td>' +
        esc(AKTION_ZIELE[a.ziel] || a.ziel) + '</td><td>' + esc(aktionZeitraum(a)) + '</td><td>' + badge + '</td>';
      tr.addEventListener('click', function () { openAktion(a); });
      body.appendChild(tr);
    });
  }

  function openAktion(a) {
    a = a || {};
    var isEdit = !!a.id;
    function opt(map, sel) {
      return Object.keys(map).map(function (k) {
        return '<option value="' + k + '"' + (k === sel ? ' selected' : '') + '>' + esc(map[k]) + '</option>';
      }).join('');
    }
    openModal(
      '<div class="spread"><h2 style="color:#fff">' + (isEdit ? 'Aktion bearbeiten' : 'Neue Aktion') + '</h2><button class="btn btn-ghost btn-sm" id="akClose">Schließen</button></div>' +
      '<div class="field"><label>Name der Aktion</label><input id="akName" type="text" placeholder="z. B. Sommer-Aktion" value="' + esc(a.name || '') + '"></div>' +
      '<div class="grid-2" style="gap:12px;">' +
        '<div class="field"><label>Typ</label><select id="akTyp">' + opt(AKTION_TYPEN, a.typ || 'prozent') + '</select></div>' +
        '<div class="field"><label>Wert</label><input id="akWert" type="number" min="0" value="' + esc(a.wert != null ? a.wert : 10) + '"><span class="muted" id="akWertHint" style="font-size:12px;"></span></div>' +
        '<div class="field"><label>Gilt für</label><select id="akZiel">' + opt(AKTION_ZIELE, a.ziel || 'alle') + '</select></div>' +
        '<div class="field"><label>Status</label><select id="akAktiv"><option value="1"' + (Number(a.aktiv) !== 0 ? ' selected' : '') + '>aktiv</option><option value="0"' + (Number(a.aktiv) === 0 ? ' selected' : '') + '>aus</option></select></div>' +
        '<div class="field"><label>Start (optional)</label><input id="akStart" type="date" value="' + esc((a.start_am || '').slice(0, 10)) + '"></div>' +
        '<div class="field"><label>Ende (optional)</label><input id="akEnd" type="date" value="' + esc((a.end_am || '').slice(0, 10)) + '"></div>' +
      '</div>' +
      '<div class="field"><label>Aufkleber (optional)</label><input id="akBadge" type="text" placeholder="leer = automatisch, z. B. -30 %" value="' + esc(a.badge || '') + '"></div>' +
      '<div class="field"><label>Banner-Text (optional)</label><input id="akHinweis" type="text" placeholder="z. B. Zum Sommer: 30 % auf alle Pakete" value="' + esc(a.hinweis || '') + '"></div>' +
      '<div class="row row-end" style="gap:8px;">' +
        (isEdit ? '<button class="btn btn-ghost" id="akDelete">Löschen</button>' : '') +
        '<button class="btn btn-primary" id="akSave">Speichern</button>' +
      '</div>'
    );
    function updateHint() {
      var t = document.getElementById('akTyp').value;
      document.getElementById('akWertHint').textContent =
        t === 'prozent' ? '% Rabatt (0–100)' : (t === 'fest' ? '€ Abzug' : 'Anzahl Monate');
    }
    document.getElementById('akTyp').addEventListener('change', updateHint);
    updateHint();
    document.getElementById('akClose').addEventListener('click', closeModal);
    document.getElementById('akSave').addEventListener('click', function () {
      var payload = {
        id: a.id || '',
        name: document.getElementById('akName').value || '',
        typ: document.getElementById('akTyp').value,
        ziel: document.getElementById('akZiel').value,
        wert: document.getElementById('akWert').value || 0,
        badge: document.getElementById('akBadge').value || '',
        hinweis: document.getElementById('akHinweis').value || '',
        start_am: document.getElementById('akStart').value || '',
        end_am: document.getElementById('akEnd').value || '',
        aktiv: document.getElementById('akAktiv').value === '1' ? 1 : 0
      };
      if (!payload.name.trim()) { showErr('Bitte einen Namen angeben.'); return; }
      send('api/admin/aktionen.php', payload, 'POST')
        .then(function () { closeModal(); return loadAll(); })
        .catch(function (e) { showErr(e.message); });
    });
    var del = document.getElementById('akDelete');
    if (del) del.addEventListener('click', function () {
      send('api/admin/aktionen.php', { id: a.id }, 'DELETE')
        .then(function () { closeModal(); return loadAll(); })
        .catch(function (e) { showErr(e.message); });
    });
  }

  var aktNewBtn = document.getElementById('aktNew');
  if (aktNewBtn) aktNewBtn.addEventListener('click', function () { openAktion(null); });

  Array.prototype.forEach.call(document.querySelectorAll('.tab'), function (btn) {
    btn.addEventListener('click', function () {
      Array.prototype.forEach.call(document.querySelectorAll('.tab'), function (b) { b.classList.toggle('is-on', b === btn); });
      ['anfragen', 'angebote', 'rechnungen', 'projekte', 'kunden', 'aktionen'].forEach(function (name) {
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
      { id: 'o1', created_at: '2026-07-06T10:00:00', name: 'Peter Klein', email: 'peter@klein-elektro.de', paket: 'start', preis_einmalig: 1097, preis_regulaer: 1290, aktion_label: 'Sommer-Aktion −15 %', care_stufe: 'care-s', care_preis: 49, korrekturrunden: 1, liefertext: 'in 7 Werktagen', gueltig_bis: '2026-07-31', umfang: 'One-Pager\nTexte inklusive', status: 'gesendet' },
      { id: 'o2', created_at: '2026-07-04T14:00:00', name: 'Sabine Beispiel', email: 'sabine@beispiel-hotel.de', paket: 'platzhirsch', preis_einmalig: 6490, care_stufe: 'care-l', care_preis: 249, korrekturrunden: 3, liefertext: 'in 7–14 Werktagen', status: 'angenommen', angenommen_am: '2026-07-05T09:30:00', angenommen_ip: '84.12.x.x', agb_version: 'AGB-2026-07' }
    ];
    state._demoBriefing = {
      firmenname: 'Muster Bäckerei', branche: 'Bäckerei & Café', gegruendet: '1985',
      steckbrief: 'Handwerksbäckerei mit eigenem Café. Besonders: Sauerteigbrot, alles ohne Zusatzstoffe.',
      zielgruppe: 'Familien & Berufstätige aus der Umgebung, die Wert auf Qualität legen',
      usp: 'Echtes Handwerk, tägliche frische Backwaren, gemütliches Café',
      hauptziele: ['anfragen', 'termine'], hat_logo: 'ja',
      ci_farben: 'Warmes Braun/Beige, #7a4b2b', stimmung: ['modern', 'warm'],
      seiten: ['start', 'leistungen', 'ueber', 'kontakt'],
      leistungen_inhalt: 'Brot & Brötchen – täglich frisch\nKuchen & Torten – auch auf Bestellung\nCafé – Frühstück & Kaffee',
      funktionen: ['kontaktformular', 'karte', 'bewertungen'],
      muss_rein: 'Öffnungszeiten gut sichtbar, Bestell-Telefonnummer',
      telefon: '030 123456', kleinunternehmer: 'nein'
    };
    state.invoices = [
      { id: 'r1', nummer: '2026-0001', kunde: 'Muster Bäckerei GmbH', ausgestellt_am: '2026-07-05', faellig_am: '2026-07-19', betrag: '3.290,00 €', status: 'offen' },
      { id: 'r2', nummer: '2026-0002', kunde: 'Hotel Beispiel', ausgestellt_am: '2026-07-02', faellig_am: '2026-07-16', betrag: '6.490,00 €', status: 'bezahlt' }
    ];
    state.aktionen = [
      { id: 'a1', name: 'Sommer-Aktion', typ: 'prozent', ziel: 'alle', wert: 15, badge: '', hinweis: 'Zum Sommer: 15 % auf alle Website-Pakete.', start_am: '2026-07-01', end_am: '2026-08-31', aktiv: 1 },
      { id: 'a2', name: 'KI-Assistent Setup-Aktion', typ: 'fest', ziel: 'addon:ki-assistent', wert: 300, badge: '990 → 690 €', hinweis: 'KI-Chat-Assistent: 300 € Rabatt auf die Einrichtung.', start_am: '', end_am: '2026-09-30', aktiv: 1 },
      { id: 'a3', name: 'Neujahrs-Aktion (Entwurf)', typ: 'gratis_monate', ziel: 'addon:seo-betreuung', wert: 2, badge: '', hinweis: '', start_am: '2027-01-01', end_am: '2027-01-31', aktiv: 0 }
    ];
    renderAll();
  } else {
    requireAdmin().then(function (ok) {
      if (ok) loadAll();
    });
  }
})();
