<template>
    <div class="user-form">
        <h3>{{ isEdit ? 'ユーザー編集' : 'ユーザー追加' }}</h3>

        <!-- エラー表示 -->
        <FormMessageComponent :apiError="apiError" />

        <form @submit.prevent="submitForm" class="form-container">
            <div class="form-group">
                <label>名前:
                    <input type="text" v-model="form.name" required />
                </label>
            </div>

            <div class="form-group">
                <label>ログインID:
                    <input type="text" v-model="form.login_id" required />
                </label>
            </div>

            <div class="form-group">
                <label>パスワード:
                    <input type="password" v-model="form.password" :required="!isEdit" placeholder="編集時は空欄で変更なし" />
                </label>
            </div>

            <div class="form-group">
                <label>役割:
                    <select v-model="form.role_code" required>
                        <option value="" disabled>選択してください</option>
                        <option value="admin">管理者</option>
                        <option value="user">一般ユーザー</option>
                    </select>
                </label>
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
import { reactive, watch, computed, ref } from 'vue'
import axios from '@/plugins/axios'
import { useUserStore } from '@/stores/userStore'
import type { User } from '@/types/userType'
import { ApiError } from '@/types/apiErrorType'
import FormMessageComponent from '@/components/common/FormMessageComponent.vue'

const props = defineProps<{
    user: User | null
}>()

const emit = defineEmits(['user-store', 'user-update', 'edit-cancel'])

const userStore = useUserStore()
const isEdit = computed(() => !!props.user)
const apiError = ref<ApiError | null>(null)

const emptyForm = {
    id: 0,
    name: '',
    login_id: '',
    password: '',
    role_code: 'user',
}

const form = reactive({ ...emptyForm })

watch(() => props.user, (newVal) => {
    if (newVal) {
        form.id = newVal.id
        form.name = newVal.name
        form.login_id = newVal.login_id
        form.password = ''
        form.role_code = newVal.role_code
    } else {
        Object.assign(form, emptyForm)
    }
}, { immediate: true })

const submitForm = async () => {
    try {
        if (isEdit.value && props.user) {
            await axios.put(`/api/user/${props.user.id}`, form)
            emit('user-store')
        } else {
            await axios.post(`/api/user`, form)
            emit('user-update')
        }

    } catch (error: any) {
        apiError.value = new ApiError(error)
    }
}

const cancelEdit = () => {
    emit('edit-cancel')
}
</script>
