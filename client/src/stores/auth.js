import { reactive } from "vue";
import axios from "axios";

const state = reactive({
  user: null,
  token: localStorage.getItem("token"),
  isAuthenticated: false,
});

export const auth = {
  state,

  async login(email, password) {
    try {
      const response = await axios.post(
        "http://localhost:8000/api/auth/login",
        {
          email,
          password,
        },
        {
          headers: { "Content-Type": "application/json" },
        }
      );

      if (response.data.success) {
        state.token = response.data.token;
        state.user = response.data.user;
        state.isAuthenticated = true;

        localStorage.setItem("token", response.data.token);
        axios.defaults.headers.common[
          "Authorization"
        ] = `Bearer ${response.data.token}`;

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
      const response = await axios.post(
        "http://localhost:8000/api/auth/register",
        {
          name,
          email,
          password,
        },
        {
          headers: { "Content-Type": "application/json" },
        }
      );

      if (response.data.success) {
        state.token = response.data.token;
        state.user = response.data.user;
        state.isAuthenticated = true;

        localStorage.setItem("token", response.data.token);
        axios.defaults.headers.common[
          "Authorization"
        ] = `Bearer ${response.data.token}`;

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
        await axios.post(
          "http://localhost:8000/api/auth/logout",
          {},
          {
            headers: {
              "Content-Type": "application/json",
              Authorization: `Bearer ${state.token}`,
            },
          }
        );
      }
    } catch (error) {
      console.error("Logout error:", error);
    } finally {
      state.token = null;
      state.user = null;
      state.isAuthenticated = false;

      localStorage.removeItem("token");
      delete axios.defaults.headers.common["Authorization"];
    }
  },

  async checkAuth() {
    const token = localStorage.getItem("token");
    if (!token) return false;

    try {
      const response = await axios.get("http://localhost:8000/api/auth/me", {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

      if (response.data.success) {
        state.token = token;
        state.user = response.data.user;
        state.isAuthenticated = true;
        axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
        return true;
      }
    } catch (error) {
      localStorage.removeItem("token");
    }

    return false;
  },

  init() {
    const token = localStorage.getItem("token");
    if (token) {
      state.token = token;
      axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
      this.checkAuth();
    }
  },
};
