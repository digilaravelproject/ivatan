<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class ServerHealthController extends Controller
{
    public function index()
    {
        return view('admin.server-health.index');
    }

    public function checkReverb()
    {
        $internalHost = config('reverb.servers.reverb.host', '127.0.0.1');
        $internalPort = config('reverb.servers.reverb.port', 8080);

        $externalHost = env('REVERB_HOST', config('reverb.apps.apps.0.options.host', 'ivatan.in'));
        $externalPort = env('REVERB_PORT', config('reverb.apps.apps.0.options.port', 443));
        $scheme = env('REVERB_SCHEME', config('reverb.apps.apps.0.options.scheme', 'https'));

        $appKey = config('reverb.apps.apps.0.key', config('broadcasting.connections.reverb.key'));

        $connected = false;
        $error = null;

        try {
            $socket = @fsockopen($internalHost, $internalPort, $errno, $errstr, 3);
            if ($socket) {
                $connected = true;
                fclose($socket);
            } else {
                $error = $errstr;
            }
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        $processRunning = false;
        if (PHP_OS_FAMILY === 'Windows') {
            $output = [];
            exec('tasklist /FI "IMAGENAME eq php.exe" 2>NUL', $output, $exitCode);
            $processRunning = count($output) > 1;
        } else {
            $output = [];
            exec('ps aux | grep "reverb:start" | grep -v grep 2>/dev/null', $output, $exitCode);
            $processRunning = count($output) > 0;
        }

        return response()->json([
            'connected' => $connected,
            'process_running' => $processRunning,
            'internal_host' => $internalHost,
            'internal_port' => $internalPort,
            'external_host' => $externalHost,
            'external_port' => $externalPort,
            'scheme' => $scheme,
            'app_key' => $appKey ? substr($appKey, 0, 8) . '...' : null,
            'error' => $error,
            'checked_at' => now()->toISOString(),
        ]);
    }

    public function checkQueue()
    {
        $pendingJobs = 0;
        $failedJobs = 0;
        $queueName = config('queue.connections.database.queue', 'default');

        try {
            $pendingJobs = DB::table('jobs')->where('queue', $queueName)->count();
        } catch (\Exception $e) {
            // jobs table might not exist
        }

        try {
            $failedJobs = DB::table('failed_jobs')->count();
        } catch (\Exception $e) {
            //
        }

        $workerRunning = false;
        if (PHP_OS_FAMILY === 'Windows') {
            $output = [];
            exec('tasklist /FI "IMAGENAME eq php.exe" 2>NUL', $output, $exitCode);
            $workerRunning = count($output) > 1;
        } else {
            $output = [];
            exec('ps aux | grep "queue:work" | grep -v grep 2>/dev/null', $output, $exitCode);
            $workerRunning = count($output) > 0;
        }

        return response()->json([
            'worker_running' => $workerRunning,
            'pending_jobs' => $pendingJobs,
            'failed_jobs' => $failedJobs,
            'queue_driver' => config('queue.default'),
            'queue_name' => $queueName,
            'checked_at' => now()->toISOString(),
        ]);
    }

    public function checkSystem()
    {
        $diskTotal = disk_total_space(base_path());
        $diskFree = disk_free_space(base_path());
        $diskUsed = $diskTotal - $diskFree;
        $diskPercent = $diskTotal > 0 ? round(($diskUsed / $diskTotal) * 100, 1) : 0;

        $memoryUsed = null;
        $memoryTotal = null;
        if (PHP_OS_FAMILY === 'Windows') {
            $output = [];
            exec('wmic OS get TotalVisibleMemorySize,FreePhysicalMemory /Value 2>NUL', $output, $exitCode);
            if ($exitCode === 0) {
                foreach ($output as $line) {
                    if (str_starts_with($line, 'TotalVisibleMemorySize=')) {
                        $memoryTotal = (int) trim(substr($line, strpos($line, '=') + 1)) * 1024;
                    }
                    if (str_starts_with($line, 'FreePhysicalMemory=')) {
                        $memoryFree = (int) trim(substr($line, strpos($line, '=') + 1)) * 1024;
                        $memoryUsed = $memoryTotal - $memoryFree;
                    }
                }
            }
        }

        return response()->json([
            'php_version' => phpversion(),
            'laravel_version' => app()->version(),
            'os' => PHP_OS_FAMILY . ' ' . php_uname('r'),
            'environment' => app()->environment(),
            'debug_mode' => config('app.debug'),
            'disk_total' => $this->formatBytes($diskTotal),
            'disk_free' => $this->formatBytes($diskFree),
            'disk_used' => $this->formatBytes($diskUsed),
            'disk_percent' => $diskPercent,
            'memory_used' => $memoryUsed ? $this->formatBytes($memoryUsed) : null,
            'memory_total' => $memoryTotal ? $this->formatBytes($memoryTotal) : null,
            'timezone' => config('app.timezone'),
            'checked_at' => now()->toISOString(),
        ]);
    }

    private function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        return round($bytes / pow(1024, $pow), $precision) . ' ' . $units[$pow];
    }
}
