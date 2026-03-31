<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CRDisionController extends Controller
{
    public function manage()
    {
        return view('manage.manage-cr_desicion');
    }
    public function create()
    {
        return view('add.add-cr_desicion');
    }
}
