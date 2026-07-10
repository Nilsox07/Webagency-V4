/* Sartu Portal — Hell/Dunkel-Umschalter. Standard = Gerät (kein data-theme).
   Klick wechselt System → Hell → Dunkel. Wahl in localStorage. */
(function () {
  'use strict';
  var KEY = 'sartu-theme';
  var root = document.documentElement;
  var order = ['system', 'light', 'dark'];

  function stored() { try { return localStorage.getItem(KEY); } catch (e) { return null; } }
  function current() { var s = stored(); return (s === 'light' || s === 'dark') ? s : 'system'; }
  function apply(mode) {
    if (mode === 'light' || mode === 'dark') root.setAttribute('data-theme', mode);
    else root.removeAttribute('data-theme');
  }
  var ICON = {
    light: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>',
    dark: '<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"/></svg>',
    system: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 3a9 9 0 0 0 0 18z" fill="currentColor" stroke="none"/></svg>'
  };
  function label(btn, mode) {
    btn.innerHTML = ICON[mode] || ICON.system;
    var name = mode === 'light' ? 'Hell' : mode === 'dark' ? 'Dunkel' : 'System';
    btn.setAttribute('title', 'Design: ' + name + ' — klicken zum Wechseln');
    btn.setAttribute('aria-label', 'Design-Modus: ' + name);
  }

  var btn = document.getElementById('themeToggle');
  if (!btn) return;
  label(btn, current());
  btn.addEventListener('click', function () {
    var next = order[(order.indexOf(current()) + 1) % order.length];
    try { if (next === 'system') localStorage.removeItem(KEY); else localStorage.setItem(KEY, next); } catch (e) {}
    apply(next);
    label(btn, next);
  });
})();
