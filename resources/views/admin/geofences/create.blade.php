@extends('layouts.app')

@section('title', 'Tambah Geofence')
@section('header', 'Tambah Zona Geofence')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<style>
    #map { z-index: 1; }
</style>
@endpush

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    {{-- Top Action Bar --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800 dark:text-white">Tambah Zona Baru</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Konfigurasikan pembatasan lokasi GPS untuk akses presensi karyawan</p>
        </div>
        <a href="{{ route('admin.geofences.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold transition-all">
            ← Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs space-y-1">
            @foreach ($errors->all() as $error)
                <p class="font-semibold">⚠️ {{ $error }}</p>
            @endforeach
        </div>
    @endif

    {{-- Form and Map Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        
        {{-- Map Column (Left) --}}
        <div class="lg:col-span-3 space-y-3">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-4 shadow-sm space-y-3">
                <div class="flex justify-between items-center px-1">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Peta Gambar Poligon</span>
                    <button type="button" id="btnResetPolygon" class="text-[10px] text-rose-500 font-bold bg-rose-500/10 px-2.5 py-1 rounded-full hover:bg-rose-500/20 transition-colors">🔄 Reset Poligon</button>
                </div>
                <div id="map" class="h-80 sm:h-[400px] w-full rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800 shadow-inner"></div>
            </div>
        </div>

        {{-- Form Column (Right) --}}
        <div class="lg:col-span-2">
            <form action="{{ route('admin.geofences.store') }}" method="POST" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Nama Zona</label>
                    <input type="text" name="zone_name" value="{{ old('zone_name') }}" placeholder="Contoh: Unit 1 PLTU Paiton" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-800 dark:text-slate-200 outline-none focus:border-brand-500" required>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Kode Zona</label>
                    <input type="text" name="zone_code" value="{{ old('zone_code') }}" placeholder="Contoh: ZONE-U1" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-800 dark:text-slate-200 outline-none focus:border-brand-500" required>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Latitude (Tengah)</label>
                        <input type="number" step="any" name="center_lat" id="center_lat" value="{{ old('center_lat') }}" placeholder="Otomatis dari poligon" class="w-full bg-slate-100 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-500 dark:text-slate-400 outline-none font-mono" required readonly>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Longitude (Tengah)</label>
                        <input type="number" step="any" name="center_lng" id="center_lng" value="{{ old('center_lng') }}" placeholder="Otomatis dari poligon" class="w-full bg-slate-100 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-500 dark:text-slate-400 outline-none font-mono" required readonly>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Batas Area Poligon</label>
                    <div class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-600 dark:text-slate-400 flex items-center justify-between">
                        <span>Status Gambar:</span>
                        <span id="poly_points_count" class="font-bold text-brand-600 dark:text-brand-400">0 Titik Terpilih</span>
                    </div>
                    <input type="hidden" name="coordinates" id="coordinates" value="{{ old('coordinates', '[]') }}">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Deskripsi Area</label>
                    <textarea name="description" placeholder="Keterangan area zona..." rows="3" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-800 dark:text-slate-200 outline-none focus:border-brand-500">{{ old('description') }}</textarea>
                </div>

                <div class="flex items-center gap-3 py-2 border-t border-slate-100 dark:border-slate-800/80">
                    <input type="checkbox" name="is_active" id="is_active" value="1" class="w-4 h-4 rounded text-brand-600 focus:ring-brand-500 border-slate-300" {{ old('is_active', true) ? 'checked' : '' }}>
                    <label for="is_active" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Aktifkan Zona Secara Instan</label>
                </div>

                <div class="flex justify-end pt-3">
                    <button type="submit" class="w-full px-5 py-3 rounded-xl bg-brand-500 hover:bg-brand-600 active:scale-95 text-white text-sm font-bold shadow-md shadow-brand-500/10 transition-all">
                        Simpan Zona Geofence Poligon
                    </button>
                </div>
            </form>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Default location: PLTU Paiton
    const defaultLat = -7.7145;
    const defaultLng = 113.5850;
    
    // Google Satellite Hybrid layer (Satelit + Footprint Gedung & Jalan)
    const googleHybrid = L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
        attribution: '&copy; Google Maps',
        maxZoom: 20
    });
    
    const osmRoads = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 19
    });
    
    // Initialize map with Google Hybrid Satellite as default view
    const map = L.map('map', {
        center: [defaultLat, defaultLng],
        zoom: 17,
        layers: [googleHybrid]
    });

    // Control layer switcher
    L.control.layers({
        '🛰️ Satelit & Gedung (Google)': googleHybrid,
        '🗺️ Peta Jalan (OSM)': osmRoads
    }, null, { position: 'topright' }).addTo(map);

    let polygon = null;
    let polygonPoints = [];
    let markers = [];

    // Get input elements
    const latInput = document.getElementById('center_lat');
    const lngInput = document.getElementById('center_lng');
    const coordinatesInput = document.getElementById('coordinates');
    const polyPointsCount = document.getElementById('poly_points_count');
    const btnReset = document.getElementById('btnResetPolygon');

    function drawPolygon() {
        if (polygon) {
            map.removeLayer(polygon);
            polygon = null;
        }
        if (polygonPoints.length >= 2) {
            polygon = L.polygon(polygonPoints, {
                color: '#2563eb',
                fillColor: '#3b82f6',
                fillOpacity: 0.35,
                weight: 3
            }).addTo(map);
        }
    }

    function updateInputs() {
        coordinatesInput.value = JSON.stringify(polygonPoints);
        polyPointsCount.textContent = `${polygonPoints.length} Titik Terpilih`;

        if (polygonPoints.length > 0) {
            const sumLat = polygonPoints.reduce((sum, p) => sum + p.lat, 0);
            const sumLng = polygonPoints.reduce((sum, p) => sum + p.lng, 0);
            latInput.value = (sumLat / polygonPoints.length).toFixed(6);
            lngInput.value = (sumLng / polygonPoints.length).toFixed(6);
        } else {
            latInput.value = '';
            lngInput.value = '';
        }
    }

    function addVertexMarker(lat, lng, index) {
        const marker = L.marker([lat, lng], { 
            draggable: true,
            title: `Titik Gedung #${index + 1}`
        }).addTo(map);

        marker.bindTooltip(`🏢 <b>Titik Gedung #${index + 1}</b><br><span style="font-family:monospace">${lat.toFixed(6)}, ${lng.toFixed(6)}</span>`, {
            permanent: false,
            direction: 'top'
        });

        markers.push(marker);

        marker.on('drag', () => {
            const pos = marker.getLatLng();
            polygonPoints[index] = { lat: pos.lat, lng: pos.lng };
            marker.setTooltipContent(`🏢 <b>Titik Gedung #${index + 1}</b><br><span style="font-family:monospace">${pos.lat.toFixed(6)}, ${pos.lng.toFixed(6)}</span>`);
            drawPolygon();
            updateInputs();
        });
    }

    // Map click handler to add vertices
    map.on('click', (e) => {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;
        const index = polygonPoints.length;

        polygonPoints.push({ lat: lat, lng: lng });
        addVertexMarker(lat, lng, index);

        drawPolygon();
        updateInputs();
    });

    // Reset button handler
    btnReset.addEventListener('click', () => {
        markers.forEach(m => map.removeLayer(m));
        markers = [];
        
        if (polygon) {
            map.removeLayer(polygon);
            polygon = null;
        }

        polygonPoints = [];
        updateInputs();
    });
});
</script>
@endpush
