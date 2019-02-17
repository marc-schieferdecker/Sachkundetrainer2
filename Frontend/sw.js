const CACHE = 'wskt-cache-v1.12';
const expectedCaches = ['wskt-cache-v1.12'];
const cacheFiles = [
    '/Frontend/Resources/Public/images/logo.jpg',
    '/Frontend/Resources/Public/node_modules/bootstrap/dist/css/bootstrap.min.css',
    '/Frontend/Resources/Public/node_modules/bootstrap-select/dist/css/bootstrap-select.min.css',
    '/Frontend/Resources/Public/css/font-awesome-4.7.0/css/font-awesome.min.css',
    '/Frontend/Resources/Public/css/dataTables.bootstrap4.min.css',
    '/Frontend/Resources/Public/node_modules/video.js/dist/video-js.min.css',
    '/Frontend/Resources/Public/node_modules/jquery/dist/jquery.min.js',
    '/Frontend/Resources/Public/node_modules/cookieconsent/build/cookieconsent.min.css',
    '/Frontend/Resources/Public/node_modules/popper.js/dist/umd/popper.min.js',
    '/Frontend/Resources/Public/node_modules/tooltip.js/dist/umd/tooltip.min.js',
    '/Frontend/Resources/Public/node_modules/bootstrap/dist/js/bootstrap.min.js',
    '/Frontend/Resources/Public/node_modules/bootstrap-select/dist/js/bootstrap-select.min.js',
    '/Frontend/Resources/Public/node_modules/bootstrap-select/dist/js/i18n/defaults-de_DE.min.js',
    '/Frontend/Resources/Public/node_modules/cookieconsent/build/cookieconsent.min.js',
    '/Frontend/Resources/Public/node_modules/video.js/dist/video.min.js',
    '/Frontend/Resources/Public/node_modules/video.js/dist/lang/de.js',
    '/Frontend/Resources/Public/javascript/jquery.dataTables.min.js',
    '/Frontend/Resources/Public/javascript/dataTables.bootstrap4.min.js',
    '/Frontend/Resources/Public/javascript/jquery.md5.js',
    '/Frontend/Resources/Public/javascript/esrever.js',
    '/Frontend/Resources/Public/icons/android-icon-144x144.png',
    '/Frontend/Resources/Public/images/screenshot-favorisieren.png',
    '/Frontend/offline.html'
];

self.addEventListener('install', function(event) {
    self.skipWaiting();
    event.waitUntil(
        caches.open(CACHE).then(function(cache) {
            return cache.addAll(cacheFiles);
        })
    );
    console.log('Installed sw.js', event);
});

self.addEventListener('activate', function(event) {
    // delete any caches that aren't in expectedCaches
    // which will get rid of older version of cached files
    event.waitUntil(
        caches.keys().then(keys => Promise.all(
            keys.map(key => {
                if (!expectedCaches.includes(key)) {
                    return caches.delete(key);
                }
            })
        )).then(() => {
            console.log('sw cache updated to the next version!');
        })
    );
    console.log('Activated sw.js', event);
});

self.addEventListener('fetch', event => {
    if (event.request.mode === 'navigate' || (event.request.method === 'GET' && event.request.headers.get('accept').includes('text/html'))) {
        event.respondWith(
            fetch(event.request).catch(error => {
                return caches.match('/Frontend/offline.html');
            })
        );
    }
    else {
        event.respondWith(caches.match(event.request)
            .then(function (response) {
                return response || fetch(event.request);
            })
        );
    }
});
