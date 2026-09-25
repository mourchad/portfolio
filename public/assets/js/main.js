/* ==========================================================================
   PORTFOLIO MOURCHAD — Interactions & animations dynamiques
   ========================================================================== */
(function () {
    'use strict';

    /* ---------- Préchargeur ---------- */
    const preloader = document.getElementById('preloader');
    const hidePreloader = () => preloader && preloader.classList.add('hidden');
    window.addEventListener('load', () => setTimeout(hidePreloader, 450));
    setTimeout(hidePreloader, 2500); // sécurité si l'événement load tarde

    /* ---------- Navigation ---------- */
    const nav = document.getElementById('mainNav');
    const backToTop = document.getElementById('backToTop');

    const onScroll = () => {
        if (nav) nav.classList.toggle('scrolled', window.scrollY > 40);
        if (backToTop) backToTop.classList.toggle('show', window.scrollY > 550);
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    // Ferme le menu mobile après un clic sur un lien.
    document.querySelectorAll('#navMenu .nav-link').forEach((link) => {
        link.addEventListener('click', () => {
            const collapsible = document.getElementById('navMenu');
            if (collapsible && collapsible.classList.contains('show')) {
                bootstrap.Collapse.getOrCreateInstance(collapsible).hide();
            }
        });
    });

    /* ---------- Retour en haut ---------- */
    if (backToTop) {
        backToTop.addEventListener('click', () =>
            window.scrollTo({ top: 0, behavior: 'smooth' })
        );
    }

    /* ---------- Lien actif selon la section visible ---------- */
    const sections = document.querySelectorAll('main section[id]');
    const navLinks = document.querySelectorAll('#mainNav .nav-link');
    if ('IntersectionObserver' in window && sections.length) {
        const spy = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (!entry.isIntersecting) return;
                    navLinks.forEach((l) =>
                        l.classList.toggle('active', l.getAttribute('href') === '#' + entry.target.id)
                    );
                });
            },
            { rootMargin: '-42% 0px -52% 0px' }
        );
        sections.forEach((s) => spy.observe(s));
    }

    /* ---------- Apparitions au scroll ---------- */
    const revealEls = document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-zoom');
    if ('IntersectionObserver' in window && revealEls.length) {
        const revealObserver = new IntersectionObserver(
            (entries, observer) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        observer.unobserve(entry.target);
                    }
                });
            },
            { threshold: 0.14, rootMargin: '0px 0px -36px 0px' }
        );
        revealEls.forEach((el) => revealObserver.observe(el));
    } else {
        revealEls.forEach((el) => el.classList.add('visible'));
    }

    /* ---------- Compteurs animés ---------- */
    const counters = document.querySelectorAll('.counter[data-target]');
    const animateCounter = (el) => {
        const target = parseInt(el.dataset.target || '0', 10);
        const duration = 1600;
        const start = performance.now();
        const step = (now) => {
            const progress = Math.min((now - start) / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            el.textContent = Math.round(eased * target).toString();
            if (progress < 1) requestAnimationFrame(step);
        };
        requestAnimationFrame(step);
    };
    if ('IntersectionObserver' in window && counters.length) {
        const counterObs = new IntersectionObserver(
            (entries, obs) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        animateCounter(entry.target);
                        obs.unobserve(entry.target);
                    }
                });
            },
            { threshold: 0.6 }
        );
        counters.forEach((c) => counterObs.observe(c));
    }

    /* ---------- Barres de progression ---------- */
    const fills = document.querySelectorAll('.progress-fill[data-progress]');
    if ('IntersectionObserver' in window && fills.length) {
        const fillObs = new IntersectionObserver(
            (entries, obs) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        const el = entry.target;
                        setTimeout(() => {
                            el.style.width = Math.min(100, parseFloat(el.dataset.progress)) + '%';
                        }, 120);
                        obs.unobserve(el);
                    }
                });
            },
            { threshold: 0.5 }
        );
        fills.forEach((f) => fillObs.observe(f));
    }

    /* ---------- Effet machine à écrire ---------- */
    const typedEl = document.getElementById('typed');
    if (typedEl) {
        let strings = [];
        try { strings = JSON.parse(typedEl.dataset.strings); } catch (e) { strings = []; }
        if (strings.length) {
            let strIndex = 0, charIndex = 0, deleting = false;
            const typeLoop = () => {
                const current = strings[strIndex];
                charIndex += deleting ? -1 : 1;
                typedEl.textContent = current.slice(0, charIndex);
                let delay = deleting ? 34 : 74;
                if (!deleting && charIndex === current.length) {
                    delay = 1900;
                    deleting = true;
                } else if (deleting && charIndex === 0) {
                    deleting = false;
                    strIndex = (strIndex + 1) % strings.length;
                    delay = 420;
                }
                setTimeout(typeLoop, delay);
            };
            typeLoop();
        }
    }

    /* ---------- Particules du héros ---------- */
    const canvas = document.getElementById('heroParticles');
    if (canvas && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        const ctx = canvas.getContext('2d');
        let particles = [], rafId = null;

        const resize = () => {
            canvas.width = canvas.offsetWidth * devicePixelRatio;
            canvas.height = canvas.offsetHeight * devicePixelRatio;
            ctx.setTransform(devicePixelRatio, 0, 0, devicePixelRatio, 0, 0);
            build();
        };

        const build = () => {
            const count = Math.min(70, Math.floor(canvas.offsetWidth / 16));
            particles = Array.from({ length: count }, () => ({
                x: Math.random() * canvas.offsetWidth,
                y: Math.random() * canvas.offsetHeight,
                vx: (Math.random() - 0.5) * 0.35,
                vy: (Math.random() - 0.5) * 0.35,
                r: Math.random() * 2 + 0.6,
                violet: Math.random() > 0.4,
            }));
        };

        const tick = () => {
            const w = canvas.offsetWidth, h = canvas.offsetHeight;
            ctx.clearRect(0, 0, w, h);
            for (const p of particles) {
                p.x += p.vx; p.y += p.vy;
                if (p.x < 0 || p.x > w) p.vx *= -1;
                if (p.y < 0 || p.y > h) p.vy *= -1;
                ctx.beginPath();
                ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
                ctx.fillStyle = p.violet ? 'rgba(167,139,250,0.55)' : 'rgba(255,255,255,0.35)';
                ctx.fill();
            }
            for (let i = 0; i < particles.length; i++) {
                for (let j = i + 1; j < particles.length; j++) {
                    const dx = particles[i].x - particles[j].x;
                    const dy = particles[i].y - particles[j].y;
                    const dist = Math.hypot(dx, dy);
                    if (dist < 120) {
                        ctx.beginPath();
                        ctx.moveTo(particles[i].x, particles[i].y);
                        ctx.lineTo(particles[j].x, particles[j].y);
                        ctx.strokeStyle = 'rgba(139,92,246,' + (0.14 * (1 - dist / 120)).toFixed(3) + ')';
                        ctx.lineWidth = 1;
                        ctx.stroke();
                    }
                }
            }
            rafId = requestAnimationFrame(tick);
        };

        window.addEventListener('resize', resize);
        resize();
        tick();
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) { cancelAnimationFrame(rafId); rafId = null; }
            else if (!rafId) { tick(); }
        });
    }

    /* ---------- Effet d'inclinaison des cartes ---------- */
    if (window.matchMedia('(hover: hover)').matches) {
        document.querySelectorAll('.tilt').forEach((card) => {
            card.addEventListener('mousemove', (e) => {
                const rect = card.getBoundingClientRect();
                const x = (e.clientX - rect.left) / rect.width - 0.5;
                const y = (e.clientY - rect.top) / rect.height - 0.5;
                card.style.transform =
                    'perspective(900px) rotateX(' + (-y * 7).toFixed(2) + 'deg) rotateY(' + (x * 9).toFixed(2) + 'deg) translateY(-6px)';
            });
            card.addEventListener('mouseleave', () => { card.style.transform = ''; });
        });
    }
})();
