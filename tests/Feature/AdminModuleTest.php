<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Table;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $kasir;

    protected User $owner;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin@bradercafe.com'],
            [
                'name' => 'Admin Test',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]
        );

        $this->kasir = User::firstOrCreate(
            ['email' => 'kasir@bradercafe.com'],
            [
                'name' => 'Kasir Test',
                'password' => bcrypt('password'),
                'role' => 'kasir',
            ]
        );

        $this->owner = User::firstOrCreate(
            ['email' => 'owner@bradercafe.com'],
            [
                'name' => 'Owner Test',
                'password' => bcrypt('password'),
                'role' => 'owner',
            ]
        );
    }

    public function test_guest_is_redirected_to_login_when_accessing_admin(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/login');
    }

    public function test_non_admin_cannot_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->kasir)->get('/admin');
        $response->assertStatus(403);
    }

    public function test_kasir_can_access_cashier_station(): void
    {
        $response = $this->actingAs($this->kasir)->get('/admin/cashier');
        $response->assertStatus(200);
        $response->assertSee('Stasiun Kasir');
    }

    public function test_kasir_login_redirects_to_cashier_station(): void
    {
        $response = $this->post('/login', [
            'email' => 'kasir@bradercafe.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.cashier.index'));
    }

    public function test_kasir_visiting_home_redirects_to_cashier_station(): void
    {
        $response = $this->actingAs($this->kasir)->get('/');
        $response->assertRedirect(route('admin.cashier.index'));
    }

    public function test_owner_can_access_owner_portal(): void
    {
        $response = $this->actingAs($this->owner)->get('/admin/owner');
        $response->assertStatus(200);
        $response->assertSee('Portal Pemilik');
    }

    public function test_owner_login_redirects_to_owner_portal(): void
    {
        $response = $this->post('/login', [
            'email' => 'owner@bradercafe.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.owner.index'));
    }

    public function test_owner_visiting_home_redirects_to_owner_portal(): void
    {
        $response = $this->actingAs($this->owner)->get('/');
        $response->assertRedirect(route('admin.owner.index'));
    }

    public function test_owner_visiting_admin_redirects_to_owner_portal(): void
    {
        $response = $this->actingAs($this->owner)->get('/admin');
        $response->assertRedirect(route('admin.owner.index'));
    }

    public function test_kasir_cannot_access_owner_portal(): void
    {
        $response = $this->actingAs($this->kasir)->get('/admin/owner');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_owner_portal(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/owner');
        $response->assertStatus(200);
        $response->assertSee('Portal Pemilik');
    }

    public function test_admin_can_access_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('Dashboard Utama');
    }

    public function test_admin_can_view_and_create_category(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/categories');
        $response->assertStatus(200);

        $postResponse = $this->actingAs($this->admin)->post('/admin/categories', [
            'name' => 'Kategori Baru Test',
        ]);
        $postResponse->assertRedirect('/admin/categories');
        $this->assertDatabaseHas('categories', ['name' => 'Kategori Baru Test']);
    }

    public function test_admin_can_create_menu_with_variants_and_image(): void
    {
        Storage::fake('public');

        $category = Category::first() ?? Category::create(['name' => 'Kopi', 'slug' => 'kopi']);

        $image = UploadedFile::fake()->image('coffee.jpg');

        $response = $this->actingAs($this->admin)->post('/admin/menus', [
            'category_id' => $category->id,
            'name' => 'Kopi Gula Aren Super',
            'description' => 'Enak dan mantap',
            'price' => 22000,
            'is_available' => 1,
            'image' => $image,
            'variants' => [
                ['name' => 'Extra Shot', 'extra_price' => 5000],
                ['name' => 'Less Sugar', 'extra_price' => 0],
            ],
        ]);

        $response->assertRedirect('/admin/menus');
        $this->assertDatabaseHas('menus', ['name' => 'Kopi Gula Aren Super']);
        $this->assertDatabaseHas('menu_variants', ['name' => 'Extra Shot', 'extra_price' => 5000]);
    }

    public function test_admin_can_create_table_with_auto_generated_token(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/tables', [
            'table_number' => 'Meja Test 99',
            'is_active' => 1,
        ]);

        $response->assertRedirect('/admin/tables');
        $table = Table::where('table_number', 'Meja Test 99')->first();
        $this->assertNotNull($table);
        $this->assertNotEmpty($table->token);
    }

    public function test_admin_can_view_table_print_and_download_qr(): void
    {
        $table = Table::first() ?? Table::create([
            'table_number' => 'Meja 01',
            'token' => 'testtoken123456',
            'is_active' => true,
        ]);

        $printResponse = $this->actingAs($this->admin)->get("/admin/tables/{$table->id}/print");
        $printResponse->assertStatus(200);
        $printResponse->assertSee($table->table_number);

        $downloadResponse = $this->actingAs($this->admin)->get("/admin/tables/{$table->id}/download-qr");
        $downloadResponse->assertStatus(200);
        $downloadResponse->assertHeader('content-type', 'image/svg+xml');
    }

    public function test_customer_order_url_works(): void
    {
        $table = Table::create([
            'table_number' => 'Meja 05',
            'token' => 'tokenabc12345678',
            'is_active' => true,
        ]);

        $response = $this->get("/order/{$table->token}");
        $response->assertStatus(200);
        $response->assertSee($table->table_number);
    }

    public function test_invalid_table_token_shows_error(): void
    {
        $response = $this->get('/order/invalid-token-xyz');
        $response->assertStatus(200);
        $response->assertSee('Meja Tidak Dapat Diakses');
    }
}
