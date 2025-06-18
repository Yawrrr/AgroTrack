<template>
  <div>
    <h2>Crops Management</h2>
    <div class="mb-3">
      <router-link class="btn btn-primary" to="/crops/new">
        Add New Crop
      </router-link>
    </div>
    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Variety</th>
            <th>Planting Date</th>
            <th>Expected Harvest</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="crop in crops" :key="crop.id">
            <td>{{ crop.name }}</td>
            <td>{{ crop.variety }}</td>
            <td>{{ formatDate(crop.planting_date) }}</td>
            <td>{{ formatDate(crop.expected_harvest_date) }}</td>
            <td>{{ crop.status }}</td>
            <td>
              <router-link
                class="btn btn-sm btn-warning me-2"
                :to="`/crops/${crop.id}/edit`"
              >
                Edit
              </router-link>
              <button
                class="btn btn-sm btn-danger"
                @click="deleteCrop(crop.id)"
              >
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, inject } from "vue";
import { useRoute, useRouter } from "vue-router";
import api from "@/services/api";

const showNotification = inject("showNotification");

const crops = ref([]);
const router = useRouter();

const formatDate = (date) => {
  if (!date) return "";
  return new Date(date).toLocaleDateString();
};

const fetchCrops = async () => {
  try {
    const response = await api.getCrops();
    crops.value = response.data;
  } catch (error) {
    console.error("Error fetching crops:", error);
    showNotification("Failed to fetch crops", "error");
  }
};

const deleteCrop = async (id) => {
  if (confirm("Are you sure you want to delete this crop?")) {
    try {
      await api.deleteCrop(id);
      showNotification("Crop deleted successfully");
      await fetchCrops();
    } catch (error) {
      let msg = "Failed to delete crop";
      if (error.response && error.response.data && error.response.data.error) {
        msg += ": " + error.response.data.error;
      }
      showNotification(msg, "error");
    }
  }
};

onMounted(() => {
  fetchCrops();
});
</script>
