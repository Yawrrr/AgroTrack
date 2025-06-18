<template>
  <div>
    <h2>Actuators</h2>
    <div class="row">
      <div
        v-for="actuator in actuators"
        :key="actuator.id"
        class="col-md-4 mb-4"
      >
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">{{ actuator.name }}</h5>
            <p class="card-text">
              Type: {{ actuator.type }}<br />
              Location: {{ actuator.location }}<br />
              Status: {{ actuator.status }}
            </p>
            <button
              class="btn"
              :class="actuator.status === 'on' ? 'btn-danger' : 'btn-success'"
              @click="toggleActuator(actuator)"
              :disabled="actuator.status === 'pending'"
            >
              {{ actuator.status === "on" ? "Turn Off" : "Turn On" }}
              <span
                v-if="actuator.status === 'pending'"
                class="spinner-border spinner-border-sm ms-2"
              ></span>
            </button>
          </div>
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
