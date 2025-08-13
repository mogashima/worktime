<template>
    <div class="auth-container">
        <h2>ログイン</h2>
        <form @submit.prevent="login" class="auth-form">
            <div class="form-group">
                <label for="loginId">ログインID</label>
                <input id="loginId" type="text" v-model="loginId" required autocomplete="username" />
            </div>

            <div class="form-group">
                <label for="password">パスワード</label>
                <input id="password" type="password" v-model="password" required autocomplete="current-password" />
            </div>

            <button type="submit" :disabled="loading">
                {{ loading ? 'ログイン中...' : 'ログイン' }}
            </button>

            <p v-if="errorMessage" class="error-message">{{ errorMessage }}</p>
        </form>

        <p class="switch-link">
            アカウントをお持ちでない方は
            <router-link to="/register">こちら</router-link>から登録
        </p>
    </div>
</template>

<script lang="ts" setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useUserStore } from '@/stores/userStore'

import axios from '@/plugins/axios'

const router = useRouter()
const userStore = useUserStore()
const loginId = ref('')
const password = ref('')
const errorMessage = ref('')
const loading = ref(false)

const login = async () => {
    errorMessage.value = ''
    loading.value = true

    try {
        const response = await axios.post('/api/login', {
            login_id: loginId.value,
            password: password.value,
        })

        if (response.status === 200) {
            localStorage.setItem('auth_token', response.data.token)
            userStore.setUser(response.data.user)
            router.push('/dashboard')
        } else {
            errorMessage.value = 'ログインに失敗しました。'
        }
    } catch {
        errorMessage.value = 'ログインIDかパスワードが正しくありません。'
    } finally {
        loading.value = false
    }
}
</script>
