<template>
    <div class="approval-expense-form" v-if="approvalExpense">
        <h3>{{ approvalExpense.title }}</h3>
        <p>申請日: {{ formatDate(approvalExpense?.created_at) }}</p>
        <table class="page-list">
            <thead>
                <tr>
                    <th>日付</th>
                    <th>タイトル</th>
                    <th>金額</th>
                    <th>カテゴリ</th>
                    <th>備考</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="expense in approvalExpense?.expenses || []" :key="expense.id">
                    <td>{{ expense.date }}</td>
                    <td>{{ expense.title }}</td>
                    <td>{{ expense.amount }}</td>
                    <td>{{ expense.category?.name || '' }}</td>
                    <td>{{ expense.description }}</td>
                </tr>
            </tbody>
        </table>
        <div class="form-actions" v-if="isAdmin">
            <button class="btn-cancel" @click="closeModal">閉じる</button>
            <div>
                <button class="btn-add mr-small" @click="approve(approvalExpense.id)"
                    :disabled="approvalExpense.approval_status.status_code !== StatusCode.Pending">承認</button>
                <button class="btn-delete" @click="reject(approvalExpense.id)"
                    :disabled="approvalExpense.approval_status.status_code !== StatusCode.Pending">却下</button>
            </div>
        </div>
        <div class="form-actions" v-else>
            <button class="btn-cancel" @click="closeModal">閉じる</button>
            <div v-if="deleteConfirmVisible" class="confirm-message">
                <p>削除してもいいですか？</p>
                <button class="btn-delete" @click="clickApprovalExpenseDelete(approvalExpense)">削除</button>
            </div>
            <div v-else>
                <button class="btn-delete" @click="confirmApprovalExpenseDelete()">削除</button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { StatusCode } from '@/types/approvalStatusType'
import type { ApprovalExpense } from '@/types/approvalExpenseType'
import { RoleCode } from '@/types/roleType'
import { useUserStore } from '@/stores/userStore'

const userStore = useUserStore()
const isAdmin = computed(() => userStore.user?.role_code === RoleCode.Admin)

const props = defineProps<{
    approvalExpense: ApprovalExpense | null
}>()

const emit = defineEmits(['delete-approval-expense', 'close', 'approve', 'reject'])

const deleteConfirmVisible = ref(false)

const formatDate = (dateStr?: string) => {
    if (!dateStr) return ''
    const date = new Date(dateStr)
    return date.toLocaleDateString('ja-JP', { year: 'numeric', month: '2-digit', day: '2-digit' })
}

const closeModal = () => {
    deleteConfirmVisible.value = false
    emit('close')
}

/**
 * 確認前の削除ボタン押下
 */
const confirmApprovalExpenseDelete = () => {
    deleteConfirmVisible.value = true
}

/**
 * 確認後の削除ボタン押下
 */
const clickApprovalExpenseDelete = (approvalExpense: ApprovalExpense) => {
    emit('delete-approval-expense', approvalExpense)
}

const approve = (id: number) => emit('approve', id)
const reject = (id: number) => emit('reject', id)
</script>
