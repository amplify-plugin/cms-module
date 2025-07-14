<?php

namespace Amplify\System\Cms\Http\Controllers;

use Amplify\System\Cms\Http\Requests\PageRequest;
use Amplify\System\Cms\Models\Banner;
use Amplify\System\Cms\Models\BannerZone;
use Amplify\System\Cms\Models\Content;
use Amplify\System\Cms\Models\Form;
use Amplify\System\Cms\Models\MenuGroup;
use Amplify\System\Cms\Models\Page;
use Amplify\Marketing\Models\MerchandisingZone;
use Amplify\System\Abstracts\BackpackCustomCrudController;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Permission;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ReorderOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanel;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\Pro\Http\Controllers\Operations\FetchOperation;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

/**
 * Class PageCrudController
 *
 * @property-read CrudPanel $crud
 */
class PageCrudController extends BackpackCustomCrudController
{
    use CreateOperation;
    use DeleteOperation;
    use FetchOperation;
    use ListOperation;
    use ReorderOperation;
    use ShowOperation;
    use UpdateOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(Page::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/page');
        CRUD::setEntityNameStrings('page', 'pages');
        $this->crud->enableBulkActions();
        $this->crud->addButton('top', 'bulk_page_status', 'view', 'crud::buttons.bulk_page_status');
    }

    protected function setupCustomRoutes($segment, $routeName, $controller)
    {
        Route::get($segment.'/get-widget', [
            'as' => $routeName.'.getWidget',
            'uses' => $controller.'@getWidget',
        ]);

        Route::get($segment.'/get-widget-data/{field}', [
            'as' => $routeName.'.get-widget-data',
            'uses' => $controller.'@getWidgetData',
        ]);
        Route::get($segment.'/{page}/check-url-param', [
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
        CRUD::addFilter(
            [
                'name' => 'is_new',
                'type' => 'dropdown',
                'label' => 'Is New',
            ],
            function () {
                return [
                    '0' => 'No',
                    '1' => 'Yes',
                ];
            },
            function ($value) {
                // if the filter is active
                $this->crud->addClause('where', 'is_new', '=', $value);
            }
        );

        CRUD::addFilter([
            'name' => 'is_published',
            'label' => 'Published',
            'type' => 'dropdown',
        ],
            [
                1 => 'Yes',
                0 => 'No',

            ],
            function ($value) {
                $this->crud->addClause('where', 'is_published', '=', $value);
            });
        CRUD::addFilter(
            [
                'name' => 'page_type',
                'type' => 'select2_multiple',
                'label' => 'Type',
            ],
            function () {
                $options = [];
                collect(config('amplify.cms.page_types'))->each(function ($item) use (&$options) {
                    $options[$item['code']] = $item['label'];
                });

                return $options;
            },
            function ($value) {
                $value = json_decode($value, true);
                $this->crud->addClause('whereIn', 'page_type', $value);
            }
        );

        CRUD::addColumn([
            'name' => 'id',
            'type' => 'custom_html',
            'value' => function ($model) {
                if ($model->is_new === 1 && ! $model->is_updated) {
                    return $model->id.' <sup class="badge text-danger px-0">New</sup>';
                } elseif ($model->is_updated) {
                    return $model->id.' <sup class="badge text-warning px-0">Updated</sup>';
                } else {
                    return $model->id;
                }
            },
        ]);

        CRUD::addColumn([
            'name' => 'name',
            'label' => 'Name',
        ]);

        CRUD::addColumn([
            'name' => 'url',
            'type' => 'custom_html',
            'value' => function (Page $model) {
                return $model->full_url_without_substitute;
            },
        ]);

        CRUD::addColumn([
            'name' => 'is_published',
            'type' => 'boolean',
            'label' => 'Published',
        ]);

        $this->crud
            ->addClause('pageList')
            ->orderBy('id', 'desc');

        $this->crud->addButtonFromView('line', 'preview-page', 'preview-page', 'ending');
        $this->crud->addButtonFromView('line', 'publish', 'publish-page', 'beginning');
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
        CRUD::setValidation(PageRequest::class);
        $activeTemplate = template();
        $this->crud->setCreateContentClass('col-lg-12');
        $this->data['page'] = $this->crud->model->find(request()->id);
        $this->data['active_template'] = $activeTemplate;
        $this->crud->setCreateView('crud::pages.page.create');

        CRUD::addField([
            'name' => 'template_id',
            'label' => 'Template',
        ]);
        CRUD::addField([
            'name' => 'name',
            'label' => 'Page Name',
        ]);
        CRUD::addField([
            'name' => 'slug',
            'label' => 'Page Slug',
        ]);
        CRUD::addField([
            'name' => 'page_type',
            'label' => 'Page Type',
        ]);
        CRUD::addField([
            'name' => 'content',
            'label' => 'Page Content',
        ]);
        CRUD::addField([
            'name' => 'meta_description',
            'label' => 'Meta Description',
        ]);
        CRUD::addField([
            'name' => 'meta_image_path',
            'label' => 'Meta Image Path',
        ]);
        CRUD::addField([
            'name' => 'meta_key',
            'label' => 'Meta Keywords',
        ]);
        CRUD::addField([
            'name' => 'title',
            'label' => 'Title',
        ]);
        CRUD::addField([
            'name' => 'middleware',
        ]);
        CRUD::addField([
            'name' => 'is_new',
        ]);
        CRUD::addField([
            'name' => 'is_updated',
        ]);
        CRUD::addField([
            'name' => 'has_breadcrumb',
        ]);
        CRUD::addField([
            'name' => 'breadcrumb_title',
        ]);
        CRUD::addField([
            'name' => 'has_footer',
        ]);
        CRUD::addField([
            'name' => 'styles',
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
        // Cache clear on update
        CRUD::getModel()->updating(function ($entry) {
            Cache::flush();
        });

        $this->crud->setUpdateContentClass('col-lg-12');
        $this->crud->setUpdateView('crud::pages.page.create');
        $this->setupCreateOperation();
    }

    protected function setupShowOperation()
    {

        CRUD::addColumns([
            [
                'name' => 'template_id',
                'label' => 'Template',
                'type' => 'relationship',
            ],
            [
                'name' => 'name',
                'label' => 'Name',
                'type' => 'text',
            ],
            [
                'name' => 'slug',
                'label' => 'Slug',
                'type' => 'text',
            ],
            [
                'name' => 'page_type',
                'label' => 'page Type',
                'type' => 'select_from_array',
                'options' => Page::PAGE_TYPES,
            ],
            [
                'name' => 'parent_id',
                'label' => 'Parent',
                'type' => 'relationship',
            ],
            [
                'name' => 'middleware',
                'label' => 'Middleware',
                'type' => 'select_from_array',
                'options' => ['customer' => 'Customer', 'guest' => 'Public', 'guest:customer' => 'Guest'],
            ],
            [
                'name' => 'is_published',
                'label' => 'Published',
                'type' => 'boolean',
            ],
            [
                'name' => 'meta_description',
                'label' => 'Meta Description',
                'type' => 'textarea',
            ],
            [
                'name' => 'meta_key',
                'label' => 'Meta Key',
                'type' => 'textarea',
            ],
            [
                'name' => 'title',
                'label' => 'Title',
                'type' => 'text',
            ],
        ]);
    }

    protected function setupReorderOperation()
    {
        CRUD::set('reorder.label', 'name');
        CRUD::set('reorder.max_level', 5);
    }

    /**
     * bulkPublish
     *
     * @param  mixed  $request
     * @return void
     */
    public function bulkStatus(Request $request)
    {
        // CRUD::hasAccessOrFail('status');
        $selectedItems = $request->entries;
        if (! empty($selectedItems)) {
            return Page::whereIn('id', $selectedItems)->where('is_published', '!=', $request->status)->update(['is_published' => $request->status]);
        }
    }

    public function fetchPageSlug(): JsonResponse
    {
        $slug = request()->slug;
        $id = request()->id ?? null;

        return response()->json([
            'slug' => getPageSlug($slug, $id),
        ]);
    }

    public function fetchConvertObjectToCode(Request $request): JsonResponse
    {
        try {
            $pageType = $request->input('page_type', 'static_page');

            $widget = json_decode($request->input('code', '[]'), true);

            if (empty($widget)) {
                throw new Exception('Invalid widget option selected');
            }

            if (! empty($widget['model']) && ! in_array('static_page', $widget['model'])) {

                if (! in_array($pageType, $widget['model'])) {

                    $pageTypeConfig = collect(config('amplify.cms.page_types'))->firstWhere('code', '=', $pageType);

                    $label = $pageTypeConfig['label'];

                    throw new Exception("This widget will not operate with `{$label}` page type.");
                }
            }

            return response()->json([
                'type' => 'success',
                'message' => 'Widget code generated successfully!',
                'code' => $this->makeConversionFromObjectToCode(
                    json_decode($request->input('code'), false, 512, JSON_THROW_ON_ERROR),
                    false
                ),
            ]);

        } catch (\Exception $exception) {
            return response()->json([
                'type' => 'error',
                'message' => $exception->getMessage(),
                'code' => '',
            ], 500);
        }
    }

    private function makeConversionFromObjectToCode($tag, bool $isNestedTag = false): string
    {
        /* Initializing some variables */
        $finalCode = '';
        $addTabSpaceIfNested = $isNestedTag
            ? "\t"
            : '';

        /* Generate starting 'start tag' */
        $finalCode .= $addTabSpaceIfNested.'<'.$tag->name;
        /* Generate attributes */
        if (count($tag->{'@attributes'}) > 0) {
            foreach ($tag->{'@attributes'} as $attribute) {
                if (in_array($attribute->type, ['dropdown', 'content-dropdown', 'merchandises-dropdown'])) {
                    $attribute->value = $attribute->value->id ?? '';
                }

                if ($attribute->type == 'menugroup-dropdown') {
                    $attribute->value = $attribute->value->short_code;
                }

                if ($attribute->type == 'page-dropdown') {
                    $attribute->value = $attribute->value->id;
                }

                if ($attribute->type == 'category-dropdown') {
                    $attribute->value = collect($attribute->value)->pluck('id')->implode(',');
                }

                if ($attribute->type == 'banner-zone') {
                    $attribute->value = $attribute->value->code;
                }

                if ($attribute->type == 'form-dropdown') {
                    $attribute->value = $attribute->value->code;
                }

                if ($attribute->type == 'banner-code') {
                    $attribute->value = optional($attribute->value)->code;
                }

                if ($attribute->type == 'permission-dropdown') {
                    $attribute->value = optional($attribute->value)->name;
                }

                if ($attribute->name !== 'repeatable') {
                    $finalCode .= "\n\t".$addTabSpaceIfNested.$attribute->name."='"
                        .$this->setAttributeValue($attribute->type, $attribute->value)."'";
                }
            }
        }

        /* Generate ending 'start tag' */
        $finalCode .= '>'.PHP_EOL;

        /* Generate inside tag text */
        if (! empty($tag->{'@inside'})) {
            $finalCode .= $addTabSpaceIfNested."\t".$tag->{'@inside'}.PHP_EOL;
        }

        /* Generate nested tags */
        if (! empty($tag->{'@nestedItems'})) {
            foreach ($tag->{'@nestedItems'} as $child) {
                $finalCode .= $this->makeConversionFromObjectToCode($child, true);
            }
        }

        /* Generate 'end tag' */
        $finalCode .= $addTabSpaceIfNested.'</'.$tag->name.'>'.PHP_EOL;

        return $finalCode;
    }

    /**
     * @return bool|string
     */
    private function setAttributeValue($type, $value)
    {
        if ($type === 'boolean') {
            return empty($value) || $value === 'false'
                ? 'false'
                : 'true';
        } elseif ($type === 'array') {
            return json_encode($value);
        }

        return $value;
    }

    public function publish(Page $page, $status)
    {
        $response = [];
        try {
            $page->is_published = $status;

            $page->save();

            $response = [
                'type' => 'success',
                'message' => ($status == 1) ? 'Page Published Successfully.' : 'Page Drafted Successfully.',
            ];

        } catch (Exception $exception) {
            $response = [
                'type' => 'error',
                'message' => $exception->getMessage(),
            ];
        } finally {
            return response()->json($response);
        }
    }

    public function getWidget()
    {
        $widgetCollection = collect(config('amplify.widget'));

        $clientUsableWidgetCollection = $widgetCollection
            ->filter(function ($widget) {
                return ($widget['internal'] == false)
                    && ($widget['@client'] == null || $widget['@client'] == config('amplify.basic.client_code'));
            })->map(function ($item) {
                $item['name'] = 'x-'.$item['name'];

                return $item;
            });

        return response()->json($clientUsableWidgetCollection, 200);
    }

    public function getWidgetData(string $field, Request $request)
    {
        return match ($field) {
            'merchandising_zones' => MerchandisingZone::get(['id', 'name'])->toArray(),

            'banner_item_codes' => Banner::get(['id', 'name', 'code'])->toArray(),

            'banner_zones' => BannerZone::get(['id', 'code', 'name', 'fetch_data_from_easyask'])->toArray(),

            'content_lists' => Content::where(['status' => 1, 'is_approved' => 1])->get(),

            'category_lists' => Category::has('child')->get()
                ->sortBy(fn ($item) => $item->getLabelAttribute(), SORT_NATURAL | SORT_FLAG_CASE)
                ->values()
                ->map(fn ($item) => [
                    'id' => $item->id,
                    'value' => $item->getLabelAttribute(),
                ])
                ->toArray(),

            'menu_group_lists' => MenuGroup::where('is_reserved', false)->get(['id', 'name', 'short_code'])->toArray(),

            'page_lists' => $this->preparePageTypes(),

            'form_lists' => Form::whereEnabled(true)->get(['name', 'code'])->toArray(),

            'permissions' => Permission::whereGuardName(Contact::AUTH_GUARD)->get(['id', 'name'])->toArray(),
        };
    }

    private function preparePageTypes(): array
    {
        $pageTypes = collect(config('amplify.cms.page_types', []));

        $pages = [];

        Page::select('id', 'name', 'page_type')->get()->each(function (Page $page) use (&$pages, $pageTypes) {

            $pageType = $pageTypes->firstWhere('code', '', $page->page_type);

            if ($pageType) {
                $pages[$page->page_type]['name'] = $pageType['label'];
                $pages[$page->page_type]['pages'][] = ['id' => $page->id, 'name' => $page->name];
            } else {
                $pages['static_page']['name'] = 'Static';
                $pages['static_page']['pages'][] = ['id' => $page->id, 'name' => $page->name];
            }
        });

        return array_values($pages);
    }

    public function checkUrlParam(Page $page): JsonResponse
    {
        $jsonResponse = [
            'url' => url($page->slug),
            'params' => [],
            'type' => 'success',
            'message' => 'Redirecting to Web Page.',
        ];

        $jsonResponse['url'] = $page->full_url_without_substitute;

        $jsonResponse['params'] = get_uri_parameter($jsonResponse['url']);

        if (! empty($jsonResponse['params'])) {
            $jsonResponse['type'] = 'warning';
            $jsonResponse['message'] = 'Please Fill the required parameters';
        }

        return response()->json($jsonResponse, ($jsonResponse['type'] == 'error') ? 500 : 200);
    }
}
