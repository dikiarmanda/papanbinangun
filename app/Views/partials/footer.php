<?php $site = $pengaturan ?? pengaturan(); ?>
<footer class="site-footer">
    <div class="container footer-grid">
        <div>
            <h3 class="footer-title"><?= esc($site['nama_desa']) ?></h3>
            <p><?= esc(strip_tags($site['deskripsi_singkat'] ?: $site['tagline'])) ?></p>
        </div>
        <div>
            <h4><i class="fa-solid fa-compass"></i> Navigasi</h4>
            <ul class="footer-links">
                <li><a href="<?= site_url('tentang') ?>"><i class="fa-solid fa-angle-right"></i> Tentang</a></li>
                <li><a href="<?= site_url('wisata') ?>"><i class="fa-solid fa-angle-right"></i> Wisata</a></li>
                <li><a href="<?= site_url('artikel') ?>"><i class="fa-solid fa-angle-right"></i> Artikel</a></li>
                <li><a href="<?= site_url('galeri') ?>"><i class="fa-solid fa-angle-right"></i> Galeri</a></li>
                <li><a href="<?= site_url('kontak') ?>"><i class="fa-solid fa-angle-right"></i> Kontak</a></li>
            </ul>
        </div>
        <div>
            <h4><i class="fa-solid fa-address-book"></i> Kontak</h4>
            <?php if (!empty($site['alamat'])): ?>
                <p><i class="fa-solid fa-location-dot"></i> <?= esc($site['alamat']) ?></p>
            <?php endif; ?>
            <?php if (!empty($site['email_kontak'])): ?>
                <p><i class="fa-solid fa-envelope"></i> <?= esc($site['email_kontak']) ?></p>
            <?php endif; ?>
            <?php if (!empty($site['no_whatsapp'])): ?>
                <p>
                    <a href="<?= wa_link($site['no_whatsapp']) ?>" target="_blank" rel="noopener">
                        <i class="fa-brands fa-whatsapp"></i> WhatsApp
                    </a>
                </p>
            <?php endif; ?>
            <div class="social-links">
                <?php if (!empty($site['instagram_url'])): ?>
                    <a href="<?= esc($site['instagram_url']) ?>" target="_blank" rel="noopener" aria-label="Instagram">
                        <i class="fa-brands fa-instagram"></i>
                    </a>
                <?php endif; ?>
                <?php if (!empty($site['facebook_url'])): ?>
                    <a href="<?= esc($site['facebook_url']) ?>" target="_blank" rel="noopener" aria-label="Facebook">
                        <i class="fa-brands fa-facebook-f"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container">
            <p>&copy; <?= date('Y') ?> <?= esc($site['nama_desa']) ?>. Semua hak dilindungi.</p>
        </div>
    </div>
</footer>