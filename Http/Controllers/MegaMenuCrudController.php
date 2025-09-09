<?php

namespace Amplify\System\Cms\Http\Controllers;

use Amplify\System\Abstracts\BackpackCustomCrudController;
use Amplify\System\Cms\Http\Requests\MegaMenuRequest;
use Amplify\System\Cms\Models\MegaMenu;
use Amplify\System\Cms\Models\Menu;
use Amplify\System\Marketing\Models\MerchandisingZone;
use Amplify\System\Sayt\Sayt;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Http\Request;

/**
 * Class MegaMenuCrudController
 *
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class MegaMenuCrudController extends BackpackCustomCrudController
{
    use \Amplify\System\Backend\Traits\ReorderTrait;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ReorderOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    public $reorderLabel = 'name';

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(MegaMenu::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/mega-menu');
        CRUD::setEntityNameStrings('mega-menu', 'mega menus');
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
        $menu = Menu::where('id', request()->menuId)->where('type', 'mega-menu')->firstOrFail();
        $this->crud->addClause('where', 'menu_id', $menu->id);
        $this->crud->menu = $menu;

        CRUD::column('name')->type('text');

        CRUD::addColumn([
            'name' => 'type',
            'label' => 'Type',
            'type' => 'select_from_array',
            'options' => MegaMenu::TYPES,
        ]);

        $this->crud->addColumn([
            'label' => 'Menu Column',
            'type' => 'custom_html',
            'value' => function ($entity) {
                return $entity->getRawOriginal('menu_column_size');
            },
        ]);
        $this->crud->addColumn([
            'label' => 'Parent Menu',
            'name' => 'menu.name',
            'type' => 'relationship',
        ]);

        $this->crud->addColumn([
            'label' => 'Enabled',
            'name' => 'enabled',
            'type' => 'boolean',
        ]);

        $this->crud->removeButton('create'); // remove previous create button
        $this->crud->addButtonFromModelFunction('top', 'create', 'createMegaMenuButton');

        $this->crud->setListView('backend::pages.mega-menus.list');
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
        if (request()->routeIs('mega-menu.create') || request()->routeIs('mega-menu.update') || request()->routeIs('mega-menu.store')) {
            $menu = Menu::where('id', request()->menuId ?? request()->menu_id)->where('type', 'mega-menu')->firstOrFail();
            $this->data['available_menu_column'] = MegaMenu::menuFreeColumn(request()->menuId ?? request()->menu_id);
        } elseif (request()->routeIs('mega-menu.edit')) {
            $menu = Menu::where('id', $this->crud->getEntry(request()->id)->menu_id)->where('type', 'mega-menu')->firstOrFail();
            $this->data['available_menu_column'] = MegaMenu::menuFreeColumn($menu->id, request()->id);
        }

        $this->data['menu'] = $menu;
        $this->data['mega_menu'] = null;
        $this->data['mega_menu_types'] = MegaMenu::TYPES;
        $this->data['merchandising_zones'] = MerchandisingZone::all(['id', 'name']);
        $this->data['categories'] = [];

        if (request()->id) {
            $this->data['mega_menu'] = $this->crud->model->find(request()->id)->load('products');
        }

        $this->crud->setEditContentClass('col-md-12 bold-labels');

        $this->crud->setCreateView('backend::pages.mega-menus.create');
    }

    public function store(MegaMenuRequest $request)
    {
        $data = $this->formatMegaMenuRequest($request);

        $megaMenu = MegaMenu::create($data);

        /* if type is Product */
        if ($request->type == 'product') {
            $megaMenu->products()->createMany($request->products);
        }
    }

    private function formatMegaMenuRequest(Request $request): array
    {
        $data = $request->only(['name', 'menu_column_size', 'type', 'menu_id', 'show_name', 'enabled']);
        $links = [];

        /* if Mega menu type is default */
        if ($request->type == 'default') {
            foreach ($request->links as $link) {
                if ($link['name'] != null && $link['link'] != null) {
                    array_push($links, $link);
                }
            }
            $data['links'] = json_encode($links);
        }

        /* if type is category */
        if ($request->type == 'category' || $request->type == 'manufacturer') {
            $data['number_of_categories'] = $request->number_of_categories;
        }

        $data['only_featured_manufacturer'] = ($request->type == 'manufacturer')
            ? $request->input('only_featured_manufacturer')
            : null;

        /* if type is sub-category */
        if ($request->type == 'sub-category') {
            $data['category_seopath'] = $request->category_seopath;
        }

        /* if type is merchandising zones */
        if ($request->type == 'merchandise-zone') {
            $data = $data + $request->only(['merchandising_zone_id', 'number_of_merchandising_products', 'number_of_column_merchandising_zone', 'merchandising_attribute_access']);
        }

        if ($request->type == 'video') {
            //            $data = $data + $request->only(['merchandising_zone_id', 'number_of_merchandising_products', 'number_of_column_merchandising_zone', 'merchandising_attribute_access']);
        }

        if ($request->type == 'image') {
            //            $data = $data + $request->only(['merchandising_zone_id', 'number_of_merchandising_products', 'number_of_column_merchandising_zone', 'merchandising_attribute_access']);
        }

        /* if type is html */
        if ($request->type == 'html') {
            $data['html_content'] = $request->html_content;
        }

        return $data;
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
        $this->crud->setUpdateContentClass('col-md-12');
        $this->crud->setUpdateView('backend::pages.mega-menus.create');
        $this->setupCreateOperation();
    }

    public function update(MegaMenuRequest $request)
    {
        $megaMenu = MegaMenu::findOrFail($request->id);

        $data = $this->formatMegaMenuRequest($request);

        $megaMenu->update($data);

        /* if type is Product */
        if ($request->type == 'product') {
            $megaMenu->products()->delete();
            $megaMenu->products()->createMany($request->products);
        }
    }

    // Setup mega menu reorder operation.
    public function setupReorderOperation()
    {
        $this->crud->set('reorder.max_level', 1);
    }

    public function reorder(Request $request)
    {
        $this->crud->addClause('where', 'menu_id', $request->menuId);
        $this->getReorderData();

        return view('backend::pages.mega-menus.mega-menu-items.reorder', $this->data);
    }

    public function getEACategories()
    {
        return optional(Sayt::getEaProductsData('shop')['categories'])->categoryList;
    }
}
