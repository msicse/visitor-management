<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class SystemDiagnosticsController extends Controller
{
    /**
     * Display the system diagnostics page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.system.diagnostics');
    }

    /**
     * Run the database diagnostics command and return the output
     *
     * @return \Illuminate\Http\Response
     */
    public function runDatabaseDiagnostics()
    {
        try {
            // Capture output from Artisan command
            $output = [];
            Artisan::call('diagnose:db-connection', [], $output);

            return response()->json([
                'status' => 'success',
                'output' => $output
            ]);
        } catch (\Exception $e) {
            Log::error('Error running database diagnostics: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to run diagnostics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check file permissions for critical directories
     *
     * @return \Illuminate\Http\Response
     */
    public function checkPermissions()
    {
        $directories = [
            'storage' => storage_path(),
            'bootstrap/cache' => base_path('bootstrap/cache'),
            'public/uploads' => public_path('uploads'),
            'public/uploads/visitors' => public_path('uploads/visitors'),
        ];

        $results = [];

        foreach ($directories as $name => $path) {
            $result = [
                'name' => $name,
                'path' => $path,
                'exists' => File::exists($path),
                'writable' => false,
                'permissions' => null,
                'owner' => null,
                'group' => null
            ];

            if ($result['exists']) {
                $result['writable'] = File::isWritable($path);
                $result['permissions'] = substr(sprintf('%o', fileperms($path)), -4);

                // Only get owner/group info on Linux/Unix systems
                if (PHP_OS !== 'WINNT') {
                    $result['owner'] = posix_getpwuid(fileowner($path))['name'] ?? 'unknown';
                    $result['group'] = posix_getgrgid(filegroup($path))['name'] ?? 'unknown';
                }
            }

            $results[] = $result;
        }

        return response()->json([
            'status' => 'success',
            'results' => $results
        ]);
    }

    /**
     * Get the most recent database error logs
     *
     * @return \Illuminate\Http\Response
     */
    public function getRecentErrors()
    {
        try {
            $logs = [];
            $logFile = storage_path('logs/laravel-' . date('Y-m-d') . '.log');

            if (File::exists($logFile)) {
                $content = File::get($logFile);

                // Extract error patterns with regex
                preg_match_all('/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\].*?ERROR.*?(?=\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}|\Z)/s', $content, $matches);

                // Get the 10 most recent errors
                $logs = array_slice($matches[0], -10);
            }

            return response()->json([
                'status' => 'success',
                'logs' => $logs
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve logs: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fix common permission issues
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function fixPermissions(Request $request)
    {
        if (PHP_OS === 'WINNT') {
            return response()->json([
                'status' => 'error',
                'message' => 'This feature is only available on Linux/Unix systems'
            ], 400);
        }

        try {
            $directory = $request->input('directory');
            $path = null;

            // Only allow specific directories to be fixed
            switch ($directory) {
                case 'storage':
                    $path = storage_path();
                    break;
                case 'bootstrap_cache':
                    $path = base_path('bootstrap/cache');
                    break;
                case 'uploads':
                    $path = public_path('uploads');
                    break;
                case 'uploads_visitors':
                    $path = public_path('uploads/visitors');
                    break;
                default:
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Invalid directory specified'
                    ], 400);
            }

            // Create directory if it doesn't exist
            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
            }

            // Fix permissions
            exec("chmod -R 755 {$path} 2>&1", $output, $returnCode);

            if ($returnCode !== 0) {
                throw new \Exception("Failed to set permissions: " . implode("\n", $output));
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Permissions fixed successfully',
                'output' => $output
            ]);

        } catch (\Exception $e) {
            Log::error('Error fixing permissions: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fix permissions: ' . $e->getMessage()
            ], 500);
        }
    }
}
