<!DOCTYPE html>
<html lang="en">

<?= $this->include('templates/head') ?>

<body>
   <div>
      <?= $this->include('templates/sidebar') ?>
      <div class="main-panel">

         <?= $this->include('templates/navbar') ?>

         <?= $this->renderSection('content') ?>

         <?= $this->include('templates/footer') ?>

         <!-- komentar jika tidak dipakai -->
         <?php
         // echo $this->include('templates/fixed_plugin') 
         ?>

      </div>
   </div>
</body>

</html>