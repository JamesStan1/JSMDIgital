<template>
  <div class="dash-shell">
    <!-- Sidebar -->
    <aside class="sidebar" :class="{ open: sidebarOpen }">
      <div class="sidebar-inner">
        <!-- Brand -->
        <div class="sidebar-brand">
          <img src="/JSM Digital Logo.png" alt="JSM Digital" class="sidebar-logo" />
        </div>

        <!-- Nav -->
        <nav class="sidebar-nav">
          <RouterLink to="/dashboard/inquiries" class="nav-item" active-class="active" @click="closeSidebar">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            <span>Inquiries</span>
          </RouterLink>
          <RouterLink to="/dashboard/boards" class="nav-item" active-class="active" @click="closeSidebar">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            <span>Boards</span>
          </RouterLink>
        </nav>

        <!-- User -->
        <div class="sidebar-user">
          <div class="user-avatar">{{ userInitial }}</div>
          <div class="user-info">
            <span class="user-name">{{ user?.full_name }}</span>
            <span class="user-role">{{ user?.role }}</span>
          </div>
          <button class="logout-btn" title="Logout" @click="logout">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
          </button>
        </div>
      </div>
    </aside>

    <!-- Overlay for mobile -->
    <div v-if="sidebarOpen" class="overlay" @click="closeSidebar" />

    <!-- Main content -->
    <div class="main-wrap">
      <!-- Top bar (mobile) -->
      <header class="topbar">
        <button class="menu-btn" @click="sidebarOpen = true" aria-label="Open menu">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>
        <span class="topbar-title">{{ pageTitle }}</span>
        <div class="topbar-user">{{ userInitial }}</div>
      </header>

      <main class="content">
        <RouterView />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuth } from '@/stores/auth'
import { api } from '@/utils/api'

const router           = useRouter()
const route            = useRoute()
const { user, clearAuth } = useAuth()
const sidebarOpen      = ref(false)

const userInitial = computed(() => {
  const name = user.value?.full_name || ''
  return name ? name.charAt(0).toUpperCase() : '?'
})

const pageTitle = computed(() => {
  if (route.path.includes('/boards/')) return 'Board Detail'
  if (route.path.endsWith('/boards'))  return 'Boards'
  if (route.path.endsWith('/inquiries')) return 'Inquiries'
  return 'Dashboard'
})

function closeSidebar() {
  sidebarOpen.value = false
}

async function logout() {
  try { await api.post('/auth/logout') } catch (_) { /* ignore */ }
  clearAuth()
  router.push({ name: 'home' })
}
</script>

<style scoped>
.dash-shell {
  display: flex;
  min-height: 100vh;
  background: #030712;
  color: #f9fafb;
}

/* ── Sidebar ──────────────────────────────────────────────── */
.sidebar {
  width: 240px;
  flex-shrink: 0;
  background: #0d1117;
  border-right: 1px solid rgba(255,255,255,0.06);
  display: flex;
  flex-direction: column;
  position: sticky;
  top: 0;
  height: 100vh;
  overflow-y: auto;
}

.sidebar-inner {
  display: flex;
  flex-direction: column;
  height: 100%;
  padding: 1.25rem 1rem;
}

.sidebar-brand {
  display: flex;
  align-items: center;
  margin-bottom: 2rem;
  padding: 0 0.25rem;
}

.sidebar-logo {
  height: 32px;
  width: auto;
  object-fit: contain;
}

.sidebar-nav {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
  flex: 1;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.6rem 0.85rem;
  border-radius: 8px;
  color: #6b7280;
  font-size: 0.9rem;
  font-weight: 500;
  text-decoration: none;
  transition: color 0.2s, background 0.2s;
}

.nav-item:hover,
.nav-item.active {
  color: #f9fafb;
  background: rgba(255,255,255,0.06);
}

.nav-item.active {
  color: #818cf8;
  background: rgba(79, 70, 229, 0.12);
}

/* ── Sidebar user area ────────────────────────────────────── */
.sidebar-user {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem 0.5rem;
  margin-top: auto;
  border-top: 1px solid rgba(255,255,255,0.06);
}

.user-avatar {
  width: 34px;
  height: 34px;
  border-radius: 50%;
  background: linear-gradient(135deg, #4f46e5, #06b6d4);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.85rem;
  font-weight: 700;
  color: #fff;
  flex-shrink: 0;
}

.user-info {
  display: flex;
  flex-direction: column;
  flex: 1;
  min-width: 0;
}

.user-name {
  font-size: 0.85rem;
  font-weight: 600;
  color: #f9fafb;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.user-role {
  font-size: 0.72rem;
  color: #6b7280;
  text-transform: capitalize;
}

.logout-btn {
  background: none;
  border: none;
  color: #6b7280;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  display: flex;
  align-items: center;
  transition: color 0.2s;
  flex-shrink: 0;
}

.logout-btn:hover {
  color: #f87171;
}

/* ── Main wrap ─────────────────────────────────────────────── */
.main-wrap {
  flex: 1;
  display: flex;
  flex-direction: column;
  min-width: 0;
}

.topbar {
  display: none;
  align-items: center;
  gap: 1rem;
  padding: 0.75rem 1.25rem;
  background: #0d1117;
  border-bottom: 1px solid rgba(255,255,255,0.06);
  position: sticky;
  top: 0;
  z-index: 50;
}

.menu-btn {
  background: none;
  border: none;
  color: #9ca3af;
  cursor: pointer;
  padding: 4px;
  display: flex;
  align-items: center;
}

.topbar-title {
  font-size: 1rem;
  font-weight: 600;
  color: #f9fafb;
  flex: 1;
}

.topbar-user {
  width: 30px;
  height: 30px;
  border-radius: 50%;
  background: linear-gradient(135deg, #4f46e5, #06b6d4);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.8rem;
  font-weight: 700;
  color: #fff;
}

.content {
  flex: 1;
  padding: 2rem;
  overflow-y: auto;
}

/* ── Overlay ──────────────────────────────────────────────── */
.overlay {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.55);
  z-index: 40;
}

/* ── Mobile ───────────────────────────────────────────────── */
@media (max-width: 768px) {
  .sidebar {
    position: fixed;
    inset: 0;
    width: 260px;
    z-index: 50;
    transform: translateX(-100%);
    transition: transform 0.3s ease;
  }

  .sidebar.open {
    transform: translateX(0);
  }

  .overlay {
    display: block;
  }

  .topbar {
    display: flex;
  }

  .content {
    padding: 1.25rem;
  }
}
</style>
