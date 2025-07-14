<?php

namespace Amplify\System\Cms\Http\Controllers;

use Amplify\System\Cms\Http\Requests\ContentCategoryRequest;
use Amplify\System\Cms\Models\ContentCategory;
use Amplify\System\Abstracts\BackpackCustomCrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\Pro\Http\Controllers\Operations\InlineCreateOperation;

/**
 * Class ContentCategoryCrudController
 *
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ContentCategoryCrudController extends BackpackCustomCrudController
{
    use \App\Http\Controllers\Backpack\Operations\SlugOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use InlineCreateOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(ContentCategory::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/content-category');
        CRUD::setEntityNameStrings('content-category', 'Content categories');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     *
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('id')->type('number')->thousands_sep('');
        CRUD::column('name');
        CRUD::column('slug');

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     *
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(ContentCategoryRequest::class);

        CRUD::field('name');
        CRUD::addField([
            'name' => 'slug',
            'target' => 'name',
            'label' => 'Slug',
            'type' => 'slug',
        ]);

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
         */
    }

    public function setupShowOperation()
    {
        CRUD::column('id')->type('number')->thousands_sep('');
        CRUD::column('name');
        CRUD::column('slug');
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     *
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
