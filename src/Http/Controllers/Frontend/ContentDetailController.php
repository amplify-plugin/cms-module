<?php

namespace Amplify\System\Cms\Http\Controllers\Frontend;

use Amplify\Frontend\Traits\HasDynamicPage;
use Amplify\System\Cms\Models\Content;
use App\Http\Controllers\Controller;
use ErrorException;
use Illuminate\Contracts\Container\BindingResolutionException;

class ContentDetailController extends Controller
{
    use HasDynamicPage;

    /**
     * Handle the incoming request.
     *
     * @throws ErrorException
     * @throws BindingResolutionException
     */
    public function __invoke(Content $content)
    {
        $this->loadPageByType('content_detail');

        store()->contentModel = $content;

        store()->pageTitle = $content->name;

        return $this->render();
    }
}
