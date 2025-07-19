export default defineNuxtRouteMiddleware(async (to) => {
  if (import.meta.client) {
    return;
  }

  //create store auth

  const publicRoutes = ["/auth", "/auth/forgot-password"];

  if (publicRoutes.includes(to.path)) {
    return;
  }
});
