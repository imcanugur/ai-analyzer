<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class IsolateDemoDatabase
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('demo.enabled', false)) {
            return $next($request);
        }

        // Zero-dependency Demo Mode: Override Redis, Cloud Storage (R2/S3) and external APIs
        config([
            'queue.default' => env('DEMO_QUEUE_CONNECTION', 'sync'),
            'cache.default' => env('DEMO_CACHE_DRIVER', 'file'),
            'session.driver' => env('DEMO_SESSION_DRIVER', 'file'),
            'filesystems.default' => 'local',
        ]);

        $demoDir = config('demo.storage_path', storage_path('demo_dbs'));
        if (! File::exists($demoDir)) {
            File::makeDirectory($demoDir, 0755, true);
        }

        $sessionKey = 'demo_db_path';
        $expiresKey = 'demo_expires_at';

        $dbPath = session($sessionKey);
        $expiresAt = session($expiresKey);

        $isExpired = $expiresAt && now()->timestamp > $expiresAt;

        if (! $dbPath || ! File::exists($dbPath) || $isExpired) {
            $sessionId = session()->getId() ?: Str::random(16);
            $fileName = 'demo_'.substr(md5($sessionId), 0, 12).'.sqlite';
            $dbPath = $demoDir.'/'.$fileName;

            if (! File::exists($dbPath)) {
                touch($dbPath);

                // Configure temporary SQLite connection for migration & seeding
                config([
                    'database.default' => 'sqlite',
                    'database.connections.sqlite.database' => $dbPath,
                    'database.connections.sqlite.busy_timeout' => 5000,
                    'database.connections.sqlite.journal_mode' => 'wal',
                    'database.connections.sqlite.synchronous' => 'normal',
                    'webpush.database_connection' => 'sqlite',
                ]);
                DB::setDefaultConnection('sqlite');
                DB::purge();
                DB::reconnect();

                // Migrate and seed default database schema and routes
                Artisan::call('migrate', ['--database' => 'sqlite', '--force' => true]);
                Artisan::call('db:seed', ['--database' => 'sqlite', '--force' => true]);

                // Create initial demo user if empty
                if (User::count() === 0) {
                    User::create([
                        'name' => 'Demo Ziyaretçisi',
                        'email' => 'demo@ai-analyzer.com',
                        'password' => bcrypt('demo1234'),
                        'email_verified_at' => now(),
                    ]);
                }
            }

            $durationMinutes = (int) config('demo.session_duration_minutes', 60);
            session([
                $sessionKey => $dbPath,
                $expiresKey => now()->addMinutes($durationMinutes)->timestamp,
            ]);
        }

        // Apply isolated SQLite connection for active request
        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => $dbPath,
            'database.connections.sqlite.busy_timeout' => 5000,
            'database.connections.sqlite.journal_mode' => 'wal',
            'database.connections.sqlite.synchronous' => 'normal',
            'webpush.database_connection' => 'sqlite',
        ]);
        DB::setDefaultConnection('sqlite');
        DB::purge();
        DB::reconnect();

        // Auto login demo user for zero-barrier interaction in demo mode (with self-healing migration check)
        if (! Auth::check()) {
            try {
                $demoUser = User::first();
                if ($demoUser) {
                    Auth::login($demoUser);
                }
            } catch (\Throwable $e) {
                Artisan::call('migrate', ['--database' => 'sqlite', '--force' => true]);
                Artisan::call('db:seed', ['--database' => 'sqlite', '--force' => true]);

                if (User::count() === 0) {
                    User::create([
                        'name' => 'Demo Ziyaretçisi',
                        'email' => 'demo@ai-analyzer.com',
                        'password' => bcrypt('demo1234'),
                        'email_verified_at' => now(),
                    ]);
                }

                $demoUser = User::first();
                if ($demoUser) {
                    Auth::login($demoUser);
                }
            }
        }

        $this->cleanExpiredDatabases($demoDir);

        return $next($request);
    }

    /**
     * Clean up expired SQLite files older than 3 hours.
     */
    protected function cleanExpiredDatabases(string $demoDir): void
    {
        try {
            $files = File::files($demoDir);
            $cutoff = now()->subHours(3)->timestamp;

            foreach ($files as $file) {
                if ($file->getExtension() === 'sqlite' && $file->getMTime() < $cutoff) {
                    File::delete($file->getPathname());
                }
            }
        } catch (\Throwable $e) {
            // Ignore cleanup exceptions
        }
    }
}
