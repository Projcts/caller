<?php


namespace Alisons\Caller\Http\Controllers;

use Illuminate\Routing\Controller;
use Alisons\Caller\Http\Models\CallerSetting;
use Alisons\Caller\Http\Models\CallLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redis;

class CallerController extends Controller
{

    public function index()
    {
        $userCount = User::count();
        $caller = new CallerSetting();
        $callsMade = CallLog::count();
        $users = User::pluck('name', 'id');
        $callsConnected = CallLog::where('call_started', 1)->count();
        $latestCallLogs =  CallLog::latest()->take(10)->get();
        return view('caller::caller.dashboard', compact('userCount', 'callsMade', 'callsConnected', 'latestCallLogs', 'caller', 'users'));
    }

    public function callSummary()
    {
        $calls = DB::table('call_logs')  // Change 'your_calls_table' to your actual table name
            ->selectRaw('DATE(created_at) as date, 
                        SUM(CASE WHEN call_terminated_by = "them" THEN 1 ELSE 0 END) as picked_calls,
                        SUM(CASE WHEN call_terminated_by = "us" THEN 1 ELSE 0 END) as missed_calls')
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date', 'ASC')
            ->get();

        return response()->json($calls);
    }

    public function exportLogs(Request $request)
    {

        // Get the filtered logs based on the search parameters
        $callLogs = CallLog::with('user')->search($request->all())->get();
        $filename = 'call_logs_' . now()->format('Y_m') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        $columns = [
            'id',
            'call_started',
            'user',
            'lead',
            'call_type',
            'call_time',
            'start_time',
            'duration',
            'created_at',
            'updated_at',
        ];

        $callback = function () use ($callLogs, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns); // header row

            foreach ($callLogs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->call_started,
                    $log->user->email,
                    $log->lead,
                    $log->call_type,
                    $log->call_time,
                    $log->start_time,
                    $log->duration,
                    $log->created_at,
                    $log->updated_at,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function settings()
    {
        $users = User::pluck('name', 'id');
        $caller = new CallerSetting();
        $settings = CallerSetting::with('users')->get();
        return view('caller::caller.index',  compact('settings', 'users', 'caller'));
    }

    public function show(CallerSetting $caller)
    {

        $caller->load('users');
        return view('caller::caller.settings',  compact('caller'));
    }

    public function getLogs(Request $request)
    {
        $logs = CallLog::with('user')->search($request->all())
            ->orderBy('created_at', 'desc')
            ->paginate(10)      // Paginate 10 records per page
            ->withQueryString(); // Keep search values in URL when paginating

        $calltype = CallLog::distinct()->pluck('call_type');

        $caller = new CallerSetting();
        $users = User::pluck('name', 'id');

        return view('caller::caller.calllogs', compact('caller', 'users', 'logs', 'calltype'));
    }
    public function exportCsv()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $callLogs = CallLog::with('user')->whereMonth('call_time', $currentMonth)
            ->whereYear('call_time', $currentYear)
            ->get();

        $filename = 'call_logs_' . now()->format('Y_m') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        $columns = [
            'id',
            'call_started',
            'user',
            'lead',
            'call_type',
            'call_time',
            'start_time',
            'duration',
            'created_at',
            'updated_at',
        ];

        $callback = function () use ($callLogs, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns); // header row

            foreach ($callLogs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->call_started,
                    $log->user->email,
                    $log->lead,
                    $log->call_type,
                    $log->call_time,
                    $log->start_time,
                    $log->duration,
                    $log->created_at,
                    $log->updated_at,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function generate_log(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'call_started'       => ['required', 'boolean'],
            'lead'               => ['required', 'string'],
            'call_type'          => ['required', 'string', 'max:255'],
            'call_time'          => ['nullable', 'date'],
            'start_time'         => ['nullable', 'date'],
            'duration'           => ['nullable', 'integer', 'min:0'],
            'call_terminated_by' => ['nullable', 'string', 'max:250'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Convert UTC to Pakistan Standard Time (PKT)
            $callTime = $request->call_time ? Carbon::parse($request->call_time)->setTimezone('Asia/Karachi') : null;
            $startTime = $request->start_time ? Carbon::parse($request->start_time)->setTimezone('Asia/Karachi') : null;

            $callActivity = CallLog::create([
                'call_started'       => $request->boolean('call_started'),
                'user_id'            => Auth::id(),
                'lead'               => $request->lead,
                'call_type'          => $request->call_type,
                'call_time'          => $callTime,
                'start_time'         => $startTime,
                'duration'           => $request->duration,
                'call_terminated_by' => $request->call_terminated_by,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Call activity created successfully.',
                'data'    => $callActivity
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to create call activity.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    public function getCallerSetting(Request $request)
    {
        // Fetch the CallerSetting for the authenticated user
        $model = CallerSetting::where('user_id', Auth::id())->first();

        // Handle the case where the CallerSetting is not found
        if (!$model) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Caller setting not found'], 404);
            } else {
                return redirect()->back()->with('error', 'Caller setting not found');
            }
        }

        // Return the appropriate response based on the request type
        if ($request->ajax()) {
            return response()->json($model);
        } else {
            return view('caller::caller.index', compact('model'));
        }
    }

    public function dailer()
    {
        return view('caller::caller.dailer');
    }


    public function callerPop()
    {

        return view('caller::caller.pop');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'websocket_server_tls' => 'required|ip',
            'websocket_port' => 'required|integer',
            'websocket_path' => 'required|string',
            'sip_full_name' => 'required|string|max:255',
            'sip_domain' => 'required|ip',
            'sip_username' => 'required|string',
            'sip_password' => 'required|string',
            'user_id' => 'required|integer|exists:users,id|unique:caller_settings,user_id',
        ]);

        // If validation fails, return JSON response with errors
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422); // 422 Unprocessable Entity
        }

        try {
            // Create a new record
            $settings = new CallerSetting();

            // Update the settings
            $settings->websocket_server_tls = $request->websocket_server_tls;
            $settings->websocket_port = $request->websocket_port;
            $settings->websocket_path = $request->websocket_path;
            $settings->sip_full_name = $request->sip_full_name;
            $settings->sip_domain = $request->sip_domain;
            $settings->sip_username = $request->sip_username;
            $settings->sip_password = $request->sip_password;
            $settings->user_id = $request->user_id;

            // Save the settings
            $settings->save();

            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Settings updated successfully',
            ]);
        } catch (\Exception $e) {

            // Log the error
            \Log::error('Error updating caller settings: ' . $e->getMessage());

            // Return error response
            return response()->json([
                'success' => false,
                'message' => 'Failed to update settings. Please try again.',
            ], 500); // 500 Internal Server Error
        }
    }

    public function update_settings(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'websocket_server_tls' => 'required|ip',
            'websocket_port' => 'required|integer',
            'websocket_path' => 'required|string',
            'sip_full_name' => 'required|string|max:255',
            'sip_domain' => 'required|ip',
            'sip_username' => 'required|string',
            'sip_password' => 'required|string',
        ]);
        // If validation fails, redirect back with errors
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }
        try {
            // Get the first record or create if doesn't exist
            $settings = CallerSetting::firstOrNew();

            // Update the settings
            $settings->websocket_server_tls = $request->websocket_server_tls;
            $settings->websocket_port = $request->websocket_port;
            $settings->websocket_path = $request->websocket_path;
            $settings->sip_full_name = $request->sip_full_name;
            $settings->sip_domain = $request->sip_domain;
            $settings->sip_username = $request->sip_username;
            $settings->sip_password = $request->sip_password;

            // Save the settings
            $settings->save();

            // Redirect with success message
            return redirect()
                ->back()
                ->with('success', 'Settings updated successfully');
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error updating caller settings: ' . $e->getMessage());

            // Redirect with error message
            return redirect()
                ->back()
                ->with('error', 'Failed to update settings. Please try again.')
                ->withInput();
        }
    }

    public function destroy(CallerSetting $caller)
    {
        // Check if the caller setting exists
        if (!$caller) {
            return redirect()
                ->back()
                ->with('error', 'Data not found')
                ->withInput();
        }

        // Delete the caller setting
        $caller->delete();

        return redirect()
            ->back()
            ->with('success', 'Settings Deleted Successfully');
    }
}
