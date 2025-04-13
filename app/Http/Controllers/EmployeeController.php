<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $nom = auth()->user()->name;
        $secteur = auth()->user()->secteur;
        return view('employee.index',compact('user','nom', 'secteur'));
    }

    public function index2()
    {
        $user = auth()->user();
        $nom = auth()->user()->name;
        $secteur = auth()->user()->secteur;
        return view('employee.index2',compact('user','nom', 'secteur'));
    }

    public function index3()
    {
        $user = auth()->user();
        $nom = auth()->user()->name;
        $secteur = auth()->user()->secteur;
        return view('employee.index3',compact('user','nom', 'secteur'));
    }
    public function index4()
    {
        $user = auth()->user();
        $nom = auth()->user()->name;
        $secteur = auth()->user()->secteur;
        return view('employee.index4',compact('user','nom', 'secteur'));
    }
    public function index5()
    {
        $user = auth()->user();
        $nom = auth()->user()->name;
        $secteur = auth()->user()->secteur;
        return view('employee.index5',compact('user','nom', 'secteur'));
    }
}
