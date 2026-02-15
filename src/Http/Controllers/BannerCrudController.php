<?php

namespace Amplify\System\Cms\Http\Controllers;

use Amplify\System\Cms\Http\Requests\BannerRequest;
use Amplify\System\Cms\Models\Banner;
use Amplify\System\Cms\Models\BannerZone;
use Amplify\System\Abstracts\BackpackCustomCrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Cache;

/**
 * Class BannerCrudController
 *
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class BannerCrudController extends BackpackCustomCrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\FetchOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(Banner::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/banner');
        CRUD::setEntityNameStrings('banner', 'banner items');
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

        CRUD::addFilter(
            [
                'name' => 'banner_zone_id',
                'type' => 'select2_ajax',
                'label' => 'Banner Zone',
                'placeholder' => 'Type Name, Code',
                'method' => 'POST',
                'select_attribute' => 'name',
            ],
            backpack_url('banner/fetch/banner-zone'),
            function ($value) { // if the filter is active
                $this->crud->query->where('banner_zone_id', '=', $value);
            }
        );

        CRUD::column('id')->label('#');
        CRUD::column('name');
        CRUD::column('bannerZone.code')->label('Banner Zone');
        CRUD::column('enabled')->type('boolean');
        CRUD::column('created_at');
        CRUD::column('updated_at');

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
        CRUD::setValidation(BannerRequest::class);

        CRUD::field('alignment');
        CRUD::field('button_link');
        CRUD::field('button_style');
        CRUD::field('button_title');
        CRUD::field('content');
        CRUD::field('enabled');
        CRUD::field('has_button');
        CRUD::field('image');
        CRUD::field('name');
        CRUD::field('open_new_tab');
        CRUD::field('slider_ratio');
        CRUD::field('text_alignment');
        CRUD::field('image_alignment');
        CRUD::field('background_image');
        CRUD::field('background_type');
        CRUD::field('foreground_type');
        CRUD::field('has_heading');
        CRUD::field('has_content');
        CRUD::field('code');
        CRUD::field('banner_zone_id');

        $this->data['banner_zones'] = BannerZone::orderBy('name')->get()
            ->pluck('name', 'id')->toArray();

        $this->crud->setCreateView('backend::pages.banner_slider.create');

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
        // Cache clear on update
        CRUD::getModel()->updating(function ($entry) {
            Cache::flush();
        });

        $this->crud->setUpdateView('backend::pages.banner_slider.create');

        $this->setupCreateOperation();
    }

    /**
     * Reorder Category
     */
    protected function setupReorderOperation()
    {
        $this->crud->set('reorder.label', 'name');
        $this->crud->set('reorder.max_level', 0);
    }

    protected function fetchBannerZone()
    {
        return $this->fetch(BannerZone::class);
    }
}
