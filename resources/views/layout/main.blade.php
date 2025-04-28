@extends('layouts.app')


<!-- Bootstrap CDN -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
</script>
<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="{{ asset('caller/css/custom.css') }}" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>

</style>

@section('caller-wrapper')
    <div class="container py-4">
        <div id="liveAlertPlaceholder" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>
        <div class="wrapper">
            <!-- Sidebar -->
            <nav id="sidebar">
                <div class="sidebar-header">
                    <div class="logo-container">
                        <div class="logo-icon">CP</div>
                        <div class="logo-text">Caller Plugin</div>
                    </div>
                    <button class="toggle-btn" id="sidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>

                <ul class="sidebar-menu nav flex-column">
                    <li class="nav-item">
                        <a href="{{ route('caller.caller.index') }}"
                            class="nav-link {{ request()->routeIs('caller.caller.index') ? 'active' : '' }}">
                            <i class="fas fa-home"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('caller.caller.getlogs') }}"
                            class="nav-link {{ request()->routeIs('caller.caller.getlogs') ? 'active' : '' }}">
                            <i class="fas fa-chart-bar"></i>
                            <span>Call Log Report</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('caller.caller.getsettings') }}"
                            class="nav-link {{ request()->routeIs('caller.caller.getsettings') ? 'active' : '' }}">
                            <i class="fas fa-cog"></i>
                            <span>Settings</span>
                        </a>
                    </li>
                </ul>
            </nav>

            @yield('caller')

            @include('caller::caller.modals.create')
        </div>
    </div>
    <script src="{{ asset('caller/js/alert-dismiss.js') }}"></script>

    <script>
        var callerUrl = "{{ route('caller.caller.store') }}";
    </script>
    <script src="{{ asset('caller/js/caller.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content');
            const sidebarToggle = document.getElementById('sidebarToggle');

            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
                content.classList.toggle('collapsed');
            });
        });
    </script>
@endsection
