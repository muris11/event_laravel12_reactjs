import axios from "axios";
import { lazy, Suspense, useEffect, useState } from "react";
import "react-confirm-alert/src/react-confirm-alert.css";
import { Navigate, Route, BrowserRouter as Router, Routes, useLocation } from "react-router-dom";
import { ToastContainer } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";
import { apiUrl } from "./config/api";
import logger from "./utils/logger";

// Theme Context
import { ThemeProvider } from "./context/ThemeContext";

// Error Boundary
import ErrorBoundary from "./components/ErrorBoundary";

// Komponen yang selalu di-load (critical path)
import AdminProtectedRoute from "./components/AdminProtectedRoute";
import Navbar from "./components/Navbar";
import PrivateRoute from "./components/PrivateRoute";

// Lazy-loaded: Public pages
const HomeUser = lazy(() => import("./components/HomeUser"));
const TentangKami = lazy(() => import("./components/TentangKami"));
const Layanan = lazy(() => import("./components/Layanan"));
const Partner = lazy(() => import("./components/Partner"));
const Kontak = lazy(() => import("./components/Kontak"));
const KelasListUser = lazy(() => import("./components/KelasListUser"));
const DetailKelasUser = lazy(() => import("./components/DetailKelasUser"));
const Login = lazy(() => import("./components/Login"));
const LoginAdmin = lazy(() => import("./components/LoginAdmin"));
const RegisterUser = lazy(() => import("./components/RegisterUser"));

// Lazy-loaded: Admin pages (only loaded when admin navigates)
const Home = lazy(() => import("./components/Home"));
const DetailKelas = lazy(() => import("./components/DetailKelas"));
const TambahKelas = lazy(() => import("./components/TambahKelas"));
const EditKelas = lazy(() => import("./components/EditKelas"));
const KelasList = lazy(() => import("./components/KelasList"));
const TambahKategori = lazy(() => import("./components/TambahKategori"));
const TentangKamiAdmin = lazy(() => import("./components/TentangKamiAdmin"));
const LayananKamiAdmin = lazy(() => import("./components/LayananKamiAdmin"));
const FooterKontakEditor = lazy(() => import("./components/FooterKontakEditor"));
const KontakAdmin = lazy(() => import("./components/KontakAdmin"));
const PartnerAdmin = lazy(() => import("./components/PartnerAdmin"));

// Loading fallback component
function PageLoader() {
  return (
    <div className="min-h-screen flex items-center justify-center bg-gradient-to-br from-[#0A0E0B] via-[#1A1F16] to-[#0A0E0B]">
      <div className="text-center">
        <div className="w-10 h-10 border-3 border-[#D7FE51] border-t-transparent rounded-full animate-spin mx-auto mb-3"></div>
        <p className="text-[#ABB89D] text-sm">Memuat halaman...</p>
      </div>
    </div>
  );
}

// Hook sederhana untuk update title berdasarkan URL
function useTitleUpdater() {
  const location = useLocation();
  
  useEffect(() => {
    if (location.pathname.startsWith('/admin')) {
      document.title = 'Gastronomi Admin';
    } else {
      document.title = 'Gastronomi Run';
    }
  }, [location]);
}

// Hook untuk lazy socket connection (hanya admin)
function useSocketConnection() {
  useEffect(() => {
    const role = localStorage.getItem("role");
    if (role === "admin") {
      import('./services/socket').then((mod) => {
        const socketService = mod.default;
        socketService.connect();
        window.socketService = socketService;
      });
    }

    return () => {
      if (window.socketService) {
        window.socketService.disconnect();
        window.socketService = null;
      }
    };
  }, []);
}

function AppContent() {
  const [kelasData, setKelasData] = useState([]);
  const [kategoriData, setKategoriData] = useState([]);
  const location = useLocation();

  // Gunakan title updater
  useTitleUpdater();

  // Lazy socket (hanya connect untuk admin)
  useSocketConnection();

  // Path yang tidak perlu navbar
  const hideNavbarPaths = ["/login", "/register", "/admin/login"];

  // Hapus navbar jika di path tertentu
  const shouldHideNavbar = hideNavbarPaths.includes(location.pathname);

  useEffect(() => {
    // Fetch data dari endpoint /kelas
    axios.get(apiUrl("/kelas"))
      .then((res) => {
        logger.log("Data kelas berhasil di-fetch:", res.data);
        setKelasData(res.data);
        
        // Ekstrak kategori dari data kelas
        const kategoriUnik = [...new Set(res.data.map((kelas) => {
          return kelas.kategori_nama || "";
        }))];
        setKategoriData(kategoriUnik.filter(Boolean));
        logger.log("Kategori unik:", kategoriUnik.filter(Boolean));
      })
      .catch((err) => {
        logger.error("Error fetching kelas data:", err);
      });
  }, []);

  return (
    <>
      {!shouldHideNavbar && <Navbar />}

      <Suspense fallback={<PageLoader />}>
        <Routes>
          {/* ==================== */}
          {/* PUBLIC USER ROUTES */}
          {/* ==================== */}
          <Route path="/" element={<HomeUser />} />
          <Route path="/tentang-kami" element={<TentangKami />} />
          <Route path="/layanan" element={<Layanan />} />
          <Route path="/partner" element={<Partner />} />
          <Route path="/kontak" element={<Kontak />} />
          <Route path="/events" element={<KelasListUser kelasData={kelasData} kategoriData={kategoriData} />} />
          <Route path="/events/:id" element={<DetailKelasUser />} />
          <Route path="/barang/:id" element={<DetailKelasUser />} />
          
          {/* Login dan Register */}
          <Route path="/login" element={<Login />} />
          <Route path="/register" element={<RegisterUser />} />
          
          {/* ==================== */}
          {/* ADMIN LOGIN PAGE */}
          {/* ==================== */}
          <Route path="/admin/login" element={<LoginAdmin />} />
          
          {/* ==================== */}
          {/* ADMIN AREA ROUTES */}
          {/* ==================== */}
          <Route element={<AdminProtectedRoute />}>
            <Route
              path="/admin/dashboard"
              element={
                <PrivateRoute allowedRoles={["admin"]}>
                  <Home />
                </PrivateRoute>
              }
            />
            
            <Route
              path="/admin/events"
              element={
                <PrivateRoute allowedRoles={["admin"]}>
                  <KelasList
                    kelasData={kelasData}
                    setKelasData={setKelasData}
                    kategoriData={kategoriData}
                  />
                </PrivateRoute>
              }
            />
            
            <Route
              path="/admin/tambah-events"
              element={
                <PrivateRoute allowedRoles={["admin"]}>
                  <TambahKelas />
                </PrivateRoute>
              }
            />
            
            <Route
              path="/admin/events/:id"
              element={
                <PrivateRoute allowedRoles={["admin"]}>
                  <DetailKelas />
                </PrivateRoute>
              }
            />
            
            <Route
              path="/admin/edit-events/:id"
              element={
                <PrivateRoute allowedRoles={["admin"]}>
                  <EditKelas />
                </PrivateRoute>
              }
            />
            
            <Route
              path="/admin/kategori/tambah"
              element={
                <PrivateRoute allowedRoles={["admin"]}>
                  <TambahKategori />
                </PrivateRoute>
              }
            />
            
            <Route
              path="/admin/tentang-kami"
              element={
                <PrivateRoute allowedRoles={["admin"]}>
                  <TentangKamiAdmin />
                </PrivateRoute>
              }
            />
            
            <Route
              path="/admin/layanan"
              element={
                <PrivateRoute allowedRoles={["admin"]}>
                  <LayananKamiAdmin />
                </PrivateRoute>
              }
            />
            
            <Route
              path="/admin/partner"
              element={
                <PrivateRoute allowedRoles={["admin"]}>
                  <PartnerAdmin />
                </PrivateRoute>
              }
            />
            
            <Route
              path="/admin/kontak"
              element={
                <PrivateRoute allowedRoles={["admin"]}>
                  <KontakAdmin />
                </PrivateRoute>
              }
            />
            
            <Route
              path="/admin/footer-kontak"
              element={
                <PrivateRoute allowedRoles={["admin"]}>
                  <FooterKontakEditor />
                </PrivateRoute>
              }
            />
          </Route>
          
          {/* ==================== */}
          {/* REDIRECT ROUTES */}
          {/* ==================== */}
          <Route path="/about" element={<Navigate to="/tentang-kami" replace />} />
          <Route path="/contact" element={<Navigate to="/kontak" replace />} />
          <Route path="/services" element={<Navigate to="/layanan" replace />} />
          <Route path="/layanan-kami" element={<Navigate to="/layanan" replace />} />
          <Route path="/barang" element={<Navigate to="/events" replace />} />
          <Route path="/tambah-events" element={<Navigate to="/admin/tambah-events" replace />} />
          <Route path="/admin/tambah" element={<Navigate to="/admin/tambah-events" replace />} />
          <Route path="/partners" element={<Navigate to="/partner" replace />} />
          <Route path="/sponsorship" element={<Navigate to="/partner" replace />} />
          <Route path="/sponsors" element={<Navigate to="/partner" replace />} />
          
          {/* ==================== */}
          {/* ADMIN ROOT REDIRECT */}
          {/* ==================== */}
          <Route path="/admin" element={<Navigate to="/admin/login" replace />} />
          <Route path="/admin-login" element={<Navigate to="/admin/login" replace />} />
          
          {/* ==================== */}
          {/* 404 PAGE */}
          {/* ==================== */}
          <Route path="*" element={
            <div className="min-h-screen flex items-center justify-center p-4 bg-gradient-to-br from-[#0A0E0B] via-[#1A1F16] to-[#0A0E0B]">
              <div className="text-center max-w-2xl">
                <h1 className="text-4xl font-bold text-white mb-4">404</h1>
                <p className="text-[#ABB89D] mb-6">Halaman tidak ditemukan</p>
                <a href="/" className="px-6 py-3 bg-[#D7FE51] text-[#0A0E0B] font-medium rounded-lg hover:bg-[#c5ec47] transition inline-block">
                  Kembali ke Beranda
                </a>
              </div>
            </div>
          } />
        </Routes>
      </Suspense>

      <ToastContainer 
        position="top-center" 
        autoClose={2000} 
        hideProgressBar={false}
        newestOnTop={false}
        closeOnClick
        rtl={false}
        pauseOnFocusLoss
        draggable
        pauseOnHover
        theme="colored"
      />
    </>
  );
}

export default function App() {
  return (
    <ThemeProvider>
      <ErrorBoundary>
        <Router>
          <AppContent />
        </Router>
      </ErrorBoundary>
    </ThemeProvider>
  );
}
