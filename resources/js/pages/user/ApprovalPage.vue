<template>
    <NavigationComponent />
    <div class="approval-page page">
        <h2>承認履歴</h2>
        <ApprovalAttendanceListComponent />
    </div>
</template>

<script setup>
import NavigationComponent from '@/components/NavigationComponent.vue'
import ApprovalAttendanceListComponent from '@/components/approval/ApprovalAttendanceListComponent.vue'

</script>