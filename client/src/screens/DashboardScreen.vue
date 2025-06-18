<template>
  <div>
    <h2>Dashboard</h2>
    <div class="row">
      <div class="col-md-6">
        <div class="card mb-4">
          <div class="card-header">Latest Sensor Readings</div>
          <div class="card-body">
            <div
              v-for="reading in latestReadings"
              :key="reading.id"
              class="mb-2"
            >
              <strong>{{ reading.sensor_name }}:</strong> {{ reading.value }}
              {{ reading.unit }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card mb-4">
          <div class="card-header">Crop Status</div>
          <div class="card-body">
            <div v-for="crop in crops" :key="crop.id" class="mb-2">
              <strong>{{ crop.name }}:</strong> {{ crop.status }}
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
  name: "DashboardScreen",
  data() {
    return {
      crops: [],
      latestReadings: [],
    };
  },
  methods: {
    async fetchData() {
      try {
        const [cropsResponse, readingsResponse] = await Promise.all([
          axios.get("http://localhost:8000/api/crops"),
          axios.get("http://localhost:8000/api/readings?limit=10"),
        ]);
        this.crops = cropsResponse.data;
        this.latestReadings = readingsResponse.data;
      } catch (error) {
        console.error("Error fetching dashboard data:", error);
        this.$root.showNotification("Failed to fetch dashboard data", "error");
      }
    },
  },
  mounted() {
    this.fetchData();
  },
};
</script>
