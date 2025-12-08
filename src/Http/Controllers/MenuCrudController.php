<?php

namespace Amplify\System\Cms\Http\Controllers;

use Amplify\System\Abstracts\BackpackCustomCrudController;
use Amplify\System\Backend\Traits\CrudCustomButtonTrait;
use Amplify\System\Backend\Traits\ReorderTrait;
use Amplify\System\Cms\Http\Requests\MenuRequest;
use Amplify\System\Cms\Models\Menu;
use Amplify\System\Cms\Models\MenuGroup;
use Backpack\CRUD\app\Http\Controllers\Operations\ReorderOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use JsonException;

/**
 * Class MenuCrudController
 *
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class MenuCrudController extends BackpackCustomCrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use CrudCustomButtonTrait;
    use ReorderOperation;
    use ReorderTrait;

    public $reorderLabel = 'name';

    private $permission_model;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     *
     * @throws \Exception
     */
    public function setup()
    {
        $this->permission_model = config('backpack.permissionmanager.models.permission');

        CRUD::setModel(Menu::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/menu');
        CRUD::setEntityNameStrings('menu', 'menus');
    }

    protected function setupCustomRoutes($segment, $routeName, $controller)
    {
        Route::get($segment.'/{menu}/check-url-param', [
            'as' => $routeName.'.check-url-param',
            'uses' => $controller.'@checkUrlParam',
        ])
            ->where(['page' => '[\d]+']);
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
        if (request()->has('group_id')) {
            $this->crud->addClause('where', 'group_id', \request('group_id'));
        }

        /* Remove Add New Button */
        $this->crud->removeButton('create');
        $this->crud->removeButton('show');
        $this->crud->removeButton('update');

        $this->crud->addButtonFromModelFunction('top', 'reorder', 'reorderButton', 'beginning');
        $this->crud->addButtonFromModelFunction('line', 'addItem', 'addMegaMenuItemButton', 'end');
        $this->crud->addButtonFromModelFunction('line', 'listItem', 'listMegaMenuItems', 'end');
        $this->crud->addButtonFromModelFunction('top', 'create', 'addNew', 'beginning');
        $this->addCustomButton('edit', $this->crud->route.'/:id/edit?group_id='.request('group_id'),
            [
                'stack' => 'line',
                'view' => 'custom-button',
                'position' => 'beginning',
            ],
            [
                'icon' => 'la la-edit',
                'classes' => 'btn btn-sm btn-link',
            ]
        );
        $this->addCustomButton('show', $this->crud->route.'/:id/show?group_id='.request('group_id'), [
            'position' => 'beginning',
        ], [
            'icon' => 'la la-eye',
            'classes' => 'btn btn-sm btn-link',
        ]);
        $this->crud->addButtonFromView('line', 'preview-page', 'preview-page', 'ending');
        $this->crud->addColumns(
            [
                [
                    'name' => 'id',
                    'label' => '#',
                ],
                [
                    'name' => 'name',
                ],
                [
                    'label' => 'Type',
                    'name' => 'url_type',
                    'type' => 'custom_html',
                    'value' => function (Menu $entity) {
                        if ($entity->type == 'categories') {
                            return 'Categories';
                        } else if ($entity->type == 'mega-menu') {
                            return Menu::MENU_TYPES['mega-menu'];
                        } else {
                            return Menu::URL_TYPES[$entity->url_type] ?? 'N/A';
                        }
                    },
                ],
                [
                    'label' => 'URL',
                    'type' => 'custom_html',
                    'name' => 'url',
                    'value' => function ($model) {
                        return $model->link();
                    },

                ],
                [
                    'label' => 'Parent Item',
                    'name' => 'parent_id',
                    'entity' => 'parent',
                    'attribute' => 'name',
                ],
            ]
        );

        $this->crud->setListView('backend::pages.menus.list');
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
        CRUD::setValidation(MenuRequest::class);
        Widget::add()->type('script')->content('vendor/backend/js/forms/menu-item.js');
        CRUD::addFields([
            [
                'name' => 'name',
                'type' => 'text',
                'label' => 'Name',
                'tab' => 'Basic',
            ],
            [
                'name' => 'group_id',
                'type' => 'select',
                'label' => 'Position',
                'default' => request('group_id'),
                'entity' => 'group',
                'attributes' => ['readonly' => 'readonly'],
                'tab' => 'Basic',
            ],
            [
                'name' => 'type',
                'type' => 'select_from_array',
                'label' => 'Menu Type',
                'options' => Menu::MENU_TYPES,
                'tab' => 'Basic',
            ],
            [
                'name' => 'url_type',
                'type' => 'select_from_array',
                'label' => 'URL Type',
                'options' => Menu::URL_TYPES,
                'default' => 'page',
                'tab' => 'Basic',
            ],
            [
                'name' => 'url',
                'type' => 'text',
                'label' => 'Full URL',
                'tab' => 'Basic',
            ],
            [
                'name' => 'seo_path',
                'type' => 'text',
                'label' => 'SEO Path',
                'hint' => 'Enter SEO Path',
                'tab' => 'Basic',
            ],
            [
                'name' => 'sub_category_depth',
                'type' => 'number',
                'label' => 'Sub Category Depth',
                'default' => 0,
                'hint' => 'Applicable if Menu Type is Categories Tree',
                'tab' => 'Basic',
            ],
            [
                'name' => 'display_product_count',
                'type' => 'boolean',
                'label' => 'Display Product Count',
                'default' => false,
                'hint' => 'Applicable if Menu Type is Categories Tree',
                'tab' => 'Basic',
            ],
            [
                'name' => 'page_id',
                'type' => 'select2',
                'label' => 'Page',
                'entity' => 'page',
                'tab' => 'Basic',
                'options' => (fn ($query) => $query->orderBy('name')->get()),
            ],
            [
                'name' => 'for_authenticated',
                'type' => 'boolean',
                'label' => 'Only Authenticated',
                'hint' => 'Available if authenticated and has permissions.',
                'tab' => 'Basic',
            ],
            [
                'name' => 'for_guest',
                'type' => 'boolean',
                'label' => 'Only Public',
                'hint' => 'Invisible for Authenticated',
                'tab' => 'Basic',
            ],
            [
                'name' => 'enabled',
                'type' => 'boolean',
                'default' => true,
                'tab' => 'Basic',
                'hint' => 'If disabled then menu will not load any condition',
            ],
            [
                'label' => mb_ucfirst(trans('backpack::permissionmanager.permission_plural')),
                'type' => 'permission',
                'name' => 'permissions',
                'model' => $this->permission_model,
                'options' => function () {
                    return $this->permission_model::where('guard_name', customer_guard())->orderBy('name', 'ASC')->pluck('name', 'id')->toArray();
                },
                'tab' => 'Security',
            ],
            [
                'name' => 'open_new_tab',
                'type' => 'boolean',
                'label' => 'Open Link in Separate Tab',
                'tab' => 'Additional',
            ],
            [
                'name' => 'class',
                'type' => 'text',
                'label' => 'CSS Class',
                'tab' => 'Additional',
            ],
            [
                'name' => 'icon',
                'type' => 'icon_picker',
                'label' => 'Icon',
                'iconset' => 'fontawesome',
                'tab' => 'Additional',
            ],
            [
                'name' => 'style',
                'type' => 'textarea',
                'label' => 'CSS Style',
                'tab' => 'Additional',
            ],
            [
                'name' => 'queries',
                'type' => 'table',
                'label' => 'URL Query String',
                'entity_singular' => 'Add More',
                'tab' => 'Additional',
                'columns' => [
                    'name' => 'Key',
                    'value' => 'Value',
                ],
            ],
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
        $this->setupCreateOperation();
    }

    public function setupShowOperation()
    {
        Widget::add()->type('style')
            ->content(asset('packages/bootstrap-iconpicker/icon-fonts/font-awesome-5.12.0-1/css/all.min.css'));

        CRUD::addColumns([
            [
                'name' => 'name',
                'type' => 'text',
                'label' => 'Name',
            ],
            [
                'name' => 'group',
                'type' => 'select',
                'label' => 'Position',
                'entity' => 'group',
            ],
            [
                'name' => 'type',
                'type' => 'select_from_array',
                'label' => 'Menu Type',
                'options' => Menu::MENU_TYPES,
            ],
            [
                'name' => 'url_type',
                'type' => 'select_from_array',
                'label' => 'URL Type',
                'options' => Menu::URL_TYPES,
                'default' => 'page',
            ],
            [
                'name' => 'url',
                'type' => 'model_function',
                'function_name' => 'link',
                'label' => 'Full URL',
            ],
            [
                'name' => 'page',
                'type' => 'select',
                'label' => 'Page',
                'entity' => 'page',
            ],
            [
                'name' => 'for_authenticated',
                'type' => 'boolean',
                'label' => 'Only Authenticated',
                'hint' => 'Available if authenticated and has permissions.',
            ],
            [
                'name' => 'for_guest',
                'type' => 'boolean',
                'label' => 'Only Public',
                'hint' => 'Invisible for Authenticated',
            ],
            [
                'name' => 'enabled',
                'type' => 'boolean',
                'tab' => 'Basic',
                'hint' => 'If disabled then menu will not load any condition',
            ],
            [
                'name' => 'icon',
                'type' => 'custom_html',
                'value' => function ($menu) {
                    return $menu->icon
                        ? "<p class='mb-0'><span class='d-block'>Preview: <i class='{$menu->icon}'></i></span><span class='d-block'>Class: {$menu->icon}</span></p>"
                        : '-';
                },
            ],
            [
                'name' => 'open_new_tab',
                'type' => 'boolean',
                'label' => 'Open Link in Separate Tab',
            ],
        ]);
    }

    public function setupReorderOperation()
    {
        $level = 2;

        if (request()->has('group_id')) {

            $menuGroup = MenuGroup::find(request('group_id'));

            $this->crud->addClause('where', 'group_id', $menuGroup->id);

            if ($menuGroup->short_code == config('amplify.frontend.menus.primary_menu', 'primary-menu')) {
                $level = 3;
            }

        }

        $this->crud->set('reorder.max_level', $level);
        $this->crud->set('reorder.label', 'name');
    }

    /**
     *  Reorder the items in the database using the Nested Set pattern.
     *
     *  Database columns needed: id, parent_id, lft, rgt, depth, name/title
     *
     * @return Application|Factory|View
     */
    public function reorder(Request $request)
    {
        /* Change back url */
        $this->crud->route = $this->backTo($request);

        $this->getReorderData();

        return view('backend::pages.menus.menu-items.reorder', $this->data);
    }

    public function backTo(Request $request)
    {
        return route('menu.index');
    }

    private function setLocalData($newDataField, $existingDataField, $locale)
    {
        $existingDataField[$locale] = $newDataField;

        return json_encode($existingDataField);
    }

    /**
     * @throws JsonException
     */
    public function checkUrlParam(Menu $menu): JsonResponse
    {
        $jsonResponse = [
            'url' => url('#'),
            'params' => [],
            'type' => 'success',
            'message' => 'Redirecting to Web Page.',
        ];

        if ($menu->type == 'mega-menu') {
            $jsonResponse['type'] = 'error';
            $jsonResponse['url'] = route('frontend.index');
        }

        if ($menu->type == 'default') {

            if ($menu->url_type == 'external') {
                $jsonResponse['url'] = $menu->url;
            }

            if ($menu->url_type == 'page') {

                if ($page = $menu->page) {
                    $jsonResponse['url'] = $page->full_url_without_substitute;
                    $jsonResponse['params'] = get_uri_parameter($jsonResponse['url']);

                    if (! empty($jsonResponse['params'])) {
                        $jsonResponse['type'] = 'warning';
                        $jsonResponse['message'] = 'Please Fill the required parameters';
                    }
                } else {
                    $jsonResponse['type'] = 'error';
                    $jsonResponse['message'] = 'Something went wrong.';
                }
            }
        }

        if ($queries = $menu->queries) {
            $queryString = $menu->prepareQueries($queries);

            if (stripos($jsonResponse['url'], '?')) {
                $queryString = '&'.substr($queryString, 1);
            }

            $jsonResponse['url'] .= $queryString;
        }

        return response()->json($jsonResponse, ($jsonResponse['type'] == 'error') ? 500 : 200);
    }
}
