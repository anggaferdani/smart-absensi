@extends('templates.user')
@section('title', 'Index')
@section('content')
<div class="row">
  @if(Session::get('success'))
  <div>
    <div class="alert alert-important alert-success" role="alert">
      @if(session('tokenMessage') && session('absenMessage'))
      <div class="fs-3 fw-bold mb-3">Tanggal {{ session('absenMessage')->tanggal }}</div>
        <div>Token : {{ session('tokenMessage')->token }}</div>
        <div>Lokasi : {{ session('tokenMessage')->lokasi->nama }}</div>
        <div>Status : @if(session('tokenMessage')->status == 1) Masuk @elseif(session('tokenMessage')->status == 2) Pulang @endif</div>
        <div>Kode : {{ session('absenMessage')->kode }}</div>
        <div>Nama : {{ session('absenMessage')->user->name }}</div>
        <div>Email : {{ session('absenMessage')->user->email }}</div>
        <div>Status : @if(session('absenMessage')->status == 1) Lebih Awal @elseif(session('absenMessage')->status == 2) Tepat Waktu @elseif(session('absenMessage')->status == 3) Terlambat @endif</div>
      @endif
    </div>
  </div>
  @endif
  @if(Session::get('error'))
    <div>
      <div class="alert alert-important alert-danger" role="alert">
        {{ Session::get('error') }}
      </div>
    </div>
  @endif
  <div>
    <div id="map" class="mb-3" style="height: 300px;"></div>
  </div>
  <form action="{{ route('user.absen') }}" method="POST">
      @csrf
      <button type="button" class="btn btn-primary w-100 mb-3" id="absen">Absen</button>
      <div id="form" style="display: none;">
          <div class="mb-3">
              <label class="form-label">Token</label>
              <input readonly type="text" class="form-control" name="token" placeholder="Token" id="token">
          </div>
          <div class="mb-3">
              <label class="form-label required">Confirm Token</label>
              <input type="number" class="form-control" name="" placeholder="Token" id="confirmToken">
          </div>
          <div class="mb-3">
            <label class="form-label required">Status</label>
            <div class="form-selectgroup form-selectgroup-boxes d-flex flex-column">
              <label class="form-selectgroup-item flex-fill">
                <input type="radio" name="status" value="1" class="form-selectgroup-input">
                <div class="form-selectgroup-label d-flex align-items-center p-3">
                  <div class="me-3">
                    <span class="form-selectgroup-check"></span>
                  </div>
                  <div>
                    <div>Masuk</div>
                  </div>
                </div>
              </label>
              <label class="form-selectgroup-item flex-fill">
                <input type="radio" name="status" value="2" class="form-selectgroup-input">
                <div class="form-selectgroup-label d-flex align-items-center p-3">
                  <div class="me-3">
                    <span class="form-selectgroup-check"></span>
                  </div>
                  <div>
                    <div>Pulang</div>
                  </div>
                </div>
              </label>
            </div>
          </div>
          <input type="hidden" class="form-control" name="lat" placeholder="" id="lat">
          <input type="hidden" class="form-control" name="long" placeholder="" id="long">
          <input type="hidden" class="form-control" name="lokasi_id" placeholder="" id="lokasi">
          <input type="hidden" class="form-control" name="jam_masuk" placeholder="" id="jam_masuk">
          <input type="hidden" class="form-control" name="jam_pulang" placeholder="" id="jam_pulang">
          <button type="submit" class="btn btn-primary w-100" id="submitButton" disabled>Submit</button>
      </div>
  </form>
</div>
@endsection
@push('scripts')
<script>
  var map = L.map('map').setView([0, 0], 2);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {}).addTo(map);

  var locations = @json($lokasis);
  var userMarker = null;
  var generatedToken = '';

  if (Array.isArray(locations)) {
      var bounds = [];

      locations.forEach(function(location) {
          var lat = parseFloat(location.lat);
          var lng = parseFloat(location.long);
          var radius = parseFloat(location.radius);
          
          if (!isNaN(lat) && !isNaN(lng) && !isNaN(radius)) {
              var latLng = [lat, lng];
              
              L.marker(latLng)
                  .addTo(map)
                  .bindPopup(location.nama);

              L.circle(latLng, {
                  color: 'red',
                  fillColor: '#f03',
                  fillOpacity: 0.3,
                  radius: radius,
              }).addTo(map);

              bounds.push(latLng);
          } else {
              console.warn('Invalid location data:', location);
          }
      });

      if (bounds.length > 0) {
          var latLngBounds = L.latLngBounds(bounds);
          map.fitBounds(latLngBounds);
      }
  } else {
      console.error('Locations data is not an array or is empty:', locations);
  }

  function calculateDistance(lat1, lon1, lat2, lon2) {
      var R = 6371e3;
      var φ1 = lat1 * Math.PI / 180;
      var φ2 = lat2 * Math.PI / 180;
      var Δφ = (lat2 - lat1) * Math.PI / 180;
      var Δλ = (lon2 - lon1) * Math.PI / 180;

      var a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
              Math.cos(φ1) * Math.cos(φ2) *
              Math.sin(Δλ/2) * Math.sin(Δλ/2);
      var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

      return R * c;
  }

  function generateUniqueTokens(count) {
    var tokens = new Set();
    while (tokens.size < count) {
        var token = Math.floor(Math.random() * 90000) + 10000;
        tokens.add(token);
    }
    return Array.from(tokens);
  }

  function getUserLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                var userLat = position.coords.latitude;
                var userLng = position.coords.longitude;

                if (userMarker) {
                    map.removeLayer(userMarker);
                }

                userMarker = L.marker([userLat, userLng]).addTo(map)
                    .bindPopup("You are here!").openPopup();

                map.setView([userLat, userLng], 15);

                var inRadius = locations.some(function(location) {
                    var officeLat = parseFloat(location.lat);
                    var officeLng = parseFloat(location.long);
                    var radius = parseFloat(location.radius);
                    var officeId = location.id;
                    var jamMasuk = location.jam_masuk;
                    var jamPulang = location.jam_pulang;

                    var distance = calculateDistance(userLat, userLng, officeLat, officeLng);
                    if (distance <= radius) {
                        document.getElementById('lat').value = userLat;
                        document.getElementById('long').value = userLng;
                        document.getElementById('lokasi').value = officeId;
                        document.getElementById('jam_masuk').value = jamMasuk;
                        document.getElementById('jam_pulang').value = jamPulang;
                        return true;
                    }
                    return false;
                });

                if (inRadius) {
                    document.getElementById('form').style.display = 'block';
                    document.getElementById('absen').style.display = 'none';
                    
                    var tokens = generateUniqueTokens(5);
                    generatedToken = tokens[0];
                    document.getElementById('token').value = generatedToken;
                } else {
                    document.getElementById('form').style.display = 'none';
                    document.getElementById('absen').style.display = 'block';
                    document.getElementById('lokasi').value = '';
                }
            }, function(error) {
                console.error("Geolocation error: " + error.message);
            });
        } else {
            console.error("Geolocation is not supported by this browser.");
        }
    }

  function validateCofirmToken() {
      var tokenValue = document.getElementById('token').value.trim();
      var confirmToken = document.getElementById('confirmToken').value.trim();
      var submitButton = document.getElementById('submitButton');

      $.ajax({
          url: '/token/check',
          type: 'POST',
          data: {
              _token: '{{ csrf_token() }}',
              token: tokenValue
          },
          success: function(response) {
              if (response.exists) {
                  document.getElementById('token').value = response.newToken;
                  validateCofirmToken();
              } else {
                  if (confirmToken === tokenValue) {
                      submitButton.disabled = false;
                  } else {
                      submitButton.disabled = true;
                  }
              }
          }
      });
  }

  document.getElementById('absen').addEventListener('click', function() {
      getUserLocation();
  });

  document.getElementById('confirmToken').addEventListener('input', function() {
    validateCofirmToken();
  });
</script>
@endpush