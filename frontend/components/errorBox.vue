<template>
  <div v-if="props.error" class="error-box-container">
    <Icon name="i-lucide-triangle-alert" class="error-box-icon" />
    <p>{{ props.error }}</p>
  </div>
</template>

<script setup lang="ts">
const props = defineProps({
  error: {
    type: String,
  },
});
</script>

<style scoped>
@reference "../assets/css/main.css";

.error-box-container {
  @apply flex items-center w-full p-[8px] bg-red-300 border border-red-500 rounded mb-[16px];
}

.error-box-icon {
  @apply px-[16px];
}
</style>
