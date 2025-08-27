<?php

namespace Amplify\System\Cms\Http\Requests;

use Amplify\System\Cms\Models\Menu;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MenuRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
        return backpack_auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'group_id' => ['required'],
            'name' => 'required|min:3|max:255',
            'url' => 'nullable|url|max:255',
            'seo_path' => 'nullable|string|max:255',
            'sub_category_depth' => ['nullable', 'integer'],
            'display_product_count' => ['nullable', 'boolean'],
            'page_id' => ['nullable', 'exists:pages,id'],
            'type' => ['string', Rule::in(array_keys(Menu::MENU_TYPES))],
            'url_type' => ['required', Rule::in(array_keys(Menu::URL_TYPES))],
            'icon' => ['nullable', 'string', 'max:255'],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge(
            [
                'name' => json_encode($this->input('name')),
                'display_product_count' => $this->input('display_product_count') ? 1 : 0
            ]
        );
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            //
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            //
        ];
    }
}
