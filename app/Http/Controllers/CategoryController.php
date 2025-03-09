<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function beverage(){
        return view('categories.beverage');
    }
    public function beauty(){
        return view('categories.beauty');
    }
    public function homecare(){
        return view('categories.homecare');
    }
    public function babykid(){
        return view('categories.babykid');
    }
}
