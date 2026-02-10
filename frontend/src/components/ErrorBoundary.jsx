import { Component } from "react";
import logger from "../utils/logger";

class ErrorBoundary extends Component {
  constructor(props) {
    super(props);
    this.state = { hasError: false, error: null };
  }

  static getDerivedStateFromError(error) {
    return { hasError: true, error };
  }

  componentDidCatch(error, errorInfo) {
    logger.error("ErrorBoundary caught:", error, errorInfo);
  }

  handleReload = () => {
    window.location.reload();
  };

  handleGoHome = () => {
    window.location.href = "/";
  };

  render() {
    if (this.state.hasError) {
      return (
        <div className="min-h-screen flex items-center justify-center bg-gradient-to-br from-[#0A0E0B] via-[#1A1F16] to-[#0A0E0B] p-4">
          <div className="text-center max-w-md">
            <div className="w-16 h-16 mx-auto mb-6 rounded-full bg-[#2A3025] flex items-center justify-center">
              <svg className="w-8 h-8 text-[#D7FE51]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
              </svg>
            </div>
            <h1 className="text-2xl font-bold text-white mb-3">Oops! Terjadi Kesalahan</h1>
            <p className="text-[#ABB89D] mb-6 text-sm">
              Halaman mengalami masalah. Silakan coba muat ulang atau kembali ke beranda.
            </p>
            <div className="flex gap-3 justify-center">
              <button
                onClick={this.handleReload}
                className="px-5 py-2.5 rounded-lg font-medium text-sm bg-[#D7FE51] text-[#0A0E0B] hover:bg-[#c5ec47] transition-colors"
              >
                Muat Ulang
              </button>
              <button
                onClick={this.handleGoHome}
                className="px-5 py-2.5 rounded-lg font-medium text-sm bg-[#2A3025] text-white hover:bg-[#363D30] transition-colors border border-[#363D30]"
              >
                Ke Beranda
              </button>
            </div>
          </div>
        </div>
      );
    }

    return this.props.children;
  }
}

export default ErrorBoundary;
