<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ViewController extends Controller
{
    public function redirect() {
        return redirect()->route('orderbook.index');
    }

    public function orderbook() {
        return view('orderbook');
    }
}
