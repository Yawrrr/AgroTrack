<template>
  <div>
    <h2>Sensors</h2>
    <div class="row">
      <div v-for="sensor in sensors" :key="sensor.id" class="col-md-4 mb-4">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">{{ sensor.name }}</h5>
            <p class="card-text">
              Type: {{ sensor.type }}<br />
              Location: {{ sensor.location }}<br />
              Status: {{ sensor.status }}
            </p>
            <div v-if="sensor.latest_reading">
              Latest Reading: {{ sensor.latest_reading.value }}
              {{ sensor.latest_reading.unit }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "SensorsScreen",
  data() {
    return {
      sensors: [],
    };
  },
  methods: {
    async fetchSensors() {
      try {
        const response = await axios.get("http://localhost:8000/api/sensors");
        this.sensors = response.data;
      } catch (error) {
        console.error("Error fetching sensors:", error);
        this.$root.showNotification("Failed to fetch sensors", "error");
      }
    },
  },
  mounted() {
    this.fetchSensors();
  },
};
</script>
