<?php

namespace Amplify\System\Cms\Http\Controllers;

use Amplify\System\Abstracts\BackpackCustomCrudController;
use Amplify\System\Backend\Http\Requests\NavigationRequest;
use Amplify\System\Cms\Models\MenuGroup;
use Amplify\System\Cms\Models\Navigation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class NavigationCrudController
 *
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class NavigationCrudController extends BackpackCustomCrudController
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
        CRUD::setModel(Navigation::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/navigation');
        CRUD::setEntityNameStrings('navigation', 'navigation headers');
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
        if (empty(request()->get('template_id'))) {
            $this->crud->addClause('where', 'template_id', '=', template()->id);
        }

        CRUD::column('id')->type('number')->thousands_sep('');

        CRUD::addFilter(
            [
                'name' => 'template_id',
                'type' => 'select2',
                'label' => 'Template',
            ],
            function () {
                $templateOptions = [];

                foreach (config('amplify.cms.themes') as $template) {
                    $templateOptions[$template['id']] = $template['label'];
                }

                return $templateOptions;
            },
            function ($values) {
                $this->crud->addClause('where', 'template_id', json_decode($values));
            }
        );

        CRUD::addFilter(
            [
                'name' => 'is_enabled',
                'type' => 'dropdown',
                'label' => 'Enabled',
            ],
            function () {
                return [
                    '0' => 'No',
                    '1' => 'Yes',
                ];
            },
            function ($value) {
                // if the filter is active
                $this->crud->addClause('where', 'is_enabled', '=', $value);
            }
        );

        CRUD::addColumns([
            [
                'name' => 'name',
                'label' => 'Name',
            ],
            [
                'name' => 'layout',
                'label' => 'Layout',
                'type' => 'custom_html',
                'value' => function ($model) {
                    $nav_layouts = getNavigationLayoutList();
                    if (! empty($nav_layouts[$model->layout])) {
                        return $nav_layouts[$model->layout]['name'];
                    } else {
                        return $model->layout;
                    }
                },
            ],
            [
                'name' => 'top_bar',
                'label' => 'Top Bar',
                'type' => 'boolean',
            ],
            [
                'name' => 'is_enabled',
                'label' => 'Enabled',
                'type' => 'boolean',
            ],
        ]);
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
        CRUD::setValidation(NavigationRequest::class);

        $this->data['menu_list'] = MenuGroup::where('template_id', template()->id)->get();
        $this->data['active_template'] = template();

        $this->data['navigation'] = $this->crud->model->find(request()->id);
        $this->data['nav_layouts'] = getNavigationLayoutList();

        $this->crud->setCreateView('crud::pages.navigation.create');
        CRUD::addField([
            'name' => 'name',
            'label' => 'Name',
        ]);
        CRUD::addField([
            'name' => 'layout',
            'label' => 'Layout',
        ]);
        CRUD::addField([
            'name' => 'menu_group_id',
            'label' => 'Menu group',
        ]);
        CRUD::addField([
            'name' => 'account_menu_id',
            'label' => 'User Account Menu',
        ]);
        CRUD::addField([
            'name' => 'mobile_menu_id',
            'label' => 'Mobile Menu',
        ]);
        CRUD::addField([
            'name' => 'content',
            'label' => 'Content',
        ]);
        CRUD::addField([
            'name' => 'top_bar',
            'label' => 'Top Bar',
        ]);
        CRUD::addField([
            'name' => 'is_new',
            'label' => 'New',
        ]);
        CRUD::addField([
            'name' => 'is_updated',
            'label' => 'Updated',
        ]);
        CRUD::addField([
            'name' => 'is_enabled',
            'label' => 'Updated',
        ]);
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
        $this->data['navigation'] = $this->crud->model->find(request()->id);
        $this->data['menu_group'] = $this->data['navigation']->menu_group;
        $this->data['account_menu'] = $this->data['navigation']->account_menu;
        $this->data['mobile_menu'] = $this->data['navigation']->mobile_menu;
        $this->crud->setUpdateView('crud::pages.navigation.create');
        $this->setupCreateOperation();
    }

    protected function setupShowOperation()
    {
        $this->crud->addColumns([
            [
                'name' => 'id',
                'label' => '#',
            ],
            [
                'name' => 'name',
                'label' => 'Name',
            ],
            [
                'name' => 'menu_group',
                'label' => 'Primary Menu',
                'type' => 'relationship',
            ],
            [
                'name' => 'account_menu',
                'label' => 'Account Menu',
                'type' => 'relationship',
            ],
            [
                'name' => 'mobile_menu',
                'label' => 'Mobile Menu',
                'type' => 'relationship',
            ],
            [
                'name' => 'top_bar',
                'label' => 'Has Top Bar',
                'type' => 'boolean',
            ],
            [
                'name' => 'is_enabled',
                'label' => 'Enabled',
                'type' => 'boolean',
            ],
            [
                'name' => 'search_enabled',
                'label' => 'Search Enabled',
                'type' => 'boolean',
            ],
        ]);
    }
}
