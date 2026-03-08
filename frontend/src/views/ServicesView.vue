<template>
  <!-- ===== PAGE HERO ===== -->
  <section class="page-hero dot-grid">
    <div class="container">
      <span class="page-hero-badge">Our Services</span>
      <h1>Everything You Need to<br /><span class="gradient-text">Build & Scale Digitally</span></h1>
      <p>From MVP development to enterprise-grade platforms, we deliver end-to-end software solutions tailored to your business needs.</p>
    </div>
  </section>

  <!-- ===== SERVICES DETAIL ===== -->
  <section class="section">
    <div class="container">
      <div class="services-list">
        <div class="service-detail-card card" v-for="svc in services" :key="svc.title" :id="svc.slug">
          <div class="sdc-left">
            <div class="sdc-icon">{{ svc.icon }}</div>
            <h2>{{ svc.title }}</h2>
            <p>{{ svc.description }}</p>
            <RouterLink to="/contact" class="btn btn-primary">
              Request This Service
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </RouterLink>
          </div>
          <div class="sdc-right">
            <div class="sdc-features">
              <h4>What's Included</h4>
              <ul>
                <li v-for="f in svc.features" :key="f">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                  {{ f }}
                </li>
              </ul>
            </div>
            <div class="sdc-techs">
              <h4>Technologies</h4>
              <div class="tech-tags">
                <span class="tag" v-for="t in svc.techs" :key="t">{{ t }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== PROCESS ===== -->
  <section class="section process-section">
    <div class="container">
      <div class="section-header">
        <span class="section-badge">Our Approach</span>
        <h2>How We Deliver Every Project</h2>
        <p>Our proven agile process keeps you in the loop and ensures nothing slips through the cracks.</p>
      </div>
      <div class="process-timeline">
        <div class="pt-step" v-for="(step, i) in processSteps" :key="step.title">
          <div class="pt-indicator">
            <div class="pt-dot"></div>
            <div class="pt-line" v-if="i < processSteps.length - 1"></div>
          </div>
          <div class="pt-content">
            <div class="pt-number">{{ String(i + 1).padStart(2, '0') }}</div>
            <h3>{{ step.title }}</h3>
            <p>{{ step.desc }}</p>
            <ul class="pt-details">
              <li v-for="d in step.details" :key="d">{{ d }}</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== PRICING / ENGAGEMENT ===== -->
  <section class="section engagement-section">
    <div class="container">
      <div class="section-header">
        <span class="section-badge">Engagement Models</span>
        <h2>Flexible Ways to Work With Us</h2>
        <p>We adapt to how your team and budget works best.</p>
      </div>
      <div class="engage-grid">
        <div class="engage-card card" v-for="e in engagements" :key="e.title" :class="{ featured: e.featured }">
          <div v-if="e.featured" class="engage-badge">Most Popular</div>
          <div class="engage-icon">{{ e.icon }}</div>
          <h3>{{ e.title }}</h3>
          <p>{{ e.desc }}</p>
          <ul class="engage-features">
            <li v-for="f in e.features" :key="f">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              {{ f }}
            </li>
          </ul>
          <RouterLink to="/contact" class="btn" :class="e.featured ? 'btn-primary' : 'btn-outline'">Get Started</RouterLink>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== FAQ ===== -->
  <section class="section faq-section">
    <div class="container">
      <div class="section-header">
        <span class="section-badge">FAQ</span>
        <h2>Common Questions</h2>
      </div>
      <div class="faq-list">
        <div
          class="faq-item"
          v-for="faq in faqs"
          :key="faq.q"
          @click="toggleFaq(faq)"
          :class="{ open: faq.open }"
        >
          <div class="faq-q">
            {{ faq.q }}
            <svg class="faq-arrow" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
          </div>
          <div class="faq-a">{{ faq.a }}</div>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== CTA ===== -->
  <section class="section-sm cta-strip">
    <div class="container">
      <div class="cta-strip-inner">
        <div>
          <h2>Not sure what you need?</h2>
          <p>Book a free 30-minute consultation and let us guide you.</p>
        </div>
        <RouterLink to="/contact" class="btn btn-primary btn-lg">Book Free Consultation</RouterLink>
      </div>
    </div>
  </section>
</template>

<script setup>
import { reactive } from 'vue'

const services = [
  {
    slug: 'web-development',
    icon: '🌐',
    title: 'Web Development',
    description: 'We build fast, secure, and scalable web applications that power modern businesses. From single-page apps to complex multi-tenant platforms, we deliver polished products your users will love.',
    features: [
      'Custom front-end development (Vue.js, React)',
      'Backend APIs & business logic (Laravel, Node.js)',
      'Database design & optimisation',
      'Authentication & role-based access control',
      'Third-party integrations (Stripe, Twilio, etc.)',
      'Performance optimisation & SEO',
      'Responsive, mobile-first design',
      'Admin panels & reporting dashboards'
    ],
    techs: ['Vue.js', 'React', 'Laravel', 'Node.js', 'PHP', 'PostgreSQL', 'MySQL', 'Redis']
  },
  {
    slug: 'mobile-apps',
    icon: '📱',
    title: 'Mobile App Development',
    description: 'We create beautiful, high-performance iOS and Android apps using cross-platform frameworks — so you get two platforms for the investment of one, without sacrificing quality or native feel.',
    features: [
      'Cross-platform iOS & Android development',
      'Native-feel UI with smooth animations',
      'Offline support & data synchronisation',
      'Push notifications & real-time features',
      'App Store & Google Play submission',
      'Biometric authentication & device APIs',
      'In-app purchases & payment integration',
      'Analytics and crash reporting setup'
    ],
    techs: ['React Native', 'Flutter', 'Expo', 'Firebase', 'Swift', 'Kotlin', 'TypeScript']
  },
  {
    slug: 'ui-ux-design',
    icon: '🎨',
    title: 'UI/UX Design',
    description: 'Great design is more than aesthetics — it\'s about creating intuitive experiences that reduce friction and guide users toward their goals. Our designers bridge the gap between beauty and function.',
    features: [
      'User research & persona development',
      'Information architecture & user flows',
      'Wireframing & low-fidelity prototyping',
      'High-fidelity UI design in Figma',
      'Interactive prototypes for user testing',
      'Design system & component library creation',
      'Usability testing & iteration',
      'Handing off specs to developers'
    ],
    techs: ['Figma', 'Adobe XD', 'Prototyping', 'Design Systems', 'Accessibility', 'UX Research']
  }
]

const processSteps = [
  {
    title: 'Discovery & Planning',
    desc: 'We start every engagement with a thorough discovery phase to understand your business, users, and technical requirements.',
    details: [
      'Stakeholder interviews & requirements gathering',
      'Technical feasibility assessment',
      'Project scope, timeline, and budget definition',
      'Technology stack selection'
    ]
  },
  {
    title: 'Design & Prototyping',
    desc: 'Before a single line of code is written, you approve the exact look and feel of your product.',
    details: [
      'Wireframes and user flow diagrams',
      'High-fidelity Figma designs',
      'Clickable prototype for stakeholder review',
      'Design system documentation'
    ]
  },
  {
    title: 'Agile Development',
    desc: 'Two-week sprints with regular demos ensure your product evolves in the right direction.',
    details: [
      'Sprint planning and backlog grooming',
      'Daily standups and progress tracking',
      'Code reviews and automated testing',
      'Bi-weekly demos and feedback loops'
    ]
  },
  {
    title: 'QA & Testing',
    desc: 'Comprehensive testing catches bugs before they reach your users.',
    details: [
      'Unit, integration, and end-to-end testing',
      'Cross-browser and device compatibility',
      'Performance and load testing',
      'Security vulnerability scanning'
    ]
  },
  {
    title: 'Deployment & Launch',
    desc: 'We handle the full deployment pipeline and ensure a smooth, zero-downtime launch.',
    details: [
      'Staging environment for final review',
      'CI/CD pipeline automated deployment',
      'DNS configuration and SSL setup',
      'Go-live monitoring and support'
    ]
  },
  {
    title: 'Ongoing Support',
    desc: 'Post-launch, we stay by your side to maintain, improve, and scale your product.',
    details: [
      'Priority bug fixes and patches',
      'Feature enhancements and roadmap planning',
      'Performance monitoring and optimisation',
      'Dedicated support channel access'
    ]
  }
]

const engagements = [
  {
    icon: '🎯',
    title: 'Fixed-Price Project',
    desc: 'Ideal for well-defined projects with clear scope. We agree on deliverables, timeline, and cost upfront.',
    features: ['Fixed cost & timeline', 'Clear milestones', 'Defined deliverables', 'Best for MVPs & launches'],
    featured: false
  },
  {
    icon: '🔄',
    title: 'Dedicated Team',
    desc: 'A fully-integrated team of developers, designers, and a QA engineer that works as an extension of your company.',
    features: ['Monthly retainer', 'Full-time dedicated engineers', 'Daily standups & sprints', 'Scales up or down easily', 'Best for long-term products'],
    featured: true
  },
  {
    icon: '⏱️',
    title: 'Time & Materials',
    desc: 'Pay only for the hours worked. Perfect for evolving projects where scope may change.',
    features: ['Hourly or daily rates', 'Maximum flexibility', 'Weekly billing', 'Best for ongoing improvements'],
    featured: false
  }
]

const faqs = reactive([
  { q: 'How long does a typical project take?', a: 'It depends on scope. A standard web or mobile app MVP typically takes 8–14 weeks. Larger enterprise systems can take 4–6 months. We provide a detailed timeline estimate during the discovery phase before any work begins.', open: false },
  { q: 'What is your development process?', a: 'We follow an agile methodology with two-week sprints. You\'ll get regular demos, weekly progress reports, and full access to our project management tools so you always know where things stand.', open: false },
  { q: 'Do you offer post-launch support?', a: 'Yes. All projects include a 30-day post-launch warranty. After that, we offer monthly retainer plans for ongoing maintenance, monitoring, and feature development.', open: false },
  { q: 'Who owns the code after delivery?', a: 'You own 100% of the code, IP, and all assets we create for you. We transfer full ownership upon final payment. We retain no rights to your software.', open: false },
  { q: 'Can you work with our existing codebase?', a: 'Absolutely. We regularly take over, modernise, and enhance existing applications. We\'ll start with a technical audit to understand the current system before proposing improvements.', open: false }
])

function toggleFaq(faq) {
  faq.open = !faq.open
}
</script>

<style scoped>
/* Service Detail Cards */
.services-list {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.service-detail-card {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 3rem;
  padding: 2.5rem;
}

.sdc-left { display: flex; flex-direction: column; gap: 1rem; }

.sdc-icon {
  font-size: 2.25rem;
  width: 60px;
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(79, 70, 229, 0.1);
  border-radius: var(--radius-md);
}

.sdc-left h2 {
  font-size: 1.4rem;
  font-weight: 800;
  letter-spacing: -0.02em;
}

.sdc-left p {
  color: var(--text-secondary);
  font-size: 0.95rem;
  line-height: 1.75;
  flex: 1;
}

.sdc-right { display: flex; flex-direction: column; gap: 1.75rem; }

.sdc-features h4,
.sdc-techs h4 {
  font-size: 0.82rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: var(--text-muted);
  margin-bottom: 0.75rem;
}

.sdc-features ul { list-style: none; display: flex; flex-direction: column; gap: 0.5rem; }

.sdc-features li {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  font-size: 0.875rem;
  color: var(--text-secondary);
}

.sdc-features li svg { flex-shrink: 0; }

.tech-tags { display: flex; flex-wrap: wrap; gap: 0.4rem; }

/* Process Timeline */
.process-section { background: var(--bg-secondary); }

.process-timeline {
  display: flex;
  flex-direction: column;
  gap: 0;
  max-width: 800px;
  margin: 0 auto;
}

.pt-step {
  display: grid;
  grid-template-columns: 40px 1fr;
  gap: 1.5rem;
}

.pt-indicator {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.pt-dot {
  width: 14px;
  height: 14px;
  border-radius: 50%;
  background: linear-gradient(135deg, #4f46e5, #06b6d4);
  flex-shrink: 0;
  margin-top: 6px;
  box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.2);
}

.pt-line {
  width: 2px;
  flex: 1;
  background: linear-gradient(to bottom, rgba(79, 70, 229, 0.4), rgba(6, 182, 212, 0.2));
  margin: 6px 0;
  min-height: 40px;
}

.pt-content {
  padding-bottom: 2.5rem;
}

.pt-number {
  font-size: 0.7rem;
  font-weight: 800;
  letter-spacing: 0.1em;
  color: #818cf8;
  margin-bottom: 0.3rem;
}

.pt-content h3 {
  font-size: 1.05rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
}

.pt-content p {
  color: var(--text-secondary);
  font-size: 0.9rem;
  line-height: 1.65;
  margin-bottom: 0.75rem;
}

.pt-details {
  list-style: disc;
  padding-left: 1.25rem;
  display: flex;
  flex-direction: column;
  gap: 0.3rem;
}

.pt-details li {
  font-size: 0.85rem;
  color: var(--text-muted);
}

/* Engagement Models */
.engagement-section { background: var(--bg-primary); }

.engage-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.5rem;
  align-items: start;
}

.engage-card {
  position: relative;
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.engage-card.featured {
  border-color: var(--border-accent);
  box-shadow: var(--shadow-accent);
}

.engage-badge {
  position: absolute;
  top: -12px;
  left: 50%;
  transform: translateX(-50%);
  background: linear-gradient(135deg, #4f46e5, #06b6d4);
  color: #fff;
  font-size: 0.72rem;
  font-weight: 700;
  padding: 0.25rem 0.85rem;
  border-radius: 999px;
  white-space: nowrap;
}

.engage-icon { font-size: 1.75rem; }

.engage-card h3 { font-size: 1.1rem; font-weight: 700; }

.engage-card p {
  color: var(--text-secondary);
  font-size: 0.875rem;
  line-height: 1.65;
  flex: 1;
}

.engage-features {
  list-style: none;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  margin: 0.5rem 0;
}

.engage-features li {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
  color: var(--text-secondary);
}

/* FAQ */
.faq-section { background: var(--bg-secondary); }

.faq-list {
  max-width: 800px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  gap: 0;
}

.faq-item {
  border-bottom: 1px solid var(--border);
  cursor: pointer;
  overflow: hidden;
}

.faq-q {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.25rem 0;
  font-size: 0.95rem;
  font-weight: 600;
  color: var(--text-primary);
  gap: 1rem;
}

.faq-arrow {
  flex-shrink: 0;
  color: var(--text-secondary);
  transition: transform 0.3s;
}

.faq-item.open .faq-arrow {
  transform: rotate(180deg);
}

.faq-a {
  max-height: 0;
  overflow: hidden;
  transition: max-height 0.35s ease, padding 0.35s ease;
  color: var(--text-secondary);
  font-size: 0.9rem;
  line-height: 1.7;
}

.faq-item.open .faq-a {
  max-height: 300px;
  padding-bottom: 1.25rem;
}

/* CTA Strip */
.cta-strip {
  background: var(--bg-secondary);
  border-top: 1px solid var(--border);
}

.cta-strip-inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 2rem;
  flex-wrap: wrap;
}

.cta-strip h2 {
  font-size: 1.4rem;
  font-weight: 800;
  letter-spacing: -0.02em;
  margin-bottom: 0.3rem;
}

.cta-strip p {
  color: var(--text-secondary);
  font-size: 0.9rem;
}

/* Responsive */
@media (max-width: 1024px) {
  .service-detail-card { grid-template-columns: 1fr; gap: 2rem; }
  .engage-grid { grid-template-columns: 1fr; max-width: 480px; margin: 0 auto; }
  .cta-strip-inner { flex-direction: column; text-align: center; }
}

@media (max-width: 768px) {
  .service-detail-card { padding: 1.75rem; }
}
</style>
