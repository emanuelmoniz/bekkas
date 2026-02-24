<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use App\Services\ImageThumbnailService;

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

        // --- basic project data ------------------------------------------------
        DB::table('projects')->insert([
            // architecture models
            [
                'id' => 1,
                'production_date' => '2025-01-15',
                'execution_time' => '24.00',
                'width' => 200,
                'length' => 150,
                'height' => 100,
                'weight' => '500.00',
                'is_active' => true,
                'is_featured' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'production_date' => '2025-02-10',
                'execution_time' => '48.00',
                'width' => 300,
                'length' => 200,
                'height' => 400,
                'weight' => '1200.00',
                'is_active' => true,
                'is_featured' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'production_date' => '2025-03-05',
                'execution_time' => '18.00',
                'width' => 250,
                'length' => 100,
                'height' => 80,
                'weight' => '650.00',
                'is_active' => true,
                'is_featured' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'production_date' => '2025-04-20',
                'execution_time' => '36.00',
                'width' => 400,
                'length' => 300,
                'height' => 200,
                'weight' => '2000.00',
                'is_active' => true,
                'is_featured' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'production_date' => '2025-05-18',
                'execution_time' => '30.00',
                'width' => 180,
                'length' => 160,
                'height' => 120,
                'weight' => '800.00',
                'is_active' => true,
                'is_featured' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'production_date' => '2025-06-22',
                'execution_time' => '60.00',
                'width' => 500,
                'length' => 350,
                'height' => 450,
                'weight' => '2500.00',
                'is_active' => true,
                'is_featured' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 7,
                'production_date' => '2025-07-11',
                'execution_time' => '20.00',
                'width' => 220,
                'length' => 180,
                'height' => 140,
                'weight' => '900.00',
                'is_active' => true,
                'is_featured' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 8,
                'production_date' => '2025-08-30',
                'execution_time' => '72.00',
                'width' => 600,
                'length' => 400,
                'height' => 300,
                'weight' => '3000.00',
                'is_active' => true,
                'is_featured' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // design models
            [
                'id' => 9,
                'production_date' => '2025-09-14',
                'execution_time' => '15.00',
                'width' => 160,
                'length' => 120,
                'height' => 90,
                'weight' => '550.00',
                'is_active' => true,
                'is_featured' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 10,
                'production_date' => '2025-10-05',
                'execution_time' => '22.00',
                'width' => 210,
                'length' => 170,
                'height' => 110,
                'weight' => '720.00',
                'is_active' => true,
                'is_featured' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 11,
                'production_date' => '2025-11-20',
                'execution_time' => '12.00',
                'width' => 140,
                'length' => 130,
                'height' => 85,
                'weight' => '480.00',
                'is_active' => true,
                'is_featured' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 12,
                'production_date' => '2025-12-12',
                'execution_time' => '18.00',
                'width' => 170,
                'length' => 150,
                'height' => 95,
                'weight' => '610.00',
                'is_active' => true,
                'is_featured' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 13,
                'production_date' => '2026-01-08',
                'execution_time' => '20.00',
                'width' => 190,
                'length' => 160,
                'height' => 100,
                'weight' => '780.00',
                'is_active' => true,
                'is_featured' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 14,
                'production_date' => '2026-01-25',
                'execution_time' => '25.00',
                'width' => 230,
                'length' => 180,
                'height' => 120,
                'weight' => '870.00',
                'is_active' => true,
                'is_featured' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 15,
                'production_date' => '2026-02-10',
                'execution_time' => '30.00',
                'width' => 260,
                'length' => 200,
                'height' => 140,
                'weight' => '950.00',
                'is_active' => true,
                'is_featured' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // translations for each project (pt-PT and en-UK)
        DB::table('project_translations')->insert([
            ['id' => 1, 'project_id' => 1, 'locale' => 'pt-PT', 'name' => 'Modelo Casa Moderna', 'description' => null],
            ['id' => 2, 'project_id' => 1, 'locale' => 'en-UK', 'name' => 'Modern House Model', 'description' => null],
            ['id' => 3, 'project_id' => 2, 'locale' => 'pt-PT', 'name' => 'Modelo Arranha-céus', 'description' => null],
            ['id' => 4, 'project_id' => 2, 'locale' => 'en-UK', 'name' => 'Skyscraper Model', 'description' => null],
            ['id' => 5, 'project_id' => 3, 'locale' => 'pt-PT', 'name' => 'Modelo Ponte', 'description' => null],
            ['id' => 6, 'project_id' => 3, 'locale' => 'en-UK', 'name' => 'Bridge Design Model', 'description' => null],
            ['id' => 7, 'project_id' => 4, 'locale' => 'pt-PT', 'name' => 'Modelo Planejamento Urbano', 'description' => null],
            ['id' => 8, 'project_id' => 4, 'locale' => 'en-UK', 'name' => 'Urban Planning Model', 'description' => null],
            ['id' => 9, 'project_id' => 5, 'locale' => 'pt-PT', 'name' => 'Modelo Vila Clássica', 'description' => null],
            ['id' => 10, 'project_id' => 5, 'locale' => 'en-UK', 'name' => 'Classic Villa Model', 'description' => null],
            ['id' => 11, 'project_id' => 6, 'locale' => 'pt-PT', 'name' => 'Modelo Arquitetura Museu', 'description' => null],
            ['id' => 12, 'project_id' => 6, 'locale' => 'en-UK', 'name' => 'Museum Architecture Model', 'description' => null],
            ['id' => 13, 'project_id' => 7, 'locale' => 'pt-PT', 'name' => 'Modelo Edifício Sustentável', 'description' => null],
            ['id' => 14, 'project_id' => 7, 'locale' => 'en-UK', 'name' => 'Sustainable Building Model', 'description' => null],
            ['id' => 15, 'project_id' => 8, 'locale' => 'pt-PT', 'name' => 'Modelo Cidade Futurista', 'description' => null],
            ['id' => 16, 'project_id' => 8, 'locale' => 'en-UK', 'name' => 'Futuristic City Model', 'description' => null],
            ['id' => 17, 'project_id' => 9, 'locale' => 'pt-PT', 'name' => 'Modelo Design de Interiores', 'description' => null],
            ['id' => 18, 'project_id' => 9, 'locale' => 'en-UK', 'name' => 'Interior Design Model', 'description' => null],
            ['id' => 19, 'project_id' => 10, 'locale' => 'pt-PT', 'name' => 'Modelo Design de Paisagem', 'description' => null],
            ['id' => 20, 'project_id' => 10, 'locale' => 'en-UK', 'name' => 'Landscape Design Model', 'description' => null],
            ['id' => 21, 'project_id' => 11, 'locale' => 'pt-PT', 'name' => 'Modelo Protótipo Mobiliário', 'description' => null],
            ['id' => 22, 'project_id' => 11, 'locale' => 'en-UK', 'name' => 'Furniture Prototype Model', 'description' => null],
            ['id' => 23, 'project_id' => 12, 'locale' => 'pt-PT', 'name' => 'Modelo Design de Produto', 'description' => null],
            ['id' => 24, 'project_id' => 12, 'locale' => 'en-UK', 'name' => 'Product Design Model', 'description' => null],
            ['id' => 25, 'project_id' => 13, 'locale' => 'pt-PT', 'name' => 'Modelo Design de Moda', 'description' => null],
            ['id' => 26, 'project_id' => 13, 'locale' => 'en-UK', 'name' => 'Fashion Design Model', 'description' => null],
            ['id' => 27, 'project_id' => 14, 'locale' => 'pt-PT', 'name' => 'Modelo Design Gráfico', 'description' => null],
            ['id' => 28, 'project_id' => 14, 'locale' => 'en-UK', 'name' => 'Graphic Design Model', 'description' => null],
            ['id' => 29, 'project_id' => 15, 'locale' => 'pt-PT', 'name' => 'Modelo Design Industrial', 'description' => null],
            ['id' => 30, 'project_id' => 15, 'locale' => 'en-UK', 'name' => 'Industrial Design Model', 'description' => null],
        ]);

        // simple material relationships – all projects use material 1
        $materialRows = [];
        for ($i = 1; $i <= 15; $i++) {
            $materialRows[] = ['project_id' => $i, 'material_id' => 1];
        }
        DB::table('material_project')->insert($materialRows);

        // ---------------------------------------------------------------------
        // dynamic photo fetching using unsplash service (search term = project name)
        $service = new ImageThumbnailService;
        $unsplashService = app(\App\Services\UnsplashService::class);

        $queries = [
            1 => 'modern house model',
            2 => 'skyscraper model',
            3 => 'bridge design model',
            4 => 'urban planning model',
            5 => 'classic villa model',
            6 => 'museum architecture model',
            7 => 'sustainable building model',
            8 => 'futuristic city model',
            9 => 'interior design model',
            10 => 'landscape design model',
            11 => 'furniture prototype model',
            12 => 'product design model',
            13 => 'fashion design model',
            14 => 'graphic design model',
            15 => 'industrial design model',
        ];

        $nextId = DB::table('project_photos')->max('id') + 1;
        $photoRows = [];
        $basePhotos = [];

        foreach ($queries as $pid => $query) {
            try {
                $downloaded = $unsplashService->searchAndDownload($query, 'seed-temp');
                if ($downloaded) {
                    $filePath = public_path($downloaded);
                    if (file_exists($filePath)) {
                        $file = new UploadedFile($filePath, basename($filePath), null, null, true);
                        $paths = $service->store($file, 'projects');
                        $row = [
                            'id' => $nextId++,
                            'project_id' => $pid,
                            'path' => $paths['path'],
                            'original_path' => $paths['original_path'],
                            'is_primary' => 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                        $photoRows[] = $row;
                        $basePhotos[] = $row;
                        @unlink($filePath);
                    }
                }
            } catch (\Exception $e) {
                // ignore failures and continue
            }
        }

        if (!empty($basePhotos)) {
            $counts = array_fill_keys(array_keys($queries), 1);
            while (min($counts) < 5) {
                $eligible = array_filter($counts, function ($c) {
                    return $c < 5;
                });
                $targetPid = array_rand($eligible);
                $sample = $basePhotos[array_rand($basePhotos)];
                $photoRows[] = [
                    'id' => $nextId++,
                    'project_id' => $targetPid,
                    'path' => $sample['path'],
                    'original_path' => $sample['original_path'],
                    'is_primary' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $counts[$targetPid]++;
            }
        }

        @array_map('unlink', glob(public_path('seed-temp/*')));

        if (!empty($photoRows)) {
            DB::table('project_photos')->insert($photoRows);
        }
    }
}
