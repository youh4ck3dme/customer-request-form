import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [react()],
  server: {
    proxy: {
      '/wp-json': {
        target: 'https://api.gruppa.cloud',
        changeOrigin: true,
        secure: true,
      }
    }
  }
})
