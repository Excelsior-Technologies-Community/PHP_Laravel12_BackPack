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
}