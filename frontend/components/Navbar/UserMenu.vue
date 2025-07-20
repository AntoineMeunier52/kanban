<template>
  <div class="flex items-center space-x-4">
    <!-- Logout Button -->
    <button
      v-if="authStore.user"
      :loading="authStore.isLoading"
      :disabled="authStore.isLoading"
      class="logout-button"
      @click="handleLogout"
    >
      <template v-if="!authStore.isLoading">
        <Icon
          name="i-heroicons-arrow-right-end-on-rectangle"
          class="w-4 h-4 mr-2"
        />
        <span class="hidden sm:inline">Logout</span>
      </template>
      <template v-else>
        <Icon name="i-heroicons-arrow-path" class="w-4 h-4 mr-2 animate-spin" />
        <span class="hidden sm:inline">Logging out...</span>
      </template>
    </button>

    <!-- Dark Mode Toggle -->

    <button
      :aria-label="isDark ? 'Switch to light mode' : 'Switch to dark mode'"
      @click="toggleDarkMode"
      class="dark-mode-button"
    >
      <Icon :name="isDark ? 'i-lucide-sun' : 'i-lucide-moon'" class="w-4 h-4" />
    </button>

    <!-- Avatar -->
    <div
      v-if="authStore.user"
      class="w-8 h-8 bg-black rounded-full flex items-center justify-center"
    >
      <span class="text-white text-sm font-semibold">
        {{ userInitials }}
      </span>
    </div>
    <Icon name="i-lucide-moon" class="!hidden" />
    <Icon name="i-lucide-sun" class="!hidden" />
    <Icon name="i-heroicons-arrow-right-end-on-rectangle" class="!hidden" />
    <Icon name="i-heroicons-arrow-path" class="!hidden" />
  </div>
</template>

<script setup lang="ts">
import { useAuthStore } from "~/store/auth";

const authStore = useAuthStore();
const router = useRouter();
const colorMode = useColorMode();

const isDark = computed(() => {
  console.log("isDark computed - colorMode.value:", colorMode.value);
  return colorMode.value === "dark";
});

// User initials for avatar
const userInitials = computed(() => {
  if (!authStore.user) return "";
  const first = authStore.user.firstName?.charAt(0) || "";
  const last = authStore.user.lastName?.charAt(0) || "";
  return (first + last).toUpperCase();
});

// Dark mode toggle
const toggleDarkMode = () => {
  const newMode = colorMode.value === "dark" ? "light" : "dark";
  colorMode.preference = newMode;
};

// Logout function
const handleLogout = async () => {
  try {
    await authStore.logout();

    // Redirect to login page
    await router.push("/auth/authentification");
  } catch (error) {
    console.error("Logout error:", error);
  }
};
</script>

<style scoped>
@reference "../../assets/css/main.css";

.dark-mode-button {
  @apply flex justify-center items-center min-w-[34px] min-h-[34px] text-text-primary bg-primary-creme border border-border-primary font-bold overflow-hidden rounded;
}

.dark-mode-button:hover {
  @apply bg-solid-creme border-0;
}

.logout-button {
  @apply min-h-[34px] bg-primary-creme flex items-center border border-border-primary rounded p-[4px] text-text-primary;
}

.logout-button:hover {
  @apply bg-solid-creme border-0;
}
</style>
