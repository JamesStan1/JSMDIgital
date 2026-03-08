<template>
  <!-- ===== PAGE HERO ===== -->
  <section class="page-hero dot-grid">
    <div class="container">
      <span class="page-hero-badge">Portfolio</span>
      <h1>Products We've Built &<br /><span class="gradient-text">Problems We've Solved</span></h1>
      <p>A selection of real projects we've delivered for clients across multiple industries and markets.</p>
    </div>
  </section>

  <!-- ===== FILTERS ===== -->
  <section class="section-sm filter-section">
    <div class="container">
      <div class="filter-tabs">
        <button
          v-for="cat in categories"
          :key="cat.value"
          class="filter-tab"
          :class="{ active: activeFilter === cat.value }"
          @click="activeFilter = cat.value"
        >
          {{ cat.label }}
          <span class="filter-count">{{ cat.value === 'all' ? projects.length : projects.filter(p => p.category === cat.value).length }}</span>
        </button>
      </div>
    </div>
  </section>

  <!-- ===== PROJECT GRID ===== -->
  <section class="section-sm projects-section">
    <div class="container">
      <TransitionGroup name="project-list" tag="div" class="projects-grid">
        <div
          v-for="project in filteredProjects"
          :key="project.id"
          class="project-card card"
        >
          <div class="project-visual" :style="`background: ${project.color}`">
            <img v-if="project.logo" :src="project.logo" :alt="project.title + ' logo'" class="project-logo" />
            <div v-else class="project-mockup">{{ project.icon }}</div>
          </div>
          <div class="project-body">
            <div class="project-meta">
              <span class="tag" :class="`tag-${project.tagColor}`">{{ project.category.replace('-', ' ') }}</span>
              <span class="project-year">{{ project.year }}</span>
            </div>
            <h3>{{ project.title }}</h3>
            <p>{{ project.description }}</p>
            <div class="project-techs">
              <span class="tech-pill" v-for="t in project.techs" :key="t">{{ t }}</span>
            </div>
            <div class="project-footer">
              <div class="project-results">
                <span v-for="r in project.results" :key="r" class="result-item">{{ r }}</span>
              </div>
              <a
                v-if="project.url"
                :href="project.url"
                target="_blank"
                rel="noopener noreferrer"
                class="live-link"
              >
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                Live Site
              </a>
            </div>
          </div>
        </div>
      </TransitionGroup>

      <p v-if="filteredProjects.length === 0" class="no-results">No projects in this category yet.</p>
    </div>
  </section>

  <!-- ===== INDUSTRIES ===== -->
  <section class="section industries-section">
    <div class="container">
      <div class="section-header">
        <span class="section-badge">Industries</span>
        <h2>Sectors We've Worked In</h2>
        <p>We bring domain knowledge and best practices from a wide range of industries.</p>
      </div>
      <div class="industries-grid">
        <div class="industry-card" v-for="ind in industries" :key="ind.name">
          <div class="ind-icon">{{ ind.icon }}</div>
          <h4>{{ ind.name }}</h4>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== CTA ===== -->
  <section class="cta-section">
    <div class="container">
      <div class="cta-box">
        <div class="cta-blob"></div>
        <span class="section-badge">Your Project Could Be Next</span>
        <h2>Ready to Build Something <span class="gradient-text">Amazing?</span></h2>
        <p>Tell us about your idea and we'll tell you how we can bring it to life.</p>
        <RouterLink to="/contact" class="btn btn-primary btn-lg">Start a Conversation</RouterLink>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, computed } from 'vue'

const activeFilter = ref('all')

const categories = [
  { label: 'All Projects',    value: 'all' },
  { label: 'Custom Software', value: 'custom-software' }
]

const projects = [
  {
    id: 1,
    icon: '',
    title: 'Joanna\'s Nook — Hotel Management System',
    category: 'custom-software',
    tagColor: '',
    color: 'linear-gradient(135deg, #0f766e, #0d9488)',
    year: '2025',
    url: 'https://joannasnook.online',
    logo: '/Joannaslogo.png',
    description: 'A full-featured hotel management system for Joanna\'s Nook, covering room reservations, guest check-in/check-out, housekeeping schedules, invoicing, and an admin dashboard with live occupancy analytics.',
    techs: ['Vue.js', 'PHP', 'Laravel', 'MySQL', 'Stripe'],
    results: ['Live & operational', 'Full reservation flow', 'Real-time dashboard']
  }
]

const filteredProjects = computed(() => {
  if (activeFilter.value === 'all') return projects
  return projects.filter(p => p.category === activeFilter.value)
})

const industries = [
  { icon: '🏥', name: 'Healthcare' },
  { icon: '💳', name: 'Fintech' },
  { icon: '🛍️', name: 'E-Commerce' },
  { icon: '🎓', name: 'EdTech' },
  { icon: '📦', name: 'Logistics' },
  { icon: '🍔', name: 'Food & Beverage' },
  { icon: '🏨', name: 'Hospitality' },
  { icon: '🏗️', name: 'Construction' },
  { icon: '⚡', name: 'Energy' },
  { icon: '🎮', name: 'Entertainment' },
  { icon: '🏛️', name: 'Government' },
  { icon: '📡', name: 'Telecoms' }
]
</script>

<style scoped>
/* Filters */
.filter-section { background: var(--bg-secondary); border-bottom: 1px solid var(--border); }

.filter-tabs {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.filter-tab {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  background: transparent;
  border: 1px solid var(--border);
  color: var(--text-secondary);
  font-size: 0.875rem;
  font-weight: 500;
  padding: 0.55rem 1.1rem;
  border-radius: 999px;
  cursor: pointer;
  transition: all 0.2s;
}

.filter-tab:hover {
  border-color: var(--border-accent);
  color: var(--text-primary);
}

.filter-tab.active {
  background: var(--accent-gradient);
  border-color: transparent;
  color: #fff;
  box-shadow: 0 4px 16px rgba(79, 70, 229, 0.35);
}

.filter-count {
  background: rgba(255, 255, 255, 0.15);
  padding: 0.05rem 0.45rem;
  border-radius: 999px;
  font-size: 0.75rem;
  font-weight: 700;
}

/* Project Grid */
.projects-section { background: var(--bg-primary); }

.projects-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.5rem;
}

.project-card {
  display: flex;
  flex-direction: column;
  padding: 0;
  overflow: hidden;
}

.project-visual {
  height: 180px;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  overflow: hidden;
}

.project-logo {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
  display: block;
}

.project-mockup {
  font-size: 4rem;
  filter: drop-shadow(0 4px 16px rgba(0,0,0,0.3));
  animation: floatIcon 4s ease-in-out infinite;
}

@keyframes floatIcon {
  0%, 100% { transform: translateY(0); }
  50%       { transform: translateY(-8px); }
}

.project-body {
  padding: 1.5rem;
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  flex: 1;
}

.project-meta {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.project-year {
  font-size: 0.78rem;
  color: var(--text-muted);
  font-weight: 500;
}

.project-card h3 {
  font-size: 1.05rem;
  font-weight: 700;
}

.project-card p {
  color: var(--text-secondary);
  font-size: 0.875rem;
  line-height: 1.65;
  flex: 1;
}

.project-techs {
  display: flex;
  flex-wrap: wrap;
  gap: 0.35rem;
}

.tech-pill {
  background: var(--bg-surface2);
  color: var(--text-muted);
  font-size: 0.72rem;
  font-weight: 600;
  padding: 0.2rem 0.6rem;
  border-radius: 999px;
}

.project-footer {
  margin-top: auto;
  display: flex;
  flex-direction: column;
  gap: 0.65rem;
}

.live-link {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  font-size: 0.8rem;
  font-weight: 700;
  color: #818cf8;
  text-decoration: none;
  transition: color 0.2s, gap 0.2s;
  align-self: flex-start;
}

.live-link:hover {
  color: #a5b4fc;
  gap: 0.6rem;
}

.project-results {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.result-item {
  font-size: 0.75rem;
  font-weight: 600;
  color: #34d399;
  background: rgba(16, 185, 129, 0.08);
  border: 1px solid rgba(16, 185, 129, 0.2);
  padding: 0.2rem 0.65rem;
  border-radius: 999px;
}

/* Transitions */
.project-list-enter-active,
.project-list-leave-active {
  transition: all 0.35s ease;
}
.project-list-enter-from,
.project-list-leave-to {
  opacity: 0;
  transform: scale(0.95);
}
.project-list-leave-active {
  position: absolute;
}

.no-results {
  text-align: center;
  color: var(--text-muted);
  padding: 4rem 0;
}

/* Industries */
.industries-section { background: var(--bg-secondary); }

.industries-grid {
  display: grid;
  grid-template-columns: repeat(6, 1fr);
  gap: 1rem;
}

.industry-card {
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  padding: 1.5rem 1rem;
  text-align: center;
  transition: border-color var(--transition), transform var(--transition);
  cursor: default;
}

.industry-card:hover {
  border-color: var(--border-accent);
  transform: translateY(-3px);
}

.ind-icon {
  font-size: 1.75rem;
  margin-bottom: 0.6rem;
}

.industry-card h4 {
  font-size: 0.82rem;
  font-weight: 600;
  color: var(--text-secondary);
}

/* CTA */
.cta-section { padding: 5rem 0; background: var(--bg-primary); }

.cta-box {
  position: relative;
  background: var(--bg-surface);
  border: 1px solid var(--border-accent);
  border-radius: var(--radius-xl);
  padding: 5rem 3rem;
  text-align: center;
  overflow: hidden;
}

.cta-blob {
  position: absolute;
  inset: -80px;
  background: radial-gradient(ellipse 60% 60% at 50% 50%, rgba(79, 70, 229, 0.15) 0%, transparent 70%);
  pointer-events: none;
}

.cta-box .section-badge { display: block; margin-bottom: 1.25rem; position: relative; }
.cta-box h2 { font-size: clamp(1.6rem, 3.5vw, 2.5rem); font-weight: 900; letter-spacing: -0.03em; margin-bottom: 1rem; position: relative; }
.cta-box p { color: var(--text-secondary); font-size: 1rem; margin-bottom: 2rem; position: relative; }
.cta-box .btn { position: relative; }

/* Responsive */
@media (max-width: 1200px) {
  .industries-grid { grid-template-columns: repeat(4, 1fr); }
}

@media (max-width: 1024px) {
  .projects-grid { grid-template-columns: repeat(2, 1fr); }
  .industries-grid { grid-template-columns: repeat(3, 1fr); }
}

@media (max-width: 768px) {
  .projects-grid { grid-template-columns: 1fr; }
  .industries-grid { grid-template-columns: repeat(2, 1fr); }
}
</style>
