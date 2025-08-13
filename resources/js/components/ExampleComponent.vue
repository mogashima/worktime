<template>
  <div>
    <h1>{{ message }}</h1>
  </div>
</template>

<script lang="ts" setup>
import { ref } from 'vue';

const message = ref<string>('こんにちは、Laravel + Vite + Vue 3 + TypeScript!');
</script>

<style lang="scss" scoped>
h1 {
  color: #42b983;
}
</style>
