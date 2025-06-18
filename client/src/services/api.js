import axios from "axios";

const apiClient = axios.create({
  baseURL: "http://localhost:8000/api",
  headers: {
    "Content-Type": "application/json",
  },
});

apiClient.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem("token");
    if (token) {
      config.headers["Authorization"] = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

export default {
  // Auth
  login(credentials) {
    return apiClient.post("/auth/login", credentials);
  },
  register(userData) {
    return apiClient.post("/auth/register", userData);
  },
  logout() {
    return apiClient.post("/auth/logout");
  },
  checkAuth() {
    return apiClient.get("/auth/me");
  },

  // Crops
  getCrops() {
    return apiClient.get("/crops");
  },
  getCrop(id) {
    return apiClient.get(`/crops/${id}`);
  },
  createCrop(cropData) {
    return apiClient.post("/crops", cropData);
  },
  updateCrop(id, cropData) {
    return apiClient.put(`/crops/${id}`, cropData);
  },
  deleteCrop(id) {
    return apiClient.delete(`/crops/${id}`);
  },

  // Sensors
  getSensors() {
    return apiClient.get("/sensors");
  },

  // Actuators
  getActuators() {
    return apiClient.get("/actuators");
  },
  toggleActuator(id, command) {
    return apiClient.post(`/actuators/${id}/command`, { command });
  },

  // Readings
  getLatestReadings() {
    return apiClient.get("/readings?limit=10");
  },
}; 