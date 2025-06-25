<template>
  <div>
    <header class="page-header">
      <h1>Sensors</h1>
      <p>Live monitoring of your farm's environmental sensors.</p>
    </header>
    <div class="sensors-grid">
      <div v-for="sensor in sensors" :key="sensor.id" class="card sensor-card">
        <div class="card-body">
          <div class="sensor-header">
            <i class="sensor-icon" :class="getSensorIcon(sensor.type)"></i>
            <h5 class="card-title">{{ sensor.name }}</h5>
          </div>
          <div class="sensor-details">
            <p>
              <strong>Type:</strong> {{ sensor.type }}<br />
              <strong>Location:</strong> {{ sensor.location }}
            </p>
            <div class="status-badge" :class="`status-${sensor.status}`">
              {{ sensor.status }}
            </div>
          </div>
          <div class="latest-reading" v-if="sensor.latest_reading">
            <div class="reading-value">
              {{ sensor.latest_reading.value }}
              <span class="reading-unit">{{ sensor.latest_reading.unit }}</span>
            </div>
            <div class="reading-time">
              {{ formatTimeAgo(sensor.latest_reading.created_at) }}
            </div>
          </div>
          <div v-else class="latest-reading-none">No reading available</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, inject } from "vue";
import api from "@/services/api";
import { formatDistanceToNow } from "date-fns";

const showNotification = inject("showNotification");

const sensors = ref([]);

const getSensorIcon = (type) => {
  switch (type.toLowerCase()) {
    case "temperature":
      return "fas fa-thermometer-half";
    case "humidity":
      return "fas fa-tint";
    case "soil moisture":
      return "fas fa-mountain";
    default:
      return "fas fa-microchip";
  }
};

const formatTimeAgo = (date) => {
  if (!date) return "";
  return formatDistanceToNow(new Date(date), { addSuffix: true });
};

const fetchSensors = async () => {
  try {
    const response = await api.getSensors();
    sensors.value = response.data;
  } catch (error) {
    console.error("Error fetching sensors:", error);
    showNotification("Failed to fetch sensors", "error");
  }
};

onMounted(() => {
  fetchSensors();
});
</script>

<style scoped>
.page-header {
  margin-bottom: 2rem;
}

.page-header h1 {
  font-size: 2rem;
  font-weight: 700;
}

.sensors-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.5rem;
}

.sensor-card .card-body {
  display: flex;
  flex-direction: column;
}

.sensor-header {
  display: flex;
  align-items: center;
  margin-bottom: 1rem;
}

.sensor-icon {
  font-size: 1.5rem;
  color: var(--primary-color);
  margin-right: 1rem;
  width: 30px;
  text-align: center;
}

.card-title {
  margin-bottom: 0;
}

.sensor-details {
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

.status-active {
  background-color: #28a745;
}
.status-inactive {
  background-color: #6c757d;
}
.status-error {
  background-color: #dc3545;
}

.latest-reading {
  background-color: var(--secondary-color);
  border-radius: 6px;
  padding: 1rem;
  text-align: center;
}

.reading-value {
  font-size: 2rem;
  font-weight: 700;
  color: var(--primary-color);
}

.reading-unit {
  font-size: 1rem;
  font-weight: 500;
  color: #666;
  margin-left: 0.25rem;
}

.reading-time {
  font-size: 0.8rem;
  color: #777;
  margin-top: 0.25rem;
}

.latest-reading-none {
  background-color: #f8f9fa;
  border-radius: 6px;
  padding: 1rem;
  text-align: center;
  color: #6c757d;
}
</style>
