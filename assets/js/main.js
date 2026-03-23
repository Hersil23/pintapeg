/**
 * PintaPeg - Script principal
 * Se implementara en Fase 5
 */

// Registro del Service Worker
if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker.register('/sw.js')
      .catch(err => console.log('SW registro fallido:', err));
  });
}
