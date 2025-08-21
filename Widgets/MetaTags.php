<?php

namespace Amplify\System\Cms\Widgets;

use Amplify\System\Cms\Models\Page;
use Amplify\Widget\Abstracts\BaseComponent;
use Closure;
use ErrorException;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

/**
 * @class MetaTags
 */
class MetaTags extends BaseComponent
{
    /**
     * @var array
     */
    public $options;

    public array $tags;

    private array $uniqueTags;

    /**
     * Create a new component instance.
     */
    public function __construct(array $tags = [])
    {
        $this->options = Config::get('amplify.widget.'.__CLASS__, []);

        $this->tags = [];

        $this->uniqueTags = $tags;

        $this->uniqueTags[] = ['name' => 'csrf-token', 'content' => csrf_token()];
        $this->uniqueTags[] = ['id' => 'check-authenticated', 'data-link' => customer_check() ? 'true' : 'false'];
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
     *
     * @throws ErrorException
     */
    public function render(): View|Closure|string
    {
        $key = Str::lower(Str::slug(request()->path()));
        if (! Cache::has($key)) {

            $this->loadCommonMetaTags()
                ->loadPageMetaTags()
                ->fillSystemTags();

            Cache::put($key, $this->tags, 24 * HOUR);
        }

        $this->tags = Cache::get($key);

        array_walk($this->uniqueTags, function ($item) {
            $this->tags[] = $item;
        });

        return view('cms::meta-tags');
    }

    private function fillSystemTags(): void
    {
        array_push(
            $this->tags,
            ['id' => 'quick-order-link', 'data-link' => route('frontend.order.quick-order-add-to-order')],
            ['id' => 'check-customer-list-name-link', 'data-link' => route('frontend.order.order-list.check-name-availability')],
            ['id' => 'add-product-to-customer-list-link', 'data-link' => route('frontend.order-list.add-product')],
            ['id' => 'add-product-to-customer-list-link', 'data-link' => route('frontend.order-list.add-product')],
        );

    }

    private function loadCommonMetaTags(): static
    {
        array_push($this->tags,
            ['name' => 'copyright', 'content' => config('app.name')],
            ['name' => 'language', 'content' => strtoupper(config('app.locale'))],
            ['name' => 'Classification', 'content' => config('app.name')],
            ['name' => 'author', 'content' => 'Hafijul Islam, '.config('amplify.cms.email', 'hafijul233@gmail.com')],
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
        );

        return $this;
    }

    /**
     * @throws ErrorException
     */
    private function loadPageMetaTags(): static
    {
        $page = store('dynamicPageModel');

        if ($page) {

            array_push(
                $this->tags,
                ['name' => 'keywords', 'content' => ($page->meta_key ?? '')],
                ['name' => 'description', 'content' => ($page->meta_description ?? '')],
                ['name' => 'pagename', 'content' => ($page->name ?? '')],
                ['name' => 'category', 'content' => $this->getPageTypeLabel($page->page_type)],
                ['name' => 'pageKey', 'content' => ($page->slug ?? '#')],
                ['name' => 'revised', 'content' => $page->updated_at->format('l, F dS, Y, h:i a')],
            );

            if (in_array($page->page_type, ['shop', 'single_product'])) {
                $this->loadProductMetaTags($page);
            }
        }

        return $this;
    }

    private function loadProductMetaTags(Page $page): void
    {
        array_push(
            $this->tags,
            ['name' => 'keywords', 'content' => ($page->meta_key ?? '')],
            ['name' => 'description', 'content' => ($page->meta_description ?? '')],
            ['name' => 'pagename', 'content' => ($page->name ?? '')],
            ['name' => 'category', 'content' => $this->getPageTypeLabel($page->page_type)],
            ['name' => 'pageKey', 'content' => ($page->slug ?? '#')],
            ['name' => 'revised', 'content' => $page->updated_at->format('l, F dS, Y, h:i a')],
        );

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
            $html .= (' '.str_replace('.', '-', $attribute));
            $html .= ('="'.str_replace('"', "'", ($value ?? '')).'"');
        }

        return $html;
    }
}
