<template>
  <header class="navbar" :class="{ scrolled: isScrolled }">
    <div class="container nav-inner">
      <!-- Logo -->
      <RouterLink to="/" class="logo" @click="closeMenu">
        <img src="/JSM Digital Logo.png" alt="JSM Digital" class="logo-img" />
      </RouterLink>

      <!-- Desktop Links -->
      <nav class="nav-links" :class="{ open: menuOpen }" role="navigation">
        <RouterLink to="/" exact-active-class="active" @click="closeMenu">Home</RouterLink>
        <RouterLink to="/about" active-class="active" @click="closeMenu">About</RouterLink>
        <RouterLink to="/services" active-class="active" @click="closeMenu">Services</RouterLink>
        <RouterLink to="/portfolio" active-class="active" @click="closeMenu">Portfolio</RouterLink>
        <RouterLink to="/contact" active-class="active" @click="closeMenu">Contact</RouterLink>
        <RouterLink to="/contact" class="btn-nav-cta" @click="closeMenu">Get a Quote</RouterLink>

        <!-- Logged-in user -->
        <template v-if="isAuthenticated">
          <RouterLink to="/dashboard" class="btn-nav-dashboard" @click="closeMenu">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            Dashboard
          </RouterLink>
          <button class="btn-nav-logout" @click="logout">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            Logout
          </button>
        </template>

        <!-- Guest login -->
        <RouterLink v-else to="/login" class="btn-nav-login" @click="closeMenu">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
          Staff Login
        </RouterLink>
      </nav>

      <!-- Hamburger -->
      <button
        class="hamburger"
        :class="{ open: menuOpen }"
        @click="toggleMenu"
        :aria-expanded="menuOpen"
        aria-label="Toggle menu"
      >
        <span></span>
        <span></span>
        <span></span>
      </button>
    </div>
  </header>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '@/stores/auth'
import { api } from '@/utils/api'

const router = useRouter()
const { isAuthenticated, clearAuth } = useAuth()

const isScrolled = ref(false)
const menuOpen   = ref(false)

function onScroll() {
  isScrolled.value = window.scrollY > 60
}

function toggleMenu() {
  menuOpen.value = !menuOpen.value
  document.body.style.overflow = menuOpen.value ? 'hidden' : ''
}

function closeMenu() {
  menuOpen.value = false
  document.body.style.overflow = ''
}

async function logout() {
  closeMenu()
  try {
    await api.post('/auth/logout')
  } catch (_) { /* ignore network errors on logout */ }
  clearAuth()
  router.push({ name: 'home' })
}

onMounted(() => window.addEventListener('scroll', onScroll, { passive: true }))
onUnmounted(() => { window.removeEventListener('scroll', onScroll); document.body.style.overflow = '' })
</script>

<style scoped>
.navbar {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 999;
  padding: 1.25rem 0;
  transition: background 0.35s ease, padding 0.35s ease, box-shadow 0.35s ease;
}

.navbar.scrolled {
  background: rgba(3, 7, 18, 0.92);
  backdrop-filter: blur(24px);
  -webkit-backdrop-filter: blur(24px);
  border-bottom: 1px solid rgba(255, 255, 255, 0.06);
  padding: 0.85rem 0;
  box-shadow: 0 4px 32px rgba(0, 0, 0, 0.35);
}

.nav-inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

/* Logo */
.logo {
  display: flex;
  align-items: center;
  text-decoration: none;
}

.logo-img {
  height: 40px;
  width: auto;
  display: block;
  object-fit: contain;
}

/* Nav Links */
.nav-links {
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

.nav-links a {
  position: relative;
  color: #9ca3af;
  font-size: 0.9rem;
  font-weight: 500;
  padding: 0.45rem 0.85rem;
  border-radius: 6px;
  transition: color 0.2s, background 0.2s;
  text-decoration: none;
}

.nav-links a:hover,
.nav-links a.active {
  color: #f9fafb;
  background: rgba(255, 255, 255, 0.05);
}

.btn-nav-cta {
  background: linear-gradient(135deg, #4f46e5, #06b6d4) !important;
  color: #fff !important;
  padding: 0.55rem 1.3rem !important;
  border-radius: 8px !important;
  font-weight: 600 !important;
  margin-left: 0.5rem;
  transition: opacity 0.2s, transform 0.2s, box-shadow 0.2s !important;
  box-shadow: 0 4px 16px rgba(79, 70, 229, 0.3);
}

.btn-nav-cta:hover {
  opacity: 0.9 !important;
  transform: translateY(-1px) !important;
  box-shadow: 0 6px 24px rgba(79, 70, 229, 0.45) !important;
  background: rgba(255,255,255,0.05) !important;
}

/* Login / Dashboard / Logout buttons */
.btn-nav-login,
.btn-nav-dashboard {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  color: #9ca3af !important;
  font-size: 0.9rem;
  font-weight: 500;
  padding: 0.45rem 0.85rem;
  border-radius: 6px;
  text-decoration: none;
  transition: color 0.2s, background 0.2s;
  margin-left: 0.25rem;
}

.btn-nav-login:hover,
.btn-nav-dashboard:hover {
  color: #f9fafb !important;
  background: rgba(255, 255, 255, 0.05) !important;
}

.btn-nav-logout {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  background: none;
  border: 1px solid rgba(255,255,255,0.12);
  color: #9ca3af;
  font-size: 0.9rem;
  font-weight: 500;
  padding: 0.42rem 0.85rem;
  border-radius: 6px;
  cursor: pointer;
  transition: color 0.2s, background 0.2s, border-color 0.2s;
  margin-left: 0.25rem;
}

.btn-nav-logout:hover {
  color: #f87171;
  border-color: rgba(248, 113, 113, 0.4);
  background: rgba(248, 113, 113, 0.08);
}

/* Hamburger */
.hamburger {
  display: none;
  flex-direction: column;
  gap: 5px;
  background: none;
  border: none;
  padding: 4px;
  cursor: pointer;
}

.hamburger span {
  display: block;
  width: 22px;
  height: 2px;
  background: #f9fafb;
  border-radius: 2px;
  transition: transform 0.3s ease, opacity 0.3s ease;
}

.hamburger.open span:nth-child(1) {
  transform: translateY(7px) rotate(45deg);
}
.hamburger.open span:nth-child(2) {
  opacity: 0;
  transform: scaleX(0);
}
.hamburger.open span:nth-child(3) {
  transform: translateY(-7px) rotate(-45deg);
}

/* Mobile */
@media (max-width: 820px) {
  .hamburger { display: flex; }

  .nav-links {
    position: fixed;
    inset: 0;
    top: 64px;
    background: rgba(3, 7, 18, 0.97);
    backdrop-filter: blur(24px);
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transform: translateX(100%);
    transition: transform 0.35s ease;
    pointer-events: none;
  }

  .nav-links.open {
    transform: translateX(0);
    pointer-events: all;
  }

  .nav-links a {
    font-size: 1.25rem;
    padding: 0.75rem 2rem;
    width: 100%;
    text-align: center;
    border-radius: 10px;
  }

  .btn-nav-cta {
    margin-left: 0 !important;
    margin-top: 0.75rem;
    width: 220px;
    text-align: center;
    justify-content: center;
  }

  .btn-nav-login,
  .btn-nav-dashboard,
  .btn-nav-logout {
    margin-left: 0 !important;
    margin-top: 0.5rem;
    width: 220px;
    justify-content: center;
  }
}
</style>
