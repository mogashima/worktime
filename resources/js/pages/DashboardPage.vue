<template>
    <NavigationComponent />
    <div class="dashboard-container" v-if="userStore.user">
        <h2>ダッシュボード</h2>
        <p>ようこそ、{{ userStore.user.name }} さん！</p>

        <div class="attendance-buttons">
            <button v-if="!hasAttendance && !isLoading" @click="startWork" class="btn-start">
                勤務開始
            </button>

            <div v-else-if="isWorking">
                <button @click="endWork" :disabled="isLoading || isOnBreak" class="btn-end">
                    勤務終了
                </button>

                <button v-if="!isOnBreak" @click="startBreak" :disabled="isLoading" class="btn-break-start">
                    休憩開始
                </button>
                <button v-else @click="endBreak" :disabled="isLoading" class="btn-break-end">
                    休憩終了
                </button>
            </div>

            <p v-else-if="isWorkEnded">本日の勤務は終了しました</p>
        </div>

        <section v-if="hasAttendance" class="attendance-summary">
            <h3>本日の勤務詳細</h3>
            <p>
                {{ currentAttendance?.clock_in }}
                　～　
                {{ currentAttendance?.clock_out ? currentAttendance.clock_out : '未終了' }}
            </p>
            <table class="break-table" v-if="currentAttendance?.attendance_breaks?.length">
                <thead>
                    <tr>
                        <th>休憩開始</th>
                        <th>休憩終了</th>
                        <th>休憩時間</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="breakItem in currentAttendance.attendance_breaks" :key="breakItem.id">
                        <td>{{ breakItem.start_time }}</td>
                        <td>
                            {{ breakItem.end_time ?? '休憩中' }}
                        </td>
                        <td>
                            {{ breakItem.end_time ? diffMinutes(breakItem.start_time, breakItem.end_time) + '分' : '-' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <p v-else>休憩履歴はありません</p>
        </section>
    </div>

    <div v-else class="loading-container">
        <p>読み込み中...</p>
    </div>
</template>

<script lang="ts" setup>
import { ref, watch, onMounted } from 'vue'
import axios from 'axios'
import { useUserStore } from '@/stores/userStore'
import NavigationComponent from '@/components/NavigationComponent.vue'
import type { Attendance } from '@/types/attendanceType'

const userStore = useUserStore()

const isLoading = ref(false)
const currentAttendance = ref<Attendance | null>(null)

const hasAttendance = ref(false)
const isWorking = ref(false)
const isWorkEnded = ref(false)
const isOnBreak = ref(false)

watch(currentAttendance, (val) => {
    hasAttendance.value = val !== null
    isWorking.value = val !== null && !val.clock_out
    isWorkEnded.value = val !== null && !!val.clock_out

    // 休憩中判定: attendance_breaks の中に end_time が null のものがあれば休憩中
    if (val && val.attendance_breaks && Array.isArray(val.attendance_breaks)) {
        isOnBreak.value = val.attendance_breaks.some(b => b.end_time === null)
    } else {
        isOnBreak.value = false
    }
})

// 勤務開始
const startWork = async () => {
    isLoading.value = true
    try {
        await axios.post('/api/attendance/start')
        await fetchCurrentAttendance()
        alert('勤務を開始しました')
    } catch (error) {
        console.error(error)
        alert('勤務開始に失敗しました')
    } finally {
        isLoading.value = false
    }
}

// 勤務終了
const endWork = async () => {
    isLoading.value = true
    try {
        await axios.post('/api/attendance/end')
        currentAttendance.value = null
        alert('勤務を終了しました')
    } catch (error) {
        console.error(error)
        alert('勤務終了に失敗しました')
    } finally {
        isLoading.value = false
    }
}

// 休憩開始
const startBreak = async () => {
    if (!currentAttendance.value) {
        alert('勤務開始していません')
        return
    }
    isLoading.value = true
    try {
        await axios.post('/api/attendance/break/start')
        await fetchCurrentAttendance()  // 休憩履歴も含まれて返るため再取得
        alert('休憩を開始しました')
    } catch (error) {
        console.error(error)
        alert('休憩開始に失敗しました')
    } finally {
        isLoading.value = false
    }
}

// 休憩終了
const endBreak = async () => {
    if (
        !currentAttendance.value ||
        !currentAttendance.value.attendance_breaks?.some((b) => b.end_time === null)
    ) {
        alert('休憩中ではありません')
        return
    }
    isLoading.value = true
    try {
        await axios.post('/api/attendance/break/end')
        await fetchCurrentAttendance()
        alert('休憩を終了しました')
    } catch (error) {
        console.error(error)
        alert('休憩終了に失敗しました')
    } finally {
        isLoading.value = false
    }
}

// 勤務情報取得（勤務開始・終了情報 + 休憩履歴）
const fetchCurrentAttendance = async () => {
    try {
        const res = await axios.get('/api/attendance/current')
        currentAttendance.value =
            Object.keys(res.data).length === 0 ? null : res.data
    } catch (error) {
        console.error(error)
        currentAttendance.value = null
    }
}

// 日時フォーマット（yyyy/mm/dd hh:mm形式）
const formatDateTime = (dateStr: string) => {
    const date = new Date(dateStr)
    return date.toLocaleString('ja-JP', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
    })
}

const timeToSeconds = (timeStr: string): number => {
    const [h, m, s] = timeStr.split(":").map(Number)
    return h * 3600 + m * 60 + s
}

// 休憩時間の分差を計算
const diffMinutes = (startStr: string, endStr: string) => {
    const start = timeToSeconds(startStr.slice(0, 5) + ':00')
    const end = timeToSeconds(endStr.slice(0, 5) + ':00')
    return Math.floor((end - start) / 60)
}

onMounted(() => {
    fetchCurrentAttendance()
})
</script>
