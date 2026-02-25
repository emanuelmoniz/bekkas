<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Role;
use App\Models\Tax;
use App\Models\ProductOptionType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminProductOptionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed locales so Locale::activeList() returns pt-PT/en-UK
        // (required for product/option translations to be saved)
        $this->seed(\Database\Seeders\LocaleSeeder::class);
    }

    #[Test]
    public function admin_can_create_product_with_option_types_and_options()
    {
        // prepare tax and user
        $tax = Tax::factory()->create(['percentage' => 23]);

        $admin = \App\Models\User::factory()->create();
        $admin->roles()->attach(Role::firstOrCreate(['name' => 'admin'])->id);

        $this->actingAs($admin);

        $payload = [
            'tax_id' => $tax->id,
            'price' => '10.00',
            'stock' => 5,
            'production_time' => 0,
            'weight' => 100,
            'width' => 10,
            'length' => 10,
            'height' => 5,
            'name' => [
                'pt-PT' => 'Produto teste',
                'en-UK' => 'Test product',
            ],
            'description' => [
                'pt-PT' => 'Descrição',
                'en-UK' => 'Description',
            ],
            'technical_info' => [
                'pt-PT' => 'Informação técnica',
                'en-UK' => 'Technical info',
            ],
            'option_types' => [
                [
                    'is_active' => 1,
                    'name' => [
                        'pt-PT' => 'Tamanho',
                        'en-UK' => 'Size',
                    ],
                    'description' => [
                        'pt-PT' => 'Tamanhos disponíveis',
                        'en-UK' => 'Available sizes',
                    ],
                    'options' => [
                        [
                            'is_active' => 1,
                            'stock' => 10,
                            'name' => [
                                'pt-PT' => 'Pequeno',
                                'en-UK' => 'Small',
                            ],
                            'description' => [
                                'pt-PT' => 'Tamanho pequeno',
                                'en-UK' => 'Small size',
                            ],
                        ],
                        [
                            'is_active' => 1,
                            'stock' => 20,
                            'name' => [
                                'pt-PT' => 'Grande',
                                'en-UK' => 'Large',
                            ],
                            'description' => [
                                'pt-PT' => 'Tamanho grande',
                                'en-UK' => 'Large size',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->post(route('admin.products.store'), $payload);

        // should be sent to the edit page for the newly created product
        $product = Product::first();
        $response->assertRedirect(route('admin.products.edit', $product));

        $this->assertDatabaseHas('products', ['price' => '10.00']);

        $product = Product::first();
        $this->assertNotNull($product);

        // make sure translations were saved correctly
        $ptTrans = $product->translations()->where('locale', 'pt-PT')->first();
        $this->assertEquals('Descrição', $ptTrans->description);
        $this->assertEquals('Informação técnica', $ptTrans->technical_info);

        $this->assertCount(1, $product->optionTypes);
        $type = $product->optionTypes->first();
        $this->assertEquals('Tamanho', $type->translation('pt-PT')->name);

        $this->assertCount(2, $type->options);
        $option = $type->options()->where('stock', 10)->first();
        $this->assertEquals('Pequeno', $option->translation('pt-PT')->name);
    }

    #[Test]
    public function admin_can_create_option_without_stock_and_it_defaults_to_zero()
    {
        $tax = Tax::factory()->create(['percentage' => 23]);
        $admin = \App\Models\User::factory()->create();
        $admin->roles()->attach(Role::firstOrCreate(['name' => 'admin'])->id);
        $this->actingAs($admin);

        // omit the stock value for one of the options (blank string simulates form input)
        $payload = [
            'tax_id' => $tax->id,
            'price' => '10.00',
            'stock' => 5,
            'production_time' => 0,
            'weight' => 100,
            'width' => 10,
            'length' => 10,
            'height' => 5,
            'name' => [
                'pt-PT' => 'Produto teste',
                'en-UK' => 'Test product',
            ],
            'option_types' => [
                [
                    'is_active' => 1,
                    'name' => [
                        'pt-PT' => 'Tamanho',
                        'en-UK' => 'Size',
                    ],
                    'options' => [
                        [
                            'is_active' => 1,
                            // stock missing entirely – controller should treat as zero
                            'name' => [
                                'pt-PT' => 'Pequeno',
                                'en-UK' => 'Small',
                            ],
                        ],
                        [
                            'is_active' => 1,
                            'stock' => '', // blank string from form
                            'name' => [
                                'pt-PT' => 'Grande',
                                'en-UK' => 'Large',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->post(route('admin.products.store'), $payload);
        $response->assertRedirect();

        $product = Product::first();
        $this->assertNotNull($product);
        $type = $product->optionTypes->first();
        $this->assertCount(2, $type->options);

        // both options should exist and have zero stock; verify via translation
        $options = $type->options()->get();
        $this->assertCount(2, $options);
        $this->assertEquals(0, $options[0]->stock);
        $this->assertEquals(0, $options[1]->stock);
        $this->assertEquals('Pequeno', $options[0]->translation('pt-PT')->name);
        $this->assertEquals('Grande', $options[1]->translation('pt-PT')->name);
    }

    #[Test]
    public function admin_can_update_product_and_add_option_without_stock()
    {
        $tax = Tax::factory()->create(['percentage' => 23]);
        $admin = \App\Models\User::factory()->create();
        $admin->roles()->attach(Role::firstOrCreate(['name' => 'admin'])->id);
        $this->actingAs($admin);

        // create initial product without options
        $product = Product::create([
            'tax_id' => $tax->id,
            'price' => '5.00',
            'stock' => 1,
            'production_time' => 0,
            'weight' => 50,
        ]);

        // now attempt to update by adding a second option with blank stock
        $payload = [
            'tax_id' => $tax->id,
            'price' => '5.00',
            'stock' => 1,
            'production_time' => 0,
            'weight' => 50,
            'name' => [ 'pt-PT' => 'Produto existente', 'en-UK' => 'Existing product' ],
            'option_types' => [
                [
                    'is_active' => 1,
                    'name' => [ 'pt-PT' => 'Tipo 1', 'en-UK' => 'Type 1' ],
                    'options' => [
                        [
                            'is_active' => 1,
                            'stock' => '',
                            'name' => [ 'pt-PT' => 'Novo', 'en-UK' => 'New' ],
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->put(route('admin.products.update', $product), $payload);
        $response->assertRedirect();

        $this->assertEquals(1, $product->fresh()->optionTypes->first()->options()->count());
        $newOpt = $product->fresh()->optionTypes->first()->options()->first();
        $this->assertEquals(0, $newOpt->stock);
        $this->assertEquals('Novo', $newOpt->translation('pt-PT')->name);
    }

    #[Test]
    public function admin_sees_errors_when_required_fields_missing()
    {
        $tax = Tax::factory()->create(['percentage' => 23]);
        $admin = \App\Models\User::factory()->create();
        $admin->roles()->attach(Role::firstOrCreate(['name' => 'admin'])->id);
        $this->actingAs($admin);

        // submit without price and tax
        $response = $this->post(route('admin.products.store'), [
            'stock' => 1,
            'weight' => 10,
            'production_time' => 0,
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['tax_id', 'price']);
    }
}
