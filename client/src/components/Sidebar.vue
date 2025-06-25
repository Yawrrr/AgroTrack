<template>
  <aside class="sidebar">
    <div class="sidebar-header">
      <router-link to="/" class="sidebar-brand">AgroTrack</router-link>
    </div>
    <ul class="nav-links">
      <li>
        <router-link to="/">
          <i class="fas fa-tachometer-alt"></i>
          <span>Dashboard</span>
        </router-link>
      </li>
      <li>
        <router-link to="/crops">
          <i class="fas fa-leaf"></i>
          <span>Crops</span>
        </router-link>
      </li>
      <li>
        <router-link to="/sensors">
          <i class="fas fa-thermometer-half"></i>
          <span>Sensors</span>
        </router-link>
      </li>
      <li>
        <router-link to="/actuators">
          <i class="fas fa-robot"></i>
          <span>Actuators</span>
        </router-link>
      </li>
    </ul>
    <div class="sidebar-footer">
      <div class="user-info">
        <i class="fas fa-user-circle"></i>
        <span>{{ auth.state.user?.name || "User" }}</span>
      </div>
      <button @click="handleLogout" class="logout-button">
        <i class="fas fa-sign-out-alt"></i>
        <span>Logout</span>
      </button>
    </div>
  </aside>
</template>

<script setup>
import { auth } from "@/stores/auth";
import { useRouter } from "vue-router";

const router = useRouter();

const handleLogout = async () => {
  await auth.logout();
  router.push("/login");
  // Consider showing a notification on the login page after redirect
};
</script>

<style scoped>
.sidebar {
  width: 260px;
  background-color: #ffffff;
  border-right: 1px solid var(--border-color);
  display: flex;
  flex-direction: column;
  transition: width 0.3s ease;
}

.sidebar-header {
  padding: 1.5rem;
  text-align: center;
  border-bottom: 1px solid var(--border-color);
}

.sidebar-brand {
  font-size: 1.5rem;
  font-weight: bold;
  color: var(--primary-color);
  text-decoration: none;
}

.nav-links {
  list-style: none;
  padding: 0;
  margin: 1rem 0;
  flex-grow: 1;
}

.nav-links a {
  display: flex;
  align-items: center;
  padding: 1rem 1.5rem;
  color: var(--text-color);
  text-decoration: none;
  transition: background-color 0.2s ease, color 0.2s ease;
}

.nav-links a:hover,
.nav-links a.router-link-active {
  background-color: var(--secondary-color);
  color: var(--primary-color);
}

.nav-links a i {
  margin-right: 1rem;
  width: 20px;
  text-align: center;
}

.sidebar-footer {
  border-top: 1px solid var(--border-color);
  padding: 1rem 1.5rem;
}

.user-info {
  display: flex;
  align-items: center;
  margin-bottom: 1rem;
}

.user-info i {
  margin-right: 0.75rem;
  font-size: 1.2rem;
}

.logout-button {
  background: none;
  border: 1px solid var(--border-color);
  color: var(--text-color);
  width: 100%;
  padding: 0.75rem;
  border-radius: 6px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
}

.logout-button:hover {
  background-color: var(--secondary-color);
  border-color: #ccc;
}

.logout-button i {
  margin-right: 0.5rem;
}
</style>
