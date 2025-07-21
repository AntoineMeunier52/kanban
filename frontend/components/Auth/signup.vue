<template>
  <div class="sign-up-container">
    <form @submit.prevent="signup">
      <ErrorBox :error="auth.error" />
      <div class="signup-name-container">
        <div>
          <label for="email">First name</label>
          <input
            v-model="firstName"
            type="text"
            placeholder="First name"
            name="firstname"
            class="input-text mb-[8px]"
            :class="nameError ? 'border !border-red-500' : ''"
          />
        </div>
        <div>
          <label for="email">Last name</label>
          <input
            v-model="lastName"
            type="text"
            placeholder="Last name"
            name="lastname"
            class="input-text"
            :class="nameError ? 'border !border-red-500' : ''"
          />
        </div>
      </div>
      <p class="signup-error">{{ nameError }}</p>
      <div class="my-[16px]">
        <label for="email">Email</label>
        <input
          v-model="email"
          type="text"
          placeholder="Enter your email"
          name="email"
          class="input-text mb-[8px]"
          :class="emailError ? 'border !border-red-500' : ''"
        />
        <p class="signup-error">{{ emailError }}</p>
      </div>
      <div>
        <label for="password">Password</label>
        <input
          v-model="password"
          type="password"
          placeholder="Enter your password"
          name="password"
          class="input-text mb-[8px]"
          :class="passwordError ? 'border !border-red-500' : ''"
        />
        <p class="signup-error">{{ passwordError }}</p>
      </div>
      <button type="submit" class="form-button-primary">
        <span v-if="auth.isLoading">
          <Icon name="i-heroicons-arrow-path" class="w-4 h-4 animate-spin" />
          Signing Up...
        </span>
        <span v-else> Sign Up</span>
      </button>
    </form>
  </div>
</template>

<script setup lang="ts">
import { useAuthStore } from "~/store/auth";

const firstName = ref("");
const lastName = ref("");
const email = ref("");
const password = ref("");

const nameError = ref("");
const emailError = ref("");
const passwordError = ref("");

const auth = useAuthStore();
const router = useRouter();

async function signup() {
  nameError.value =
    validator("name", firstName.value, "First name") ||
    validator("name", lastName.value, "Last name");
  emailError.value = validator("email", email.value, "Email");
  passwordError.value = validator("password", password.value, "Password", 8);

  if (nameError.value || emailError.value || passwordError.value) return;

  const data = await auth.register({
    firstName: firstName.value,
    lastName: lastName.value,
    email: email.value,
    password: password.value,
  });

  if (data.success) {
    setTimeout(() => {
      router.push({
        path: "/auth/verify-email",
        query: { email: email.value },
      });
    }, 800);
  }
}

watch(
  [
    () => email.value,
    () => password.value,
    () => firstName.value,
    () => lastName.value,
  ],
  () => {
    if (auth.error) {
      auth.clearError();
    }
    nameError.value = "";
    emailError.value = "";
    passwordError.value = "";
  }
);

onUnmounted(() => auth.clearError());
</script>

<style scoped>
@reference "../../assets/css/main.css";

.sign-up-container {
  @apply text-text-primary;
}

.signup-name-container {
  @apply flex gap-[8px] justify-between;
}

.signup-error {
  @apply text-[14px] text-red-500 mb-[8px];
}
</style>
