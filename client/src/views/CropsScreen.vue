<template>
  <div>
    <header class="page-header">
      <h1>Crops Management</h1>
      <p>Oversee all your crops in one place.</p>
    </header>
    <div class="actions-bar">
      <router-link class="btn btn-primary" to="/crops/new">
        <i class="fas fa-plus"></i> Add New Crop
      </router-link>
    </div>
    <div class="card">
      <div class="table-responsive">
        <table class="table table-hover">
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
            <tr v-if="crops.length === 0">
              <td colspan="6" class="text-center">No crops found.</td>
            </tr>
            <tr v-for="crop in crops" :key="crop.id">
              <td>{{ crop.name }}</td>
              <td>{{ crop.variety }}</td>
              <td>{{ formatDate(crop.planting_date) }}</td>
              <td>{{ formatDate(crop.expected_harvest_date) }}</td>
              <td>
                <span
                  class="status-badge"
                  :class="`status-${crop.status.toLowerCase()}`"
                  >{{ crop.status }}</span
                >
              </td>
              <td>
                <router-link
                  class="btn btn-sm btn-outline-primary me-2"
                  :to="`/crops/${crop.id}/edit`"
                >
                  <i class="fas fa-edit"></i> Edit
                </router-link>
                <button
                  class="btn btn-sm btn-outline-danger"
                  @click="deleteCrop(crop.id)"
                >
                  <i class="fas fa-trash"></i> Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
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

<style scoped>
.page-header {
  margin-bottom: 2rem;
}

.page-header h1 {
  font-size: 2rem;
  font-weight: 700;
}

.actions-bar {
  margin-bottom: 1.5rem;
  text-align: right;
}

.actions-bar .btn i {
  margin-right: 0.5rem;
}

.card {
  padding: 0;
}

.table {
  width: 100%;
  border-collapse: collapse;
  margin: 0;
}

.table th,
.table td {
  padding: 1rem 1.25rem;
  vertical-align: middle;
  text-align: left;
}

.table thead th {
  background-color: #f8f9fa;
  border-bottom: 2px solid var(--border-color);
  font-weight: 600;
}

.table tbody tr:not(:last-child) {
  border-bottom: 1px solid var(--border-color);
}

.table-hover tbody tr:hover {
  background-color: var(--secondary-color);
}

.btn-sm {
  padding: 0.35rem 0.75rem;
  font-size: 0.875rem;
}

.btn-sm i {
  margin-right: 0.3rem;
}

.btn-outline-primary {
  color: var(--primary-color);
  border-color: var(--primary-color);
}

.btn-outline-primary:hover {
  background-color: var(--primary-color);
  color: white;
}

.btn-outline-danger {
  color: #dc3545;
  border-color: #dc3545;
}

.btn-outline-danger:hover {
  background-color: #dc3545;
  color: white;
}

.status-badge {
  padding: 0.3em 0.7em;
  border-radius: 12px;
  font-size: 0.8rem;
  font-weight: 500;
  text-transform: capitalize;
  display: inline-block;
  color: white;
}

.status-planted {
  background-color: #17a2b8;
}
.status-growing {
  background-color: #28a745;
}
.status-ready {
  background-color: #fdba74;
  color: #333;
}
.status-harvested {
  background-color: #ffc107;
  color: #333;
}
.status-withered {
  background-color: #6c757d;
}
</style>
