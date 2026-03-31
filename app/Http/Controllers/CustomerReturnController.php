<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerReturnController extends Controller
{
    public function manage()
    {
        return view('manage.manage-customer_return');
    }

    public function create()
    {
        return view('add.add-customer_return');
    }
}
