<template>
  <div>
    <p>{{ props.label }}</p>
    <input
      class="input-text"
      :type="props.type"
      :placeholder="props.placeholder"
      @input="handleInput"
    />
    <p v-if="props?.error">{{ props.error }}</p>
  </div>
</template>

<script setup lang="ts">
const props = defineProps({
  modelValue: {
    type: String,
    required: true,
  },
  placeholder: {
    type: String,
    default: "Placeholder",
  },
  label: {
    type: String,
    default: "Label",
  },
  type: {
    type: String,
    default: "text",
  },
  error: {
    type: String,
  },
});

const emit = defineEmits(["update:modelValue"]);

const handleInput = (event: Event) => {
  emit("update:modelValue", (event.target as HTMLInputElement).value);
};
</script>

<style scoped>
@reference "../assets/css/main.css";

.input-text {
  @apply bg-white border border-border-primary rounded w-full p-[8px] mt-[8px] mb-[8px];
}

.input-text:hover {
  @apply border-border-solid;
}

.input-text:focus {
  @apply border-border-solid;
}
</style>
