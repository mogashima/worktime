<template>
    <div class="attendance-form">
        <h3>{{ isEdit ? '出勤記録編集' : '出勤記録追加' }}</h3>
        <form @submit.prevent="submitForm" class="form-container">
            <div class="form-group">
                <label>日付:</label>
                <input v-model="form.date" type="date" required />
            </div>

            <div class="form-group">
                <label>出勤時間:</label>
                <input v-model="form.clock_in" type="time" required />
            </div>

            <div class="form-group">
                <label>退勤時間:</label>
                <input v-model="form.clock_out" type="time" required />
            </div>

            <div class="form-group">
                <label>休憩時間:</label>
                <div v-for="(breakItem, index) in form.attendance_breaks" :key="index" class="break-item">
                    <input v-model="breakItem.start_time" type="time" required /> ~
                    <input v-model="breakItem.end_time" type="time" />
                    <button type="button" class="btn-delete" @click="removeBreak(index)">
                        削除
                    </button>
                </div>
                <button type="button" class="btn-add-break" @click="addBreak(form.id)">＋ 休憩追加</button>
            </div>

            <div class="form-actions">
                <button class="btn-cancel" type="button" @click="cancelEdit">
                    キャンセル
                </button>
                <button type="submit" :class="isEdit ? 'btn-edit' : 'btn-add'">
                    {{ isEdit ? '更新' : '登録' }}
                </button>
            </div>
        </form>
    </div>
</template>

<script setup lang="ts">
import { reactive, watch, computed } from 'vue'
import axios from '@/plugins/axios'
import { useUserStore } from '@/stores/userStore';
import type { Attendance } from '@/types/attendanceType';
import { AttendanceBreak } from '@/types/attendanceBreakType';

const props = defineProps<{
    userId: number | null
    attendance: Attendance | null
}>()

const emit = defineEmits(['attendance-saved', 'edit-cancel'])

const userStore = useUserStore()

const isEdit = computed(() => !!props.attendance)

const emptyForm = {
    id: 0,
    date: '',
    clock_in: '',
    clock_out: '',
    attendance_breaks: [] as AttendanceBreak[]
}

const form = reactive({ ...emptyForm })

watch(() => props.attendance, (newVal) => {
    if (newVal) {
        form.id = newVal.id || 0
        form.date = newVal.date ? new Date(newVal.date).toLocaleDateString('sv-SE') : ''
        form.clock_in = newVal.clock_in.slice(0, 5)
        form.clock_out = newVal.clock_out ? newVal.clock_out.slice(0, 5) : ''
        form.attendance_breaks = newVal.attendance_breaks
            ? newVal.attendance_breaks.map(b => ({
                id: b.id || 0,
                attendance_id: newVal.id || 0,
                start_time: b.start_time.slice(0, 5),
                end_time: b.end_time ? b.end_time.slice(0, 5) : ''
            }))
            : []
    } else {
        Object.assign(form, emptyForm)
    }
}, { immediate: true })

const submitForm = async () => {
    if (!props.userId) {
        console.error('ユーザーIDが設定されていません')
        return
    }

    try {
        if (isEdit.value && props.attendance) {
            if (userStore.isAdmin()) {
                await axios.put(`/api/admin/user/${props.userId}/attendance/${props.attendance.id}`, form)
            } else {
                await axios.post(`/api/approval/attendance`, form)
            }
        } else {
            if (userStore.isAdmin()) {
                await axios.post(`/api/admin/user/${props.userId}/attendance`, form)
            } else {
                await axios.post(`/api/attendance`, form)
            }
        }
        emit('attendance-saved')
    } catch (error) {
        console.error('出勤登録エラー:', error)
    }
}

const cancelEdit = () => {
    emit('edit-cancel')
}

const addBreak = (attendanceId: number) => {
    form.attendance_breaks.push({ id: 0, attendance_id: attendanceId, start_time: '', end_time: '' })
}

const removeBreak = (index: number) => {
    form.attendance_breaks.splice(index, 1)
}
</script>
