<?php

namespace Amplify\System\Cms\Http\Controllers;

use Amplify\System\Abstracts\BackpackCustomCrudController;
use Amplify\System\Backend\Models\SystemConfiguration;
use Amplify\System\Cms\Http\Requests\TemplateRequest;
use Amplify\System\Cms\Models\Footer;
use Amplify\System\Cms\Models\Template;
use Amplify\System\Cms\Rules\ThemeFile;
use Amplify\System\Cms\Rules\ThemeVersion;
use Amplify\System\Support\ChunkUpload;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanel;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\Pro\Http\Controllers\Operations\FetchOperation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Prologue\Alerts\Facades\Alert;
use Symfony\Component\Yaml\Yaml;
use Illuminate\Support\Facades\File;

/**
 * Class ThemeCrudController
 *
 * @property-read CrudPanel $crud
 */
class ThemeCrudController extends BackpackCustomCrudController
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
        CRUD::setRoute(config('backpack.base.route_prefix') . '/theme');
        CRUD::setEntityNameStrings('theme', 'themes');
    }

    protected function setupCustomRoutes($segment, $routeName, $controller)
    {
        Route::get($segment . '/set-template-active/{template}', [
            'as' => $routeName . '.set.activeTemplate',
            'uses' => $controller . '@setActiveTemplate',
            'operation' => 'setActiveTemplate',
        ]);

        Route::get($segment . '/install', [
            'as' => $routeName . '.install',
            'uses' => $controller . '@install',
            'operation' => 'install',
        ]);

        Route::post($segment . '/upload', [
            'as' => $routeName . '.chunk-upload',
            'uses' => $controller . '@chunkUpload',
            'operation' => 'chunk-upload',
        ]);

        Route::post($segment . '/sync', [
            'as' => $routeName . '.sync',
            'uses' => $controller . '@sync',
            'operation' => 'sync',
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
        CRUD::addButtonFromView('top', 'sync_themes', 'sync_themes');

        CRUD::addColumns([
            [
                'name' => 'id',
                'type' => 'custom_html',
                'value' => function ($model) {
                    if ($model->is_new == 1 && $model->is_updated == 0) {
                        return $model->id . ' <sup class="badge text-danger px-0">New</sup>';
                    }

                    if ($model->is_updated) {
                        return $model->id . ' <sup class="badge text-warning px-0">Updated</sup>';
                    }

                    return $model->id;
                },
            ],
            [
                'name' => 'name',
                'label' => 'Name',
            ],
            [
                'name' => 'author',
                'label' => 'Author',
                'type' => 'custom_html',
                'element' => 'p',
                'class' => 'mb-0',
                'value' => function ($model) {
                    if ($model->author) {
                        return implode("<br>", array_map(function ($author) {

                            if (preg_match('/^([^<>]+)\s*<([^<>\s@]+@[^<>\s@]+\.[^<>\s@]+)>$/', $author, $matches)) {
                                $name = trim($matches[1]);
                                $email = $matches[2];
                                return "<a href='mailto:{$email}'>{$name}</a>";
                            }

                            return $author;

                        }, $model->author));
                    }
                    return 'N/A';
                }
            ],
            [
                'name' => 'is_active',
                'label' => 'Active?',
                'type' => 'boolean',
            ],
            [
                'name' => 'created_at',
                'label' => 'Installed At',
                'type' => 'datetime',
            ],
            [
                'name' => 'updated_at',
                'label' => 'Updated At',
                'type' => 'datetime',
            ]
        ]);

        $this->crud->addButtonFromModelFunction('line', 'set_active_template', 'setActiveTemplate', 'ending');
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
                'tab' => 'Basic',
                'name' => 'name',
                'label' => 'Name',
            ],
            [
                'tab' => 'Basic',
                'name' => 'slug',
                'label' => 'Code',
            ],
            [
                'tab' => 'Basic',
                'name' => 'version',
                'label' => 'Version',
            ],
            [
                'tab' => 'Basic',
                'name' => 'author',
                'label' => 'Author',
                'type' => 'custom_html',
                'element' => 'p',
                'class' => 'mb-0',
                'value' => function ($model) {
                    if ($model->author) {
                        return implode("<br>", array_map(function ($author) {

                            if (preg_match('/^([^<>]+)\s*<([^<>\s@]+@[^<>\s@]+\.[^<>\s@]+)>$/', $author, $matches)) {
                                $name = trim($matches[1]);
                                $email = $matches[2];
                                return "<a href='mailto:{$email}'>{$name}</a>";
                            }

                            return $author;

                        }, $model->author));
                    }
                    return 'N/A';
                }
            ],
            [
                'tab' => 'Basic',
                'name' => 'description',
                'label' => 'Description',
                'type' => 'textarea',
            ],
            [
                'tab' => 'Basic',
                'name' => 'is_active',
                'label' => 'Active?',
                'type' => 'boolean',
            ],
            [
                'tab' => 'Preview',
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
            ...$this->data['template']->options,
            [
                'tab' => 'Resources',
                'name' => 'readme',
                'label' => 'README',
                'type' => 'custom_html',
                'value' => function ($model) {
                    if (!$model->readme) {
                        return '';
                    }
                    $filepath = base_path("themes/{$model->slug}/{$model->readme}");
                    if (file_exists($filepath)) {
                        $content = file_get_contents($filepath);
                        return "<div class='markdown-content'>" . \Illuminate\Mail\Markdown::parse($content) . "</div>";
                    }

                    return '';
                }
            ],
            [
                'tab' => 'Resources',
                'name' => 'changelog',
                'label' => 'CHANGELOG',
                'type' => 'custom_html',
                'value' => function ($model) {
                    if (!$model->changelog) {
                        return '';
                    }
                    $filepath = base_path("themes/{$model->slug}/{$model->changelog}");
                    if (file_exists($filepath)) {
                        $content = file_get_contents($filepath);
                        return "<div class='markdown-content'>" . \Illuminate\Mail\Markdown::parse($content) . "</div>";
                    }

                    return '';
                }
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
        $model = $this->crud->getCurrentEntry();

        $this->crud->addFields($model->options);
    }

    protected function sync()
    {
        $themes = glob(base_path('themes/*/theme.yaml'));

        if (empty($themes)) {
            return response('No valid theme configurations found');
        }

        $errors = [];
        $configurations = [];
        $folders = [];


        //config file validation
        foreach ($themes as $theme) {
            $folder = basename(dirname($theme));
            $folders[] = $folder;
            $temp = Yaml::parseFile($theme);

            $validator = $this->verifyConfig($temp, $folder);

            if ($validator->fails()) {
                $messages = implode("</li><li>", Arr::flatten($validator->errors()->toArray()));
                $errors[] = "Theme <strong>" . ucfirst($folder) . "</strong> has some errors.<br>"
                    . "<ul class='list-unstyled'><li>{$messages}</li></ul>";

                continue;
            }

            $optionNames = array_column($temp['options'] ?? [], 'name');

            if (count($optionNames) !== count(array_unique($optionNames))) {
                $errors[] = "Theme <strong>" . ucfirst($folder) . "</strong> has duplicate option names.<br>";

                continue;
            }

            $configurations[$folder] = $temp;
        }

        if (!empty($errors)) {
            return response()->json(['error' => $errors]);
        }

        $report = [];

        //update and create theme
        foreach ($configurations as $slug => $config) {
            try {
                DB::transaction(function () use ($slug, $config, &$report) {
                    $template = Template::withTrashed()->where('slug', $slug)->first();

                    if ($template === null) {
                        $config['is_new'] = true;
                        $this->createTheme($config);
                        $report['success'][] = "Theme {$config['name']} installed successfully.";
                    } else {

                        if ($template->trashed()) {
                            $template->restore();
                        }

                        $config['options'] = $this->mergeThemeOptions($template->options ?? [], $config['options'] ?? []);
                        $config['is_new'] = false;
                        $config['is_updated'] = true;
                        $template->update($config);
                        $report['warning'][] = "Theme {$config['name']} updated successfully.";
                    }
                });
            } catch (\Throwable $e) {
                $report['error'][] = "Theme <strong>" . ucfirst($slug) . "</strong> synchronization failed: {$e->getMessage()}";
            }
        }

        // delete not available themes
        Template::whereNotIn('slug', $folders)
            ->get()->each(function ($template) use (&$report) {
                DB::transaction(function () use ($template, &$report) {
                    $template->delete();

                    $report['error'][] = "{$template->name} theme successfully deleted.";
                });
            });

        return response()->json($report);
    }

    private function mergeThemeOptions(array $databaseOptions, array $configurationOptions): array
    {
        $existing = collect($databaseOptions)->keyBy('name');
        $merged = [];

        foreach ($configurationOptions as $option) {
            $current = $existing->get($option['name']);

            if ($current !== null && array_key_exists('value', $current)) {
                $option['value'] = $current['value'];
            }

            $merged[] = $option;
        }

        return $merged;
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

    public function setActiveTemplate(Template $template): RedirectResponse
    {
        Template::query()->where('is_active', '=', true)->update(['is_active' => false]);

        // Enable footer for template
        Footer::where('template', $template->slug)->update(['is_enabled' => true]);

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
        return (new ChunkUpload($request))->receive('file', function ($file) {
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

    public function install()
    {

        try {

            throw_if(!file_exists(base_path("themes/tmp/theme.yaml")), 'Uploaded theme missing configuration file.');

            $config = Yaml::parseFile(base_path('themes/tmp/theme.yaml'));

            $validator = $this->verifyConfig($config, 'tmp', true);

            throw_if($validator->fails(), "Invalid Theme Configuration. Error: " . $validator->errors()->first());

            $folder = $config['slug'];

            throw_if(is_dir(base_path("themes/{$folder}")), "Theme folder already exists. Please try again later.");

            throw_if(
                !File::moveDirectory(base_path('themes/tmp'), base_path("themes/{$folder}"), true),
                'The team installation failed because the theme could not be moved.'
            );

            $this->createTheme($config);

            return response()->json([
                'message' => "The {$config['name']} theme installed successfully.",
                'redirect_to' => backpack_url('theme')
            ]);

        } catch (\Throwable $th) {

            File::cleanDirectory(base_path('themes/tmp'));

            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    private function verifyConfig(array $data = [], string $folder = 'tmp', bool $new = false): \Illuminate\Validation\Validator
    {
        if ($theme = Template::withTrashed()->where('slug', $data['slug'])->first()) {
            $theme->restore();
        }

        $slugRules = ['required', 'string', 'min:3', 'max:255', 'ascii'];

        if ($new) {
            $slugRules[] = 'unique:templates,slug';
        }


        return Validator::make($data, [
            'name' => 'required|string|min:3|max:255|ascii',
            'slug' => $slugRules,
            'author' => 'required|array|min:1',
            'author.*' => ['required', 'string', 'regex:/^[^<>]+<[^<>\s@]+@[^<>\s@]+\.[^<>\s@]+>$/'],
            'version' => ['required', 'string', new ThemeVersion],
            'description' => 'present|string|ascii',
            'asset' => ['nullable', new ThemeFile($folder, 'mix-manifest.json')],
            'layout' => ['nullable', new ThemeFile($folder, 'index.blade.php')],
            'readme' => ['nullable', new ThemeFile($folder)],
            'changelog' => ['nullable', new ThemeFile($folder)],
            'screenshot' => ['nullable', new ThemeFile($folder)],
            'options' => 'present|array',
            'options.*.name' => 'required|string|ascii',
            'options.*.label' => 'required|string|ascii',
            'options.*.type' => 'required|string|ascii',
            'options.*.value' => 'present|nullable|ascii',
            'options.*.tab' => 'present|nullable|ascii',
        ], [
            'author.regex' => 'The author is invalid. Format: Name <example@example.com>',
        ]);
    }

    private function createTheme(array $config)
    {
        return Template::create([
            'name' => $config['name'],
            'slug' => $config['slug'],
            'author' => $config['author'] ?? null,
            'version' => $config['version'] ?? null,
            'description' => $config['description'] ?? null,
            'readme' => $config['readme'] ?? null,
            'screenshot' => $config['screenshot'] != null
                ? str_contains($config['screenshot'], 'http')
                    ? $config['screenshot']
                    : "themes/{$config['slug']}/" . ($config['asset'] ? $config['asset'] . '/' : '') . $config['screenshot']
                : null,
            'options' => $config['options'] ?? [],
            'layout' => $config['layout'] ?? null,
            'asset' => $config['asset'] ?? null,
            'changelog' => $config['changelog'] ?? null,
            'is_new' => true
        ]);
    }
}
