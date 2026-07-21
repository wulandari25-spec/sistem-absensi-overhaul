@extends('layouts.app')

@section('title', 'Edit Geofence')
@section('header', 'Edit Zona Geofence')

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
            <h2 class="text-xl font-bold text-slate-800 dark:text-white">Ubah Data Zona</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Ubah konfigurasi pembatasan lokasi GPS untuk zona "{{ $geofence->zone_name }}"</p>
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
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Sesuaikan Lokasi Pada Peta</span>
                    <span class="text-[10px] text-brand-500 font-semibold bg-brand-500/10 px-2 py-0.5 rounded-full">Klik peta untuk memindahkan pin</span>
                </div>
                <div id="map" class="h-80 sm:h-[400px] w-full rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800 shadow-inner"></div>
            </div>
        </div>

        {{-- Form Column (Right) --}}
        <div class="lg:col-span-2">
            <form action="{{ route('admin.geofences.update', $geofence) }}" method="POST" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Nama Zona</label>
                    <input type="text" name="zone_name" value="{{ old('zone_name', $geofence->zone_name) }}" placeholder="Contoh: Unit 1 PLTU Paiton" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-800 dark:text-slate-200 outline-none focus:border-brand-500" required>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Kode Zona</label>
                    <input type="text" name="zone_code" value="{{ old('zone_code', $geofence->zone_code) }}" placeholder="Contoh: ZONE-U1" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-800 dark:text-slate-200 outline-none focus:border-brand-500" required>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Latitude</label>
                        <input type="number" step="any" name="center_lat" id="center_lat" value="{{ old('center_lat', $geofence->center_lat) }}" placeholder="Contoh: -7.714500" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-800 dark:text-slate-200 outline-none focus:border-brand-500 font-mono" required readonly>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Longitude</label>
                        <input type="number" step="any" name="center_lng" id="center_lng" value="{{ old('center_lng', $geofence->center_lng) }}" placeholder="Contoh: 113.585000" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-800 dark:text-slate-200 outline-none focus:border-brand-500 font-mono" required readonly>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Radius Jangkauan (Meter)</label>
                    <input type="number" name="radius_meters" id="radius_meters" value="{{ old('radius_meters', $geofence->radius_meters) }}" min="5" max="10000" placeholder="Misal: 200" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-800 dark:text-slate-200 outline-none focus:border-brand-500" required>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Deskripsi Area</label>
                    <textarea name="description" placeholder="Keterangan area zona..." rows="3" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-800 dark:text-slate-200 outline-none focus:border-brand-500">{{ old('description', $geofence->description) }}</textarea>
                </div>

                <div class="flex items-center gap-3 py-2 border-t border-slate-100 dark:border-slate-800/80">
                    <input type="checkbox" name="is_active" id="is_active" class="w-4 h-4 rounded text-brand-600 focus:ring-brand-500 border-slate-300" {{ old('is_active', $geofence->is_active) ? 'checked' : '' }}>
                    <label for="is_active" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Aktifkan Zona</label>
                </div>

                <div class="flex justify-end pt-3">
                    <button type="submit" class="w-full px-5 py-3 rounded-xl bg-brand-500 hover:bg-brand-600 active:scale-95 text-white text-sm font-bold shadow-md shadow-brand-500/10 transition-all">
                        Simpan Perubahan
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
    // Initial coordinates from the existing geofence zone
    const initLat = {{ $geofence->center_lat }};
    const initLng = {{ $geofence->center_lng }};
    const initRadius = {{ $geofence->radius_meters }};
    
    // Initialize map centered at current zone location
    const map = L.map('map').setView([initLat, initLng], 15);
    
    // Add tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Initialize marker
    let marker = L.marker([initLat, initLng], { draggable: true }).addTo(map);
    
    // Initialize circle
    let circle = L.circle([initLat, initLng], {
        color: '#3b82f6',
        fillColor: '#3b82f6',
        fillOpacity: 0.25,
        radius: initRadius
    }).addTo(map);

    // Get input elements
    const latInput = document.getElementById('center_lat');
    const lngInput = document.getElementById('center_lng');
    const radiusInput = document.getElementById('radius_meters');

    function updateGeofence(lat, lng, radius) {
        // Update inputs
        latInput.value = lat.toFixed(6);
        lngInput.value = lng.toFixed(6);

        // Update Marker position
        marker.setLatLng([lat, lng]);

        // Update Circle position and radius
        circle.setLatLng([lat, lng]);
        circle.setRadius(radius);

        // Fit map bounds to show circle
        map.fitBounds(circle.getBounds());
    }

    // Set map focus boundaries initially
    map.fitBounds(circle.getBounds());

    // Draggable marker handler
    marker.on('dragend', function(e) {
        const position = marker.getLatLng();
        updateGeofence(position.lat, position.lng, parseInt(radiusInput.value) || 200);
    });

    // Map click handler to relocate geofence
    map.on('click', (e) => {
        const radius = parseInt(radiusInput.value) || 200;
        updateGeofence(e.latlng.lat, e.latlng.lng, radius);
    });

    // Input radius change listener
    radiusInput.addEventListener('input', () => {
        const radius = parseInt(radiusInput.value) || 200;
        const lat = parseFloat(latInput.value) || initLat;
        const lng = parseFloat(lngInput.value) || initLng;
        
        circle.setRadius(radius);
        map.fitBounds(circle.getBounds());
    });
});
</script>
@endpush
