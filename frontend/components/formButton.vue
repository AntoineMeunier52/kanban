<template>
  <button :type="props.type" :class="handleStyle">{{ props.label }}</button>
</template>

<script setup lang="ts">
const props = defineProps({
  label: {
    type: String,
    required: true,
  },
  type: {
    type: String as () => "submit" | "button" | "reset",
    default: "submit",
  },
  variant: {
    type: String as () => "primary" | "secondary",
    default: "primary",
  },
});

const handleStyle = computed(() => `form-button-${props.variant}`);
</script>

<style scoped>
@reference "../assets/css/main.css";

.form-button-primary {
  @apply w-full h-[42px] p-[8px] bg-black text-white font-bold overflow-hidden rounded mt-[28px];
}

.form-button-primary:hover {
  @apply bg-gradient-to-r from-black via-[#ffffff4d] to-black scale-[102%];
}

.form-button-secondary {
  @apply w-full h-[42px] p-[8px] bg-primary-creme border border-border-primary font-bold overflow-hidden rounded mt-[16px];
}

.form-button-secondary:hover {
  @apply bg-solid-creme border-0;
}
</style>
