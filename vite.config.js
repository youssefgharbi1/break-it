import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';

export default defineConfig({
  plugins: [react()],
  resolve: {
    alias: {
      'react-router-dom': 'react-router-dom',
    },
  },
  server: {
    host: '0.0.0.0', // Expose to local network
    port: 5173, // Optional: set a custom port if needed
  },
});
