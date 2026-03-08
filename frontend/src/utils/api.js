/**
 * Lightweight API client for the JSM Digital dashboard backend.
 * Automatically attaches the Authorization header from the auth store.
 * On 401, clears auth state and redirects to /login.
 */
import { useAuth } from '@/stores/auth'

const BASE = import.meta.env.VITE_API_BASE || '/api'

async function request(method, path, body = null) {
  const { token, clearAuth } = useAuth()

  const headers = { 'Content-Type': 'application/json' }
  if (token.value) {
    headers['Authorization'] = `Bearer ${token.value}`
  }

  const opts = { method, headers }
  if (body !== null) {
    opts.body = JSON.stringify(body)
  }

  const res  = await fetch(`${BASE}${path}`, opts)
  const data = await res.json().catch(() => ({}))

  if (res.status === 401) {
    clearAuth()
    window.location.href = '/login'
    return
  }

  if (!res.ok) {
    const err = new Error(data.message || `HTTP ${res.status}`)
    err.status = res.status
    err.data   = data
    throw err
  }

  return data
}

export const api = {
  get:    (path)        => request('GET',    path),
  post:   (path, body)  => request('POST',   path, body),
  put:    (path, body)  => request('PUT',    path, body),
  patch:  (path, body)  => request('PATCH',  path, body),
  delete: (path)        => request('DELETE', path),
}
