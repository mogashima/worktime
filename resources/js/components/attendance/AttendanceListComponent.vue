<template>
  <div class="attendance-list">
    <p>ユーザー名: {{ userName }}</p>
    <table class="page-list">
      <thead>
        <tr>
          <th>日付</th>
          <th>出勤</th>
          <th>退勤</th>
          <th>操作</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="att in attendances" :key="att.id">
          <td>{{ formatDate(att.date) }}</td>
          <td>{{ att.start_time }}</td>
          <td>{{ att.end_time }}</td>
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
import type { Attendance } from '@/types/attendanceType'

const props = defineProps<{ attendances: Attendance[] }>()

const emit = defineEmits(['edit-attendance'])

const userStore = useUserStore()

const userName = computed(() => userStore.user?.name ?? '未取得')

const formatDate = (dateStr: string) => {
  const date = new Date(dateStr)
  return date.toLocaleDateString('ja-JP', { year: 'numeric', month: '2-digit', day: '2-digit' })
}
</script>