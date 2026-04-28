# PageGrid Site Templates

> **CLI agents with AgentTools:** you can check the existing site template with `php index.php --at-eval '$pages->get("/your-page/")->template->name'`

## Site Templates vs. Block Templates

ProcessWire uses the word "template" for both. In PageGrid there are two distinct kinds:

| Kind | What it is | Where |
|------|-----------|-------|
| **Site template** | The PHP file assigned to a *content page* — renders the full page HTML including the PageGrid field | `site/templates/my-template.php` |
| **Block template** | The PHP file that renders one *block* inside the grid | `site/templates/blocks/my-block.php` (see `block-creator.md`) |

This skill covers **site templates** only.

---

## The Default: `pagegrid-page`

The PageGrid module automatically creates a ready-to-use site template called `pagegrid-page` (file: `site/templates/pagegrid-page.php`). It works out of the box — just assign it to any page from the admin.

**When is a custom site template needed?**
- You want to integrate PageGrid into an existing page template that already has your site's design
- You need to add PageGrid to a template that also contains other fields or custom markup

---

## Core Rendering Functions

These three calls are required to render a PageGrid field on a page:

```php
<?= $pagegrid->styles($page); ?>      // outputs <link> and <style> tags for the grid
<?= $pagegrid->renderGrid($page); ?>  // outputs the block HTML
<?= $pagegrid->scripts($page); ?>     // outputs <script> tags for the grid
```

---

## Pattern A — Standalone Template (full HTML in one file)

Use this when the template file contains the complete HTML document from `<!DOCTYPE html>` to `</html>`. Call `noAppendFile()` at the very top to prevent ProcessWire from automatically appending `_main.php`.

```php
<?= $pagegrid->noAppendFile($page); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $page->title ?></title>
    <?= $pagegrid->styles($page); ?>
</head>
<body>
    <?= $pagegrid->renderGrid($page); ?>
    <?= $pagegrid->scripts($page); ?>
</body>
</html>
```

This is how the auto-generated `pagegrid-page.php` works.

---

## Pattern B — Markup Regions (ProcessWire default)

Most ProcessWire sites use markup regions: `_main.php` contains the full HTML structure and defines regions by `id` attribute; each template file outputs HTML that gets merged into the matching region.

**Step 1:** Read `site/templates/_main.php` to find the region IDs available (e.g., `id="content"`, `id="main"`).

**Step 2:** In your template file, output the 3 render calls inside a tag matching that region ID:

```php
<div id="content">
    <?= $pagegrid->styles($page); ?>
    <?= $pagegrid->renderGrid($page); ?>
    <?= $pagegrid->scripts($page); ?>
</div>
```

ProcessWire will merge this into the `<div id="content">` defined in `_main.php`. No `noAppendFile()` is needed.

---

## Multiple PageGrid Fields on One Page

If a page has more than one PageGrid field (e.g., `mygrid` and `sidebar`), render them individually by field name instead of using `renderGrid()`:

```php
<?= $page->mygrid; ?>
<?= $page->sidebar; ?>
```

`styles()` and `scripts()` still use `$page` — they automatically include assets for all fields on the page.

---

## Shared Blocks — `renderItem()`

Use `renderItem()` to pull a specific block from *another* page (e.g., a header or footer block managed on a central page) and render it anywhere in your site template:

```php
<?php
// Get a specific block by name or ID
$header = $pages->get('pg_group_3224');  // or use 'name=pg-group-3224'

echo $pagegrid->styles($header);
echo $pagegrid->renderItem($header);
echo $pagegrid->scripts($header);
?>
```

---

## Inside Block Template Files

These two helpers are for use **inside block template files** (not site templates):

### `getPage($page)`

Inside a block template, `$page` refers to the block itself. Use `getPage()` to access the content page that hosts the grid:

```php
// site/templates/blocks/my_block.php
$mainPage = $pagegrid->getPage($page);
echo $mainPage->title;
```

### `isBackend()`

Returns `true` when the block is being rendered in the ProcessWire admin (e.g., the PageGrid editor), `false` on the frontend:

```php
if($pagegrid->isBackend()) {
    // Admin-only markup (e.g., edit hints, placeholder content)
} else {
    // Frontend markup
}
```
