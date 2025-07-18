<template>
  <div>
    <form>
      <TextInput
        v-model="email"
        placeholder="Enter your email"
        label="Email"
        class="mb-[16px]"
      />
      <TextInput
        v-model="password"
        type="password"
        placeholder="Enter your password"
        label="Password"
      />
      <a class="sign-in-forgot" href="/auth/forgot-password"
        >Forgot your password?</a
      >
      <FormButton label="Sign in" />
    </form>
  </div>
</template>

<script setup lang="ts">
const email = ref("");
const password = ref("");
</script>

<style scoped>
@reference "../../assets/css/main.css";

.sign-in-forgot {
  @apply text-[14px] text-text-secondary underline;
}

.sign-in-forgot:hover {
  @apply text-accent-primary;
}
</style>
