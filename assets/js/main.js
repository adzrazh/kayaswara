/* ==========================================================================
   KAYASWARA — Front-end behaviour
   Vanilla JS only. No dependency beyond Bootstrap's bundle (already loaded).
   ========================================================================== */
(function () {
    'use strict';

    /* ----------------------------------------------------------------------
       Sticky header shadow
    ---------------------------------------------------------------------- */
    var header = document.querySelector('.site-header');
    if (header) {
        var onScrollHeader = function () {
            header.classList.toggle('is-stuck', window.scrollY > 8);
        };
        onScrollHeader();
        window.addEventListener('scroll', onScrollHeader, { passive: true });
    }

    /* ----------------------------------------------------------------------
       Scroll reveal
    ---------------------------------------------------------------------- */
    var revealables = document.querySelectorAll('.reveal');
    if (revealables.length) {
        if ('IntersectionObserver' in window) {
            var io = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (!entry.isIntersecting) return;
                    var el = entry.target;
                    var delay = parseFloat(el.dataset.revealDelay || 0);
                    if (delay) el.style.transitionDelay = delay + 's';
                    el.classList.add('in');
                    io.unobserve(el);
                });
            }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
            revealables.forEach(function (el) { io.observe(el); });
        } else {
            revealables.forEach(function (el) { el.classList.add('in'); });
        }
    }

    /* ----------------------------------------------------------------------
       Back-to-top
    ---------------------------------------------------------------------- */
    var toTop = document.querySelector('.to-top');
    if (toTop) {
        var onScrollTop = function () {
            toTop.classList.toggle('show', window.scrollY > 500);
        };
        onScrollTop();
        window.addEventListener('scroll', onScrollTop, { passive: true });
        toTop.addEventListener('click', function (e) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    /* ----------------------------------------------------------------------
       Counting numbers (hero / figures)
    ---------------------------------------------------------------------- */
    var counters = document.querySelectorAll('[data-count-to]');
    if (counters.length && 'IntersectionObserver' in window) {
        var cio = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) return;
                var el = entry.target;
                cio.unobserve(el);
                var target = parseFloat(el.dataset.countTo) || 0;
                var suffix = el.dataset.countSuffix || '';
                var duration = 1100;
                var start = null;
                var step = function (ts) {
                    if (start === null) start = ts;
                    var p = Math.min((ts - start) / duration, 1);
                    var eased = 1 - Math.pow(1 - p, 3);
                    el.textContent = Math.round(target * eased).toLocaleString('id-ID') + suffix;
                    if (p < 1) requestAnimationFrame(step);
                };
                requestAnimationFrame(step);
            });
        }, { threshold: 0.4 });
        counters.forEach(function (el) { cio.observe(el); });
    }

    /* ----------------------------------------------------------------------
       Auto-dismiss flash toasts
    ---------------------------------------------------------------------- */
    document.querySelectorAll('.flash-stack .alert').forEach(function (el, i) {
        window.setTimeout(function () {
            el.classList.remove('show');
            window.setTimeout(function () { el.remove(); }, 300);
        }, 6000 + i * 400);
    });

    /* ----------------------------------------------------------------------
       Generic file dropzone (manuscript upload)
       Markup contract: .file-drop > input[type=file] + [data-drop-idle] + [data-drop-picked]
    ---------------------------------------------------------------------- */
    document.querySelectorAll('.file-drop').forEach(function (zone) {
        var input = zone.querySelector('input[type="file"]');
        var idle = zone.querySelector('[data-drop-idle]');
        var picked = zone.querySelector('[data-drop-picked]');
        if (!input || !idle || !picked) return;

        var nameEl = picked.querySelector('[data-drop-name]');
        var sizeEl = picked.querySelector('[data-drop-size]');
        var removeBtn = picked.querySelector('[data-drop-remove]');

        var human = function (bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
            return (bytes / 1048576).toFixed(1) + ' MB';
        };

        var show = function (file) {
            if (nameEl) nameEl.textContent = file.name;
            if (sizeEl) sizeEl.textContent = human(file.size);
            idle.classList.add('d-none');
            picked.classList.remove('d-none');
        };

        var clear = function () {
            input.value = '';
            idle.classList.remove('d-none');
            picked.classList.add('d-none');
        };

        input.addEventListener('change', function () {
            if (input.files && input.files.length) show(input.files[0]);
        });

        if (removeBtn) {
            removeBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                clear();
            });
        }

        ['dragenter', 'dragover'].forEach(function (ev) {
            zone.addEventListener(ev, function (e) {
                e.preventDefault();
                zone.classList.add('dragover');
            });
        });
        ['dragleave', 'drop'].forEach(function (ev) {
            zone.addEventListener(ev, function (e) {
                e.preventDefault();
                zone.classList.remove('dragover');
            });
        });
        zone.addEventListener('drop', function (e) {
            if (e.dataTransfer && e.dataTransfer.files.length) {
                input.files = e.dataTransfer.files;
                show(e.dataTransfer.files[0]);
            }
        });
    });

    /* ----------------------------------------------------------------------
       Bootstrap tooltips (opt-in)
    ---------------------------------------------------------------------- */
    if (window.bootstrap && bootstrap.Tooltip) {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
            new bootstrap.Tooltip(el);
        });
    }
})();
