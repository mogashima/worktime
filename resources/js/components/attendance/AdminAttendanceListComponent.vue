<template>
    <div class="admin-attendance-list">
        <p>ユーザー名: {{ user?.name }}</p>
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
                    <td>{{ attendance.start_time }}</td>
                    <td>{{ attendance.end_time }}</td>
                    <td>
                        <button class="btn-edit mr-small" @click="$emit('edit-attendance', attendance)">編集</button>
                        <button class="btn-delete" @click="$emit('delete-attendance', attendance)">削除</button>
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

const props = defineProps<{
    user: User | null
    attendances: Attendance[]
}>()

const emit = defineEmits(['edit-attendance', 'delete-attendance'])

const formatDate = (dateStr: string) => {
    const date = new Date(dateStr)
    return date.toLocaleDateString('ja-JP', { year: 'numeric', month: '2-digit', day: '2-digit' })
}
</script>
