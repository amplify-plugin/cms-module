<?php

namespace Amplify\System\Cms\Http\Controllers;

use Amplify\System\Cms\Http\Requests\BannerZoneRequest;
use Amplify\System\Cms\Models\BannerZone;
use App\Abstracts\BackpackCustomCrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class BannerZoneCrudController
 *
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class BannerZoneCrudController extends BackpackCustomCrudController
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
        CRUD::setModel(BannerZone::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/banner-zone');
        CRUD::setEntityNameStrings('banner-zone', 'banner zones');
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
        CRUD::column('code');
        CRUD::column('fetch_data_from_easyask')->label('Fetch From EasyAsk')->type('boolean');
    }

    public function setupShowOperation()
    {
        CRUD::column('id')->type('number')->thousands_sep('');
        CRUD::column('name');
        CRUD::column('code');
        CRUD::column('fetch_data_from_easyask')->label('Fetch From EasyAsk')->type('boolean');
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
        CRUD::setValidation(BannerZoneRequest::class);

        CRUD::field('name');
        CRUD::field('code');
        CRUD::field('fetch_data_from_easyask')->type('boolean');

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
