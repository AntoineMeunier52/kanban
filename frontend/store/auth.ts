import { defineStore } from "pinia";
import type { User } from "~/types/User";
import { useAuthPersistence } from "~/composables/useAuthPersistence";

interface LoginCredentials {
  email: string;
  password: string;
}

interface LoginResponse {
  user: User;
  message?: string;
}

interface RegisterBody {
  firstName: string;
  lastName: string;
  email: string;
  password: string;
}

interface RegisterResponse {
  id: number;
  firstName: string;
  lastName: string;
  email: string;
}

interface VerifyEmailBody {
  email: string | null;
  code: string;
}

export const useAuthStore = defineStore("auth", () => {
  const config = useRuntimeConfig();
  const baseURL = config.public.apiBase;
  const { saveUser, loadUser, clearUser } = useAuthPersistence();

  const user = ref<User | null>(import.meta.client ? loadUser() : null);
  const isLoading = ref(false);
  const error = ref<string | null>(null);
  const registrering = ref<string | null>(null);

  async function login(
    credentials: LoginCredentials
  ): Promise<{ success: boolean; error?: string }> {
    isLoading.value = true;
    error.value = null;

    try {
      const response = await $fetch<LoginResponse>(
        `${baseURL}/api/auth/login`,
        {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(credentials),
          credentials: "include",
        }
      );

      user.value = response.user;
      saveUser(response.user);

      return { success: true };
    } catch (err: unknown) {
      const errorMessage =
        err &&
        typeof err === "object" &&
        "data" in err &&
        err.data &&
        typeof err.data === "object" &&
        "message" in err.data
          ? (err.data as { message: string }).message
          : "An error occurred during login";
      error.value = errorMessage;
      user.value = null;
      return { success: false, error: errorMessage };
    } finally {
      isLoading.value = false;
    }
  }

  async function register(
    registerInfo: RegisterBody
  ): Promise<{ success: boolean; error?: string }> {
    isLoading.value = true;
    error.value = null;

    try {
      const response = await $fetch<RegisterResponse>(
        `${baseURL}/api/auth/register`,
        {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(registerInfo),
          credentials: "include",
        }
      );

      registrering.value = response.email;
      console.log("nul ca alors", registrering.value);
      return { success: true };
    } catch (err: unknown) {
      const errorMessage =
        err &&
        typeof err === "object" &&
        "data" in err &&
        err.data &&
        typeof err.data === "object" &&
        "message" in err.data
          ? (err.data as { message: string }).message
          : "An error occurred during login";
      error.value = errorMessage;
      return { success: false, error: errorMessage };
    } finally {
      isLoading.value = false;
    }
  }

  async function verifyEmail(
    verifyBody: VerifyEmailBody
  ): Promise<{ success: boolean; error?: string }> {
    isLoading.value = true;
    error.value = null;

    try {
      const response = await $fetch<LoginResponse>(
        `${baseURL}/api/auth/verify-email`,
        {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(verifyBody),
          credentials: "include",
        }
      );

      if (response.user) {
        user.value = response.user;
        saveUser(response.user);
      } else if ("id" in response && "email" in response) {
        // Si la structure est différente, adapter
        user.value = response as unknown as User;
        saveUser(response as unknown as User);
      }

      return { success: true };
    } catch (err: unknown) {
      const errorMessage =
        err &&
        typeof err === "object" &&
        "data" in err &&
        err.data &&
        typeof err.data === "object" &&
        "message" in err.data
          ? (err.data as { message: string }).message
          : "An error occurred during login";
      error.value = errorMessage;
      user.value = null;
      return { success: false, error: errorMessage };
    } finally {
      isLoading.value = false;
    }
  }

  async function fetchUser() {
    isLoading.value = true;
    try {
      console.log("Fetching user from:", `${baseURL}/api/user/me`);
      const response = await $fetch<User>(`${baseURL}/api/user/me`, {
        credentials: "include",
      });
      console.log("Fetch user response:", response);
      user.value = response;
      saveUser(response);
    } catch (error) {
      console.log("Fetch user failed:", error);
      user.value = null;
    } finally {
      isLoading.value = false;
    }
  }

  async function logout() {
    isLoading.value = true;
    try {
      await $fetch(`${baseURL}/api/auth/logout`, {
        method: "POST",
        credentials: "include",
      });
    } catch {
      // Ignore logout errors
    } finally {
      user.value = null;
      error.value = null;
      isLoading.value = false;
      clearUser();
    }
  }

  function clearError() {
    error.value = null;
  }

  async function resendVerificationEmail(email: string) {
    isLoading.value = true;
    error.value = null;

    try {
      await $fetch(`${baseURL}/api/auth/resend-code`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ email }),
        credentials: "include",
      });

      return { success: true };
    } catch (err: unknown) {
      const errorMessage =
        err &&
        typeof err === "object" &&
        "data" in err &&
        err.data &&
        typeof err.data === "object" &&
        "message" in err.data
          ? (err.data as { message: string }).message
          : "An error occurred while resending verification email";
      error.value = errorMessage;
      return { success: false, error: errorMessage };
    } finally {
      isLoading.value = false;
    }
  }

  return {
    user,
    isLoading,
    error,
    registrering,
    login,
    fetchUser,
    logout,
    register,
    clearError,
    verifyEmail,
    resendVerificationEmail,
  };
});
