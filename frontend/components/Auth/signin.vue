<template>
  <div>
    <form @submit.prevent="handleSignIn">
      <p>Email</p>
      <input
        v-model="email"
        placeholder="Enter your email"
        class="input-text mb-[8px]"
        :class="emailError ? 'border !border-red-500' : ''"
      />
      <p class="signin-error">{{ emailError }}</p>
      <p>Password</p>
      <input
        v-model="password"
        placeholder="Enter your password"
        class="input-text mb-[8px]"
        :class="passwordError ? 'border !border-red-500' : ''"
      />
      <p class="signin-error-p">{{ passwordError }}</p>
      <a class="sign-in-forgot" href="/auth/forgot-password"
        >Forgot your password?</a
      >
      <button type="submit" class="form-button-primary">Sign In</button>
    </form>
  </div>
</template>

<script setup lang="ts">
const email = ref("");
const password = ref("");

const emailError = ref<string | null>(null);
const passwordError = ref<string | null>(null);

function handleSignIn() {
  emailError.value = validator("email", email.value, "Email");
  passwordError.value = validator("password", password.value, "Password", 8);
}
</script>

<style scoped>
@reference "../../assets/css/main.css";

.sign-in-forgot {
  @apply text-[14px] text-text-secondary underline;
}

.sign-in-forgot:hover {
  @apply text-accent-primary;
}

.signin-error {
  @apply text-[14px] text-red-500 mb-[8px];
}

.signin-error-p {
  @apply text-[14px] text-red-500;
}
</style>
