<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @auth
                        {{ __("You're logged in!") }}
                    @endauth
                    <!-- Google Maps Container -->
                    <div id="map" style="height: 500px; width: 100%; margin-top: 2rem;"></div>
                    <div id="pin-info" style="display:none; margin-top:1rem; padding:1rem; border:1px solid #e5e7eb; border-radius:0.5rem; background:#f9fafb;"></div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsApiKey }}"></script>
    <script>window.LaravelIsAuthenticated = {{ auth()->check() ? 'true' : 'false' }};</script>
    <script>
        let map;
        function initMap() {
            map = new google.maps.Map(document.getElementById('map'), {
                center: { lat: 54.5973, lng: -5.9301 },
                zoom: 12
            });
            // Fetch pins from backend (JSON endpoint)
            fetch('/pins/json')
                .then(response => response.json())
                .then(pins => {
                    pins.forEach(pin => {
                        // Use latest update's status for marker color if available
                        let latestStatus = pin.updates && pin.updates.length > 0
                            ? pin.updates.slice().sort((a, b) => new Date(b.created_at) - new Date(a.created_at))[0].status
                            : pin.status;
                        let color = 'green';
                        if (latestStatus === 'Worn') color = 'orange';
                        else if (latestStatus === 'Needs replaced') color = 'red';
                        else if (latestStatus === 'Missing') color = 'black';
                        const icon = {
                            path: google.maps.SymbolPath.CIRCLE,
                            scale: 8,
                            fillColor: color,
                            fillOpacity: 1,
                            strokeWeight: 1,
                            strokeColor: '#333'
                        };
                        const marker = new google.maps.Marker({
                            position: { lat: parseFloat(pin.latitude), lng: parseFloat(pin.longitude) },
                            map: map,
                            title: pin.title || '',
                            icon: icon
                        });
                        marker.addListener('click', function() {
                            showPinInfoWithUpdates(pin);
                        });
                    });
                });

        // Show pin info with updates and cycle button
        function showPinInfoWithUpdates(pin) {
            const infoBox = document.getElementById('pin-info');
            infoBox.style.display = 'block';
            // Parse updates, sort by date desc
            let updates = Array.isArray(pin.updates) ? pin.updates.slice().sort((a, b) => new Date(b.created_at) - new Date(a.created_at)) : [];
            // Add the parent pin as the last item (oldest)
            updates.push({
                status: pin.status,
                created_at: pin.created_at,
                photo: pin.photo,
                user: pin.user
            });
            let currentIdx = 0;
            function renderUpdate(idx) {
                let update = updates[idx];
                let photoHtml = '';
                if (update && update.photo) {
                    photoHtml = `<img src='/storage/${update.photo}' alt='Update Photo' style='max-width:100%;max-height:200px;margin-bottom:1rem;border-radius:0.5rem;'>`;
                }
                let userHtml = '';
                if (update && update.user && update.user.name) {
                    userHtml = `<div style='font-size:0.95rem; color:#374151; margin-bottom:0.5rem;'>By: <strong>${update.user.name}</strong></div>`;
                }
                let status = update ? update.status : '';
                let date = update ? update.created_at : '';
                let isOriginal = idx === updates.length - 1;
                let editBtnHtml = '';
                if (window.LaravelIsAuthenticated) {
                    editBtnHtml = `<a href='/pins/${pin.id}/edit' class='inline-block mt-2 ml-2 px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700'>Edit Pin</a>`;
                }
                infoBox.innerHTML = `
                    ${photoHtml}
                    ${userHtml}
                    <h3 style='font-size:1.25rem; font-weight:600; color:#374151;'>${pin.title || ''}</h3>
                    <p style='color:#6b7280;'>Status: <strong>${status || ''}</strong></p>
                    <div style='font-size:0.9rem; color:#9ca3af;'>Lat: ${pin.latitude}, Lng: ${pin.longitude}</div>
                    <div style='font-size:0.9rem; color:#6b7280; margin-top:0.5rem;'>${isOriginal ? 'Added' : 'Update'}: <strong>${date ? new Date(date).toLocaleString() : ''}</strong></div>
                    ${updates.length > 1 ? `<button id='cycle-update-btn' class='mt-2 px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700'>Show ${isOriginal ? 'Latest' : 'Previous'}</button>` : ''}
                    ${editBtnHtml}
                `;
                if (updates.length > 1) {
                    document.getElementById('cycle-update-btn').onclick = function() {
                        currentIdx = (currentIdx + 1) % updates.length;
                        renderUpdate(currentIdx);
                    };
                }
            }
            renderUpdate(0);
        }
        }
        window.onload = initMap;
    </script>
</x-app-layout>
