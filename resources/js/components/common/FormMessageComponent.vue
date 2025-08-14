<template>
    <div class="error-message" v-if="apiError">
        <ul v-if="apiError.errors">
            <li v-for="(messages, field) in apiError.errors" :key="field">
                <span v-for="(msg, index) in messages" :key="index">{{ msg }}</span>
            </li>
        </ul>
    </div>
</template>

<script setup lang="ts">
import { ApiError } from '@/types/apiErrorType';
defineProps<{
    apiError: ApiError | null
}>()
</script>