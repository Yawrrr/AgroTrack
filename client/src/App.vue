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
            @click="removeNotification(notification.id)"
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
                  <a class="dropdown-item" href="#" @click="handleLogout"
                    >Logout</a
                  >
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

<script setup>
import { ref, onMounted, onBeforeUnmount, watch, provide } from "vue";
import { useRouter } from "vue-router";
import { auth } from "./stores/auth.js";

const notifications = ref([]);
const router = useRouter();
let ws = null;

const showNotification = (message, type = "success") => {
  const id = Date.now();
  notifications.value.push({ id, message, type });
  setTimeout(() => {
    removeNotification(id);
  }, 5000);
};

const removeNotification = (id) => {
  notifications.value = notifications.value.filter((n) => n.id !== id);
};

provide("showNotification", showNotification);

const handleLogout = async () => {
  await auth.logout();
  showNotification("Logged out successfully", "success");
  router.push("/login");
};

const setupWebSocket = () => {
  if (!auth.state.isAuthenticated || ws) return;

  ws = new WebSocket("ws://localhost:8080");

  ws.onopen = () => console.log("WebSocket connected");

  ws.onmessage = (event) => {
    const data = JSON.parse(event.data);
    // Here you would use a more robust event bus or state management to pass
    // the message to the relevant components.
    // For now, we just log it.
    console.log("WebSocket message received:", data);
    if (data.type === "actuator_status") {
      showNotification(
        `Actuator ${data.actuator_id} status updated to ${data.status}`
      );
    } else if (data.type === "sensor_reading") {
      showNotification(
        `New reading for sensor ${data.sensor_id}: ${data.value} ${data.unit}`
      );
    }
  };

  ws.onclose = () => {
    console.log("WebSocket disconnected");
    ws = null;
    // Optional: attempt to reconnect
    setTimeout(() => setupWebSocket(), 5000);
  };

  ws.onerror = (error) => {
    console.error("WebSocket error:", error);
    ws = null;
  };
};

onMounted(() => {
  auth.init();
});

watch(
  () => auth.state.isAuthenticated,
  (isAuthenticated) => {
    if (isAuthenticated) {
      setupWebSocket();
    } else if (ws) {
      ws.close();
      ws = null;
    }
  },
  { immediate: true }
);

onBeforeUnmount(() => {
  if (ws) {
    ws.close();
  }
});
</script>
