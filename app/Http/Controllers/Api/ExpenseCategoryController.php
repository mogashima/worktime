<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExpenseCategory;

class ExpenseCategoryController extends Controller
{
    public function index()
    {
        $categories = ExpenseCategory::all();
        return $this->responseJson($categories);
    }
}
