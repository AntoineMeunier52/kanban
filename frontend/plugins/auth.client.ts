export default defineNuxtPlugin(async () => {
  const auth = useAuth();
  
  // Initialize authentication from backend cookies on startup
  console.log('🔐 Initializing auth from backend cookies...');
  await auth.initAuth();
  console.log('🔐 Auth init complete. User:', auth.user.value);
});