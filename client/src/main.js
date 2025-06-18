import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";
import { auth } from "./stores/auth.js";
import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap/dist/js/bootstrap.bundle.min.js";

const app = createApp(App);

// Make the notification system available globally
app.config.globalProperties.showNotification = function (
  message,
  type = "success"
) {
  this.$root.showNotification(message, type);
};

// Initialize auth before mounting
auth.init();

app.use(router);
app.mount("#app");
