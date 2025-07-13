export default defineNuxtPlugin(() => {
  if (process.client && process.dev) {
    // Intercepter les erreurs de fetch pour debugging
    const originalFetch = window.fetch;
    
    window.fetch = async (input: RequestInfo | URL, init?: RequestInit) => {
      try {
        console.log('🔍 API Request:', { input, init });
        const response = await originalFetch(input, init);
        console.log('✅ API Response:', response.status, response.statusText);
        return response;
      } catch (error) {
        console.error('❌ API Error:', error);
        throw error;
      }
    };
  }
});