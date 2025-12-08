<?php

namespace Amplify\System\Cms\Http\Middleware;

use Amplify\System\Cms\Models\Page;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LookupDynamicPage
{
    /**
     * Handle an incoming request.
     *
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $slug = $request->input('slug');

        $request->merge(['dynamicPage' => $this->checkPageExists($slug)]);

        return $next($request);
    }

    /**
     * @return Builder|Model|\Amplify\System\Cms\Models\Page|object|null
     *
     * @throws NotFoundHttpException
     */
    private function checkPageExists(?string $slug)
    {
        if ($page = Page::published()->whereSlug($slug)->first()) {
            return $page;
        }

        return abort(404, 'Page Not Found');
    }
}
