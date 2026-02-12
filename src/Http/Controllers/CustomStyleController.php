<?php

namespace Amplify\System\Cms\Http\Controllers;

use Amplify\System\Abstracts\BackpackCustomCrudController;
use Amplify\System\Backend\Http\Requests\EnvVariableUpdateRequest;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

/**
 * Class CustomStyleController
 *
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class CustomStyleController extends BackpackCustomCrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;

    private string $customStylePath;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setRoute(config('backpack.base.route_prefix') . '/custom-style');
        CRUD::setEntityNameStrings('custom style', 'custom styles');
        $this->customStylePath = public_path('assets/css/custom.css');
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
        if (!file_exists($this->customStylePath)) {
            touch($this->customStylePath);
        }

        $this->crud->removeButton('create');
        $this->crud->setListContentClass('col-lg-12');
        $this->data['content'] = file_get_contents($this->customStylePath);
        $this->crud->setListView('backend::pages.editor');
        $this->data['header'] = <<<HTML
                            <label class="fw-bold">
                                Caution: Custom Styles affect all pages.
                                <code class="text-danger">Style file location: (assets/css/custom.css)</code>.
                            </label>
HTML;

    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     *
     * @param EnvVariableUpdateRequest $request
     * @return RedirectResponse
     */
    protected function store(Request $request)
    {
        $code = $request->input('content', '');

        if (!file_put_contents($this->customStylePath, $code . PHP_EOL)) {
            \Alert::error('Unable to modify customer style file.')->flash();
        } else {
            \Alert::success(__('backpack::crud.update_success'))->flash();
            Artisan::call('optimize:clear');
        }

        return redirect()->to($this->crud->getRoute());
    }
}
