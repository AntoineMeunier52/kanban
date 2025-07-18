<template>
  <div class="auth-wrapper">
    <div class="auth-container">
      <p class="auth-forgot">Verify your Email</p>
      <p class="auth-call-to-action">
        Verify your email now by checking your inbox!<br />
        We've sent you a code.
      </p>
      <div class="verify-otp" ref="otpContainer">
        <input
          v-for="n in length"
          :key="n"
          v-model="otpArray[n - 1]"
          type="text"
          maxlength="1"
          class="verify-otp-input"
        />
      </div>
      <FormButton label="Verify Email" />
    </div>
  </div>
</template>

<script setup lang="ts">
const otpArray = ref([]);
const length = 6;
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
</style>
