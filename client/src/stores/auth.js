import { reactive } from "vue";
import api from "../services/api";

const state = reactive({
  user: null,
  token: localStorage.getItem("token"),
  isAuthenticated: false,
});

export const auth = {
  state,

  async login(email, password) {
    try {
      const response = await api.login({ email, password });

      if (response.data.success) {
        state.token = response.data.token;
        state.user = response.data.user;
        state.isAuthenticated = true;

        localStorage.setItem("token", response.data.token);

        return { success: true, message: response.data.message };
      }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || "Login failed",
      };
    }
  },

  async register(name, email, password) {
    try {
      const response = await api.register({ name, email, password });

      if (response.data.success) {
        state.token = response.data.token;
        state.user = response.data.user;
        state.isAuthenticated = true;

        localStorage.setItem("token", response.data.token);

        return { success: true, message: response.data.message };
      }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || "Registration failed",
      };
    }
  },

  async logout() {
    try {
      if (state.token) {
        await api.logout();
      }
    } catch (error) {
      console.error("Logout error:", error);
    } finally {
      state.token = null;
      state.user = null;
      state.isAuthenticated = false;
      localStorage.removeItem("token");
    }
  },

  async checkAuth() {
    if (!localStorage.getItem("token")) return false;

    try {
      const response = await api.checkAuth();

      if (response.data.success) {
        state.token = localStorage.getItem("token");
        state.user = response.data.user;
        state.isAuthenticated = true;
        return true;
      }
    } catch (error) {
      this.logout();
    }

    return false;
  },

  init() {
    this.checkAuth();
  },
};
