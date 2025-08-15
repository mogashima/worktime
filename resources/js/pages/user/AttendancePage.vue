<template>
    <NavigationComponent />
    <div class="attendance-page page">
        <h2>出勤簿管理</h2>
        <AlertComponent :alert-data="alertData" />
        <!-- 追加ボタン -->
        <button @click="openAddModal" class="btn-add">出勤記録を追加</button>

        <hr />

        <AttendanceListComponent :attendances="attendances" @edit-attendance="openEditModal" />

        <!-- モーダル -->
        <div v-if="modalVisible" class="modal-overlay" @click.self="closeModal">
            <div class="modal-content">
                <AttendanceFormComponent :user-id="userStore.user ? userStore.user.id : null"
                    :attendance="editingAttendance" 
                    @attendance-stored="onAttendanceStored" 
                    @attendance-updated="onAttendanceUpdated"
                    @edit-cancel="closeModal" />
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import axios from '@/plugins/axios'
import { useUserStore } from '@/stores/userStore'
import { Attendance } from '@/types/attendanceType'
import { Alert, AlertType } from '@/types/alertType'
import NavigationComponent from '@/components/NavigationComponent.vue'
import AttendanceFormComponent from '@/components/attendance/AttendanceFormComponent.vue'
import AttendanceListComponent from '@/components/attendance/AttendanceListComponent.vue'
import AlertComponent from '@/components/common/AlertComponent.vue'

const attendances = ref<Attendance[]>([])
const editingAttendance = ref<Attendance | null>(null)
const modalVisible = ref(false)
const userStore = useUserStore()
const alertData = ref<Alert | null>(null)

const fetchAttendances = async () => {
    if (!userStore.user) return

    try {
        const res = await axios.get(`/api/attendance`)
        attendances.value = res.data
    } catch (error) {
        console.error('出勤データ取得エラー:', error)
    }
}

// userStore.userがセットされたら出勤データ取得
watch(
    () => userStore.user,
    (newUser) => {
        if (newUser) {
            fetchAttendances()
        }
    }
)

// 念のためマウント時にもfetch
onMounted(() => {
    if (userStore.user) {
        fetchAttendances()
    }
})

// 「追加」ボタン押下
const openAddModal = () => {
    editingAttendance.value = null
    modalVisible.value = true
}

// 編集ボタン押下時
const openEditModal = (attendance: Attendance) => {
    editingAttendance.value = { ...attendance }
    modalVisible.value = true
}

// モーダル閉じる
const closeModal = () => {
    modalVisible.value = false
    editingAttendance.value = null
}

// フォーム保存後の処理
const onAttendanceStored = async () => {
    modalVisible.value = false
    editingAttendance.value = null
    await fetchAttendances()
    alertData.value = new Alert('登録に成功しました', AlertType.Success)
}

// フォーム更新後の処理
const onAttendanceUpdated = async () => {
    modalVisible.value = false
    editingAttendance.value = null
    await fetchAttendances()
    alertData.value = new Alert('変更を申請しました', AlertType.Success)
}
</script>