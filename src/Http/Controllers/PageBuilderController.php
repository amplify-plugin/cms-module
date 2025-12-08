<?php

namespace Amplify\System\Cms\Http\Controllers;

use App\Http\Controllers\Controller;

class PageBuilderController extends Controller
{
    public function index()
    {
        return view('cms::page-builder.index');
    }
}
