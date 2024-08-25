@extends('templates.admin')
@section('title', 'Dashboard')
@section('header')
<div class="container-xl">
  <div class="row g-2 align-items-center mb-3">
    <div class="col">
      <h2 class="page-title">
        Dashboard
      </h2>
    </div>
    <div class="col-auto ms-auto">
      <form action="{{ route('admin.dashboard') }}" method="GET" id="filterFormPulang" class="ms-auto">
        <div class="d-flex gap-1">
          <select class="form-select" name="lokasi" onchange="document.getElementById('filterFormPulang').submit();">
            <option disabled selected value="">Lokasi</option>
            <option value="">Semua</option>
            @foreach($lokasis as $lokasi)
                <option value="{{ $lokasi->id }}" {{ request('lokasi') == $lokasi->id ? 'selected' : '' }}>
                    {{ $lokasi->nama }}
                </option>
            @endforeach
          </select>
          <select class="form-select" name="filter" onchange="document.getElementById('filterFormPulang').submit();">
              <option disabled selected value="">Filter</option>
              <option value="day" {{ request('filter') == 'day' ? 'selected' : '' }}>Hari</option>
              <option value="month" {{ request('filter') == 'month' ? 'selected' : '' }}>Bulan</option>
              <option value="year" {{ request('filter') == 'year' ? 'selected' : '' }}>Tahun</option>
          </select>
        </div>
      </form>
    </div>
  </div>
  <div class="row row-deck row-cards">
    <div class="col-md-6">
      <div class="card">
        <div class="card-header">
          <h2 class="card-title">Masuk</h2>
          <h2 class="card-title ms-auto">Jumlah : {{ $totalMasuk }}</h2>
        </div>
        <div class="card-body">
          <canvas id="masukChart"></canvas>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card">
        <div class="card-header">
          <h2 class="card-title">Pulang</h2>
          <h2 class="card-title ms-auto">Jumlah : {{ $totalPulang }}</h2>
        </div>
        <div class="card-body">
          <canvas id="pulangChart"></canvas>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
      var ctxMasuk = document.getElementById('masukChart').getContext('2d');
      var ctxPulang = document.getElementById('pulangChart').getContext('2d');

      var statusesMasuk = @json($statusesMasuk);
      var labelsMasuk = Object.keys(statusesMasuk);
      var dataMasuk = Object.values(statusesMasuk);

      var statusesPulang = @json($statusesPulang);
      var labelsPulang = Object.keys(statusesPulang);
      var dataPulang = Object.values(statusesPulang);

      var masukChart = new Chart(ctxMasuk, {
          type: 'bar',
          data: {
              labels: labelsMasuk,
              datasets: [{
                  label: 'Jumlah Masuk',
                  data: dataMasuk,
                  backgroundColor: 'rgba(54, 162, 235, 0.2)',
                  borderColor: 'rgba(54, 162, 235, 1)',
                  borderWidth: 1
              }]
          },
          options: {
              scales: {
                  y: {
                      beginAtZero: true,
                      ticks: {
                          stepSize: 1,
                          callback: function(value) {
                              return Number.isInteger(value) ? value : '';
                          }
                      },
                      title: {
                          display: true,
                          text: 'Jumlah'
                      }
                  },
                  x: {
                      title: {
                          display: true,
                          text: 'Status'
                      }
                  }
              }
          }
      });

      var pulangChart = new Chart(ctxPulang, {
          type: 'bar',
          data: {
              labels: labelsPulang,
              datasets: [{
                  label: 'Jumlah Pulang',
                  data: dataPulang,
                  backgroundColor: 'rgba(255, 99, 132, 0.2)',
                  borderColor: 'rgba(255, 99, 132, 1)',
                  borderWidth: 1
              }]
          },
          options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        callback: function(value) {
                            return Number.isInteger(value) ? value : '';
                        }
                    },
                    title: {
                        display: true,
                        text: 'Jumlah'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Status'
                    }
                }
            }
        }
      });
  });
</script>
@endpush