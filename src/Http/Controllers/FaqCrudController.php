<?php

namespace Amplify\System\Cms\Http\Controllers;

use Amplify\System\Abstracts\BackpackCustomCrudController;
use Amplify\System\Backend\Http\Requests\FaqRequest;
use Amplify\System\Backend\Models\Faq;
use Amplify\System\Backend\Models\FaqCategory;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanel;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\Pro\Http\Controllers\Operations\FetchOperation;

/**
 * Class FaqCrudController
 *
 * @property-read CrudPanel $crud
 */
class FaqCrudController extends BackpackCustomCrudController
{
    use CreateOperation;
    use DeleteOperation;
    use FetchOperation;
    use ListOperation;
    use ShowOperation;
    use UpdateOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(Faq::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/faq');
        CRUD::setEntityNameStrings('faq', 'FAQs');
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
        CRUD::addColumn('id');
        CRUD::addColumn(['name' => 'question', 'type' => 'string', 'label' => 'FAQ Question']);
        CRUD::addColumn([
            'name' => 'faq_categories',
            'label' => 'FAQ Category',
            'type' => 'custom_html',
            'value' => function ($model) {
                return FaqCategory::query()->find($model->faq_category_id)->name ?? '-';
            },
        ]);
        CRUD::addColumn([
            'name' => 'no_views',
            'label' => 'FAQ Views',
            'type' => 'custom_html',
            'value' => function ($model) {
                return '<p class="text-center"><span class="badge badge-primary">'.$model->no_views.'</span></p>';
            },
        ]);
        CRUD::addColumn([
            'name' => 'useful',
            'label' => 'Useful',
            'type' => 'custom_html',
            'value' => function ($model) {
                return '<p class="text-center"><span class="badge badge-success">'.$model->useful.'</span></p>';
            },
        ]);
        CRUD::addColumn([
            'name' => 'not_useful',
            'label' => 'Not Useful',
            'type' => 'custom_html',
            'value' => function ($model) {
                return '<p class="text-center"><span class="badge badge-error">'.$model->not_useful.'</span></p>';
            },
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
        CRUD::setValidation(FaqRequest::class);

        CRUD::addField([
            'name' => 'faq_category_id',
            'type' => 'select_from_array',
            'options' => FaqCategory::get()->pluck('name', 'id')->toArray(),
            'allows_null' => false,
        ]);

        CRUD::addField([
            'name' => 'question',
            'label' => 'FAQ Question',
        ]);

        CRUD::addField([
            'name' => 'response',
            'label' => 'FAQ Response Text',
            'type' => 'ckeditor',
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
        $this->data['faqData'] = $this->crud->getCurrentEntry();

        $this->setupCreateOperation();
    }

    protected function setupShowOperation(): void
    {
        CRUD::addColumn(['name' => 'question', 'label' => 'FAQ Question']);
        CRUD::addColumn([
            'name' => 'response',
            'label' => 'FAQ Response Text',
            'type' => 'custom_html',
            'value' => function ($model) {
                return $model->response ?? '-';
            },
        ]);
        CRUD::addColumn([
            'name' => 'faq_category_id',
            'label' => 'FAQ Category',
            'type' => 'custom_html',
            'value' => function ($model) {
                return FaqCategory::query()->find($model->faq_category_id)->name ?? '-';
            },
        ]);
        CRUD::addColumn([
            'name' => 'no_views',
            'label' => 'FAQ Views',
            'type' => 'custom_html',
            'value' => function ($model) {
                return '<span class="badge badge-primary">'.$model->no_views.'</span>';
            },
        ]);
        CRUD::addColumn([
            'name' => 'useful',
            'label' => 'Useful',
            'type' => 'custom_html',
            'value' => function ($model) {
                return '<span class="badge badge-success">'.$model->useful.'</span>';
            },
        ]);
        CRUD::addColumn([
            'name' => 'not_useful',
            'label' => 'Not Useful',
            'type' => 'custom_html',
            'value' => function ($model) {
                return '<span class="badge badge-error">'.$model->not_useful.'</span>';
            },
        ]);
        CRUD::button('delete')->remove();
    }
}
