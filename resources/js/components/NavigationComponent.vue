<template>
    <nav class="navbar">
        <div class="navbar-left">
            <span class="logo"><router-link to="/dashboard">勤怠管理システム</router-link></span>
        </div>
        <div class="nav-contents">
            <!-- 管理者のみ表示 -->
            <router-link v-if="userStore.isAdmin()" to="/admin/user" class="nav-item">社員管理</router-link>
            <router-link v-if="userStore.isAdmin()" to="/admin/approval" class="nav-item">承認管理</router-link>
            <!-- 一般ユーザーのみ表示 -->
            <router-link v-if="userStore.isUser()" to="/attendance" class="nav-item">勤怠入力</router-link>
            <router-link v-if="userStore.isUser()" to="/expense" class="nav-item">経費入力</router-link>
            <router-link v-if="userStore.isUser()" to="/approval" class="nav-item">申請履歴</router-link>
        </div>
        <div class="nav-user">
            <div class="dropdown" @mouseenter="open = true" @mouseleave="open = false">
                <button class="dropdown-toggle">
                    {{ userName }} ▼
                </button>
                <ul v-if="open" class="dropdown-menu">
                    <li><a href="#" @click.prevent="logout">ログアウト</a></li>
                </ul>
            </div>
        </div>
    </nav>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useUserStore } from '@/stores/userStore'
import { useRouter } from 'vue-router'

const open = ref(false)
const userStore = useUserStore()
const router = useRouter()

const userName = computed(() => userStore.user?.name || 'ユーザー名')

const logout = () => {
    userStore.logout()
    localStorage.removeItem('auth_token')
    router.push('/login')
}
</script>
