<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ArduinoPythonController extends Controller
{
    private $pythonBaseUrl = 'http://127.0.0.1:5000';

    /**
     * List available ports
     */
    public function listPorts()
    {
        try {
            $response = Http::timeout(5)->get($this->pythonBaseUrl . '/ports');
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Failed to get ports: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to communicate with Python service',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Connect to Arduino
     */
    public function connect(Request $request)
    {
        try {
            $port = $request->input('port', '6');
            $response = Http::timeout(5)->get($this->pythonBaseUrl . '/connect/' . $port);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Failed to connect: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to connect to Arduino',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Read data from Arduino with long timeout
     */
    public function read()
    {
        try {
            // Set a 35-second timeout (slightly longer than Python's 30-second timeout)
            $response = Http::timeout(35)
                ->get($this->pythonBaseUrl . '/read');

            $result = $response->json();

            // Add some additional information for debugging
            $result['php_info'] = [
                'request_time' => date('Y-m-d H:i:s'),
                'php_timeout' => 35,
                'python_timeout' => 30
            ];

            return response()->json($result);

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Connection timeout: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Request timed out waiting for Arduino response',
                'error' => 'Connection timeout after 35 seconds'
            ], 504); // Gateway Timeout
        } catch (\Exception $e) {
            Log::error('Failed to read data: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to read from Arduino',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Disconnect from Arduino
     */
    public function disconnect()
    {
        try {
            $response = Http::timeout(5)->get($this->pythonBaseUrl . '/disconnect');
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Failed to disconnect: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to disconnect from Arduino',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
