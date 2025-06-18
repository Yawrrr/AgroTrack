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

<script>
import axios from "axios";

export default {
  name: "ActuatorsScreen",
  data() {
    return {
      actuators: [],
    };
  },
  methods: {
    async fetchActuators() {
      try {
        const response = await axios.get("http://localhost:8000/api/actuators");
        this.actuators = response.data;
      } catch (error) {
        console.error("Error fetching actuators:", error);
        this.$root.showNotification("Failed to fetch actuators", "error");
      }
    },
    async toggleActuator(actuator) {
      try {
        const newStatus = actuator.status === "on" ? "off" : "on";
        actuator.status = "pending";

        await axios.post(
          `http://localhost:8000/api/actuators/${actuator.id}/command`,
          {
            command: newStatus,
          }
        );
        this.$root.showNotification(`Actuator turned ${newStatus}`);
        await this.fetchActuators();
      } catch (error) {
        console.error("Error toggling actuator:", error);
        this.$root.showNotification("Failed to control actuator", "error");
        await this.fetchActuators();
      }
    },
  },
  mounted() {
    this.fetchActuators();
  },
};
</script>
