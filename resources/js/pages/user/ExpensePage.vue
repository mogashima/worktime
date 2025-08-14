<template>
    <NavigationComponent />
    <div class="expense-page page">
        <h2>経費入力</h2>
        <AlertComponent :alert-data="alertData" />

        <button @click="openAddModal" class="btn-add">経費を申請</button>
        <hr>

        <ExpenseListComponent :expenses="expenses" @edit-expense="openEditModal" @delete-expense="confirmDelete" />

        <!-- 経費フォームモーダル -->
        <div v-if="modalVisible" class="modal-overlay" @click.self="closeModal">
            <div class="modal-content">
                <ExpenseFormComponent :user-id="userStore.user ? userStore.user.id : null" :expense="editingExpense"
                    :categories="expenseCategories" @expense-saved="onExpenseSaved" @edit-cancel="closeModal" />
            </div>
        </div>

        <!-- 確認モーダル -->
        <ConfirmComponent v-if="confirmVisible" title="削除確認" :message="confirmMessage" @confirm="deleteExpense"
            @cancel="confirmVisible = false" />
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import axios from '@/plugins/axios'
import { useUserStore } from '@/stores/userStore'
import { Expense } from '@/types/expenseType'
import type { ExpenseCategory } from '@/types/expenseCategoryType'
import { Alert, AlertType } from '@/types/alertType'
import NavigationComponent from '@/components/NavigationComponent.vue'
import ExpenseListComponent from '@/components/expense/ExpenseListComponent.vue'
import ExpenseFormComponent from '@/components/expense/ExpenseFormComponent.vue'
import ConfirmComponent from '@/components/common/ConfirmComponent.vue'
import AlertComponent from '@/components/common/AlertComponent.vue'

const expenses = ref<Expense[]>([])
const editingExpense = ref<Expense | null>(null)
const expenseCategories = ref<ExpenseCategory[]>([])
const modalVisible = ref(false)
const confirmVisible = ref(false)
const confirmMessage = ref('')
const deleteTargetId = ref<number | null>(null)
const alertData = ref<Alert | null>(null)

const userStore = useUserStore()

const fetchExpenses = async () => {
    if (!userStore.user) return
    const res = await axios.get(`/api/expense`)
    expenses.value = res.data
}

watch(() => userStore.user, (newUser) => {
    if (newUser) fetchExpenses()
})

onMounted(() => {
    fetchCategories()
    if (userStore.user) fetchExpenses()
})

// 追加モーダル
const openAddModal = () => {
    editingExpense.value = null
    modalVisible.value = true
}

// 編集モーダル
const openEditModal = (expense: Expense) => {
    editingExpense.value = { ...expense }
    modalVisible.value = true
}

// モーダル閉じる
const closeModal = () => {
    modalVisible.value = false
    editingExpense.value = null
}

// フォーム保存後
const onExpenseSaved = async () => {
    modalVisible.value = false
    editingExpense.value = null
    alertData.value = new Alert('登録に成功しました', AlertType.Success)
    await fetchExpenses()
}

// 削除確認モーダル表示
const confirmDelete = (expense: Expense) => {
    deleteTargetId.value = expense.id
    confirmMessage.value = expense.title + 'を削除しますか？'
    confirmVisible.value = true
}

// 実際の削除処理
const deleteExpense = async () => {
    if (!deleteTargetId.value) return
    try {
        if (userStore.isAdmin() && userStore.user) {
            await axios.delete(`/api/admin/user/${userStore.user.id}/expense/${deleteTargetId.value}`)
        } else {
            await axios.delete(`/api/expense/${deleteTargetId.value}`)
        }
        alertData.value = new Alert('削除に成功しました', AlertType.Success)

        await fetchExpenses()
    } catch (error) {
        alertData.value = new Alert('削除に失敗しました', AlertType.Error)
    } finally {
        confirmVisible.value = false
        deleteTargetId.value = null
    }
}

const fetchCategories = async () => {
    try {
        const res = await axios.get('/api/expense/category')
        expenseCategories.value = res.data
    } catch (error) {
        console.error('カテゴリー取得エラー:', error)
    }
}
</script>
