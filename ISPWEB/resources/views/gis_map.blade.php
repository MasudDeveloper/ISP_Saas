<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GIS Fiber Layout Map</title>
    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #map { height: calc(100vh - 64px); width: 100%; z-index: 1; }
        .controls-panel { z-index: 1000; position: absolute; top: 80px; right: 20px; }
    </style>
</head>
<body class="bg-gray-100">

    <!-- Topbar -->
    <header class="bg-gray-900 text-white h-16 flex items-center justify-between px-6 shadow-md">
        <h1 class="text-xl font-bold flex items-center gap-2">
            <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            ISP GIS Fiber Map
        </h1>
        <div class="flex gap-4">
            <button id="btnDrawMode" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded-lg text-sm font-medium transition">Draw Fiber Line</button>
            <button id="btnAddTJ" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 rounded-lg text-sm font-medium transition">Add TJ Box</button>
        </div>
    </header>

    <div class="relative">
        <!-- The Map -->
        <div id="map"></div>
        
        <!-- Action Panel (Hidden by default) -->
        <div id="actionPanel" class="controls-panel bg-white p-4 rounded-xl shadow-xl border border-gray-200 hidden w-72">
            <h3 id="panelTitle" class="font-bold text-gray-800 mb-4">Add Item</h3>
            
            <!-- TJ Box Form -->
            <div id="tjForm" class="hidden space-y-3">
                <input type="text" id="tjName" placeholder="Splitter Name (e.g. TJ-Zone1)" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                <input type="number" id="tjPorts" placeholder="Total Ports (e.g. 8)" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                <div class="text-xs text-gray-500">Click anywhere on the map to place this box.</div>
            </div>

            <!-- Fiber Line Form -->
            <div id="fiberForm" class="hidden space-y-3">
                <input type="text" id="fiberName" placeholder="Line Name (e.g. Core to Zone A)" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                <select id="fiberColor" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="#3B82F6">Blue (Core)</option>
                    <option value="#10B981">Green (Distribution)</option>
                    <option value="#F59E0B">Orange (Drop)</option>
                </select>
                <div class="text-xs text-gray-500">Click on the map to draw the path. Double click to finish.</div>
            </div>

            <button id="btnCancel" class="w-full mt-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition">Cancel Action</button>
        </div>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <script>
        // Initialize Map centered roughly at Dhaka, Bangladesh
        const map = L.map('map').setView([23.8103, 90.4125], 13);
        
        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        let currentMode = 'view'; // view, add_tj, draw_fiber
        let tempPolyline = null;
        let drawnPoints = [];

        // Load existing data passed from controller
        const existingTjBoxes = @json($tjBoxes);
        const existingFiberLines = @json($fiberLines);

        // Render Existing TJ Boxes
        existingTjBoxes.forEach(box => {
            L.marker([box.latitude, box.longitude])
                .addTo(map)
                .bindPopup(`<b>${box.name}</b><br>Ports: ${box.used_ports}/${box.total_ports}`);
        });

        // Render Existing Fiber Lines
        existingFiberLines.forEach(line => {
            const coords = JSON.parse(line.coordinates);
            L.polyline(coords, {color: line.color, weight: 4}).addTo(map).bindPopup(`<b>${line.name}</b>`);
        });

        // UI Interactions
        document.getElementById('btnAddTJ').addEventListener('click', () => {
            currentMode = 'add_tj';
            document.getElementById('actionPanel').classList.remove('hidden');
            document.getElementById('tjForm').classList.remove('hidden');
            document.getElementById('fiberForm').classList.add('hidden');
            document.getElementById('panelTitle').innerText = 'Add Splitter Box';
            map.getContainer().style.cursor = 'crosshair';
        });

        document.getElementById('btnDrawMode').addEventListener('click', () => {
            currentMode = 'draw_fiber';
            drawnPoints = [];
            document.getElementById('actionPanel').classList.remove('hidden');
            document.getElementById('fiberForm').classList.remove('hidden');
            document.getElementById('tjForm').classList.add('hidden');
            document.getElementById('panelTitle').innerText = 'Draw Fiber Line';
            map.getContainer().style.cursor = 'crosshair';
            
            if (tempPolyline) map.removeLayer(tempPolyline);
            tempPolyline = L.polyline([], {color: document.getElementById('fiberColor').value, weight: 4, dashArray: '5, 5'}).addTo(map);
        });

        document.getElementById('btnCancel').addEventListener('click', resetMode);

        function resetMode() {
            currentMode = 'view';
            document.getElementById('actionPanel').classList.add('hidden');
            map.getContainer().style.cursor = '';
            if (tempPolyline) {
                map.removeLayer(tempPolyline);
                tempPolyline = null;
            }
            drawnPoints = [];
        }

        // Map Click Events
        map.on('click', function(e) {
            if (currentMode === 'add_tj') {
                const name = document.getElementById('tjName').value;
                const ports = document.getElementById('tjPorts').value;
                if (!name) return alert("Please enter a name first.");

                // Save via API
                fetch('/gis/tj-box', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ name: name, lat: e.latlng.lat, lng: e.latlng.lng, total_ports: ports || 8 })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        L.marker([e.latlng.lat, e.latlng.lng]).addTo(map).bindPopup(`<b>${name}</b><br>Ports: 0/${ports}`);
                        resetMode();
                        alert("TJ Box Saved!");
                    }
                }).catch(err => console.error(err));

            } else if (currentMode === 'draw_fiber') {
                drawnPoints.push([e.latlng.lat, e.latlng.lng]);
                tempPolyline.setLatLngs(drawnPoints);
            }
        });

        // Double click to finish drawing line
        map.on('dblclick', function(e) {
            if (currentMode === 'draw_fiber' && drawnPoints.length > 1) {
                const name = document.getElementById('fiberName').value;
                const color = document.getElementById('fiberColor').value;
                if (!name) return alert("Please enter a line name first.");

                // Save via API
                fetch('/gis/fiber-line', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ name: name, color: color, coordinates: JSON.stringify(drawnPoints) })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        L.polyline(drawnPoints, {color: color, weight: 4}).addTo(map).bindPopup(`<b>${name}</b>`);
                        resetMode();
                        alert("Fiber Line Saved!");
                    }
                }).catch(err => console.error(err));
            }
        });
    </script>
</body>
</html>
