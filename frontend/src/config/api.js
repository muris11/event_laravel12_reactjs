const envBaseUrl = import.meta.env.VITE_API_BASE_URL?.trim();

if (!envBaseUrl && import.meta.env.PROD) {
  throw new Error("VITE_API_BASE_URL is required for production build/runtime");
}

const rawBaseUrl = envBaseUrl || "http://localhost:8000";

export const API_BASE_URL = rawBaseUrl.replace(/\/+$/, "");

export const apiUrl = (path = "") => {
  if (!path) return API_BASE_URL;
  return path.startsWith("/") ? `${API_BASE_URL}${path}` : `${API_BASE_URL}/${path}`;
};
