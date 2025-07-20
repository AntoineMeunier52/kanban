export const useApi = () => {
  const config = useRuntimeConfig();
  const baseURL = config.public.apiBase;

  const api = async (endpoint: string, options: any = {}) => {
    try {
      const response = await fetch(`${baseURL}${endpoint}`, {
        credentials: "include",
        headers: {
          "Content-Type": "application/json",
          ...options.headers,
        },
        ...options,
      });

      if (response.ok) {
        const data = await response.json();
        return { data, error: null };
      } else {
        const error = await response.json();
        return { data: null, error: error.message || "Error" };
      }
    } catch {
      return { data: null, error: "Connexion error" };
    }
  };

  const get = (endpoint: string) => api(endpoint);

  const post = (endpoint: string, body: any) =>
    api(endpoint, { method: "POST", body: JSON.stringify(body) });

  const patch = (endpoint: string, body: any) =>
    api(endpoint, { method: "PATCH", body: JSON.stringify(body) });

  const del = (endpoint: string) => api(endpoint, { methods: "DELETE" });

  return { get, post, patch, del };
};
