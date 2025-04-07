<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
class QueryInterfaceController extends Controller
{
    public function showQueryForm()
    {
        return view('query-form');
    }
}
