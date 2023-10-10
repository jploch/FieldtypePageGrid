<?php

namespace ProcessWire;
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= $page->title ?></title>

  <!--function to render PAGEGRID styles-->
  <?= $pagegrid->styles($page); ?>
</head>

<body>
 
  <!--function to render PAGEGRID markup-->
  <?= $pagegrid->renderGrid($page); ?>

  <!--function to render PAGEGRID scripts-->
  <?= $pagegrid->scripts($page); ?>
</body>

</html>