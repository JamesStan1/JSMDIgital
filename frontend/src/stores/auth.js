/**
 * Auth store — reactive singleton using Vue 3 composables.
 * No external library required; state persists via localStorage.
 */
import { ref, computed } from 'vue'

const _user  = ref(JSON.parse(localStorage.getItem('jsm_user')  || 'null'))
const _token = ref(localStorage.getItem('jsm_token') || null)

export function useAuth() {
  const isAuthenticated = computed(() => !!_token.value && !!_user.value)
  const user            = computed(() => _user.value)
  const token           = computed(() => _token.value)

  function setAuth(userData, authToken) {
    _user.value  = userData
    _token.value = authToken
    localStorage.setItem('jsm_user',  JSON.stringify(userData))
    localStorage.setItem('jsm_token', authToken)
  }

  function clearAuth() {
    _user.value  = null
    _token.value = null
    localStorage.removeItem('jsm_user')
    localStorage.removeItem('jsm_token')
  }

  return { user, token, isAuthenticated, setAuth, clearAuth }
}
