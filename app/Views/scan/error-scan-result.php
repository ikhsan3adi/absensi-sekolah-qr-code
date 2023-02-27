<h3 class="text-danger"><?= $msg; ?></h3>
<?php if ($data != NULL) : ?>
   <?php foreach ($data as $key => $value) : ?>
      <p><?= $key; ?><b><?= $value; ?></b></p>
   <?php endforeach ?>
<?php endif ?>