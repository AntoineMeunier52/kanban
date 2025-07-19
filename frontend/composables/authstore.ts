import { useAuth } from "./useAuth";

export const useAuthStore = () => {
  const auth = useAuth();

  return {
    ...auth,
    isLogin: auth.isAuthenticated,
  };
};
