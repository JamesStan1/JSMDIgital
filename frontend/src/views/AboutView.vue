<template>
  <!-- ===== PAGE HERO ===== -->
  <section class="page-hero dot-grid">
    <div class="container">
      <span class="page-hero-badge">About Us</span>
      <h1>Passionate Builders of<br /><span class="gradient-text">Digital Excellence</span></h1>
      <p>We are a team of engineers, designers, and strategists united by one goal — turning your boldest ideas into world-class digital products.</p>
    </div>
  </section>

  <!-- ===== STORY ===== -->
  <section class="section story-section">
    <div class="container story-inner">
      <div class="story-text">
        <span class="section-badge">Our Story</span>
        <h2>From a Small Studio to a Global Agency</h2>
        <p>JSM Digital was founded in 2025 by a group of passionate developers who were tired of seeing businesses settle for mediocre software. What started as a small specialist team has grown into a dedicated group of 5 experts serving clients across the Philippines, Singapore, and Malaysia.</p>
        <p>We've helped clients launch their first digital products with the same level of commitment and craftsmanship we plan to bring to every future project — big or small.</p>
        <div class="story-milestones">
          <div class="milestone" v-for="m in milestones" :key="m.year">
            <div class="milestone-year gradient-text">{{ m.year }}</div>
            <div class="milestone-text">{{ m.text }}</div>
          </div>
        </div>
      </div>
      <div class="story-stats">
        <div class="story-stat-card" v-for="s in storyStats" :key="s.label">
          <div class="ss-number gradient-text">{{ s.number }}</div>
          <div class="ss-label">{{ s.label }}</div>
          <div class="ss-desc">{{ s.desc }}</div>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== MISSION & VISION ===== -->
  <section class="section mv-section">
    <div class="container">
      <div class="mv-grid">
        <div class="mv-card card">
          <div class="mv-icon">🎯</div>
          <h3>Our Mission</h3>
          <p>To empower businesses of all sizes with custom digital solutions that are reliable, scalable, and built to last. We believe every business deserves software that truly works for them — not the other way around.</p>
        </div>
        <div class="mv-card card">
          <div class="mv-icon">🔭</div>
          <h3>Our Vision</h3>
          <p>To be the most trusted software development partner for growing businesses worldwide — known for transparency, technical excellence, and delivering measurable results that matter to our clients.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== VALUES ===== -->
  <section class="section values-section">
    <div class="container">
      <div class="section-header">
        <span class="section-badge">Our Values</span>
        <h2>The Principles We Build By</h2>
        <p>These aren't just words on a wall — they shape every decision we make, every line of code we write.</p>
      </div>
      <div class="values-grid">
        <div class="value-card" v-for="v in values" :key="v.title">
          <div class="value-icon">{{ v.icon }}</div>
          <h3>{{ v.title }}</h3>
          <p>{{ v.desc }}</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== TEAM ===== -->
  <section class="section team-section">
    <div class="container">
      <div class="section-header">
        <span class="section-badge">The Team</span>
        <h2>The People Behind Your Success</h2>
        <p>A diverse team of passionate experts who bring both craft and strategy to every project.</p>
      </div>
      <div class="team-grid">
        <div class="team-card card" v-for="member in team" :key="member.name">
          <div class="team-avatar">
            <img v-if="member.photo" :src="member.photo" :alt="member.name" class="team-photo" />
            <span v-else>{{ member.initials }}</span>
          </div>
          <h3>{{ member.name }}</h3>
          <div class="team-role">{{ member.role }}</div>
          <p>{{ member.bio }}</p>
          <div class="team-skills">
            <span class="tag" v-for="skill in member.skills" :key="skill">{{ skill }}</span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== CTA ===== -->
  <section class="cta-section">
    <div class="container">
      <div class="cta-box">
        <div class="cta-blob"></div>
        <h2>Ready to Work With Us?</h2>
        <p>Let's discuss your project and explore how JSM Digital can help you build something remarkable.</p>
        <RouterLink to="/contact" class="btn btn-primary btn-lg">Get in Touch</RouterLink>
      </div>
    </div>
  </section>
</template>

<script setup>
const milestones = [
  { year: '2025', text: 'Founded as a specialist software development studio' },
  { year: '2025', text: 'Delivered our first project — Joanna\'s Nook Hotel Management System' },
  { year: '2025', text: 'Expanded reach across Philippines, Singapore, and Malaysia' }
]

const storyStats = [
  { number: '1',  label: 'Projects', desc: 'Successfully delivered for our first client' },
  { number: '1',  label: 'Client', desc: 'Trusted partner we proudly serve' },
  { number: '5',  label: 'Specialists', desc: 'Engineers, designers, and strategists on the team' },
  { number: '3',  label: 'Countries', desc: 'Serving clients in the Philippines, Singapore & Malaysia' }
]

const values = [
  { icon: '🏗️', title: 'Quality First',       desc: 'We never cut corners. Every feature is properly tested, reviewed, and refined before it ships.' },
  { icon: '🔍', title: 'Radical Transparency', desc: 'No hidden costs, no ambiguous timelines. You always know exactly where your project stands.' },
  { icon: '🤝', title: 'Client Partnership',   desc: 'We treat your business goals as our own and make decisions accordingly.' },
  { icon: '🚀', title: 'Continuous Improvement', desc: 'We stay on the cutting edge — constantly learning new tools and frameworks to serve you better.' },
  { icon: '🌍', title: 'Inclusive by Design',  desc: 'Every product we build is accessible, scalable, and designed for a global audience.' },
  { icon: '🔐', title: 'Security Minded',       desc: 'We embed security into every layer of development, following OWASP principles as standard practice.' }
]

const team = [
  {
    initials: 'JSM',
    photo: '/James Stanley Macarulay.png',
    name: 'James Stanley Macarulay',
    role: 'CEO & Founder',
    bio: 'Visionary leader and full-stack developer with a passion for building digital products that make a real difference for businesses across Southeast Asia.',
    skills: ['Leadership', 'Web Development', 'Strategy', 'Laravel']
  },
  {
    initials: 'GMN',
    photo: '/Graciella Mieh Nilama.png',
    name: 'Graciella Mieh Nilama',
    role: 'Head Designer',
    bio: 'Creative designer who crafts beautiful, user-centred interfaces. Bridges the gap between aesthetics and function to deliver experiences users love.',
    skills: ['UI/UX', 'Figma', 'Prototyping', 'Design Systems']
  }
]
</script>

<style scoped>
/* Story */
.story-section { background: var(--bg-primary); }

.story-inner {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 5rem;
  align-items: start;
}

.story-text .section-badge { margin-bottom: 1rem; }

.story-text h2 {
  font-size: clamp(1.6rem, 3vw, 2.25rem);
  font-weight: 800;
  margin-bottom: 1.25rem;
  letter-spacing: -0.02em;
}

.story-text p {
  color: var(--text-secondary);
  font-size: 0.95rem;
  line-height: 1.75;
  margin-bottom: 1rem;
}

.story-milestones {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  margin-top: 2rem;
  padding-left: 1rem;
  border-left: 2px solid var(--border-accent);
}

.milestone {
  display: flex;
  gap: 1rem;
  align-items: baseline;
}

.milestone-year {
  font-weight: 800;
  font-size: 0.9rem;
  min-width: 44px;
  letter-spacing: -0.02em;
}

.milestone-text {
  color: var(--text-secondary);
  font-size: 0.88rem;
  line-height: 1.5;
}

.story-stats {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.25rem;
  position: sticky;
  top: 120px;
}

.story-stat-card {
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-lg);
  padding: 1.75rem;
  transition: border-color var(--transition), transform var(--transition);
}

.story-stat-card:hover {
  border-color: var(--border-accent);
  transform: translateY(-3px);
}

.ss-number {
  font-size: 2.25rem;
  font-weight: 900;
  letter-spacing: -0.04em;
  margin-bottom: 0.2rem;
  line-height: 1;
}

.ss-label {
  font-size: 0.95rem;
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: 0.3rem;
}

.ss-desc {
  font-size: 0.82rem;
  color: var(--text-muted);
  line-height: 1.5;
}

/* Mission & Vision */
.mv-section { background: var(--bg-secondary); }

.mv-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
}

.mv-card { display: flex; flex-direction: column; gap: 0.75rem; }

.mv-icon {
  font-size: 2rem;
  width: 52px;
  height: 52px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(79, 70, 229, 0.1);
  border-radius: var(--radius-sm);
}

.mv-card h3 {
  font-size: 1.2rem;
  font-weight: 700;
}

.mv-card p {
  color: var(--text-secondary);
  font-size: 0.95rem;
  line-height: 1.7;
}

/* Values */
.values-section { background: var(--bg-primary); }

.values-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.5rem;
}

.value-card {
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-lg);
  padding: 2rem;
  transition: border-color var(--transition), transform var(--transition), box-shadow var(--transition);
}

.value-card:hover {
  border-color: var(--border-accent);
  box-shadow: var(--shadow-accent);
  transform: translateY(-4px);
}

.value-icon {
  font-size: 1.75rem;
  margin-bottom: 1rem;
  width: 48px;
  height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(79, 70, 229, 0.08);
  border-radius: 10px;
}

.value-card h3 {
  font-size: 1rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
}

.value-card p {
  color: var(--text-secondary);
  font-size: 0.88rem;
  line-height: 1.65;
}

/* Team */
.team-section { background: var(--bg-secondary); }

.team-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1.5rem;
  max-width: 720px;
  margin: 0 auto;
}

.team-card {
  display: flex;
  flex-direction: column;
  gap: 0.6rem;
  text-align: center;
}

.team-avatar {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  background: linear-gradient(135deg, #4f46e5, #06b6d4);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.25rem;
  font-weight: 800;
  color: #fff;
  margin: 0 auto 0.5rem;
  overflow: hidden;
  border: 3px solid rgba(79, 70, 229, 0.4);
}

.team-photo {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center top;
  border-radius: 50%;
}

.team-card h3 {
  font-size: 1rem;
  font-weight: 700;
}

.team-role {
  font-size: 0.8rem;
  color: #818cf8;
  font-weight: 600;
  margin-bottom: 0.25rem;
}

.team-card p {
  color: var(--text-secondary);
  font-size: 0.83rem;
  line-height: 1.6;
  flex: 1;
}

.team-skills {
  display: flex;
  flex-wrap: wrap;
  gap: 0.35rem;
  justify-content: center;
  margin-top: 0.25rem;
}

/* CTA */
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
  inset: -80px;
  background: radial-gradient(ellipse 60% 60% at 50% 50%, rgba(79, 70, 229, 0.15) 0%, transparent 70%);
  pointer-events: none;
}

.cta-box h2 {
  font-size: clamp(1.6rem, 3.5vw, 2.5rem);
  font-weight: 900;
  letter-spacing: -0.03em;
  margin-bottom: 1rem;
  position: relative;
}

.cta-box p {
  color: var(--text-secondary);
  font-size: 1rem;
  margin-bottom: 2rem;
  position: relative;
}

.cta-box .btn { position: relative; }

/* Responsive */
@media (max-width: 1024px) {
  .story-inner { grid-template-columns: 1fr; gap: 3rem; }
  .story-stats { position: static; }
  .team-grid { grid-template-columns: repeat(2, 1fr); }
  .values-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 768px) {
  .mv-grid { grid-template-columns: 1fr; }
  .team-grid { grid-template-columns: 1fr; }
  .values-grid { grid-template-columns: 1fr; }
  .story-stats { grid-template-columns: 1fr 1fr; }
}
</style>
