<?php
// Update this when you make changes to CSS files
$assetVersion = '1.0.2';
?>
<link rel="stylesheet" href="<?= base_url('assets/fonts/fonts.css?v=' . $assetVersion); ?>" />
<link rel="stylesheet" href="<?= base_url('assets/css/material-dashboard.min.css?v=' . $assetVersion); ?>" />
<link rel="stylesheet" href="<?= base_url('assets/css/style.min.css?v=' . $assetVersion); ?>" />
<link rel="stylesheet" href="<?= base_url('assets/js/plugins/file-uploader/css/jquery.dm-uploader.min.css?v=' . $assetVersion); ?>" />
<link rel="stylesheet" href="<?= base_url('assets/js/plugins/file-uploader/css/styles-1.0.css?v=' . $assetVersion); ?>" />

<?= $this->renderSection("styles") ?>