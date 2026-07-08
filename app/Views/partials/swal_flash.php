<?php foreach (['success', 'error'] as $type): ?>
    <?php if ($msg = session()->getFlashdata($type)): ?>
        <div class="swal-flash" data-type="<?= esc($type) ?>" data-message="<?= esc($msg, 'attr') ?>" hidden></div>
    <?php endif; ?>
<?php endforeach; ?>
