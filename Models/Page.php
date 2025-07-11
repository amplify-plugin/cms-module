<?php

namespace Amplify\System\Cms\Models;

use Amplify\System\Sayt\Classes\BreadCrumbTrail;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Routing\Route;
use OwenIt\Auditing\Contracts\Auditable;

class Page extends Model implements Auditable
{
    use CrudTrait, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'pages';

    protected $casts = [
        'middleware' => 'array',
        'has_breadcrumb' => 'bool',
        'has_footer' => 'bool',
    ];

    protected $guarded = ['id'];

    // protected $hidden = [];

    /**
     * !!! IMPORTANT NOTICE !!!
     *
     * Follow this instruction when adding new page type
     *
     *  1. Type writing type is `snake case`
     *  2. Add a key like {page_type_value}_id this in config/amplify/frontend.php
     *    Ex: Page Type `single_product` then the key will be `single_product_id`
     *  3. Add this page type value in \App\Http\Controllers\DynamicPageLoadController
     *    `renderDynamicPage` method switch case
     *  4. create a static route this page type
     *  5. generatePageInstance
     */
    public const PAGE_TYPES = [
        'home' => 'Home',
        'shop' => 'Shop',
        'single_product' => 'Single Product',
        'cart_page' => 'Cart Page',
        'contact' => 'Contact', // depreciated
        'shop_category' => 'Shop Category',
        'faq' => 'FAQ', // depreciated
        'quick_order' => 'Quick Order',
        'order' => 'Order',
        'order_detail' => 'Order Details',
        'draft_order' => 'Draft Order',
        'draft_order_detail' => 'Draft Order Details',
        'quotation' => 'Quotation',
        'quotation_detail' => 'Quotation Details',
        'favourite' => 'Customer List',
        'favourite_detail' => 'Customer List Details',
        'static_page' => 'Static',
        'content' => 'Content', // depreciated
        'dashboard' => 'Customer Dashboard',
        'message' => 'Message',
        'payment' => 'Payment',
        'login' => 'Customer Login',
        'registration' => 'Customer Registration',
        'forgot_password' => 'Customer Forgot Password',
        'password_reset' => 'Password Reset',
        'invoice' => 'Invoice',
        'invoice_detail' => 'Invoice Details',
        'ticket' => 'Tickets List',
        'ticket_open' => 'Open Ticket',
        'ticket_detail' => 'Ticket Details',
        'force_password_reset' => 'Force Password Update',
        'shop_by_catalog' => 'Shop By Catalog', // depreciated
        'order_rule' => 'Order Rule List',
        'order_waiting_approval' => 'Order Waiting Approval',
        'event_detail' => 'Event Details',
        'custom_product' => 'Custom Product',
        'shipping_address' => 'Shipping Address',
    ];

    protected $appends = ['label'];

    public const CUSTOMER_MIDDLEWARE = [
        'guest',
        'guest:customer',
        'customers',
    ];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /**
     * return button html to preview the page
     */
    public function previewPage(): string
    {
        return '<a class="btn btn-sm btn-link" href="'.route('dynamic-page', $this->slug)
            .'" data-toggle="tooltip" title="Preview page" target="_blank"><i class="las la-retweet"></i> Preview Page</a>';
    }

    public static function getPages($page_type, $template_id = null)
    {
        config('database.connections.mysql.strict', false);

        Page::when(is_array($page_type), function ($query) use ($page_type) {
            return $query->whereIn('page_type', $page_type);
        })
            ->when(is_string($page_type), function ($query) use ($page_type) {
                return $query->where('page_type', $page_type);
            })
            ->orderBy('name')
            ->get()
            ->each(function ($page) use (&$pages) {
                $pages[$page->page_type][] = [
                    'name' => $page->name,
                    'id' => $page->id,
                ];
            });

        config('database.connections.mysql.strict', true);

        return $pages ?? [];
    }

    public static function getConfigurableTypes()
    {
        $excluded = config('amplify.frontend.excluded_page_types', []);

        $page_types = config('amplify.cms.page_types');

        return collect($page_types)->filter(function ($type) use ($excluded) {
            return in_array($type['code'], $excluded) === false;
        })->toArray();
    }

    /**
     * Guess current active page model from slug parameter
     */
    public static function guessCurrentPage(): ?Page
    {
        $slug = null;

        // Dynamic Route
        if (request()->route() !== null && in_array(request()->route()->getName(),
            ['dynamic-page', 'frontend.dynamic-route'])) {
            $slug = request()->route('slug');
        } // parse the url for page slug
        else {

            $directories = explode('/', request()->path());

            if (isset($directories[0])) {
                $slug = $directories[0];
            }
        }

        if (! $slug) {
            return null;
        }

        if ($page = Page::whereSlug($slug)->first()) {
            return $page;
        }

        return null;
    }

    /**
     * Guess current active page model from slug parameter
     *
     * @throws \ErrorException
     */
    public static function guessCurrentPageTitle(): ?string
    {
        if ($page = store('dynamicPageModel')) {
            return match ($page->page_type) {
                'shop' => self::getTitleFromEasyAskResponse($page),
                'single_product' => self::getProductNameFromEasyAskResponse($page),
                default => $page->name
            };
        }

        return 'No Name';
    }

    private static function getTitleFromEasyAskResponse($page)
    {
        $easyAskResult = store()->eaProductsData;

        $breadcrumbTrails = $easyAskResult?->getBreadCrumbTrail() ?? new BreadCrumbTrail(null);

        if ($navigateNode = collect($breadcrumbTrails->getSearchPath())->last()) {
            return $navigateNode->getEnglishName();
        }

        return $page->name;
    }

    private static function getProductNameFromEasyAskResponse($page)
    {
        $products = store()->eaProductDetail?->getProducts() ?? [];

        if (! empty($products)) {
            if ($result = $products[0]) {
                return $result->Product_Name ?? $result?->Model_Name ?? '';
            }
        }

        return $page->name;
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function template()
    {
        return $this->belongsTo(Template::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopePageList($query)
    {
        return $query->where('default_page', 0);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', '=', true);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */
    public function getLabelAttribute()
    {
        return $this->attributes['label'] = $this->name;
    }

    public function getMetaTagsAttribute()
    {
        $metaInfo = [];

        if ($this->meta_image_path) {
            $metaInfo[] = [
                'content' => $this->meta_image_path ?? '',
                'property' => 'og:image',
                'name' => 'image',
            ];
        }

        if ($this->title) {
            $metaInfo[] = [
                'content' => $this->title ?? '',
                'property' => 'og:title',
                'name' => 'title',
            ];
        }

        if ($this->meta_key) {
            $metaInfo[] = [
                'content' => $this->meta_key ?? '',
                'property' => 'og:keywords',
                'name' => 'keywords',
            ];
        }

        if ($this->meta_description) {
            $metaInfo[] = [
                'content' => $this->meta_description ?? '',
                'property' => 'og:description',
                'name' => 'description',
            ];
        }

        $metaInfo[] = [
            'content' => url()->current(),
            'property' => 'og:url',
            'name' => 'url',
        ];

        return $metaInfo;
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */

    public function getFullUrlAttribute(): \Illuminate\Foundation\Application|string|UrlGenerator|Application
    {
        $pageType = collect(config('amplify.cms.page_types', []))
            ->filter(function ($type) {
                return $type['code'] == $this->page_type;
            })->first();

        if (is_array($pageType) && isset($pageType['code']) && $pageType['code'] == 'custom_product') {
            $slug = $this->slug;

            return url("custom/{$slug}");
        }

        if (is_array($pageType) && isset($pageType['url']['type']) && $pageType['url']['type'] == 'route') {

            $url = \Illuminate\Support\Facades\Route::getRoutes()->getByName($pageType['url']['name'])?->uri() ?? '#';
            $params = get_uri_parameter($url);
            $params = array_combine($params, $params);
            $params = array_map(function ($item) {
                return stripos($item, '?') ? '' : 'home';
            }, $params);

            return url(str_replace(array_keys($params), array_values($params), $url));
        }

        if (is_array($pageType) && isset($pageType['code']) && $pageType['code'] == 'static_page') {
            return url($this->slug);
        }

        return route('frontend.dynamic-route', $this->slug);
    }

    public function getFullUrlWithoutSubstituteAttribute(
    ): \Illuminate\Foundation\Application|string|UrlGenerator|Application {
        $pageType = collect(config('amplify.cms.page_types', []))
            ->filter(function ($type) {
                return $type['code'] == $this->page_type;
            })->first();

        if (is_array($pageType) && isset($pageType['url']['type']) && $pageType['url']['type'] == 'route') {
            return url(\Illuminate\Support\Facades\Route::getRoutes()->getByName($pageType['url']['name'])?->uri() ?? '#');
        }

        if (is_array($pageType) && isset($pageType['code']) && $pageType['code'] == 'static_page') {
            return url($this->slug);
        }

        return route('frontend.dynamic-route', $this->slug);
    }
}
