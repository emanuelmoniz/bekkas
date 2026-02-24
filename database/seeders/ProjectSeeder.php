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
                'width' => 350,
                'length' => 250,
                'height' => 450,
                'weight' => '450.00',
                'is_active' => true,
                'is_featured' => true,
                'created_at' => '2026-02-23 17:50:51',
                'updated_at' => '2026-02-24 13:12:01',
            ],
            [
                'id' => 2,
                'production_date' => '2024-03-23',
                'execution_time' => '1.00',
                'width' => 50,
                'length' => 80,
                'height' => 5,
                'weight' => '50.00',
                'is_active' => true,
                'is_featured' => false,
                'created_at' => '2026-02-23 17:59:29',
                'updated_at' => '2026-02-24 13:12:18',
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
            ['id' => 12, 'project_id' => 1, 'path' => 'projects/YxOSPiDVaxkYbogn9Bmz0SVAFnSk1DDsoRhrPq7l.jpg', 'is_primary' => 1, 'created_at' => '2026-02-24 12:56:06', 'updated_at' => '2026-02-24 12:56:06'],
            ['id' => 13, 'project_id' => 1, 'path' => 'projects/RW6XjuLfi01tgyzACNLv7mm2sazsnU3BXlFYKUxJ.jpg', 'is_primary' => 0, 'created_at' => '2026-02-24 12:56:06', 'updated_at' => '2026-02-24 12:56:06'],
            ['id' => 14, 'project_id' => 1, 'path' => 'projects/RCTsA4YHRurTK28kepcUPheznTxRLdMEhfJYHP1C.jpg', 'is_primary' => 0, 'created_at' => '2026-02-24 12:56:07', 'updated_at' => '2026-02-24 12:56:07'],
            ['id' => 15, 'project_id' => 1, 'path' => 'projects/o11l5x0jiTXTaltXbKpxYRKhZstWJ9hKugKtyBtc.jpg', 'is_primary' => 0, 'created_at' => '2026-02-24 12:56:08', 'updated_at' => '2026-02-24 12:56:08'],
            ['id' => 16, 'project_id' => 1, 'path' => 'projects/YPDCfbRtSqvlLzww2YKU0Mo4DgVckQm1Fh0OWXMg.jpg', 'is_primary' => 0, 'created_at' => '2026-02-24 12:56:09', 'updated_at' => '2026-02-24 12:56:09'],
            ['id' => 17, 'project_id' => 1, 'path' => 'projects/i5WODnTeADebRQflMdP0JYniIKwMT3UYTFm31XOZ.jpg', 'is_primary' => 0, 'created_at' => '2026-02-24 12:56:09', 'updated_at' => '2026-02-24 12:56:09'],
            ['id' => 18, 'project_id' => 1, 'path' => 'projects/6NfSvDF0vLDORMbtWxoedh0WKXqKl5OTzhKAVa4I.jpg', 'is_primary' => 0, 'created_at' => '2026-02-24 12:56:10', 'updated_at' => '2026-02-24 12:56:10'],
            ['id' => 19, 'project_id' => 1, 'path' => 'projects/M3Zcd6qrsSzoYtjhhiwgtDKV4NVRkRlcIIMWULzh.jpg', 'is_primary' => 0, 'created_at' => '2026-02-24 12:56:11', 'updated_at' => '2026-02-24 12:56:11'],
            ['id' => 20, 'project_id' => 2, 'path' => 'projects/yFn20emBea4DX0PxbQDX60Q5VA7ALLwmAOEx92pj.jpg', 'is_primary' => 1, 'created_at' => '2026-02-24 12:57:36', 'updated_at' => '2026-02-24 12:57:36'],
        ]);
    }
}
