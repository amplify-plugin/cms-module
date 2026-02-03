<?php

namespace Amplify\System\Cms\Widgets\Menu\AccountSidebar;

use Amplify\System\Backend\Models\Contact;
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

    /**
     * Create a new component instance.
     */
    public function __construct(public ?Contact $account)
    {
        parent::__construct();

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
        return view('cms::menu.account-sidebar.profile');
    }

    public function accountProfileImage()
    {
        return ! empty($this->account->profile_image) ? $this->account?->profile_image : generateUserAvatar($this->account?->name);
    }

    public function accountName(): ?string
    {
        return $this->account?->name ?? null;
    }

    public function companyName(): ?string
    {
        return $this->account?->customer->customer_name ?? null;
    }

    public function companyCode(): ?string
    {
        return $this->account?->customer->customer_code ?? null;
    }

    public function accountRoles()
    {
        return implode(', ', ($this->account?->roles?->pluck('name')?->toArray() ?? []));
    }

    public function htmlAttributes(): string
    {
        $this->attributes = $this->attributes->class(['user-info-wrapper']);

        return parent::htmlAttributes();
    }
}
