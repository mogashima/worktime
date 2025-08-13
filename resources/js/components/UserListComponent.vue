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
                <tr v-for="user in users" :key="user.id" class="user-row" @mouseover="hoverRow = user.id"
                    @mouseleave="hoverRow = null" :class="{ 'hovered': hoverRow === user.id }">
                    <td>{{ user.id }}</td>
                    <td>{{ user.login_id }}</td>
                    <td>{{ user.name }}</td>
                    <td class="text-center">
                        <button class="btn-edit" @click="$emit('select-user', user)">
                            勤怠情報
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from '@/plugins/axios'

const users = ref([])
const hoverRow = ref(null)

const fetchUsers = async () => {
    try {
        const response = await axios.get('/api/admin/user')
        users.value = response.data
    } catch (error) {
        console.error('ユーザー取得エラー', error)
    }
}

fetchUsers()
</script>
