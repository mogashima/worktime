<template>
    <NavigationComponent />
    <div class="admin-attendance-page page">
        <h2>[管理者]勤怠管理</h2>
        <AlertComponent :alert-data="alertData" />

        <!-- ユーザー一覧表示 -->
        <UserListComponent v-if="!selectedUser" @select-user="handleSelectUser" />

        <!-- 勤怠情報表示 -->
        <div v-else>
            <button class="btn-cancel mr-small" @click="selectedUser = null">
                社員一覧に戻る
            </button>
            <button @click="openAddModal" class="btn-add">出勤記録を追加</button>

            <AdminAttendanceListComponent ref="attendanceListRef" :user="selectedUser" @edit-attendance="openEditModal"
                @delete-attendance="requestDeleteAttendance" />

            <!-- モーダル表示 -->
            <div v-if="modalVisible" class="modal-overlay" @click.self="closeModal">
                <div class="modal-content">
                    <AttendanceFormComponent :user-id="selectedUser?.id ?? null" :attendance="editingAttendance"
                        @attendance-stored="onAttendanceStored" @attendance-updated="onAttendanceUpdated"
                        @edit-cancel="closeModal" />
                </div>
            </div>

            <!-- 確認モーダル -->
            <ConfirmComponent v-if="confirmVisible" title="削除確認" :message="confirmMessage" @confirm="deleteAttendance"
                @cancel="confirmVisible = false" />
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
import AlertComponent from '@/components/common/AlertComponent.vue'
import ConfirmComponent from '@/components/common/ConfirmComponent.vue'
import { Alert, AlertType } from '@/types/alertType'
import type { User } from '@/types/userType'
import type { Attendance } from '@/types/attendanceType'

const selectedUser = ref<User | null>(null)
const editingAttendance = ref<Attendance | null>(null)
const modalVisible = ref(false)
const alertData = ref<Alert | null>(null)

// Confirmモーダル用
const confirmVisible = ref(false)
const confirmMessage = ref('')
const deleteTargetId = ref<number | null>(null)

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

const onAttendanceStored = async () => {
    modalVisible.value = false
    editingAttendance.value = null
    if (attendanceListRef.value?.fetchAttendances) {
        await attendanceListRef.value.fetchAttendances()
    }
    alertData.value = new Alert('登録に成功しました', AlertType.Success)
}

const onAttendanceUpdated = async () => {
    modalVisible.value = false
    editingAttendance.value = null
    if (attendanceListRef.value?.fetchAttendances) {
        await attendanceListRef.value.fetchAttendances()
    }
    alertData.value = new Alert('登録に成功しました', AlertType.Success)
}

const requestDeleteAttendance = (attendance: Attendance) => {
    deleteTargetId.value = attendance.id
    confirmMessage.value = '本当に削除しますか？'
    confirmVisible.value = true
}

const deleteAttendance = async () => {
    if (!deleteTargetId.value || !selectedUser.value) return
    try {
        await axios.delete(`/api/admin/user/${selectedUser.value.id}/attendance/${deleteTargetId.value}`)
        alertData.value = new Alert('削除に成功しました', AlertType.Success)
        if (attendanceListRef.value?.fetchAttendances) {
            await attendanceListRef.value.fetchAttendances()
        }
    } catch (error) {
        alertData.value = new Alert('削除に失敗しました', AlertType.Error)
    } finally {
        confirmVisible.value = false
        deleteTargetId.value = null
    }
}
</script>
