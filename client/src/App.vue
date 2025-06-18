<template>
  <div>
    <!-- Notification Component -->
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 1050">
      <div
        v-for="notification in notifications"
        :key="notification.id"
        class="toast show"
        role="alert"
        aria-live="assertive"
        aria-atomic="true"
      >
        <div
          class="toast-header"
          :class="{
            'bg-success text-white': notification.type === 'success',
            'bg-danger text-white': notification.type === 'error',
          }"
        >
          <strong class="me-auto">{{
            notification.type === "success" ? "Success" : "Error"
          }}</strong>
          <button
            type="button"
            class="btn-close btn-close-white"
            @click="
              notifications = notifications.filter(
                (n) => n.id !== notification.id
              )
            "
          ></button>
        </div>
        <div class="toast-body">
          {{ notification.message }}
        </div>
      </div>
    </div>

    <!-- Loading Indicators -->
    <div v-if="loading" class="position-fixed top-50 start-50 translate-middle">
      <div class="spinner-border text-success" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>

    <!-- Navigation (only show when authenticated) -->
    <nav
      v-if="auth.state.isAuthenticated"
      class="navbar navbar-expand-lg navbar-dark bg-success"
    >
      <div class="container">
        <router-link class="navbar-brand" to="/">AgroTrack</router-link>
        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navbarNav"
        >
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav me-auto">
            <li class="nav-item">
              <router-link class="nav-link" to="/">Dashboard</router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" to="/crops">Crops</router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" to="/sensors">Sensors</router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" to="/actuators"
                >Actuators</router-link
              >
            </li>
          </ul>
          <ul class="navbar-nav">
            <li class="nav-item dropdown">
              <a
                class="nav-link dropdown-toggle"
                href="#"
                id="navbarDropdown"
                role="button"
                data-bs-toggle="dropdown"
              >
                {{ auth.state.user?.name || "User" }}
              </a>
              <ul class="dropdown-menu">
                <li>
                  <a class="dropdown-item" href="#" @click="logout">Logout</a>
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="container mt-4">
      <router-view></router-view>
    </div>
  </div>
</template>

<script>
import { auth } from "./stores/auth.js";

export default {
  name: "App",
  data() {
    return {
      notifications: [],
      loading: false,
      ws: null,
      auth,
    };
  },
  methods: {
    showNotification(message, type = "success") {
      const id = Date.now();
      this.notifications.push({ id, message, type });
      setTimeout(() => {
        this.notifications = this.notifications.filter((n) => n.id !== id);
      }, 3000);
    },

    async logout() {
      await auth.logout();
      this.showNotification("Logged out successfully", "success");
      this.$router.push("/login");
    },

    setupWebSocket() {
      if (!auth.state.isAuthenticated) return;

      this.ws = new WebSocket("ws://localhost:8080");

      this.ws.onopen = () => {
        console.log("WebSocket connected");
      };

      this.ws.onmessage = (event) => {
        const data = JSON.parse(event.data);
        this.handleWebSocketMessage(data);
      };

      this.ws.onclose = () => {
        console.log("WebSocket disconnected");
        setTimeout(() => this.setupWebSocket(), 5000);
      };

      this.ws.onerror = (error) => {
        console.error("WebSocket error:", error);
      };
    },

    handleWebSocketMessage(data) {
      switch (data.type) {
        case "sensor_reading":
          this.$root.$emit("sensor-reading", data);
          break;
        case "actuator_status":
          this.$root.$emit("actuator-status", data);
          break;
        default:
          console.log("Unknown message type:", data.type);
      }
    },
  },

  async mounted() {
    // Initialize auth
    auth.init();

    // Setup WebSocket if authenticated
    if (auth.state.isAuthenticated) {
      this.setupWebSocket();
    }

    // Watch for auth state changes
    this.$watch(
      () => auth.state.isAuthenticated,
      (newVal) => {
        if (newVal) {
          this.setupWebSocket();
        } else if (this.ws) {
          this.ws.close();
          this.ws = null;
        }
      }
    );
  },

  beforeUnmount() {
    if (this.ws) {
      this.ws.close();
    }
  },
};
</script>
