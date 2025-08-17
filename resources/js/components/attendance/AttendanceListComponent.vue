<template>
  <div class="attendance-list">
    <p>ユーザー名: {{ userName }}</p>
    <table class="page-list">
      <thead>
        <tr>
          <th>日付</th>
          <th>出勤</th>
          <th>退勤</th>
          <th>勤務時間</th>
          <th>休憩時間</th>
          <th>操作</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="att in attendances" :key="att.id">
          <td>{{ DateFormatter.formatDate(att.date) }}</td>
          <td>{{ att.start_time }}</td>
          <td>{{ att.end_time }}</td>
          <td>{{ TimeCalculator.format(att.work_value - att.break_value) }}</td>
          <td>{{ TimeCalculator.format(att.break_value) }}</td>
          <td>
            <button class="btn-edit" @click="$emit('edit-attendance', att)">編集</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useUserStore } from '@/stores/userStore'
import { TimeCalculator } from '@/utils/timeCalculator';
import { DateFormatter } from '@/utils/dateFormatter';
import { AttendanceModel } from '@/models/attendanceModel';

const props = defineProps<{ attendances: AttendanceModel[] }>()

const emit = defineEmits(['edit-attendance'])

const userStore = useUserStore()

const userName = computed(() => userStore.user?.name ?? '未取得')

</script>