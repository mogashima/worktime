<template>
    <NavigationComponent />
    <div class="approval-page page">
        <h2>承認履歴</h2>
        <AlertComponent :alert-data="alertData" />
        <ApprovalAttendanceListComponent :approval-attendances="approvalAttendances" />
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import NavigationComponent from '@/components/NavigationComponent.vue'
import ApprovalAttendanceListComponent from '@/components/approval/ApprovalAttendanceListComponent.vue'
import AlertComponent from '@/components/common/AlertComponent.vue'
import axios from '@/plugins/axios'
import type { ApprovalAttendance } from '@/types/approvalAttendanceType'
import { Alert, AlertType } from '@/types/alertType'

const approvalAttendances = ref<ApprovalAttendance[]>([])
const alertData = ref<Alert | null>(null)

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
})
</script>
