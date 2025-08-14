import { ExpenseCategory } from "./expenseCategoryType"
export interface Expense {
    id: number
    user_id: number
    title: string
    date: string
    category_code: string
    category: ExpenseCategory
    amount: number
    description: string
}