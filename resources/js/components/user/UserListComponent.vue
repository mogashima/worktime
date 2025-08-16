<template>
    <div class="user-list">
        <table class="page-list">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>ログインID</th>
                    <th>名前</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="user in users" :key="user.id" class="user-row">
                    <td>{{ user.id }}</td>
                    <td>{{ user.login_id }}</td>
                    <td>{{ user.name }}</td>
                    <td class="text-center">
                        <button class="btn-add mr-small" @click="clickAttendanceButton(user)">
                            勤怠情報
                        </button>
                        <button class="btn-edit" @click="clickEditUserButton(user)">
                            社員編集
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from '@/plugins/axios'
import { User } from '@/types/userType'


const props = defineProps<{
    users: User[]
}>()

const emit = defineEmits(['select-attendance', 'edit-user'])

const clickAttendanceButton = (user: User) => {
    emit('select-attendance', user)
}

const clickEditUserButton = (user: User) => {
    emit('edit-user', user)
}


</script>
