<?php

namespace Amplify\System\Cms\Http\Controllers;

use Amplify\System\Abstracts\BackpackCustomCrudController;
use Amplify\System\Cms\Http\Requests\FooterRequest;
use Amplify\System\Cms\Models\Banner;
use Amplify\System\Cms\Models\BannerZone;
use Amplify\System\Cms\Models\Content;
use Amplify\System\Cms\Models\Footer;
use Amplify\System\Cms\Models\MenuGroup;
use Amplify\System\Marketing\Models\MerchandisingZone;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class FooterCrudController
 *
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class FooterCrudController extends BackpackCustomCrudController
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
     *
     * @throws \Exception
     */
    public function setup()
    {
        CRUD::setModel(Footer::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/footer');
        CRUD::setEntityNameStrings('footer', 'footers');
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

        CRUD::column('id')->type('number')->thousands_sep('');

        CRUD::column('name');

        CRUD::addColumn([
            'name' => 'templat',
            'label' => 'Template',
            'type' => 'custom_html',
            'value' => function ($footer) {
                foreach (config('amplify.cms.themes') as $template) {
                    if ($footer->template_id == $template['id']) {
                        return $template['label'];
                    }
                }

                return 'N/A';
            },
        ]);

        CRUD::addColumn([
            'name' => 'is_enabled',
            'label' => 'Enabled',
            'type' => 'boolean',
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
        CRUD::setValidation(FooterRequest::class);

        $activeTemplate = theme();
        $this->data['active_template'] = $activeTemplate;
        $this->data['footer'] = $this->crud->model->find(request()->id);
        $this->data['footer_layouts'] = getFooterLayoutList();
        $this->data['active_widgets'] = '[]';
        $this->data['footer_items'] = [];
        $this->data['merchandising_zones'] = MerchandisingZone::get(['id', 'name'])->toArray();
        $this->data['banner_item_codes'] = Banner::get(['id', 'name', 'code'])->toArray();
        $this->data['banner_zones'] = BannerZone::get(['id', 'code', 'name', 'fetch_data_from_easyask'])->toArray();
        $this->data['content_lists'] = Content::where(['status' => 1, 'is_approved' => 1])->get();
        $this->data['menu_group_lists'] = MenuGroup::where('is_reserved', false)->get(['id', 'name', 'short_code'])->toArray();

        $this->crud->setCreateView('backend::pages.footer.create');

        CRUD::addField([
            'name' => 'name',
            'label' => 'Name',
        ]);

        CRUD::addField([
            'name' => 'layout',
            'label' => 'layout',
        ]);
        CRUD::addField([
            'name' => 'template_id',
            'label' => 'layout',
        ]);

        CRUD::addField([
            'name' => 'content',
            'label' => 'Content',
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
            'label' => 'Enabled',
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
        $this->data['footer'] = $this->crud->model->find(request()->id);
        $this->crud->setUpdateView('backend::pages.footer.create');
        $this->setupCreateOperation();
    }

    protected function setupShowOperation()
    {
        $this->data['footer'] = $this->crud->model->find(request()->id);
        $this->data['footer_layouts'] = getFooterLayoutList();
        $this->data['footer_items'] = [];
        $saveAction = [
            'active' => [
                'value' => 'save_and_preview',
                'label' => 'Save and preview',
            ],
            'options' => [
                'save_and_back' => 'Save and back',
                'save_and_edit' => 'Save and edit this item',
                'save_and_new' => 'Save and new item',
            ],
        ];
        $this->data['saveAction'] = $saveAction;

        $this->crud->setShowView('backend::pages.footer.create');
    }
}
