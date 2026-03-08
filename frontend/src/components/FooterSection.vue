<template>
  <footer class="footer">
    <div class="footer-top">
      <div class="container footer-grid">
        <!-- Brand -->
        <div class="footer-brand">
          <RouterLink to="/" class="logo">
            <img src="/JSM Digital Logo.png" alt="JSM Digital" class="logo-img" />
          </RouterLink>
          <p class="brand-desc">
            We build custom web and mobile applications that help businesses grow, innovate, and succeed in the digital age.
          </p>
          <div class="socials">
            <a href="#" aria-label="LinkedIn" target="_blank" rel="noopener">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>
            </a>
            <a href="#" aria-label="GitHub" target="_blank" rel="noopener">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"/></svg>
            </a>
            <a href="#" aria-label="Twitter / X" target="_blank" rel="noopener">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/></svg>
            </a>
            <a href="#" aria-label="Instagram" target="_blank" rel="noopener">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
            </a>
          </div>
        </div>

        <!-- Quick Links -->
        <div class="footer-col">
          <h4>Company</h4>
          <ul>
            <li><RouterLink to="/">Home</RouterLink></li>
            <li><RouterLink to="/about">About Us</RouterLink></li>
            <li><RouterLink to="/services">Services</RouterLink></li>
            <li><RouterLink to="/portfolio">Portfolio</RouterLink></li>
            <li><RouterLink to="/contact">Contact</RouterLink></li>
          </ul>
        </div>

        <!-- Services -->
        <div class="footer-col">
          <h4>Services</h4>
          <ul>
            <li><RouterLink to="/services">Web Development</RouterLink></li>
            <li><RouterLink to="/services">Mobile App Development</RouterLink></li>
            <li><RouterLink to="/services">UI/UX Design</RouterLink></li>
          </ul>
        </div>

        <!-- Contact -->
        <div class="footer-col">
          <h4>Get in Touch</h4>
          <ul class="contact-list">
            <li>
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
              <a href="mailto:hello@jsmdigital.com">hello@jsmdigital.com</a>
            </li>
            <li>
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.64 3.42 2 2 0 0 1 3.62 1.22h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 8.91a16 16 0 0 0 6 6l.77-.77a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
              <a href="tel:+639754276334">0975 427 6334 / 0965 904 7100</a>
            </li>
            <li>
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
              <span>Balingasag, Misamis Oriental, Philippines</span>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <div class="footer-bottom">
      <div class="container footer-bottom-inner">
        <p>&copy; {{ currentYear }} JSM Digital. All rights reserved.</p>
        <div class="footer-legal">
          <a href="#">Privacy Policy</a>
          <a href="#">Terms of Service</a>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
const currentYear = new Date().getFullYear()
</script>

<style scoped>
.footer {
  background: var(--bg-secondary);
  border-top: 1px solid var(--border);
  margin-top: auto;
}

.footer-top {
  padding: 5rem 0;
}

.footer-grid {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr 1.5fr;
  gap: 3rem;
}

/* Brand */
.logo {
  display: inline-flex;
  align-items: center;
  text-decoration: none;
  margin-bottom: 1rem;
}

.logo-img {
  height: 40px;
  width: auto;
  display: block;
  object-fit: contain;
}

.brand-desc {
  color: var(--text-secondary);
  font-size: 0.9rem;
  line-height: 1.7;
  margin-bottom: 1.5rem;
  max-width: 280px;
}

.socials {
  display: flex;
  gap: 0.75rem;
}

.socials a {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  background: var(--bg-surface2);
  border: 1px solid var(--border);
  border-radius: 8px;
  color: var(--text-secondary);
  transition: color 0.2s, border-color 0.2s, background 0.2s;
}

.socials a:hover {
  color: var(--text-primary);
  border-color: var(--border-accent);
  background: rgba(79, 70, 229, 0.1);
}

/* Columns */
.footer-col h4 {
  font-size: 0.85rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--text-primary);
  margin-bottom: 1.25rem;
}

.footer-col ul {
  list-style: none;
  display: flex;
  flex-direction: column;
  gap: 0.65rem;
}

.footer-col ul li a {
  color: var(--text-secondary);
  font-size: 0.9rem;
  transition: color 0.2s;
  text-decoration: none;
}

.footer-col ul li a:hover {
  color: var(--text-primary);
}

/* Contact List */
.contact-list li {
  display: flex;
  align-items: flex-start;
  gap: 0.6rem;
  color: var(--text-secondary);
  font-size: 0.9rem;
}

.contact-list li svg {
  flex-shrink: 0;
  margin-top: 3px;
  color: #818cf8;
}

.contact-list a {
  color: var(--text-secondary);
  transition: color 0.2s;
}
.contact-list a:hover { color: var(--text-primary); }

/* Bottom Bar */
.footer-bottom {
  border-top: 1px solid var(--border);
  padding: 1.5rem 0;
}

.footer-bottom-inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 1rem;
}

.footer-bottom p {
  color: var(--text-muted);
  font-size: 0.86rem;
}

.footer-legal {
  display: flex;
  gap: 1.5rem;
}
.footer-legal a {
  color: var(--text-muted);
  font-size: 0.86rem;
  transition: color 0.2s;
}
.footer-legal a:hover { color: var(--text-primary); }

/* Responsive */
@media (max-width: 1024px) {
  .footer-grid {
    grid-template-columns: 1fr 1fr;
    gap: 2.5rem;
  }
}

@media (max-width: 600px) {
  .footer-grid {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
  .footer-top { padding: 3rem 0; }
  .footer-bottom-inner { flex-direction: column; align-items: flex-start; }
}
</style>
