<template>
    <div class="approval-attendance-list">
        <h3>勤怠承認</h3>
        <table class="page-list">
            <thead>
                <tr>
                    <th v-if="isAdmin">ユーザー名</th>
                    <th>申請日</th>
                    <th>日付</th>
                    <th>出勤時刻</th>
                    <th>退勤時刻</th>
                    <th>状態</th>
                    <th v-if="isAdmin">操作</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="attendance in approvalAttendances" :key="attendance.id"
                    :class="rowClass(attendance.approval_status.status_code)">
                    <td v-if="isAdmin">{{ attendance.user.name }}</td>
                    <td>{{ formatDate(attendance.created_at) }}</td>
                    <td>{{ formatDate(attendance.date) }}</td>
                    <td>{{ attendance.clock_in.slice(0, 5) }}</td>
                    <td>{{ attendance.clock_out ? attendance.clock_out.slice(0, 5) : '' }}</td>
                    <td>{{ attendance.approval_status.name }}</td>
                    <td v-if="isAdmin">
                        <button class="btn-add mr-small" @click="approve(attendance.id)"
                            :disabled="attendance.approval_status.status_code !== StatusCode.Pending">承認</button>
                        <button class="btn-delete" @click="reject(attendance.id)"
                            :disabled="attendance.approval_status.status_code !== StatusCode.Pending">却下</button>
                    </td>
                </tr>
                <tr v-if="approvalAttendances.length === 0">
                    <td colspan="7" class="text-center">承認待ち勤怠がありません</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { StatusCode } from '@/types/approvalStatusType'
import { RoleCode } from '@/types/roleType'
import { useUserStore } from '@/stores/userStore'
import type { ApprovalAttendance } from '@/types/approvalAttendanceType'

const props = defineProps<{
    approvalAttendances: ApprovalAttendance[]
}>()

const emit = defineEmits(['approve', 'reject'])

const userStore = useUserStore()
const isAdmin = computed(() => userStore.user?.role_code === RoleCode.Admin)

const rowClass = (statusCode: string) => {
    switch (statusCode) {
        case StatusCode.Pending:
            return 'row-pending'
        case StatusCode.Approved:
            return 'row-approved'
        case StatusCode.Rejected:
            return 'row-rejected'
        default:
            return ''
    }
}

const formatDate = (dateStr: string) => {
    const date = new Date(dateStr)
    return date.toLocaleDateString('ja-JP', { year: 'numeric', month: '2-digit', day: '2-digit' })
}

const approve = (id: number) => emit('approve', id)
const reject = (id: number) => emit('reject', id)
</script>
