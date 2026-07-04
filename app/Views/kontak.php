<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<section class="page-hero">
    <div class="container">
        <span class="stamp"><i class="fa-solid fa-envelope"></i> Hubungi Kami</span>
        <h1>Kontak</h1>
        <p>Informasi dan lokasi <?= esc($pengaturan['nama_desa']) ?></p>
    </div>
</section>

<section class="section contact-page">
    <div class="container">
        <div class="contact-box">
            <div class="contact-info">
                <h2><?= esc($pengaturan['nama_desa']) ?></h2>
                <p class="contact-lead">Silakan hubungi kami untuk informasi wisata, paket, atau kunjungan.</p>

                <ul class="contact-list">
                    <li>
                        <span class="contact-list-icon"><i class="fa-solid fa-location-dot"></i></span>
                        <div>
                            <strong>Alamat</strong>
                            <p><?= esc($alamat) ?></p>
                        </div>
                    </li>

                    <?php if (!empty($pengaturan['email_kontak'])): ?>
                        <li>
                            <span class="contact-list-icon"><i class="fa-solid fa-envelope"></i></span>
                            <div>
                                <strong>Email</strong>
                                <p><a
                                        href="mailto:<?= esc($pengaturan['email_kontak']) ?>"><?= esc($pengaturan['email_kontak']) ?></a>
                                </p>
                            </div>
                        </li>
                    <?php endif; ?>

                    <?php if (!empty($pengaturan['no_whatsapp'])): ?>
                        <li>
                            <span class="contact-list-icon"><i class="fa-brands fa-whatsapp"></i></span>
                            <div>
                                <strong>WhatsApp</strong>
                                <p><?= esc($pengaturan['no_whatsapp']) ?></p>
                            </div>
                        </li>
                    <?php endif; ?>
                </ul>

                <div class="contact-actions">
                    <?php if (!empty($pengaturan['no_whatsapp'])): ?>
                        <a href="<?= wa_link($pengaturan['no_whatsapp'], 'Halo, saya ingin bertanya tentang ' . $pengaturan['nama_desa']) ?>"
                            class="btn btn-primary" target="_blank" rel="noopener">
                            <i class="fa-brands fa-whatsapp"></i> Chat WhatsApp
                        </a>
                    <?php endif; ?>
                    <a href="<?= $mapsUrl ?>" class="btn btn-outline" target="_blank" rel="noopener">
                        <i class="fa-solid fa-map-location-dot"></i> Buka Google Maps
                    </a>
                </div>

                <?php if (!empty($pengaturan['instagram_url']) || !empty($pengaturan['facebook_url'])): ?>
                    <div class="contact-social-row">
                        <?php if (!empty($pengaturan['instagram_url'])): ?>
                            <a href="<?= esc($pengaturan['instagram_url']) ?>" target="_blank" rel="noopener"
                                aria-label="Instagram">
                                <i class="fa-brands fa-instagram"></i>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($pengaturan['facebook_url'])): ?>
                            <a href="<?= esc($pengaturan['facebook_url']) ?>" target="_blank" rel="noopener"
                                aria-label="Facebook">
                                <i class="fa-brands fa-facebook-f"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="contact-map">
                <?= $mapEmbed ?>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>