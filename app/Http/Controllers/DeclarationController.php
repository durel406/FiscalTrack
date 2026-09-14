<?php
namespace App\Http\Controllers;

class DeclarationController extends Controller
{
    public function index()
    {
        return view('declarations.index');
    }
}