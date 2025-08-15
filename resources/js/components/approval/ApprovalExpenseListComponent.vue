<template>
    <div class="approval-expense-list">
        <table class="page-list">
            <thead>
                <tr>
                    <th>申請日</th>
                    <th>タイトル</th>
                    <th>経費件数</th>
                    <th>状態</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="approval in approvalExpenses" :key="approval.id" class="clickable-row"
                    @click="clickApprovalExpense(approval)" :class="rowClass(approval.approval_status.status_code)">
                    <td>{{ formatDate(approval.created_at) }}</td>
                    <td>{{ approval.title }}</td>
                    <td>{{ approval.expenses.length }}</td>
                    <td>{{ approval.approval_status.name }}</td>
                </tr>
                <tr v-if="approvalExpenses.length === 0">
                    <td colspan="4" class="text-center">申請中の経費がありません</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup lang="ts">
import type { ApprovalExpense } from '@/types/approvalExpenseType'
import { StatusCode } from '@/types/approvalStatusType'

const props = defineProps<{
    approvalExpenses: ApprovalExpense[]
}>()

const emit = defineEmits(['select-approval-expense'])

const clickApprovalExpense = (approvalExpense: ApprovalExpense) => {
    emit('select-approval-expense', approvalExpense)
}

const formatDate = (dateStr: string) => {
    const date = new Date(dateStr)
    return date.toLocaleDateString('ja-JP', { year: 'numeric', month: '2-digit', day: '2-digit' })
}

const rowClass = (statusCode: string) => {
    switch (statusCode) {
        case StatusCode.Pending:
            return 'row-pending'
        case StatusCode.Approved:
            return 'row-approved'
        case StatusCode.Rejected:
            return 'row-rejected'
        default:
            return ''
    }
}
</script>