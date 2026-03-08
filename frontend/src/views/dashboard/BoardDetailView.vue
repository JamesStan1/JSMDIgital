<template>
  <div class="board-detail">
    <!-- Board header -->
    <div class="board-header" v-if="board">
      <div class="board-header-left">
        <RouterLink to="/dashboard/boards" class="back-link">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        </RouterLink>
        <div class="color-dot" :style="{ background: board.color }"></div>
        <h1 class="board-title">{{ board.title }}</h1>
        <span v-if="board.description" class="board-desc">{{ board.description }}</span>
      </div>
      <button class="btn-add-list" @click="addListOpen = true">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Add List
      </button>
    </div>

    <!-- Loading / error -->
    <div v-if="loading" class="state-msg">
      <span class="spinner"></span> Loading board…
    </div>
    <div v-else-if="error" class="state-msg error">{{ error }}</div>

    <!-- Kanban board -->
    <div v-else class="kanban-scroll">
      <div class="kanban-board">
        <!-- List columns -->
        <div
          v-for="list in lists"
          :key="list.id"
          class="kanban-list"
          @dragover.prevent="onDragOver(list.id)"
          @drop.prevent="onDrop(list.id)"
        >
          <!-- List header -->
          <div class="list-header">
            <span class="list-title">{{ list.title }}</span>
            <span class="list-count">{{ cardsForList(list.id).length }}</span>
            <button class="list-delete-btn" title="Delete list" @click="deleteList(list)">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
          </div>

          <!-- Cards -->
          <div class="card-stack">
            <div
              v-for="card in cardsForList(list.id)"
              :key="card.id"
              class="kanban-card"
              draggable="true"
              @dragstart="onDragStart(card, list.id)"
              @dragend="onDragEnd"
              :class="{ dragging: dragging?.card.id === card.id }"
              @click="openCard(card)"
            >
              <!-- Priority badge -->
              <span class="priority-badge" :class="card.priority">{{ card.priority }}</span>

              <p class="card-title">{{ card.title }}</p>

              <div v-if="card.description" class="card-desc">{{ truncate(card.description, 80) }}</div>

              <div class="card-footer">
                <span v-if="card.due_date" class="card-due" :class="{ overdue: isOverdue(card.due_date) }">
                  <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                  {{ formatDate(card.due_date) }}
                </span>
                <span v-if="card.assigned_names" class="card-assignees">
                  <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                  {{ card.assigned_names }}
                </span>
              </div>
            </div>
          </div>

          <!-- Add card -->
          <div v-if="addingCardList === list.id" class="add-card-form">
            <textarea
              v-model="newCardTitle"
              class="card-input"
              placeholder="Card title…"
              rows="2"
              @keydown.enter.prevent="submitCard(list.id)"
              @keydown.escape="addingCardList = null"
              ref="cardInputRef"
            ></textarea>
            <div class="add-card-actions">
              <button class="btn-add-card-ok" @click="submitCard(list.id)">Add Card</button>
              <button class="btn-add-card-cancel" @click="addingCardList = null">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
              </button>
            </div>
          </div>
          <button v-else class="btn-add-card" @click="startAddCard(list.id)">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add card
          </button>
        </div>

        <!-- Add list inline -->
        <div class="kanban-list add-list-col" v-if="addListOpen">
          <input
            v-model="newListTitle"
            class="list-name-input"
            placeholder="List name…"
            ref="listInputRef"
            @keydown.enter.prevent="submitList"
            @keydown.escape="addListOpen = false"
          />
          <div class="add-card-actions">
            <button class="btn-add-card-ok" @click="submitList">Add List</button>
            <button class="btn-add-card-cancel" @click="addListOpen = false">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Card detail modal -->
    <div v-if="activeCard" class="modal-backdrop" @click.self="activeCard = null">
      <div class="modal card-modal">
        <div class="modal-header">
          <span class="priority-badge lg" :class="activeCard.priority">{{ activeCard.priority }}</span>
          <button class="modal-close" @click="activeCard = null">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          </button>
        </div>

        <!-- Editable title -->
        <div v-if="editingField === 'title'" class="edit-field">
          <textarea v-model="editValues.title" class="edit-textarea" rows="2"></textarea>
          <div class="edit-actions">
            <button class="btn-save" @click="saveCard('title')">Save</button>
            <button class="btn-cancel-edit" @click="editingField = null">Cancel</button>
          </div>
        </div>
        <h2 v-else class="card-modal-title" @click="startEdit('title', activeCard.title)">
          {{ activeCard.title }}
          <svg class="edit-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
        </h2>

        <div class="modal-meta-row">
          <!-- Priority -->
          <div class="meta-field">
            <span class="meta-label">Priority</span>
            <select v-model="activeCard.priority" @change="saveField('priority', activeCard.priority)" class="meta-select">
              <option value="low">Low</option>
              <option value="medium">Medium</option>
              <option value="high">High</option>
              <option value="urgent">Urgent</option>
            </select>
          </div>
          <!-- Due date -->
          <div class="meta-field">
            <span class="meta-label">Due Date</span>
            <input type="date" v-model="activeCard.due_date" @change="saveField('due_date', activeCard.due_date)" class="meta-input" />
          </div>
          <!-- Move to list -->
          <div class="meta-field">
            <span class="meta-label">List</span>
            <select v-model="activeCard.list_id" @change="moveCard(activeCard, activeCard.list_id)" class="meta-select">
              <option v-for="l in lists" :key="l.id" :value="l.id">{{ l.title }}</option>
            </select>
          </div>
        </div>

        <!-- Description -->
        <div class="modal-section">
          <span class="section-label">Description</span>
          <div v-if="editingField === 'description'" class="edit-field">
            <textarea v-model="editValues.description" class="edit-textarea" rows="5"></textarea>
            <div class="edit-actions">
              <button class="btn-save" @click="saveCard('description')">Save</button>
              <button class="btn-cancel-edit" @click="editingField = null">Cancel</button>
            </div>
          </div>
          <p
            v-else
            class="desc-text"
            :class="{ placeholder: !activeCard.description }"
            @click="startEdit('description', activeCard.description || '')"
          >
            {{ activeCard.description || 'Add a description…' }}
            <svg class="edit-icon inline" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
          </p>
        </div>

        <!-- Assignees -->
        <div class="modal-section">
          <span class="section-label">Assignees</span>
          <div class="assignee-list">
            <label v-for="s in staffList" :key="s.id" class="assignee-check">
              <input
                type="checkbox"
                :value="s.id"
                :checked="activeCard.assigned_ids?.includes(s.id)"
                @change="toggleAssignee(s.id)"
              />
              <div class="assignee-avatar">{{ s.full_name.charAt(0) }}</div>
              <span>{{ s.full_name }}</span>
            </label>
          </div>
        </div>

        <!-- Delete card -->
        <div class="modal-footer">
          <button class="btn-delete-card" @click="deleteCard(activeCard)">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
            Delete Card
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, nextTick, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import { api } from '@/utils/api'

const route = useRoute()
const boardId = computed(() => Number(route.params.id))

// ── State ─────────────────────────────────────────────────────
const board         = ref(null)
const lists         = ref([])
const cards         = ref([])
const staffList     = ref([])
const loading       = ref(false)
const error         = ref('')
const activeCard    = ref(null)
const addListOpen   = ref(false)
const addingCardList = ref(null)
const newListTitle  = ref('')
const newCardTitle  = ref('')
const cardInputRef  = ref(null)
const listInputRef  = ref(null)
const dragging      = ref(null)
const dragOverList  = ref(null)
const editingField  = ref(null)
const editValues    = ref({})

// ── Computed ──────────────────────────────────────────────────
function cardsForList(listId) {
  return cards.value
    .filter(c => c.list_id === listId)
    .sort((a, b) => a.position - b.position)
}

// ── Fetch ─────────────────────────────────────────────────────
async function fetchBoard() {
  loading.value = true; error.value = ''
  try {
    const [boardsData, listsData, cardsData, staffData] = await Promise.all([
      api.get('/dashboard/boards'),
      api.get(`/dashboard/lists?board_id=${boardId.value}`),
      api.get(`/dashboard/cards?board_id=${boardId.value}`),
      api.get('/dashboard/staff'),
    ])
    board.value     = boardsData.boards.find(b => b.id === boardId.value) || null
    lists.value     = listsData.lists
    cards.value     = cardsData.cards
    staffList.value = staffData.staff
    if (!board.value) error.value = 'Board not found.'
  } catch (e) {
    error.value = e.message || 'Failed to load board.'
  } finally {
    loading.value = false
  }
}

// ── Add List ──────────────────────────────────────────────────
async function submitList() {
  if (!newListTitle.value.trim()) { addListOpen.value = false; return }
  try {
    await api.post('/dashboard/lists', {
      board_id: boardId.value,
      title: newListTitle.value.trim(),
    })
    newListTitle.value = ''
    addListOpen.value  = false
    fetchBoard()
  } catch (e) {
    alert(e.message)
  }
}

async function deleteList(list) {
  if (!confirm(`Delete list "${list.title}" and all its cards?`)) return
  try {
    await api.delete(`/dashboard/lists?id=${list.id}`)
    fetchBoard()
  } catch (e) { alert(e.message) }
}

// ── Add Card ──────────────────────────────────────────────────
function startAddCard(listId) {
  addingCardList.value = listId
  newCardTitle.value   = ''
  nextTick(() => {
    if (cardInputRef.value?.[0]) cardInputRef.value[0].focus()
    else if (cardInputRef.value) cardInputRef.value.focus()
  })
}

async function submitCard(listId) {
  if (!newCardTitle.value.trim()) { addingCardList.value = null; return }
  try {
    await api.post('/dashboard/cards', {
      list_id: listId,
      title:   newCardTitle.value.trim(),
    })
    newCardTitle.value   = ''
    addingCardList.value = null
    fetchBoard()
  } catch (e) { alert(e.message) }
}

// ── Drag & Drop ───────────────────────────────────────────────
function onDragStart(card, fromListId) {
  dragging.value = { card, fromListId }
}

function onDragEnd() {
  dragging.value    = null
  dragOverList.value = null
}

function onDragOver(listId) {
  dragOverList.value = listId
}

async function onDrop(toListId) {
  if (!dragging.value) return
  const { card, fromListId } = dragging.value
  if (fromListId === toListId) { dragging.value = null; return }

  // Optimistic update
  card.list_id = toListId
  dragging.value = null

  try {
    await api.put(`/dashboard/cards?id=${card.id}`, { list_id: toListId })
  } catch (e) {
    card.list_id = fromListId // revert
    alert(e.message)
  }
}

// ── Card modal ─────────────────────────────────────────────────
function openCard(card) {
  activeCard.value  = { ...card, assigned_ids: [...(card.assigned_ids || [])] }
  editingField.value = null
}

function startEdit(field, value) {
  editingField.value = field
  editValues.value   = { [field]: value }
}

async function saveCard(field) {
  const val = editValues.value[field]
  activeCard.value[field] = val
  editingField.value = null
  // Sync to card in list
  const idx = cards.value.findIndex(c => c.id === activeCard.value.id)
  if (idx !== -1) cards.value[idx][field] = val
  try {
    await api.put(`/dashboard/cards?id=${activeCard.value.id}`, { [field]: val })
  } catch (e) { alert(e.message) }
}

async function saveField(field, val) {
  const idx = cards.value.findIndex(c => c.id === activeCard.value.id)
  if (idx !== -1) cards.value[idx][field] = val
  try {
    await api.put(`/dashboard/cards?id=${activeCard.value.id}`, { [field]: val })
  } catch (e) { alert(e.message) }
}

async function moveCard(card, toListId) {
  const idx = cards.value.findIndex(c => c.id === card.id)
  if (idx !== -1) cards.value[idx].list_id = toListId
  try {
    await api.put(`/dashboard/cards?id=${card.id}`, { list_id: toListId })
  } catch (e) { alert(e.message) }
}

async function toggleAssignee(staffId) {
  const card = activeCard.value
  const ids  = [...(card.assigned_ids || [])]
  const pos  = ids.indexOf(staffId)
  if (pos === -1) ids.push(staffId)
  else ids.splice(pos, 1)
  card.assigned_ids = ids

  // Update display names
  const names = ids.map(id => staffList.value.find(s => s.id === id)?.full_name).filter(Boolean)
  card.assigned_names = names.join(', ')

  // Sync to cards list
  const idx = cards.value.findIndex(c => c.id === card.id)
  if (idx !== -1) {
    cards.value[idx].assigned_ids   = ids
    cards.value[idx].assigned_names = card.assigned_names
  }

  try {
    await api.put(`/dashboard/cards?id=${card.id}`, { assigned_ids: ids })
  } catch (e) { alert(e.message) }
}

async function deleteCard(card) {
  if (!confirm(`Delete card "${card.title}"?`)) return
  activeCard.value = null
  cards.value = cards.value.filter(c => c.id !== card.id)
  try {
    await api.delete(`/dashboard/cards?id=${card.id}`)
  } catch (e) { alert(e.message) }
}

// ── Helpers ───────────────────────────────────────────────────
function truncate(str, n) {
  return str.length > n ? str.slice(0, n) + '…' : str
}

function formatDate(d) {
  if (!d) return ''
  return new Date(d + 'T00:00:00').toLocaleDateString('en-GB', { day: 'numeric', month: 'short' })
}

function isOverdue(d) {
  return new Date(d + 'T00:00:00') < new Date()
}

watch(boardId, fetchBoard)
onMounted(fetchBoard)
</script>

<style scoped>
.board-detail {
  display: flex;
  flex-direction: column;
  height: 100%;
  max-height: calc(100vh - 4rem);
}

/* ── Header ─────────────────────────────────────────────────── */
.board-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
}

.board-header-left {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  flex-wrap: wrap;
  min-width: 0;
}

.back-link {
  color: #6b7280;
  display: flex;
  align-items: center;
  transition: color 0.2s;
}
.back-link:hover { color: #f9fafb; }

.color-dot {
  width: 14px;
  height: 14px;
  border-radius: 50%;
  flex-shrink: 0;
}

.board-title {
  font-size: 1.3rem;
  font-weight: 700;
  color: #f9fafb;
  margin: 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.board-desc {
  font-size: 0.82rem;
  color: #6b7280;
}

.btn-add-list {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  background: rgba(255,255,255,0.06);
  border: 1px solid rgba(255,255,255,0.1);
  color: #9ca3af;
  font-size: 0.85rem;
  font-weight: 500;
  padding: 0.5rem 1rem;
  border-radius: 8px;
  cursor: pointer;
  white-space: nowrap;
  transition: all 0.2s;
  flex-shrink: 0;
}
.btn-add-list:hover { color: #f9fafb; border-color: rgba(255,255,255,0.2); }

/* ── Kanban scroll ─────────────────────────────────────────── */
.kanban-scroll {
  overflow-x: auto;
  flex: 1;
  padding-bottom: 1rem;
}

.kanban-board {
  display: flex;
  gap: 1rem;
  align-items: flex-start;
  min-height: 400px;
  padding-bottom: 1.5rem;
}

/* ── List column ────────────────────────────────────────────── */
.kanban-list {
  width: 280px;
  flex-shrink: 0;
  background: rgba(255,255,255,0.03);
  border: 1px solid rgba(255,255,255,0.07);
  border-radius: 12px;
  display: flex;
  flex-direction: column;
  gap: 0;
  max-height: calc(100vh - 200px);
}

.add-list-col {
  padding: 1rem;
  min-height: 80px;
}

.list-header {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1rem;
  border-bottom: 1px solid rgba(255,255,255,0.05);
}

.list-title {
  font-size: 0.875rem;
  font-weight: 600;
  color: #e5e7eb;
  flex: 1;
}

.list-count {
  background: rgba(255,255,255,0.08);
  color: #6b7280;
  font-size: 0.72rem;
  font-weight: 600;
  border-radius: 20px;
  padding: 2px 7px;
}

.list-delete-btn {
  background: none;
  border: none;
  color: #4b5563;
  cursor: pointer;
  padding: 2px;
  border-radius: 4px;
  display: flex;
  align-items: center;
  transition: color 0.2s;
}
.list-delete-btn:hover { color: #f87171; }

/* ── Card stack ──────────────────────────────────────────────── */
.card-stack {
  flex: 1;
  overflow-y: auto;
  padding: 0.5rem;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  scrollbar-width: thin;
  scrollbar-color: rgba(255,255,255,0.1) transparent;
}

.kanban-card {
  background: #0d1117;
  border: 1px solid rgba(255,255,255,0.07);
  border-radius: 10px;
  padding: 0.85rem;
  cursor: pointer;
  transition: border-color 0.2s, transform 0.15s, box-shadow 0.15s;
  display: flex;
  flex-direction: column;
  gap: 0.4rem;
}

.kanban-card:hover {
  border-color: rgba(255,255,255,0.14);
  box-shadow: 0 2px 12px rgba(0,0,0,0.3);
}

.kanban-card.dragging {
  opacity: 0.4;
  transform: rotate(2deg);
}

/* Priority badge */
.priority-badge {
  display: inline-block;
  font-size: 0.65rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  padding: 2px 7px;
  border-radius: 20px;
  width: fit-content;
}

.priority-badge.lg { font-size: 0.75rem; padding: 3px 10px; }

.priority-badge.low     { background: rgba(34,197,94,0.12);  color: #4ade80; }
.priority-badge.medium  { background: rgba(234,179,8,0.12);  color: #fbbf24; }
.priority-badge.high    { background: rgba(249,115,22,0.12); color: #fb923c; }
.priority-badge.urgent  { background: rgba(239,68,68,0.12);  color: #f87171; }

.card-title {
  font-size: 0.875rem;
  font-weight: 500;
  color: #f9fafb;
  margin: 0;
  line-height: 1.4;
}

.card-desc {
  font-size: 0.78rem;
  color: #6b7280;
  line-height: 1.5;
}

.card-footer {
  display: flex;
  gap: 0.6rem;
  flex-wrap: wrap;
  margin-top: 0.25rem;
}

.card-due, .card-assignees {
  display: inline-flex;
  align-items: center;
  gap: 0.25rem;
  font-size: 0.72rem;
  color: #6b7280;
}

.card-due.overdue { color: #f87171; }

/* Add card */
.btn-add-card {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  background: none;
  border: none;
  color: #4b5563;
  font-size: 0.82rem;
  padding: 0.6rem 1rem;
  cursor: pointer;
  transition: color 0.2s, background 0.2s;
  border-top: 1px solid rgba(255,255,255,0.04);
  border-radius: 0 0 12px 12px;
  width: 100%;
  text-align: left;
}
.btn-add-card:hover { color: #9ca3af; background: rgba(255,255,255,0.03); }

.add-card-form {
  padding: 0.6rem;
  border-top: 1px solid rgba(255,255,255,0.05);
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.card-input, .list-name-input {
  background: rgba(255,255,255,0.06);
  border: 1px solid rgba(79,70,229,0.4);
  border-radius: 8px;
  color: #f9fafb;
  font-size: 0.875rem;
  padding: 0.55rem 0.75rem;
  outline: none;
  resize: vertical;
  font-family: inherit;
  width: 100%;
  box-sizing: border-box;
}

.list-name-input { resize: none; }

.add-card-actions {
  display: flex;
  gap: 0.4rem;
  align-items: center;
}

.btn-add-card-ok {
  background: #4f46e5;
  border: none;
  color: #fff;
  font-size: 0.8rem;
  font-weight: 600;
  padding: 0.4rem 0.9rem;
  border-radius: 6px;
  cursor: pointer;
  transition: opacity 0.2s;
}
.btn-add-card-ok:hover { opacity: 0.9; }

.btn-add-card-cancel {
  background: none;
  border: none;
  color: #6b7280;
  cursor: pointer;
  padding: 4px;
  display: flex;
  align-items: center;
  border-radius: 4px;
  transition: color 0.2s;
}
.btn-add-card-cancel:hover { color: #f9fafb; }

/* State messages */
.state-msg {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 2rem;
  color: #6b7280;
}
.state-msg.error { color: #f87171; }

/* ── Card modal ─────────────────────────────────────────────── */
.modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.7);
  z-index: 200;
  display: flex;
  align-items: flex-start;
  justify-content: center;
  padding: 3rem 1rem;
  overflow-y: auto;
}

.modal {
  background: #0d1117;
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 16px;
  padding: 1.5rem;
  width: 100%;
}

.card-modal { max-width: 540px; display: flex; flex-direction: column; gap: 1rem; }

.modal-header { display: flex; align-items: center; justify-content: space-between; }

.modal-close {
  background: none;
  border: none;
  color: #6b7280;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  display: flex;
  align-items: center;
  transition: color 0.2s;
}
.modal-close:hover { color: #f9fafb; }

.card-modal-title {
  font-size: 1.1rem;
  font-weight: 700;
  color: #f9fafb;
  margin: 0;
  cursor: pointer;
  display: flex;
  align-items: flex-start;
  gap: 0.5rem;
  line-height: 1.4;
}

.edit-icon { color: #4b5563; flex-shrink: 0; margin-top: 3px; }
.edit-icon.inline { display: inline; margin-left: 4px; vertical-align: middle; }
.card-modal-title:hover .edit-icon, .desc-text:hover .edit-icon { color: #818cf8; }

.modal-meta-row {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
}

.meta-field {
  display: flex;
  flex-direction: column;
  gap: 0.3rem;
}

.meta-label, .section-label {
  font-size: 0.72rem;
  font-weight: 600;
  color: #6b7280;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.meta-select, .meta-input {
  background: rgba(255,255,255,0.05);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 6px;
  color: #d1d5db;
  font-size: 0.82rem;
  padding: 0.35rem 0.65rem;
  outline: none;
  cursor: pointer;
  transition: border-color 0.2s;
  font-family: inherit;
}
.meta-select:focus, .meta-input:focus { border-color: #4f46e5; }

.modal-section { display: flex; flex-direction: column; gap: 0.5rem; }

.desc-text {
  font-size: 0.875rem;
  color: #d1d5db;
  line-height: 1.6;
  margin: 0;
  cursor: pointer;
  white-space: pre-wrap;
  min-height: 2.5rem;
}
.desc-text.placeholder { color: #4b5563; font-style: italic; }
.desc-text:hover { color: #f9fafb; }

.edit-field { display: flex; flex-direction: column; gap: 0.5rem; }

.edit-textarea {
  background: rgba(255,255,255,0.06);
  border: 1px solid rgba(79,70,229,0.5);
  border-radius: 8px;
  color: #f9fafb;
  font-size: 0.875rem;
  padding: 0.55rem 0.75rem;
  outline: none;
  resize: vertical;
  font-family: inherit;
  width: 100%;
  box-sizing: border-box;
}

.edit-actions { display: flex; gap: 0.5rem; }

.btn-save {
  background: #4f46e5;
  border: none;
  color: #fff;
  font-size: 0.8rem;
  font-weight: 600;
  padding: 0.4rem 0.9rem;
  border-radius: 6px;
  cursor: pointer;
}
.btn-save:hover { opacity: 0.9; }

.btn-cancel-edit {
  background: rgba(255,255,255,0.05);
  border: 1px solid rgba(255,255,255,0.1);
  color: #9ca3af;
  font-size: 0.8rem;
  padding: 0.4rem 0.75rem;
  border-radius: 6px;
  cursor: pointer;
}

/* Assignees */
.assignee-list {
  display: flex;
  flex-direction: column;
  gap: 0.4rem;
  max-height: 180px;
  overflow-y: auto;
}

.assignee-check {
  display: flex;
  align-items: center;
  gap: 0.65rem;
  padding: 0.35rem 0.5rem;
  border-radius: 6px;
  cursor: pointer;
  font-size: 0.875rem;
  color: #d1d5db;
  transition: background 0.15s;
}
.assignee-check:hover { background: rgba(255,255,255,0.05); }
.assignee-check input { accent-color: #4f46e5; }

.assignee-avatar {
  width: 26px;
  height: 26px;
  border-radius: 50%;
  background: linear-gradient(135deg, #4f46e5, #06b6d4);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.72rem;
  font-weight: 700;
  color: #fff;
  flex-shrink: 0;
}

/* Modal footer */
.modal-footer { border-top: 1px solid rgba(255,255,255,0.06); padding-top: 1rem; }

.btn-delete-card {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  background: none;
  border: none;
  color: #6b7280;
  font-size: 0.82rem;
  cursor: pointer;
  padding: 0.3rem 0;
  transition: color 0.2s;
}
.btn-delete-card:hover { color: #f87171; }

/* Spinner */
.spinner {
  display: inline-block;
  width: 18px;
  height: 18px;
  border: 2px solid rgba(255,255,255,0.2);
  border-top-color: #818cf8;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}

@keyframes spin { to { transform: rotate(360deg); } }
</style>
