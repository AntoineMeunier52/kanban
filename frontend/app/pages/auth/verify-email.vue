<template>
  <div class="relative flex justify-center dark:bg-[#171717] pt-[15vh]">
    <div class="auth-wrapper">
      <p class="text-[32px] font-medium">Verification code</p>
      <p class="pb-[42px]">Enter the 6 digits code</p>

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
      <UPinInput
        v-model="pin"
        type="number"
        :length="6"
        color="neutral"
        highlight
        placeholder="-"
        size="xl"
        :ui="{ root: 'w-full' }"
      />
      <p class="text-[14px] pt-[14px]">
        Haven't got email?
        <a class="text-[14px] underline hover:text-blue-500"
          >Click here to resend</a
        >
      </p>
      <UButton
        type="submit"
        color="neutral"
        class="w-full flex justify-center mt-[42px]"
        s
        :disabled="authStore.isLoading"
        @click="onSubmit"
      >
        <span v-if="!authStore.isLoading" class="flex items-center gap-2">
          <Icon name="i-heroicons-arrow-right-on-rectangle" class="w-4 h-4" />
          Verify email
        </span>
        <span v-else class="flex items-center gap-2">
          <Icon name="i-heroicons-arrow-path" class="w-4 h-4 animate-spin" />
          Verifying email...
        </span>
      </UButton>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useAuthStore } from "~/store/auth";

const authStore = useAuthStore();
const router = useRouter();
const toast = useToast();
const pin = ref([]);

async function onSubmit() {
  authStore.clearError();
  console.log("submit submit");

  const result = await authStore.verifyEmail({
    email: authStore.registrering,
    code: pin.value.join(""),
  });

  if (result.success) {
    toast.add({
      title: "Verify email successful",
      description: "Welcome to your dashboard",
      icon: "i-heroicons-check-circle",
      color: "success",
    });

    setTimeout(() => {
      router.push("/");
    }, 800);
  }
}

watch([() => pin.value], () => {
  if (authStore.error) {
    authStore.clearError();
  }
});

onBeforeUnmount(() => {
  authStore.clearError();
});
</script>

<style scoped>
.auth-wrapper {
  @apply w-[300px];
}

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
</style>
