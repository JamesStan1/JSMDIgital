import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '@/views/HomeView.vue'
import { useAuth } from '@/stores/auth'

const routes = [
  {
    path: '/',
    name: 'home',
    component: HomeView,
    meta: { title: 'JSM Digital — Web & Mobile App Development' }
  },
  {
    path: '/about',
    name: 'about',
    component: () => import('@/views/AboutView.vue'),
    meta: { title: 'About Us — JSM Digital' }
  },
  {
    path: '/services',
    name: 'services',
    component: () => import('@/views/ServicesView.vue'),
    meta: { title: 'Our Services — JSM Digital' }
  },
  {
    path: '/portfolio',
    name: 'portfolio',
    component: () => import('@/views/PortfolioView.vue'),
    meta: { title: 'Portfolio — JSM Digital' }
  },
  {
    path: '/contact',
    name: 'contact',
    component: () => import('@/views/ContactView.vue'),
    meta: { title: 'Contact Us — JSM Digital' }
  },
  {
    path: '/login',
    name: 'login',
    component: () => import('@/views/LoginView.vue'),
    meta: { title: 'Staff Login — JSM Digital', guestOnly: true }
  },
  {
    path: '/dashboard',
    component: () => import('@/views/dashboard/DashboardLayout.vue'),
    meta: { requiresAuth: true, isDashboard: true },
    children: [
      {
        path: '',
        redirect: '/dashboard/inquiries'
      },
      {
        path: 'inquiries',
        name: 'dashboard-inquiries',
        component: () => import('@/views/dashboard/InquiriesView.vue'),
        meta: { title: 'Inquiries — JSM Digital', requiresAuth: true, isDashboard: true }
      },
      {
        path: 'boards',
        name: 'dashboard-boards',
        component: () => import('@/views/dashboard/BoardsView.vue'),
        meta: { title: 'Boards — JSM Digital', requiresAuth: true, isDashboard: true }
      },
      {
        path: 'boards/:id',
        name: 'dashboard-board-detail',
        component: () => import('@/views/dashboard/BoardDetailView.vue'),
        meta: { title: 'Board — JSM Digital', requiresAuth: true, isDashboard: true }
      }
    ]
  },
  {
    path: '/:pathMatch(.*)*',
    redirect: '/'
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, _from, savedPosition) {
    if (savedPosition) return savedPosition
    if (to.hash) return { el: to.hash, behavior: 'smooth' }
    return { top: 0, behavior: 'smooth' }
  }
})

router.beforeEach((to, _from, next) => {
  const { isAuthenticated } = useAuth()

  if (to.meta.requiresAuth && !isAuthenticated.value) {
    return next({ name: 'login', query: { redirect: to.fullPath } })
  }
  if (to.meta.guestOnly && isAuthenticated.value) {
    return next({ name: 'dashboard-inquiries' })
  }
  next()
})

router.afterEach((to) => {
  document.title = to.meta.title || 'JSM Digital'
})

export default router

