<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Nearby Temples & Route — Bhaktikalpa</title>
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Poppins', sans-serif; margin:0; background: linear-gradient(to bottom,#f5f9ff,#eef3fb); color:#123; }
    header{ padding:14px; text-align:center;}
    #app { max-width:1100px; margin:18px auto; display:flex; gap:18px; padding:12px; }
    #map { flex:1 1 65%; height:78vh; border-radius:10px; box-shadow:0 6px 20px rgba(0,0,0,0.12); }
    #panel { flex:1 1 35%; max-height:78vh; overflow:auto; padding:12px; background:#fff; border-radius:10px; box-shadow:0 6px 20px rgba(0,0,0,0.06); }
    .controls { display:flex; gap:8px; justify-content:center; margin-bottom:12px; }
    select, input[type=number], button { padding:8px 10px; border-radius:6px; border:1px solid #cfe0ff; background:#f8fbff; color:#123; }
    .place { border-bottom:1px solid #eef4ff; padding:12px 6px; }
    .place h4 { margin:0 0 6px; color:#1b3a78; }
    .muted { color:#666; font-size:13px; }
    .actions { margin-top:8px; display:flex; gap:8px; flex-wrap:wrap; }
    .btn { padding:7px 10px; border-radius:6px; border:none; cursor:pointer; color:#fff; background: linear-gradient(90deg,#154281,#1c5dad); }
    .btn.secondary { background: linear-gradient(90deg,#d4af37,#e6b63b); color:#222; }
    #now { margin:10px 0; font-weight:600; color:#234; }
    @media (max-width:900px) { #app { flex-direction:column } #map { height:60vh } }
    /* hide controls on print */
    @media print {
      .controls, .actions, button, select, input { display:none !important; }
    }
  </style>
</head>
<body>
  <header>
    <img src="assets/images/logo/bakthi-logo.png" alt="logo" style="height:64px"><br>
    <strong style="font-size:1.2rem;color:#1b3a78;">Find Nearby Temples & Route</strong>
  </header>

  <div id="app">
    <div id="map"></div>

    <div id="panel">
      <div class="controls">
        <select id="radius">
          <option value="1000">1 km</option>
          <option value="2000">2 km</option>
          <option value="5000" selected>5 km</option>
          <option value="10000">10 km</option>
        </select>
        <button id="findBtn" class="btn">Find Temples</button>
      </div>

      <div id="now">Current location: <span id="locText">detecting...</span></div>
      <div id="placesList"></div>
      <p style="font-size:13px;color:#444;margin-top:10px;">Tip: Click a temple in the list to show it on the map. Use "Get Route" to draw directions.</p>
    </div>
  </div>

  <!-- Load Google Maps JS API (replace YOUR_GOOGLE_CLIENT_KEY) -->
  <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_CLIENT_KEY&libraries=places"></script>

  <script>
    // ---- CONFIG ----
    const NEARBY_ENDPOINT = 'nearby-api.php'; // server endpoint
    // ----------------

    let map, userMarker, directionsService, directionsRenderer;
    let currentPos = null;
    let markers = [];

    // initialize map with default center
    function initMap() {
      map = new google.maps.Map(document.getElementById('map'), {
        center: { lat: 20.5937, lng: 78.9629 }, // default India center
        zoom: 5,
        styles: [/* optional map styling here */]
      });

      directionsService = new google.maps.DirectionsService();
      directionsRenderer = new google.maps.DirectionsRenderer({ suppressMarkers: false });
      directionsRenderer.setMap(map);

      // get location
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(pos => {
          currentPos = { lat: pos.coords.latitude, lng: pos.coords.longitude };
          map.setCenter(currentPos);
          map.setZoom(14);
          document.getElementById('locText').textContent = currentPos.lat.toFixed(5) + ', ' + currentPos.lng.toFixed(5);

          userMarker = new google.maps.Marker({
            position: currentPos,
            map,
            title: 'You are here',
            icon: {
              path: google.maps.SymbolPath.CIRCLE,
              scale: 7,
              fillColor: '#1c5dad',
              fillOpacity: 1,
              strokeColor: '#fff',
              strokeWeight: 2
            }
          });

          // auto-find nearby on load
          fetchNearby();
        }, err => {
          console.warn('Geolocation error', err);
          document.getElementById('locText').textContent = 'Location permission denied';
        }, { enableHighAccuracy: true, timeout: 10000 });
      } else {
        alert('Geolocation is not supported by your browser.');
      }
    }

    // fetch nearby temples from server
    async function fetchNearby() {
      if (!currentPos) {
        alert('Current location not available yet.');
        return;
      }
      clearMarkers();
      document.getElementById('placesList').innerHTML = '<p class="muted">Searching nearby temples…</p>';

      const radius = document.getElementById('radius').value;
      try {
        const resp = await fetch(NEARBY_ENDPOINT, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ lat: currentPos.lat, lng: currentPos.lng, radius: parseInt(radius) })
        });
        if (!resp.ok) throw new Error('Nearby API error: ' + resp.status);
        const data = await resp.json();

        if (data.status !== 'OK' && data.status !== 'ZERO_RESULTS') {
          document.getElementById('placesList').innerHTML = '<p class="muted">Places API error: ' + (data.status || 'unknown') + '</p>';
          console.error(data);
          return;
        }

        const places = data.results || [];
        if (places.length === 0) {
          document.getElementById('placesList').innerHTML = '<p class="muted">No temples found within this radius.</p>';
          return;
        }

        // sort by distance (approx using straight-line)
        places.forEach(p => {
          const d = haversineDistance(currentPos.lat, currentPos.lng, p.geometry.location.lat, p.geometry.location.lng);
          p.__distanceKm = d;
        });
        places.sort((a,b) => a.__distanceKm - b.__distanceKm);

        renderPlaces(places);
      } catch (err) {
        console.error(err);
        document.getElementById('placesList').innerHTML = '<p class="muted">Search failed. See console.</p>';
      }
    }

    // render places into list + markers
    function renderPlaces(places) {
      const list = document.getElementById('placesList');
      list.innerHTML = '';
      places.forEach((place, idx) => {
        const li = document.createElement('div');
        li.className = 'place';
        li.innerHTML = `
          <h4>${escapeHtml(place.name)}</h4>
          <div class="muted">${escapeHtml(place.vicinity || '')} · ${(place.__distanceKm||0).toFixed(2)} km</div>
          <div class="actions">
            <button class="btn secondary" onclick="openInGoogleMaps(${place.geometry.location.lat}, ${place.geometry.location.lng})">Open in Maps</button>
            <button class="btn" onclick="showOnMap(${idx})">Show on Map</button>
            <button class="btn" onclick="routeTo(${idx})">Get Route</button>
          </div>
        `;
        list.appendChild(li);

        // marker
        const marker = new google.maps.Marker({
          position: place.geometry.location,
          map,
          title: place.name,
          label: { text: String(idx+1), color: 'white' },
          icon: { url: 'https://maps.gstatic.com/mapfiles/api-3/images/spotlight-poi2_hdpi.png' }
        });
        marker.place = place;
        markers.push(marker);

        // click marker -> show info window & center
        const info = new google.maps.InfoWindow({
          content: `<div style="min-width:200px"><strong>${escapeHtml(place.name)}</strong><div class="muted">${escapeHtml(place.vicinity||'')}</div></div>`
        });
        marker.addListener('click', () => {
          info.open(map, marker);
          map.panTo(marker.getPosition());
        });
      });
      // center to first place
      if (places[0]) map.panTo(places[0].geometry.location);
    }

    // show place by index (list index)
    function showOnMap(idx) {
      const marker = markers[idx];
      if (!marker) return;
      map.panTo(marker.getPosition());
      map.setZoom(17);
      new google.maps.InfoWindow({ content: `<b>${escapeHtml(marker.place.name)}</b><div class="muted">${escapeHtml(marker.place.vicinity||'')}</div>` }).open(map, marker);
    }

    // route to place index
    function routeTo(idx) {
      if (!currentPos) { alert('Current location not available'); return; }
      const dest = markers[idx].getPosition();
      const request = {
        origin: new google.maps.LatLng(currentPos.lat, currentPos.lng),
        destination: dest,
        travelMode: google.maps.TravelMode.DRIVING, // optionally WALKING or TRANSIT
        provideRouteAlternatives: false
      };
      directionsService.route(request, (result, status) => {
        if (status === 'OK') {
          directionsRenderer.setDirections(result);
        } else {
          alert('Directions request failed: ' + status);
        }
      });
    }

    // open in Google Maps app/site
    function openInGoogleMaps(lat, lng) {
      const url = `https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}&travelmode=driving`;
      window.open(url, '_blank');
    }

    // remove existing markers
    function clearMarkers() {
      markers.forEach(m => m.setMap(null));
      markers = [];
      directionsRenderer.set('directions', null);
    }

    // Haversine (km)
    function haversineDistance(lat1, lon1, lat2, lon2) {
      function toRad(x){ return x * Math.PI / 180; }
      const R = 6371;
      const dLat = toRad(lat2 - lat1);
      const dLon = toRad(lon2 - lon1);
      const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
                Math.sin(dLon/2) * Math.sin(dLon/2);
      const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
      return R * c;
    }

    // escape for HTML injection safety
    function escapeHtml(str) {
      if (!str) return '';
      return str.replace(/[&<>"']/g, function(m){ return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[m];});
    }

    // Hook up buttons
    document.getElementById('findBtn').addEventListener('click', fetchNearby);

    // Start map on load
    window.onload = initMap;
  </script>
</body>
</html>
