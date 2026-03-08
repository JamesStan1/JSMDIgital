<template>
  <div class="login-page">
    <div class="login-card">
      <!-- Logo -->
      <div class="login-logo">
        <img src="/JSM Digital Logo.png" alt="JSM Digital" />
      </div>

      <h1 class="login-title">Staff Login</h1>
      <p class="login-sub">Sign in to access the team dashboard.</p>

      <form class="login-form" @submit.prevent="submit" novalidate>
        <div class="field">
          <label for="username">Username or Email</label>
          <input
            id="username"
            v-model="form.username"
            type="text"
            autocomplete="username"
            placeholder="your.username"
            :class="{ error: errors.username }"
            required
          />
          <span v-if="errors.username" class="field-error">{{ errors.username }}</span>
        </div>

        <div class="field">
          <label for="password">Password</label>
          <div class="password-wrap">
            <input
              id="password"
              v-model="form.password"
              :type="showPwd ? 'text' : 'password'"
              autocomplete="current-password"
              placeholder="••••••••"
              :class="{ error: errors.password }"
              required
            />
            <button type="button" class="toggle-pwd" @click="showPwd = !showPwd" :aria-label="showPwd ? 'Hide password' : 'Show password'">
              <svg v-if="!showPwd" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              <svg v-else width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
            </button>
          </div>
          <span v-if="errors.password" class="field-error">{{ errors.password }}</span>
        </div>

        <p v-if="globalError" class="global-error">{{ globalError }}</p>

        <button type="submit" class="btn-submit" :disabled="loading">
          <span v-if="loading" class="spinner"></span>
          <span>{{ loading ? 'Signing in…' : 'Sign In' }}</span>
        </button>
      </form>

      <RouterLink to="/" class="back-link">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        Back to website
      </RouterLink>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuth } from '@/stores/auth'

const router      = useRouter()
const route       = useRoute()
const { setAuth } = useAuth()

const form = reactive({ username: '', password: '' })
const errors = reactive({ username: '', password: '' })
const globalError = ref('')
const loading     = ref(false)
const showPwd     = ref(false)

function validate() {
  errors.username = form.username.trim() === '' ? 'Username is required.' : ''
  errors.password = form.password === '' ? 'Password is required.' : ''
  return !errors.username && !errors.password
}

async function submit() {
  globalError.value = ''
  if (!validate()) return

  loading.value = true
  try {
    const BASE = import.meta.env.VITE_API_BASE || '/api'
    const res  = await fetch(`${BASE}/auth/login`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ username: form.username.trim(), password: form.password }),
    })
    const data = await res.json()

    if (!res.ok || !data.success) {
      globalError.value = data.message || 'Login failed. Please check your credentials.'
      return
    }

    setAuth(data.user, data.token)
    const redirect = route.query.redirect || '/dashboard'
    router.push(redirect)
  } catch {
    globalError.value = 'Unable to connect to the server. Please try again.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.login-page {
  min-height: 100vh;
  background: #030712;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem 1rem;
}

.login-card {
  width: 100%;
  max-width: 420px;
  background: rgba(255,255,255,0.03);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 16px;
  padding: 2.5rem 2rem;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.login-logo img {
  height: 44px;
  width: auto;
  object-fit: contain;
  margin-bottom: 1.5rem;
}

.login-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #f9fafb;
  margin: 0 0 0.4rem;
  text-align: center;
}

.login-sub {
  color: #6b7280;
  font-size: 0.875rem;
  text-align: center;
  margin: 0 0 2rem;
}

.login-form {
  width: 100%;
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.field {
  display: flex;
  flex-direction: column;
  gap: 0.4rem;
}

.field label {
  font-size: 0.8rem;
  font-weight: 500;
  color: #9ca3af;
  letter-spacing: 0.02em;
}

.field input {
  background: rgba(255,255,255,0.05);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 8px;
  padding: 0.65rem 0.9rem;
  color: #f9fafb;
  font-size: 0.925rem;
  outline: none;
  transition: border-color 0.2s;
  width: 100%;
  box-sizing: border-box;
}

.field input:focus {
  border-color: #4f46e5;
}

.field input.error {
  border-color: #f87171;
}

.password-wrap {
  position: relative;
}

.password-wrap input {
  padding-right: 2.75rem;
}

.toggle-pwd {
  position: absolute;
  right: 0.75rem;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: #6b7280;
  cursor: pointer;
  padding: 0;
  display: flex;
  align-items: center;
  transition: color 0.2s;
}

.toggle-pwd:hover {
  color: #9ca3af;
}

.field-error {
  font-size: 0.78rem;
  color: #f87171;
}

.global-error {
  background: rgba(248, 113, 113, 0.1);
  border: 1px solid rgba(248, 113, 113, 0.3);
  border-radius: 8px;
  padding: 0.65rem 0.9rem;
  color: #f87171;
  font-size: 0.85rem;
  text-align: center;
  margin: 0;
}

.btn-submit {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  background: linear-gradient(135deg, #4f46e5, #06b6d4);
  color: #fff;
  border: none;
  border-radius: 8px;
  padding: 0.75rem;
  font-size: 0.95rem;
  font-weight: 600;
  cursor: pointer;
  transition: opacity 0.2s, transform 0.2s;
  width: 100%;
  margin-top: 0.25rem;
}

.btn-submit:hover:not(:disabled) {
  opacity: 0.9;
  transform: translateY(-1px);
}

.btn-submit:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.spinner {
  width: 16px;
  height: 16px;
  border: 2px solid rgba(255,255,255,0.3);
  border-top-color: #fff;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.back-link {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  margin-top: 1.75rem;
  color: #6b7280;
  font-size: 0.85rem;
  text-decoration: none;
  transition: color 0.2s;
}

.back-link:hover {
  color: #9ca3af;
}
</style>
