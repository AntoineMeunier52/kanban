import type { User } from "~/types/User";

export const useAuthStore = () => {
  const isLogin = false;

  return {
    isLogin,
  };
};
