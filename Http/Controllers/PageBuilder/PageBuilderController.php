<?php

namespace Amplify\System\Cms\Http\Controllers\PageBuilder;

use App\Http\Controllers\Controller;

class PageBuilderController extends Controller
{
    public function index()
    {
        return view('page-builder.index');
    }
}
