<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\CategoryTranslation;
use App\Models\Material;
use App\Models\MaterialTranslation;
use App\Models\Project;
use App\Models\ProjectPhoto;
use App\Models\ProjectTranslation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortfolioShowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        if (! extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('PDO SQLite extension is not available');
        }

        parent::setUp();

        $this->seed(\Database\Seeders\StaticTranslationsSeeder::class);
        app()->setLocale('en-UK');
    }

    public function test_portfolio_show_displays_project_details_and_client_link()
    {
        $project = Project::factory()->create([
            'production_date' => '2025-05-11',
            'execution_time' => 42,
            'width' => 300,
            'length' => 180,
            'height' => 95,
            'weight' => 123.4,
            'client' => 'ACME Architecture',
            'client_url' => 'https://example.com',
            'is_active' => true,
        ]);

        ProjectTranslation::create([
            'project_id' => $project->id,
            'locale' => 'en-UK',
            'name' => 'Museum Facade Scale Model',
            'description' => 'Detailed architectural prototype for client validation.',
        ]);

        $material = Material::query()->create();
        MaterialTranslation::create([
            'material_id' => $material->id,
            'locale' => 'en-UK',
            'name' => 'PLA',
        ]);
        $project->materials()->attach($material);

        ProjectPhoto::create([
            'project_id' => $project->id,
            'path' => 'projects/museum-main.jpg',
            'original_path' => 'projects/museum-main-original.jpg',
            'is_primary' => true,
        ]);

        $response = $this->get(route('portfolio.show', $project));

        $response->assertOk();
        $response->assertSeeText('Museum Facade Scale Model');
        $response->assertSeeText('Project details');
        $response->assertSeeText('2025');
        $response->assertSeeText('42h');
        $response->assertSeeText('300 mm');
        $response->assertSeeText('180 mm');
        $response->assertSeeText('95 mm');
        $response->assertSeeText('123.40 g');
        $response->assertSeeText('ACME Architecture');
        $response->assertSee('href="https://example.com"', false);
        $response->assertSeeText('PLA');
        $response->assertSee('museum-main.jpg', false);
    }

    public function test_portfolio_show_related_projects_are_filtered_by_categories()
    {
        $categoryA = Category::query()->create();
        CategoryTranslation::create([
            'category_id' => $categoryA->id,
            'locale' => 'en-UK',
            'name' => 'Architecture',
        ]);

        $categoryB = Category::query()->create();
        CategoryTranslation::create([
            'category_id' => $categoryB->id,
            'locale' => 'en-UK',
            'name' => 'Product Design',
        ]);

        $project = Project::factory()->create(['is_active' => true]);
        ProjectTranslation::create([
            'project_id' => $project->id,
            'locale' => 'en-UK',
            'name' => 'Main Project',
            'description' => null,
        ]);
        $project->categories()->attach($categoryA);

        $related = Project::factory()->create(['is_active' => true]);
        ProjectTranslation::create([
            'project_id' => $related->id,
            'locale' => 'en-UK',
            'name' => 'Related Project',
            'description' => null,
        ]);
        $related->categories()->attach($categoryA);

        $unrelated = Project::factory()->create(['is_active' => true]);
        ProjectTranslation::create([
            'project_id' => $unrelated->id,
            'locale' => 'en-UK',
            'name' => 'Unrelated Project',
            'description' => null,
        ]);
        $unrelated->categories()->attach($categoryB);

        $inactiveRelated = Project::factory()->create(['is_active' => false]);
        ProjectTranslation::create([
            'project_id' => $inactiveRelated->id,
            'locale' => 'en-UK',
            'name' => 'Inactive Related Project',
            'description' => null,
        ]);
        $inactiveRelated->categories()->attach($categoryA);

        $response = $this->get(route('portfolio.show', $project));

        $response->assertOk();
        $response->assertSeeText('Related Projects');
        $response->assertSee(route('portfolio.show', $related), false);
        $response->assertDontSee(route('portfolio.show', $unrelated), false);
        $response->assertDontSee(route('portfolio.show', $inactiveRelated), false);
    }

    public function test_portfolio_show_returns_not_found_for_inactive_project()
    {
        $project = Project::factory()->create(['is_active' => false]);

        $response = $this->get(route('portfolio.show', $project));

        $response->assertNotFound();
    }
}
