@extends('caller::layout.main')
@section('caller')
    <!-- Bootstrap  CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <div class="container py-4">
        <div class="card bg-white shadow">
            <div class="card-header bg-danger text-white">
                <h3 class="card-title mb-0">Caller Configuration</h3>
                <button type="button" class="btn btn-light create-button" data-bs-toggle="modal"
                    data-bs-target="#callerConfigModal">
                    Create
                </button>


            </div>


            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>WebSocket Server TLS</th>
                            <th>WebSocket Port *</th>
                            <th>WebSocket Path *</th>
                            <th>SIP Full Name *</th>
                            <th>SIP Domain *</th>
                            <th>SIP Username *</th>
                            <th>SIP Agent Assigned To</th>

                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($settings as $index => $setting)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $setting->websocket_server_tls }}</td>
                                <td>{{ $setting->websocket_port }}</td>
                                <td>{{ $setting->websocket_path }}</td>
                                <td>{{ $setting->sip_full_name }}</td>
                                <td>{{ $setting->sip_domain }}</td>
                                <td>{{ $setting->sip_username }}</td>
                                <td>{{ $setting->users->name }}</td>
                                <td>


                                    <form action="{{ route('caller.caller.destroy', $setting->id) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Are you sure you want to delete this item?')">
                                            <i class="bi bi-trash white"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">No configuration found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    @include('caller::caller.modals.create')


    </div>
    <script>
        var callerUrl = "{{ route('caller.caller.store') }}";
    </script>
    <script src="{{ asset('caller/js/caller.js') }}"></script>
    <script src="{{ asset('caller/js/alert-dismiss.js') }}"></script>
    <link href="{{ asset('caller/css/custom.css') }}" rel="stylesheet" />
@endsection
