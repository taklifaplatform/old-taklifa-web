<?php

namespace Webkul\Shop\Http\Controllers;

use Illuminate\Http\Request;


class CalculateController extends Controller
{
    public function calculateCost()
    {
        return view('shop::calculate-cost');
    }
}
