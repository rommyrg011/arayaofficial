<?php
include 'function.php';
include 'template/head.php'; 
?>

<!-- Load CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
    #map { height: 550px; width: 100%; border-radius: 12px; z-index: 1; border: 2px solid #e3e6f0; }
    .search-panel { background: #fff; padding: 25px; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    .btn-direction { width: 100%; margin-top: 10px; font-weight: bold; }
    .select2-container--default .select2-selection--single { height: calc(1.5em + .75rem + 2px) !important; padding: .375rem .75rem !important; border: 1px solid #d1d3e2 !important; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 45px !important; }
</style>

<body id="page-top">
    <div id="wrapper">
        <?php include 'template/sidebar.php'; ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include 'template/topbar.php'; ?>
                <div class="container-fluid">
                    <h1 class="h3 mb-4 text-gray-800">Navigasi Lokasi Pelanggan</h1>

                    <div class="search-panel">
                        <div class="row align-items-end">
                            <div class="col-md-9">
                                <label class="small font-weight-bold text-primary">Cari Alamat Pelanggan / Cabang:</label>
                                <select class="form-control" id="lokasi-dropdown" style="width: 100%;">
                                    <option value="">-- Cari Alamat Lengkap --</option>
                                    <?php
                                    $query = mysqli_query($koneksi, "SELECT * FROM geolokasi ORDER BY alamatlengkap ASC");
                                    while($row = mysqli_fetch_assoc($query)) {
                                        // Pastikan koordinat diambil apa adanya dari database
                                        $val = $row['lat'] . "," . $row['lng'];
                                        echo "<option value='". $val ."' data-nama='". htmlspecialchars($row['alamatlengkap'], ENT_QUOTES) ."'>". $row['alamatlengkap'] ."</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="button" id="btn-reset" class="btn btn-outline-secondary btn-block">
                                    <i class="fas fa-sync-alt"></i> Reset Peta
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-map-marked-alt"></i> Preview Peta</h6>
                        </div>
                        <div class="card-body p-0">
                            <div id="map"></div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include 'template/footer.php'; ?>
        </div>
    </div>

    <?php include 'template/script.php'; ?>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Setup Peta
        var map = L.map('map').setView([-3.316694, 114.590111], 13);
        var userCoords = null;
        var targetMarker = null;
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);

        $('#lokasi-dropdown').select2({ placeholder: "Ketik alamat pelanggan..." });

        // 2. Deteksi Lokasi Saya
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                userCoords = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                L.circle([userCoords.lat, userCoords.lng], {
                    radius: 50, color: '#e74a3b', fillColor: '#e74a3b', fillOpacity: 0.5
                }).addTo(map).bindPopup("Lokasi Saya");
            });
        }

        // 3. Event Dropdown
        $('#lokasi-dropdown').on('change', function() {
            var dataValue = $(this).val();
            if (!dataValue) return;

            var coords = dataValue.split(',');
            var lat = coords[0].trim();
            var lng = coords[1].trim();
            var alamat = $(this).find(':selected').data('nama');

            if (targetMarker) { map.removeLayer(targetMarker); }

            targetMarker = L.marker([lat, lng]).addTo(map);
            
            // PERBAIKAN: Menggunakan tanda kutip tunggal di navigasiGoogleMaps agar dikirim sebagai string murni
            var popupContent = `
                <div style='text-align:center; min-width: 150px;'>
                    <i class="fas fa-store text-primary"></i> <br>
                    <strong>${alamat}</strong><hr style='margin: 5px 0;'>
                    <button onclick="navigasiGoogleMaps('${lat}', '${lng}')" class="btn btn-success btn-sm btn-direction shadow-sm">
                        <i class="fas fa-location-arrow"></i> Buka Google Maps
                    </button>
                </div>
            `;

            targetMarker.bindPopup(popupContent).openPopup();
            map.flyTo([lat, lng], 17, { duration: 1.5 });
        });

        // 4. Fungsi Navigasi (Menerima string koordinat murni)
        window.navigasiGoogleMaps = function(destLat, destLng) {
            let url;
            // Gunakan format query lat,lng untuk akurasi pinpoint maksimal
            if (userCoords) {
                url = `https://www.google.com/maps/dir/?api=1&origin=${userCoords.lat},${userCoords.lng}&destination=${destLat},${destLng}&travelmode=driving`;
            } else {
                url = `https://www.google.com/maps/search/?api=1&query=${destLat},${destLng}`;
            }
            window.open(url, '_blank');
        };

        $('#btn-reset').click(function() {
            $('#lokasi-dropdown').val('').trigger('change');
            if (targetMarker) { map.removeLayer(targetMarker); }
            map.setView([-3.316694, 114.590111], 13);
        });
        
        setTimeout(function(){ map.invalidateSize()}, 500);
    });
    </script>
</body>
</html>