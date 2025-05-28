<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class DiagnoseDbConnection extends Command
{
    protected $signature = 'diagnose:db-connection';
    protected $description = 'Diagnose database connection issues on Ubuntu/Linux environments';

    public function handle()
    {
        $this->info('Starting database connection diagnostic...');
        $this->info('Server OS: ' . PHP_OS);
        $this->info('PHP Version: ' . PHP_VERSION);

        // Check database configuration
        $this->info("\nChecking database configuration:");
        $dbConfig = config('database.connections.' . config('database.default'));
        $this->info('Database Driver: ' . $dbConfig['driver']);
        $this->info('Database Host: ' . $dbConfig['host']);
        $this->info('Database Name: ' . $dbConfig['database']);

        // Check database connection
        $this->info("\nTesting database connection:");
        try {
            DB::connection()->getPdo();
            $this->info('✅ Database connection successful');
        } catch (\Exception $e) {
            $this->error('❌ Database connection failed: ' . $e->getMessage());

            // Get error code and state if available
            if (method_exists($e, 'errorInfo')) {
                $errorInfo = $e->errorInfo ?? [];
                $this->error('SQL State: ' . ($errorInfo[0] ?? 'N/A'));
                $this->error('Error Code: ' . ($errorInfo[1] ?? 'N/A'));
                $this->error('Driver Message: ' . ($errorInfo[2] ?? 'N/A'));
            }

            // Provide guidance based on error message
            if (strpos($e->getMessage(), 'Access denied') !== false) {
                $this->warn('This appears to be a database authentication issue. Check your credentials in .env file.');
            } elseif (strpos($e->getMessage(), 'Connection refused') !== false) {
                $this->warn('Database server connection refused. Check if MySQL service is running:');
                $this->line('Run: sudo systemctl status mysql');
            }
        }

        // Ubuntu-specific checks
        if (PHP_OS !== 'WINNT') {
            $this->info("\nPerforming Linux/Ubuntu specific checks:");

            // Check MySQL service status if available
            $this->info('MySQL/MariaDB service status:');
            exec('systemctl status mysql 2>&1', $mysqlStatus, $mysqlStatusCode);
            if ($mysqlStatusCode === 0) {
                $this->info(implode("\n", array_slice($mysqlStatus, 0, 3)));
            } else {
                $this->warn('Could not check MySQL service status. Run manually: sudo systemctl status mysql');
            }

            // Check disk space
            $this->info("\nDisk space status:");
            exec('df -h /var/lib/mysql 2>&1', $diskSpace, $diskSpaceCode);
            if ($diskSpaceCode === 0) {
                $this->info(implode("\n", $diskSpace));
            } else {
                exec('df -h / 2>&1', $rootDiskSpace);
                $this->info(implode("\n", $rootDiskSpace));
            }

            // Check directory permissions
            $uploadPath = public_path('uploads/visitors');
            $this->info("\nChecking upload directory permissions:");
            $this->info("Upload path: $uploadPath");

            if (File::exists($uploadPath)) {
                $perms = substr(sprintf('%o', fileperms($uploadPath)), -4);
                $this->info("Directory permissions: $perms");
                $owner = posix_getpwuid(fileowner($uploadPath));
                $group = posix_getgrgid(filegroup($uploadPath));
                $this->info("Directory owner: " . $owner['name']);
                $this->info("Directory group: " . $group['name']);

                // Check if web server can write to this directory
                $webUser = exec('ps aux | grep -E "apache|nginx|www-data|http" | grep -v grep | head -1 | cut -d" " -f1', $webUserOutput);
                $webUser = trim($webUser);
                if (!empty($webUser)) {
                    $this->info("Web server running as user: $webUser");

                    if ($owner['name'] === $webUser) {
                        $this->info("✅ Web server user owns the upload directory");
                    } else {
                        $this->warn("❌ Web server user does not own the upload directory");
                        $this->line("Consider running: sudo chown -R $webUser:$webUser " . $uploadPath);
                    }
                }

                // Check if directory is writable
                if (is_writable($uploadPath)) {
                    $this->info("✅ Upload directory is writable");
                } else {
                    $this->error("❌ Upload directory is not writable");
                    $this->line("Consider running: sudo chmod -R 755 " . $uploadPath);
                }
            } else {
                $this->warn("Upload directory does not exist: $uploadPath");
                $this->line("Consider creating it: mkdir -p $uploadPath && chmod 755 $uploadPath");
            }

            // Check MySQL error log for clues
            $this->info("\nChecking MySQL error log (last 5 entries):");
            exec('sudo tail -n 5 /var/log/mysql/error.log 2>&1', $mysqlErrorLog, $mysqlErrorLogCode);
            if ($mysqlErrorLogCode === 0) {
                $this->info(implode("\n", $mysqlErrorLog));
            } else {
                $this->warn('Could not access MySQL error log. Check manually: sudo tail -n 20 /var/log/mysql/error.log');
            }
        }

        // Check database tables
        $this->info("\nChecking database tables:");
        try {
            $tables = DB::select('SHOW TABLES');
            $tableCount = count($tables);
            $this->info("✅ Found $tableCount tables in the database");

            // Check visitors table specifically
            $this->info("\nChecking visitors table structure:");
            $columns = DB::select('SHOW COLUMNS FROM visitors');
            if (count($columns) > 0) {
                $this->info("✅ Visitors table exists with " . count($columns) . " columns");
            } else {
                $this->warn("❌ Visitors table exists but has no columns");
            }
        } catch (\Exception $e) {
            $this->error('❌ Error checking database tables: ' . $e->getMessage());
        }

        $this->info("\nDiagnostic completed. Use this information to troubleshoot your database connection issues.");
    }
}
