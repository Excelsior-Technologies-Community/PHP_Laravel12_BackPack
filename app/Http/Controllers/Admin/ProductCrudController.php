<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\Category;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class ProductCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    /**
     * Setup CRUD
     */
    public function setup()
    {
        CRUD::setModel(Product::class);

        CRUD::setRoute(config('backpack.base.route_prefix') . '/product');

        CRUD::setEntityNameStrings('Product', 'Products');

        // Pagination
        $this->crud->setDefaultPageLength(5);

        $this->crud->setPageLengthMenu([10, 25, 50, 100]);

        // Default order
        $this->crud->orderBy('id', 'ASC');
    }

    /**
     * List Operation
     */
    protected function setupListOperation()
    {

        /*
        |--------------------------------------------------------------------------
        | Columns
        |--------------------------------------------------------------------------
        */

        CRUD::column('id')
            ->label('ID');



        CRUD::column('image')
            ->type('image')
            ->disk('public')
            ->height('60px')
            ->width('60px')
            ->label('Image');



        CRUD::column('name')
            ->label('Product Name');



        CRUD::column('category')
            ->type('relationship')
            ->attribute('name')
            ->label('Category');



        CRUD::column('price')
            ->type('number')
            ->prefix('₹')
            ->decimals(2);



        CRUD::column('quantity');



        CRUD::column('restock_level')
            ->label('Restock');



        CRUD::addColumn([
            'name' => 'stock_status',
            'label' => 'Stock Status',
            'type' => 'closure',
            'escaped' => false,

            'function' => function ($entry) {

                if ($entry->quantity == 0) {

                    return '<span class="badge bg-danger">
            Out Of Stock
            </span>';
                } elseif ($entry->quantity <= $entry->restock_level) {

                    return '<span class="badge bg-warning text-dark">
            Low Stock
            </span>';
                }

                return '<span class="badge bg-success">
        In Stock
        </span>';
            }
        ]);



        CRUD::column('is_available')
            ->type('boolean')
            ->label('Available');



        CRUD::column('created_at')
            ->type('datetime')
            ->label('Created');



        CRUD::column('updated_at')
            ->type('datetime')
            ->label('Updated');



        /*
        |--------------------------------------------------------------------------
        | Buttons
        |--------------------------------------------------------------------------
        */

        
        $this->crud->addButtonFromView(
            'line',
            'preview',
            'preview_product',
            'beginning'
        );
    }

    /**
|--------------------------------------------------------------------------
| Create Operation
|--------------------------------------------------------------------------
     */

    protected function setupCreateOperation()
    {
        CRUD::setValidation([

            'name' => 'required|min:2|max:255',

            'category_id' => 'required|exists:categories,id',

            'price' => 'required|numeric|min:0',

            'quantity' => 'required|integer|min:0',

            'restock_level' => 'required|integer|min:1',

            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

        ]);


        CRUD::field([

            'name' => 'name',

            'label' => 'Product Name',

            'type' => 'text',

            'tab' => 'Basic Details'

        ]);


        CRUD::field([

            'name' => 'category_id',

            'label' => 'Category',

            'type' => 'select',

            'entity' => 'category',

            'model' => \App\Models\Category::class,

            'attribute' => 'name',

            'tab' => 'Basic Details'

        ]);


        CRUD::field([

            'name' => 'description',

            'label' => 'Description',

            'type' => 'textarea',

            'tab' => 'Basic Details'

        ]);


        CRUD::field([

            'name' => 'price',

            'label' => 'Price',

            'type' => 'number',

            'attributes' => [

                'step' => '0.01',

                'min' => 0

            ],

            'tab' => 'Inventory'

        ]);


        CRUD::field([

            'name' => 'quantity',

            'label' => 'Quantity',

            'type' => 'number',

            'tab' => 'Inventory'

        ]);


        CRUD::field([

            'name' => 'restock_level',

            'label' => 'Restock Level',

            'type' => 'number',

            'default' => 10,

            'tab' => 'Inventory'

        ]);


        CRUD::field([

            'name' => 'image',

            'label' => 'Product Image',

            'type' => 'upload',

            'withFiles' => [

                'disk' => 'public',

                'path' => 'products'

            ],

            'tab' => 'Media'

        ]);


        CRUD::field([

            'name' => 'is_available',

            'label' => 'Available',

            'type' => 'checkbox',

            'default' => true,

            'tab' => 'Status'

        ]);
    }


    /**
|--------------------------------------------------------------------------
| Update Operation
|--------------------------------------------------------------------------
     */

    protected function setupUpdateOperation()
    {

        $this->setupCreateOperation();

        // Preview Button
        $this->crud->addButtonFromView(
            'line',
            'preview',
            'preview_product',
            'beginning'
        );
    }

    public function store()
    {

        $response = $this->traitStore();

        \App\Models\ProductActivity::create([

            'product_id' => $this->crud->entry->id,

            'action' => 'Created',

            'description' => auth()->user()->name .
                ' created product "' .
                $this->crud->entry->name . '"'

        ]);

        return $response;
    }



    public function update()
    {

        $response = $this->traitUpdate();

        \App\Models\ProductActivity::create([

            'product_id' => $this->crud->entry->id,

            'action' => 'Updated',

            'description' => auth()->user()->name .
                ' updated product "' .
                $this->crud->entry->name . '"'

        ]);

        return $response;
    }

    public function preview($id)
    {
        $product = Product::with('category')->findOrFail($id);

        return view('admin.product-preview', compact('product'));
    }

    public function exportCsv(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=products.csv',
        ];

        $callback = function () {

            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'ID',
                'Name',
                'Category',
                'Price',
                'Quantity',
                'Available'
            ]);

            Product::with('category')->get()->each(function ($product) use ($file) {

                fputcsv($file, [

                    $product->id,

                    $product->name,

                    optional($product->category)->name,

                    $product->price,

                    $product->quantity,

                    $product->is_available ? 'Yes' : 'No',

                ]);
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
