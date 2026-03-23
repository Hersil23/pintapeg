// PintaPeg - Service Worker
// Se implementara completo en Fase 7 (PWA)

const CACHE_NAME = 'pintapeg-v1';

self.addEventListener('install', (event) => {
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(clients.claim());
});

self.addEventListener('fetch', (event) => {
  event.respondWith(fetch(event.request));
});
