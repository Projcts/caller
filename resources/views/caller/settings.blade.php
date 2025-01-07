{{-- Modal --}}
<div class="modal fade" id="callerConfigModal" tabindex="-1" aria-labelledby="callerConfigModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="callerConfigModalLabel">Caller Configuration</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="websocketForm" method="POST" action="{{ route('caller.update.settings') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="websocket_server_tls" class="form-label text-danger">WebSocket Server
                                    TLS *</label>
                                <input type="text"
                                    class="form-control @error('websocket_server_tls') is-invalid @enderror"
                                    id="websocket_server_tls" name="websocket_server_tls"
                                    value="{{ old('websocket_server_tls', $caller->websocket_server_tls) }}"
                                    placeholder="Enter IP address" required>
                                @error('websocket_server_tls')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="websocket_port" class="form-label text-danger">WebSocket Port *</label>
                                <input type="number" class="form-control @error('websocket_port') is-invalid @enderror"
                                    id="websocket_port" name="websocket_port"
                                    value="{{ old('websocket_port', $caller->websocket_port) }}"
                                    placeholder="Enter port number" required>
                                @error('websocket_port')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group">
                                <label for="websocket_path" class="form-label text-danger">WebSocket Path *</label>
                                <input type="text" class="form-control @error('websocket_path') is-invalid @enderror"
                                    id="websocket_path" name="websocket_path"
                                    value="{{ old('websocket_path', $caller->websocket_path) }}"
                                    placeholder="Enter WebSocket path" required>
                                @error('websocket_path')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sip_full_name" class="form-label text-danger">SIP Full Name *</label>
                                <input type="text" class="form-control @error('sip_full_name') is-invalid @enderror"
                                    id="sip_full_name" name="sip_full_name"
                                    value="{{ old('sip_full_name', $caller->sip_full_name) }}" maxlength="255"
                                    placeholder="Enter full name" required>
                                @error('sip_full_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sip_domain" class="form-label text-danger">SIP Domain *</label>
                                <input type="text" class="form-control @error('sip_domain') is-invalid @enderror"
                                    id="sip_domain" name="sip_domain"
                                    value="{{ old('sip_domain', $caller->sip_domain) }}" placeholder="Enter SIP domain"
                                    required>
                                @error('sip_domain')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sip_username" class="form-label text-danger">SIP Username *</label>
                                <input type="text" class="form-control @error('sip_username') is-invalid @enderror"
                                    id="sip_username" name="sip_username"
                                    value="{{ old('sip_username', $caller->sip_username) }}"
                                    placeholder="Enter SIP username" required>
                                @error('sip_username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sip_password" class="form-label text-danger">SIP Password *</label>
                                <input type="password" class="form-control @error('sip_password') is-invalid @enderror"
                                    id="sip_password" name="sip_password"
                                    value="{{ old('sip_password', $caller->sip_password) }}"
                                    placeholder="Enter SIP password" required>
                                @error('sip_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger" id="syncButton">Sync Configuration</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
