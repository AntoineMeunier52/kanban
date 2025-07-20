import type { User } from "~/types/User";

export const useAuthPersistence = () => {
  const STORAGE_KEY = "kanban_auth_user";

  const saveUser = (user: User) => {
    if (import.meta.client) {
      try {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(user));
      } catch (error) {
        console.warn("Failed to save user to localStorage:", error);
      }
    }
  };

  const loadUser = () => {
    if (import.meta.client) {
      try {
        const stored = localStorage.getItem(STORAGE_KEY);
        return stored ? JSON.parse(stored) : null;
      } catch (error) {
        console.warn("Failed to load user from localStorage:", error);
        return null;
      }
    }
    return null;
  };

  const clearUser = () => {
    if (import.meta.client) {
      try {
        localStorage.removeItem(STORAGE_KEY);
      } catch (error) {
        console.warn("Failed to clear user from localStorage:", error);
      }
    }
  };

  return {
    saveUser,
    loadUser,
    clearUser,
  };
};
