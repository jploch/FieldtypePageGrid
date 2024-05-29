<?php

namespace ProcessWire;
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
  <?php
  $itemsParent = $pages->get('name=pg-' . $page->id . ', template=pg_container');
  if ($itemsParent->id) {
    foreach ($itemsParent->children('template=pg_container') as $fieldPage) {
      $fId = str_replace('pg-', '', $fieldPage->name);
      $f = $fields->get("id=$fId, type=FieldtypePageGrid");
      if ($f && $f->id) {
        if ($pagegrid->isBackend()) {
          if (isset($_GET['field']) && $_GET['field'] === $f->name) echo $pagegrid->renderGrid($page, $f);
        } else {
          echo $pagegrid->renderGrid($page, $f);
        }
      }
    }
  }
  ?>

  <!-- function to render PAGEGRID scripts -->
  <?= $pagegrid->scripts($page); ?>
</body>

</html>