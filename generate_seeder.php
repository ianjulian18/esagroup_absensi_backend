<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$tables = [
    'regions', 'areas', 'sub_areas', 'channels', 'entities', 'principals', 'locations',
    'working_hours', 'working_groups', 'working_group_schedules', 'users', 'principal_user',
    'roles', 'permissions', 'model_has_permissions', 'model_has_roles', 'role_has_permissions',
    'stores', 'attendances', 'baps', 'extra_hours', 'leaves', 'visits', 'visit_logs', 'itineraries', 'payslips'
];

$output = "<?php\n\nnamespace Database\Seeders;\n\nuse Illuminate\Database\Seeder;\nuse Illuminate\Support\Facades\DB;\n\nclass ExistingDataSeeder extends Seeder\n{\n    public function run(): void\n    {\n";

foreach ($tables as $table) {
    if (!\Illuminate\Support\Facades\Schema::hasTable($table)) continue;

    $rows = DB::table($table)->get()->map(function($item) { return (array)$item; })->toArray();
    if (count($rows) === 0) continue;

    $output .= "        DB::table('{$table}')->insert([\n";
    foreach ($rows as $row) {
        $output .= "            [";
        foreach ($row as $key => $value) {
            if ($value === null) {
                $output .= "'{$key}' => null, ";
            } else {
                $val = var_export($value, true);
                $output .= "'{$key}' => {$val}, ";
            }
        }
        $output .= "],\n";
    }
    $output .= "        ]);\n\n";
}

$output .= "    }\n}\n";

file_put_contents(__DIR__.'/database/seeders/ExistingDataSeeder.php', $output);

echo "ExistingDataSeeder generated successfully!\n";
