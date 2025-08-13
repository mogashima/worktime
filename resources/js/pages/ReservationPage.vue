<template>
  <div>
    <h1>予約管理ページ</h1>
    <p>ここに予約一覧や予約フォームを実装します。</p>

    <button @click="logout">ログアウト</button>
  </div>
</template>

<script lang="ts" setup>
import { useRouter } from 'vue-router'

const router = useRouter()

// ログアウト処理例（トークン削除してログイン画面へ遷移）
const logout = () => {
  localStorage.removeItem('auth_token')
  router.push('/login')
}
</script>

<style scoped>
h1 {
  color: #2c3e50;
  margin-bottom: 1rem;
}
button {
  background-color: #e74c3c;
  color: white;
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}
button:hover {
  background-color: #c0392b;
}
</style>
