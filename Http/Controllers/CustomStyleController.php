<?php

namespace Amplify\System\Cms\Http\Controllers;

use Amplify\System\Cms\Http\Requests\FormRequest;
use Amplify\System\Abstracts\BackpackCustomCrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;
use Illuminate\Http\Request;

/**
 * Class FormCrudController
 *
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class CustomStyleController extends BackpackCustomCrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setRoute(config('backpack.base.route_prefix') . '/custom-style');
        CRUD::setEntityNameStrings('custom-style', 'custom styles');
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
        $filePath = 'assets/css/custom.css';

        if (!file_exists(public_path($filePath))) {
            touch(public_path($filePath));
        }

        $this->crud->removeButton('create');
        $this->crud->setListContentClass('col-lg-12');
        $this->crud->setListView('backend::pages.custom-style');
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     *
     * @return void
     */
    public function store(Request $request)
    {

    }
}
