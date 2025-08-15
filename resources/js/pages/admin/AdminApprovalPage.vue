<template>
    <NavigationComponent />
    <div class="admni-approval-page page">
        <h2>[管理者]承認管理</h2>
        <AlertComponent :alert-data="alertData" />
        <ApprovalAttendanceListComponent :approval-attendances="approvalAttendances" @approve="handleApprove"
            @reject="handleReject" />
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import NavigationComponent from '@/components/NavigationComponent.vue'
import ApprovalAttendanceListComponent from '@/components/approval/ApprovalAttendanceListComponent.vue'
import AlertComponent from '@/components/common/AlertComponent.vue'
import axios from '@/plugins/axios'
import { StatusCode } from '@/types/approvalStatusType'
import { Alert, AlertType } from '@/types/alertType'

const approvalAttendances = ref([]) // Page 側で管理
const alertData = ref<Alert | null>(null)

const fetchApprovalAttendances = async () => {
    try {
        const res = await axios.get('/api/admin/approval/attendance')
        approvalAttendances.value = res.data
    } catch (error) {
        alertData.value = new Alert('承認待ち勤怠の取得に失敗しました', AlertType.Error)
    }
}

const reloadList = () => fetchApprovalAttendances()

const handleApprove = async (approvalId: number) => {
    try {
        await axios.put(`/api/admin/approval/attendance/${approvalId}`, { status_code: StatusCode.Approved })
        alertData.value = new Alert('承認に成功しました', AlertType.Success)
        reloadList()
    } catch (error) {
        alertData.value = new Alert('承認に失敗しました', AlertType.Error)
    }
}

const handleReject = async (approvalId: number) => {
    try {
        await axios.put(`/api/admin/approval/attendance/${approvalId}`, { status_code: StatusCode.Rejected })
        alertData.value = new Alert('却下に成功しました', AlertType.Success)
        reloadList()
    } catch (error) {
        alertData.value = new Alert('却下に失敗しました', AlertType.Error)
    }
}

onMounted(() => {
    fetchApprovalAttendances()
})
</script>
