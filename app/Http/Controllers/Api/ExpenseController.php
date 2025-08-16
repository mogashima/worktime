<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\Expense;
use App\Models\User;

class ExpenseController extends Controller
{
    public function index(User $user)
    {
        $expenses = Expense::with('category', 'user')
            ->whereUserId($user->id)
            ->orderBy('date', 'desc')
            ->get();
        return $this->responseJson($expenses);
    }

    public function store(Request $request, User $user)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'date' => 'required|date',
            'category_code' => [
                'required',
                'string',
                Rule::exists('expense_categories', 'category_code')
            ],
            'description' => 'nullable|string',
        ]);

        $expense = Expense::create([
            'user_id' => $user->id,
            'title' => $validated['title'],
            'amount' => $validated['amount'],
            'date' => $validated['date'],
            'category_code' => $validated['category_code'],
            'description' => $validated['description']

        ]);

        return $this->responseJson($expense);
    }

    public function update(Request $request, User $user, Expense $expense)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'category_code' => [
                'required',
                'string',
                Rule::exists('expense_categories', 'category_code')
            ],
            'description' => 'nullable|string|max:255',
        ]);

        $expense->update($validated);

        return $this->responseJson($expense);
    }

    /**
     * 一般ユーザー向け：自分の経費削除
     */
    public function delete(Request $request, User $user, Expense $expense)
    {
        $expense->delete();
        return $this->responseJson(['message' => '削除しました']);
    }
}
