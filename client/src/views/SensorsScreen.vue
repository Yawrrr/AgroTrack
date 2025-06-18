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

<script setup>
import { ref, onMounted, inject } from "vue";
import api from "@/services/api";

const showNotification = inject("showNotification");

const sensors = ref([]);

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
