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

<script setup>
import { ref, onMounted, inject } from "vue";
import api from "@/services/api";

const showNotification = inject("showNotification");

const crops = ref([]);
const latestReadings = ref([]);

const fetchData = async () => {
  try {
    const [cropsResponse, readingsResponse] = await Promise.all([
      api.getCrops(),
      api.getLatestReadings(),
    ]);
    crops.value = cropsResponse.data;
    latestReadings.value = readingsResponse.data;
  } catch (error) {
    console.error("Error fetching dashboard data:", error);
    showNotification("Failed to fetch dashboard data", "error");
  }
};

onMounted(() => {
  fetchData();
});
</script>
