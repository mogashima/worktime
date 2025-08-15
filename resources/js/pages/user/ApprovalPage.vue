<template>
    <NavigationComponent />
    <div class="approval-page page">
        <h2>申請履歴</h2>
        <AlertComponent :alert-data="alertData" />

        <!-- 勤怠履歴 -->
        <ApprovalAttendanceListComponent :approval-attendances="approvalAttendances" />

        <p class="mt-regular"></p>
        <!-- 経費履歴 -->
        <ApprovalExpenseListComponent :approval-expenses="approvalExpenses"
            @select-approval-expense="openApprovalExpenseModal" />

        <!-- 経費詳細モーダル -->
        <div v-if="expenseModalVisible" class="modal-overlay" @click.self="closeModal">
            <div class="modal-large-content">
                <ApprovalExpenseFormComponent :approvalExpense="selectedApprovalExpense" @close="closeModal"
                    @delete-approval-expense="deleteApprovalExpense" />
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from '@/plugins/axios'

import NavigationComponent from '@/components/NavigationComponent.vue'
import ApprovalAttendanceListComponent from '@/components/approval/ApprovalAttendanceListComponent.vue'
import ApprovalExpenseListComponent from '@/components/approval/ApprovalExpenseListComponent.vue'
import ApprovalExpenseFormComponent from '@/components/approval/ApprovalExpenseFormComponent.vue'
import AlertComponent from '@/components/common/AlertComponent.vue'
import type { ApprovalAttendance } from '@/types/approvalAttendanceType'
import type { ApprovalExpense } from '@/types/approvalExpenseType'
import { Alert, AlertType } from '@/types/alertType'

const approvalAttendances = ref<ApprovalAttendance[]>([])
const approvalExpenses = ref<ApprovalExpense[]>([])
const selectedApprovalExpense = ref<ApprovalExpense | null>(null)
const expenseModalVisible = ref(false)

const alertData = ref<Alert | null>(null)

const fetchApprovalExpenses = async () => {
    try {
        const res = await axios.get('/api/approval/expense')
        approvalExpenses.value = res.data
    } catch (error) {
        alertData.value = new Alert('承認経費の取得に失敗しました', AlertType.Error)
    }
}

const fetchApprovalAttendances = async () => {
    try {
        const res = await axios.get('/api/approval/attendance')
        approvalAttendances.value = res.data
    } catch (error) {
        alertData.value = new Alert('承認待ち勤怠の取得に失敗しました', AlertType.Error)
    }
}


onMounted(() => {
    fetchApprovalAttendances()
    fetchApprovalExpenses()
})

const openApprovalExpenseModal = (approvalExpense: ApprovalExpense) => {
    selectedApprovalExpense.value = approvalExpense
    expenseModalVisible.value = true
}

const closeModal = () => {
    expenseModalVisible.value = false
    selectedApprovalExpense.value = null
}

const deleteApprovalExpense = async (approvalExpense: ApprovalExpense) => {
    const deleteId = approvalExpense.id
    try {
        const res = await axios.delete(`/api/approval/expense/${deleteId}`)
        closeModal()
        alertData.value = new Alert('削除に成功しました', AlertType.Success)
        fetchApprovalExpenses()
    } catch (error) {
        alertData.value = new Alert('経費申請の削除に失敗しました', AlertType.Error)
    }
}
</script>