self.addEventListener('fetch', function (event) {
    console.log('self.addEventlistener fetch init. Event object = ', event);
    event.respondWith(
        caches.match(event.request)
            .then(function (response) {
                // Cache hit - return response
                console.log('Cache hit');
                console.log('return response = ', response);
                if (response) {
                    return response;
                }

                // IMPORTANT: Clone the request. A request is a stream and
                // can only be consumed once. Since we are consuming this
                // once by cache and once by the browser for fetch, we need
                // to clone the response.
                var fetchRequest = event.request.clone();
                console.log('fetchRequest event.request.clone', fetchRequest);

                return fetch(fetchRequest).then(
                    function (response) {
                        console.log('return fetch(fetchRequest).then. Responde = ');
                        console.log(response);
                        // Check if we received a valid response
                        if (!response || response.status !== 200 || response.type !== 'basic') {
                            console.log('valid response');
                            return response;
                        }

                        // IMPORTANT: Clone the response. A response is a stream
                        // and because we want the browser to consume the response
                        // as well as the cache consuming the response, we need
                        // to clone it so we have two streams.
                        var responseToCache = response.clone();
                        console.log('var responseToCache = ', responseToCache);

                        caches.open('video-element-cache-v1')
                            .then(function (cache) {
                                console.log('caches.open video-element-cache-v1 = ', cache);
                                cache.put(event.request, responseToCache);
                                console.log('post cache.put');
                            });

                        console.log('final return response');
                        return response;
                    }
                );
            })
    );
});