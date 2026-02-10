import react from '@vitejs/plugin-react'
import { defineConfig } from 'vite'

export default defineConfig({
  plugins: [react()],
  // Use relative paths for cPanel deployment
  base: '/', 
  build: {
    // Output directly to Laravel public directory
    outDir: '../public',
    emptyOutDir: true, // Warning: this deletes contents of public! We should be careful... 
    // actually, let's NOT emptyOutDir: true because public/index.php and .htaccess exist there.
    // Let's build to a subdirectory 'build' or just handle assets.
    // If we build to '../public', Vite usually deletes everything. 
    // SAFE APPROACH: Build to 'dist' (default), then USER MANUAL copy is safer for cPanel novice.
    // OR: Build to '../public/build' and we point layout there.
    // Let's go with '../public/build' to be safe and clean.
    outDir: '../public/build', 
    emptyOutDir: true,
    rollupOptions: {
      output: {
        entryFileNames: 'assets/[name]-[hash].js',
        chunkFileNames: 'assets/[name]-[hash].js',
        assetFileNames: 'assets/[name]-[hash].[ext]',
        manualChunks: {
          vendor: ['react', 'react-dom', 'react-router-dom'],
          ui: ['framer-motion', 'lucide-react'],
        }
      }
    },
    sourcemap: false,
    chunkSizeWarningLimit: 600,
  }
})