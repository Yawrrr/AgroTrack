// API Configuration
const API_BASE_URL = "http://localhost:8000/api";
const WS_URL = "ws://localhost:8080";

// Vue Application
const app = Vue.createApp({
  data() {
    return {
      currentView: "dashboard",
      crops: [],
      sensors: [],
      actuators: [],
      latestReadings: [],
      showAddCropModal: false,
      editingCrop: null,
      cropForm: {
        name: "",
        variety: "",
        planting_date: "",
        expected_harvest_date: "",
        status: "planted",
      },
      ws: null,
      loading: {
        crops: false,
        sensors: false,
        actuators: false,
        readings: false,
      },
      notifications: [],
    };
  },

  methods: {
    // Notification System
    showNotification(message, type = "success") {
      const id = Date.now();
      this.notifications.push({ id, message, type });
      setTimeout(() => {
        this.notifications = this.notifications.filter((n) => n.id !== id);
      }, 3000);
    },

    // API Calls
    async fetchCrops() {
      this.loading.crops = true;
      try {
        const response = await $.get(`${API_BASE_URL}/crops`);
        this.crops = response;
      } catch (error) {
        console.error("Error fetching crops:", error);
        this.showNotification("Failed to fetch crops", "error");
      } finally {
        this.loading.crops = false;
      }
    },

    async fetchSensors() {
      this.loading.sensors = true;
      try {
        const response = await $.get(`${API_BASE_URL}/sensors`);
        this.sensors = response;
      } catch (error) {
        console.error("Error fetching sensors:", error);
        this.showNotification("Failed to fetch sensors", "error");
      } finally {
        this.loading.sensors = false;
      }
    },

    async fetchActuators() {
      this.loading.actuators = true;
      try {
        const response = await $.get(`${API_BASE_URL}/actuators`);
        this.actuators = response;
      } catch (error) {
        console.error("Error fetching actuators:", error);
        this.showNotification("Failed to fetch actuators", "error");
      } finally {
        this.loading.actuators = false;
      }
    },

    async fetchLatestReadings() {
      this.loading.readings = true;
      try {
        const response = await $.get(`${API_BASE_URL}/readings?limit=10`);
        this.latestReadings = response;
      } catch (error) {
        console.error("Error fetching readings:", error);
        this.showNotification("Failed to fetch readings", "error");
      } finally {
        this.loading.readings = false;
      }
    },

    // Crop Management
    validateCropForm() {
      if (!this.cropForm.name.trim()) {
        this.showNotification("Crop name is required", "error");
        return false;
      }
      if (!this.cropForm.variety.trim()) {
        this.showNotification("Crop variety is required", "error");
        return false;
      }
      if (!this.cropForm.planting_date) {
        this.showNotification("Planting date is required", "error");
        return false;
      }
      if (!this.cropForm.expected_harvest_date) {
        this.showNotification("Expected harvest date is required", "error");
        return false;
      }
      if (
        new Date(this.cropForm.planting_date) >
        new Date(this.cropForm.expected_harvest_date)
      ) {
        this.showNotification(
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
          await $.ajax({
            url: `${API_BASE_URL}/crops/${this.editingCrop.id}`,
            method: "PUT",
            contentType: "application/json",
            data: JSON.stringify(this.cropForm),
          });
          this.showNotification("Crop updated successfully");
        } else {
          await $.ajax({
            url: `${API_BASE_URL}/crops`,
            method: "POST",
            contentType: "application/json",
            data: JSON.stringify(this.cropForm),
          });
          this.showNotification("Crop added successfully");
        }
        this.showAddCropModal = false;
        this.resetCropForm();
        await this.fetchCrops();
      } catch (error) {
        console.error("Error saving crop:", error);
        this.showNotification("Failed to save crop", "error");
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
          await $.ajax({
            url: `${API_BASE_URL}/crops/${id}`,
            method: "DELETE",
          });
          this.showNotification("Crop deleted successfully");
          await this.fetchCrops();
        } catch (error) {
          console.error("Error deleting crop:", error);
          this.showNotification("Failed to delete crop", "error");
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

    // Actuator Control
    async toggleActuator(actuator) {
      try {
        const newStatus = actuator.status === "on" ? "off" : "on";
        actuator.status = "pending"; // Show loading state

        await $.ajax({
          url: `${API_BASE_URL}/actuators/${actuator.id}/command`,
          method: "POST",
          contentType: "application/json",
          data: JSON.stringify({ command: newStatus }),
        });

        this.showNotification(`Actuator turned ${newStatus}`);
        await this.fetchActuators();
      } catch (error) {
        console.error("Error toggling actuator:", error);
        this.showNotification("Failed to control actuator", "error");
        await this.fetchActuators(); // Refresh to get correct status
      }
    },

    // Utility Methods
    formatDate(date) {
      return new Date(date).toLocaleDateString();
    },

    // WebSocket Setup
    setupWebSocket() {
      this.ws = new WebSocket(WS_URL);

      this.ws.onopen = () => {
        console.log("WebSocket connected");
      };

      this.ws.onmessage = (event) => {
        const data = JSON.parse(event.data);
        this.handleWebSocketMessage(data);
      };

      this.ws.onclose = () => {
        console.log("WebSocket disconnected");
        // Attempt to reconnect after 5 seconds
        setTimeout(() => this.setupWebSocket(), 5000);
      };

      this.ws.onerror = (error) => {
        console.error("WebSocket error:", error);
      };
    },

    handleWebSocketMessage(data) {
      switch (data.type) {
        case "sensor_reading":
          this.updateSensorReading(data);
          break;
        case "actuator_status":
          this.updateActuatorStatus(data);
          break;
        default:
          console.log("Unknown message type:", data.type);
      }
    },

    updateSensorReading(data) {
      // Update sensor reading in the UI
      const sensor = this.sensors.find((s) => s.id === data.sensor_id);
      if (sensor) {
        sensor.latest_reading = {
          value: data.value,
          unit: data.unit,
        };
      }
      // Update latest readings list
      this.latestReadings.unshift({
        sensor_name: sensor?.name || "Unknown Sensor",
        value: data.value,
        unit: data.unit,
      });
      // Keep only the latest 10 readings
      if (this.latestReadings.length > 10) {
        this.latestReadings.pop();
      }
    },

    updateActuatorStatus(data) {
      const actuator = this.actuators.find((a) => a.id === data.actuator_id);
      if (actuator) {
        actuator.status = data.status;
      }
    },
  },

  mounted() {
    // Initial data fetch
    this.fetchCrops();
    this.fetchSensors();
    this.fetchActuators();
    this.fetchLatestReadings();

    // Setup WebSocket connection
    this.setupWebSocket();

    // Refresh data every 30 seconds
    setInterval(() => {
      this.fetchLatestReadings();
    }, 30000);
  },

  beforeUnmount() {
    // Clean up WebSocket connection
    if (this.ws) {
      this.ws.close();
    }
  },
});

// Mount the Vue application
app.mount("#app");
