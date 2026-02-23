<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('project_photos')->truncate();
        DB::table('material_project')->truncate();
        DB::table('project_translations')->truncate();
        DB::table('projects')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('projects')->insert([
            [
                'id' => 1,
                'production_date' => '2025-03-25',
                'execution_time' => '36.00',
                'dimensions' => null,
                'weight' => '450.00',
                'is_active' => true,
                'is_featured' => true,
                'created_at' => '2026-02-23 17:50:51',
                'updated_at' => '2026-02-23 17:50:51',
            ],
            [
                'id' => 2,
                'production_date' => '2024-03-23',
                'execution_time' => '1.00',
                'dimensions' => null,
                'weight' => '50.00',
                'is_active' => true,
                'is_featured' => false,
                'created_at' => '2026-02-23 17:59:29',
                'updated_at' => '2026-02-23 17:59:29',
            ],
        ]);

        DB::table('project_translations')->insert([
            ['id' => 1, 'project_id' => 1, 'locale' => 'pt-PT', 'name' => 'Aquaterrário', 'description' => 'Plataforma para aquaterrario.'],
            ['id' => 2, 'project_id' => 1, 'locale' => 'en-UK', 'name' => 'Aquaterrarium', 'description' => null],
            ['id' => 3, 'project_id' => 2, 'locale' => 'pt-PT', 'name' => 'Porta-chaves FERRARI', 'description' => 'Porta-chaves bi-color.'],
            ['id' => 4, 'project_id' => 2, 'locale' => 'en-UK', 'name' => 'FERRARI keychain', 'description' => 'Bi-color keychain'],
        ]);

        DB::table('material_project')->insert([
            ['project_id' => 1, 'material_id' => 1],
            ['project_id' => 2, 'material_id' => 1],
        ]);

        DB::table('project_photos')->insert([
            ['id' => 1, 'project_id' => 1, 'path' => 'projects/SsXnnz96NOqKiGLKnXa2yuYW4lQDieM82XbOhF6E.jpg', 'is_primary' => 0, 'created_at' => '2026-02-23 17:51:52', 'updated_at' => '2026-02-23 17:52:22'],
            ['id' => 2, 'project_id' => 1, 'path' => 'projects/jpBCYsOrGC9BRnH9Tj5aqMBjb4bq3haeda5cBYcS.jpg', 'is_primary' => 0, 'created_at' => '2026-02-23 17:51:52', 'updated_at' => '2026-02-23 17:52:22'],
            ['id' => 3, 'project_id' => 1, 'path' => 'projects/wXXxG9zoZif5yNynqI0QmUi9ylfFnVMRrg8uS9aA.jpg', 'is_primary' => 0, 'created_at' => '2026-02-23 17:51:52', 'updated_at' => '2026-02-23 17:52:22'],
            ['id' => 4, 'project_id' => 1, 'path' => 'projects/LHIfdYs4zyvRcbOY3SDrp50hCWdD5PsUbjHVEiqj.jpg', 'is_primary' => 0, 'created_at' => '2026-02-23 17:51:52', 'updated_at' => '2026-02-23 17:52:22'],
            ['id' => 5, 'project_id' => 1, 'path' => 'projects/0X4pPwJYrryF53l9t47WkzPjcDCiUK4O7G4wOf8X.jpg', 'is_primary' => 0, 'created_at' => '2026-02-23 17:51:52', 'updated_at' => '2026-02-23 17:52:22'],
            ['id' => 6, 'project_id' => 1, 'path' => 'projects/NF6gVlbfQCDkwFiCXLPDxsrcyWysfvbbz2ZIEChg.jpg', 'is_primary' => 0, 'created_at' => '2026-02-23 17:51:52', 'updated_at' => '2026-02-23 17:52:22'],
            ['id' => 7, 'project_id' => 1, 'path' => 'projects/XL9SKEA1DCZYlckkse4Uy4eEtIIXEueqhGNGRpzp.jpg', 'is_primary' => 0, 'created_at' => '2026-02-23 17:51:52', 'updated_at' => '2026-02-23 17:52:22'],
            ['id' => 8, 'project_id' => 1, 'path' => 'projects/LU4lCyDSxGmkoS6vjFffgwCspAf8gXe9cs3USjRS.jpg', 'is_primary' => 0, 'created_at' => '2026-02-23 17:51:52', 'updated_at' => '2026-02-23 17:52:22'],
            ['id' => 9, 'project_id' => 1, 'path' => 'projects/DrN51m9qFbxxecby1s2wa158NkiRHZY1TtnOJi3e.jpg', 'is_primary' => 0, 'created_at' => '2026-02-23 17:51:52', 'updated_at' => '2026-02-23 17:52:22'],
            ['id' => 10, 'project_id' => 1, 'path' => 'projects/KqieDDgSBBpQd6jF8Ii7kOlROaf9eRWnzv6bSrwC.jpg', 'is_primary' => 1, 'created_at' => '2026-02-23 17:52:19', 'updated_at' => '2026-02-23 17:52:22'],
            ['id' => 11, 'project_id' => 2, 'path' => 'projects/5Mi0EkmmEQyltgzG6LHm4rCCmmZfIRdVbvB8pZaG.jpg', 'is_primary' => 1, 'created_at' => '2026-02-23 18:00:12', 'updated_at' => '2026-02-23 18:00:12'],
        ]);
    }
}
