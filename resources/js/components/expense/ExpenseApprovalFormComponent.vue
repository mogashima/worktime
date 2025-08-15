<template>
    <div class="expense-approval-form">
        <h3>経費申請フォーム</h3>
        <p>以下の経費を申請します:</p>

        <ul>
            <li v-for="expense in expenses" :key="expense.id">
                {{ expense.date }} - {{ expense.title }} - {{ expense.amount }}円
            </li>
        </ul>

        <!-- コメントなど任意入力 -->
        <div class="form-group">
            <label>申請コメント:</label>
            <textarea v-model="comment" placeholder="コメントを入力"></textarea>
        </div>

        <div class="form-actions">
            <button class="btn-cancel" type="button" @click="cancel">キャンセル</button>
            <button class="btn-add" type="button" @click="submitApproval">申請</button>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import axios from '@/plugins/axios'
import type { Expense } from '@/types/expenseType'
import type { ExpenseCategory } from '@/types/expenseCategoryType';

const props = defineProps<{
    userId: number | null
    expenses: Expense[]
    categories: ExpenseCategory[]
}>()

const emit = defineEmits(['approval-saved', 'edit-cancel'])

const comment = ref('')

const submitApproval = async () => {
    if (!props.userId || props.expenses.length === 0) return

    try {
        // APIに申請データを送信
        /*
        await axios.post(`/api/expense/approval`, {
            user_id: props.userId,
            expense_ids: props.expenses.map(e => e.id),
            comment: comment.value
        })
            */


        emit('approval-saved')
    } catch (error) {

    }
}

const cancel = () => {
    emit('edit-cancel')
}
</script>
