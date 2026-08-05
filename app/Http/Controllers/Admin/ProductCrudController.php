<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\Category;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class ProductCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        CRUD::setModel(Product::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/product');
        CRUD::setEntityNameStrings('product', 'products');
    }

    protected function setupListOperation()
    {
        /*
        |--------------------------------------------------------------------------
        | Filters
        |--------------------------------------------------------------------------
        */

        // CRUD::filter('low_stock')
        //     ->type('simple')
        //     ->label('Low Stock')
        //     ->whenActive(function () {
        //         CRUD::addClause('whereColumn', 'quantity', '<=', 'restock_level');
        //         CRUD::addClause('where', 'quantity', '>', 0);
        //     });

        /*
        |--------------------------------------------------------------------------
        | Columns
        |--------------------------------------------------------------------------
        */

        CRUD::column('id');

        CRUD::column('image')
            ->type('image')
            ->disk('public')
            ->label('Image')
            ->height('50px')
            ->width('50px');

        CRUD::column('name');

        CRUD::column('category')
            ->type('relationship')
            ->attribute('name')
            ->label('Category');

        CRUD::column('price')
            ->type('number')
            ->prefix('₹')
            ->decimals(2);

        CRUD::column('quantity')
            ->label('Quantity');

        CRUD::column('restock_level')
            ->label('Restock Level');

        CRUD::addColumn([
            'name' => 'stock_status',
            'label' => 'Stock Status',
            'type' => 'closure',
            'function' => function ($entry) {

                if ($entry->quantity == 0) {
                    return '<span class="badge bg-danger">Out of Stock</span>';
                }

                if ($entry->quantity <= $entry->restock_level) {
                    return '<span class="badge bg-warning">Low Stock</span>';
                }

                return '<span class="badge bg-success">In Stock</span>';
            },
            'escaped' => false,
        ]);

        CRUD::column('is_available')
            ->type('boolean')
            ->label('Available');

        CRUD::column('created_at')
            ->type('datetime');
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation([
            'name' => 'required|min:2|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'restock_level' => 'required|integer|min:1',
        ]);

        CRUD::field('name')
            ->type('text')
            ->label('Product Name');

        CRUD::field('category_id')
            ->type('select')
            ->entity('category')
            ->attribute('name')
            ->model(Category::class)
            ->label('Category');

        CRUD::field('description')
            ->type('textarea')
            ->label('Description');

        CRUD::field('price')
            ->type('number')
            ->attributes([
                'step' => '0.01'
            ])
            ->label('Price');

        CRUD::field('quantity')
            ->type('number')
            ->label('Quantity');

        CRUD::field('restock_level')
            ->type('number')
            ->default(10)
            ->label('Restock Level');

        CRUD::field('image')
            ->type('upload')
            ->withFiles([
                'disk' => 'public',
                'path' => 'products',
            ])
            ->label('Product Image');

        CRUD::field('is_available')
            ->type('checkbox')
            ->default(true)
            ->label('Available for Sale');
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    protected function setupShowOperation()
    {
        CRUD::column('image')
            ->type('image')
            ->disk('public')
            ->label('Image')
            ->height('300px')
            ->width('300px');

        CRUD::column('name');

        CRUD::column('category')
            ->type('relationship')
            ->attribute('name');

        CRUD::column('description');

        CRUD::column('price')
            ->type('number')
            ->prefix('₹')
            ->decimals(2);

        CRUD::column('quantity');

        CRUD::column('restock_level');

        CRUD::addColumn([
            'name' => 'stock_status',
            'label' => 'Stock Status',
            'type' => 'closure',
            'function' => function ($entry) {

                if ($entry->quantity == 0) {
                    return '<span class="badge bg-danger">Out of Stock</span>';
                }

                if ($entry->quantity <= $entry->restock_level) {
                    return '<span class="badge bg-warning">Low Stock</span>';
                }

                return '<span class="badge bg-success">In Stock</span>';
            },
            'escaped' => false,
        ]);

        CRUD::column('is_available')
            ->type('boolean')
            ->label('Available');

        CRUD::column('created_at')
            ->type('datetime');

        CRUD::column('updated_at')
            ->type('datetime');
    }
}
