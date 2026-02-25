<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Project;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminPhotoDeletionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Convenience helper for logging in a user with the admin role.
     *
     * @return \App\Models\User
     */
    protected function signInAsAdmin()
    {
        $admin = \App\Models\User::factory()->create();
        $admin->roles()->attach(Role::firstOrCreate(['name' => 'admin'])->id);
        $this->actingAs($admin);

        return $admin;
    }

    #[Test]
    public function deleting_a_product_photo_removes_thumbnail_and_original()
    {
        Storage::fake('public');
        $this->signInAsAdmin();

        $product = Product::factory()->create();
        $photo = $product->photos()->create([
            'path' => 'products/thumb.jpg',
            'original_path' => 'products/originals/orig.jpg',
        ]);

        // create dummy files that should be cleaned up
        Storage::disk('public')->put($photo->path, '');
        Storage::disk('public')->put($photo->original_path, '');

        $response = $this->delete(route('admin.photos.destroy', $photo));
        $response->assertRedirect();

        Storage::disk('public')->assertMissing($photo->path);
        Storage::disk('public')->assertMissing($photo->original_path);
    }

    #[Test]
    public function deleting_a_project_photo_removes_thumbnail_and_original()
    {
        Storage::fake('public');
        $this->signInAsAdmin();

        $project = Project::factory()->create();
        $photo = $project->photos()->create([
            'path' => 'projects/thumb.jpg',
            'original_path' => 'projects/originals/orig.jpg',
        ]);

        Storage::disk('public')->put($photo->path, '');
        Storage::disk('public')->put($photo->original_path, '');

        $response = $this->delete(route('admin.project-photos.destroy', $photo));
        $response->assertRedirect();

        Storage::disk('public')->assertMissing($photo->path);
        Storage::disk('public')->assertMissing($photo->original_path);
    }

    #[Test]
    public function deleting_a_product_removes_all_associated_images()
    {
        Storage::fake('public');
        $this->signInAsAdmin();

        $product = Product::factory()->create();
        $photo1 = $product->photos()->create([
            'path' => 'products/a.jpg',
            'original_path' => 'products/originals/a.jpg',
        ]);
        $photo2 = $product->photos()->create([
            'path' => 'products/b.jpg',
            'original_path' => 'products/originals/b.jpg',
        ]);

        Storage::disk('public')->put($photo1->path, '');
        Storage::disk('public')->put($photo1->original_path, '');
        Storage::disk('public')->put($photo2->path, '');
        Storage::disk('public')->put($photo2->original_path, '');

        $response = $this->delete(route('admin.products.destroy', $product));
        $response->assertRedirect();

        Storage::disk('public')->assertMissing($photo1->path);
        Storage::disk('public')->assertMissing($photo1->original_path);
        Storage::disk('public')->assertMissing($photo2->path);
        Storage::disk('public')->assertMissing($photo2->original_path);
    }

    #[Test]
    public function deleting_a_project_removes_all_associated_images()
    {
        Storage::fake('public');
        $this->signInAsAdmin();

        $project = Project::factory()->create();
        $photo1 = $project->photos()->create([
            'path' => 'projects/a.jpg',
            'original_path' => 'projects/originals/a.jpg',
        ]);
        $photo2 = $project->photos()->create([
            'path' => 'projects/b.jpg',
            'original_path' => 'projects/originals/b.jpg',
        ]);

        Storage::disk('public')->put($photo1->path, '');
        Storage::disk('public')->put($photo1->original_path, '');
        Storage::disk('public')->put($photo2->path, '');
        Storage::disk('public')->put($photo2->original_path, '');

        $response = $this->delete(route('admin.projects.destroy', $project));
        $response->assertRedirect();

        Storage::disk('public')->assertMissing($photo1->path);
        Storage::disk('public')->assertMissing($photo1->original_path);
        Storage::disk('public')->assertMissing($photo2->path);
        Storage::disk('public')->assertMissing($photo2->original_path);
    }
}
