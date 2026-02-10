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
    use \Backpack\CRUD\app\Http\Controllers\Operations\BulkDeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    // In setup() method
    public function setup()
    {
        CRUD::setModel(Product::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/product');
        CRUD::setEntityNameStrings('product', 'products');
    }

    protected function setupListOperation()
    {
        CRUD::column('id');
        CRUD::column('image')
            ->type('image')
            ->value(fn($value) => $value ?: 'https://placehold.co/50x50')
            ->label('Image');
        CRUD::column('name')
            ->type('text');
        CRUD::column('category')
            ->type('relationship')
            ->attribute('name')
            ->label('Category');
        CRUD::column('price')
            ->type('number')
            ->prefix('$')
            ->decimals(2);
        CRUD::column('quantity')
            ->type('number')
            ->label('Qty');
        CRUD::column('is_available')
            ->type('boolean')
            ->label('Available')
            ->options([
                0 => 'No',
                1 => 'Yes'
            ]);
        CRUD::column('created_at')
            ->type('datetime');

        CRUD::addFilter(
            [
                'type' => 'simple',
                'name' => 'in_stock',
                'label' => 'In Stock'
            ],
            false,
            function () {
                CRUD::addClause('where', 'quantity', '>', 0);
            }
        );

        CRUD::addFilter(
            [
                'type' => 'dropdown',
                'name' => 'category_id',
                'label' => 'Category'
            ],
            function () {
                return Category::all()->pluck('name', 'id')->toArray();
            },
            function ($value) {
                CRUD::addClause('where', 'category_id', $value);
            }
        );
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation([
            'name' => 'required|min:2|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
        ]);

        CRUD::field('name')
            ->type('text')
            ->label('Product Name');

        CRUD::field('category_id')
            ->type('select2')
            ->label('Category')
            ->entity('category')
            ->attribute('name')
            ->model(Category::class);

        CRUD::field('description')
            ->type('textarea')
            ->label('Description');

        CRUD::field('price')
            ->type('number')
            ->prefix('$')
            ->attributes(['step' => '0.01'])
            ->label('Price');

        CRUD::field('quantity')
            ->type('number')
            ->label('Quantity');

        CRUD::field('image')
            ->type('upload')
            ->withFiles([
                'disk' => 'public',
                'path' => 'products',
            ])
            ->label('Product Image');

        CRUD::field('is_available')
            ->type('checkbox')
            ->label('Available for Sale')
            ->default(true);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();

        // Add custom button
        $this->crud->addButtonFromView('line', 'preview', 'preview_product', 'beginning');
    }

    protected function setupShowOperation()
    {
        CRUD::column('image')
            ->type('image')
            ->value(fn($value) => $value ?: 'https://placehold.co/300x300')
            ->label('Image');
        CRUD::column('name');
        CRUD::column('category')
            ->type('relationship')
            ->attribute('name');
        CRUD::column('description')
            ->type('textarea');
        CRUD::column('price')
            ->type('number')
            ->prefix('$')
            ->decimals(2);
        CRUD::column('quantity');
        CRUD::column('is_available')
            ->type('boolean')
            ->label('Available');
        CRUD::column('created_at')
            ->type('datetime');
        CRUD::column('updated_at')
            ->type('datetime');
    }


    // After setup()
    protected function setupExportOperation()
    {
        CRUD::setExportColumns([
            'id',
            'name',
            'category.name' => 'Category',
            'price',
            'quantity',
            'is_available' => 'Available',
            'created_at',
        ]);
    }
    // In ProductCrudController
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;





    // Create custom method
    public function preview($id)
    {
        $product = Product::findOrFail($id);
        return view('admin.product-preview', compact('product'));
    }
}