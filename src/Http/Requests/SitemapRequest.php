<?php

namespace Amplify\System\Cms\Http\Requests;

use Amplify\System\Backend\Models\Product;
use Amplify\System\Cms\Models\Menu;
use Amplify\System\Cms\Models\Page;
use Amplify\System\Cms\Models\Sitemap;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SitemapRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
        //        return backpack_auth()->check();
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'mappable' => 'array|nullable',
            'mappable.mappable_type' => 'string',
            'mappable.mappable_id' => 'integer|nullable',
            'location' => 'nullable|url',
            'changefreq' => ['required', Rule::in(array_keys(Sitemap::CHANGE_FREQ))],
            'priority' => 'numeric|min:0|max:1',
            'sitemapTags' => 'nullable|array',
        ];
    }

    protected function prepareForValidation()
    {
        //        dd($this->all());
        if (! $this->has('location')) {
            $this->mergeIfMissing(['location' => $this->resolveUrlFromModel()]);
        }
    }

    private function resolveUrlFromModel()
    {
        $type = $this->input('mappable.mappable_type');

        $id = $this->input('mappable.mappable_id');

        switch ($type) {
            case Page::class:

                $page = Page::find($id);

                return url($page->slug);

            case Product::class:

                $product = Product::find($id);

                return route('frontend.singleProduct', $product->{config('amplify.frontend.easyask_single_product_index')});

            case Menu::class:

                $menu = Menu::find($id);

                return route('frontend.singleProduct', $menu->{config('amplify.frontend.easyask_single_product_index')});

        }
    }
}
