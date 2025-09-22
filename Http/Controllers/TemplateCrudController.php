<?php

namespace Amplify\System\Cms\Http\Controllers;

use Amplify\System\Abstracts\BackpackCustomCrudController;
use Amplify\System\Backend\Models\SystemConfiguration;
use Amplify\System\Cms\Http\Requests\TemplateRequest;
use Amplify\System\Cms\Models\Footer;
use Amplify\System\Cms\Models\Template;
use Amplify\System\Support\ChunkUpload;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanel;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\Pro\Http\Controllers\Operations\FetchOperation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Prologue\Alerts\Facades\Alert;

/**
 * Class TemplateCrudController
 *
 * @property-read CrudPanel $crud
 */
class TemplateCrudController extends BackpackCustomCrudController
{
    use CreateOperation;
    use DeleteOperation;
    use FetchOperation;
    use ListOperation;
    use ShowOperation;
    use UpdateOperation {
        update as traitUpdate;
    }

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(Template::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/template');
        CRUD::setEntityNameStrings('template', 'themes');
    }

    protected function setupCustomRoutes($segment, $routeName, $controller)
    {
        Route::get($segment . '/set-template-active/{template}', [
            'as' => $routeName . '.set.activeTemplate',
            'uses' => $controller . '@setActiveTemplate',
            'operation' => 'setActiveTemplate',
        ]);
        Route::get($segment . '/installation-info', [
            'as' => $routeName . '.installation-info',
            'uses' => $controller . '@installationInfo',
            'operation' => 'installation-info',
        ]);
        Route::post($segment . '/chunk-upload', [
            'as' => $routeName . '.chunk-upload',
            'uses' => $controller . '@chunkUpload',
            'operation' => 'chunk-upload',
        ]);
        Route::post($segment . '/install-template', [
            'as' => $routeName . '.install-template',
            'uses' => $controller . '@installTemplate',
            'operation' => 'install-template',
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
        CRUD::addColumn([
            'name' => 'id',
            'type' => 'custom_html',
            'value' => function ($model) {
                if ($model->is_new === 1 && !$model->is_updated) {
                    return $model->id . ' <sup class="badge text-danger px-0">New</sup>';
                } elseif ($model->is_updated) {
                    return $model->id . ' <sup class="badge text-warning px-0">Updated</sup>';
                } else {
                    return $model->id;
                }
            },
        ]);

        CRUD::addColumn([
            'name' => 'name',
            'label' => 'Template',
        ]);

        CRUD::addColumn([
            'name' => 'screenshot',
            'label' => 'Thumbnail',
            'type' => 'image',
            'height' => '234px',
            'width' => '192px',
            'wrapper' => [
                'class' => 'text-center',
                'element' => 'div',
                'style' => 'display: block; width: 192px; object-fit: contain',
            ],
        ]);

        CRUD::addColumn([
            'name' => 'author',
            'label' => 'Author',
        ]);
        CRUD::addColumn([
            'name' => 'is_active',
            'label' => 'Active?',
            'type' => 'boolean',
        ]);
        CRUD::addColumn([
            'name' => 'created_at',
            'label' => 'Installed At',
            'type' => 'datetime',
        ]);

        $this->crud->addButtonFromModelFunction('line', 'set_active_template', 'setActiveTemplate', 'ending');
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
        $target_directory = base_path('themes/tmp');

        if (!is_dir($target_directory)) {
            mkdir($target_directory, 0777, true);
        }

        CRUD::setValidation(TemplateRequest::class);
        $this->crud->setCreateView('backend::pages.template.create');
    }

    protected function setupShowOperation()
    {
        $this->data['template'] = $this->crud->model->find(request('id'));
        if ($this->data['template']->is_new) {
            $this->data['template']->is_new = false;
            $this->data['template']->save();
        }

        CRUD::addColumns([
            [
                'name' => 'screenshot',
                'label' => 'Thumbnail',
                'type' => 'image',
                'height' => '234px',
                'width' => '192px',
                'wrapper' => [
                    'class' => 'text-center',
                    'element' => 'div',
                    'style' => 'display: block; width: 192px; object-fit: contain',
                ],
            ],
            [
                'name' => 'name',
                'label' => 'Template Name',
            ],
            [
                'name' => 'author',
                'label' => 'Author',
            ],
            [
                'name' => 'description',
                'label' => 'Description',
            ],
            [
                'name' => 'is_active',
                'label' => 'Activated?',
                'type' => 'boolean',
            ],
            ...$this->data['template']->options,
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
        $model = $this->crud->getCurrentEntry();

        $this->crud->addFields($model->options);
    }

    public function update(Request $request)
    {
        $template = $this->crud->getCurrentEntry();

        $options = $template->options;

        array_walk($options, function (&$option) use ($request) {
            $option['value'] = $request->input($option['name'], $option['value']);
        });

        $template->options = $options;

        $template->getDirty();

        $template->save();

        return $this->crud->performSaveAction($this->crud->getCurrentEntry()->getKey());
    }

    public function fetchTemplateSlug(): JsonResponse
    {
        $slug = request()->slug;
        $id = request()->id ?? null;

        return response()->json([
            'slug' => getTemplateSlug($slug, $id),
        ]);
    }

    public function setActiveTemplate(Template $template): RedirectResponse
    {
        Template::query()->where('is_active', '=', true)->update(['is_active' => false]);

        // Enable top bar and navigation for template
        // Navigation::where('template_id', $template->id)->update(['is_enabled' => true]);

        // Enable footer for template
        Footer::where('template_id', $template->id)->update(['is_enabled' => true]);

        // Activating template
        if ($template->update(['is_active' => true])) {
            SystemConfiguration::setValue('cms', 'default', $template->slug);
            Alert::add('success', "The {$template->name} is activated")->flash();
        } else {
            Alert::add('error', 'Something went wrong please try again!')->flash();
        }

        return redirect()->back();
    }

    public function chunkUpload(Request $request)
    {
        $receiver = new ChunkUpload($request);

        return $receiver->receive('file', function ($file) {
            $zip = new \ZipArchive;
            if ($zip->open($file) === true) {
                $zip->extractTo(base_path('themes/tmp'));
                $zip->close();

                return [
                    'message' => 'Successfully uploaded.',
                ];
            }

            return [
                'message' => 'Corrupted file.',
            ];
        });
    }

    public function installationInfo(Request $request)
    {
        try {

            if (!file_exists(base_path('themes/tmp/config.json'))) {
                throw new \InvalidArgumentException('`config.json` file is missing');
            }

            $config = json_decode(file_get_contents(base_path('themes/tmp/config.json')), true);

            if (!file_exists(base_path('themes/tmp/assets/' . $config['screenshot']))) {
                throw new \InvalidArgumentException('`Thumbnail Screenshot` file is missing');
            }

            $target_dest = public_path('.tmp/');

            if (!dir($target_dest)) {
                mkdir($target_dest, 0777, true);
            }

            copy(base_path('themes/tmp/assets/' . $config['screenshot']), $target_dest . $config['screenshot']);

            $res = [
                'name' => $config['label'],
                'author' => $config['author'],
                'template_banner' => asset('.tmp/' . $config['screenshot']),
            ];

            return response()->json($res, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function installTemplate(Request $request)
    {
        $destination = null;
        try {
            $config = json_decode(file_get_contents(base_path('themes/tmp/config.json')), true);

            $destination = base_path("themes/{$config['slug']}");

            if (is_dir($destination)) {
                $this->deleteTmpTemplate();

                return response()->json([
                    'message' => 'Template already exists.',
                ], 500);
            }

            rename(base_path('themes/tmp'), $destination);

            Template::create([
                'name' => $config['label'],
                'author' => $config['author'],
                'slug' => $config['slug'],
                'component_folder' => $config['component_folder'],
                'asset_folder' => $config['asset_folder'],
                'description' => $config['description'],
                'screenshot' => "themes/{$config['asset_folder']}/assets/{$config['screenshot']}",
                'readme' => $config['readme'],
                'is_new' => true,
                'options' => $config['options'] ?? [],
            ]);

            return response()->json([
                'message' => 'Successfully installed template.',
            ]);
        } catch (\Throwable $th) {
            $this->deleteTmpTemplate($destination);

            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    private function deleteTmpTemplate($destination = null): void
    {
        exec('rm -rf ' . base_path('public/tmp/*'));
        if ($destination) {
            exec('rm -rf ' . $destination);
        }
    }
}
