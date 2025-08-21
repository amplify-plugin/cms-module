<?php

namespace Amplify\System\Cms\Widgets;

use Amplify\System\Cms\Models\Form as FormModel;
use Amplify\Widget\Abstracts\BaseComponent;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Config;

/**
 * @class Form
 */
class Form extends BaseComponent
{
    /**
     * @var array
     */
    public $options;

    public ?FormModel $form;

    /**
     * Create a new component instance.
     */
    public function __construct(public string $code)
    {
        $this->options = Config::get('amplify.widget.'.__CLASS__, []);

        $this->form = FormModel::with('formFields')->whereCode($this->code)->first();

    }

    /**
     * Whether the component should be rendered
     */
    public function shouldRender(): bool
    {
        return (bool) $this->form?->enabled;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('widget::form');
    }

    public function clearButtonTitle()
    {
        return $this->form->reset_button_title ?? 'Clear';
    }

    public function submitButtonTitle()
    {
        return $this->form->submit_button_title ?? 'Submit';
    }

    public function captchaVerification()
    {
        return $this->form->allow_captcha ?? false;
    }
}
