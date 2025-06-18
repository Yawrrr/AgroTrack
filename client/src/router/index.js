import { createRouter, createWebHistory } from "vue-router";
import DashboardScreen from "../views/DashboardScreen.vue";
import CropsScreen from "../views/CropsScreen.vue";
import SensorsScreen from "../views/SensorsScreen.vue";
import ActuatorsScreen from "../views/ActuatorsScreen.vue";
import CropFormScreen from "../views/CropFormScreen.vue";
import LoginScreen from "../views/LoginScreen.vue";
import RegisterScreen from "../views/RegisterScreen.vue";
import { auth } from "../stores/auth.js";

const routes = [
  {
    path: "/login",
    name: "login",
    component: LoginScreen,
    meta: { requiresGuest: true },
  },
  {
    path: "/register",
    name: "register",
    component: RegisterScreen,
    meta: { requiresGuest: true },
  },
  {
    path: "/",
    name: "dashboard",
    component: DashboardScreen,
    meta: { requiresAuth: true },
  },
  {
    path: "/crops",
    name: "crops",
    component: CropsScreen,
    meta: { requiresAuth: true },
  },
  {
    path: "/sensors",
    name: "sensors",
    component: SensorsScreen,
    meta: { requiresAuth: true },
  },
  {
    path: "/actuators",
    name: "actuators",
    component: ActuatorsScreen,
    meta: { requiresAuth: true },
  },
  {
    path: "/crops/new",
    name: "crop-new",
    component: CropFormScreen,
    meta: { requiresAuth: true },
  },
  {
    path: "/crops/:id/edit",
    name: "crop-edit",
    component: CropFormScreen,
    meta: { requiresAuth: true },
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// Route guards
router.beforeEach(async (to, from, next) => {
  // Initialize auth if not done yet
  if (!auth.state.isAuthenticated && localStorage.getItem("token")) {
    await auth.checkAuth();
  }

  // Check if route requires authentication
  if (to.meta.requiresAuth && !auth.state.isAuthenticated) {
    next("/login");
    return;
  }

  // Check if route requires guest (not authenticated)
  if (to.meta.requiresGuest && auth.state.isAuthenticated) {
    next("/");
    return;
  }

  next();
});

export default router;
