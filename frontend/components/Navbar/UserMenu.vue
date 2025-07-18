<template>
  <div>
    <button
      aria-label="Color Mode"
      class="inline-block text-sm h-[32px] w-[32px] leading-none border border-border-primary rounded mt-4 lg:mt-0 hover:bg-solid2-creme hover:border-0"
      @click="changeColor"
    >
      <Icon
        :name="
          colorMode.preference === 'dark' ? 'i-lucide-moon' : 'i-lucide-sun'
        "
        class="text-xl text-text-primary"
      />
    </button>
  </div>
</template>

<script setup lang="ts">
import { useAuthStore } from "~/composables/authstore";

const authStore = useAuthStore();
const colorMode = useColorMode();
const changeColor = () =>
  (colorMode.preference = colorMode.value === "light" ? "dark" : "light");
</script>

<style scoped></style>
