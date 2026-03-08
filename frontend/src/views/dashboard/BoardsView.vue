<template>
  <div class="boards-view">
    <!-- Page header -->
    <div class="page-header">
      <div>
        <h1 class="page-title">Boards</h1>
        <p class="page-sub">Organise projects and tasks using boards, lists, and cards.</p>
      </div>
      <button class="btn-primary" @click="showCreate = true">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        New Board
      </button>
    </div>

    <!-- Loading / error -->
    <div v-if="loading" class="state-msg">
      <span class="spinner"></span> Loading boards…
    </div>
    <div v-else-if="error" class="state-msg error">{{ error }}</div>

    <!-- Grid -->
    <div v-else-if="boards.length" class="boards-grid">
      <RouterLink
        v-for="b in boards"
        :key="b.id"
        :to="`/dashboard/boards/${b.id}`"
        class="board-card"
      >
        <div class="board-color-bar" :style="{ background: b.color }"></div>
        <div class="board-body">
          <h2 class="board-title">{{ b.title }}</h2>
          <p v-if="b.description" class="board-desc">{{ b.description }}</p>
          <div class="board-meta">
            <span class="meta-chip">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
              {{ b.list_count }} list{{ b.list_count !== 1 ? 's' : '' }}
            </span>
            <span class="meta-chip">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
              {{ b.card_count }} card{{ b.card_count !== 1 ? 's' : '' }}
            </span>
          </div>
          <div class="board-creator">Created by {{ b.created_by_name }}</div>
        </div>

        <!-- Delete button (admin/manager only) -->
        <button
          v-if="canDelete"
          class="board-delete"
          title="Delete board"
          @click.prevent="confirmDelete(b)"
        >
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
        </button>
      </RouterLink>
    </div>

    <div v-else class="empty-state">
      <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="empty-icon"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
      <p>No boards yet. Create your first board to get started.</p>
    </div>

    <!-- Create Board Modal -->
    <div v-if="showCreate" class="modal-backdrop" @click.self="showCreate = false">
      <div class="modal">
        <h2 class="modal-title">Create Board</h2>

        <div class="field">
          <label>Board Title *</label>
          <input v-model="form.title" type="text" placeholder="e.g. Website Redesign" />
          <span v-if="formError.title" class="field-error">{{ formError.title }}</span>
        </div>

        <div class="field">
          <label>Description</label>
          <textarea v-model="form.description" rows="2" placeholder="Optional description…"></textarea>
        </div>

        <div class="field">
          <label>Colour</label>
          <div class="color-row">
            <button
              v-for="c in COLORS"
              :key="c"
              class="color-swatch"
              :class="{ active: form.color === c }"
              :style="{ background: c }"
              @click="form.color = c"
            ></button>
          </div>
        </div>

        <p v-if="createError" class="global-error">{{ createError }}</p>

        <div class="modal-actions">
          <button class="btn-secondary" @click="showCreate = false">Cancel</button>
          <button class="btn-primary" :disabled="createLoading" @click="submitCreate">
            <span v-if="createLoading" class="spinner sm"></span>
            {{ createLoading ? 'Creating…' : 'Create Board' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Delete confirm modal -->
    <div v-if="deleteTarget" class="modal-backdrop" @click.self="deleteTarget = null">
      <div class="modal sm">
        <h2 class="modal-title">Delete Board</h2>
        <p class="modal-sub">
          Delete <strong>{{ deleteTarget.title }}</strong>? All lists and cards will be permanently removed.
        </p>
        <div class="modal-actions">
          <button class="btn-secondary" @click="deleteTarget = null">Cancel</button>
          <button class="btn-danger" :disabled="deleteLoading" @click="submitDelete">
            <span v-if="deleteLoading" class="spinner sm"></span>
            {{ deleteLoading ? 'Deleting…' : 'Delete' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useAuth } from '@/stores/auth'
import { api } from '@/utils/api'

const { user } = useAuth()
const canDelete = computed(() => ['admin', 'manager'].includes(user.value?.role))

const COLORS = [
  '#4f46e5', '#7c3aed', '#db2777', '#dc2626',
  '#ea580c', '#d97706', '#16a34a', '#0891b2',
]

// ── State ─────────────────────────────────────────────────────
const boards       = ref([])
const loading      = ref(false)
const error        = ref('')
const showCreate   = ref(false)
const createLoading = ref(false)
const createError  = ref('')
const deleteTarget = ref(null)
const deleteLoading = ref(false)

const form = reactive({ title: '', description: '', color: '#4f46e5' })
const formError = reactive({ title: '' })

// ── Fetch ─────────────────────────────────────────────────────
async function fetchBoards() {
  loading.value = true; error.value = ''
  try {
    const data = await api.get('/dashboard/boards')
    boards.value = data.boards
  } catch (e) {
    error.value = e.message || 'Failed to load boards.'
  } finally {
    loading.value = false
  }
}

// ── Create ────────────────────────────────────────────────────
async function submitCreate() {
  formError.title = form.title.trim() === '' ? 'Title is required.' : ''
  if (formError.title) return

  createLoading.value = true; createError.value = ''
  try {
    await api.post('/dashboard/boards', {
      title: form.title.trim(),
      description: form.description.trim(),
      color: form.color,
    })
    Object.assign(form, { title: '', description: '', color: '#4f46e5' })
    showCreate.value = false
    fetchBoards()
  } catch (e) {
    createError.value = e.message || 'Failed to create board.'
  } finally {
    createLoading.value = false
  }
}

// ── Delete ────────────────────────────────────────────────────
function confirmDelete(b) {
  deleteTarget.value = b
}

async function submitDelete() {
  deleteLoading.value = true
  try {
    await api.delete(`/dashboard/boards?id=${deleteTarget.value.id}`)
    deleteTarget.value = null
    fetchBoards()
  } catch (e) {
    alert(e.message || 'Delete failed.')
  } finally {
    deleteLoading.value = false
  }
}

onMounted(fetchBoards)
</script>

<style scoped>
.boards-view { max-width: 1200px; }

.page-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: 2rem;
  flex-wrap: wrap;
}

.page-title { font-size: 1.5rem; font-weight: 700; color: #f9fafb; margin: 0 0 0.25rem; }
.page-sub   { color: #6b7280; font-size: 0.875rem; margin: 0; }

/* Grid */
.boards-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
  gap: 1.25rem;
}

.board-card {
  position: relative;
  background: rgba(255,255,255,0.03);
  border: 1px solid rgba(255,255,255,0.07);
  border-radius: 14px;
  overflow: hidden;
  text-decoration: none;
  display: flex;
  flex-direction: column;
  transition: border-color 0.2s, transform 0.2s;
}

.board-card:hover {
  border-color: rgba(255,255,255,0.14);
  transform: translateY(-2px);
}

.board-color-bar {
  height: 5px;
  width: 100%;
}

.board-body {
  padding: 1.25rem;
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.board-title { font-size: 1rem; font-weight: 700; color: #f9fafb; margin: 0; }

.board-desc {
  font-size: 0.8rem;
  color: #6b7280;
  margin: 0;
  line-height: 1.5;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.board-meta {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
  margin-top: 0.25rem;
}

.meta-chip {
  display: inline-flex;
  align-items: center;
  gap: 0.3rem;
  background: rgba(255,255,255,0.05);
  border-radius: 20px;
  padding: 0.2rem 0.6rem;
  font-size: 0.75rem;
  color: #9ca3af;
}

.board-creator {
  font-size: 0.72rem;
  color: #4b5563;
  margin-top: auto;
}

.board-delete {
  position: absolute;
  top: 0.85rem;
  right: 0.85rem;
  background: rgba(248,113,113,0.08);
  border: 1px solid rgba(248,113,113,0.2);
  color: #f87171;
  border-radius: 6px;
  padding: 4px 6px;
  cursor: pointer;
  opacity: 0;
  transition: opacity 0.2s;
}

.board-card:hover .board-delete { opacity: 1; }
.board-delete:hover { background: rgba(248,113,113,0.18); }

/* Empty state */
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
  padding: 4rem 2rem;
  color: #4b5563;
  text-align: center;
}

.empty-icon { color: #374151; }

/* State messages */
.state-msg {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 2rem;
  color: #6b7280;
}
.state-msg.error { color: #f87171; }

/* Buttons */
.btn-primary {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  background: linear-gradient(135deg, #4f46e5, #06b6d4);
  border: none;
  color: #fff;
  font-size: 0.875rem;
  font-weight: 600;
  padding: 0.6rem 1.25rem;
  border-radius: 8px;
  cursor: pointer;
  transition: opacity 0.2s;
  white-space: nowrap;
}
.btn-primary:hover:not(:disabled) { opacity: 0.9; }
.btn-primary:disabled { opacity: 0.5; cursor: not-allowed; }

.btn-secondary {
  background: rgba(255,255,255,0.05);
  border: 1px solid rgba(255,255,255,0.1);
  color: #9ca3af;
  font-size: 0.875rem;
  font-weight: 600;
  padding: 0.6rem 1.25rem;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s;
}
.btn-secondary:hover { border-color: rgba(255,255,255,0.2); color: #f9fafb; }

.btn-danger {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  background: rgba(220,38,38,0.15);
  border: 1px solid rgba(220,38,38,0.4);
  color: #f87171;
  font-size: 0.875rem;
  font-weight: 600;
  padding: 0.6rem 1.25rem;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s;
}
.btn-danger:hover:not(:disabled) { background: rgba(220,38,38,0.25); }
.btn-danger:disabled { opacity: 0.5; cursor: not-allowed; }

/* Modal */
.modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.65);
  z-index: 200;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1rem;
}

.modal {
  background: #0d1117;
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 16px;
  padding: 2rem;
  width: 100%;
  max-width: 460px;
}

.modal.sm { max-width: 380px; }

.modal-title { font-size: 1.15rem; font-weight: 700; color: #f9fafb; margin: 0 0 0.5rem; }
.modal-sub   { color: #6b7280; font-size: 0.875rem; margin: 0 0 1.5rem; }

.field { display: flex; flex-direction: column; gap: 0.4rem; margin-bottom: 1rem; }
.field label { font-size: 0.8rem; font-weight: 500; color: #9ca3af; }

.field input, .field textarea {
  background: rgba(255,255,255,0.05);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 8px;
  color: #f9fafb;
  font-size: 0.9rem;
  padding: 0.6rem 0.85rem;
  outline: none;
  transition: border-color 0.2s;
  width: 100%;
  box-sizing: border-box;
  font-family: inherit;
}
.field input:focus, .field textarea:focus { border-color: #4f46e5; }
.field textarea { resize: vertical; }

.field-error { color: #f87171; font-size: 0.78rem; }
.global-error {
  background: rgba(248,113,113,0.1);
  border: 1px solid rgba(248,113,113,0.3);
  border-radius: 8px;
  color: #f87171;
  font-size: 0.85rem;
  padding: 0.6rem 0.85rem;
  margin: 0 0 1rem;
}

.color-row {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.color-swatch {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  border: 2px solid transparent;
  cursor: pointer;
  transition: transform 0.15s, border-color 0.15s;
}

.color-swatch:hover { transform: scale(1.1); }
.color-swatch.active { border-color: #fff; transform: scale(1.15); }

.modal-actions {
  display: flex;
  gap: 0.75rem;
  justify-content: flex-end;
  margin-top: 1.5rem;
}

.spinner {
  display: inline-block;
  width: 16px;
  height: 16px;
  border: 2px solid rgba(255,255,255,0.25);
  border-top-color: currentColor;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
  vertical-align: middle;
}

.spinner.sm { width: 13px; height: 13px; }

@keyframes spin { to { transform: rotate(360deg); } }
</style>
