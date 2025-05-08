@extends('caller::layout.main')

@section('caller')
    <div id="content">
        <div class="row">

            @include('caller::caller.search.search')

            <div class="card bg-white shadow">
                <div class="card-header bg-danger text-white"
                    style="
    display: flex;
    justify-content: space-between;
    align-items: center;
">
                    <h3 class="card-title mb-0">Call Logs</h3>
                    <a href="{{ route('caller.call-logs.export', request()->query()) }}" class="btn btn-success">
                        Export Logs to CSV
                    </a>
                </div>

                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Call Started</th>
                                <th>User</th>
                                <th>Lead</th>
                                <th>Call Type</th>
                                <th>Call Time</th>
                                <th>Start Time</th>
                                <th>Duration (seconds)</th>
                                <th>Call Recording </th>
                                <th>Created At</th>
                                <th>Updated At</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($logs as $index => $log)
                                <tr>
                                    <td>{{ ($logs->currentPage() - 1) * $logs->perPage() + $index + 1 }}</td>
                                    <td>{{ $log->call_started ? 'Yes' : 'No' }}</td>
                                    <td>{{ $log->user->email }}</td>
                                    <td>{{ $log->lead }}</td>
                                    <td>{{ $log->call_type }}</td>
                                    <td>{{ $log->call_time }}</td>
                                    <td>{{ $log->start_time }}</td>
                                    <td>{{ $log->duration }}</td>
                                    <td>
                                        @if ($log->call_recording_url)
                                            <audio controls>
                                                <source
                                                    src="http://campaign.alkhidmat.com/recordings/{{ $log->call_recording_url }}"
                                                    type="audio/wav">
                                                Your browser does not support the audio element.
                                            </audio>
                                        @else
                                            No recording available
                                        @endif
                                    </td>
                                    <td>{{ $log->created_at }}</td>
                                    <td>{{ $log->updated_at }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center">No call logs found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <!-- Pagination Links -->
                    <div class="d-flex justify-content-center">
                        {{ $logs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
