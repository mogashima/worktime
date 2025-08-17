<template>
    <NavigationComponent />
    <div class="admin-attendance-page page">
        <h2>[管理者]勤怠管理</h2>
        <AlertComponent :alert-data="alertData" />
        <!-- ユーザー一覧表示 -->
        <UserListComponent v-if="visibleContent === VisibleContent.User" :users="users"
            @select-attendance="selectAttendance" @edit-user="editUser" />

        <!-- 勤怠情報表示 -->
        <div v-if="visibleContent === VisibleContent.Attendance">
            <button class="btn-cancel mr-small" @click="clickBackUserListButton">
                社員一覧に戻る
            </button>
            <button @click="clickAddAttendanceButton" class="btn-add">出勤記録を追加</button>

            <AdminAttendanceListComponent ref="attendanceListRef" :user="selectedUser" :attendances="attendances"
                @edit-attendance="clickEditAttendanceButton" @delete-attendance="requestDeleteAttendance" />


        </div>

        <!-- ユーザーフォーム -->
        <div v-if="userModalVisible" class="modal-overlay" @click.self="closeUserModal">
            <div class="modal-content" v-if="visibleContent === VisibleContent.User">
                <UserFormComponent :user="selectedUser" @user-store="onUserStored" @user-update="onUserUpdated"
                    @edit-cancel="closeUserModal" />
            </div>
        </div>
        <!-- 勤怠フォーム -->
        <div v-if="attendanceModalVisible" class="modal-overlay" @click.self="closeAttendanceModal">
            <div class="modal-content" v-if="visibleContent === VisibleContent.Attendance">
                <AttendanceFormComponent :user-id="selectedUser?.id ?? null" :attendance="selectedAttendance"
                    @attendance-stored="onAttendanceStored" @attendance-updated="onAttendanceUpdated"
                    @edit-cancel="closeAttendanceModal" />
            </div>
        </div>

        <!-- 確認モーダル -->
        <ConfirmComponent v-if="confirmVisible" title="削除確認" :message="confirmMessage" @confirm="deleteAttendance"
            @cancel="confirmVisible = false" />
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import type { ComponentPublicInstance } from 'vue'
import axios from '@/plugins/axios'
import NavigationComponent from '@/components/common/NavigationComponent.vue'
import UserListComponent from '@/components/user/UserListComponent.vue'
import UserFormComponent from '@/components/user/UserFormComponent.vue'
import AdminAttendanceListComponent from '@/components/attendance/AdminAttendanceListComponent.vue'
import AttendanceFormComponent from '@/components/attendance/AttendanceFormComponent.vue'
import AlertComponent from '@/components/common/AlertComponent.vue'
import ConfirmComponent from '@/components/common/ConfirmComponent.vue'
import { Alert, AlertType } from '@/types/alertType'
import type { User } from '@/types/userType'
import type { Attendance } from '@/types/attendanceType'

enum VisibleContent {
    User = "User",
    Attendance = "Attendance",
}

const users = ref<User[]>([])
const attendances = ref<Attendance[]>([])
const selectedUser = ref<User | null>(null)
const selectedAttendance = ref<Attendance | null>(null)

// 表示切替
const attendanceModalVisible = ref(false)
const userModalVisible = ref(false)
const visibleContent = ref(VisibleContent.User)

// Confirmモーダル用
const confirmVisible = ref(false)
const confirmMessage = ref('')
const deleteTargetId = ref<number | null>(null)

// メッセージ表示
const alertData = ref<Alert | null>(null)



onMounted(() => {
    fetchUsers()
})

const selectAttendance = (user: User) => {
    selectedUser.value = user
    visibleContent.value = VisibleContent.Attendance
    fetchAttendances()
}

const editUser = (user: User) => {
    selectedUser.value = user
    userModalVisible.value = true
    visibleContent.value = VisibleContent.User
}

const clickBackUserListButton = () => {
    selectedUser.value = null
    visibleContent.value = VisibleContent.User
}

const clickAddAttendanceButton = () => {
    selectedAttendance.value = null
    attendanceModalVisible.value = true
    visibleContent.value = VisibleContent.Attendance
}

const clickEditAttendanceButton = (attendance: Attendance) => {
    selectedAttendance.value = { ...attendance }
    attendanceModalVisible.value = true
    visibleContent.value = VisibleContent.Attendance
}

const closeUserModal = () => {
    userModalVisible.value = false
    selectedUser.value = null
    visibleContent.value = VisibleContent.User
}
const closeAttendanceModal = () => {
    attendanceModalVisible.value = false
    selectedAttendance.value = null
    visibleContent.value = VisibleContent.Attendance
}

const onAttendanceStored = async () => {
    closeAttendanceModal()
    fetchAttendances()
    alertData.value = new Alert('登録に成功しました', AlertType.Success)
}

const onAttendanceUpdated = async () => {
    closeAttendanceModal()
    fetchAttendances()
    alertData.value = new Alert('登録に成功しました', AlertType.Success)
}

const onUserUpdated = async () => {
    closeUserModal()
    fetchUsers()
    alertData.value = new Alert('更新に成功しました', AlertType.Success)
}

const onUserStored = async () => {
    closeUserModal()
    fetchUsers()
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
        fetchAttendances()
    } catch (error) {
        alertData.value = new Alert('削除に失敗しました', AlertType.Error)
    } finally {
        confirmVisible.value = false
    }
}

const fetchUsers = async () => {
    try {
        const response = await axios.get('/api/user')
        users.value = response.data
    } catch (error) {
        alertData.value = new Alert('社員情報の取得に失敗しました', AlertType.Error)
    }
}

const fetchAttendances = async () => {
    try {
        const userId = selectedUser?.value?.id || 0
        const res = await axios.get(`/api/admin/user/${userId}/attendance`)
        attendances.value = res.data
    } catch (error) {
        console.error('勤怠データの取得に失敗しました', error)
    }
}
</script>
