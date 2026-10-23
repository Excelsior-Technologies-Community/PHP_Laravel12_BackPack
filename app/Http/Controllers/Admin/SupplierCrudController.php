<?php

namespace App\Http\Controllers\Admin;

use App\Models\AuditLog;
use App\Models\Supplier;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class SupplierCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        CRUD::setModel(Supplier::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/supplier');
        CRUD::setEntityNameStrings('Supplier', 'Suppliers');
        $this->crud->setDefaultPageLength(10);
    }

    protected function setupListOperation()
    {
        CRUD::column('id')->label('ID');

        CRUD::column('name')
            ->label('Supplier Name')
            ->wrapper([
                'element' => 'strong',
            ]);

        CRUD::column('contact_person')->label('Contact Person');
        CRUD::column('email')->label('Email');
        CRUD::column('phone')->label('Phone');

        CRUD::addColumn([
            'name' => 'payment_terms',
            'label' => 'Payment Terms',
            'type' => 'closure',
            'escaped' => false,
            'function' => function ($entry) {
                return '<span class="badge bg-secondary">' . e($entry->payment_terms ?? 'Net 30') . '</span>';
            },
        ]);

        CRUD::addColumn([
            'name' => 'lead_time_days',
            'label' => 'Lead Time',
            'type' => 'closure',
            'function' => function ($entry) {
                return ($entry->lead_time_days ?? 7) . ' days';
            },
        ]);

        CRUD::addColumn([
            'name' => 'reliability_score',
            'label' => 'Reliability',
            'type' => 'closure',
            'escaped' => false,
            'function' => function ($entry) {
                $score = (float) ($entry->reliability_score ?? 5.0);
                $badgeClass = $score >= 4.5 ? 'bg-success' : ($score >= 3.5 ? 'bg-info' : 'bg-warning text-dark');
                return '<span class="badge ' . $badgeClass . '">⭐ ' . number_format($score, 1) . ' / 5.0</span>';
            },
        ]);

        CRUD::addColumn([
            'name' => 'products_count',
            'label' => 'Products Supplied',
            'type' => 'closure',
            'escaped' => false,
            'function' => function ($entry) {
                $count = $entry->products()->count();
                return '<span class="badge bg-primary">' . $count . ' Items</span>';
            },
        ]);
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation([
            'name' => 'required|min:2|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|max:50',
            'lead_time_days' => 'required|integer|min:1|max:365',
            'reliability_score' => 'required|numeric|min:1|max:5',
        ]);

        // Tab: Basic Info
        CRUD::field('name')
            ->label('Company / Supplier Name')
            ->type('text')
            ->tab('Basic Info');

        CRUD::field('contact_person')
            ->label('Contact Person')
            ->type('text')
            ->tab('Basic Info');

        CRUD::field('email')
            ->label('Email Address')
            ->type('email')
            ->tab('Basic Info');

        CRUD::field('phone')
            ->label('Phone Number')
            ->type('text')
            ->tab('Basic Info');

        CRUD::field('address')
            ->label('Warehouse / Head Office Address')
            ->type('textarea')
            ->tab('Basic Info');

        // Tab: Commercial Terms
        CRUD::field('payment_terms')
            ->label('Payment Terms')
            ->type('select_from_array')
            ->options([
                'Net 15' => 'Net 15 Days',
                'Net 30' => 'Net 30 Days',
                'Net 60' => 'Net 60 Days',
                'Advance' => '100% Advance Payment',
                'COD' => 'Cash On Delivery (COD)',
            ])
            ->default('Net 30')
            ->tab('Commercial Terms');

        CRUD::field('lead_time_days')
            ->label('Average Fulfillment Lead Time (Days)')
            ->type('number')
            ->default(7)
            ->tab('Commercial Terms');

        CRUD::field('reliability_score')
            ->label('Reliability Rating (1.0 to 5.0)')
            ->type('number')
            ->attributes(['step' => '0.1', 'min' => '1.0', 'max' => '5.0'])
            ->default(5.0)
            ->tab('Commercial Terms');

        CRUD::field('notes')
            ->label('Vendor Notes & Contract Terms')
            ->type('textarea')
            ->tab('Commercial Terms');
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    protected function setupShowOperation()
    {
        $this->crud->set('show.setFromDb', false);

        CRUD::column('id')->label('Supplier ID');
        CRUD::column('name')->label('Supplier / Vendor Name');
        CRUD::column('contact_person')->label('Contact Person');
        CRUD::column('email')->label('Email');
        CRUD::column('phone')->label('Phone');
        CRUD::column('address')->label('Address');
        CRUD::column('payment_terms')->label('Payment Terms');
        CRUD::column('lead_time_days')->label('Delivery Lead Time (Days)');
        CRUD::column('reliability_score')->label('Reliability Score (Out of 5.0)');
        CRUD::column('notes')->label('Internal Notes');
        CRUD::column('created_at')->label('Vendor Registered On');

        CRUD::addColumn([
            'name' => 'supplied_products',
            'label' => 'Products Catalogue & Stock',
            'type' => 'closure',
            'escaped' => false,
            'function' => function ($entry) {
                $products = $entry->products()->get();
                if ($products->isEmpty()) {
                    return '<div class="alert alert-info mb-0">No products mapped to this supplier yet.</div>';
                }

                $html = '<div class="table-responsive"><table class="table table-sm table-bordered mt-2">
                    <thead class="table-light">
                        <tr>
                            <th>SKU</th>
                            <th>Product Name</th>
                            <th>Price</th>
                            <th>Stock Qty</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>';

                foreach ($products as $p) {
                    $badge = $p->quantity == 0 
                        ? '<span class="badge bg-danger">Out Of Stock</span>' 
                        : ($p->quantity <= $p->restock_level ? '<span class="badge bg-warning text-dark">Low Stock</span>' : '<span class="badge bg-success">In Stock</span>');

                    $html .= '<tr>
                        <td><code>' . e($p->sku ?? 'N/A') . '</code></td>
                        <td><strong>' . e($p->name) . '</strong></td>
                        <td>₹' . number_format($p->price, 2) . '</td>
                        <td>' . $p->quantity . ' units</td>
                        <td>' . $badge . '</td>
                    </tr>';
                }

                $html .= '</tbody></table></div>';
                return $html;
            },
        ]);
    }
}
