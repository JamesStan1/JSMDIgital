<template>
  <!-- ===== PAGE HERO ===== -->
  <section class="page-hero dot-grid">
    <div class="container">
      <span class="page-hero-badge">Contact Us</span>
      <h1>Let's Build Something<br /><span class="gradient-text">Great Together</span></h1>
      <p>Tell us about your project and we'll get back to you within one business day with a free consultation.</p>
    </div>
  </section>

  <!-- ===== CONTACT CONTENT ===== -->
  <section class="section contact-section">
    <div class="container contact-grid">

      <!-- Left: Contact Info -->
      <aside class="contact-info">
        <h2>Get in Touch</h2>
        <p>We'd love to hear about your project. Fill out the form or reach us directly through any of the channels below.</p>

        <div class="info-items">
          <div class="info-item" v-for="item in contactInfo" :key="item.label">
            <div class="info-icon">{{ item.icon }}</div>
            <div>
              <div class="info-label">{{ item.label }}</div>
              <a :href="item.href" class="info-value">{{ item.value }}</a>
            </div>
          </div>
        </div>

        <div class="divider"></div>

        <div class="hours">
          <h4>Business Hours</h4>
          <div class="hour-row" v-for="h in hours" :key="h.days">
            <span class="hour-days">{{ h.days }}</span>
            <span class="hour-time">{{ h.time }}</span>
          </div>
        </div>

        <div class="divider"></div>

        <div class="response-promise">
          <div class="rp-icon">⚡</div>
          <div>
            <strong>Fast Response Guaranteed</strong>
            <span>We respond to all inquiries within 1 business day.</span>
          </div>
        </div>
      </aside>

      <!-- Right: Tabs for Contact / Inquiry forms -->
      <div class="contact-forms">
        <div class="form-tabs">
          <button
            class="form-tab"
            :class="{ active: activeTab === 'contact' }"
            @click="activeTab = 'contact'"
          >
            Contact Us
          </button>
          <button
            class="form-tab"
            :class="{ active: activeTab === 'inquiry' }"
            @click="activeTab = 'inquiry'"
          >
            Project Inquiry
          </button>
        </div>

        <!-- Contact Form -->
        <Transition name="tab-fade" mode="out-in">
          <form
            v-if="activeTab === 'contact'"
            key="contact"
            class="form-box"
            @submit.prevent="submitContact"
            novalidate
          >
            <div class="form-row">
              <div class="form-group">
                <label class="form-label" for="c-name">Full Name *</label>
                <input id="c-name" type="text" class="form-input" v-model="contactForm.name" placeholder="Jane Smith" required />
                <span class="field-error" v-if="contactErrors.name">{{ contactErrors.name }}</span>
              </div>
              <div class="form-group">
                <label class="form-label" for="c-email">Email Address *</label>
                <input id="c-email" type="email" class="form-input" v-model="contactForm.email" placeholder="jane@company.com" required />
                <span class="field-error" v-if="contactErrors.email">{{ contactErrors.email }}</span>
              </div>
            </div>

            <div class="form-group">
              <label class="form-label" for="c-subject">Subject *</label>
              <input id="c-subject" type="text" class="form-input" v-model="contactForm.subject" placeholder="How can we help?" required />
              <span class="field-error" v-if="contactErrors.subject">{{ contactErrors.subject }}</span>
            </div>

            <div class="form-group">
              <label class="form-label" for="c-message">Message *</label>
              <textarea id="c-message" class="form-textarea" v-model="contactForm.message" placeholder="Tell us more about your question or project..." rows="5" required></textarea>
              <span class="field-error" v-if="contactErrors.message">{{ contactErrors.message }}</span>
            </div>

            <div class="alert alert-success" v-if="contactStatus === 'success'">
              ✅ Message sent! We'll get back to you within 1 business day.
            </div>
            <div class="alert alert-error" v-if="contactStatus === 'error'">
              ❌ Something went wrong. Please try again or email us directly.
            </div>

            <button type="submit" class="btn btn-primary btn-lg submit-btn" :disabled="contactLoading">
              <span v-if="contactLoading" class="spinner"></span>
              <span v-else>
                Send Message
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/></svg>
              </span>
            </button>
          </form>

          <!-- Project Inquiry Form -->
          <form
            v-else
            key="inquiry"
            class="form-box"
            @submit.prevent="submitInquiry"
            novalidate
          >
            <div class="form-row">
              <div class="form-group">
                <label class="form-label" for="i-name">Full Name *</label>
                <input id="i-name" type="text" class="form-input" v-model="inquiryForm.name" placeholder="Jane Smith" required />
                <span class="field-error" v-if="inquiryErrors.name">{{ inquiryErrors.name }}</span>
              </div>
              <div class="form-group">
                <label class="form-label" for="i-email">Email Address *</label>
                <input id="i-email" type="email" class="form-input" v-model="inquiryForm.email" placeholder="jane@company.com" required />
                <span class="field-error" v-if="inquiryErrors.email">{{ inquiryErrors.email }}</span>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label class="form-label" for="i-company">Company / Organisation</label>
                <input id="i-company" type="text" class="form-input" v-model="inquiryForm.company" placeholder="Acme Corp" />
              </div>
              <div class="form-group">
                <label class="form-label" for="i-phone">Phone Number</label>
                <input id="i-phone" type="tel" class="form-input" v-model="inquiryForm.phone" placeholder="+1 555 000 0000" />
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label class="form-label" for="i-type">Project Type *</label>
                <select id="i-type" class="form-select form-input" v-model="inquiryForm.project_type" required>
                  <option value="" disabled>Select a project type</option>
                  <option value="web-app">Web Application</option>
                  <option value="mobile-app">Mobile App (iOS / Android)</option>
                  <option value="custom-software">Custom Software / System</option>
                  <option value="e-commerce">E-Commerce Platform</option>
                  <option value="api">API Development / Integration</option>
                  <option value="ui-ux">UI/UX Design</option>
                  <option value="other">Other</option>
                </select>
                <span class="field-error" v-if="inquiryErrors.project_type">{{ inquiryErrors.project_type }}</span>
              </div>
              <div class="form-group">
                <label class="form-label" for="i-budget">Estimated Budget</label>
                <select id="i-budget" class="form-select form-input" v-model="inquiryForm.budget">
                  <option value="" disabled>Select a budget range</option>
                  <option value="under-5k">Under $5,000</option>
                  <option value="5k-15k">$5,000 – $15,000</option>
                  <option value="15k-50k">$15,000 – $50,000</option>
                  <option value="50k-100k">$50,000 – $100,000</option>
                  <option value="over-100k">$100,000+</option>
                  <option value="not-sure">Not sure yet</option>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label class="form-label" for="i-timeline">Desired Timeline</label>
              <select id="i-timeline" class="form-select form-input" v-model="inquiryForm.timeline">
                <option value="" disabled>When do you need this done?</option>
                <option value="asap">As soon as possible</option>
                <option value="1-2-months">Within 1–2 months</option>
                <option value="3-6-months">3–6 months</option>
                <option value="6-plus-months">More than 6 months</option>
                <option value="flexible">I'm flexible</option>
              </select>
            </div>

            <div class="form-group">
              <label class="form-label" for="i-description">Project Description *</label>
              <textarea id="i-description" class="form-textarea" v-model="inquiryForm.description" placeholder="Please describe your project in as much detail as possible — goals, features, target users, existing systems, and anything else that's relevant..." rows="6" required></textarea>
              <span class="field-error" v-if="inquiryErrors.description">{{ inquiryErrors.description }}</span>
            </div>

            <div class="alert alert-success" v-if="inquiryStatus === 'success'">
              ✅ Inquiry submitted! Our team will review it and reach out within 1 business day.
            </div>
            <div class="alert alert-error" v-if="inquiryStatus === 'error'">
              ❌ Submission failed. Please try again or email us at hello@jsmdigital.com.
            </div>

            <button type="submit" class="btn btn-primary btn-lg submit-btn" :disabled="inquiryLoading">
              <span v-if="inquiryLoading" class="spinner"></span>
              <span v-else>
                Submit Inquiry
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
              </span>
            </button>
          </form>
        </Transition>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, reactive } from 'vue'

const activeTab = ref('contact')

/* ---- Contact Info ---- */
const contactInfo = [
  { icon: '✉️', label: 'Email',   href: 'mailto:hello@jsmdigital.com', value: 'hello@jsmdigital.com' },
  { icon: '📞', label: 'Phone',   href: 'tel:+639754276334',           value: '0975 427 6334 / 0965 904 7100' },
  { icon: '📍', label: 'Address', href: '#',                           value: 'Balingasag, Misamis Oriental, Philippines' }
]

const hours = [
  { days: 'Monday – Friday', time: '9:00 AM – 6:00 PM PST' },
  { days: 'Saturday',        time: '10:00 AM – 2:00 PM PST' },
  { days: 'Sunday',          time: 'Closed' }
]

/* ---- Contact Form ---- */
const contactForm = reactive({ name: '', email: '', subject: '', message: '' })
const contactErrors = reactive({})
const contactLoading = ref(false)
const contactStatus = ref('')

function validateEmail(val) {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)
}

function validateContactForm() {
  Object.keys(contactErrors).forEach(k => delete contactErrors[k])
  let valid = true
  if (!contactForm.name.trim()) { contactErrors.name = 'Name is required.'; valid = false }
  if (!contactForm.email.trim() || !validateEmail(contactForm.email)) { contactErrors.email = 'A valid email is required.'; valid = false }
  if (!contactForm.subject.trim()) { contactErrors.subject = 'Subject is required.'; valid = false }
  if (!contactForm.message.trim() || contactForm.message.trim().length < 10) { contactErrors.message = 'Please enter a message (at least 10 characters).'; valid = false }
  return valid
}

async function submitContact() {
  if (!validateContactForm()) return
  contactLoading.value = true
  contactStatus.value = ''
  try {
    const res = await fetch('/api/contact', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        name: contactForm.name.trim(),
        email: contactForm.email.trim(),
        subject: contactForm.subject.trim(),
        message: contactForm.message.trim()
      })
    })
    const data = await res.json()
    if (res.ok && data.success) {
      contactStatus.value = 'success'
      Object.keys(contactForm).forEach(k => (contactForm[k] = ''))
    } else {
      contactStatus.value = 'error'
    }
  } catch {
    contactStatus.value = 'error'
  } finally {
    contactLoading.value = false
  }
}

/* ---- Inquiry Form ---- */
const inquiryForm = reactive({
  name: '', email: '', company: '', phone: '',
  project_type: '', budget: '', timeline: '', description: ''
})
const inquiryErrors = reactive({})
const inquiryLoading = ref(false)
const inquiryStatus = ref('')

function validateInquiryForm() {
  Object.keys(inquiryErrors).forEach(k => delete inquiryErrors[k])
  let valid = true
  if (!inquiryForm.name.trim()) { inquiryErrors.name = 'Name is required.'; valid = false }
  if (!inquiryForm.email.trim() || !validateEmail(inquiryForm.email)) { inquiryErrors.email = 'A valid email is required.'; valid = false }
  if (!inquiryForm.project_type) { inquiryErrors.project_type = 'Please select a project type.'; valid = false }
  if (!inquiryForm.description.trim() || inquiryForm.description.trim().length < 20) { inquiryErrors.description = 'Please provide more details (at least 20 characters).'; valid = false }
  return valid
}

async function submitInquiry() {
  if (!validateInquiryForm()) return
  inquiryLoading.value = true
  inquiryStatus.value = ''
  try {
    const res = await fetch('/api/inquiry', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        name: inquiryForm.name.trim(),
        email: inquiryForm.email.trim(),
        company: inquiryForm.company.trim(),
        phone: inquiryForm.phone.trim(),
        project_type: inquiryForm.project_type,
        budget: inquiryForm.budget,
        timeline: inquiryForm.timeline,
        description: inquiryForm.description.trim()
      })
    })
    const data = await res.json()
    if (res.ok && data.success) {
      inquiryStatus.value = 'success'
      Object.keys(inquiryForm).forEach(k => (inquiryForm[k] = ''))
    } else {
      inquiryStatus.value = 'error'
    }
  } catch {
    inquiryStatus.value = 'error'
  } finally {
    inquiryLoading.value = false
  }
}
</script>

<style scoped>
/* Contact Grid */
.contact-section { background: var(--bg-primary); }

.contact-grid {
  display: grid;
  grid-template-columns: 360px 1fr;
  gap: 4rem;
  align-items: start;
}

/* Contact Info */
.contact-info h2 {
  font-size: 1.5rem;
  font-weight: 800;
  margin-bottom: 0.75rem;
  letter-spacing: -0.02em;
}

.contact-info > p {
  color: var(--text-secondary);
  font-size: 0.92rem;
  line-height: 1.7;
  margin-bottom: 2rem;
}

.info-items {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
  margin-bottom: 1.5rem;
}

.info-item {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
}

.info-icon {
  width: 42px;
  height: 42px;
  background: rgba(79, 70, 229, 0.1);
  border: 1px solid rgba(79, 70, 229, 0.2);
  border-radius: var(--radius-sm);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.1rem;
  flex-shrink: 0;
}

.info-label {
  font-size: 0.75rem;
  font-weight: 600;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: var(--text-muted);
  margin-bottom: 0.15rem;
}

.info-value {
  font-size: 0.9rem;
  color: var(--text-primary);
  font-weight: 500;
  text-decoration: none;
  transition: color 0.2s;
}
.info-value:hover { color: #818cf8; }

/* Hours */
.hours h4 {
  font-size: 0.82rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--text-muted);
  margin-bottom: 0.85rem;
}

.hour-row {
  display: flex;
  justify-content: space-between;
  font-size: 0.875rem;
  margin-bottom: 0.4rem;
}

.hour-days { color: var(--text-secondary); }
.hour-time { color: var(--text-primary); font-weight: 500; }

/* Response Promise */
.response-promise {
  display: flex;
  align-items: flex-start;
  gap: 0.75rem;
  background: rgba(79, 70, 229, 0.06);
  border: 1px solid rgba(79, 70, 229, 0.15);
  border-radius: var(--radius-md);
  padding: 1rem 1.25rem;
}

.rp-icon { font-size: 1.25rem; flex-shrink: 0; margin-top: 2px; }

.response-promise div {
  display: flex;
  flex-direction: column;
  gap: 0.15rem;
}

.response-promise strong {
  font-size: 0.88rem;
  font-weight: 700;
}

.response-promise span {
  font-size: 0.82rem;
  color: var(--text-secondary);
}

/* Form Area */
.contact-forms {
  position: sticky;
  top: 100px;
}

.form-tabs {
  display: flex;
  gap: 0;
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-md) var(--radius-md) 0 0;
  overflow: hidden;
}

.form-tab {
  flex: 1;
  background: transparent;
  border: none;
  color: var(--text-secondary);
  font-size: 0.9rem;
  font-weight: 600;
  padding: 0.9rem 1.5rem;
  cursor: pointer;
  transition: color 0.2s, background 0.2s;
  border-bottom: 2px solid transparent;
}

.form-tab:hover { color: var(--text-primary); background: rgba(255,255,255,0.03); }

.form-tab.active {
  color: var(--text-primary);
  border-bottom-color: var(--accent-primary);
  background: rgba(79, 70, 229, 0.05);
}

.form-box {
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-top: none;
  border-radius: 0 0 var(--radius-md) var(--radius-md);
  padding: 2rem;
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.25rem;
}

.field-error {
  font-size: 0.78rem;
  color: var(--error);
  margin-top: 0.2rem;
}

.submit-btn {
  align-self: flex-start;
  min-width: 180px;
  justify-content: center;
}

.submit-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none !important;
}

/* Spinner */
.spinner {
  width: 16px;
  height: 16px;
  border: 2px solid rgba(255,255,255,0.3);
  border-top-color: #fff;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
  display: inline-block;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* Tab Transition */
.tab-fade-enter-active,
.tab-fade-leave-active {
  transition: opacity 0.2s, transform 0.2s;
}
.tab-fade-enter-from,
.tab-fade-leave-to {
  opacity: 0;
  transform: translateY(8px);
}

/* Responsive */
@media (max-width: 1024px) {
  .contact-grid { grid-template-columns: 1fr; gap: 3rem; }
  .contact-forms { position: static; }
}

@media (max-width: 640px) {
  .form-row { grid-template-columns: 1fr; }
  .form-box { padding: 1.5rem; }
  .submit-btn { width: 100%; }
}
</style>
