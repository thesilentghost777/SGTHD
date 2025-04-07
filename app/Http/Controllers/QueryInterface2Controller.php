<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class QueryInterface2Controller extends Controller
{
    function showQueryForm(){
        return view('pages.advices.query')
    }
}
