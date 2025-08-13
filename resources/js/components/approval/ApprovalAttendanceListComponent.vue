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
                    <td>{{ attendance.clock_in }}</td>
                    <td>{{ attendance.clock_out }}</td>
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
import { ref, onMounted, computed } from 'vue'
import axios from '@/plugins/axios'
import { ApprovalAttendance } from '@/types/approvalAttendance'
import { StatusCode } from '@/types/approvalStatus'
import { RoleCode } from '@/types/roleType'
import { useUserStore } from '@/stores/userStore' // 追加

const userStore = useUserStore() // store インスタンス取得
const approvalAttendances = ref<ApprovalAttendance[]>([])

// ログインユーザ ID を取得（computed にすると reactive に追従）
const userId = computed(() => userStore.user?.id)
// 管理者判定
const isAdmin = computed(() => userStore.user?.role_code === RoleCode.Admin)

const fetchApprovalAttendances = async () => {
    try {
        if (isAdmin.value) {
            const res = await axios.get('/api/admin/approval/attendance')
            approvalAttendances.value = res.data
        } else {
            const res = await axios.get('/api/approval/attendance')
            approvalAttendances.value = res.data
        }

    } catch (error) {
        console.error('承認待ち勤怠取得エラー:', error)
    }
}

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

const emit = defineEmits(['approve', 'reject'])

const approve = (id: number) => {
    emit('approve', id)
}

const reject = (id: number) => {
    emit('reject', id)
}

onMounted(() => {
    fetchApprovalAttendances()
})

// 外部にfetchApprovalAttendancesを公開（必要に応じて）
defineExpose({ fetchApprovalAttendances })
</script>
