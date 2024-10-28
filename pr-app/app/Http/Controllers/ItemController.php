<?php

namespace App\Http\Controllers;

use App\Models\Items;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(){
        $items=Items::all();

        return view("main",compact('items'));
    }
}
