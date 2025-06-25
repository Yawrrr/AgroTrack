<template>
  <div>
    <header class="page-header">
      <h1>{{ isEdit ? "Edit Crop" : "Add New Crop" }}</h1>
      <p>Fill in the details below to manage a crop.</p>
    </header>
    <div class="card">
      <div class="card-body">
        <form @submit.prevent="saveCrop">
          <div class="form-grid">
            <div class="form-group">
              <label for="name">Name</label>
              <input
                id="name"
                type="text"
                class="form-control"
                v-model="cropForm.name"
                required
                placeholder="e.g., Tomato"
              />
            </div>
            <div class="form-group">
              <label for="variety">Variety</label>
              <input
                id="variety"
                type="text"
                class="form-control"
                v-model="cropForm.variety"
                required
                placeholder="e.g., Cherry"
              />
            </div>
            <div class="form-group">
              <label for="planting_date">Planting Date</label>
              <input
                id="planting_date"
                type="date"
                class="form-control"
                v-model="cropForm.planting_date"
                required
              />
            </div>
            <div class="form-group">
              <label for="expected_harvest_date">Expected Harvest Date</label>
              <input
                id="expected_harvest_date"
                type="date"
                class="form-control"
                v-model="cropForm.expected_harvest_date"
                required
              />
            </div>
            <div class="form-group form-group-full">
              <label for="status">Status</label>
              <select
                id="status"
                class="form-control"
                v-model="cropForm.status"
                required
              >
                <option value="planted">Planted</option>
                <option value="growing">Growing</option>
                <option value="ready">Ready for Harvest</option>
                <option value="withered">Withered</option>
              </select>
            </div>
          </div>
          <div class="form-actions">
            <button type="submit" class="btn btn-primary" :disabled="loading">
              <span
                v-if="loading"
                class="spinner-border spinner-border-sm me-2"
              ></span>
              {{ loading ? "Saving..." : "Save Crop" }}
            </button>
            <router-link class="btn btn-secondary" to="/crops">
              Cancel
            </router-link>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, inject } from "vue";
import { useRoute, useRouter } from "vue-router";
import api from "@/services/api";

const showNotification = inject("showNotification");

const route = useRoute();
const router = useRouter();

const cropForm = ref({
  name: "",
  variety: "",
  planting_date: "",
  expected_harvest_date: "",
  status: "planted",
});
const loading = ref(false);
const cropId = route.params.id;
const isEdit = computed(() => !!cropId);

const fetchCrop = async () => {
  if (!isEdit.value) return;
  loading.value = true;
  try {
    const response = await api.getCrop(cropId);
    // The date needs to be in YYYY-MM-DD for the input type="date"
    response.data.planting_date = response.data.planting_date.split("T")[0];
    response.data.expected_harvest_date =
      response.data.expected_harvest_date.split("T")[0];
    cropForm.value = response.data;
  } catch (error) {
    showNotification("Failed to fetch crop data", "error");
    router.push("/crops");
  } finally {
    loading.value = false;
  }
};

const validateForm = () => {
  if (!cropForm.value.name.trim()) {
    showNotification("Crop name is required", "error");
    return false;
  }
  if (!cropForm.value.variety.trim()) {
    showNotification("Crop variety is required", "error");
    return false;
  }
  if (!cropForm.value.planting_date) {
    showNotification("Planting date is required", "error");
    return false;
  }
  if (!cropForm.value.expected_harvest_date) {
    showNotification("Expected harvest date is required", "error");
    return false;
  }
  if (
    new Date(cropForm.value.planting_date) >=
    new Date(cropForm.value.expected_harvest_date)
  ) {
    showNotification("Planting date must be before harvest date", "error");
    return false;
  }
  return true;
};

const saveCrop = async () => {
  if (!validateForm()) return;

  loading.value = true;
  try {
    if (isEdit.value) {
      await api.updateCrop(cropId, cropForm.value);
      showNotification("Crop updated successfully");
    } else {
      await api.createCrop(cropForm.value);
      showNotification("Crop added successfully");
    }
    router.push("/crops");
  } catch (error) {
    showNotification("Failed to save crop", "error");
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchCrop();
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

.card-body {
  padding: 2rem;
}

.form-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1.5rem;
}

@media (min-width: 768px) {
  .form-grid {
    grid-template-columns: 1fr 1fr;
  }
}

.form-group-full {
  grid-column: 1 / -1;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
}

.form-group input,
.form-group select {
  background-color: #f8f9fa;
  border: 1px solid #ced4da;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.form-control {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 1px solid var(--border-color);
  border-radius: 6px;
  font-size: 1rem;
}

.form-control:focus {
  outline: none;
  border-color: var(--primary-color);
  box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.2);
  background-color: #fff;
}

.form-actions {
  margin-top: 2rem;
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
}

.btn-secondary {
  background-color: #6c757d;
  color: white;
}
.btn-secondary:hover {
  background-color: #5a6268;
}
</style>
