<?php if (!empty($homestayKamar)): ?>
    <div class="wisata-homestay mt-lg">
        <h3 class="detail-section-title"><i class="fa-solid fa-house-chimney" aria-hidden="true"></i> Homestay</h3>
        <div class="homestay-grid">
            <?php foreach ($homestayKamar as $kamar): ?>
                <div class="homestay-card">
                    <?php if (!empty($kamar['gambar'])): ?>
                        <div class="homestay-card-image">
                            <img src="<?= upload_url($kamar['gambar']) ?>" alt="<?= esc($kamar['nama_kamar']) ?>"
                                loading="lazy">
                        </div>
                    <?php endif; ?>

                    <div class="homestay-card-body">
                        <h4 class="homestay-card-title"><?= esc($kamar['nama_kamar']) ?></h4>

                        <?php if (!empty($kamar['kapasitas'])): ?>
                            <div class="homestay-card-meta">
                                <i class="fa-solid fa-user-group"></i>
                                Kapasitas <?= (int) $kamar['kapasitas'] ?> orang
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($kamar['deskripsi'])): ?>
                            <p class="homestay-card-desc"><?= esc($kamar['deskripsi']) ?></p>
                        <?php endif; ?>

                        <div class="homestay-card-footer">
                            <span class="homestay-card-price">
                                <span class="homestay-price-amount">Rp <?= number_format((int) $kamar['harga'], 0, ',', '.') ?></span>
                                <span class="homestay-price-label">/ malam</span>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (!empty($pengaturan['no_whatsapp'])): ?>
            <div class="text-center mt-md">
                <a href="<?= wa_link($pengaturan['no_whatsapp'], 'Halo, saya mau reservasi homestay') ?>"
                    class="btn btn-primary" target="_blank" rel="noopener">
                    <i class="fa-brands fa-whatsapp"></i> Reservasi via WhatsApp
                </a>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>