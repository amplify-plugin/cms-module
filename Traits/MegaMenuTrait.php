<?php

namespace Amplify\Cms\Traits;

use Amplify\System\Cms\Models\MegaMenu;

trait MegaMenuTrait
{
    protected $defaultClasses = [];

    protected $defaultStyles = [];

    public MegaMenu $menu;

    /**
     * Create a new component instance.
     */
    public function __construct(MegaMenu $menu)
    {
        parent::__construct();

        $this->menu = $menu;
    }

    /**
     * Whether the component should be rendered
     */
    public function shouldRender(): bool
    {
        return true;
    }

    public function showTitle(): bool
    {
        return $this->menu->show_name;
    }

    public function megaMenuTitle(): ?string
    {
        return $this->menu->displayName();
    }

    public function setDefaultStyles($styles): void
    {
        if (is_array($styles)) {
            array_push($this->defaultStyles, ...$styles);
        } else {
            $this->defaultStyles[] = $styles;
        }
    }

    public function setDefaultClasses($class): void
    {
        if (is_array($class)) {
            array_push($this->defaultClasses, ...$class);
        } else {
            $this->defaultClasses[] = $class;
        }
    }

    public function megaMenuColumns(): int
    {
        return mega_menu_columns($this->menu->menu_column);
    }

    public function htmlAttributes(): string
    {
        if (! empty($this->defaultClasses)) {
            $this->attributes = $this->attributes->class($this->defaultClasses);
        }

        if (! empty($this->defaultStyles)) {
            $this->attributes = $this->attributes->style($this->defaultStyles);
        }

        return parent::htmlAttributes();
    }
}
