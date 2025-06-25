<template>
  <div>
    <header class="page-header">
      <h1>Welcome back, {{ auth.state.user?.name || "User" }}!</h1>
      <p>Here's a snapshot of your farm's status.</p>
    </header>

    <!-- Stat Cards -->
    <div class="stats-grid">
      <div class="card stat-card">
        <div class="stat-icon" style="background-color: #22c55e20">
          <i class="fas fa-leaf" style="color: #22c55e"></i>
        </div>
        <div class="stat-info">
          <h4>Total Crops</h4>
          <p>{{ crops.length }}</p>
        </div>
      </div>
      <div class="card stat-card">
        <div class="stat-icon" style="background-color: #ef444420">
          <i class="fas fa-robot" style="color: #ef4444"></i>
        </div>
        <div class="stat-info">
          <h4>Active Actuators</h4>
          <p>{{ activeActuatorsCount }}</p>
        </div>
      </div>
      <div class="card stat-card">
        <div class="stat-icon" style="background-color: #3b82f620">
          <i class="fas fa-microchip" style="color: #3b82f6"></i>
        </div>
        <div class="stat-info">
          <h4>Sensors Online</h4>
          <p>{{ onlineSensorsCount }}</p>
        </div>
      </div>
    </div>

    <!-- Dashboard Panels -->
    <div class="dashboard-grid">
      <div class="card">
        <div class="card-header">
          <h3>
            <i class="fas fa-thermometer-half"></i> Latest Sensor Readings
          </h3>
        </div>
        <ul class="list-group list-group-flush">
          <li v-if="latestReadings.length === 0" class="list-group-item">
            No recent readings.
          </li>
          <li
            v-for="reading in latestReadings"
            :key="reading.id"
            class="list-group-item"
          >
            <strong>{{ reading.sensor_name }}:</strong>
            <span class="float-end reading-value"
              >{{ reading.value }} {{ reading.unit }}</span
            >
          </li>
        </ul>
      </div>
      <div class="card">
        <div class="card-header">
          <h3><i class="fas fa-seedling"></i> Crop Status</h3>
        </div>
        <ul class="list-group list-group-flush">
          <li v-if="crops.length === 0" class="list-group-item">
            No crops being tracked.
          </li>
          <li v-for="crop in crops" :key="crop.id" class="list-group-item">
            <strong>{{ crop.name }}</strong> ({{ crop.variety }})
            <span
              class="status-badge float-end"
              :class="`status-${crop.status.toLowerCase()}`"
              >{{ crop.status }}</span
            >
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, inject, computed } from "vue";
import { auth } from "@/stores/auth";
import api from "@/services/api";

const showNotification = inject("showNotification");

const crops = ref([]);
const latestReadings = ref([]);
const actuators = ref([]);
const sensors = ref([]);

const activeActuatorsCount = computed(
  () => actuators.value.filter((a) => a.status === "on").length
);
const onlineSensorsCount = computed(
  () => sensors.value.filter((s) => s.status === "active").length
);

const fetchData = async () => {
  try {
    const [
      cropsResponse,
      readingsResponse,
      actuatorsResponse,
      sensorsResponse,
    ] = await Promise.all([
      api.getCrops(),
      api.getLatestReadings(),
      api.getActuators(),
      api.getSensors(),
    ]);
    crops.value = cropsResponse.data;
    latestReadings.value = readingsResponse.data;
    actuators.value = actuatorsResponse.data;
    sensors.value = sensorsResponse.data;
  } catch (error) {
    console.error("Error fetching dashboard data:", error);
    showNotification("Failed to fetch dashboard data", "error");
  }
};

onMounted(() => {
  fetchData();
});
</script>

<style scoped>
.page-header h1 {
  color: var(--primary-color);
  font-weight: 700;
  font-size: 2.25rem;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
  margin: 2rem 0;
}

.stat-card {
  display: flex;
  flex-direction: row;
  align-items: center;
  padding: 1.5rem;
}

.stat-icon {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 1.5rem;
}

.stat-icon i {
  font-size: 1.75rem;
}

.stat-info h4 {
  font-size: 1rem;
  color: #666;
  margin: 0;
}

.stat-info p {
  font-size: 2rem;
  font-weight: 700;
  margin: 0;
}

.dashboard-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
}

@media (max-width: 992px) {
  .dashboard-grid {
    grid-template-columns: 1fr;
  }
}

.card-header {
  background-color: #f8f9fa;
  padding: 1rem 1.25rem;
  font-size: 1.1rem;
  font-weight: 600;
  border-bottom: 1px solid var(--border-color);
}
.card-header i {
  color: var(--primary-color);
  margin-right: 0.75rem;
}

.list-group-item {
  padding: 1rem 1.25rem;
}

.reading-value {
  font-weight: 600;
  color: var(--primary-color);
}

.status-badge {
  padding: 0.3em 0.7em;
  border-radius: 12px;
  font-size: 0.8rem;
  font-weight: 500;
  text-transform: capitalize;
  color: white;
}

/* Duplicating status colors from CropsScreen for now */
.status-planted {
  background-color: #17a2b8;
}
.status-growing {
  background-color: #28a745;
}
.status-ready {
  background-color: #fdba74;
  color: #333;
} /* Orange for Ready */
.status-harvested {
  background-color: #ffc107;
  color: #333;
}
.status-withered {
  background-color: #6c757d;
}
</style>
