<template>
  <div class="auth-wrapper">
    <div class="auth-container">
      <p class="auth-forgot">Verify your Email</p>
      <p class="auth-call-to-action">
        Verify your email now by checking your inbox!<br />
        We've sent you a code.
      </p>
      <ErrorBox :error="auth.error" />
      <div class="verify-otp" ref="otpContainer">
        <input
          v-for="n in length"
          :key="n"
          @keydown="(e) => handleKeydown(e, n - 1)"
          @input="(e) => handleInput(e, n - 1)"
          @paste="(e) => onPaste(e, n - 1)"
          v-model="otpArray[n - 1]"
          type="text"
          maxlength="1"
          placeholder="◦"
          class="verify-otp-input"
        />
      </div>
      <p v-if="otpError" class="verify-error">{{ otpError }}</p>
      <a class="verify-dont" @click="resendCode"
        >Don't received the code?
        <span class="verify-dont-link">Resend it!</span></a
      >
      <button class="form-button-primary" @click="handleCheck">
        Verify Email
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useAuthStore } from "~/store/auth";

const otpContainer = ref();
const otpArray = ref<string[]>([]);
const length = 6;

const otpError = ref("");

const route = useRoute();

const email = computed(() => {
  const value = route.query.email;
  if (Array.isArray(value)) return value[0] || null;
  return value || null;
});

const router = useRouter();

const auth = useAuthStore();

function handleKeydown(e: KeyboardEvent, i: number) {
  const children = otpContainer.value.children;
  const keypressed = e.key;

  clearBorderError();

  // Gestion Ctrl+V / Cmd+V
  if ((e.ctrlKey || e.metaKey) && keypressed === "v") {
    // Laisser l'événement paste se déclencher naturellement
    return;
  }

  //delete number
  if (keypressed === "Backspace" || keypressed === "Delete") {
    if (otpArray.value[i] === "" && i > 0) {
      e.preventDefault();
      otpArray.value[i - 1] = "";
      nextTick(() => {
        children[i - 1].focus();
      });
    }
    //navigate into the input
  } else if (keypressed === "ArrowLeft" && i > 0) {
    e.preventDefault();
    nextTick(() => {
      children[i - 1].focus();
    });
  } else if (keypressed === "ArrowRight" && i < length - 1) {
    e.preventDefault();
    nextTick(() => {
      children[i + 1].focus();
    });
    //get only number
  } else if (keypressed.match(/^[0-9]$/)) {
    e.preventDefault();
    otpArray.value[i] = keypressed;
    if (i < length - 1) {
      nextTick(() => {
        children[i + 1].focus();
      });
    }
  } else if (!["Tab", "Shift", "Control", "Meta", "Alt"].includes(keypressed)) {
    e.preventDefault();
  }

  otpError.value = "";
}

function handleInput(e: Event, i: number) {
  const target = e.target as HTMLInputElement;
  const value = target.value;

  if (!value.match(/^[0-9]$/)) {
    otpArray.value[i] = "";
    return;
  }

  if (value && i < length - 1) {
    const children = otpContainer.value.children;
    nextTick(() => {
      children[i + 1].focus();
    });
  }
}

async function handleCheck() {
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
    otpError.value = "Enter all the digits";
    return;
  }

  const data = await auth.verifyEmail({
    email: email.value,
    code: otpArray.value.join(""),
  });

  if (data.success) {
    setTimeout(() => {
      router.push("/");
    }, 800);
  }
}

function onPaste(e: ClipboardEvent, startIndex: number) {
  e.preventDefault();

  const pasteData = e.clipboardData?.getData("text") || "";
  const numbers = pasteData.replace(/\D/g, "");

  if (numbers.length === 0) return;

  const children = otpContainer.value.children;
  let currentIndex = startIndex;

  for (let i = 0; i < numbers.length && currentIndex < length; i++) {
    otpArray.value[currentIndex] = numbers[i];
    currentIndex++;
  }

  nextTick(() => {
    if (currentIndex < length) {
      children[currentIndex].focus();
    } else {
      children[length - 1].focus();
    }
  });

  otpError.value = "";
}

function clearBorderError() {
  const children = otpContainer.value.children;
  for (let i = 0; i < length; i++) {
    children[i].classList.remove("!border-red-500");
  }
}

function resendCode() {
  console.log("resend");
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

.verify-dont {
  @apply text-[14px] text-text-secondary ml-[8px];
}

.verify-dont-link {
  @apply underline;
}

.verify-dont-link:hover {
  @apply text-accent-primary;
}
</style>
