<template>
  <div>
    <h2>{{ isEdit ? "Edit Crop" : "Add New Crop" }}</h2>
    <form @submit.prevent="saveCrop">
      <div class="mb-3">
        <label class="form-label">Name</label>
        <input
          type="text"
          class="form-control"
          v-model="cropForm.name"
          required
        />
      </div>
      <div class="mb-3">
        <label class="form-label">Variety</label>
        <input
          type="text"
          class="form-control"
          v-model="cropForm.variety"
          required
        />
      </div>
      <div class="mb-3">
        <label class="form-label">Planting Date</label>
        <input
          type="date"
          class="form-control"
          v-model="cropForm.planting_date"
          required
        />
      </div>
      <div class="mb-3">
        <label class="form-label">Expected Harvest Date</label>
        <input
          type="date"
          class="form-control"
          v-model="cropForm.expected_harvest_date"
          required
        />
      </div>
      <div class="mb-3">
        <label class="form-label">Status</label>
        <select class="form-control" v-model="cropForm.status" required>
          <option value="planted">Planted</option>
          <option value="growing">Growing</option>
          <option value="ready">Ready</option>
          <option value="harvested">Harvested</option>
        </select>
      </div>
      <button type="submit" class="btn btn-primary" :disabled="loading">
        {{ loading ? "Saving..." : "Save" }}
      </button>
      <router-link class="btn btn-secondary ms-2" to="/crops">
        Cancel
      </router-link>
    </form>
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
