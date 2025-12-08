<?php

namespace Amplify\System\Cms\Http\Requests;

use Amplify\System\Cms\Models\MegaMenu;
use Amplify\System\Rules\MenuColumnSizeRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MegaMenuRequest extends FormRequest
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
            'enabled' => 'required|boolean',
            'name' => 'required|min:5|max:255',
            'menu_column_size' => ['required', 'integer', new MenuColumnSizeRule],
            'type' => ['required', Rule::in(array_keys(MegaMenu::TYPES))],
            'number_of_categories' => 'nullable|numeric',
            'products' => 'required_if:type,product|array',
            'products.*.product_id' => 'required|integer',
            'products.*.product_column_size' => 'required|integer|between:1,12',
            'products.*.attribute_access' => 'required|array',
            'merchandising_zone_id' => 'required_if:type,merchandising-zones|nullable|numeric',
            'number_of_column_merchandising_zone' => 'required_if:type,merchandising-zones|nullable|numeric|between:1,12',
            'number_of_merchandising_products' => 'nullable|numeric',
        ];
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
