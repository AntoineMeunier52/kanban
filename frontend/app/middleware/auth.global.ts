import { useAuthStore } from "~/store/auth";

export default defineNuxtRouteMiddleware(async (to) => {
  //execut on client side to prevent SSR
  if (import.meta.server) {
    return;
  }

  const authStore = useAuthStore();

  const publicRoutes = [
    "/auth/authentification",
    "/auth/verify-email",
    "/auth/invit",
    "/auth/forgot-password",
  ];

  if (publicRoutes.includes(to.path)) {
    return;
  }

  if (!authStore.user && !authStore.isLoading) {
    try {
      await authStore.fetchUser();
    } catch (error) {
      console.log("Failed to fetch user:", error);
    }
  }

  if (!authStore.user) {
    return navigateTo("/auth/authentification");
  }
});
