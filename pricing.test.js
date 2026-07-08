/* ============================================================
   Sartu · Tests für die Preisberechnung
   Lauf:  node pricing.test.js
   Testet exakt die Funktion, die auch im Browser rechnet
   (pricing-calc.js gegen die zentralen Daten in pricing.js).
   ============================================================ */
'use strict';
var PRICING = require('./pricing.js');
var PAY = require('./payment-terms.js');
var CALC = require('./pricing-calc.js');

var fails = 0;
function eq(label, got, want) {
  var ok = got === want;
  if (!ok) fails++;
  console.log((ok ? '✓' : '✗ FAIL') + '  ' + label + '  →  got ' + got + (ok ? '' : ', want ' + want));
}
// Hilfsfunktion: Add-on-State bauen
function addons(map) {
  var out = {};
  PRICING.addons.forEach(function (a) { out[a.id] = { selected: false, qty: a.qty ? a.qty.default : 1 }; });
  Object.keys(map || {}).forEach(function (id) {
    out[id] = { selected: true, qty: map[id] === true ? (out[id].qty) : map[id] };
  });
  return out;
}
function t(state) { return CALC.computeTotals(state, PRICING); }

console.log('— Preis-Tests —');

// 1) Basis + Schutz S (49 €/Mon, Pflicht), keine Add-ons
var r1 = t({ paket: 'basis', wartung: 'care-s', addons: addons({}) });
eq('Basis einmalig', r1.once, 1290);
eq('Basis monatlich (Schutz S 49)', r1.monthly, 49);

// 2) Pro + Schutz M (99) + Logo Lite (490) + Texte ×3 (120×3)
var r2 = t({ paket: 'pro', wartung: 'care-m', addons: addons({ 'logo-lite': true, texte: 3 }) });
eq('Pro+Logo Lite+Texte×3 einmalig', r2.once, 3290 + 490 + 360);
eq('Pro+Schutz M monatlich', r2.monthly, 99);

// 3) Platin + Schutz L (249) + KI-Chat-Assistent (Kombi: 990 einmalig + 79/Mon) + Terminbuchung (290 einmalig)
var r3 = t({ paket: 'platin', wartung: 'care-l', addons: addons({ 'ki-assistent': true, terminbuchung: true }) });
eq('Platin einmalig (KI-Assistent 990 + Termin 290)', r3.once, 6490 + 990 + 290);
eq('Platin monatlich (Schutz L 249 + KI-Betrieb 79)', r3.monthly, 249 + 79);

// 4) Enterprise = price null → Paket zählt NICHT in die Einmalsumme
var r4 = t({ paket: 'enterprise', wartung: 'care-l', addons: addons({ analytics: true }) });
eq('Enterprise einmalig (Paket null, nur Analytics 190)', r4.once, 190);
eq('Enterprise monatlich (Schutz L 249)', r4.monthly, 249);

// 5) Mengen-Clamp: Texte ×20 → max 10
var r5 = t({ paket: 'basis', wartung: 'care-s', addons: addons({ texte: 20 }) });
eq('Texte ×20 → geclamped auf 10 (120×10)', r5.once, 1290 + 1200);

// 6) Abgewähltes Add-on zählt nicht
var st6 = addons({ 'logo-lite': true }); st6['logo-lite'].selected = false;
var r6 = t({ paket: 'basis', wartung: 'care-s', addons: st6 });
eq('Abgewähltes Add-on zählt nicht', r6.once, 1290);

// 7) Express = +50 % vom Paketpreis, mind. 390 € (Pro 3290 → +1645)
var r7 = t({ paket: 'pro', wartung: 'care-m', addons: addons({ express: true }) });
eq('Express +50 % auf Pro (3290→+1645)', r7.once, 3290 + 1645);

// 8) Mehrsprachigkeit = +40 % je Sprache (Basis 1290, ×2 → +1032)
var r8 = t({ paket: 'basis', wartung: 'care-s', addons: addons({ mehrsprachig: 2 }) });
eq('Mehrsprachig ×2 (+40 % je Sprache) einmalig', r8.once, 1290 + 1032);

// 9) Extraseiten (Variante A): Pro + 5 Extraseiten (199×5)
var r9 = t({ paket: 'pro', wartung: 'care-m', extraPages: 5, addons: addons({}) });
eq('Pro + 5 Extraseiten einmalig (3290 + 995)', r9.once, 3290 + 995);
eq('Pro + 5 Extraseiten monatlich (Schutz M 99)', r9.monthly, 99);

// 10) Monatliche Retainer: SEO-Betreuung (490, ein Preis) + Profil Basic (79) + Schutz M (99)
var r10 = t({ paket: 'pro', wartung: 'care-m', addons: addons({ 'seo-betreuung': true, 'profil-basic': true }) });
eq('SEO-Betreuung + Profil Basic + Schutz M monatlich', r10.monthly, 99 + 490 + 79);

// 11) SEO-Betreuung + Profil Pro + Schutz L
var r12 = t({ paket: 'platin', wartung: 'care-l', addons: addons({ 'seo-betreuung': true, 'profil-pro': true }) });
eq('SEO-Betreuung + Profil Pro + Schutz L monatlich', r12.monthly, 249 + 490 + 149);

// 12) SEO-Option für Texte: +30 €/Seite (×5 = 150)
var r13 = t({ paket: 'basis', wartung: 'care-s', addons: addons({ texte: 5, 'texte-seo': 5 }) });
eq('Texte ×5 + SEO-Option ×5 einmalig', r13.once, 1290 + 600 + 150);

// 13) Enterprise: priceFrom ist reine Anzeige und zählt NICHT in die Summe
var r14 = t({ paket: 'enterprise', wartung: 'care-l', addons: addons({}) });
eq('Enterprise einmalig trotz priceFrom = 0', r14.once, 0);

// 7) Zahlungsstaffelung: Prozente ergeben 100 %
['basis', 'pro', 'platin', 'enterprise'].forEach(function (id) {
  var sum = PAY.forPackage(id).reduce(function (s, x) { return s + x.pct; }, 0);
  eq('Staffelung ' + id + ' = 100 %', sum, 100);
});

console.log(fails === 0 ? '\nAlle Tests bestanden ✓' : '\n' + fails + ' Test(s) fehlgeschlagen ✗');
process.exit(fails === 0 ? 0 : 1);
