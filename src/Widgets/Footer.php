<?php

namespace Amplify\System\Cms\Widgets;

use Amplify\System\Cms\Models\Footer as FooterModel;
use Amplify\System\Cms\Models\Page;
use Amplify\Widget\Abstracts\BaseComponent;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

/**
 * @class Footer
 */
class Footer extends BaseComponent
{

    private ?Page $page;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->page = store('dynamicPageModel', null);
    }

    /**
     * Whether the component should be rendered
     */
    public function shouldRender(): bool
    {
        if ($this->page instanceof Page) {
            return $this->page->has_footer;
        }

        return true;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $contents = Cache::rememberForever('site-footer', function () {
            return FooterModel::where(['is_enabled' => true, 'template_id' => theme()->id])
                ->get()
                ->pluck('content')
                ->implode("\n");
        });

        $contents = str_replace('__year__', date('Y'), $contents);

        return view('cms::footer', [
            'content' => $contents,
        ]);
    }

    public function htmlAttributes(): string
    {
        $this->attributes = $this->attributes->class(['site-footer']);

        return parent::htmlAttributes();
    }
}
