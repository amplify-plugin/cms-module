<?php

namespace Amplify\System\Cms\Widgets\Menu\AccountMenu;

use Amplify\Widget\Abstracts\BaseComponent;
use Closure;
use Illuminate\Contracts\View\View;

/**
 * @class Profile
 */
class Profile extends BaseComponent
{
    /**
     * @var array
     */
    public $options;

    private $account;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->account = customer(true);

    }

    /**
     * Whether the component should be rendered
     */
    public function shouldRender(): bool
    {
        return customer_check();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {

        return view('widget::menu.account-menu.profile');
    }

    public function accountProfileImage()
    {
        return ! empty($this->account->profile_image) ? $this->account->profile_image : generateUserAvatar($this->account->name);
    }

    public function accountName(): ?string
    {
        return $this->account->name ?? null;
    }

    public function companyName(): ?string
    {
        return $this->account->customer->customer_name ?? null;
    }
}
