<?php

namespace Amplify\System\Cms\Widgets;

use Amplify\System\Helpers\UtilityHelper;
use Amplify\Widget\Abstracts\BaseComponent;
use Closure;
use Illuminate\Contracts\View\View;

/**
 * @class FormBuilder
 */
class CustomForm extends BaseComponent
{
    /**
     * @var array
     */
    public $options;

    public $actionUrl;

    public $allowReset;

    public $fields;

    public $submitButtonTitle;

    public $allowCaptcha;

    public $clearButtonTitle;

    /**
     * Create a new component instance.
     */
    public function __construct($actionUrl,
        $allowReset,
        $clearButtonTitle,
        $submitButtonTitle,
        $allowCaptcha,
        $fields)
    {
        parent::__construct();

        $this->actionUrl = $actionUrl;
        $this->submitButtonTitle = $submitButtonTitle;
        $this->clearButtonTitle = $clearButtonTitle;
        $this->allowReset = UtilityHelper::typeCast($allowReset, 'bool');
        $this->allowCaptcha = UtilityHelper::typeCast($allowCaptcha, 'bool');
        $this->fields = UtilityHelper::typeCast($fields, 'json');
        $this->clearButtonTitle = $clearButtonTitle;
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
        return view('widget::form-builder');
    }
}
