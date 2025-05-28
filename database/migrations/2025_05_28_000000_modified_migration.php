<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Schema\Blueprint;

class ModifyTablesForMariadbCompatibility extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if the database engine is MariaDB
        $databaseEngine = DB::select('SELECT VERSION() as version')[0]->version;
        $isMariaDB = stripos($databaseEngine, 'MariaDB') !== false;

        // Apply fixes regardless of database type
        Log::info($isMariaDB ? 'Running MariaDB compatibility fixes' : 'Running general database compatibility fixes');

        // List of tables that need to be fixed
        $tables = ['departments', 'employees', 'visitors', 'visitor_guests'];

        // Step 1: Ensure all tables are using InnoDB engine and proper character set
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                // Get the current engine
                $tableInfo = DB::select("SHOW TABLE STATUS WHERE Name = '$table'")[0];
                $engine = $tableInfo->Engine;

                if ($engine !== 'InnoDB') {
                    Log::info("Converting table '$table' from $engine to InnoDB engine");
                    DB::statement("ALTER TABLE `$table` ENGINE = InnoDB");
                }

                // Set character set and collation for MariaDB
                if ($isMariaDB) {
                    Log::info("Setting character set and collation for table '$table'");
                    DB::statement("ALTER TABLE `$table` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                }
            } else {
                Log::info("Table '$table' does not exist, skipping engine and charset conversion");
            }
        }

        // Step 2: Fix orphaned records that would violate foreign key constraints

        // Fix visitors with invalid employee_id before adding foreign key constraints
        if (Schema::hasTable('visitors') && Schema::hasTable('employees')) {
            $orphanedVisitors = DB::select("
                SELECT COUNT(*) as count FROM visitors v
                LEFT JOIN employees e ON v.employee_id = e.id
                WHERE e.id IS NULL
            ")[0]->count;

            if ($orphanedVisitors > 0) {
                Log::info("Found $orphanedVisitors visitor records with invalid employee_id references, fixing...");

                // Get the first valid employee ID
                $validEmployee = DB::select("SELECT id FROM employees WHERE status = 1 LIMIT 1");

                if (count($validEmployee) > 0) {
                    $validId = $validEmployee[0]->id;
                    DB::statement("UPDATE visitors SET employee_id = $validId WHERE employee_id NOT IN (SELECT id FROM employees)");
                    Log::info("Updated visitor records with invalid employee_id to use employee ID $validId");
                } else {
                    // If no valid employee exists, create one
                    Log::info("No valid employees found, creating a placeholder employee");

                    // Get a valid department or create one
                    $departmentId = 1;
                    if (!DB::table('departments')->where('id', $departmentId)->exists()) {
                        if (Schema::hasTable('departments')) {
                            $department = DB::select("SELECT id FROM departments LIMIT 1");
                            if (count($department) > 0) {
                                $departmentId = $department[0]->id;
                            } else {
                                DB::table('departments')->insert([
                                    'name' => 'Default Department',
                                    'created_at' => now(),
                                    'updated_at' => now()
                                ]);
                                $departmentId = DB::getPdo()->lastInsertId();
                            }
                        }
                    }

                    // Create placeholder employee
                    DB::table('employees')->insert([
                        'department_id' => $departmentId,
                        'emply_id' => 'DEFAULT001',
                        'name' => 'System Default',
                        'designation' => 'System',
                        'phone' => '00000000',
                        'email' => 'system@example.com',
                        'status' => 1,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    $newEmployeeId = DB::getPdo()->lastInsertId();
                    DB::statement("UPDATE visitors SET employee_id = $newEmployeeId WHERE employee_id NOT IN (SELECT id FROM employees)");
                    Log::info("Created placeholder employee and updated visitor records");
                }
            }

            // Fix visitors with invalid department_id
            $orphanedDeptVisitors = DB::select("
                SELECT COUNT(*) as count FROM visitors v
                LEFT JOIN departments d ON v.department_id = d.id
                WHERE d.id IS NULL
            ")[0]->count;

            if ($orphanedDeptVisitors > 0) {
                Log::info("Found $orphanedDeptVisitors visitor records with invalid department_id references, fixing...");

                // Get valid department ID
                $validDept = DB::select("SELECT id FROM departments LIMIT 1");

                if (count($validDept) > 0) {
                    $validId = $validDept[0]->id;
                    DB::statement("UPDATE visitors SET department_id = $validId WHERE department_id NOT IN (SELECT id FROM departments)");
                    Log::info("Updated visitor records with invalid department_id");
                } else {
                    // Create department if none exists
                    DB::table('departments')->insert([
                        'name' => 'Default Department',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    $newDeptId = DB::getPdo()->lastInsertId();
                    DB::statement("UPDATE visitors SET department_id = $newDeptId WHERE department_id NOT IN (SELECT id FROM departments)");
                    Log::info("Created default department and updated visitor records");
                }
            }
        }

        // Fix visitor_guests with invalid visitor_id
        if (Schema::hasTable('visitor_guests') && Schema::hasTable('visitors')) {
            $orphanedGuests = DB::select("
                SELECT COUNT(*) as count FROM visitor_guests vg
                LEFT JOIN visitors v ON vg.visitor_id = v.id
                WHERE v.id IS NULL
            ")[0]->count;

            if ($orphanedGuests > 0) {
                Log::info("Found $orphanedGuests visitor guest records with invalid visitor_id references");

                // For guests, we'll delete the orphaned records as they depend on a visitor
                DB::statement("DELETE FROM visitor_guests WHERE visitor_id NOT IN (SELECT id FROM visitors)");
                Log::info("Deleted orphaned visitor guest records with invalid visitor_id references");
            }
        }

        // Step 3: Add or update foreign key constraints

        // Define the foreign key relationships
        $foreignKeys = [
            'employees' => [
                [
                    'column' => 'department_id',
                    'references' => 'departments',
                    'ref_column' => 'id',
                    'constraint_name' => 'employees_department_id_foreign'
                ]
            ],
            'visitors' => [
                [
                    'column' => 'employee_id',
                    'references' => 'employees',
                    'ref_column' => 'id',
                    'constraint_name' => 'visitors_employee_id_foreign'
                ],
                [
                    'column' => 'department_id',
                    'references' => 'departments',
                    'ref_column' => 'id',
                    'constraint_name' => 'visitors_department_id_foreign'
                ]
            ],
            'visitor_guests' => [
                [
                    'column' => 'visitor_id',
                    'references' => 'visitors',
                    'ref_column' => 'id',
                    'constraint_name' => 'visitor_guests_visitor_id_foreign'
                ]
            ]
        ];

        foreach ($foreignKeys as $table => $constraints) {
            if (Schema::hasTable($table)) {
                foreach ($constraints as $constraint) {
                    try {
                        // Check if the referenced table exists
                        if (!Schema::hasTable($constraint['references'])) {
                            Log::error("Referenced table '{$constraint['references']}' does not exist, skipping foreign key");
                            continue;
                        }

                        // Check if the foreign key already exists
                        $existingConstraint = $this->getForeignKeyName($table, $constraint['column']);

                        if ($existingConstraint) {
                            // Drop existing constraint
                            Log::info("Dropping existing foreign key '$existingConstraint' on table '$table'");
                            DB::statement("ALTER TABLE `$table` DROP FOREIGN KEY `$existingConstraint`");
                        }

                        // Add the foreign key with ON DELETE CASCADE
                        Log::info("Adding foreign key '{$constraint['constraint_name']}' to table '$table'");
                        DB::statement("ALTER TABLE `$table` ADD CONSTRAINT `{$constraint['constraint_name']}`
                            FOREIGN KEY (`{$constraint['column']}`)
                            REFERENCES `{$constraint['references']}`(`{$constraint['ref_column']}`)
                            ON DELETE CASCADE");
                    } catch (\Exception $e) {
                        Log::error("Error adding foreign key to table '$table': " . $e->getMessage());

                        // Try to continue with other constraints
                        continue;
                    }
                }
            } else {
                Log::info("Table '$table' does not exist, skipping foreign key creation");
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No need to reverse these changes
    }

    /**
     * Helper function to get foreign key name for a column
     *
     * @param string $table
     * @param string $column
     * @return string|null
     */
    private function getForeignKeyName($table, $column)
    {
        try {
            $result = DB::select("
                SELECT CONSTRAINT_NAME as constraint_name
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE() AND
                    TABLE_NAME = '$table' AND
                    COLUMN_NAME = '$column' AND
                    REFERENCED_TABLE_NAME IS NOT NULL
                LIMIT 1
            ");

            return count($result) > 0 ? $result[0]->constraint_name : null;
        } catch (\Exception $e) {
            Log::error("Error getting foreign key name: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Helper function to get foreign keys for a table
     *
     * @param string $table
     * @return array
     */
    private function getForeignKeys($table)
    {
        $foreignKeys = [];

        try {
            $result = DB::select("
                SELECT
                    COLUMN_NAME as column_name,
                    REFERENCED_TABLE_NAME as referenced_table,
                    REFERENCED_COLUMN_NAME as referenced_column
                FROM
                    INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                WHERE
                    TABLE_SCHEMA = DATABASE() AND
                    TABLE_NAME = '$table' AND
                    REFERENCED_TABLE_NAME IS NOT NULL
            ");

            if ($result) {
                $foreignKeys = $result;
            }
        } catch (\Exception $e) {
            // Silently handle exceptions
        }

        return $foreignKeys;
    }
}
