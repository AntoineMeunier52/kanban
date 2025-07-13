<template>
  <div class="min-h-screen flex flex-col bg-gray-50 dark:bg-[#171717]">
    <header
      class="h-[64px] bg-white border-b border-gray-200 shadow-sm dark:bg-[#171717]"
    >
      <div class="h-full mx-auto px-6 flex items-center justify-between">
        <!-- Logo -->
        <NuxtLink to="/" class="flex items-center">
          <h1
            class="text-[28px] font-thin tracking-[-0.5px] text-gray-900 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200"
          >
            KANBAN
          </h1>
        </NuxtLink>

        <!-- User Menu -->
        <NavbarUserMenu />
      </div>
    </header>

    <main class="flex-1 dark:bg-[#171717]">
      <slot></slot>
    </main>

    <footer class="bg-white border-t border-gray-200 dark:bg-[#171717]">
      <div class="max-w-7xl mx-auto px-6 py-4">
        <p class="text-center text-gray-500 text-sm">
          Made with ❤️ for productivity
        </p>
      </div>
    </footer>
  </div>
</template>

<script setup lang="ts"></script>

<style scoped>
.logo {
  color: #1f2937;
}

/* Responsive mobile */
@media (max-width: 768px) {
  .logo {
    font-size: 24px;
  }
}
</style>
