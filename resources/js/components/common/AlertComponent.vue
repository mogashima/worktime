<template>
    <div v-if="alert" :class="['alert', 'alert-' + alert.type]">
        {{ alert.message }}
    </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import type { Alert } from '@/types/alertType'

const alert = ref<Alert | null>(null)

const props = defineProps<{
    alertData: Alert | null
}>()

watch(() => props.alertData, (newVal) => {
    alert.value = newVal
    if (newVal?.duration) {
        setTimeout(() => alert.value = null, newVal.duration)
    }
})
</script>