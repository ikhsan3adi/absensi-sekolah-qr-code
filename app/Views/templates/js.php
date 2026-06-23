<?php
// Update this when you make changes to JS files
$assetVersion = '1.0.0';
?>
<!--   Core JS Files   -->
<script src="<?= base_url('assets/js/core/jquery-3.5.1.min.js?v=' . $assetVersion) ?>"></script>
<!-- Custom Plugins Sweetalert, Cookies, Etc -->
<script src="<?= base_url('assets/js/plugins.js?v=' . $assetVersion) ?>" type="text/javascript"></script>
<script src="<?= base_url('assets/js/core/bootstrap.bundle.min.js?v=' . $assetVersion) ?>"></script>
<script src="<?= base_url('assets/js/core/popper.min.js?v=' . $assetVersion) ?>"></script>
<script src="<?= base_url('assets/js/core/bootstrap-material-design.min.js?v=' . $assetVersion) ?>"></script>
<script src="<?= base_url('assets/js/plugins/perfect-scrollbar.jquery.min.js?v=' . $assetVersion) ?>"></script>
<script src="<?= base_url('assets/js/plugins/nouislider.min.js?v=' . $assetVersion) ?>"></script>
<script src="<?= base_url('assets/js/material-dashboard.js?v=' . $assetVersion) ?>" type="text/javascript"></script>
<script src="<?= base_url('assets/js/plugins/file-uploader/js/jquery.dm-uploader.min.js?v=' . $assetVersion); ?>"></script>
<script src="<?= base_url('assets/js/plugins/file-uploader/js/ui.js?v=' . $assetVersion); ?>"></script>
<script src="<?= base_url('assets/js/custom.js?v=' . $assetVersion) ?>" type="text/javascript"></script>
<script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
<script>
  $.extend( $.fn.dataTable.defaults, {
    pageLength: 25,
    language: {
      search: "Cari:",
      lengthMenu: "Tampilkan _MENU_ data",
      info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
      infoEmpty: "Tidak ada data",
      infoFiltered: "(difilter dari _MAX_ total data)",
      zeroRecords: "Data tidak ditemukan",
      paginate: {
        first: "Pertama",
        last: "Terakhir",
        next: "Selanjutnya",
        previous: "Sebelumnya"
      }
    }
  });
</script>

<script>
  if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
      navigator.serviceWorker.register('<?= base_url('sw.js') ?>')
        .then(reg => console.log('Service Worker registered'))
        .catch(err => console.log('Service Worker registration failed', err));
    });
  }
</script>
