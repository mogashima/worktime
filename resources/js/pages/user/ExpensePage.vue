<template>
    <NavigationComponent />
    <div class="expense-page page">
        <h2>経費入力</h2>
        <AlertComponent :alert-data="alertData" />

        <div class="form-actions">
            <button @click="openAddModal" class="btn-add">経費を登録</button>
            <!-- チェックされていない場合はdisabled -->
            <button class="btn-edit" :disabled="selectedExpenses.length === 0" @click="openApprovalModal">
                経費を申請
            </button>
        </div>
        <hr>

        <ExpenseListComponent :expenses="expenses" :selected-expenses="selectedExpenses" @edit-expense="openEditModal"
            @delete-expense="confirmDelete" @toggle-select="toggleSelectExpense" />

        <!-- 経費フォームモーダル -->
        <div v-if="modalExpenseVisible" class="modal-overlay" @click.self="closeModal">
            <div class="modal-content">
                <ExpenseFormComponent :user-id="userStore.user ? userStore.user.id : null" :expense="editingExpense"
                    :categories="expenseCategories" @expense-saved="onExpenseSaved" @edit-cancel="closeModal" />
            </div>
        </div>

        <!-- 経費申請フォームモーダル -->
        <div v-if="modalApprovalVisible" class="modal-overlay" @click.self="closeModal">
            <div class="modal-large-content">
                <ExpenseApprovalFromComponent :user-id="userStore.user ? userStore.user.id : null"
                    :expenses="selectedExpenses" :categories="expenseCategories" @approval-saved="onApprovalSaved"
                    @edit-cancel="closeModal" />
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
import NavigationComponent from '@/components/common/NavigationComponent.vue'
import ExpenseListComponent from '@/components/expense/ExpenseListComponent.vue'
import ExpenseFormComponent from '@/components/expense/ExpenseFormComponent.vue'
import ExpenseApprovalFromComponent from '@/components/expense/ExpenseApprovalFormComponent.vue'
import ConfirmComponent from '@/components/common/ConfirmComponent.vue'
import AlertComponent from '@/components/common/AlertComponent.vue'

const expenses = ref<Expense[]>([])
const selectedExpenses = ref<Expense[]>([])
const editingExpense = ref<Expense | null>(null)
const expenseCategories = ref<ExpenseCategory[]>([])
const modalExpenseVisible = ref(false)
const modalApprovalVisible = ref(false)
const confirmVisible = ref(false)
const confirmMessage = ref('')
const deleteTargetId = ref<number | null>(null)
const alertData = ref<Alert | null>(null)

const userStore = useUserStore()

const fetchExpenses = async () => {
    if (!userStore.user) return
    const res = await axios.get(`/api/user/${userStore.user.id}/expense`)
    expenses.value = res.data
    // 選択リセット
    selectedExpenses.value = []
}

watch(() => userStore.user, (newUser) => {
    if (newUser) fetchExpenses()
})

onMounted(() => {
    fetchCategories()
    if (userStore.user) fetchExpenses()
})

// モーダル系
const openAddModal = () => {
    editingExpense.value = null
    modalExpenseVisible.value = true
}

const openEditModal = (expense: Expense) => {
    editingExpense.value = { ...expense }
    modalExpenseVisible.value = true
}

const closeModal = () => {
    modalExpenseVisible.value = false
    modalApprovalVisible.value = false
    editingExpense.value = null
}

const onExpenseSaved = async () => {
    modalExpenseVisible.value = false
    editingExpense.value = null
    alertData.value = new Alert('登録に成功しました', AlertType.Success)
    await fetchExpenses()
}

const onApprovalSaved = async () => {
    modalApprovalVisible.value = false
    selectedExpenses.value = []
    alertData.value = new Alert('申請が完了しました', AlertType.Success)
}

// 削除確認モーダル
const confirmDelete = (expense: Expense) => {
    deleteTargetId.value = expense.id
    confirmMessage.value = expense.title + 'を削除しますか？'
    confirmVisible.value = true
}

// 実際の削除処理
const deleteExpense = async () => {
    if (!deleteTargetId.value || !userStore.user) return
    try {
        await axios.delete(`/api/user/${userStore.user.id}/expense/${deleteTargetId.value}`)
        alertData.value = new Alert('削除に成功しました', AlertType.Success)
        await fetchExpenses()
    } catch (error) {
        alertData.value = new Alert('削除に失敗しました', AlertType.Error)
    } finally {
        confirmVisible.value = false
        deleteTargetId.value = null
    }
}

// カテゴリ取得
const fetchCategories = async () => {
    try {
        const res = await axios.get('/api/expense/category')
        expenseCategories.value = res.data
    } catch (error) {
        alertData.value = new Alert('カテゴリー取得に失敗しました', AlertType.Error)
    }
}

// チェックボックス切り替え
const toggleSelectExpense = (expense: Expense) => {
    const index = selectedExpenses.value.findIndex(e => e.id === expense.id)
    if (index >= 0) {
        selectedExpenses.value.splice(index, 1)
    } else {
        selectedExpenses.value.push(expense)
    }
}

// 申請モーダル表示
const openApprovalModal = () => {
    console.log('選択された経費:', selectedExpenses.value)
    modalApprovalVisible.value = true
}
</script>
