<template>
  <div class="auth-wrapper">
    <div class="auth-container">
      <h1 class="auth-title">KANBAN</h1>
      <Tabs :tabs="myTabs">
        <template #tab-0>
          <AuthSignin />
        </template>

        <template #tab-1>
          <AuthSignup />
        </template>
      </Tabs>
    </div>
  </div>
</template>

<script setup lang="ts">
const myTabs = ["Profil", "Messages"];
</script>

<style scoped>
@reference "../../assets/css/main.css";

.auth-wrapper {
  @apply flex justify-center w-full h-full;
}

.auth-container {
  @apply w-[400px] mt-[15vh];
}

.auth-title {
  @apply text-[60px] text-text-primary font-stardom tracking-[-0.5px] mb-[32px];
}
</style>
