<?php
namespace App\Http\Controllers;

class ArchiveController extends Controller
{
    public function index()
    {
        return view('archives.index');
    }
}