<?php

namespace Amplify\System\Cms\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PageRequest extends FormRequest
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
            'name' => 'required|min:3|max:255',
            'slug' => 'required|unique:pages,slug,'.request()->route('id').',id,deleted_at,NULL',
            'content' => 'required|string',
            'title' => 'nullable|string',
            'template' => 'nullable',
            'page_type' => 'nullable|string',
            'meta_description' => 'nullable',
            'meta_image_path' => 'nullable',
            'meta_key' => 'nullable',
            'has_breadcrumb' => 'nullable|boolean',
            'has_footer' => 'nullable|boolean',
            'breadcrumb_title' => 'nullable|string',
            'styles' => 'nullable|string',
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
