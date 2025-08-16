<template>
    <NavigationComponent />
    <div class="admni-approval-page page">
        <h2>[管理者]承認管理</h2>
        <AlertComponent :alert-data="alertData" />
        <ApprovalAttendanceListComponent :approval-attendances="approvalAttendances" @approve="handleAttendanceApprove"
            @reject="handleAttendanceReject" />
        <p class="mt-regular"></p>
        <!-- 経費履歴 -->
        <ApprovalExpenseListComponent :approval-expenses="approvalExpenses"
            @select-approval-expense="openApprovalExpenseModal" @approve="handleExpenseApprove"
            @reject="handleExpenseReject" />

        <!-- 経費詳細モーダル -->
        <div v-if="expenseModalVisible" class="modal-overlay" @click.self="closeModal">
            <div class="modal-large-content">
                <ApprovalExpenseFormComponent :approvalExpense="selectedApprovalExpense" @close="closeModal"
                    @approve="handleExpenseApprove" @reject="handleExpenseReject" />
            </div>
        </div>

    </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import NavigationComponent from '@/components/common/NavigationComponent.vue'
import ApprovalAttendanceListComponent from '@/components/approval/ApprovalAttendanceListComponent.vue'
import ApprovalExpenseListComponent from '@/components/approval/ApprovalExpenseListComponent.vue'
import ApprovalExpenseFormComponent from '@/components/approval/ApprovalExpenseFormComponent.vue'
import AlertComponent from '@/components/common/AlertComponent.vue'
import axios from '@/plugins/axios'
import { StatusCode } from '@/types/approvalStatusType'
import { Alert, AlertType } from '@/types/alertType'
import type { ApprovalExpense } from '@/types/approvalExpenseType'

const alertData = ref<Alert | null>(null)
const approvalAttendances = ref([])
const approvalExpenses = ref<ApprovalExpense[]>([])
const selectedApprovalExpense = ref<ApprovalExpense | null>(null)
const expenseModalVisible = ref(false)

onMounted(() => {
    fetchApprovalAttendances()
    fetchApprovalExpenses()
})

const fetchApprovalAttendances = async () => {
    try {
        const res = await axios.get('/api/admin/approval/attendance')
        approvalAttendances.value = res.data
    } catch (error) {
        alertData.value = new Alert('承認待ち勤怠の取得に失敗しました', AlertType.Error)
    }
}

const fetchApprovalExpenses = async () => {
    try {
        const res = await axios.get('/api/admin/approval/expense')
        approvalExpenses.value = res.data
    } catch (error) {
        alertData.value = new Alert('承認経費の取得に失敗しました', AlertType.Error)
    }
}


const handleAttendanceApprove = async (approvalId: number) => {
    try {
        await axios.put(`/api/admin/approval/attendance/${approvalId}`, { status_code: StatusCode.Approved })
        alertData.value = new Alert('承認に成功しました', AlertType.Success)
        fetchApprovalAttendances()
    } catch (error) {
        alertData.value = new Alert('承認に失敗しました', AlertType.Error)
    }
}

const handleAttendanceReject = async (approvalId: number) => {
    try {
        await axios.put(`/api/admin/approval/attendance/${approvalId}`, { status_code: StatusCode.Rejected })
        alertData.value = new Alert('却下に成功しました', AlertType.Success)
        closeModal()
        fetchApprovalAttendances()
    } catch (error) {
        alertData.value = new Alert('却下に失敗しました', AlertType.Error)
    }
}

const handleExpenseApprove = async (approvalId: number) => {
    try {
        await axios.put(`/api/admin/approval/expense/${approvalId}`, { status_code: StatusCode.Approved })
        alertData.value = new Alert('承認に成功しました', AlertType.Success)
        closeModal()
        fetchApprovalExpenses()
    } catch (error) {
        alertData.value = new Alert('承認に失敗しました', AlertType.Error)
    }
}

const handleExpenseReject = async (approvalId: number) => {
    try {
        await axios.put(`/api/admin/approval/expense/${approvalId}`, { status_code: StatusCode.Rejected })
        alertData.value = new Alert('却下に成功しました', AlertType.Success)
        fetchApprovalExpenses()
    } catch (error) {
        alertData.value = new Alert('却下に失敗しました', AlertType.Error)
    }
}

const openApprovalExpenseModal = (approvalExpense: ApprovalExpense) => {
    selectedApprovalExpense.value = approvalExpense
    expenseModalVisible.value = true
}

const closeModal = () => {
    expenseModalVisible.value = false
    selectedApprovalExpense.value = null
}

</script>
