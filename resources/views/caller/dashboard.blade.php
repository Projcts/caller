@extends('caller::layout.main')

@section('caller')
    <!-- Page Content -->
    <div id="content">


        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <canvas id="callStatusChart" width="400" height="200"></canvas>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        Quick Stats
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3">
                                <i class="fas fa-users text-primary"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Total Users</h6>
                                <h4 class="mb-0">{{ $userCount }}</h4>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-success bg-opacity-10 p-3 rounded-3 me-3">
                                <i class="fas fa-chart-line text-success"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Total Call Made</h6>
                                <h4 class="mb-0">{{ $callsMade }}</h4>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="bg-warning bg-opacity-10 p-3 rounded-3 me-3">
                                <i class="fas fa-tasks text-warning"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Call Connected</h6>
                                <h4 class="mb-0">{{ $callsConnected }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                        <h5 class="mb-0 fw-bold">Recent Activities</h5>
                        <span class="badge bg-primary rounded-pill">{{ count($latestCallLogs) }}</span>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            @forelse ($latestCallLogs as $log)
                                <li class="list-group-item border-bottom hover-bg-light transition-all">
                                    <div class="d-flex align-items-start gap-3">
                                        <div
                                            class="call-icon {{ $log->call_started ? 'bg-success-subtle' : 'bg-warning-subtle' }} p-3 rounded-circle">
                                            <i
                                                class="fas {{ $log->call_started ? 'fa-phone-alt' : 'fa-phone-slash' }} 
                                   {{ $log->call_started ? 'text-success' : 'text-warning' }} fa-lg"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <h6 class="fw-bold mb-0">
                                                    {{ $log->call_started ? 'Call Connected' : 'Call Missed' }}</h6>
                                                <span
                                                    class="text-muted small">{{ $log->created_at->diffForHumans() }}</span>
                                            </div>
                                            <div class="call-details">
                                                <div class="row g-2 mt-1">
                                                    <div class="col-md-4">
                                                        <div class="d-flex align-items-center">
                                                            <span class="text-muted me-2"><i
                                                                    class="fas fa-exchange-alt"></i></span>
                                                            <span>{{ ucfirst($log->call_type ?? 'N/A') }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="d-flex align-items-center">
                                                            <span class="text-muted me-2"><i
                                                                    class="fas fa-clock"></i></span>
                                                            <span>{{ $log->duration ? $log->duration . ' sec' : 'N/A' }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="d-flex align-items-center">
                                                            <span class="text-muted me-2"><i
                                                                    class="fas fa-user-times"></i></span>
                                                            <span>{{ ucfirst($log->call_terminated_by ?? 'N/A') }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item py-4">
                                    <div class="text-center">
                                        <i class="fas fa-phone-slash text-muted fa-2x mb-3"></i>
                                        <p class="mb-0">No recent call logs available</p>
                                    </div>
                                </li>
                            @endforelse
                        </ul>
                    </div>
                    @if (count($latestCallLogs) > 0)
                        <div class="card-footer bg-white text-center py-2">
                            <a href="{{ route('caller.caller.getlogs') }}" class="text-decoration-none">View all
                                call
                                logs <i class="fas fa-chevron-right small"></i></a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">

                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-6 link" data-bs-toggle="modal" data-bs-target="#callerConfigModal">
                                <div class="p-3 border rounded-3 text-center h-100">
                                    <i class="fas fa-plus-circle fs-3 text-primary mb-2"></i>
                                    <p class="mb-0">Add User Settings</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 border rounded-3 text-center h-100">
                                    <i class="fas fa-file-export fs-3 text-success mb-2"></i>
                                    <a href="{{ route('caller.caller.exportcsv') }}" class="mb-0">Export Monthly Call
                                        Logs
                                        </p>
                                </div>



                            </div>
                            <div class="col-6">
                                <div class="p-3 border rounded-3 text-center h-100">
                                    <i class="fas fa-cog fs-3 text-warning mb-2"></i>
                                    <a href="{{ route('caller.caller.getsettings') }}" class="mb-0">Settings</a>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        fetch("{{ route('caller.caller.summary') }}", {
                method: "GET",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    Accept: "application/json",
                },
            })
            .then(response => response.json())
            .then(data => {
                const labels = data.map(item => item.date);
                const pickedCalls = data.map(item => item.picked_calls);
                const missedCalls = data.map(item => item.missed_calls);

                const ctx = document.getElementById('callStatusChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                                label: 'Picked Calls',
                                data: pickedCalls,
                                fill: false,
                                borderColor: 'green',
                                tension: 0.1
                            },
                            {
                                label: 'Missed Calls',
                                data: missedCalls,
                                fill: false,
                                borderColor: 'red',
                                tension: 0.1
                            }
                        ]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            });
    </script>
@endsection
