<?php

namespace Amplify\System\Cms\Http\Controllers;

use Amplify\System\Abstracts\BackpackCustomCrudController;
use Amplify\System\Backend\Http\Requests\EnvVariableUpdateRequest;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

/**
 * Class RobotTextController
 *
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class RobotTextController extends BackpackCustomCrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;

    private string $robotsTextPath;

    private array $replacements = [];

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setRoute(config('backpack.base.route_prefix') . '/robots-text');
        CRUD::setEntityNameStrings('robots text', 'robots text');
        $this->robotsTextPath = public_path('robots.txt');

        $this->replacements = [
            '{sitemap_link}' => url('sitemap.xml'),
            '{site_url}' => frontendHomeURL(),
            '{modified}' => date('Y-m-d'),
        ];
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     *
     * @return void
     */
    protected function setupListOperation()
    {
        if (!file_exists($this->robotsTextPath)) {
            \file_put_contents($this->robotsTextPath, $this->defaultTemplate());
        }

        $this->crud->removeButton('create');
        $this->crud->setListContentClass('col-lg-12');
        $this->crud->setListView('backend::pages.editor');

        $content = file_get_contents($this->robotsTextPath);

        $replacements = array_flip($this->replacements);

        $this->data['content'] = strtr($content, $replacements);

        $dynamicString = implode(' ', array_map(fn($i) => "<code>{$i}</code>", $replacements));

        $this->data['header'] = <<<HTML
                            <label class="fw-bold">
                                Caution: Ensure that <code>robots.txt</code> file is Git ignored.
                                Suggestion: Dynamic String like: 
                                {$dynamicString}
                                will get replaced upon saving file.
                            </label>
HTML;

    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     *
     * @param EnvVariableUpdateRequest $request
     * @return RedirectResponse
     */
    protected function store(Request $request)
    {
        $code = $request->input('content', '');

        if (!file_put_contents($this->robotsTextPath, strtr($code . PHP_EOL, $this->replacements))) {
            \Alert::error('Unable to modify customer style file.')->flash();
        } else {
            \Alert::success(__('backpack::crud.update_success'))->flash();
            Artisan::call('optimize:clear');
        }

        return redirect()->to($this->crud->getRoute());
    }

    private function defaultTemplate(): string
    {
        return <<<HTML
# robots.txt for {site_url}
# Last updated: {modified}
# Purpose: Control search engine crawling for eCommerce site

User-agent: *
# Allow essential pages
Allow: /

# Disallow system, admin, and cart-related areas
Disallow: /admin/
Disallow: /checkout/
Disallow: /cart/
Disallow: /dashboard/
Disallow: /orders/
Disallow: /wishlist/
Disallow: /invoice/
Disallow: /ar-summary/
Disallow: /login/
Disallow: /register/
Disallow: /forgot-password/
Disallow: /reset-password/
Disallow: /backups/
Disallow: /storage/
Disallow: /packages/
Disallow: /themes/

# Block duplicate or unnecessary URLs
Disallow: /*?sort_by=
Disallow: /*?per_page=
Disallow: /*?view=
Disallow: /*?ref=

# Allow important assets for rendering
Allow: /*.css$
Allow: /*.js$
Allow: /uploads/
Allow: /assets/

# Crawl-delay (optional, uncomment if server load is high)
Crawl-delay: 10

# Sitemap location
Sitemap: {sitemap_link}

HTML;

    }
}
