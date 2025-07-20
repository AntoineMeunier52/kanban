<template>
  <div class="sign-up-container">
    <form>
      <div class="signup-name-container">
        <TextInput
          v-model="firstName"
          placeholder="first name"
          label="First name"
        />
        <TextInput
          v-model="lastName"
          placeholder="last name"
          label="Last name"
        />
      </div>
      <TextInput
        v-model="email"
        placeholder="Enter your email"
        label="Email"
        class="mb-[16px]"
      />
      <TextInput
        v-model="password"
        placeholder="Enter your password"
        label="Password"
      />
      <FormButton label="sign up" />
    </form>
  </div>
</template>

<script setup lang="ts">
const firstName = ref("");
const lastName = ref("");
const email = ref("");
const password = ref("");
</script>

<style scoped>
@reference "../../assets/css/main.css";

.sign-up-container {
  @apply text-text-primary;
}

.signup-name-container {
  @apply flex gap-[8px] justify-between mb-[16px];
}
</style>
