<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cetak QR Code - <?= esc($title ?? '') ?></title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: 'Segoe UI', Arial, sans-serif;
      background: #fff;
      padding: 20px;
    }

    .no-print { display: none; }

    .header {
      text-align: center;
      margin-bottom: 24px;
    }

    .header h2 {
      font-size: 18px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .header p {
      font-size: 13px;
      color: #555;
      margin-top: 4px;
    }

    .qr-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 12px;
    }

    .qr-card {
      border: 1px solid #ddd;
      border-radius: 6px;
      padding: 12px;
      text-align: center;
      page-break-inside: avoid;
      break-inside: avoid;
    }

    .qr-card img {
      width: 100%;
      max-width: 140px;
      height: auto;
      display: block;
      margin: 0 auto 8px;
    }

    .qr-card .name {
      font-size: 12px;
      font-weight: 700;
      line-height: 1.3;
      margin-bottom: 2px;
    }

    .qr-card .nip {
      font-size: 10px;
      color: #666;
    }

    .qr-card .kelas-label {
      font-size: 10px;
      color: #888;
      margin-top: 2px;
    }

    @media print {
      body { padding: 10px; }
      .header { margin-bottom: 16px; }
      .qr-grid { gap: 8px; }
      .qr-card { padding: 8px; border: 1px solid #ccc; }
      .qr-card img { max-width: 120px; }
      .qr-card .name { font-size: 11px; }
      .qr-card .nip { font-size: 9px; }
    }

    @media screen {
      body { max-width: 1000px; margin: 0 auto; }
      .no-print {
        display: block;
        text-align: center;
        margin-bottom: 20px;
      }
      .no-print button {
        padding: 10px 28px;
        font-size: 14px;
        font-weight: 600;
        background: #9c27b0;
        color: #fff;
        border: none;
        border-radius: 6px;
        cursor: pointer;
      }
      .no-print button:hover { background: #7b1fa2; }
    }
  </style>
</head>
<body>
  <div class="no-print">
    <button onclick="window.print()">Cetak QR Code</button>
  </div>

  <div class="header">
    <h2>Kartu QR Code <?= esc($type === 'siswa' ? 'Siswa' : 'Guru') ?></h2>
    <p><?= esc($groupInfo) ?></p>
  </div>

  <div class="qr-grid">
    <?php foreach ($items as $item): ?>
      <div class="qr-card">
        <img src="<?= esc($item['qr_url']) ?>" alt="QR <?= esc($item['nama']) ?>">
        <div class="name"><?= esc($item['nama']) ?></div>
        <div class="nip"><?= esc($item['nomor_label']) ?>: <?= esc($item['nomor']) ?></div>
        <?php if (!empty($item['kelas'])): ?>
          <div class="kelas-label"><?= esc($item['kelas']) ?></div>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>

  <script>
    window.print();
  </script>
</body>
</html>
