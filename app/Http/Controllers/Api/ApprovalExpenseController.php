<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApprovalExpense;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApprovalExpenseController extends Controller
{
    /**
     * Approvalと紐づくExpense一覧を取得
     */
    public function index()
    {
        $userId = Auth::user()->id;
        $approvalExpenses = ApprovalExpense::with(['expenses.category', 'approvalStatus'])
            ->orderBy('created_at', 'desc')
            ->whereUserId($userId)
            ->get();
        return $this->responseJson($approvalExpenses);

    }

    /**
     * 申請の登録処理
     */
    public function store(Request $request)
    {
        $userId = Auth::user()->id;
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'expense_ids' => 'required|array|min:1',
            // 該当ユーザに紐づくもののみ
            'expense_ids.*' => 'integer|exists:expenses,id',
        ]);

        $expenseIds = $validated['expense_ids'];
        $expenses = Expense::whereIn('id', $expenseIds)->whereUserId($userId)->get();

        $approval = ApprovalExpense::create([
            'title' => $validated['title'],
            'user_id' => $userId,
            'status_code' => 'pending'
        ]);

        if (!empty($expenses)) {
            foreach ($expenses as $expense) {
                $expense->approval_expense_id = $approval->id;
                $expense->save();
            }
        }

        $this->responseJson($approval, 201);
    }

    /**
     * 申請の削除処理（取り下げ）
     */
    public function destroy(Request $request, ApprovalExpense $approvalExpense)
    {
        $userId = Auth::user()->id;

        // 自分の承認経費でない場合は403
        if ($approvalExpense->user_id !== $userId) {
            return $this->responseJson(['message' => '権限がありません'], 403);
        }

        try {
            // 関連する expenses の approval_expense_id を null に更新
            $approvalExpense->expenses()->update(['approval_expense_id' => null]);

            // 承認経費を削除
            $approvalExpense->delete();

            return $this->responseJson(['message' => '削除に成功しました']);
        } catch (\Exception $e) {
            return $this->responseJson(['message' => '削除に失敗しました', 'error' => $e->getMessage()], 500);
        }
    }
}
