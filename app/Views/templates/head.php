<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <?= csrf_meta(); ?>
   <?= $this->include('templates/css'); ?>
   <title><?= $title ?></title>
   <?= $this->include('templates/js') ?>
   <script>var BaseConfig = {baseURL: '<?= base_url(); ?>', csrfTokenName: '<?= csrf_token() ?>', textOk: "Ok", textCancel: "Batalkan"};</script>
</head>