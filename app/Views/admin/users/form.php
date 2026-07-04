<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="card">
    <div class="card-header">
        <h2><i class="fa-solid fa-user-pen"></i> <?= esc($title) ?></h2>
        <a href="<?= esc($form['backUrl']) ?>" class="btn btn-sm btn-outline">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>
    <form method="post" action="<?= esc($form['action']) ?>">
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="nama">Nama *</label>
            <input type="text" id="nama" name="nama" value="<?= $form['nama'] ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" id="email" name="email" value="<?= $form['email'] ?>" required>
        </div>

        <div class="form-group">
            <label for="password">Password <?= $form['isEdit'] ? '(kosongkan jika tidak diubah)' : '*' ?></label>
            <input type="password" id="password" name="password" <?= $form['isEdit'] ? '' : 'required' ?> minlength="8">
        </div>

        <div class="form-group">
            <label for="status">Status</label>
            <select id="status" name="status">
                <?php foreach ($form['statusOptions'] as $opt): ?>
                    <option value="<?= esc($opt['value']) ?>" <?= $opt['selected'] ? 'selected' : '' ?>>
                        <?= esc($opt['label']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Simpan</button>
            <a href="<?= esc($form['backUrl']) ?>" class="btn">Batal</a>
        </div>
    </form>
</div>

<?= $this->endSection() ?>