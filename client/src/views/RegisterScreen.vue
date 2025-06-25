<template>
  <div class="register-container">
    <div class="register-box">
      <div class="register-header">
        <i class="fas fa-user-plus logo"></i>
        <h1>Create Your AgroTrack Account</h1>
        <p>Join our platform to manage your smart farm</p>
      </div>
      <form @submit.prevent="register">
        <div class="form-group">
          <label for="name">Full Name</label>
          <input
            type="text"
            id="name"
            v-model="form.name"
            required
            :disabled="loading"
            placeholder="e.g., John Doe"
          />
        </div>
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
            placeholder="Minimum 6 characters"
          />
        </div>
        <div class="form-group">
          <label for="confirmPassword">Confirm Password</label>
          <input
            type="password"
            id="confirmPassword"
            v-model="form.confirmPassword"
            required
            :disabled="loading"
            placeholder="Re-enter your password"
          />
        </div>
        <button type="submit" class="btn btn-primary" :disabled="loading">
          <span v-if="loading" class="spinner-border spinner-border-sm"></span>
          {{ loading ? "Registering..." : "Create Account" }}
        </button>
      </form>
      <div class="login-link">
        <p>
          Already have an account?
          <router-link to="/login">Login here</router-link>
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
  name: "",
  email: "",
  password: "",
  confirmPassword: "",
});
const loading = ref(false);
const router = useRouter();

const validateForm = () => {
  if (!form.name.trim()) {
    showNotification("Name is required", "error");
    return false;
  }
  if (!form.email.trim()) {
    showNotification("Email is required", "error");
    return false;
  }
  if (!form.password) {
    showNotification("Password is required", "error");
    return false;
  }
  if (form.password.length < 6) {
    showNotification("Password must be at least 6 characters long", "error");
    return false;
  }
  if (form.password !== form.confirmPassword) {
    showNotification("Passwords do not match", "error");
    return false;
  }
  return true;
};

const register = async () => {
  if (!validateForm()) return;

  loading.value = true;
  const result = await auth.register(form.name, form.email, form.password);

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
.register-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  width: 100%;
  background-color: var(--secondary-color);
  padding: 2rem 0;
}

.register-box {
  width: 100%;
  max-width: 450px;
  padding: 3rem;
  background-color: var(--card-bg-color);
  box-shadow: 0 10px 25px var(--shadow-color);
  border-radius: 12px;
  text-align: center;
}

.register-header .logo {
  font-size: 3rem;
  color: var(--primary-color);
  margin-bottom: 1rem;
}

.register-header h1 {
  font-size: 1.75rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
}

.register-header p {
  color: #666;
  margin-bottom: 2rem;
}

.form-group {
  text-align: left;
  margin-bottom: 1.25rem;
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

.login-link {
  margin-top: 2rem;
}

.login-link a {
  color: var(--primary-color);
  font-weight: 500;
  text-decoration: none;
}

.login-link a:hover {
  text-decoration: underline;
}
</style>
