<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card mt-5">
          <div class="card-header">
            <h4 class="text-center">Login to AgroTrack</h4>
          </div>
          <div class="card-body">
            <form @submit.prevent="login">
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
                  {{ loading ? "Logging in..." : "Login" }}
                </button>
              </div>
            </form>
            <div class="text-center mt-3">
              <p>
                Don't have an account?
                <router-link to="/register">Register here</router-link>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { auth } from "../stores/auth.js";

export default {
  name: "LoginScreen",
  data() {
    return {
      form: {
        email: "",
        password: "",
      },
      loading: false,
    };
  },
  methods: {
    async login() {
      if (!this.validateForm()) return;

      this.loading = true;
      const result = await auth.login(this.form.email, this.form.password);

      if (result.success) {
        this.$root.showNotification(result.message, "success");
        this.$router.push("/");
      } else {
        this.$root.showNotification(result.message, "error");
      }

      this.loading = false;
    },

    validateForm() {
      if (!this.form.email.trim()) {
        this.$root.showNotification("Email is required", "error");
        return false;
      }
      if (!this.form.password) {
        this.$root.showNotification("Password is required", "error");
        return false;
      }
      return true;
    },
  },

  mounted() {
    // Redirect if already logged in
    if (auth.state.isAuthenticated) {
      this.$router.push("/");
    }
  },
};
</script>
