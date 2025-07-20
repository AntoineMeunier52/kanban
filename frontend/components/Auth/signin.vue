<template>
  <div class="sign-in-container">
    <form @submit.prevent="handleSignIn">
      <ErrorBox :error="auth.error" />
      <label for="email">Email</label>
      <input
        v-model="email"
        type="text"
        placeholder="Enter your email"
        name="email"
        class="input-text mb-[8px]"
        :class="emailError ? 'border !border-red-500' : ''"
      />
      <p class="signin-error">{{ emailError }}</p>
      <label for="password">Password</label>
      <input
        v-model="password"
        type="password"
        placeholder="Enter your password"
        name="password"
        class="input-text mb-[8px]"
        :class="passwordError ? 'border !border-red-500' : ''"
      />
      <p class="signin-error-p">{{ passwordError }}</p>
      <a class="sign-in-forgot" href="/auth/forgot-password"
        >Forgot your password?</a
      >
      <button type="submit" class="form-button-primary">
        <span v-if="auth.isLoading">
          <Icon name="i-heroicons-arrow-path" class="w-4 h-4 animate-spin" />
          Signing In...
        </span>
        <span v-else> Sign in</span>
      </button>
    </form>
  </div>
</template>

<script setup lang="ts">
import { useAuthStore } from "~/store/auth";

const email = ref("");
const password = ref("");

const emailError = ref<string | null>(null);
const passwordError = ref<string | null>(null);

const error = ref("");

//const auth = useAuth();
const auth = useAuthStore();
const router = useRouter();

async function handleSignIn() {
  error.value = "";
  emailError.value = validator("email", email.value, "Email");
  passwordError.value = validator("password", password.value, "Password", 8);

  if (emailError.value || passwordError.value) return;

  const data = await auth.login({
    email: email.value,
    password: password.value,
  });

  if (data.success) {
    setTimeout(() => {
      router.push("/");
    }, 800);
  }
}

watch([() => email.value, () => password.value], () => {
  if (auth.error) {
    auth.clearError();
  }
});
</script>

<style scoped>
@reference "../../assets/css/main.css";

.sign-in-container {
  @apply text-text-primary;
}

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
