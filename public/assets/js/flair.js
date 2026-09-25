/* ==========================================================================
   FLAIR — Fluidité éditoriale discrète
   Curseur doux · boutons magnétiques · parallaxe légère · révélations par lignes
   Aucun effet excessif : tout est désactivé sur mobile et si mouvement réduit.
   ========================================================================== */
(function () {
    'use strict';

    const finePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* ---------- Entrée immersive du héros ---------- */
    const hero = document.querySelector('.hero');
    const heroTitle = document.querySelector('.hero-v2-title');
    if (heroTitle) {
        requestAnimationFrame(() => {
            setTimeout(() => {
                heroTitle.classList.add('in-view');
                if (hero) hero.classList.add('in-view');
            }, 250);
        });
    }

    /* ---------- Révélation par lignes ---------- */
    const lineGroups = document.querySelectorAll('.reveal-lines');
    if ('IntersectionObserver' in window && lineGroups.length) {
        const obs = new IntersectionObserver(
            (entries, o) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('in-view');
                        o.unobserve(entry.target);
                    }
                });
            },
            { threshold: 0.25 }
        );
        lineGroups.forEach((el) => obs.observe(el));
    } else {
        lineGroups.forEach((el) => el.classList.add('in-view'));
    }

    /* ---------- Curseur personnalisé ---------- */
    if (finePointer && !reducedMotion) {
        const dot = document.querySelector('.cursor-dot');
        const ring = document.querySelector('.cursor-ring');
        if (dot && ring) {
            document.body.classList.add('has-cursor');

        let mx = -100, my = -100, rx = -100, ry = -100;

        window.addEventListener('mousemove', (e) => {
            mx = e.clientX;
            my = e.clientY;
            dot.style.transform = 'translate(' + mx + 'px,' + my + 'px) translate(-50%,-50%)';
        }, { passive: true });

        (function ringLoop() {
            rx += (mx - rx) * 0.16;
            ry += (my - ry) * 0.16;
            ring.style.transform = 'translate(' + rx + 'px,' + ry + 'px) translate(-50%,-50%)';
            requestAnimationFrame(ringLoop);
        })();

        const hoverables = 'a, button, input, textarea, select, .tilt, .project-card, [data-cursor]';
        document.addEventListener('mouseover', (e) => {
            if (e.target.closest(hoverables)) ring.classList.add('is-hover');
        });
        document.addEventListener('mouseout', (e) => {
            if (e.target.closest(hoverables)) ring.classList.remove('is-hover');
        });
        }
    }

    /* ---------- Boutons magnétiques (amplitude très contenue) ---------- */
    if (finePointer && !reducedMotion) {
        document.querySelectorAll('.btn-glow, .p-arrow').forEach((el) => {
            el.addEventListener('mousemove', (e) => {
                const r = el.getBoundingClientRect();
                const x = ((e.clientX - r.left) / r.width - 0.5) * 8;
                const y = ((e.clientY - r.top) / r.height - 0.5) * 8;
                el.style.translate = x.toFixed(1) + 'px ' + y.toFixed(1) + 'px';
            });
            el.addEventListener('mouseleave', () => { el.style.translate = '0px 0px'; });
        });
    }

    /* ---------- Parallaxe douce au scroll ---------- */
    const pxEls = [];
    document.querySelectorAll('[data-parallax]').forEach((el) => {
        pxEls.push({ el: el, speed: parseFloat(el.dataset.parallax || '0.12') });
    });
    if (!reducedMotion && pxEls.length) {
        let ticking = false;
        const applyParallax = () => {
            const vh = window.innerHeight;
            for (const item of pxEls) {
                const rect = item.el.getBoundingClientRect();
                const delta = (rect.top + rect.height / 2) - vh / 2;
                item.el.style.transform = 'translateY(' + (-delta * item.speed).toFixed(1) + 'px)';
            }
            ticking = false;
        };
        window.addEventListener('scroll', () => {
            if (!ticking) {
                requestAnimationFrame(applyParallax);
                ticking = true;
            }
        }, { passive: true });
        applyParallax();
    }
})();
