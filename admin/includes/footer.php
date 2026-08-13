    <footer class="admin-footer">
        <div>
            &copy; <?= date('Y') ?>
            <?= function_exists('getSetting') ? htmlspecialchars(getSetting('legal_name', 'CV. Kayaswara')) : 'CV. Kayaswara' ?>
            &mdash; Panel Redaksi
        </div>
        <div>Katalog, naskah, dan produksi dalam satu tempat.</div>
    </footer>
</div><!-- /.admin-main -->
</div><!-- /.admin-wrapper -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
/* ==========================================================
   Shared admin behaviour
   ========================================================== */

function toggleSidebar() {
    var sidebar = document.getElementById('adminSidebar');
    var overlay = document.getElementById('sidebarOverlay');
    var main    = document.getElementById('adminMain');

    if (window.innerWidth <= 768) {
        sidebar.classList.toggle('mobile-open');
        overlay.classList.toggle('active');
    } else {
        sidebar.classList.toggle('collapsed');
        main.style.marginLeft = sidebar.classList.contains('collapsed')
            ? 'var(--sidebar-collapsed-width)'
            : 'var(--sidebar-width)';
    }
}

function closeSidebar() {
    document.getElementById('adminSidebar').classList.remove('mobile-open');
    document.getElementById('sidebarOverlay').classList.remove('active');
}

window.addEventListener('resize', function () {
    if (window.innerWidth > 768) closeSidebar();
});

function dismissFlash(btn) {
    var msg = btn.closest('.flash-message');
    msg.classList.add('flash-out');
    window.setTimeout(function () { msg.remove(); }, 300);
}

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.flash-message').forEach(function (msg, i) {
        window.setTimeout(function () {
            if (!msg.parentNode) return;
            msg.classList.add('flash-out');
            window.setTimeout(function () { if (msg.parentNode) msg.remove(); }, 300);
        }, 5500 + i * 400);
    });
});

function confirmDelete(form, itemName) {
    if (confirm('Hapus ' + (itemName || 'item ini') + '?\n\nTindakan ini tidak dapat dibatalkan.')) {
        form.submit();
        return true;
    }
    return false;
}

function deleteConfirm(name) {
    return confirm('Hapus "' + (name || 'item ini') + '"?\n\nTindakan ini tidak dapat dibatalkan.');
}

function previewImage(input, previewId) {
    if (!input.files || !input.files[0]) return;
    var file = input.files[0];
    var allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    if (allowed.indexOf(file.type) === -1) {
        alert('Tipe berkas tidak didukung. Gunakan JPG, PNG, GIF, atau WEBP.');
        input.value = '';
        return;
    }
    if (file.size > 2 * 1024 * 1024) {
        alert('Ukuran berkas terlalu besar. Maksimal 2MB.');
        input.value = '';
        return;
    }

    var reader = new FileReader();
    reader.onload = function (e) {
        var preview = document.getElementById(previewId);
        if (!preview) return;
        preview.src = e.target.result;
        preview.style.display = 'block';
        var wrap = preview.closest('.img-preview-wrap');
        if (wrap) wrap.style.display = 'inline-block';
    };
    reader.readAsDataURL(file);
}

function autoSlug(titleInput, slugInput) {
    slugInput.value = titleInput.value
        .toLowerCase()
        .replace(/[àáâãäå]/g, 'a')
        .replace(/[èéêë]/g, 'e')
        .replace(/[ìíîï]/g, 'i')
        .replace(/[òóôõö]/g, 'o')
        .replace(/[ùúûü]/g, 'u')
        .replace(/[ñ]/g, 'n')
        .replace(/[^a-z0-9\s-]/g, '')
        .trim()
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-');
}

function showLoading() {
    var overlay = document.createElement('div');
    overlay.className = 'loading-overlay';
    overlay.innerHTML = '<div class="loading-spinner"></div>';
    document.body.appendChild(overlay);
}

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('form[data-loading]').forEach(function (form) {
        form.addEventListener('submit', showLoading);
    });

    if (window.jQuery && jQuery.fn.DataTable) {
        jQuery('.admin-datatable').each(function () {
            jQuery(this).DataTable({
                responsive: true,
                pageLength: 15,
                language: {
                    emptyTable:     'Belum ada data',
                    info:           'Menampilkan _START_–_END_ dari _TOTAL_ data',
                    infoEmpty:      'Tidak ada data',
                    infoFiltered:   '(disaring dari _MAX_ data)',
                    lengthMenu:     'Tampilkan _MENU_ baris',
                    loadingRecords: 'Memuat…',
                    processing:     'Memproses…',
                    search:         'Cari:',
                    zeroRecords:    'Tidak ada data yang cocok',
                    paginate: { first: '«', last: '»', next: '›', previous: '‹' }
                },
                dom: '<"row align-items-center mb-3"<"col-sm-6"l><"col-sm-6"f>>rt<"row align-items-center mt-3"<"col-sm-6"i><"col-sm-6"p>>',
                columnDefs: [{ orderable: false, targets: -1 }]
            });
        });
    }

    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
        new bootstrap.Tooltip(el);
    });
});
</script>

<?php if (isset($extra_js)) echo $extra_js; ?>

</body>
</html>
