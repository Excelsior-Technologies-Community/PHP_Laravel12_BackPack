<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\Supplier;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BackpackSuiteTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);

        $this->admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin User', 'password' => bcrypt('password')]
        );
    }

    protected function actingAsAdmin()
    {
        return $this->actingAs($this->admin, backpack_guard_name());
    }

    public function test_dashboard_renders_with_chart_data_and_financial_analytics(): void
    {
        $response = $this->actingAsAdmin()->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Supply Chain Intelligence Studio');
        $response->assertSee('Total Asset Valuation');
        $response->assertSee('Category Capital Distribution');
        $response->assertSee('Top 5 High-Value Inventory Lines');
        $response->assertSee('Automated Reorder Watchdog');
    }

    public function test_supplier_crud_operations(): void
    {
        // 1. List
        $response = $this->actingAsAdmin()->get(backpack_url('supplier'));
        $response->assertStatus(200);

        // 2. Create
        $response = $this->actingAsAdmin()->post(backpack_url('supplier'), [
            'name' => 'Quantum Tech Components',
            'contact_person' => 'Arun Joshi',
            'email' => 'arun@quantumtech.io',
            'phone' => '+91 98765 00000',
            'payment_terms' => 'Net 30',
            'lead_time_days' => 5,
            'reliability_score' => 4.8,
        ]);
        $response->assertStatus(302);

        $supplier = Supplier::where('email', 'arun@quantumtech.io')->first();
        $this->assertNotNull($supplier);
        $this->assertEquals('Quantum Tech Components', $supplier->name);

        // 3. Show
        $showResponse = $this->actingAsAdmin()->get(backpack_url('supplier/' . $supplier->id . '/show'));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Quantum Tech Components');
    }

    public function test_quick_stock_adjustment_updates_stock_and_logs_audit(): void
    {
        $product = Product::first();
        $initialQty = $product->quantity;

        // Perform Stock In (+10)
        $response = $this->actingAsAdmin()
            ->from(backpack_url('product'))
            ->post(route('product.quick.restock', $product->id), [
                'adjustment_type' => 'stock_in',
                'quantity' => 10,
                'reason' => 'Test Restock Shipment Inbound',
            ]);

        $response->assertRedirect(backpack_url('product'));
        $product->refresh();
        $this->assertEquals($initialQty + 10, $product->quantity);

        // Verify StockAdjustment record
        $adj = StockAdjustment::where('product_id', $product->id)->latest()->first();
        $this->assertNotNull($adj);
        $this->assertEquals('stock_in', $adj->type);
        $this->assertEquals(10, $adj->quantity_change);
        $this->assertEquals($product->quantity, $adj->quantity_after);

        // Verify AuditLog record
        $audit = AuditLog::where('auditable_type', Product::class)
            ->where('auditable_id', $product->id)
            ->where('action', 'STOCK_ADJUSTED')
            ->latest()
            ->first();

        $this->assertNotNull($audit);
        $this->assertStringContainsString('STOCK_ADJUSTED', $audit->action);
    }

    public function test_purchase_order_generator_and_csv_export(): void
    {
        // 1. PO Draft view
        $response = $this->actingAsAdmin()->get(route('admin.purchase-order.generate'));
        $response->assertStatus(200);
        $response->assertSee('PURCHASE ORDER');
        $response->assertSee('PO-');

        // 2. PO CSV export
        $csvResponse = $this->actingAsAdmin()->get(route('admin.purchase-order.csv'));
        $csvResponse->assertStatus(200);
        $this->assertStringContainsString('text/csv', (string) $csvResponse->headers->get('Content-Type'));
    }

    public function test_barcode_and_qr_printable_sheet(): void
    {
        $product = Product::first();

        // 1. Single product thermal sheet
        $response = $this->actingAsAdmin()->get(route('product.barcode.sheet', ['id' => $product->id]) . '?format=thermal');
        $response->assertStatus(200);
        $response->assertSee('JsBarcode');
        $response->assertSee('thermal-label');

        // 2. All products A4 grid sheet
        $gridResponse = $this->actingAsAdmin()->get(route('product.barcode.sheet') . '?format=a4');
        $gridResponse->assertStatus(200);
        $gridResponse->assertSee('a4-grid');

        // 3. Product preview with barcode and QR
        $previewResponse = $this->actingAsAdmin()->get(route('product.preview', $product->id));
        $previewResponse->assertStatus(200);
        $previewResponse->assertSee('previewBarcodeSvg');
        $previewResponse->assertSee('previewQrBox');
    }

    public function test_audit_log_crud_and_diff_viewer(): void
    {
        $log = AuditLog::latest()->first();

        $listResponse = $this->actingAsAdmin()->get(backpack_url('audit-log'));
        $listResponse->assertStatus(200);

        if ($log) {
            $showResponse = $this->actingAsAdmin()->get(backpack_url('audit-log/' . $log->id . '/show'));
            $showResponse->assertStatus(200);
            $showResponse->assertSee('Diff Ledger');
        }
    }
}
