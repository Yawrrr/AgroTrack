<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card mt-5">
          <div class="card-header">
            <h4 class="text-center">Register for AgroTrack</h4>
          </div>
          <div class="card-body">
            <form @submit.prevent="register">
              <div class="mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input
                  type="text"
                  class="form-control"
                  id="name"
                  v-model="form.name"
                  required
                  :disabled="loading"
                />
              </div>
              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input
                  type="email"
                  class="form-control"
                  id="email"
                  v-model="form.email"
                  required
                  :disabled="loading"
                />
              </div>
              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input
                  type="password"
                  class="form-control"
                  id="password"
                  v-model="form.password"
                  required
                  :disabled="loading"
                />
                <div class="form-text">
                  Password must be at least 6 characters long.
                </div>
              </div>
              <div class="mb-3">
                <label for="confirmPassword" class="form-label"
                  >Confirm Password</label
                >
                <input
                  type="password"
                  class="form-control"
                  id="confirmPassword"
                  v-model="form.confirmPassword"
                  required
                  :disabled="loading"
                />
              </div>
              <div class="d-grid gap-2">
                <button
                  type="submit"
                  class="btn btn-primary"
                  :disabled="loading"
                >
                  <span
                    v-if="loading"
                    class="spinner-border spinner-border-sm me-2"
                  ></span>
                  {{ loading ? "Registering..." : "Register" }}
                </button>
              </div>
            </form>
            <div class="text-center mt-3">
              <p>
                Already have an account?
                <router-link to="/login">Login here</router-link>
              </p>
            </div>
          </div>
        </div>
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
