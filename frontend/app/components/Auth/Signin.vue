<template>
  <UForm :schema="schema" :state="state" class="space-y-4" @submit="onSubmit">
    <!-- Error display -->
    <UAlert
      v-if="authStore.error"
      icon="i-heroicons-exclamation-triangle"
      color="error"
      variant="soft"
      :title="authStore.error"
      class="mb-4 animate-slide-down"
      :close-button="{
        icon: 'i-heroicons-x-mark-20-solid',
        color: 'gray',
        variant: 'link',
        padded: false,
      }"
      @close="authStore.clearError()"
    />

    <UFormField
      label="Email"
      name="email"
      color="neutral"
      class="w-full p-[4px]"
    >
      <UInput
        v-model="state.email"
        class="w-full transition-all duration-200 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
        :disabled="authStore.isLoading"
        placeholder="Enter your email"
      />
    </UFormField>

    <UFormField
      label="Password"
      name="password"
      color="neutral"
      class="w-full p-[4px]"
    >
      <UInput
        v-model="state.password"
        type="password"
        class="w-full mb-[8px] transition-all duration-200 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
        :disabled="authStore.isLoading"
        placeholder="Enter your password"
      />
    </UFormField>

    <div class="flex justify-start">
      <NuxtLink
        to="/auth/forgot-password"
        class="text-sm text-gray-600 hover:text-blue-500 underline transition-colors duration-200 text-[14px] pl-[4px] dark:text-gray-200"
      >
        Forgot Password?
      </NuxtLink>
    </div>

    <UButton
      type="submit"
      color="neutral"
      class="w-full justify-center submit-button relative overflow-hidden"
      :disabled="authStore.isLoading || successAnimation"
    >
      <span
        v-if="!authStore.isLoading && !successAnimation"
        class="flex items-center gap-2"
      >
        <Icon name="i-heroicons-arrow-right-on-rectangle" class="w-4 h-4" />
        Sign In
      </span>
      <span v-else-if="authStore.isLoading" class="flex items-center gap-2">
        <Icon name="i-heroicons-arrow-path" class="w-4 h-4 animate-spin" />
        Signing in...
      </span>
      <span v-else class="flex items-center gap-2">
        <Icon name="i-heroicons-check" class="w-4 h-4" />
        Success!
      </span>
    </UButton>
  </UForm>
</template>

<script setup lang="ts">
import * as v from "valibot";
import type { FormSubmitEvent } from "@nuxt/ui";
import { useAuthStore } from "~/store/auth";
const authStore = useAuthStore();
const router = useRouter();
const toast = useToast();

const schema = v.object({
  email: v.pipe(v.string(), v.email("Please enter a valid email address")),
  password: v.pipe(v.string(), v.minLength(1, "Password is required")),
});

type Schema = v.InferOutput<typeof schema>;

const state = reactive({
  email: "",
  password: "",
});

const successAnimation = ref(false);

async function onSubmit(event: FormSubmitEvent<Schema>) {
  authStore.clearError();

  const result = await authStore.login({
    email: event.data.email,
    password: event.data.password,
  });

  if (result.success) {
    successAnimation.value = true;

    toast.add({
      title: "Login successful!",
      description: "Welcome to your dashboard",
      icon: "i-heroicons-check-circle",
      color: "success",
    });

    setTimeout(() => {
      router.push("/");
    }, 800);
  }
}

watch([() => state.email, () => state.password], () => {
  if (authStore.error) {
    authStore.clearError();
  }
});

onBeforeUnmount(() => {
  authStore.clearError();
});
</script>

<style scoped>
/* Loading animation with pulse */
.submit-button :deep(button:disabled) {
  opacity: 0.7 !important;
  cursor: not-allowed !important;
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0%,
  100% {
    opacity: 0.7;
  }
  50% {
    opacity: 0.9;
  }
}

/* Ripple effect on click */
.submit-button :deep(button:not(:disabled))::before {
  content: "";
  position: absolute;
  top: 50%;
  left: 50%;
  width: 0;
  height: 0;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.3);
  transform: translate(-50%, -50%);
  transition: width 0.6s, height 0.6s;
}

.submit-button :deep(button:active:not(:disabled))::before {
  width: 300px;
  height: 300px;
}

/* Dark mode */
.dark .submit-button :deep(button:hover:not(:disabled)) {
  background: #262626 !important;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2),
    inset 0 1px 2px rgba(255, 255, 255, 0.05) !important;
}

.dark .submit-button :deep(button:active:not(:disabled)) {
  background: #1f1f1f !important;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3), inset 0 2px 8px rgba(0, 0, 0, 0.2) !important;
}

/* Success animation */
@keyframes success-scale {
  0% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.05);
  }
  100% {
    transform: scale(1);
  }
}

/* Input focus animations */
.transition-all {
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Smooth alert animation */
@keyframes slide-down {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-slide-down {
  animation: slide-down 0.3s ease-out;
}

/* Icon rotation for loading */
@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}
</style>
