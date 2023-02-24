<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <?= $this->include('templates/css'); ?>
   <title>Document</title>
   <style>
      .main-panel {
         position: relative;
         float: left;
         width: calc(100%);
         transition: 0.33s, cubic-bezier(0.685, 0.0473, 0.346, 1);
      }

      #previewKamera {
         background-color: grey;
         width: 500px;
         height: 500px;
      }

      .form-select {
         min-width: 200px;
      }
   </style>
</head>

<body>

   <?= $this->renderSection('content') ?>

   <?= $this->include('templates/js'); ?>
</body>

</html>