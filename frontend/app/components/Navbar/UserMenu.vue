<template>
  <div class="flex items-center space-x-4">
    <!-- Logout Button -->
    <UButton
      v-if="authStore.user"
      :loading="authStore.isLoading"
      :disabled="authStore.isLoading"
      color="neutral"
      variant="outline"
      size="sm"
      class="logout-button"
      @click="handleLogout"
    >
      <template v-if="!authStore.isLoading">
        <Icon
          name="i-heroicons-arrow-right-on-rectangle"
          class="w-4 h-4 mr-2"
        />
        <span class="hidden sm:inline">Logout</span>
      </template>
      <template v-else>
        <Icon name="i-heroicons-arrow-path" class="w-4 h-4 mr-2 animate-spin" />
        <span class="hidden sm:inline">Logging out...</span>
      </template>
    </UButton>

    <!-- Dark Mode Toggle -->
    <UButton
      :icon="isDark ? 'i-lucide-sun' : 'i-lucide-moon'"
      size="sm"
      color="neutral"
      variant="outline"
      :aria-label="isDark ? 'Switch to light mode' : 'Switch to dark mode'"
      @click="toggleDarkMode"
    />
    <!-- Avatar -->
    <div
      v-if="authStore.user"
      class="w-8 h-8 bg-[#171717] rounded-full flex items-center justify-center"
    >
      <span class="text-white text-sm font-semibold">
        {{ userInitials }}
      </span>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useAuthStore } from "~/store/auth";

const authStore = useAuthStore();
const router = useRouter();
const toast = useToast();

const colorMode = useColorMode();
const darkModeCookie = useCookie("dark-mode", {
  default: () => "light",
  maxAge: 60 * 60 * 24 * 365, // 1 an
});

const isDark = computed(() => colorMode.value === "dark");

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
  darkModeCookie.value = newMode;
};

// Logout function
const handleLogout = async () => {
  try {
    await authStore.logout();

    toast.add({
      title: "Logged out successfully",
      description: "You have been logged out of your account",
      icon: "i-heroicons-check-circle",
      color: "success",
    });

    // Redirect to login page
    await router.push("/auth/authentification");
  } catch (error) {
    toast.add({
      title: "Logout failed",
      description: "An error occurred while logging out",
      icon: "i-heroicons-exclamation-circle",
      color: "error",
    });
    console.error("Logout error:", error);
  }
};

// Initialize dark mode - éviter les changements après hydratation
onMounted(() => {
  // Seulement si la valeur diffère pour éviter les re-renders
  if (darkModeCookie.value && darkModeCookie.value !== colorMode.preference) {
    colorMode.preference = darkModeCookie.value;
  }
});

// Watch dark mode changes
watch(
  () => colorMode.preference,
  (newMode) => {
    darkModeCookie.value = newMode;
  }
);
</script>

<style scoped>
/* Logout button animations */
.logout-button :deep(button) {
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
  position: relative;
  overflow: hidden;
}

.logout-button :deep(button:hover:not(:disabled)) {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(239, 68, 68, 0.15);
}

.logout-button :deep(button:active:not(:disabled)) {
  transform: translateY(0);
}

/* Ripple effect on click */
.logout-button :deep(button:not(:disabled))::before {
  content: "";
  position: absolute;
  top: 50%;
  left: 50%;
  width: 0;
  height: 0;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.3);
  transform: translate(-50%, -50%);
  transition: width 0.6s, height 0.6s;
}

.logout-button :deep(button:active:not(:disabled))::before {
  width: 200px;
  height: 200px;
}

/* Avatar animation */
.w-8.h-8 {
  transition: transform 0.2s ease;
}

.w-8.h-8:hover {
  transform: scale(1.05);
}
</style>
