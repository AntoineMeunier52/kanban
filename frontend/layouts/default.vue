<template>
  <header class="navbar-container">
    <NuxtLink to="/" class="navbar-container-brand">
      <h1 class="navbar-brand">KANBAN</h1>
    </NuxtLink>
    <NavbarUserMenu />
  </header>
  <main class="main-container">
    <slot></slot>
  </main>
  <footer class="footer-container"></footer>
</template>

<script setup lang="ts"></script>

<style scoped>
@reference "../assets/css/main.css";

.navbar-container {
  @apply h-[64px] flex items-center justify-between px-[24px] bg-primary-creme;
}

.navbar-container-brand {
  @apply flex items-center;
}

.navbar-brand {
  @apply text-[28px] font-thin tracking-[-0.5px] text-text-primary font-stardom;
}

.main-container {
  @apply bg-primary-creme min-h-[calc(100vh-64px)];
}

.footer-container {
  @apply bg-primary-creme flex justify-center items-center;
}
</style>
