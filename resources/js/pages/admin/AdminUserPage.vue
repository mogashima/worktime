<template>
    <NavigationComponent />
    <div class="admin-attendance-page page">
        <h2>[管理者]勤怠管理</h2>
        <!-- ユーザー一覧表示 -->
        <UserListComponent v-if="!selectedUser" @select-user="handleSelectUser" />

        <!-- 勤怠情報表示 -->
        <div v-else>
            <button class="btn-cancel mr-small" @click="selectedUser = null">
                社員一覧に戻る
            </button>
            <!-- 追加ボタン -->
            <button @click="openAddModal" class="btn-add">出勤記録を追加</button>

            <AdminAttendanceListComponent ref="attendanceListRef" :user="selectedUser" @edit-attendance="openEditModal"
                @delete-attendance="deleteAttendance" />

            <!-- モーダル表示 -->
            <div v-if="modalVisible" class="modal-overlay" @click.self="closeModal">
                <div class="modal-content">
                    <AttendanceFormComponent :user-id="selectedUser?.id ?? null" :attendance="editingAttendance"
                        @attendance-saved="onAttendanceSaved" @edit-cancel="closeModal" />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import type { ComponentPublicInstance } from 'vue'
import axios from '@/plugins/axios'
import NavigationComponent from '@/components/NavigationComponent.vue'
import UserListComponent from '@/components/UserListComponent.vue'
import AdminAttendanceListComponent from '@/components/attendance/AdminAttendanceListComponent.vue'
import AttendanceFormComponent from '@/components/attendance/AttendanceFormComponent.vue'
import type { User } from '@/types/userType'
import type { Attendance } from '@/types/attendanceType'

const selectedUser = ref<User | null>(null)
const editingAttendance = ref<Attendance | null>(null)
const modalVisible = ref(false)

const attendanceListRef = ref<ComponentPublicInstance<{ fetchAttendances: () => Promise<void> }>>()

const handleSelectUser = (user: User) => {
    selectedUser.value = user
}

const openAddModal = () => {
    editingAttendance.value = null
    modalVisible.value = true
}

const openEditModal = (attendance: Attendance) => {
    editingAttendance.value = { ...attendance }
    modalVisible.value = true
}

const closeModal = () => {
    modalVisible.value = false
    editingAttendance.value = null
}

const onAttendanceSaved = async () => {
    modalVisible.value = false
    editingAttendance.value = null
    if (attendanceListRef.value?.fetchAttendances) {
        await attendanceListRef.value.fetchAttendances()
    }
}

const deleteAttendance = async (attendanceId: number) => {
    if (!selectedUser.value) return
    try {
        await axios.delete(`/api/admin/user/${selectedUser.value.id}/attendance/${attendanceId}`)
        alert('削除しました。')
        if (attendanceListRef.value?.fetchAttendances) {
            await attendanceListRef.value.fetchAttendances()
        }
    } catch (error) {
        console.error('勤怠削除エラー:', error)
        alert('削除に失敗しました。')
    }
}
</script>