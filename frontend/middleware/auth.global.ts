import { useAuth } from "~/composables/useAuth";

export default defineNuxtRouteMiddleware(async (to) => {
  const { user, fetchUser } = useAuth();

  const publicRoutes = ["/authentification", "/verify-email", "/invit"];

  if (!user.value) {
    await fetchUser();
  }

  if (!user.value && !publicRoutes.includes(to.path)) {
    return navigateTo("/authentification");
  }
});
