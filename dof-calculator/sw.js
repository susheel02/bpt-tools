const CACHE_NAME = 'dof-calculator-v0.0.3';
const urlsToCache = [
    './index.php',
    './assets/css/style.css?v=0.0.3',
    './assets/js/app.js',
    './ajax-handler.php',
    '../assets/css/tools.css?v=0.0.3',
    '../shared/header.php',
    '../shared/footer.php'
];

self.addEventListener('install', (event) => {
    console.log('Service Worker installing...');
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => {
                console.log('Service Worker caching app shell');
                return cache.addAll(urlsToCache);
            })
            .catch((error) => {
                console.error('Service Worker failed to cache:', error);
            })
    );
    // Force the waiting service worker to become the active service worker
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    console.log('Service Worker activating...');
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        console.log('Service Worker: Deleting old cache:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
    // Take control of all pages immediately
    return self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    // Skip non-GET requests
    if (event.request.method !== 'GET') {
        return;
    }
    
    // Skip external requests
    if (!event.request.url.startsWith(self.location.origin)) {
        return;
    }
    
    event.respondWith(
        caches.match(event.request)
            .then((response) => {
                // Return cached version if available, otherwise fetch from network
                if (response) {
                    console.log('Service Worker: Serving from cache:', event.request.url);
                    return response;
                }
                
                console.log('Service Worker: Fetching from network:', event.request.url);
                return fetch(event.request)
                    .then((response) => {
                        // Don't cache non-successful responses
                        if (!response || response.status !== 200 || response.type !== 'basic') {
                            return response;
                        }
                        
                        // Clone the response
                        const responseToCache = response.clone();
                        
                        // Cache static assets only
                        if (event.request.url.includes('.css') || 
                            event.request.url.includes('.js') || 
                            event.request.url.includes('.png') || 
                            event.request.url.includes('.jpg') || 
                            event.request.url.includes('.svg')) {
                            caches.open(CACHE_NAME)
                                .then((cache) => {
                                    cache.put(event.request, responseToCache);
                                });
                        }
                        
                        return response;
                    })
                    .catch((error) => {
                        console.error('Service Worker fetch failed:', error);
                        throw error;
                    });
            })
    );
});