<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="page-toolbar">
    <p>Kelola akun administrator panel.</p>
    <a href="<?= site_url('admin/users/create') ?>" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> Tambah Admin
    </a>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Login Terakhir</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td><?= esc($row['nama']) ?></td>
                        <td><?= esc($row['email']) ?></td>
                        <td>
                            <span class="badge badge-<?= esc($row['statusBadge']) ?>"><?= esc($row['status']) ?></span>
                        </td>
                        <td><?= esc($row['lastLogin']) ?></td>
                        <td class="actions">
                            <a href="<?= esc($row['editUrl']) ?>" class="btn btn-sm">
                                <i class="fa-solid fa-pen"></i> Edit
                            </a>
                            <?php if ($row['canDelete']): ?>
                                <form method="post" action="<?= esc($row['deleteUrl']) ?>" class="inline-form js-swal-confirm"
                                    data-swal-title="Hapus akun ini?"
                                    data-swal-text="Akun admin yang dihapus tidak dapat dikembalikan."
                                    data-swal-icon="warning"
                                    data-swal-confirm="Ya, Hapus">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fa-solid fa-trash"></i> Hapus
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>