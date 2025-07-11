import type { User } from "~/types/User";

export const useAuth = () => {
  const user = useState<User | null>("auth_user", () => null);

  const fetchUser = async () => {
    try {
      const { data } = await useFetch<User>(
        "https://127.0.0.1:8000/api/use/me",
        {
          credentials: "include",
        }
      );
      user.value = data.value ?? null;
    } catch {
      user.value = null;
    }
  };
  return { user, fetchUser };
};
