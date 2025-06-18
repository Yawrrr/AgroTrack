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

    <!-- Add/Edit Crop Modal -->
    <div
      class="modal fade"
      id="cropModal"
      tabindex="-1"
      v-if="showAddCropModal"
    >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              {{ editingCrop ? "Edit Crop" : "Add New Crop" }}
            </h5>
            <button
              type="button"
              class="btn-close"
              @click="showAddCropModal = false"
            ></button>
          </div>
          <div class="modal-body">
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
              <button type="submit" class="btn btn-primary">Save</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "CropsScreen",
  data() {
    return {
      crops: [],
      showAddCropModal: false,
      editingCrop: null,
      cropForm: {
        name: "",
        variety: "",
        planting_date: "",
        expected_harvest_date: "",
        status: "planted",
      },
    };
  },
  methods: {
    formatDate(date) {
      return new Date(date).toLocaleDateString();
    },
    async fetchCrops() {
      try {
        const response = await axios.get("http://localhost:8000/api/crops");
        this.crops = response.data;
      } catch (error) {
        console.error("Error fetching crops:", error);
        this.$root.showNotification("Failed to fetch crops", "error");
      }
    },
    validateCropForm() {
      if (!this.cropForm.name.trim()) {
        this.$root.showNotification("Crop name is required", "error");
        return false;
      }
      if (!this.cropForm.variety.trim()) {
        this.$root.showNotification("Crop variety is required", "error");
        return false;
      }
      if (!this.cropForm.planting_date) {
        this.$root.showNotification("Planting date is required", "error");
        return false;
      }
      if (!this.cropForm.expected_harvest_date) {
        this.$root.showNotification(
          "Expected harvest date is required",
          "error"
        );
        return false;
      }
      if (
        new Date(this.cropForm.planting_date) >
        new Date(this.cropForm.expected_harvest_date)
      ) {
        this.$root.showNotification(
          "Planting date cannot be after harvest date",
          "error"
        );
        return false;
      }
      return true;
    },
    async saveCrop() {
      if (!this.validateCropForm()) return;

      try {
        if (this.editingCrop) {
          await axios.put(
            `http://localhost:8000/api/crops/${this.editingCrop.id}`,
            this.cropForm
          );
          this.$root.showNotification("Crop updated successfully");
        } else {
          await axios.post("http://localhost:8000/api/crops", this.cropForm);
          this.$root.showNotification("Crop added successfully");
        }
        this.showAddCropModal = false;
        this.resetCropForm();
        await this.fetchCrops();
      } catch (error) {
        console.error("Error saving crop:", error);
        this.$root.showNotification("Failed to save crop", "error");
      }
    },
    editCrop(crop) {
      this.editingCrop = crop;
      this.cropForm = { ...crop };
      this.showAddCropModal = true;
    },
    async deleteCrop(id) {
      if (confirm("Are you sure you want to delete this crop?")) {
        try {
          await axios.delete(`http://localhost:8000/api/crops/${id}`);
          this.$root.showNotification("Crop deleted successfully");
          await this.fetchCrops();
        } catch (error) {
          let msg = "Failed to delete crop";
          if (
            error.response &&
            error.response.data &&
            error.response.data.error
          ) {
            msg += ": " + error.response.data.error;
          }
          this.$root.showNotification(msg, "error");
        }
      }
    },
    resetCropForm() {
      this.editingCrop = null;
      this.cropForm = {
        name: "",
        variety: "",
        planting_date: "",
        expected_harvest_date: "",
        status: "planted",
      };
    },
  },
  mounted() {
    this.fetchCrops();
  },
};
</script>
