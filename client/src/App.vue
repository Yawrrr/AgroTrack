<template>
  <div id="app-layout">
    <!-- Notification Component -->
    <div class="notification-container">
      <div
        v-for="notification in notifications"
        :key="notification.id"
        class="toast show"
        :class="`toast-${notification.type}`"
        role="alert"
        aria-live="assertive"
        aria-atomic="true"
      >
        <div class="toast-header">
          <strong class="me-auto">{{
            notification.type === "success" ? "Success" : "Error"
          }}</strong>
          <button
            type="button"
            class="btn-close"
            @click="removeNotification(notification.id)"
          ></button>
        </div>
        <div class="toast-body">
          {{ notification.message }}
        </div>
      </div>
    </div>

    <Sidebar v-if="auth.state.isAuthenticated" />

    <main class="main-content">
      <router-view></router-view>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, watch, provide } from "vue";
import { useRouter } from "vue-router";
import { auth } from "./stores/auth.js";
import Sidebar from "./components/Sidebar.vue";

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

const setupWebSocket = () => {
  if (!auth.state.isAuthenticated || ws) return;

  ws = new WebSocket("ws://localhost:8080");

  ws.onopen = () => console.log("WebSocket connected");

  ws.onmessage = (event) => {
    const data = JSON.parse(event.data);
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

<style>
/* Scoped styles are not used here to allow for global overrides if necessary,
   but for a larger app, you might structure this differently. */

#app-layout {
  display: flex;
  min-height: 100vh;
}

.main-content {
  flex-grow: 1;
  padding: 2rem;
  background-color: var(--secondary-color);
}

.notification-container {
  position: fixed;
  top: 1rem;
  right: 1rem;
  z-index: 1050;
  width: 350px;
}

.toast {
  border: none;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  border-radius: 8px;
}

.toast-header {
  border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.toast-success .toast-header {
  background-color: #d4edda;
  color: #155724;
}

.toast-error .toast-header {
  background-color: #f8d7da;
  color: #721c24;
}

.btn-close {
  background: none;
  border: none;
  font-size: 1.2rem;
  line-height: 1;
}
</style>
