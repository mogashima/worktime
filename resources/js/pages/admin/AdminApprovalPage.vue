<template>
    <NavigationComponent />
    <div class="admni-approval-page page">
        <h2>[管理者]承認管理</h2>
        <ApprovalAttendanceListComponent @approve="handleApprove" @reject="handleReject" />
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import NavigationComponent from '@/components/NavigationComponent.vue'
import ApprovalAttendanceListComponent from '@/components/approval/ApprovalAttendanceListComponent.vue'
import axios from '@/plugins/axios'
import { StatusCode } from '@/types/approvalStatusType'


const listRef = ref<InstanceType<typeof ApprovalAttendanceListComponent> | null>(null)
const reloadList = () => {
    listRef.value?.fetchApprovalAttendances?.() // 子側にfetchListメソッドを作っておく
}

const handleApprove = async (approvalId: number) => {
    try {
        await axios.put(`/api/admin/approval/attendance/${approvalId}`, { status_code: StatusCode.Approved })
        alert('承認しました')
        reloadList()
        // 必要に応じてリストの再取得など処理を実装
    } catch (error) {
        console.error('承認エラー', error)
        alert('承認に失敗しました')
    }
}

const handleReject = async (approvalId: number) => {
    try {
        await axios.put(`/api/admin/approval/attendance/${approvalId}`, { status_code: StatusCode.Rejected })
        alert('却下しました')
        reloadList()
        // 必要に応じてリストの再取得など処理を実装
    } catch (error) {
        console.error('却下エラー', error)
        alert('却下に失敗しました')
    }
}
</script>