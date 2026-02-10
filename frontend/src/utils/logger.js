/**
 * Production-safe logger utility.
 * Logs are only output in development mode (import.meta.env.DEV).
 * In production builds, all log calls become no-ops.
 */
const isDev = import.meta.env.DEV;

const logger = {
  log: isDev ? console.log.bind(console) : () => {},
  warn: isDev ? console.warn.bind(console) : () => {},
  error: isDev ? console.error.bind(console) : () => {},
  info: isDev ? console.info.bind(console) : () => {},
  debug: isDev ? console.debug.bind(console) : () => {},
};

export default logger;
