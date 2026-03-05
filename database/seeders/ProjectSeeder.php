<?php

namespace Database\Seeders;

use App\Services\ImageThumbnailService;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('project_photos')->truncate();
        DB::table('material_project')->truncate();
        DB::table('category_project')->truncate();
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
            // engineering / technical models
            [
                'id' => 16,
                'production_date' => '2026-02-15',
                'execution_time' => '28.00',
                'width' => 240,
                'length' => 190,
                'height' => 130,
                'weight' => '820.00',
                'is_active' => true,
                'is_featured' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 17,
                'production_date' => '2026-02-20',
                'execution_time' => '45.00',
                'width' => 350,
                'length' => 280,
                'height' => 220,
                'weight' => '1500.00',
                'is_active' => true,
                'is_featured' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 18,
                'production_date' => '2026-02-22',
                'execution_time' => '16.00',
                'width' => 150,
                'length' => 110,
                'height' => 75,
                'weight' => '420.00',
                'is_active' => true,
                'is_featured' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 19,
                'production_date' => '2026-02-23',
                'execution_time' => '55.00',
                'width' => 480,
                'length' => 320,
                'height' => 260,
                'weight' => '2200.00',
                'is_active' => true,
                'is_featured' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 20,
                'production_date' => '2026-02-24',
                'execution_time' => '38.00',
                'width' => 310,
                'length' => 240,
                'height' => 170,
                'weight' => '1100.00',
                'is_active' => true,
                'is_featured' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 21,
                'production_date' => '2026-02-25',
                'execution_time' => '42.00',
                'width' => 330,
                'length' => 260,
                'height' => 180,
                'weight' => '1350.00',
                'is_active' => true,
                'is_featured' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 22,
                'production_date' => '2026-02-26',
                'execution_time' => '14.00',
                'width' => 130,
                'length' => 100,
                'height' => 70,
                'weight' => '390.00',
                'is_active' => true,
                'is_featured' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 23,
                'production_date' => '2026-02-26',
                'execution_time' => '50.00',
                'width' => 420,
                'length' => 310,
                'height' => 240,
                'weight' => '1800.00',
                'is_active' => true,
                'is_featured' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 24,
                'production_date' => '2026-02-27',
                'execution_time' => '32.00',
                'width' => 270,
                'length' => 210,
                'height' => 150,
                'weight' => '990.00',
                'is_active' => true,
                'is_featured' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 25,
                'production_date' => '2026-02-27',
                'execution_time' => '65.00',
                'width' => 540,
                'length' => 380,
                'height' => 320,
                'weight' => '2800.00',
                'is_active' => true,
                'is_featured' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Backfill UUIDs — the seeder inserts raw rows without uuid,
        // so we generate them here (same approach as the migration backfill).
        $projectIds = DB::table('projects')->whereNull('uuid')->pluck('id');
        foreach ($projectIds as $id) {
            DB::table('projects')->where('id', $id)->update(['uuid' => (string) \Illuminate\Support\Str::uuid()]);
        }

        // Add some random clients and optional client URLs to projects
        $faker = \Faker\Factory::create();
        for ($i = 1; $i <= 25; $i++) {
            if ($faker->boolean(70)) { // ~70% of projects have a client
                $client = $faker->company();
                $clientUrl = $faker->boolean(40) ? $faker->url() : null; // ~40% of those have a URL
                DB::table('projects')->where('id', $i)->update([
                    'client' => $client,
                    'client_url' => $clientUrl,
                ]);
            }
        }

        // translations for each project (pt-PT and en-UK)
        DB::table('project_translations')->insert([
            ['id' => 1, 'project_id' => 1, 'locale' => 'pt-PT', 'name' => 'Modelo Casa Moderna', 'description' => 'Modelo residencial contemporâneo com linhas limpas e soluções de espaço abertas.'],
            ['id' => 2, 'project_id' => 1, 'locale' => 'en-UK', 'name' => 'Modern House Model', 'description' => 'A contemporary residential model featuring clean lines and open-space solutions.'],
            ['id' => 3, 'project_id' => 2, 'locale' => 'pt-PT', 'name' => 'Modelo Arranha-céus', 'description' => 'Arranha-céus conceptual com fachada envidraçada e estrutura leve.'],
            ['id' => 4, 'project_id' => 2, 'locale' => 'en-UK', 'name' => 'Skyscraper Model', 'description' => 'A conceptual skyscraper with glazed facades and lightweight structure.'],
            ['id' => 5, 'project_id' => 3, 'locale' => 'pt-PT', 'name' => 'Modelo Ponte', 'description' => 'Projeto de ponte com ênfase em engenharia estrutural e estética.'],
            ['id' => 6, 'project_id' => 3, 'locale' => 'en-UK', 'name' => 'Bridge Design Model', 'description' => 'Bridge design focusing on structural engineering and visual elegance.'],
            ['id' => 7, 'project_id' => 4, 'locale' => 'pt-PT', 'name' => 'Modelo Planejamento Urbano', 'description' => 'Plano urbano integrado com zonas residenciais e espaços verdes.'],
            ['id' => 8, 'project_id' => 4, 'locale' => 'en-UK', 'name' => 'Urban Planning Model', 'description' => 'Integrated urban plan combining residential zones and green spaces.'],
            ['id' => 9, 'project_id' => 5, 'locale' => 'pt-PT', 'name' => 'Modelo Vila Clássica', 'description' => 'Vila clássica com detalhes ornamentais e pátio central.'],
            ['id' => 10, 'project_id' => 5, 'locale' => 'en-UK', 'name' => 'Classic Villa Model', 'description' => 'Classic villa featuring ornamental details and a central courtyard.'],
            ['id' => 11, 'project_id' => 6, 'locale' => 'pt-PT', 'name' => 'Modelo Arquitetura Museu', 'description' => 'Concepção de museu com fluxos expositivos otimizados e iluminação natural.'],
            ['id' => 12, 'project_id' => 6, 'locale' => 'en-UK', 'name' => 'Museum Architecture Model', 'description' => 'Museum concept with optimized exhibition flows and natural lighting.'],
            ['id' => 13, 'project_id' => 7, 'locale' => 'pt-PT', 'name' => 'Modelo Edifício Sustentável', 'description' => 'Edifício que prioriza eficiência energética e materiais sustentáveis.'],
            ['id' => 14, 'project_id' => 7, 'locale' => 'en-UK', 'name' => 'Sustainable Building Model', 'description' => 'Building prioritising energy efficiency and sustainable materials.'],
            ['id' => 15, 'project_id' => 8, 'locale' => 'pt-PT', 'name' => 'Modelo Cidade Futurista', 'description' => 'Modelo urbano futurista com infraestruturas inovadoras.'],
            ['id' => 16, 'project_id' => 8, 'locale' => 'en-UK', 'name' => 'Futuristic City Model', 'description' => 'Futuristic urban model with innovative infrastructure concepts.'],
            ['id' => 17, 'project_id' => 9, 'locale' => 'pt-PT', 'name' => 'Modelo Design de Interiores', 'description' => 'Projeto de interiores focado em materiais e ergonomia.'],
            ['id' => 18, 'project_id' => 9, 'locale' => 'en-UK', 'name' => 'Interior Design Model', 'description' => 'Interior design project focused on materials and ergonomics.'],
            ['id' => 19, 'project_id' => 10, 'locale' => 'pt-PT', 'name' => 'Modelo Design de Paisagem', 'description' => 'Design de paisagem contemplativo com percursos e zonas de descanso.'],
            ['id' => 20, 'project_id' => 10, 'locale' => 'en-UK', 'name' => 'Landscape Design Model', 'description' => 'Contemplative landscape design with pathways and rest areas.'],
            ['id' => 21, 'project_id' => 11, 'locale' => 'pt-PT', 'name' => 'Modelo Protótipo Mobiliário', 'description' => 'Protótipo de mobiliário com preocupação funcional e estética.'],
            ['id' => 22, 'project_id' => 11, 'locale' => 'en-UK', 'name' => 'Furniture Prototype Model', 'description' => 'Furniture prototype prioritising function and aesthetics.'],
            ['id' => 23, 'project_id' => 12, 'locale' => 'pt-PT', 'name' => 'Modelo Design de Produto', 'description' => 'Design de produto pensado para produção em pequena escala.'],
            ['id' => 24, 'project_id' => 12, 'locale' => 'en-UK', 'name' => 'Product Design Model', 'description' => 'Product design intended for small-scale production.'],
            ['id' => 25, 'project_id' => 13, 'locale' => 'pt-PT', 'name' => 'Modelo Design de Moda', 'description' => 'Coleção de moda conceptual com silhuetas contemporâneas.'],
            ['id' => 26, 'project_id' => 13, 'locale' => 'en-UK', 'name' => 'Fashion Design Model', 'description' => 'Concept fashion collection with contemporary silhouettes.'],
            ['id' => 27, 'project_id' => 14, 'locale' => 'pt-PT', 'name' => 'Modelo Design Gráfico', 'description' => 'Peças de design gráfico com ênfase em tipografia e composição.'],
            ['id' => 28, 'project_id' => 14, 'locale' => 'en-UK', 'name' => 'Graphic Design Model', 'description' => 'Graphic design pieces emphasising typography and composition.'],
            ['id' => 29, 'project_id' => 15, 'locale' => 'pt-PT', 'name' => 'Modelo Design Industrial', 'description' => 'Projeto industrial com atenção à produção e durabilidade.'],
            ['id' => 30, 'project_id' => 15, 'locale' => 'en-UK', 'name' => 'Industrial Design Model', 'description' => 'Industrial design project focused on manufacturability and durability.'],
            ['id' => 31, 'project_id' => 16, 'locale' => 'pt-PT', 'name' => 'Modelo Estrutura Metálica', 'description' => 'Estrutura metálica detalhada com ligações e cargas calculadas.'],
            ['id' => 32, 'project_id' => 16, 'locale' => 'en-UK', 'name' => 'Metal Structure Model', 'description' => 'Detailed metal structure with calculated connections and loads.'],
            ['id' => 33, 'project_id' => 17, 'locale' => 'pt-PT', 'name' => 'Modelo Central Elétrica', 'description' => 'Conceito de central elétrica com componentes técnicos e segurança.'],
            ['id' => 34, 'project_id' => 17, 'locale' => 'en-UK', 'name' => 'Power Plant Model', 'description' => 'Power plant concept including technical components and safety systems.'],
            ['id' => 35, 'project_id' => 18, 'locale' => 'pt-PT', 'name' => 'Modelo Componente Mecânico', 'description' => 'Componente mecânico projetado para precisão e montagem.'],
            ['id' => 36, 'project_id' => 18, 'locale' => 'en-UK', 'name' => 'Mechanical Component Model', 'description' => 'Mechanical component engineered for precision and assembly.'],
            ['id' => 37, 'project_id' => 19, 'locale' => 'pt-PT', 'name' => 'Modelo Plataforma Offshore', 'description' => 'Plataforma offshore concebida para operações marinhas robustas.'],
            ['id' => 38, 'project_id' => 19, 'locale' => 'en-UK', 'name' => 'Offshore Platform Model', 'description' => 'Offshore platform designed for robust marine operations.'],
            ['id' => 39, 'project_id' => 20, 'locale' => 'pt-PT', 'name' => 'Modelo Viaduto Rodoviário', 'description' => 'Viaduto rodoviário com rampas e apoios estruturais.'],
            ['id' => 40, 'project_id' => 20, 'locale' => 'en-UK', 'name' => 'Road Viaduct Model', 'description' => 'Road viaduct with ramps and structural bearings.'],
            ['id' => 41, 'project_id' => 21, 'locale' => 'pt-PT', 'name' => 'Modelo Complexo Hospitalar', 'description' => 'Complexo hospitalar com fluxos clínicos e áreas técnicas.'],
            ['id' => 42, 'project_id' => 21, 'locale' => 'en-UK', 'name' => 'Hospital Complex Model', 'description' => 'Hospital complex with clinical flows and technical zones.'],
            ['id' => 43, 'project_id' => 22, 'locale' => 'pt-PT', 'name' => 'Modelo Peça de Relojoaria', 'description' => 'Peça de relojoaria em escala com pormenores mecânicos finos.'],
            ['id' => 44, 'project_id' => 22, 'locale' => 'en-UK', 'name' => 'Watchmaking Piece Model', 'description' => 'Scaled watchmaking piece with fine mechanical details.'],
            ['id' => 45, 'project_id' => 23, 'locale' => 'pt-PT', 'name' => 'Modelo Navio de Cruzeiro', 'description' => 'Navio de cruzeiro conceptual com decks e áreas públicas.'],
            ['id' => 46, 'project_id' => 23, 'locale' => 'en-UK', 'name' => 'Cruise Ship Model', 'description' => 'Concept cruise ship including decks and public areas.'],
            ['id' => 47, 'project_id' => 24, 'locale' => 'pt-PT', 'name' => 'Modelo Turbina Eólica', 'description' => 'Turbina eólica com torre e rotor dimensionados para eficiência.'],
            ['id' => 48, 'project_id' => 24, 'locale' => 'en-UK', 'name' => 'Wind Turbine Model', 'description' => 'Wind turbine with tower and rotor sized for efficiency.'],
            ['id' => 49, 'project_id' => 25, 'locale' => 'pt-PT', 'name' => 'Modelo Estação Espacial', 'description' => 'Estação espacial conceptual com módulos habitacionais e científicos.'],
            ['id' => 50, 'project_id' => 25, 'locale' => 'en-UK', 'name' => 'Space Station Model', 'description' => 'Concept space station with habitable and scientific modules.'],
        ]);

        // simple material relationships – all projects use material 1
        $materialRows = [];
        for ($i = 1; $i <= 25; $i++) {
            $materialRows[] = ['project_id' => $i, 'material_id' => 1];
        }
        DB::table('material_project')->insert($materialRows);

        // simple category relationships – distribute categories across projects
        $categoryRows = [];
        for ($i = 1; $i <= 25; $i++) {
            if ($i <= 5) {
                $cat = 4; // Home
            } elseif ($i <= 10) {
                $cat = 3; // Organizer
            } elseif ($i <= 15) {
                $cat = 2; // Decoration
            } elseif ($i <= 20) {
                $cat = 1; // Christmas
            } else {
                $cat = 4;
            }
            $categoryRows[] = ['project_id' => $i, 'category_id' => $cat];
        }
        DB::table('category_project')->insert($categoryRows);

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
            16 => 'metal structure model',
            17 => 'power plant model',
            18 => 'mechanical component model',
            19 => 'offshore platform model',
            20 => 'road viaduct model',
            21 => 'hospital complex model',
            22 => 'watchmaking piece model',
            23 => 'cruise ship model',
            24 => 'wind turbine model',
            25 => 'space station model',
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

        if (! empty($basePhotos)) {
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

        if (! empty($photoRows)) {
            DB::table('project_photos')->insert($photoRows);
        }
    }
}
