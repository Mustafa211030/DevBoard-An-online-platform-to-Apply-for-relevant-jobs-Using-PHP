<!-- ── FOOTER ── -->
<footer class="footer">
    <div class="container">

        <div class="footer-grid">

            <!-- Brand col -->
            <div class="footer-brand" data-aos="fade-up">
                <a href="<?php echo BASE_URL; ?>/index.php" class="navbar-brand" style="font-size:1.4rem;">
                    Dev<span>Board</span><span class="brand-dot"></span>
                </a>
                <p>The modern job board for developers. Find remote and on-site roles at the world's best tech companies — or post your opening in minutes.</p>
                <div class="d-flex gap-8 mt-16">
                    <span class="footer-badge"><i class="bi bi-shield-check"></i> Secure</span>
                    <span class="footer-badge"><i class="bi bi-globe"></i> Remote-first</span>
                </div>
            </div>

            <!-- Links col -->
            <div class="footer-col" data-aos="fade-up" data-aos-delay="100">
                <h4>Navigate</h4>
                <ul>
                    <li><a href="<?php echo BASE_URL; ?>/index.php">Home</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/pages/jobs.php">Browse Jobs</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/pages/post-job.php">Post a Job</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/pages/register.php">Create Account</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/pages/login.php">Sign In</a></li>
                </ul>
            </div>

            <!-- Categories col -->
            <div class="footer-col" data-aos="fade-up" data-aos-delay="200">
                <h4>Categories</h4>
                <ul>
                    <li><a href="<?php echo BASE_URL; ?>/pages/jobs.php?type=Full-time">Full-time</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/pages/jobs.php?type=Contract">Contract</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/pages/jobs.php?type=Freelance">Freelance</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/pages/jobs.php?location=Remote">Remote Only</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/pages/jobs.php?keyword=php">PHP Jobs</a></li>
                </ul>
            </div>

        </div>

        <!-- Footer bottom -->
        <div class="footer-bottom">
            <p>&copy; <?php echo SITE_YEAR; ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
            <div class="d-flex gap-16">
                <a href="#">Privacy</a>
                <a href="#">Terms</a>
                <a href="#">Contact</a>
            </div>
        </div>

    </div>
</footer>
<!-- ── END FOOTER ── -->

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
// ── Theme Toggle ──
const html = document.documentElement;
const toggle = document.getElementById('themeToggle');
const saved = localStorage.getItem('devboard-theme') || 'light';
html.setAttribute('data-theme', saved);

toggle.addEventListener('click', () => {
    const current = html.getAttribute('data-theme');
    const next = current === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-theme', next);
    localStorage.setItem('devboard-theme', next);
});

// ── Navbar scroll effect ──
const nav = document.getElementById('mainNav');
window.addEventListener('scroll', () => {
    nav.classList.toggle('scrolled', window.scrollY > 20);
});

// ── Mobile menu toggle ──
const toggler = document.getElementById('navToggler');
const navLinks = document.getElementById('navLinks');
if (toggler) {
    toggler.addEventListener('click', () => {
        navLinks.classList.toggle('open');
    });
}

// Close mobile menu on link click
document.querySelectorAll('#navLinks a').forEach(link => {
    link.addEventListener('click', () => navLinks.classList.remove('open'));
});

// ── AOS (Animate on Scroll) — simple manual impl ──
function runAOS() {
    document.querySelectorAll('[data-aos]').forEach(el => {
        const rect = el.getBoundingClientRect();
        if (rect.top < window.innerHeight - 60) {
            el.classList.add('aos-animate');
        }
    });
}
window.addEventListener('scroll', runAOS, { passive: true });
window.addEventListener('load', runAOS);
setTimeout(runAOS, 100);

// ── Job card hover lift (extra touch) ──
document.querySelectorAll('.job-card').forEach(card => {
    card.addEventListener('mouseenter', () => {
        card.style.boxShadow = '0 12px 36px rgba(14,165,233,0.15)';
    });
    card.addEventListener('mouseleave', () => {
        card.style.boxShadow = '';
    });
});
</script>

</body>
</html>
