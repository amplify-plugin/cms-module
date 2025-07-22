<?php

namespace Amplify\System\Cms\Http\Controllers;

use Amplify\System\Cms\Http\Requests\SitemapRequest;
use Amplify\System\Cms\Models\Menu;
use Amplify\System\Cms\Models\Page;
use Amplify\System\Cms\Models\Sitemap;
use Amplify\System\Abstracts\BackpackCustomCrudController;
use App\Models\Product;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;
use Illuminate\Support\Facades\Route;

/**
 * Class SitemapCrudController
 *
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class SitemapCrudController extends BackpackCustomCrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\Pro\Http\Controllers\Operations\FetchOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(Sitemap::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/sitemap');
        CRUD::setEntityNameStrings('sitemap', 'sitemaps');
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
        CRUD::column('id')->label('#');
        CRUD::column('mappable_id')
            ->type('custom_html')
            ->value(function ($sitemap) {
                $sitemapOwner = $sitemap->mappable;
                if ($sitemapOwner != null) {
                    return '<a href="'.route(strtolower(class_basename($sitemapOwner)).'.show', $sitemapOwner->id).'" class="font-weight-bold text-decoration-none">'.class_basename($sitemapOwner).': '.$sitemapOwner->id.'</a>';
                }

                return '-';
            });
        CRUD::column('priority');
        CRUD::column('changefreq')->type('select_from_array')->options(Sitemap::CHANGE_FREQ);
        CRUD::column('location');
        CRUD::column('updated_at');

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */
    }

    protected function setupCustomRoutes($segment, $routeName, $controller)
    {
        Route::get($segment.'/resolve-full-url', [
            'as' => $routeName.'.resolveFullUrl',
            'uses' => $controller.'@resolveFullUrl',
        ]);
    }

    public function fetchProduct()
    {
        return $this->fetch([
            'model' => Product::class,
            'query' => fn ($model) => $model->orderBy('product_name'),
        ]);
    }

    public function fetchPage()
    {
        return $this->fetch([
            'model' => Page::class,
            'query' => fn ($model) => $model->orderBy('name'),
        ]);
    }

    public function fetchMenu()
    {
        return $this->fetch([
            'model' => Menu::class,
            'query' => fn ($model) => $model->orderBy('name'),
        ]);
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     *
     * @return void
     *
     * @throws \Exception
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(SitemapRequest::class);

        Widget::add()->type('script')->content(asset('assets/js/admin/forms/sitemap.js'));

        CRUD::field('mappable')
            ->type('relationship')
            ->tab('URL')
//            ->addMorphOption(Menu::class, 'Menu', [
//                [
//                    'data_source' => backpack_url('sitemap/fetch/menu'),
//                    'ajax' => true,
//                    'minimum_input_length' => 2,
//                    'method' => 'POST',
//                    'attribute' => 'name',
//                ]
//            ])
            ->addMorphOption(Product::class, 'Product', [
                [
                    'data_source' => backpack_url('sitemap/fetch/product'),
                    'ajax' => true,
                    'minimum_input_length' => 2,
                    'method' => 'POST',
                    'attribute' => 'product_name',
                ],
            ])
            ->addMorphOption(Page::class, 'Page', [
                [
                    'data_source' => backpack_url('sitemap/fetch/page'),
                    'ajax' => true,
                    'minimum_input_length' => 2,
                    'method' => 'POST',
                    'attribute' => 'name',
                ],
            ]);
        CRUD::field('location')
            ->tab('URL');
        CRUD::field('changefreq')
            ->type('select_from_array')
            ->options(Sitemap::CHANGE_FREQ)->tab('URL')->value('monthly');
        CRUD::field('priority')
            ->tab('URL')
            ->type('range')->attributes(['min' => 0, 'max' => 1, 'step' => '0.1']);
        CRUD::field('sitemapTags')->tab('Tags')->label('Tags')->subfields([
            [
                'name' => 'type',
                'type' => 'select_from_array',
                'options' => [
                    'image' => 'Image',
                    'video' => 'Video',
                ],
                'value' => 'image',
                'label' => 'Type',
            ],
            [
                'name' => 'location',
                'type' => 'text',
                'label' => 'Image Location',
            ],
            [
                'name' => 'title',
                'type' => 'text',
                'label' => 'Video Title',
                'value' => '',
            ],
            [
                'name' => 'description',
                'type' => 'textarea',
                'label' => 'Video Description',
                'value' => '',
            ],
            [
                'name' => 'thumbnail_loc',
                'type' => 'url',
                'label' => 'Video Thumbnail URL',
            ],
            [
                'name' => 'content_loc',
                'type' => 'url',
                'label' => 'Video Content URL',
                'value' => '',
            ],
            [
                'name' => 'player_loc',
                'type' => 'url',
                'label' => 'Video Player URL',
                'value' => '',
            ],
            [
                'name' => 'publication_date',
                'type' => 'hidden',
                'value' => now()->format('c'),
            ],
            [
                'name' => 'family_friendly',
                'type' => 'hidden',
                'value' => 'yes',
            ],
            [
                'name' => 'live',
                'type' => 'hidden',
                'value' => 'no',
            ],

        ]);
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     *
     * @return void
     *
     * @throws \Exception
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    protected function setupShowOperation()
    {
        CRUD::column('id')->label('#');
        CRUD::column('mappable_id')
            ->type('custom_html')
            ->value(function ($sitemap) {
                $sitemapOwner = $sitemap->mappable;
                if ($sitemapOwner != null) {
                    return '<a href="'.route(strtolower(class_basename($sitemapOwner)).'.show', $sitemapOwner->id).'" class="font-weight-bold text-decoration-none">'.class_basename($sitemapOwner).': '.$sitemapOwner->id.'</a>';
                }

                return '-';
            });
        CRUD::column('priority');
        CRUD::column('changefreq')->type('select_from_array')->options(Sitemap::CHANGE_FREQ);
        CRUD::column('location');
        CRUD::column('updated_at');
    }
}
