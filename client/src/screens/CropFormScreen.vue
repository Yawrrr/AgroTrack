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
      <button type="submit" class="btn btn-primary">Save</button>
      <router-link class="btn btn-secondary ms-2" to="/crops"
        >Cancel</router-link
      >
    </form>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "CropFormScreen",
  data() {
    return {
      cropForm: {
        name: "",
        variety: "",
        planting_date: "",
        expected_harvest_date: "",
        status: "planted",
      },
      isEdit: false,
      loading: false,
    };
  },
  async created() {
    const cropId = this.$route.params.id;
    if (cropId) {
      this.isEdit = true;
      this.loading = true;
      try {
        const response = await axios.get(
          `http://localhost:8000/api/crops/${cropId}`
        );
        this.cropForm = {
          name: response.data.name,
          variety: response.data.variety,
          planting_date: response.data.planting_date,
          expected_harvest_date: response.data.expected_harvest_date,
          status: response.data.status,
        };
      } catch (error) {
        this.$root.showNotification("Failed to fetch crop", "error");
        this.$router.push("/crops");
      } finally {
        this.loading = false;
      }
    }
  },
  methods: {
    validateForm() {
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
        new Date(this.cropForm.planting_date) >=
        new Date(this.cropForm.expected_harvest_date)
      ) {
        this.$root.showNotification(
          "Planting date must be before harvest date",
          "error"
        );
        return false;
      }
      return true;
    },

    async saveCrop() {
      if (!this.validateForm()) return;

      this.loading = true;
      try {
        if (this.isEdit) {
          await axios.put(
            `http://localhost:8000/api/crops/${this.$route.params.id}`,
            this.cropForm,
            { headers: { "Content-Type": "application/json" } }
          );
          this.$root.showNotification("Crop updated successfully");
        } else {
          await axios.post("http://localhost:8000/api/crops", this.cropForm, {
            headers: { "Content-Type": "application/json" },
          });
          this.$root.showNotification("Crop added successfully");
        }
        this.$router.push("/crops");
      } catch (error) {
        let msg = "Failed to save crop";
        if (
          error.response &&
          error.response.data &&
          error.response.data.error
        ) {
          msg += ": " + error.response.data.error;
        }
        this.$root.showNotification(msg, "error");
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>
