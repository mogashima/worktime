<template>
    <div class="expense-form">
        <h3>{{ isEdit ? '経費編集' : '経費追加' }}</h3>
        <!-- エラー表示 -->
        <FormMessageComponent :apiError="apiError" />
        <form @submit.prevent="submitForm" class="form-container">
            <div class="form-group">
                <label>日付: <input type="date" v-model="form.date" required /></label>
            </div>
            <div class="form-group">
                <label>カテゴリ:
                    <select v-model="form.category_code" required>
                        <option value="" disabled>選択してください</option>
                        <option v-for="cat in props.categories" :key="cat.category_code" :value="cat.category_code">
                            {{ cat.name }}
                        </option>
                    </select>
                </label>
            </div>
            <div class="form-group">
                <label>タイトル: <input type="text" v-model="form.title" required /></label>
            </div>
            <div class="form-group">
                <label>金額: <input type="number" step="0.01" v-model="form.amount" required /></label>
            </div>
            <div class="form-group">
                <label>備考: <input type="text" v-model="form.description"></label>
            </div>

            <div class="form-actions">
                <button class="btn-cancel" type="button" @click="cancelEdit">
                    キャンセル
                </button>
                <button type="submit" :class="isEdit ? 'btn-edit' : 'btn-add'">
                    {{ isEdit ? '更新' : '登録' }}
                </button>
            </div>
        </form>
    </div>
</template>

<script setup lang="ts">
import { reactive, watch, computed, ref } from 'vue'
import axios from '@/plugins/axios'
import { useUserStore } from '@/stores/userStore';
import type { Expense } from '@/types/expenseType';
import type { ExpenseCategory } from '@/types/expenseCategoryType';
import { ApiError } from '@/types/apiErrorType';
import FormMessageComponent from '@/components/common/FormMessageComponent.vue'

const props = defineProps<{
    userId: number | null
    expense: Expense | null
    categories: ExpenseCategory[]
}>()

const emit = defineEmits(['expense-saved', 'edit-cancel'])

const userStore = useUserStore()

const isEdit = computed(() => !!props.expense)
const apiError = ref<ApiError | null>(null)

const emptyForm = {
    id: 0,
    title: '',
    date: '',
    amount: 0,
    description: '',
    category_code: '',
}


const form = reactive({ ...emptyForm })

watch(() => props.expense, (newVal) => {
    if (newVal) {
        form.id = newVal.id
        form.title = newVal.title
        form.date = new Date(newVal.date).toLocaleDateString('sv-SE')
        form.amount = newVal.amount
        form.description = newVal.description
        form.category_code = newVal.category_code
    } else {
        Object.assign(form, emptyForm)
    }
}, { immediate: true })

const submitForm = async () => {
    if (!props.userId) {
        console.error('ユーザーIDが設定されていません')
        return
    }

    try {
        if (isEdit.value && props.expense) {
            if (userStore.isAdmin()) {
                await axios.put(`/api/admin/user/${props.userId}/expense/${props.expense.id}`, form)
            } else {
                await axios.put(`/api/expense/${props.expense.id}`, form)
            }
        } else {
            if (userStore.isAdmin()) {
                await axios.post(`/api/admin/user/${props.userId}/expense`, form)
            } else {
                await axios.post(`/api/expense`, form)
            }
        }
        emit('expense-saved')
    } catch (error: any) {
        apiError.value = new ApiError(error)
    }
}

const cancelEdit = () => {
    emit('edit-cancel')
}
</script>
