<?php

namespace Amplify\System\Cms\Widgets;

use Amplify\System\Cms\Models\Content as ContentModel;
use Amplify\System\Helpers\UtilityHelper;
use Amplify\Widget\Abstracts\BaseComponent;
use Closure;
use Illuminate\Contracts\View\View;

/**
 * @class Content
 */
class Content extends BaseComponent
{
    public $show_title;

    /**
     * @var array
     */
    public $options;

    private $contentId;

    private $item;

    /**
     * Create a new component instance.
     */
    public function __construct(string $contentId = '', string $showTitle = 'true')
    {
        parent::__construct();

        $this->contentId = $contentId;

        $this->show_title = UtilityHelper::typeCast($showTitle, 'bool');
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
        $this->item = ContentModel::findOrFail($this->contentId);

        return view('widget::content');
    }

    public function title(): string
    {
        if ($this->show_title) {
            return $this->item->name ?? '';
        }

        return '';
    }

    public function content(): string
    {
        return $this->item->content ?? '';
    }

    public function contentCssClass()
    {
        return $this->item->slug;
    }
}
