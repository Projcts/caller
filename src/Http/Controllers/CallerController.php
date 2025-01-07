<?php


namespace Alisons\Caller\Http\Controllers;

use Illuminate\Routing\Controller;
use Alisons\Caller\Http\Models\CallerSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CallerController extends Controller
{

    public function index()
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
