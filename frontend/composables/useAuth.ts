import { useApi } from "./useApi";

export const useAuth = () => {
  const api = useApi();
  const user = ref(null);
  const isLoading = ref(false);

  const login = async (email: string, password: string) => {
    isLoading.value = true;
    const { data, error } = await api.post("/api/auth/login", {
      email,
      password,
    });
    isLoading.value = false;

    if (error) return { success: false, error };

    user.value = data.user;
    return { success: true };
  };

  const register = async (
    firstName: string,
    lastName: string,
    email: string,
    password: string
  ) => {
    isLoading.value = true;
    const { data, error } = await api.post("/auth/register", {
      firstName,
      lastName,
      email,
      password,
    });
    isLoading.value = false;

    return error
      ? { success: false, error }
      : { success: true, message: data.message };
  };

  const verifyEmail = async (email: string, code: string) => {
    isLoading.value = true;
    const { data, error } = await api.post("/auth/verify-email", {
      email,
      code,
    });
    isLoading.value = false;

    if (error) return { success: false, error };

    user.value = data.user;
    return { success: true };
  };

  const resendCode = async (email: string) => {
    isLoading.value = true;
    const { data, error } = await api.post("/auth/resend-verification-code", {
      email,
    });
    isLoading.value = false;

    return error
      ? { success: false, error }
      : { success: true, message: data.message };
  };

  const forgotPassword = async (email: string) => {
    isLoading.value = true;
    const { data, error } = await api.post("/auth/forgot-password", { email });
    isLoading.value = false;

    return error
      ? { success: false, error }
      : { success: true, message: data.message };
  };

  const resetPassword = async (
    email: string,
    token: string,
    newPassword: string
  ) => {
    isLoading.value = true;
    const { data, error } = await api.post("/auth/reset-password", {
      email,
      token,
      newPassword,
    });
    isLoading.value = false;

    return error
      ? { success: false, error }
      : { success: true, message: data.message };
  };

  const logout = async () => {
    await api.post("/auth/logout", {});
    user.value = null;
    return { success: true };
  };

  const checkAuth = async () => {
    const { data, error } = await api.get("/user/me");
    if (!error && data) {
      user.value = data;
      return true;
    }
    user.value = null;
    return false;
  };

  return {
    user,
    isLoading,
    isAuthenticated: computed(() => !!user.value),
    login,
    register,
    verifyEmail,
    resendCode,
    forgotPassword,
    resetPassword,
    logout,
    checkAuth,
  };
};
