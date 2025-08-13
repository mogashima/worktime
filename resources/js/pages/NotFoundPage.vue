<template>
    <div class="not-found">
        <h1>404 - ページが見つかりません</h1>
        <p>お探しのページは存在しないか、移動しました。</p>
        <router-link to="/login">ログインページへ戻る</router-link>
    </div>
</template>

<script lang="ts" setup>
</script>

<style scoped>
.not-found {
    text-align: center;
    margin-top: 5rem;
}

h1 {
    font-size: 2rem;
    color: #e74c3c;
    margin-bottom: 1rem;
}

p {
    margin-bottom: 2rem;
}

a {
    color: #3498db;
    text-decoration: underline;
    cursor: pointer;
}
</style>
