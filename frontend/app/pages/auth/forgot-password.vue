<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
          Reset your password
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
          Enter your email address and we'll send you a link to reset your password.
        </p>
      </div>

      <UCard class="p-8">
        <!-- Success message -->
        <UAlert
          v-if="emailSent"
          icon="i-heroicons-check-circle"
          color="success"
          variant="soft"
          title="Email sent!"
          description="Check your inbox for password reset instructions."
          class="mb-6"
        />

        <!-- Error message -->
        <UAlert
          v-if="error"
          icon="i-heroicons-exclamation-triangle"
          color="error"
          variant="soft"
          :title="error"
          class="mb-6 animate-slide-down"
          :close-button="{ icon: 'i-heroicons-x-mark-20-solid', color: 'gray', variant: 'link', padded: false }"
          @close="clearError"
        />

        <UForm v-if="!emailSent" :schema="schema" :state="state" class="space-y-6" @submit="onSubmit">
          <UFormField
            label="Email address"
            name="email"
            color="neutral"
            class="w-full"
          >
            <UInput
              v-model="state.email"
              type="email"
              class="w-full transition-all duration-200 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
              :disabled="isLoading"
              placeholder="Enter your email address"
            />
          </UFormField>

          <UButton
            type="submit"
            color="neutral"
            class="w-full justify-center submit-button relative overflow-hidden"
            :loading="isLoading"
            :disabled="isLoading"
          >
            <span v-if="!isLoading" class="flex items-center gap-2">
              <Icon name="i-heroicons-paper-airplane" class="w-4 h-4" />
              Send Reset Link
            </span>
            <span v-else class="flex items-center gap-2">
              <Icon name="i-heroicons-arrow-path" class="w-4 h-4 animate-spin" />
              Sending...
            </span>
          </UButton>
        </UForm>

        <!-- Back to login -->
        <div class="mt-6 text-center">
          <NuxtLink
            to="/auth/authentification"
            class="text-sm text-gray-600 hover:text-blue-500 underline transition-colors duration-200"
          >
            Back to Sign In
          </NuxtLink>
        </div>
      </UCard>
    </div>
  </div>
</template>

<script setup lang="ts">
import * as v from "valibot";
import type { FormSubmitEvent } from "@nuxt/ui";

const toast = useToast();

const schema = v.object({
  email: v.pipe(v.string(), v.email("Please enter a valid email address")),
});

type Schema = v.InferOutput<typeof schema>;

const state = reactive({
  email: "",
});

const isLoading = ref(false);
const error = ref<string | null>(null);
const emailSent = ref(false);

const { baseURL } = useApi();

async function onSubmit(event: FormSubmitEvent<Schema>) {
  isLoading.value = true;
  error.value = null;

  try {
    await $fetch(`${baseURL}/api/auth/forgot-password`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        email: event.data.email,
      }),
      server: false,
    });

    emailSent.value = true;
    toast.add({
      title: "Email sent!",
      description: "Check your inbox for password reset instructions.",
      icon: "i-heroicons-check-circle",
      color: "success",
    });
  } catch (err: unknown) {
    const errorMessage = err && typeof err === 'object' && 'data' in err && err.data && typeof err.data === 'object' && 'message' in err.data 
      ? (err.data as { message: string }).message 
      : "An error occurred while sending the reset link";
    error.value = errorMessage;
  } finally {
    isLoading.value = false;
  }
}

function clearError() {
  error.value = null;
}

// Clear errors when user starts typing
watch(() => state.email, () => {
  if (error.value) {
    clearError();
  }
});

// SEO
useHead({
  title: "Forgot Password - Reset Your Password",
  meta: [
    { name: "description", content: "Reset your password by entering your email address. We'll send you a secure link to create a new password." }
  ],
});
</script>

<style scoped>
/* Button animations */
.submit-button :deep(button) {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
  position: relative;
  overflow: hidden;
  transform: translateY(0);
}

.submit-button :deep(button:hover:not(:disabled)) {
  background: #faf9f5 !important;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05), inset 0 1px 2px rgba(255, 255, 255, 0.1) !important;
  transform: translateY(-1px);
}

.submit-button :deep(button:active:not(:disabled)) {
  background: #f5f4f0 !important;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1), inset 0 2px 8px rgba(0, 0, 0, 0.08) !important;
  transform: translateY(0);
}

.submit-button :deep(button:disabled) {
  opacity: 0.7 !important;
  cursor: not-allowed !important;
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0%, 100% {
    opacity: 0.7;
  }
  50% {
    opacity: 0.9;
  }
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
.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

/* Dark mode */
.dark .submit-button :deep(button:hover:not(:disabled)) {
  background: #262626 !important;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2), inset 0 1px 2px rgba(255, 255, 255, 0.05) !important;
}

.dark .submit-button :deep(button:active:not(:disabled)) {
  background: #1f1f1f !important;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3), inset 0 2px 8px rgba(0, 0, 0, 0.2) !important;
}
</style>