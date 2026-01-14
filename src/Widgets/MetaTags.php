<?php

namespace Amplify\System\Cms\Widgets;

use Amplify\System\Backend\Models\Product;
use Amplify\Widget\Abstracts\BaseComponent;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;

/**
 * @class MetaTags
 */
class MetaTags extends BaseComponent
{
    private array $uniqueTags;

    public array $tags = [];

    /**
     * Create a new component instance.
     */
    public function __construct(array $tags = [])
    {
        parent::__construct();

        $this->tags = [];

        $this->uniqueTags = $tags;

        $this->uniqueTags[] = ['name' => 'csrf-token', 'content' => csrf_token()];
        $this->uniqueTags[] = ['id' => 'check-authenticated', 'data-link' => customer_check() ? 'true' : 'false'];
        $this->uniqueTags[] = ['name' => 'authenticated', 'content' => customer_check() ? 'true' : 'false'];
    }

    /**
     * Whether the component should be rendered
     */
    public function shouldRender(): bool
    {
        return true;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $commonTags = Cache::remember('site-common-tags', 24 * HOUR, function () {
            return $this->loadCommonMetaTags();
        });

        //Page Specific Cache
        $pageTags = [];
        if ($page = store('dynamicPageModel', false)) {
            $pageTags = $this->loadPageMetaTags($page);
            // Cache::remember("site-{$page->page_type}", 24 * HOUR, function () use ($page) {
            //     return $this->loadPageMetaTags($page);
            // });
        }

        $this->tags = array_merge($this->tags, $commonTags, $pageTags, $this->fillSystemTags(), $this->uniqueTags);

        return view('cms::meta-tags');
    }

    private function fillSystemTags(): array
    {
        return [
            ['id' => 'quick-order-link', 'data-link' => route('frontend.carts.store')],
            ['id' => 'check-customer-list-name-link', 'data-link' => route('frontend.order.order-list.check-name-availability')],
            ['id' => 'add-product-to-customer-list-link', 'data-link' => route('frontend.order-list.add-product')],
        ];

    }

    private function loadCommonMetaTags(): array
    {
        return [
            ['name' => 'copyright', 'content' => config('app.name')],
            ['name' => 'language', 'content' => strtoupper(config('app.locale'))],
            ['name' => 'Classification', 'content' => config('app.name')],
            ['name' => 'author', 'content' => 'Hafijul Islam, ' . config('amplify.cms.email', 'hafijul233@gmail.com')],
            ['name' => 'reply-to', 'content' => config('mail.admin_email', 'hafijul233@gmail.com')],
            ['name' => 'owner', 'content' => config('app.name')],
            ['name' => 'url', 'content' => config('app.url')],
            ['name' => 'identifier-URL', 'content' => config('app.url')],
            ['name' => 'coverage', 'content' => 'Worldwide'],
            ['name' => 'distribution', 'content' => 'Global'],
            ['name' => 'rating', 'content' => 'General'],
            ['name' => 'target', 'content' => 'all'],
            ['name' => 'HandheldFriendly', 'content' => 'True'],
            ['name' => 'date', 'content' => now()->format('M. D, Y')],
            ['name' => 'search_date', 'content' => now()->format('Y-m-d')],
        ];
    }

    private function loadPageMetaTags($page): array
    {
        $meta_keywords = ($page->meta_key ?? '');
        $meta_description = implode(", ", [$page->meta_description ?? '', $page->name, $page->breadcrumb_title, $page->title]);

        $og_list = [];
        if ($page->page_type === 'single_product') {
            $product = Product::find(store()->productModel->id);
            $meta_description = $product->meta_description ?? $product->description;
            $meta_keywords = $product->meta_keywords;

            $og_list[] = [
                ['property' => 'og:title', 'content' => $product->product_name],
                ['property' => 'og:description', 'content' => $product->og_description ?? $meta_description],
                ['property' => 'og:type', 'content' => 'product'],
                ['property' => 'og:url', 'content' => frontendSingleProductURL($product)],
                ['property' => 'og:image', 'content' => asset($product->thumbnail)],
            ];
        }

        $meta_list = [
            ['name' => 'keywords', 'content' => $meta_keywords],
            ['name' => 'description', 'content' => $meta_description],
            ['name' => 'pagename', 'content' => ($page->name ?? '')],
            ['name' => 'category', 'content' => $this->getPageTypeLabel($page->page_type)],
            ['name' => 'pageKey', 'content' => ($page->slug ?? '#')],
            ['name' => 'revised', 'content' => $page->updated_at->format('l, F dS, Y, h:i a')]
        ];
        return array_merge($meta_list, ...$og_list);
    }

    private function getPageTypeLabel(string $type)
    {
        $pageType = collect(config('amplify.cms.page_types'))->firstWhere('code', $type);

        return ($pageType) ? $pageType['label'] : 'Static';
    }

    public function arrayToHtmlAttributes(array $data): string
    {
        $html = '';

        foreach ($data as $attribute => $value) {
            $html .= (' ' . str_replace('.', '-', $attribute));
            $html .= ('="' . str_replace('"', "'", ($value ?? '')) . '"');
        }

        return $html;
    }
}
