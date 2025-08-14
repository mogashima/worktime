<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\Expense;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::with('category', 'user')->orderBy('date', 'desc')->get();
        return $this->responseJson($expenses);
    }

    public function store(Request $request)
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

        $user = Auth::user();
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

    public function update(Request $request, $id)
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

        $expense = Expense::findOrFail($id);
        $expense->update($validated);

        return $this->responseJson($expense);
    }

    /**
     * 一般ユーザー向け：自分の経費削除
     */
    public function delete(Request $request, $expense_id)
    {
        $user = Auth::user();
        $expense = Expense::where('id', $expense_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$expense) {
            return $this->responseJson(['message' => '経費が見つかりません'], 404);
        }

        $expense->delete();

        return $this->responseJson(['message' => '削除しました']);
    }
}
