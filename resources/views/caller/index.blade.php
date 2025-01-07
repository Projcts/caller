@extends('caller::layout.main')
@section('caller')
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
