import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '@/views/HomeView.vue'

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

router.afterEach((to) => {
  document.title = to.meta.title || 'JSM Digital'
})

export default router
