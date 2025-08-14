<template>
  <div class="expense-list">
    <table class="page-list">
      <thead>
        <tr>
          <th>日付</th>
          <th>タイトル</th>
          <th>金額</th>
          <th>カテゴリ</th>
          <th>備考</th>
          <th>操作</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="expense in expenses" :key="expense.id">
          <td>{{ expense.date }}</td>
          <td>{{ expense.title }}</td>
          <td>{{ expense.amount }}</td>
          <td>{{ expense.category?.name }}</td>
          <td>{{ expense.description }}</td>
          <td>
            <button class="btn-edit mr-small" @click="$emit('edit-expense', expense)">
              編集
            </button>
            <button class="btn-delete" @click="$emit('delete-expense', expense)">
              削除
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup lang="ts">
import type { Expense } from '@/types/expenseType'

const props = defineProps<{ expenses: Expense[] }>()
const emit = defineEmits(['edit-expense', 'delete-expense'])
</script>