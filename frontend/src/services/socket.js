import logger from "../utils/logger";
// src/services/socket.js
import { io } from 'socket.io-client';
import { API_BASE_URL } from '../config/api';

class SocketService {
  constructor() {
    this.socket = null;
    this.isConnected = false;
  }

  connect() {
    if (!this.socket) {
      this.socket = io(API_BASE_URL, {
        transports: ['websocket', 'polling']
      });

      this.socket.on('connect', () => {
        logger.log('Connected to server');
        this.isConnected = true;
      });

      this.socket.on('disconnect', () => {
        logger.log('Disconnected from server');
        this.isConnected = false;
      });
    }
  }

  disconnect() {
    if (this.socket) {
      this.socket.disconnect();
      this.socket = null;
      this.isConnected = false;
    }
  }

  on(event, callback) {
    if (this.socket) {
      this.socket.on(event, callback);
    }
  }

  off(event, callback) {
    if (this.socket) {
      this.socket.off(event, callback);
    }
  }
}

export default new SocketService();
