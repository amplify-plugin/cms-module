<?php

namespace Amplify\System\Cms\Http\Controllers;

use App\Abstracts\BackpackCustomCrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class FormResponseCrudController
 *
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class FormResponseCrudController extends BackpackCustomCrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\Amplify\System\Cms\Models\FormResponse::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/form-response');
        CRUD::setEntityNameStrings('form-response', 'form responses');
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
        CRUD::addColumns([
            [
                'name' => 'form.name',
                'type' => 'text',
                'label' => 'Form',
            ],
            [
                'name' => 'contact.name',
                'type' => 'text',
                'label' => 'Submitted By',
            ],
            [
                'name' => 'created_at',
                'type' => 'datetime',
                'label' => 'Submitted At',
            ],
        ]);
    }

    protected function setupShowOperation()
    {
        CRUD::addColumns([
            [
                'name' => 'form.name',
                'type' => 'text',
                'label' => 'Form',
            ],
            [
                'name' => 'contact.name',
                'type' => 'text',
                'label' => 'Submitted By',
            ],
            [
                'name' => 'created_at',
                'type' => 'datetime',
                'label' => 'Submitted At',
            ],
            [
                'name' => 'response',
                'type' => 'table',
                'label' => 'Response',
                'columns' => [
                    'field' => 'Field',
                    'value' => 'Value',
                ],
            ],
        ]);
    }
}
