<template>
  <div class="max-w-4xl mx-auto">
    <!-- Welcome Section -->
    <div class="text-center py-12">
      <h1 class="text-4xl font-bold text-gray-900 dark:text-gray-100 mb-4">
        Welcome to your Kanban Dashboard
      </h1>
      <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
        Organize your projects, manage your tasks, and boost your productivity with our intuitive kanban board system.
      </p>
    </div>

    <!-- User Welcome -->
    <div v-if="authStore.user" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-8">
      <div class="flex items-center space-x-4">
        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
          <span class="text-white text-lg font-semibold">
            {{ userInitials }}
          </span>
        </div>
        <div>
          <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
            Hello, {{ authStore.user.firstName }}! 👋
          </h2>
          <p class="text-gray-600 dark:text-gray-400">
            Ready to get productive today?
          </p>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
      <UCard class="hover:shadow-md transition-shadow duration-200">
        <template #header>
          <div class="flex items-center space-x-3">
            <Icon name="i-heroicons-plus-circle" class="w-6 h-6 text-blue-500" />
            <h3 class="font-semibold">Create Board</h3>
          </div>
        </template>
        <p class="text-gray-600 dark:text-gray-400 mb-4">
          Start a new project with a fresh kanban board
        </p>
        <UButton color="blue" variant="outline" class="w-full">
          New Board
        </UButton>
      </UCard>

      <UCard class="hover:shadow-md transition-shadow duration-200">
        <template #header>
          <div class="flex items-center space-x-3">
            <Icon name="i-heroicons-users" class="w-6 h-6 text-green-500" />
            <h3 class="font-semibold">Team Collaboration</h3>
          </div>
        </template>
        <p class="text-gray-600 dark:text-gray-400 mb-4">
          Invite team members and collaborate on projects
        </p>
        <UButton color="green" variant="outline" class="w-full">
          Invite Team
        </UButton>
      </UCard>

      <UCard class="hover:shadow-md transition-shadow duration-200">
        <template #header>
          <div class="flex items-center space-x-3">
            <Icon name="i-heroicons-chart-bar" class="w-6 h-6 text-purple-500" />
            <h3 class="font-semibold">Analytics</h3>
          </div>
        </template>
        <p class="text-gray-600 dark:text-gray-400 mb-4">
          Track your productivity and project progress
        </p>
        <UButton color="purple" variant="outline" class="w-full">
          View Stats
        </UButton>
      </UCard>
    </div>

    <!-- Recent Activity -->
    <UCard>
      <template #header>
        <h3 class="font-semibold text-lg">Recent Activity</h3>
      </template>
      <div class="space-y-4">
        <div class="flex items-center space-x-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
          <Icon name="i-heroicons-check-circle" class="w-5 h-5 text-green-500" />
          <div>
            <p class="font-medium">Welcome to Kanban!</p>
            <p class="text-sm text-gray-600 dark:text-gray-400">
              Your account has been successfully set up
            </p>
          </div>
        </div>
        
        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
          <Icon name="i-heroicons-inbox" class="w-12 h-12 mx-auto mb-2 opacity-50" />
          <p>No recent activity yet. Start by creating your first board!</p>
        </div>
      </div>
    </UCard>
  </div>
</template>

<script setup lang="ts">
import { useAuthStore } from '~/store/auth';

const authStore = useAuthStore();

// User initials for avatar
const userInitials = computed(() => {
  if (!authStore.user) return '';
  const first = authStore.user.firstName?.charAt(0) || '';
  const last = authStore.user.lastName?.charAt(0) || '';
  return (first + last).toUpperCase();
});
</script>
