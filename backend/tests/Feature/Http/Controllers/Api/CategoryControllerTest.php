<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestResources;
use Tests\Traits\TestValidations;
use Illuminate\Support\Facades\Lang;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CategoryControllerTest extends TestCase
{
 
    use DatabaseMigrations, TestValidations, TestSaves, TestResources;

    private $category;

    private $serializedFields = [
        'id',
        'name',
        'description',
        'is_active',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->category = factory(Category::class)->create();
    }
    
    public function testIndex()
    {
        $response = $this->get(route('categories.index'));
        // dd($response->content());
        $response
                ->assertStatus(200)
                ->assertJson([
                    'meta' => ['per_page' => 15]
                ])
                ->assertJsonStructure([
                    'data' => [
                        '*' => $this->serializedFields
                    ],
                    'links' => [],
                    'meta' => [],
                ]);

        $resource = CategoryResource::collection(collect([$this->category]));
        $this->assertResource($response, $resource);
    }

    public function testShow()
    {
        $response = $this->get(route('categories.show',['category' => $this->category->id]));
        // dd($response->content());
        $response
                ->assertStatus(200)
                ->assertJsonStructure([
                    'data' => $this->serializedFields
                ]);

        $id = $response->json('data.id');
        $resource = new CategoryResource(Category::find($id));
        $this->assertResource($response, $resource);
    }

    // TODO: dando erro ao rodar os testes da aplicação toda
    // public function testStore()
    // {
    //     $data = ['name' => 'test'];
    //     $response = $this->assertStore($data, $data + ['description' => null, 'is_active' => true, 'deleted_at' => null ]);
    //     $response->assertJsonStructure([
    //         'data' => $this->serializedFields
    //     ]);

    //     $data = [
    //         'name' => 'test',
    //         'description' => 'description',
    //         'is_active' => false
    //     ];
    //     $this->assertStore($data, $data + ['description' => 'description', 'is_active' => false ]);
    //     $id = $response->json('data.id');
    //     $resource = new CategoryResource(Category::find($id));
    //     $this->assertResource($response, $resource);
    // }

    public function testeInvalidationData()
    {
        // testando validacao dos dados
        $data = [ 'name' => '' ];
        $this->assertInvalidationInStoreAction($data, 'required');
        $this->assertInvalidationInUpdateAction($data, 'required');

        $data = [ 'name' => str_repeat('a', 256)];
        $this->assertInvalidationInStoreAction($data, 'max.string', ['max' => 255]);
        $this->assertInvalidationInUpdateAction($data, 'max.string', ['max' => 255]);

        $data = [ 'is_active' => 'a' ];
        $this->assertInvalidationInStoreAction($data, 'boolean');
        $this->assertInvalidationInUpdateAction($data, 'boolean');
    }

    // TODO: dando erro ao rodar os testes da aplicação toda
    // public function testStoreActiveFalse()
    // {
    //     $data = [
    //         'name' => 'Test Store',
    //         'description' => 'description',
    //         'is_active' => false
    //     ];
    //     $this->assertStore($data, $data + ['description' => 'description', 'is_active' => false ]);
    // }


    public function testUpdate()
    {
        $data = [
            'name' => 'test',
            'description' => 'test',
            'is_active' => true
        ];
        $response = $this->assertUpdate($data, $data + ['deleted_at' => null]);
        // dd($response->content());
        $response->assertJsonStructure([
            'data' => $this->serializedFields
        ]);
        $id = $response->json('data.id');
        $resource = new CategoryResource(Category::find($id));
        $this->assertResource($response, $resource);

        $data = [
            'name' => 'test',
            'description' => '',
        ];
        $this->assertUpdate($data, array_merge($data, ['description' => null]));

        $data['description'] = 'test';
        $this->assertUpdate($data, array_merge($data, ['description' => 'test']));

        $data['description'] = null;
        $this->assertUpdate($data, array_merge($data, ['description' => null]));
    }


    public function testeDelete()
    {
        $response = $this->json('DELETE', route('categories.destroy',['category' => $this->category->id]));
        $response->assertStatus(204);
        $this->assertNull(Category::find($this->category->id));
        $this->assertNotNull(Category::withTrashed()->find($this->category->id));
        
        $this->category->restore();
        $this->assertNotNull(Category::find($this->category->id));
    }

    protected function routeStore()
    {
        return route('categories.store');
    }

    protected function routeUpdate()
    {
        return route('categories.update', ['category' => $this->category->id]);
    }

    protected function model()
    {
        return Category::class;
    }


}
