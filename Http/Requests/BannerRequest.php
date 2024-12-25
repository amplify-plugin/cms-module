<?php

namespace Amplify\System\Cms\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BannerRequest extends FormRequest
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
            'banner_zone_id' => 'required|integer',
            'code' => 'required|string',
            'name' => 'required|string',
            'image' => 'nullable|string',
            'background_image' => 'required|string',
            'background_type' => 'required|string',
            'foreground_type' => 'nullable|string',
            'alignment' => 'required|string|in:left,right',
            'text_alignment' => 'required|string|in:left,right,center',
            // 'image_alignment' => 'required|string|in:left,right,center',
            'button_link' => 'nullable|string',
            'button_style' => 'nullable|string',
            'button_title' => 'nullable|string',
            'content' => 'nullable|string',
            'enabled' => 'nullable|boolean',
            'has_button' => 'nullable|boolean',
            'has_heading' => 'nullable|boolean',
            'has_content' => 'nullable|boolean',
            'open_new_tab' => 'nullable|boolean',
            'slider_ratio' => 'required|integer',
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
            'name.required' => 'The heading field is required',
            'background_image.required' => 'The background image field is required',
        ];
    }
}
