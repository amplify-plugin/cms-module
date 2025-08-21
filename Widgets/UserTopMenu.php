<?php

namespace Amplify\System\Cms\Widgets;

use Amplify\Widget\Abstracts\BaseComponent;
use Closure;
use Illuminate\Contracts\View\View;

/**
 * @class UserTopMenu
 */
class UserTopMenu extends BaseComponent
{
    /**
     * @var array
     */
    public $options;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        parent::__construct();

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
        $contact = (customer_check()) ? customer(true) : null;

        $menuGroup = menu('user-top-menu', false);

        return view('widget::user-top-menu', [
            'contact' => $contact,
            'menuGroup' => $menuGroup,
        ]);
    }
}
