<?php

namespace App\Http\Controllers\Api;

use App\Models\ApprovalExpense;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminApprovalExpenseController extends Controller
{
    /**
     * 一覧表示
     */
    public function index()
    {
        $approvalExpenses = ApprovalExpense::with(['user', 'reviewer', 'approvalStatus', 'expenses.category'])
            ->orderBy('created_at', 'desc')
            ->get();
        return $this->responseJson($approvalExpenses);
    }

    public function update(Request $request, ApprovalExpense $approvalExpense)
    {
        $request->validate([
            'status_code' => [
                'required',
                'string',
                'exists:approval_statuses,status_code',
            ],
        ]);
        $approvalExpense->status_code = $request->status_code;
        $approvalExpense->save();

        return $this->responseJson($approvalExpense);
    }
}
