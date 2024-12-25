<?php

namespace Amplify\System\Cms\Http\Controllers;

use Amplify\System\Cms\Http\Requests\ScriptManagerRequest;
use Amplify\System\Cms\Models\ScriptManager;
use App\Abstracts\BackpackCustomCrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ScriptManagerCrudController
 *
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ScriptManagerCrudController extends BackpackCustomCrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(ScriptManager::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/script-manager');
        CRUD::setEntityNameStrings('script-manager', 'scripts');
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
        CRUD::column('position');
        CRUD::column('priority');

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */
    }

    public function setupShowOperation()
    {
        CRUD::column('id')->type('number')->thousands_sep('');
        CRUD::column('name');
        CRUD::column('position');
        CRUD::column('priority');
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
        CRUD::setValidation(ScriptManagerRequest::class);

        CRUD::field('name');

        CRUD::addField([
            'name' => 'position',
            'type' => 'select_from_array',
            'options' => ['header' => 'Header', 'footer' => 'Footer', 'google_event' => 'Goggle analytics event'],
            'allows_null' => false,
            'default' => 'header',
        ]);

        CRUD::field('priority');

        CRUD::addField([
            'name' => 'scripts',
            'label' => 'Script(s)',
            'type' => 'textarea',
        ]);

        /*        CRUD::field('scripts');*/

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
         */
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
