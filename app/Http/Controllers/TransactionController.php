<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function transcation(Request $request){
        $name = $request->name;
        $category = $request->category;
        $qty = 1;
        $price = 1000;

        $total = $qty * $price;

        return view('transcation',[
            'name' => $name,
            'category' => $category,
            'qty' => $qty,
            'subtotal' => $price,
            'total' => $total
        ]);
    }
}
