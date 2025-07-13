<template>
  <div class="flex justify-center dark:bg-[#171717] pt-[15vh]">
    <div class="auth-wrapper">
      <h1 class="auth-title font-thin mb-[16px]">KANBAN</h1>
      <UTabs
        :items="items"
        :ui="{
          list: 'bg-elevated rounded-lg p-1 shadow-sm tabs-container',
        }"
        class="gap-4 w-full"
        color="neutral"
      >
        <template #signin>
          <AuthSignin />
        </template>

        <template #signup>
          <AuthSignup />
        </template>
      </UTabs>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { TabsItem } from "@nuxt/ui";

const items = [
  {
    label: "Sign in",
    slot: "signin" as const,
  },
  {
    label: "Sign up",
    slot: "signup" as const,
  },
] satisfies TabsItem[];
</script>

<style scoped>
.auth-wrapper {
  @apply w-[400px];
}

.auth-title {
  @apply text-[64px];
}

/* Animation underline pour les tabs */
:deep(.tabs-container button:not([aria-selected="true"])) {
  position: relative;
}

:deep(.tabs-container button:not([aria-selected="true"])::after) {
  content: "";
  position: absolute;
  bottom: 4px;
  left: 50%;
  width: 0;
  height: 1px;
  background: #262626;
  transition: width 0.3s ease;
  transform: translateX(-50%);
}

:deep(.tabs-container button:not([aria-selected="true"]):hover::after) {
  width: 80%;
}
</style>

<style>
html.dark .tabs-container button:not([aria-selected="true"])::after {
  background: #e5e5e5 !important;
}
</style>
