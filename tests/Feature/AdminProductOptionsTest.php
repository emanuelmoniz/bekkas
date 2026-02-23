<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Role;
use App\Models\Tax;
use App\Models\ProductOptionType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProductOptionsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
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

    /** @test */
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
