<template>
  <div class="inquiries-view">
    <!-- Page header -->
    <div class="page-header">
      <div>
        <h1 class="page-title">Client Inquiries</h1>
        <p class="page-sub">Manage and assign incoming project inquiries.</p>
      </div>
    </div>

    <!-- Stats -->
    <div class="stats-row">
      <div class="stat-card" v-for="s in stats" :key="s.label">
        <span class="stat-num">{{ s.value }}</span>
        <span class="stat-label">{{ s.label }}</span>
      </div>
    </div>

    <!-- Filters -->
    <div class="filters">
      <button
        v-for="f in filters"
        :key="f.value"
        class="filter-btn"
        :class="{ active: activeFilter === f.value }"
        @click="setFilter(f.value)"
      >{{ f.label }}</button>
    </div>

    <!-- Table -->
    <div class="table-wrap">
      <div v-if="loading" class="state-msg">
        <span class="spinner"></span> Loading inquiries…
      </div>
      <div v-else-if="error" class="state-msg error">{{ error }}</div>
      <div v-else-if="!inquiries.length" class="state-msg">No inquiries found.</div>

      <table v-else class="inq-table">
        <thead>
          <tr>
            <th>Client</th>
            <th>Project Type</th>
            <th>Budget</th>
            <th>Status</th>
            <th>Assigned To</th>
            <th>Date</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <template v-for="inq in inquiries" :key="inq.id">
            <tr class="inq-row" :class="{ expanded: expanded === inq.id }" @click="toggle(inq.id)">
              <td>
                <div class="client-name">{{ inq.name }}</div>
                <div class="client-email">{{ inq.email }}</div>
              </td>
              <td><span class="tag">{{ formatType(inq.project_type) }}</span></td>
              <td class="muted">{{ inq.budget || '—' }}</td>
              <td>
                <select
                  class="status-select"
                  :value="inq.status"
                  @change.stop="updateStatus(inq, $event.target.value)"
                  @click.stop
                >
                  <option value="new">New</option>
                  <option value="in_review">In Review</option>
                  <option value="responded">Responded</option>
                </select>
              </td>
              <td>
                <span v-if="inq.assigned_to_name" class="assignee">{{ inq.assigned_to_name }}</span>
                <span v-else class="muted">Unassigned</span>
              </td>
              <td class="muted date-cell">{{ formatDate(inq.created_at) }}</td>
              <td @click.stop>
                <button
                  v-if="canAssign"
                  class="assign-btn"
                  @click="openAssign(inq)"
                  title="Assign to staff"
                >
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                  Assign
                </button>
              </td>
            </tr>

            <!-- Expanded detail row -->
            <tr v-if="expanded === inq.id" class="detail-row">
              <td colspan="7">
                <div class="detail-grid">
                  <div>
                    <span class="detail-label">Company</span>
                    <span>{{ inq.company || '—' }}</span>
                  </div>
                  <div>
                    <span class="detail-label">Phone</span>
                    <span>{{ inq.phone || '—' }}</span>
                  </div>
                  <div>
                    <span class="detail-label">Timeline</span>
                    <span>{{ inq.timeline || '—' }}</span>
                  </div>
                  <div v-if="inq.assigned_by_name">
                    <span class="detail-label">Assigned By</span>
                    <span>{{ inq.assigned_by_name }}</span>
                  </div>
                </div>
                <div class="detail-description">
                  <span class="detail-label">Project Description</span>
                  <p>{{ inq.description }}</p>
                </div>
              </td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div v-if="pages > 1" class="pagination">
      <button :disabled="page <= 1" @click="goPage(page - 1)">&lsaquo; Prev</button>
      <span>Page {{ page }} of {{ pages }}</span>
      <button :disabled="page >= pages" @click="goPage(page + 1)">Next &rsaquo;</button>
    </div>

    <!-- Assign Modal -->
    <div v-if="assignModal" class="modal-backdrop" @click.self="assignModal = null">
      <div class="modal">
        <h2 class="modal-title">Assign Inquiry</h2>
        <p class="modal-sub">Assign <strong>{{ assignModal.name }}</strong>'s inquiry to a staff member.</p>

        <div class="field">
          <label>Staff Member</label>
          <select v-model="assignStaffId">
            <option value="">— Select staff —</option>
            <option v-for="s in staffList" :key="s.id" :value="s.id">
              {{ s.full_name }} ({{ s.role }})
            </option>
          </select>
        </div>

        <div class="field">
          <label>Notes (optional)</label>
          <textarea v-model="assignNotes" rows="3" placeholder="Any notes for the assignee…"></textarea>
        </div>

        <p v-if="assignError" class="field-error">{{ assignError }}</p>

        <div class="modal-actions">
          <button class="btn-secondary" @click="assignModal = null">Cancel</button>
          <button class="btn-primary" :disabled="assignLoading" @click="submitAssign">
            <span v-if="assignLoading" class="spinner sm"></span>
            {{ assignLoading ? 'Assigning…' : 'Assign' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useAuth } from '@/stores/auth'
import { api } from '@/utils/api'

const { user } = useAuth()
const canAssign = computed(() => ['admin', 'manager'].includes(user.value?.role))

// ── State ────────────────────────────────────────────────────
const inquiries   = ref([])
const staffList   = ref([])
const loading     = ref(false)
const error       = ref('')
const page        = ref(1)
const pages       = ref(1)
const total       = ref(0)
const activeFilter = ref('')
const expanded    = ref(null)

// ── Assign modal ─────────────────────────────────────────────
const assignModal   = ref(null)
const assignStaffId = ref('')
const assignNotes   = ref('')
const assignError   = ref('')
const assignLoading = ref(false)

// ── Filters ──────────────────────────────────────────────────
const filters = [
  { label: 'All',        value: '' },
  { label: 'New',        value: 'new' },
  { label: 'In Review',  value: 'in_review' },
  { label: 'Responded',  value: 'responded' },
]

// ── Stats ────────────────────────────────────────────────────
const stats = computed(() => [
  { label: 'Total',      value: total.value },
  { label: 'New',        value: inquiries.value.filter(i => i.status === 'new').length },
  { label: 'In Review',  value: inquiries.value.filter(i => i.status === 'in_review').length },
  { label: 'Responded',  value: inquiries.value.filter(i => i.status === 'responded').length },
])

// ── Fetch ────────────────────────────────────────────────────
async function fetchInquiries() {
  loading.value = true
  error.value   = ''
  try {
    const params = new URLSearchParams({ page: page.value })
    if (activeFilter.value) params.set('status', activeFilter.value)
    const data = await api.get(`/dashboard/inquiries?${params}`)
    inquiries.value = data.inquiries
    pages.value     = data.pages
    total.value     = data.total
  } catch (e) {
    error.value = e.message || 'Failed to load inquiries.'
  } finally {
    loading.value = false
  }
}

async function fetchStaff() {
  if (!canAssign.value) return
  try {
    const data = await api.get('/dashboard/staff')
    staffList.value = data.staff
  } catch (_) { /* non-critical */ }
}

// ── Actions ──────────────────────────────────────────────────
async function updateStatus(inq, newStatus) {
  const oldStatus = inq.status
  inq.status = newStatus
  try {
    await api.patch(`/dashboard/inquiries?id=${inq.id}`, { status: newStatus })
  } catch {
    inq.status = oldStatus
  }
}

function toggle(id) {
  expanded.value = expanded.value === id ? null : id
}

function setFilter(val) {
  activeFilter.value = val
  page.value = 1
}

function goPage(n) {
  page.value = n
}

function openAssign(inq) {
  assignModal.value   = inq
  assignStaffId.value = inq.assigned_to_id || ''
  assignNotes.value   = ''
  assignError.value   = ''
}

async function submitAssign() {
  if (!assignStaffId.value) { assignError.value = 'Please select a staff member.'; return }
  assignLoading.value = true
  assignError.value   = ''
  try {
    await api.post('/dashboard/assign', {
      inquiry_id: assignModal.value.id,
      staff_id:   Number(assignStaffId.value),
      notes:      assignNotes.value,
    })
    assignModal.value = null
    fetchInquiries()
  } catch (e) {
    assignError.value = e.message || 'Assignment failed.'
  } finally {
    assignLoading.value = false
  }
}

// ── Helpers ──────────────────────────────────────────────────
function formatType(t) {
  return (t || '').replace(/-/g, ' ').replace(/\b\w/g, c => c.toUpperCase())
}

function formatDate(d) {
  if (!d) return '—'
  return new Date(d).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' })
}

// ── Watchers ─────────────────────────────────────────────────
watch([page, activeFilter], fetchInquiries)
onMounted(() => { fetchInquiries(); fetchStaff() })
</script>

<style scoped>
.inquiries-view { max-width: 1200px; }

.page-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  margin-bottom: 1.75rem;
}

.page-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #f9fafb;
  margin: 0 0 0.25rem;
}

.page-sub {
  color: #6b7280;
  font-size: 0.875rem;
  margin: 0;
}

/* Stats */
.stats-row {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.stat-card {
  background: rgba(255,255,255,0.03);
  border: 1px solid rgba(255,255,255,0.07);
  border-radius: 12px;
  padding: 1rem 1.25rem;
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.stat-num {
  font-size: 1.75rem;
  font-weight: 700;
  color: #f9fafb;
  line-height: 1;
}

.stat-label {
  font-size: 0.78rem;
  color: #6b7280;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

/* Filters */
.filters {
  display: flex;
  gap: 0.5rem;
  margin-bottom: 1.25rem;
  flex-wrap: wrap;
}

.filter-btn {
  background: rgba(255,255,255,0.04);
  border: 1px solid rgba(255,255,255,0.08);
  color: #9ca3af;
  font-size: 0.85rem;
  font-weight: 500;
  padding: 0.4rem 1rem;
  border-radius: 20px;
  cursor: pointer;
  transition: all 0.2s;
}

.filter-btn:hover,
.filter-btn.active {
  background: rgba(79, 70, 229, 0.15);
  border-color: rgba(79, 70, 229, 0.4);
  color: #818cf8;
}

/* Table */
.table-wrap {
  background: rgba(255,255,255,0.02);
  border: 1px solid rgba(255,255,255,0.07);
  border-radius: 12px;
  overflow-x: auto;
}

.inq-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.875rem;
}

.inq-table thead tr {
  border-bottom: 1px solid rgba(255,255,255,0.06);
}

.inq-table th {
  padding: 0.85rem 1rem;
  text-align: left;
  color: #6b7280;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  white-space: nowrap;
}

.inq-row {
  border-bottom: 1px solid rgba(255,255,255,0.04);
  cursor: pointer;
  transition: background 0.15s;
}

.inq-row:hover {
  background: rgba(255,255,255,0.03);
}

.inq-row.expanded {
  background: rgba(79, 70, 229, 0.05);
}

.inq-table td {
  padding: 0.85rem 1rem;
  color: #d1d5db;
  vertical-align: middle;
}

.client-name { font-weight: 600; color: #f9fafb; }
.client-email { font-size: 0.78rem; color: #6b7280; margin-top: 2px; }

.tag {
  background: rgba(79, 70, 229, 0.12);
  color: #818cf8;
  font-size: 0.75rem;
  font-weight: 600;
  padding: 0.25rem 0.65rem;
  border-radius: 20px;
  white-space: nowrap;
}

.muted { color: #6b7280; }
.date-cell { white-space: nowrap; font-size: 0.8rem; }

.status-select {
  background: rgba(255,255,255,0.06);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 6px;
  color: #d1d5db;
  font-size: 0.8rem;
  padding: 0.3rem 0.6rem;
  cursor: pointer;
  outline: none;
}

.assignee {
  background: rgba(6, 182, 212, 0.1);
  color: #22d3ee;
  font-size: 0.78rem;
  font-weight: 500;
  padding: 0.25rem 0.65rem;
  border-radius: 20px;
}

.assign-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  background: rgba(255,255,255,0.05);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 6px;
  color: #9ca3af;
  font-size: 0.8rem;
  padding: 0.3rem 0.75rem;
  cursor: pointer;
  transition: all 0.2s;
  white-space: nowrap;
}

.assign-btn:hover {
  border-color: rgba(79,70,229,0.5);
  color: #818cf8;
  background: rgba(79,70,229,0.1);
}

/* Detail row */
.detail-row td {
  background: rgba(79,70,229,0.04);
  padding: 1rem 1.25rem 1.25rem;
  border-bottom: 1px solid rgba(255,255,255,0.06);
}

.detail-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
  gap: 1rem;
  margin-bottom: 1rem;
}

.detail-grid > div,
.detail-description {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.detail-label {
  font-size: 0.72rem;
  font-weight: 600;
  color: #6b7280;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.detail-description p {
  color: #d1d5db;
  font-size: 0.875rem;
  line-height: 1.6;
  margin: 0;
  white-space: pre-wrap;
}

/* State messages */
.state-msg {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 2.5rem;
  justify-content: center;
  color: #6b7280;
  font-size: 0.9rem;
}

.state-msg.error { color: #f87171; }

/* Pagination */
.pagination {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-top: 1.25rem;
  justify-content: center;
  color: #9ca3af;
  font-size: 0.875rem;
}

.pagination button {
  background: rgba(255,255,255,0.05);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 6px;
  color: #9ca3af;
  padding: 0.4rem 1rem;
  cursor: pointer;
  transition: all 0.2s;
}

.pagination button:hover:not(:disabled) {
  border-color: rgba(79,70,229,0.5);
  color: #818cf8;
}

.pagination button:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

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
  max-width: 420px;
}

.modal-title {
  font-size: 1.15rem;
  font-weight: 700;
  color: #f9fafb;
  margin: 0 0 0.4rem;
}

.modal-sub {
  color: #6b7280;
  font-size: 0.875rem;
  margin: 0 0 1.5rem;
}

.field {
  display: flex;
  flex-direction: column;
  gap: 0.4rem;
  margin-bottom: 1rem;
}

.field label {
  font-size: 0.8rem;
  font-weight: 500;
  color: #9ca3af;
}

.field select,
.field textarea {
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
}

.field select:focus,
.field textarea:focus {
  border-color: #4f46e5;
}

.field textarea { resize: vertical; font-family: inherit; }

.field-error {
  color: #f87171;
  font-size: 0.8rem;
}

.modal-actions {
  display: flex;
  gap: 0.75rem;
  justify-content: flex-end;
  margin-top: 1.5rem;
}

.btn-primary, .btn-secondary {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.55rem 1.25rem;
  border-radius: 8px;
  font-size: 0.875rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-primary {
  background: linear-gradient(135deg, #4f46e5, #06b6d4);
  border: none;
  color: #fff;
}

.btn-primary:hover:not(:disabled) { opacity: 0.9; }
.btn-primary:disabled { opacity: 0.5; cursor: not-allowed; }

.btn-secondary {
  background: rgba(255,255,255,0.05);
  border: 1px solid rgba(255,255,255,0.1);
  color: #9ca3af;
}

.btn-secondary:hover { border-color: rgba(255,255,255,0.2); color: #f9fafb; }

/* Spinner */
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

.spinner.sm { width: 14px; height: 14px; }

@keyframes spin { to { transform: rotate(360deg); } }

/* Responsive */
@media (max-width: 640px) {
  .stats-row { grid-template-columns: repeat(2, 1fr); }
  
  .inq-table th:nth-child(3),
  .inq-table td:nth-child(3),
  .inq-table th:nth-child(6),
  .inq-table td:nth-child(6) { display: none; }
}
</style>
