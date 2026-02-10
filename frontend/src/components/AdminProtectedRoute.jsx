import axios from 'axios';
import { useEffect, useRef, useState } from 'react';
import { Navigate, Outlet, useNavigate } from 'react-router-dom';
import { toast } from 'react-toastify';
import { apiUrl } from '../config/api';
import logger from "../utils/logger";

const CACHE_KEY = 'admin_auth_validated';
const CACHE_TTL = 5 * 60 * 1000; // 5 minutes

const AdminProtectedRoute = () => {
  const navigate = useNavigate();
  
  // Check cached auth immediately (synchronous) to avoid blink
  const getCachedAuth = () => {
    const token = localStorage.getItem("token");
    const role = localStorage.getItem("role");
    if (!token || role !== "admin") return false;
    
    const cached = sessionStorage.getItem(CACHE_KEY);
    if (cached) {
      const { timestamp } = JSON.parse(cached);
      if (Date.now() - timestamp < CACHE_TTL) return true;
    }
    return null; // null means needs validation
  };
  
  const cachedAuth = getCachedAuth();
  const [isValidating, setIsValidating] = useState(cachedAuth === null);
  const [isAuthenticated, setIsAuthenticated] = useState(cachedAuth === true);
  const validationDone = useRef(cachedAuth !== null);

  useEffect(() => {
    if (validationDone.current) return;
    validationDone.current = true;
    
    const validateToken = async () => {
      const token = localStorage.getItem("token");
      const role = localStorage.getItem("role");
      
      // No token or not admin
      if (!token || role !== "admin") {
        localStorage.removeItem("token");
        localStorage.removeItem("role");
        localStorage.removeItem("loginUsername");
        localStorage.removeItem("nama_lengkap");
        localStorage.removeItem("profile");
        localStorage.removeItem("lastActiveTime");
        localStorage.removeItem("wasAdmin");
        sessionStorage.removeItem(CACHE_KEY);
        
        window.dispatchEvent(new Event('storage'));
        setIsAuthenticated(false);
        setIsValidating(false);
        toast.error("🔒 Akses ditolak. Silakan login sebagai admin");
        return;
      }

      try {
        await axios.get(apiUrl("/auth/check"), {
          headers: { 
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json"
          }
        });
        
        // Cache the successful validation
        sessionStorage.setItem(CACHE_KEY, JSON.stringify({ timestamp: Date.now() }));
        localStorage.setItem("lastActiveTime", Date.now().toString());
        setIsAuthenticated(true);
      } catch (error) {
        logger.error("Token validation failed:", error);
        localStorage.removeItem("token");
        localStorage.removeItem("role");
        localStorage.removeItem("loginUsername");
        localStorage.removeItem("nama_lengkap");
        localStorage.removeItem("profile");
        localStorage.removeItem("lastActiveTime");
        localStorage.removeItem("wasAdmin");
        sessionStorage.removeItem(CACHE_KEY);
        
        window.dispatchEvent(new Event('storage'));
        toast.error("🔒 Sesi telah berakhir. Silakan login kembali");
        setIsAuthenticated(false);
      } finally {
        setIsValidating(false);
      }
    };

    validateToken();
  }, []);

  // Inactivity timer (separate effect)
  useEffect(() => {
    const resetInactiveTimer = () => {
      localStorage.setItem("lastActiveTime", Date.now().toString());
    };

    const inactivityCheckInterval = setInterval(() => {
      const role = localStorage.getItem("role");
      if (role === "admin") {
        const lastActive = localStorage.getItem("lastActiveTime");
        const currentTime = Date.now();
        const inactiveDuration = currentTime - (parseInt(lastActive) || currentTime);
        const MAX_INACTIVE_TIME = 30 * 60 * 1000;
        
        if (inactiveDuration > MAX_INACTIVE_TIME) {
          localStorage.removeItem("token");
          localStorage.removeItem("role");
          localStorage.removeItem("loginUsername");
          localStorage.removeItem("nama_lengkap");
          localStorage.removeItem("profile");
          localStorage.removeItem("lastActiveTime");
          localStorage.removeItem("wasAdmin");
          sessionStorage.removeItem(CACHE_KEY);
          
          window.dispatchEvent(new Event('storage'));
          setIsAuthenticated(false);
          toast.info("⏰ Sesi telah berakhir karena tidak aktif");
          navigate("/admin/login");
          clearInterval(inactivityCheckInterval);
        } else if (inactiveDuration > 25 * 60 * 1000) {
          toast.warning("⚠️ Sesi akan berakhir dalam 5 menit");
        }
      }
    }, 60000);

    const events = ['mousemove', 'keypress', 'click', 'scroll', 'touchstart'];
    events.forEach(event => {
      window.addEventListener(event, resetInactiveTimer);
    });

    return () => {
      clearInterval(inactivityCheckInterval);
      events.forEach(event => {
        window.removeEventListener(event, resetInactiveTimer);
      });
    };
  }, [navigate]);

  if (isValidating) {
    // Minimal loading indicator instead of full-screen flash
    return (
      <div className="min-h-screen bg-gradient-to-br from-[#0A0E0B] via-[#1A1F16] to-[#0A0E0B]">
        <div className="fixed top-0 left-0 w-full h-1 z-50">
          <div className="h-full bg-[#D7FE51] animate-pulse" style={{ width: '60%' }}></div>
        </div>
      </div>
    );
  }

  return isAuthenticated ? <Outlet /> : <Navigate to="/admin/login" replace />;
};

export default AdminProtectedRoute;
