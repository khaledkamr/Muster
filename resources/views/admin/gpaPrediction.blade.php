@extends('layouts.admin')

@section('title', 'LSTM Model Management')

@section('content')
<style>
    .table-container {
        /* color: #ec5690; */
        background-color: #fff;
        border-radius: 8px;
        overflow: hidden;
    }
    .table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 0;
    }
    .table thead {
        background-color: #f8f9fa;
        color: #333;
    }
    .table th {
        padding: 15px;
        text-align: left;
        font-weight: 600;
        font-size: 14px;
        border-bottom: 1px solid #e9ecef;
    }
    .table td {
        padding: 15px;
        font-size: 14px;
        color: #333;
        border-bottom: 1px solid #e9ecef;
    }
    .table tbody tr:hover {
        background-color: #f1f3f5;
    }
</style>
<div class="container">
    <h1 class="text-dark fw-bold pt-3 pb-4"><i class="fa-solid fa-chart-line"></i> GPA Prediction (LSTM)</h1>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Line Chart -->
    <div class="card bg-light text-dark border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title fw-bold">Model Overview</h5>
                <form action="{{ route('admin.aiModels.lstm.retrain') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="fa-solid fa-arrow-rotate-right"></i> Retrain Now
                    </button>
                </form>
            </div>
            <div class="d-flex justify-content-center gap-4">
                <p class="badge text-bg-success mb-0">
                    Accuracy: {{ round($accuracy * 100, 2) }}%
                </p>
                <p class="badge text-bg-success mb-0">
                    Loss: {{ round($loss, 3) }}
                </p>
                <p class="badge text-bg-success mb-0">
                    Training Epochs: {{ $epochs }}
                </p>
                <p class="badge text-bg-success mb-0">
                    Train Time: {{ round($trainTime, 2) }}s
                </p>
                <p class="badge text-bg-secondary mb-0">
                    Last trained: {{ $lastTrained ?? 'Not trained yet' }}
                </p>
            </div>
            <canvas id="lstmChart" style="max-height: 400px;"></canvas>
        </div>
    </div>

    <!-- Training History Table -->
    <div class="card bg-light text-dark border-0 shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title fw-bold pb-2">Training History</h5>
            <div class="table-container mb-2">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="text-bg-dark text-center">Model</th>
                            <th class="text-bg-dark text-center">Accuracy</th>
                            <th class="text-bg-dark text-center">Loss</th>
                            <th class="text-bg-dark text-center">Epochs</th>
                            <th class="text-bg-dark text-center">Execution time</th>
                            <th class="text-bg-dark text-center">date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($history ?? [] as $data)
                            <tr>
                                <td class="text-center">LSTM</td>
                                <td class="text-center">{{ $data->accuracy ? round($data->accuracy * 100, 2) : 0 }}%</td>
                                <td class="text-center">{{ round($data->loss ?? 0, 3) }}</td>
                                <td class="text-center">{{ $data->epochs ?? 'N/A' }}</td>
                                <td class="text-center">{{ round($data->execution_time ?? 0, 2) }}s</td>
                                <td class="text-center">{{ Carbon\Carbon::parse($data->date)->format('Y-M-d') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Upload New Data -->
    <div class="card bg-light text-dark border-0 shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title fw-bold">Upload New Data</h5>
            <form action="" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="dataFile" class="form-label">Choose File</label>
                    <input type="file" class="form-control" id="dataFile" name="dataFile" accept=".csv">
                </div>
                <button type="submit" class="btn btn-primary">Upload & Retrain</button>
            </form>
        </div>
    </div>
</div>


<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('lstmChart').getContext('2d');
        let chartLabels = [];
        for(let i = 1; i <= {{ $epochs }}; i++) {
            chartLabels.push(i);
        }
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [
                    {
                        label: 'Accuracy',
                        data: @json($accuracyData),
                        borderColor: 'rgba(40, 167, 69, 1)',
                        backgroundColor: 'rgba(40, 167, 69, 0.2)',
                        fill: true,
                        tension: 0.1
                    },
                    {
                        label: 'Loss',
                        data: @json($lossData),
                        borderColor: 'rgba(220, 53, 69, 1)',
                        backgroundColor: 'rgba(220, 53, 69, 0.2)',
                        fill: true,
                        tension: 0.1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Value',
                            color: '#212529'
                        },
                        ticks: {
                            color: '#212529',
                            stepSize: 0.1
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Epochs',
                            color: '#212529'
                        },
                        ticks: {
                            color: '#212529'
                        },
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            color: '#212529',
                            usePointStyle: true,
                            padding: 20
                        }
                    }
                }
            }
        });
    });
</script>
@endsection