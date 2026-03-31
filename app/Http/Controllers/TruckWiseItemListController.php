<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TruckWiseItemListController extends Controller
{
    public function manage()
    {
        return view('manage.manage-turck_wise_item');
    }
    public function create()
    {
        return view('add.add-turck_wise_item');
    }
}
