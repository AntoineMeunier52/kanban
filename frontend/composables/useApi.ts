export const useApi = () => {
  const config = useRuntimeConfig();
  const baseURL = config.public.apiBase;

  const api = async (endpoint: string, options: any = {}) => {
    try {
      const response = await fetch(`${baseURL}${endpoint}`, {
        credentials: 'include',
        headers: {
          'Content-Type': 'application/json',
          ...options.headers,
        },
        ...options,
      });

      if (response.ok) {
        const data = await response.json();
        return { data, error: null };
      } else {
        const error = await response.json();
        return { data: null, error: error.message || 'Erreur' };
      }
    } catch {
      return { data: null, error: 'Erreur de connexion' };
    }
  };

  const get = (endpoint: string) => api(endpoint);
  
  const post = (endpoint: string, body: any) => 
    api(endpoint, { method: 'POST', body: JSON.stringify(body) });

  return { get, post };
};