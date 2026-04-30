</div><!-- /page-content -->
</main><!-- /main-content -->
</div><!-- /admin-layout -->

<script>
// Sidebar mobile toggle
document.getElementById('menuToggle')?.addEventListener('click', () => {
    document.getElementById('sidebar').classList.toggle('open');
});

// Auto-hide alerts
document.querySelectorAll('.alert').forEach(el => {
    setTimeout(() => {
        el.style.opacity = '0';
        el.style.transition = 'opacity 0.5s';
        setTimeout(() => el.remove(), 500);
    }, 4000);
});

// Image preview
document.querySelectorAll('.img-file-input').forEach(input => {
    input.addEventListener('change', function() {
        const preview = document.getElementById(this.dataset.preview);
        if (preview && this.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                preview.src = e.target.result;
                preview.classList.add('show');
            };
            reader.readAsDataURL(this.files[0]);
        }
    });
});

// Confirm delete
document.querySelectorAll('.confirm-delete').forEach(btn => {
    btn.addEventListener('click', (e) => {
        if (!confirm('Are you sure you want to delete this item? This cannot be undone.')) {
            e.preventDefault();
        }
    });
});

// Modal handling
document.querySelectorAll('[data-modal]').forEach(trigger => {
    trigger.addEventListener('click', () => {
        const modal = document.getElementById(trigger.dataset.modal);
        if (modal) modal.classList.add('open');
    });
});
document.querySelectorAll('.modal-close, .modal-overlay').forEach(el => {
    el.addEventListener('click', (e) => {
        if (e.target === el) {
            document.querySelectorAll('.modal-overlay').forEach(m => m.classList.remove('open'));
        }
    });
});

// Settings tabs
document.querySelectorAll('.settings-tab').forEach(tab => {
    tab.addEventListener('click', () => {
        document.querySelectorAll('.settings-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.settings-panel').forEach(p => p.classList.remove('active'));
        tab.classList.add('active');
        const panel = document.getElementById(tab.dataset.panel);
        if (panel) panel.classList.add('active');
    });
});
</script>
</body>
</html>
