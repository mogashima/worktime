<template>
    <div class="auth-container">
        <h2>ユーザー登録</h2>
        <form @submit.prevent="register" class="auth-form">
            <div class="form-group">
                <label for="name">名前</label>
                <input id="name" type="text" v-model="name" required autocomplete="name" />
            </div>

            <div class="form-group">
                <label for="loginId">ログインID</label>
                <input id="loginId" type="text" v-model="loginId" required autocomplete="username" />
            </div>

            <div class="form-group">
                <label for="role">役割</label>
                <select id="role" v-model="roleCode" required>
                    <option value="" disabled>役割を選択してください</option>
                    <option v-for="role in roles" :key="role.role_code" :value="role.role_code">
                        {{ role.name }}
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label for="password">パスワード</label>
                <input id="password" type="password" v-model="password" required autocomplete="new-password" />
            </div>

            <div class="form-group">
                <label for="passwordConfirm">パスワード確認</label>
                <input id="passwordConfirm" type="password" v-model="passwordConfirm" required
                    autocomplete="new-password" />
            </div>

            <button type="submit" :disabled="loading">
                {{ loading ? '登録中...' : '登録' }}
            </button>

            <p v-if="errorMessage" class="error-message">{{ errorMessage }}</p>
            <p v-if="successMessage" class="success-message">{{ successMessage }}</p>
        </form>

        <p class="switch-link">
            すでにアカウントをお持ちの方は
            <router-link to="/login">こちら</router-link>からログイン
        </p>
    </div>
</template>

<script lang="ts" setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import type { Role } from '@/types/roleType';
import axios from '@/plugins/axios'

const router = useRouter()
const name = ref('')
const loginId = ref('')
const roleCode = ref('')
const password = ref('')
const passwordConfirm = ref('')
const errorMessage = ref('')
const successMessage = ref('')
const loading = ref(false)
const roles = ref<Role[]>([])

onMounted(async () => {
    try {
        const res = await axios.get('/api/role')
        roles.value = res.data
    } catch (error) {
        console.error('役割取得エラー:', error)
    }
})

const register = async () => {
    errorMessage.value = ''
    successMessage.value = ''

    if (password.value !== passwordConfirm.value) {
        errorMessage.value = 'パスワードが一致しません。'
        return
    }

    if (!roleCode.value) {
        errorMessage.value = '役割を選択してください。'
        return
    }

    loading.value = true

    try {
        const response = await axios.post('/api/register', {
            name: name.value,
            login_id: loginId.value,
            role_code: roleCode.value,
            password: password.value,
            password_confirmation: passwordConfirm.value,
        })

        if (response.status === 201) {
            successMessage.value = '登録が完了しました。ログインページへ移動します。'
            setTimeout(() => {
                router.push('/login')
            }, 2000)
        } else {
            errorMessage.value = '登録に失敗しました。'
        }
    } catch (error: any) {
        errorMessage.value = error.response?.data?.message || '登録時にエラーが発生しました。'
    } finally {
        loading.value = false
    }
}
</script>
