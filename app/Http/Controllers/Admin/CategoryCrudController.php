<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class CategoryCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        CRUD::setModel(Category::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/category');
        CRUD::setEntityNameStrings('category', 'categories');
    }

    protected function setupListOperation()
    {
        CRUD::column('id');
        CRUD::column('name')
            ->type('text')
            ->label('Category Name');
        CRUD::column('description')
            ->type('text')
            ->limit(50);
        CRUD::column('is_active')
            ->type('boolean')
            ->label('Active')
            ->options([
                0 => 'Inactive',
                1 => 'Active'
            ]);
        CRUD::column('created_at')
            ->type('datetime');
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation([
            'name' => 'required|min:2|max:255',
            'description' => 'nullable|string',
        ]);

        CRUD::field('name')
            ->type('text')
            ->label('Category Name');

        CRUD::field('description')
            ->type('textarea')
            ->label('Description');

        CRUD::field('is_active')
            ->type('checkbox')
            ->label('Active Category')
            ->default(true);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}