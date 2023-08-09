<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <?= $this->include('templates/css'); ?>
   <title>Absensi QR Code</title>
   <style>
      .bg {
         background: url(<?= base_url('public/assets/img/bg2.jpg'); ?>) center;
         opacity: 0.1;
         background-size: cover;
         height: 100vh;
         width: 100%;
         position: absolute;
         left: 0;
         top: 0;
      }

      .main-panel {
         position: relative;
         float: left;
         width: calc(100%);
         transition: 0.33s, cubic-bezier(0.685, 0.0473, 0.346, 1);
      }

      video#previewKamera {
         width: 100%;
         height: 400px;
         margin: 0;
      }

      .previewParent {
         width: auto;
         height: auto;
         margin: auto;
         margin: auto;
         border: 2px solid grey;
      }

      .unpreview {
         background-color: aquamarine;
         text-align: center;
      }

      .form-select {
         min-width: 200px;
      }
   </style>
</head>

<body>
   <div class="bg bg-image"></div>
   <!-- Navbar -->
   <nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top">
      <div class="container-fluid">
         <div class="navbar-wrapper row w-100">
            <div class="col-6">
               <p class="navbar-brand"><b><?= $title ?? 'Login'; ?></b></p>
            </div>
            <div class="col-6 d-flex justify-content-end">
               <?= $this->renderSection('navaction') ?>
            </div>
         </div>
      </div>
   </nav>
   <!-- End Navbar -->
   <?= $this->renderSection('content') ?>
   <?= $this->include('templates/js'); ?>
</body>

</html>