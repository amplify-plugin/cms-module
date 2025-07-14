<?php

namespace Amplify\System\Cms\Http\Controllers;

use Amplify\System\Cms\Http\Requests\MenuGroupRequest;
use Amplify\System\Cms\Models\MenuGroup;
use Amplify\System\Cms\Models\Page;
use Amplify\System\Abstracts\BackpackCustomCrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;

/**
 * Class MenuGroupCrudController
 *
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class MenuGroupCrudController extends BackpackCustomCrudController
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
        CRUD::setModel(MenuGroup::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/menu-group');
        CRUD::setEntityNameStrings('menu-group', 'menus');
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
        $this->crud->addColumn([
            'name' => 'id',
            'label' => '#',
            'type' => 'custom_html',
            'value' => function (MenuGroup $menuGroup) {
                return $menuGroup->id.(($menuGroup->is_reserved == true) ? '<sup class="text-warning font-weight-bold ml-1">Reserved</sup>' : '');
            }]);
        $this->crud->addColumn(['name' => 'name', 'type' => 'text']);
        $this->crud->addColumn(['name' => 'short_code', 'type' => 'text']);
        $this->crud->addColumn(['name' => 'blade_location', 'label' => 'Location', 'type' => 'text']);
        $this->crud->addColumn(['name' => 'active', 'label' => 'Activated', 'type' => 'boolean']);

        /* Button Add */
        $this->crud->addButtonFromModelFunction('line', 'add_menus', 'buttonForMenus', 'beginning');
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     */
    protected function setupCreateOperation(): void
    {
        CRUD::setValidation(MenuGroupRequest::class);

        $pages = Page::all()->pluck('name', 'id')->toArray();

        Widget::add([
            'type' => 'script',
            'content' => 'assets/js/admin/forms/menu-group.js',
        ]);

        CRUD::addFields([
            [
                'name' => 'template_id',
                'type' => 'hidden',
                'default' => template()->id,
            ],
            [
                'name' => 'name',
                'label' => 'Group Name',
                'type' => 'text',
                'attributes' => [
                    'placeholder' => 'Enter Menu Group Name',
                ],
            ],
            [
                'name' => 'style',
                'label' => 'CSS Style',
                'type' => 'textarea',
                'attributes' => ['placeholder' => 'Write custom css style for menu'],
            ],
            [
                'name' => 'class',
                'label' => 'CSS Class',
                'type' => 'textarea',
                'attributes' => ['placeholder' => 'Write css for menu'],
            ],
            [
                'name' => 'active',
                'label' => 'Activated',
                'type' => 'checkbox',
            ],
        ]);
    }

    /**
     * Single show page setup
     */
    public function setupShowOperation(): void
    {
        CRUD::addColumns([
            [
                'name' => 'template',
                'label' => 'Template Name',
                'entity' => 'template',
                'type' => 'relationship',
            ],
            [
                'name' => 'name',
                'label' => 'Menu Name',
            ],
            [
                'name' => 'short_code',
                'label' => 'Short Code',
            ],
            [
                'name' => 'blade_location',
                'label' => 'Location',
            ],
            [
                'name' => 'class',
                'label' => 'Custom CSS Class',
                'attributes' => ['placeholder' => 'Write custom CSS class for menu'],
            ],
            [
                'name' => 'active',
                'label' => 'Activated',
                'type' => 'boolean',
            ],
        ]);
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     */
    protected function setupUpdateOperation(): void
    {
        $this->setupCreateOperation();
    }
}
