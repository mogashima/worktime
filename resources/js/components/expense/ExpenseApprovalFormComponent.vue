<template>
    <div class="expense-approval-form">
        <h3>経費申請</h3>
        <FormMessageComponent :apiError="apiError" />
        <div class="form-group">
            <label>タイトル：
                <input type="text" v-model="form.title" required />
            </label>
        </div>
        <p>以下の経費を申請します</p>
        <ul>
            <li v-for="expense in expenses" :key="expense.id">
                {{ expense.date }} - {{ expense.title }} - {{ expense.amount }}円
            </li>
        </ul>


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
import { ApiError } from '@/types/apiErrorType';
import FormMessageComponent from '@/components/common/FormMessageComponent.vue'

const props = defineProps<{
    userId: number | null
    expenses: Expense[]
    categories: ExpenseCategory[]
}>()

const emit = defineEmits(['approval-saved', 'edit-cancel'])

interface RequestForm {
    id: number
    title: string
    expense_ids: number[]
}

const emptyForm: RequestForm = {
    id: 0,
    title: '',
    expense_ids: []
}
const form = ref(emptyForm)
const apiError = ref<ApiError | null>(null)

const submitApproval = async () => {
    if (!props.userId || props.expenses.length === 0) return

    try {
        form.value.expense_ids = props.expenses.map(e => e.id)
        await axios.post(`/api/approval/expense`, form.value)


        emit('approval-saved')
    } catch (error: any) {
        apiError.value = new ApiError(error)
    }
}

const cancel = () => {
    emit('edit-cancel')
}
</script>
