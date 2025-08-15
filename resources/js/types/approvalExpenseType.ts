import type { User } from "./userType"
import type { ApprovalStatus } from "./approvalStatusType"
import type { Expense } from "./expenseType"
export interface ApprovalExpense {
    id: number
    user: User
    title: string
    created_at: string
    approval_status: ApprovalStatus
    expenses: Expense[]
}