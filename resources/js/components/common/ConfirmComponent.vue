<template>
    <div class="modal-overlay" @click.self="onCancel">
        <div class="confirm-modal">
            <h3 class="confirm-title">{{ title }}</h3>
            <p class="confirm-message">{{ message }}</p>
            <div class="form-actions">
                <button class="btn-cancel" @click="onCancel">戻る</button>
                <button class="btn-delete" @click="onConfirm">削除</button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
const props = defineProps<{
    title: string
    message: string
}>()

const emit = defineEmits<{
    (e: 'confirm'): void
    (e: 'cancel'): void
}>()

const onConfirm = () => emit('confirm')
const onCancel = () => emit('cancel')
</script>

<style scoped></style>
