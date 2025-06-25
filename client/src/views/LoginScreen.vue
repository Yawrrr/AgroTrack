<template>
  <div class="login-container">
    <div class="login-box">
      <div class="login-header">
        <i class="fas fa-leaf logo"></i>
        <h1>Welcome to AgroTrack</h1>
        <p>Sign in to manage your smart farm</p>
      </div>
      <form @submit.prevent="login">
        <div class="form-group">
          <label for="email">Email</label>
          <input
            type="email"
            id="email"
            v-model="form.email"
            required
            :disabled="loading"
            placeholder="e.g., user@agrotrack.com"
          />
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input
            type="password"
            id="password"
            v-model="form.password"
            required
            :disabled="loading"
            placeholder="Your password"
          />
        </div>
        <button type="submit" class="btn btn-primary" :disabled="loading">
          <span v-if="loading" class="spinner-border spinner-border-sm"></span>
          {{ loading ? "Logging in..." : "Login" }}
        </button>
      </form>
      <div class="register-link">
        <p>
          Don't have an account?
          <router-link to="/register">Register here</router-link>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted, inject } from "vue";
import { useRouter } from "vue-router";
import { auth } from "@/stores/auth.js";

const showNotification = inject("showNotification");

const form = reactive({
  email: "",
  password: "",
});
const loading = ref(false);
const router = useRouter();

const validateForm = () => {
  if (!form.email.trim()) {
    showNotification("Email is required", "error");
    return false;
  }
  if (!form.password) {
    showNotification("Password is required", "error");
    return false;
  }
  return true;
};

const login = async () => {
  if (!validateForm()) return;

  loading.value = true;
  const result = await auth.login(form.email, form.password);

  if (result.success) {
    showNotification(result.message, "success");
    router.push("/");
  } else {
    showNotification(result.message, "error");
  }

  loading.value = false;
};

onMounted(() => {
  // Redirect if already logged in
  if (auth.state.isAuthenticated) {
    router.push("/");
  }
});
</script>

<style scoped>
.login-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  width: 100%;
  background-color: var(--secondary-color);
}

.login-box {
  width: 100%;
  max-width: 400px;
  padding: 3rem;
  background-color: var(--card-bg-color);
  box-shadow: 0 10px 25px var(--shadow-color);
  border-radius: 12px;
  text-align: center;
}

.login-header .logo {
  font-size: 3rem;
  color: var(--primary-color);
  margin-bottom: 1rem;
}

.login-header h1 {
  font-size: 1.75rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
}

.login-header p {
  color: #666;
  margin-bottom: 2rem;
}

.form-group {
  text-align: left;
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
}

.form-group input {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 1px solid #ced4da;
  border-radius: 6px;
  font-size: 1rem;
  background-color: #f8f9fa;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.form-group input:focus {
  outline: none;
  border-color: var(--primary-color);
  box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.2);
  background-color: #fff;
}

.btn {
  width: 100%;
  padding: 0.8rem;
  margin-top: 1rem;
}

.register-link {
  margin-top: 2rem;
}

.register-link a {
  color: var(--primary-color);
  font-weight: 500;
  text-decoration: none;
}

.register-link a:hover {
  text-decoration: underline;
}
</style>
