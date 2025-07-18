<template>
  <div class="auth-wrapper">
    <div class="auth-container">
      <p class="auth-forgot">Forgot Password?</p>
      <p class="auth-call-to-action">
        Reset your password now by entering your email!<br />
        We'll send you a reset link.
      </p>
      <TextInput v-model="email" label="Email" placeholder="Enter your email" />
      <a href="/auth" class="forgot-back">Back to Sign In</a>
      <FormButton label="Send reset link" />
    </div>
  </div>
</template>

<script setup lang="ts">
const email = ref("");
</script>

<style scoped>
@reference "../../assets/css/main.css";

.auth-wrapper {
  @apply flex justify-center w-full h-full;
}

.auth-container {
  @apply w-[400px] mt-[15vh];
}

.auth-forgot {
  @apply text-[32px] font-stardom mb-[8px];
}

.auth-call-to-action {
  @apply mb-[44px];
}

.forgot-back {
  @apply text-[14px] text-text-secondary underline;
}

.forgot-back:hover {
  @apply text-accent-primary;
}
</style>
