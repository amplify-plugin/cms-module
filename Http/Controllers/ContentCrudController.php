<?php

namespace Amplify\System\Cms\Http\Controllers;

use Amplify\System\Abstracts\BackpackCustomCrudController;
use Amplify\System\Cms\Http\Requests\ContentRequest;
use Amplify\System\Cms\Models\Content;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\Pro\Http\Controllers\Operations\InlineCreateOperation;
use Illuminate\Support\Facades\Route;
use Prologue\Alerts\Facades\Alert;

/**
 * Class ContentCrudController
 *
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ContentCrudController extends BackpackCustomCrudController
{
    use \Amplify\System\Backend\Http\Controllers\Backpack\Operations\SlugOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\Pro\Http\Controllers\Operations\FetchOperation;
    use InlineCreateOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(Content::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/content');
        CRUD::setEntityNameStrings('content', 'Content Items');
    }

    protected function setupCustomRoutes($segment, $routeName, $controller): void
    {
        Route::get($segment.'/change-approval/{content}', [
            'as' => $routeName.'.changeApproval',
            'uses' => $controller.'@changeApproval',
            'operation' => 'changeApproval',
        ]);

        Route::get($segment.'/change-status/{content}/{status}', [
            'as' => $routeName.'.changeStatus',
            'uses' => $controller.'@changeStatus',
            'operation' => 'changeStatus',
        ]);
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
                'name' => 'status',
                'type' => 'dropdown',
                'label' => 'Status',
            ],
            function () {
                return ['0' => 'Draft', '1' => 'Published', '2' => 'Archived'];
            },
            function ($value) {
                $this->crud->addClause('where', 'status', '=', $value);
            }
        );
        CRUD::addFilter(
            [
                'name' => 'is_approved',
                'type' => 'dropdown',
                'label' => 'Approval',
            ],
            function () {
                return ['0' => 'Rejected', '1' => 'Approved'];
            },
            function ($value) {
                $this->crud->addClause('where', 'is_approved', '=', $value);
            }
        );

        CRUD::column('id')->type('number')->thousands_sep('');
        CRUD::column('name');
        CRUD::column('slug');
        CRUD::column('categories');
        CRUD::addColumn(['name' => 'content',
            'type' => 'custom_html',
            'value' => function ($entry) {
                return strip_tags($entry->content);
            }]);
        CRUD::addColumn([
            'name' => 'status',
            'label' => 'Status',
            'type' => 'select_from_array',
            'options' => [0 => 'Draft', 1 => 'Published', 2 => 'Archived'],
        ]);
        CRUD::addColumn([
            'name' => 'is_approved',
            'label' => 'Approval',
            'type' => 'boolean',
            'options' => [0 => 'Rejected', 1 => 'Approved'],
        ]);

        $this->crud->addButtonFromModelFunction('line', 'change-approval', 'changeApprovalBtn', 'ending');
        $this->crud->addButtonFromModelFunction('line', 'change-status', 'changeStatusBtn', 'ending');
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
        CRUD::setCreateContentClass('col-md-12');

        CRUD::setValidation(ContentRequest::class);

        CRUD::field('name');
        CRUD::addField([
            'name' => 'slug',
            'target' => 'name',
            'label' => 'Slug',
            'type' => 'slug',
        ]);
        CRUD::addField([
            'name' => 'categories',
            'type' => 'select2_multiple',
            'options' => (fn ($query) => $query->orderBy('name')->get()),
        ]);
        CRUD::field('content')->type('ckeditor');
        CRUD::addField([
            'name' => 'status',
            'type' => 'select_from_array',
            'options' => ['1' => 'Published', '0' => 'Draft', '2' => 'Archived'],
        ]);
        CRUD::addField([
            'name' => 'is_approved',
            'label' => 'Approve',
            'type' => 'boolean',
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

    protected function setupShowOperation(): void
    {
        $this->setupListOperation();
    }

    public function changeApproval(Content $content)
    {
        $content->is_approved = ! $content->is_approved;
        $content->save();

        Alert::add('success', 'Changed approval status.')->flash();

        return back();
    }

    public function changeStatus(Content $content, $status)
    {
        if (in_array($status, [0, 1, 2])) {
            $content->status = $status;
            $content->save();
        }

        Alert::add('success', 'Changed approval status.')->flash();

        return back();
    }
}
