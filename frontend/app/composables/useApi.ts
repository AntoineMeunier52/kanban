export const useApi = () => {
  const config = useRuntimeConfig();
  const baseURL = config.public.apiBase || "https://127.0.0.1:8000";

  const defaultOptions = {
    baseURL,
    credentials: "include" as RequestCredentials,
    headers: {
      "Content-Type": "application/json",
    },
    // no SSL for dev
    ...(process.dev && {
      server: false, //no ssr fetch
    }),
  };

  // fetch wrapper
  const apiCall = <T = any>(endpoint: string, options: any = {}) => {
    return $fetch<T>(endpoint, {
      ...defaultOptions,
      ...options,
      headers: {
        ...defaultOptions.headers,
        ...options.headers,
      },
    });
  };

  return {
    baseURL,
    apiCall,
    defaultOptions,
  };
};
