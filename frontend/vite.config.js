import react from '@vitejs/plugin-react'
import { defineConfig } from 'vite'

export default defineConfig({
  plugins: [react()],
  // Use relative paths for cPanel deployment
  base: './',
  build: {
    rollupOptions: {
      output: {
        manualChunks: {
          // Vendor chunk — React core
          vendor: ['react', 'react-dom', 'react-router-dom'],
          // UI libraries chunk
          ui: ['framer-motion', 'lucide-react'],
        }
      }
    },
    // Disable sourcemaps in production
    sourcemap: false,
    // Increase chunk size warning limit (some vendor chunks are naturally large)
    chunkSizeWarningLimit: 600,
  }
})