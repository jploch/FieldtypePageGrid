<?php namespace ProcessWire;
// deactivate automatic append/prepend of file "_init.php" and "_main.php" for this template
// to deactivate it globally uncomment the lines $config->prependTemplateFile and $config->appendTemplateFile inside your config.php file
$pagegrid->noAppendFile($page);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= $page->title ?></title>

  <!-- function to render PAGEGRID styles -->
  <?= $pagegrid->styles($page); ?>
</head>

<body>
  <!-- function to render PAGEGRID markup -->
  <?= $pagegrid->renderGrid($page); ?>

  <!-- function to render PAGEGRID scripts -->
  <?= $pagegrid->scripts($page); ?>
</body>

</html>