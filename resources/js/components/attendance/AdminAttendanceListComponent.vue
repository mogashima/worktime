<template>
    <div class="admin-attendance-list">
        <p>ユーザー名: {{ user.name }}</p>
        <table class="page-list">
            <thead>
                <tr>
                    <th>日付</th>
                    <th>出勤時刻</th>
                    <th>退勤時刻</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="attendance in attendances" :key="attendance.id">
                    <td>{{ formatDate(attendance.date) }}</td>
                    <td>{{ attendance.clock_in.slice(0, 5) }}</td>
                    <td>{{ attendance.clock_out ? attendance.clock_out.slice(0, 5) : '' }}</td>
                    <td>
                        <button class="btn-edit mr-small" @click="$emit('edit-attendance', attendance)">編集</button>
                        <button class="btn-delete" @click="confirmDelete(attendance.id)">削除</button>
                    </td>
                </tr>
                <tr v-if="attendances.length === 0">
                    <td colspan="4" class="text-center">勤怠情報がありません</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup lang="ts">
import type { User } from '@/types/userType'
import type { Attendance } from '@/types/attendanceType'
import { ref, onMounted } from 'vue'
import axios from '@/plugins/axios'

const props = defineProps<{
    user: User
}>()

const emit = defineEmits(['edit-attendance', 'delete-attendance'])

const attendances = ref<Attendance[]>([])

const formatDate = (dateStr: string) => {
    const date = new Date(dateStr)
    return date.toLocaleDateString('ja-JP', { year: 'numeric', month: '2-digit', day: '2-digit' })
}

const fetchAttendances = async () => {
    try {
        const res = await axios.get(`/api/admin/user/${props.user.id}/attendance`)
        attendances.value = res.data
    } catch (error) {
        console.error('勤怠データ取得エラー:', error)
    }
}

const confirmDelete = (attendanceId: number) => {
    if (confirm('本当に削除しますか？')) {
        emit('delete-attendance', attendanceId)
    }
}

onMounted(() => {
    fetchAttendances()
})

// 外部にfetchAttendancesを公開
defineExpose({ fetchAttendances })
</script>