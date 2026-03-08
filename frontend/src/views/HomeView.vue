<template>
  <!-- ===== HERO ===== -->
  <section class="hero dot-grid">
    <div class="hero-blobs">
      <div class="blob blob-1"></div>
      <div class="blob blob-2"></div>
    </div>
    <div class="container hero-inner">
      <div class="hero-content" ref="heroRef">
        <span class="hero-badge">🚀 Serving Businesses Across the Philippines, Singapore & Malaysia</span>
        <h1>
          Build Digital Products<br />
          That <span class="gradient-text">Drive Growth</span>
        </h1>
        <p>
          JSM Digital is a full-service software development agency specialising in custom
          web and mobile applications. We transform your vision into powerful, scalable
          digital solutions.
        </p>
        <div class="hero-actions">
          <RouterLink to="/contact" class="btn btn-primary btn-lg">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            Start a Project
          </RouterLink>
          <RouterLink to="/portfolio" class="btn btn-outline btn-lg">View Our Work</RouterLink>
        </div>
      </div>

      <!-- Floating Code Card -->
      <div class="hero-visual">
        <div class="code-card">
          <div class="code-card-header">
            <span class="dot red"></span><span class="dot yellow"></span><span class="dot green"></span>
            <span class="code-filename">project.config.js</span>
          </div>
          <pre class="code-body"><code><span class="c-keyword">const</span> <span class="c-var">project</span> = {
  <span class="c-key">client</span>: <span class="c-str">"Your Business"</span>,
  <span class="c-key">type</span>: <span class="c-str">"Web Application"</span>,
  <span class="c-key">stack</span>: [<span class="c-str">"Vue.js"</span>, <span class="c-str">"Laravel"</span>],
  <span class="c-key">quality</span>: <span class="c-num">100</span>,
  <span class="c-key">delivered</span>: <span class="c-bool">true</span> ✓
};</code></pre>
        </div>
        <div class="floating-badge badge-react">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="#06b6d4"><circle cx="12" cy="12" r="3"/></svg>
          Vue.js
        </div>
        <div class="floating-badge badge-php">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="#818cf8"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
          PHP / Laravel
        </div>
      </div>
    </div>
  </section>

  <!-- ===== STATS ===== -->
  <section class="stats-section">
    <div class="container">
      <div class="stats-grid">
        <div class="stat-item" v-for="s in stats" :key="s.label">
          <div class="stat-number gradient-text">{{ s.number }}</div>
          <div class="stat-label">{{ s.label }}</div>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== SERVICES OVERVIEW ===== -->
  <section class="section services-section">
    <div class="container">
      <div class="section-header">
        <span class="section-badge">What We Do</span>
        <h2>Comprehensive Digital Services</h2>
        <p>From concept to deployment, we handle every aspect of your digital journey with precision and care.</p>
      </div>
      <div class="services-grid">
        <div class="service-card card" v-for="svc in services" :key="svc.title">
          <div class="svc-icon">{{ svc.icon }}</div>
          <h3>{{ svc.title }}</h3>
          <p>{{ svc.desc }}</p>
          <RouterLink to="/services" class="svc-link">
            Learn more
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
          </RouterLink>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== PROCESS ===== -->
  <section class="section process-section">
    <div class="container">
      <div class="section-header">
        <span class="section-badge">How We Work</span>
        <h2>Our Development Process</h2>
        <p>A streamlined, transparent process designed to deliver results on time and on budget.</p>
      </div>
      <div class="process-steps">
        <div class="process-step" v-for="(step, i) in process" :key="step.title">
          <div class="step-number">{{ String(i + 1).padStart(2, '0') }}</div>
          <div class="step-content">
            <h3>{{ step.title }}</h3>
            <p>{{ step.desc }}</p>
          </div>
          <div class="step-connector" v-if="i < process.length - 1"></div>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== WHY US ===== -->
  <section class="section why-section">
    <div class="container why-inner">
      <div class="why-text">
        <span class="section-badge">Why JSM Digital</span>
        <h2>Built Different. Built Better.</h2>
        <p>We don't just write code — we craft solutions engineered to scale, perform, and impress.</p>
        <ul class="why-list">
          <li v-for="item in whyUs" :key="item.title">
            <div class="why-icon">{{ item.icon }}</div>
            <div>
              <strong>{{ item.title }}</strong>
              <span>{{ item.desc }}</span>
            </div>
          </li>
        </ul>
        <RouterLink to="/about" class="btn btn-outline">Meet Our Team</RouterLink>
      </div>
      <div class="why-cards">
        <div class="why-stat-card" v-for="c in whyCards" :key="c.label">
          <div class="wcard-icon">{{ c.icon }}</div>
          <div class="wcard-value gradient-text">{{ c.value }}</div>
          <div class="wcard-label">{{ c.label }}</div>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== TECH STACK ===== -->
  <section class="section-sm tech-section">
    <div class="container">
      <div class="section-header">
        <span class="section-badge">Technology Stack</span>
        <h2>We Use the Best Tools</h2>
      </div>
      <div class="tech-grid">
        <div class="tech-badge" v-for="t in techStack" :key="t">{{ t }}</div>
      </div>
    </div>
  </section>

  <!-- ===== TESTIMONIALS ===== -->
  <section class="section testimonials-section">
    <div class="container">
      <div class="section-header">
        <span class="section-badge">Testimonials</span>
        <h2>What Our Clients Say</h2>
      </div>
      <div class="testimonials-grid">
        <div class="testimonial-card card" v-for="t in testimonials" :key="t.name">
          <div class="t-stars">★★★★★</div>
          <p class="t-quote">"{{ t.quote }}"</p>
          <div class="t-author">
            <div class="t-avatar">{{ t.name.charAt(0) }}</div>
            <div>
              <div class="t-name">{{ t.name }}</div>
              <div class="t-role">{{ t.role }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== CTA BANNER ===== -->
  <section class="cta-section">
    <div class="container">
      <div class="cta-box">
        <div class="cta-blob"></div>
        <span class="section-badge">Ready to Build?</span>
        <h2>Let's Create Something <span class="gradient-text">Extraordinary</span></h2>
        <p>Tell us about your project and get a free consultation with our development team.</p>
        <div class="cta-actions">
          <RouterLink to="/contact" class="btn btn-primary btn-lg">Start Your Project</RouterLink>
          <RouterLink to="/portfolio" class="btn btn-outline btn-lg">See Our Work</RouterLink>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
const stats = [
  { number: '1',    label: 'Projects Delivered' },
  { number: '1',    label: 'Happy Client' },
  { number: '2025', label: 'Founded' },
  { number: '99%',  label: 'Client Satisfaction' }
]

const services = [
  { icon: '🌐', title: 'Web Development',        desc: 'Scalable, high-performance web apps built with modern frameworks like Vue.js, React, and Laravel.' },
  { icon: '📱', title: 'Mobile App Development', desc: 'Cross-platform iOS and Android applications with seamless UX using React Native and Flutter.' },
  { icon: '🎨', title: 'UI/UX Design',           desc: 'User-centered design that converts — from wireframes to polished, interactive prototypes.' }
]

const process = [
  { title: 'Discovery',    desc: 'We deep-dive into your goals, audience, and requirements to shape the perfect solution.' },
  { title: 'Design',       desc: 'Our designers create wireframes and high-fidelity prototypes for your approval before any code is written.' },
  { title: 'Development',  desc: 'Agile sprints with continuous delivery ensure you see progress every step of the way.' },
  { title: 'Launch &amp; Support', desc: 'We deploy to production and provide ongoing maintenance and feature enhancements.' }
]

const whyUs = [
  { icon: '⚡', title: 'Senior-Level Engineers',  desc: 'Every project is led by experienced full-stack developers, not junior contractors.' },
  { icon: '🔒', title: 'Security First',           desc: 'We follow OWASP best practices in every codebase we ship.' },
  { icon: '📊', title: 'Transparent Reporting',   desc: 'Weekly updates, live dashboards, and no surprises — ever.' },
  { icon: '🤝', title: 'Long-Term Partnership',   desc: 'We grow with your business, not just deliver and disappear.' }
]

const whyCards = [
  { icon: '🚀', value: '3×', label: 'Faster Time to Market' },
  { icon: '💰', value: '40%', label: 'Cost Savings vs In-House' },
  { icon: '⭐', value: '4.9/5', label: 'Average Client Rating' },
  { icon: '🔄', value: '95%', label: 'Client Retention Rate' }
]

const techStack = [
  'Vue.js', 'React', 'React Native', 'Flutter', 'TypeScript', 'Node.js',
  'PHP', 'Laravel', 'Python', 'MySQL', 'PostgreSQL', 'MongoDB',
  'Redis', 'Docker', 'AWS', 'GCP', 'CI/CD', 'REST API', 'GraphQL', 'Tailwind CSS'
]

const testimonials = [
  {
    quote: 'JSM Digital delivered our platform in record time. The quality of code and design exceeded all our expectations. Truly a world-class team.',
    name: 'Sarah Mitchell', role: 'CEO, Nexus Health Tech'
  },
  {
    quote: 'From day one, the team understood our vision and translated it into a beautiful, functional product. Our mobile app has a 4.8-star rating on the App Store.',
    name: 'David Chen', role: 'Founder, TradeFlow App'
  },
  {
    quote: 'Professional, responsive, and technically brilliant. We have launched three projects with JSM Digital and plan to continue the partnership long term.',
    name: 'Amara Osei', role: 'CTO, FinEdge Africa'
  }
]
</script>

<style scoped>
/* ===== HERO ===== */
.hero {
  position: relative;
  min-height: 100vh;
  display: flex;
  align-items: center;
  padding: 8rem 0 5rem;
  overflow: hidden;
}

.hero-blobs {
  position: absolute;
  inset: 0;
  pointer-events: none;
}

.blob {
  position: absolute;
  border-radius: 50%;
  filter: blur(120px);
  opacity: 0.18;
}

.blob-1 {
  width: 600px;
  height: 600px;
  background: #4f46e5;
  top: -150px;
  right: -100px;
  animation: float 8s ease-in-out infinite;
}

.blob-2 {
  width: 400px;
  height: 400px;
  background: #06b6d4;
  bottom: -100px;
  left: -80px;
  animation: float 10s ease-in-out infinite reverse;
}

@keyframes float {
  0%, 100% { transform: translateY(0) scale(1); }
  50%       { transform: translateY(-30px) scale(1.05); }
}

.hero-inner {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 4rem;
  align-items: center;
  position: relative;
  z-index: 1;
}

.hero-content {
  animation: fadeUp 0.8s ease both;
}

.hero-badge {
  display: inline-block;
  background: rgba(79, 70, 229, 0.12);
  border: 1px solid rgba(79, 70, 229, 0.3);
  color: #a5b4fc;
  font-size: 0.82rem;
  font-weight: 600;
  padding: 0.4rem 1rem;
  border-radius: 999px;
  margin-bottom: 1.5rem;
  letter-spacing: 0.02em;
}

.hero-content h1 {
  font-size: clamp(2.25rem, 5vw, 3.5rem);
  font-weight: 900;
  line-height: 1.12;
  margin-bottom: 1.4rem;
  letter-spacing: -0.03em;
}

.hero-content p {
  color: var(--text-secondary);
  font-size: 1.1rem;
  line-height: 1.75;
  margin-bottom: 2.25rem;
  max-width: 480px;
}

.hero-actions {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
}

/* Code Card */
.hero-visual {
  position: relative;
  animation: fadeUp 0.8s ease 0.2s both;
}

.code-card {
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-lg);
  overflow: hidden;
  box-shadow: var(--shadow-lg);
}

.code-card-header {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background: var(--bg-surface2);
  padding: 0.85rem 1.25rem;
  border-bottom: 1px solid var(--border);
  font-size: 0.8rem;
  color: var(--text-muted);
}

.dot { width: 10px; height: 10px; border-radius: 50%; }
.dot.red    { background: #ef4444; }
.dot.yellow { background: #f59e0b; }
.dot.green  { background: #10b981; }
.code-filename { margin-left: 0.5rem; }

.code-body {
  padding: 1.5rem 1.75rem;
  font-family: 'Menlo', 'Monaco', 'Courier New', monospace;
  font-size: 0.88rem;
  line-height: 1.8;
  white-space: pre;
  overflow-x: auto;
}

.c-keyword { color: #c792ea; }
.c-var     { color: #82aaff; }
.c-key     { color: #80cbc4; }
.c-str     { color: #c3e88d; }
.c-num     { color: #f78c6c; }
.c-bool    { color: #ff5370; }

/* Floating Badges */
.floating-badge {
  position: absolute;
  display: flex;
  align-items: center;
  gap: 0.45rem;
  background: var(--bg-surface);
  border: 1px solid var(--border-accent);
  color: var(--text-secondary);
  font-size: 0.8rem;
  font-weight: 600;
  padding: 0.5rem 1rem;
  border-radius: 999px;
  box-shadow: var(--shadow-md);
  animation: float 6s ease-in-out infinite;
}

.badge-react {
  top: -18px;
  right: -24px;
  animation-delay: -2s;
}

.badge-php {
  bottom: -18px;
  left: -20px;
  animation-delay: -4s;
}

@keyframes fadeUp {
  from { opacity: 0; transform: translateY(24px); }
  to   { opacity: 1; transform: translateY(0); }
}

/* ===== STATS ===== */
.stats-section {
  padding: 2rem 0;
  border-top: 1px solid var(--border);
  border-bottom: 1px solid var(--border);
  background: var(--bg-secondary);
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 2rem;
}

.stat-item {
  text-align: center;
  padding: 1.75rem 1rem;
}

.stat-number {
  font-size: 2.5rem;
  font-weight: 900;
  letter-spacing: -0.03em;
  margin-bottom: 0.3rem;
}

.stat-label {
  color: var(--text-secondary);
  font-size: 0.9rem;
  font-weight: 500;
}

/* ===== SERVICES ===== */
.services-section {
  background: var(--bg-primary);
}

.services-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.5rem;
}

.service-card {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.svc-icon {
  font-size: 2rem;
  width: 52px;
  height: 52px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(79, 70, 229, 0.08);
  border-radius: var(--radius-sm);
}

.service-card h3 {
  font-size: 1.05rem;
  font-weight: 700;
  color: var(--text-primary);
}

.service-card p {
  color: var(--text-secondary);
  font-size: 0.9rem;
  line-height: 1.65;
  flex: 1;
}

.svc-link {
  display: inline-flex;
  align-items: center;
  gap: 0.3rem;
  color: #818cf8;
  font-size: 0.875rem;
  font-weight: 600;
  text-decoration: none;
  margin-top: auto;
  transition: gap 0.2s, color 0.2s;
}

.svc-link:hover {
  color: #a5b4fc;
  gap: 0.6rem;
}

/* ===== PROCESS ===== */
.process-section {
  background: var(--bg-secondary);
}

.process-steps {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 2rem;
  position: relative;
}

.process-step {
  position: relative;
  padding: 2rem;
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-lg);
  transition: border-color var(--transition), box-shadow var(--transition);
}

.process-step:hover {
  border-color: var(--border-accent);
  box-shadow: var(--shadow-accent);
}

.step-number {
  font-size: 2.5rem;
  font-weight: 900;
  color: rgba(79, 70, 229, 0.2);
  letter-spacing: -0.05em;
  margin-bottom: 0.75rem;
  line-height: 1;
}

.step-content h3 {
  font-size: 1rem;
  font-weight: 700;
  margin-bottom: 0.6rem;
  color: var(--text-primary);
}

.step-content p {
  color: var(--text-secondary);
  font-size: 0.875rem;
  line-height: 1.65;
}

/* ===== WHY US ===== */
.why-section {
  background: var(--bg-primary);
}

.why-inner {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 5rem;
  align-items: center;
}

.why-text .section-badge { margin-bottom: 1rem; }
.why-text h2 {
  font-size: clamp(1.75rem, 3.5vw, 2.5rem);
  font-weight: 800;
  margin-bottom: 1rem;
  letter-spacing: -0.02em;
}
.why-text > p {
  color: var(--text-secondary);
  font-size: 1rem;
  line-height: 1.7;
  margin-bottom: 2rem;
}

.why-list {
  list-style: none;
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
  margin-bottom: 2rem;
}

.why-list li {
  display: flex;
  gap: 1rem;
  align-items: flex-start;
}

.why-icon {
  width: 38px;
  height: 38px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(79, 70, 229, 0.1);
  border-radius: 8px;
  font-size: 1rem;
  flex-shrink: 0;
}

.why-list li div {
  display: flex;
  flex-direction: column;
  gap: 0.15rem;
}

.why-list li strong {
  font-size: 0.95rem;
  font-weight: 700;
  color: var(--text-primary);
}

.why-list li span {
  font-size: 0.875rem;
  color: var(--text-secondary);
  line-height: 1.55;
}

.why-cards {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.25rem;
}

.why-stat-card {
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-lg);
  padding: 1.75rem;
  text-align: center;
  transition: border-color var(--transition), transform var(--transition);
}

.why-stat-card:hover {
  border-color: var(--border-accent);
  transform: translateY(-4px);
}

.wcard-icon { font-size: 1.75rem; margin-bottom: 0.5rem; }
.wcard-value {
  font-size: 1.75rem;
  font-weight: 900;
  letter-spacing: -0.03em;
  margin-bottom: 0.3rem;
}
.wcard-label {
  font-size: 0.82rem;
  color: var(--text-secondary);
  font-weight: 500;
}

/* ===== TECH STACK ===== */
.tech-section {
  background: var(--bg-secondary);
}

.tech-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
  justify-content: center;
  max-width: 900px;
  margin: 0 auto;
}

.tech-badge {
  background: var(--bg-surface);
  border: 1px solid var(--border);
  color: var(--text-secondary);
  font-size: 0.82rem;
  font-weight: 600;
  padding: 0.45rem 1rem;
  border-radius: 999px;
  transition: border-color var(--transition), color var(--transition), background var(--transition);
  cursor: default;
}

.tech-badge:hover {
  border-color: var(--border-accent);
  color: var(--text-primary);
  background: rgba(79, 70, 229, 0.07);
}

/* ===== TESTIMONIALS ===== */
.testimonials-section { background: var(--bg-primary); }

.testimonials-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.5rem;
}

.testimonial-card {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.t-stars {
  color: #f59e0b;
  font-size: 0.95rem;
  letter-spacing: 0.1em;
}

.t-quote {
  color: var(--text-secondary);
  font-size: 0.92rem;
  line-height: 1.75;
  flex: 1;
  font-style: italic;
}

.t-author {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.t-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: linear-gradient(135deg, #4f46e5, #06b6d4);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 1rem;
  color: #fff;
  flex-shrink: 0;
}

.t-name {
  font-size: 0.9rem;
  font-weight: 700;
  color: var(--text-primary);
}

.t-role {
  font-size: 0.8rem;
  color: var(--text-muted);
}

/* ===== CTA BANNER ===== */
.cta-section {
  padding: 5rem 0;
  background: var(--bg-secondary);
}

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
  inset: -100px;
  background: radial-gradient(ellipse 60% 60% at 50% 50%, rgba(79, 70, 229, 0.15) 0%, transparent 70%);
  pointer-events: none;
}

.cta-box h2 {
  font-size: clamp(1.75rem, 4vw, 2.75rem);
  font-weight: 900;
  letter-spacing: -0.03em;
  margin-bottom: 1rem;
  position: relative;
}

.cta-box p {
  color: var(--text-secondary);
  font-size: 1.05rem;
  margin-bottom: 2.25rem;
  position: relative;
}

.cta-actions {
  display: flex;
  gap: 1rem;
  justify-content: center;
  flex-wrap: wrap;
  position: relative;
}

.cta-box .section-badge {
  display: block;
  margin-bottom: 1.25rem;
  position: relative;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 1024px) {
  .hero-inner { grid-template-columns: 1fr; gap: 3rem; text-align: center; }
  .hero-content p { margin: 0 auto 2rem; }
  .hero-actions { justify-content: center; }
  .hero-visual { max-width: 500px; margin: 0 auto; }
  .services-grid { grid-template-columns: repeat(2, 1fr); }
  .process-steps { grid-template-columns: repeat(2, 1fr); }
  .why-inner { grid-template-columns: 1fr; gap: 3rem; }
  .testimonials-grid { grid-template-columns: 1fr; }
}

@media (max-width: 768px) {
  .stats-grid { grid-template-columns: repeat(2, 1fr); }
  .services-grid { grid-template-columns: 1fr; }
  .process-steps { grid-template-columns: 1fr; }
  .why-cards { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 500px) {
  .stats-grid { grid-template-columns: 1fr 1fr; }
  .why-cards { grid-template-columns: 1fr 1fr; }
  .cta-box { padding: 3rem 1.5rem; }
}
</style>
