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
          @keyup="(e) => handleEnter(e, n - 1)"
          v-model="otpArray[n - 1]"
          type="text"
          maxlength="1"
          placeholder="◦"
          class="verify-otp-input"
        />
      </div>
      <p v-if="error" class="verify-error">{{ error }}</p>
      <button class="form-button-primary" @click="handleCheck">
        Verify Email
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
const otpContainer = ref();
const otpArray = ref<string[]>([]);
const length = 6;

const error = ref("");

function handleEnter(e: KeyboardEvent, i: number) {
  const children = otpContainer.value.children;
  const keypressed = e.key;

  clearBorderError();

  if (i > 0 && (keypressed === "Backspace" || keypressed === "Delete")) {
    otpArray.value[i] = "";
    setTimeout(() => {
      children[i - 1].focus();
    }, 50);
  } else {
    const matched = keypressed.match(/^[0-9]$/);
    if (!matched) {
      otpArray.value[i] = "";
      return;
    } else if (i < length - 1) {
      setTimeout(() => {
        children[i + 1].focus();
      }, 50);
    }
  }

  error.value = "";
}

function handleCheck() {
  const children: HTMLInputElement[] = otpContainer.value.children;
  let errorCheck = false;

  for (let i = 0; i < length; i++) {
    if (otpArray.value[i] === undefined || otpArray.value[i] === "") {
      console.log("add border");
      children[i].classList.add("!border-red-500");
      errorCheck = true;
    }
  }
  if (errorCheck) {
    error.value = "Enter all the digits";
    return;
  }
}

function clearBorderError() {
  const children = otpContainer.value.children;
  for (let i = 0; i < length; i++) {
    children[i].classList.remove("!border-red-500");
  }
}
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

.verify-otp {
  @apply flex justify-between p-[8px];
}

.verify-otp-input {
  @apply bg-white size-[42px] rounded border border-border-primary text-center;
}

.verify-otp-input:hover {
  @apply border-border-solid;
}

.verify-otp-input:focus {
  @apply border-border-solid;
}

.verify-error {
  @apply text-red-500 text-[14px] pl-[8px];
}
</style>
