const CACHE_NAME = 'absensi-cache-v1';
const urlsToCache = [
  '/login',
  '/assets/css/material-dashboard.min.css',
  '/assets/js/core/jquery-3.5.1.min.js',
  '/assets/img/favicon.png'
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => cache.addAll(urlsToCache))
  );
});

self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request)
      .then(response => response || fetch(event.request))
  );
});
