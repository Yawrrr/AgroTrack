<template>
  <div>
    <header class="page-header">
      <h1>Actuators</h1>
      <p>Monitor and control your farm's actuators.</p>
    </header>
    <div class="actuators-grid">
      <div
        v-for="actuator in actuators"
        :key="actuator.id"
        class="card actuator-card"
      >
        <div class="card-body">
          <div class="actuator-header">
            <i
              class="actuator-icon"
              :class="getActuatorIcon(actuator.type)"
            ></i>
            <h5 class="card-title">{{ actuator.name }}</h5>
          </div>
          <div class="actuator-details">
            <p>
              <strong>Type:</strong> {{ actuator.type }}<br />
              <strong>Location:</strong> {{ actuator.location }}
            </p>
            <div class="status-badge" :class="`status-${actuator.status}`">
              {{ actuator.status }}
            </div>
          </div>
          <button
            class="btn"
            :class="actuator.status === 'on' ? 'btn-danger' : 'btn-success'"
            @click="toggleActuator(actuator)"
            :disabled="actuator.status === 'pending'"
          >
            <span
              v-if="actuator.status === 'pending'"
              class="spinner-border spinner-border-sm"
            ></span>
            <span v-else>
              {{ actuator.status === "on" ? "Turn Off" : "Turn On" }}
            </span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, inject } from "vue";
import api from "@/services/api";

const showNotification = inject("showNotification");

const actuators = ref([]);

const getActuatorIcon = (type) => {
  switch (type.toLowerCase()) {
    case "sprinkler":
      return "fas fa-tint";
    case "light":
      return "fas fa-lightbulb";
    case "fan":
      return "fas fa-fan";
    default:
      return "fas fa-cogs";
  }
};

const fetchActuators = async () => {
  try {
    const response = await api.getActuators();
    actuators.value = response.data;
  } catch (error) {
    console.error("Error fetching actuators:", error);
    showNotification("Failed to fetch actuators", "error");
  }
};

const toggleActuator = async (actuator) => {
  try {
    const newStatus = actuator.status === "on" ? "off" : "on";
    const originalStatus = actuator.status;
    actuator.status = "pending";

    await api.toggleActuator(actuator.id, newStatus);

    showNotification(`Actuator command '${newStatus}' sent.`);

    // We optimistically sent the command. We should get a websocket update with the new status.
    // Or we can poll for the new status. Fetching all actuators again is one way.
    await fetchActuators();
  } catch (error) {
    console.error("Error toggling actuator:", error);
    showNotification("Failed to send actuator command", "error");
    // Revert to original status on error
    await fetchActuators();
  }
};

onMounted(() => {
  fetchActuators();
});
</script>

<style scoped>
.page-header {
  margin-bottom: 2rem;
}

.page-header h1 {
  font-size: 2rem;
  font-weight: 700;
  color: var(--text-color);
}

.actuators-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.5rem;
}

.actuator-card .card-body {
  display: flex;
  flex-direction: column;
}

.actuator-header {
  display: flex;
  align-items: center;
  margin-bottom: 1rem;
}

.actuator-icon {
  font-size: 1.5rem;
  color: var(--primary-color);
  margin-right: 1rem;
  width: 30px;
  text-align: center;
}

.card-title {
  margin-bottom: 0;
}

.actuator-details {
  margin-bottom: 1.5rem;
  flex-grow: 1;
}

.status-badge {
  padding: 0.25em 0.6em;
  border-radius: 4px;
  font-size: 0.8rem;
  font-weight: 600;
  text-transform: uppercase;
  display: inline-block;
  color: white;
  margin-top: 0.5rem;
}

.status-on {
  background-color: #28a745;
}

.status-off {
  background-color: #6c757d;
}

.status-pending {
  background-color: #ffc107;
  color: #333;
}

.actuator-card .btn {
  width: 100%;
}
</style>
