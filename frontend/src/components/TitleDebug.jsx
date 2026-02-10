import logger from "../utils/logger";
// TitleDebug.jsx - Buat file baru di src/components/TitleDebug.jsx
import { useEffect } from 'react';
import { useLocation } from 'react-router-dom';

export default function TitleDebug() {
  const location = useLocation();

  useEffect(() => {
    const checkAndUpdateTitle = () => {
      const token = localStorage.getItem('token');
      const role = localStorage.getItem('role');
      
      logger.log('🔍 [DEBUG] Title Check:', { 
        token: token ? 'YES' : 'NO', 
        role: role || 'null/undefined',
        path: location.pathname 
      });
      
      // LOGIKA YANG SANGAT JELAS
      if (token && role === 'admin') {
        document.title = 'Gastronomi Admin';
        logger.log('✅ Title set to: Gastronomi Admin (Admin detected)');
      } else {
        document.title = 'Gastronomi Run';
        logger.log('✅ Title set to: Gastronomi Run (User or no token)');
      }
    };

    // Check sekarang
    checkAndUpdateTitle();
    
    // Check setiap 2 detik
    const interval = setInterval(checkAndUpdateTitle, 2000);
    
    return () => clearInterval(interval);
  }, [location]);

  return null;
}