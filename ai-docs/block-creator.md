# Creating a Custom Block Template

> **CLI agents with AgentTools:** you can delegate block registration to the Engineer sub-agent: `php index.php --at-engineer-migrate "Create a custom block template called my_block with a text field"`

This guide covers creating a custom PageGrid block template and registering it with a
PageGrid field — both manually (UI) and programmatically (migration script).

---

## Ready-made Blocks (PageGridBlocks Module)

Ready-made block templates live in `site/modules/PageGridBlocks/blocks/`. To modify one, copy it to `site/templates/blocks/` — PageGrid automatically uses the local copy over the module version. Never edit the module files directly; they are overwritten on updates.

```bash
cp site/modules/PageGridBlocks/blocks/pg_card.php site/templates/blocks/pg_card.php
```

---

## Overview

A block is a PHP template file in `site/templates/blocks/`. PageGrid renders it inside a
wrapper element and gives users drag, resize, and style controls. Optionally add a `.css`
and/or `.js` file with the same base name for block-scoped styles and scripts.

```
site/templates/blocks/
  my_block.php      ← required
  my_block.css      ← optional, auto-loaded
  my_block.js       ← optional, auto-loaded
```

> **Naming:** Never prefix custom block names with `pg_` — that prefix is reserved for
> built-in PageGrid templates and block modules. Use any other name, e.g. `card`, `hero`,
> `testimonial`.

---

## Block Template File

### Minimal Example

```php
<?php namespace ProcessWire; ?>
<div pg-wrapper>
    <h2><?= $page->my_title ?></h2>
    <p><?= $page->my_text ?></p>
</div>
```

Inside a block template, `$page` refers to the block item page. All ProcessWire API
variables (`$pages`, `$config`, `$user`, etc.) and the `$pagegrid` helper are available.

---

### Wrapper Element — `pg-wrapper`

Add `pg-wrapper` to the root element of your markup. PageGrid uses this element as the
interactive block wrapper in the editor (drag handle, resize handles, style panel target).
Without it, PageGrid wraps your output in a plain `<div>`.

```php
<section class="hero" pg-wrapper>
    <h1><?= $page->title ?></h1>
</section>
```

---

### Tag Switcher — `pg-tags`

Allow the user to change the wrapper tag in the editor by listing allowed tags:

```php
<h2 pg-tags="h1 h2 h3 h4 h5 h6 p" pg-wrapper>
    <?= $page->my_title ?>
</h2>
```

---

### Container Blocks — `pg-children`

To allow users to nest other blocks inside yours, add `pg-children` and loop through
`$page->children()` using `$pagegrid->renderItem()`:

```php
<div pg-children="true" pg-wrapper>
    <?php foreach ($page->children() as $item): ?>
        <?= $pagegrid->renderItem($item); ?>
    <?php endforeach; ?>
</div>
```

Restrict which block types can be nested by listing template names:

```php
<div pg-children="card testimonial" pg-wrapper>
    ...
</div>
```

Additional children attributes:

| Attribute | Value | Effect |
|-----------|-------|--------|
| `pg-children-label` | `"Slides"` | Label shown on the children tab / add button |
| `pg-children-tab` | `"append"` or `"prepend"` | Where the children field appears in the edit panel |
| `pg-autotitle` | `"false"` | User enters the child's title manually instead of auto-generated |

---

### Style Panel — `data-class`

Add `data-class` to inner elements to make them individually targetable in the style panel:

```php
<div pg-wrapper>
    <img src="..." data-class="my-block-image">
    <span class="label" data-class="my-block-label"><?= $page->my_text ?></span>
</div>
```

To let users assign custom CSS classes to inner elements, use `getCssClasses()`:

```php
<span class="label <?= $pagegrid->getCssClasses($page, 'label') ?>" data-class="label">
    <?= $page->my_text ?>
</span>
```

---

### Backend vs Frontend

```php
<?php if ($pagegrid->isBackend()): ?>
    <!-- Shown only in the PageGrid editor -->
    <div class="placeholder">My Block</div>
<?php else: ?>
    <!-- Shown only on the live site -->
    <div><?= $page->my_text ?></div>
<?php endif; ?>
```

---

### Getting the Main Page

Inside a block template, `$page` is the block item, not the content page. To get the page
that hosts the PageGrid field:

```php
$mainPage = $pagegrid->getPage($page);
```

---

## Registering a Block via the Admin UI

1. Place your `my_block.php` file in `site/templates/blocks/`.
2. Go to **Setup > PageGrid** in the admin.
3. Under **Block templates**, select `my_block` from the list and click **Save**.
   PageGrid auto-creates the ProcessWire template if it does not yet exist.
4. *(Optional)* Go to **Setup > Templates**, find `my_block`, and add the fields your
   block needs.

---

## Registering a Block via Migration

Use this approach when the block (and its fields) must be reproducible across environments.

### What the migration does

1. Creates the ProcessWire template (Fieldgroup + Template with `tags = 'Blocks'`).
2. Creates any custom fields and adds them to the template.
3. Registers the template with the PageGrid field by updating `$pgField->template_id`.
   Saving the field triggers a hook that syncs the value into the module config.

### The Registration Step (the non-obvious part)

```php
// Get the PageGrid field by name (check site-map.json for the correct field name)
$pgField = $fields->get('pagegrid');

// Add the new template to the allowed block list
$existing = is_array($pgField->template_id) ? $pgField->template_id : [];
$pgField->template_id = array_unique(array_merge($existing, [$t->id]));
$pgField->save(); // triggers updateFieldSettings hook → syncs to module config
```

> If a site has **multiple PageGrid fields**, repeat this block for each field that should
> allow the new block. Check `site/assets/at/site-map.json` for all `FieldtypePageGrid`
> fields.

---

### Complete Migration Example

```php
<?php namespace ProcessWire;

$name = wire('at')->migrations->getName(__FILE__);
echo "# $name\n\n";

// ── Idempotency check ─────────────────────────────────────────────────────────
if ($templates->get('card')) {
    echo "- Skipped: template 'card' already exists.\n";
    return;
}

// ── 1. Create the block template file ─────────────────────────────────────────
$blockFile = $config->paths->templates . 'blocks/card.php';
if (!file_exists($blockFile)) {
    file_put_contents($blockFile, <<<'PHP'
<?php namespace ProcessWire; ?>
<div pg-wrapper>
    <h2><?= $page->card_title ?></h2>
    <p><?= $page->card_text ?></p>
</div>
PHP);
    echo "- Created block file: site/templates/blocks/card.php\n";
}

// ── 2. Create a custom text field ─────────────────────────────────────────────
if (!$fields->get('card_title')) {
    $f = new Field();
    $f->type = $fieldtypes->get('FieldtypeText');
    $f->name = 'card_title';
    $f->label = 'Card Title';
    $f->save();
    echo "- Created field: card_title\n";
}

if (!$fields->get('card_text')) {
    $f = new Field();
    $f->type = $fieldtypes->get('FieldtypeTextarea');
    $f->name = 'card_text';
    $f->label = 'Card Text';
    $f->save();
    echo "- Created field: card_text\n";
}

// ── 3. Create the ProcessWire template ────────────────────────────────────────
$fg = new Fieldgroup();
$fg->name = 'card';
$fg->add($fields->get('title'));
$fg->add($fields->get('card_title'));
$fg->add($fields->get('card_text'));
$fg->save();

$t = new Template();
$t->name     = 'card';
$t->fieldgroup = $fg;
$t->tags     = 'Blocks'; // groups it with other block templates in the admin
$t->save();
echo "- Created template: card\n";

// ── 4. Register with the PageGrid field ───────────────────────────────────────
$pgField = $fields->get('pagegrid'); // adjust to your field name
if ($pgField && $pgField->type instanceof FieldtypePageGrid) {
    $existing = is_array($pgField->template_id) ? $pgField->template_id : [];
    $pgField->template_id = array_unique(array_merge($existing, [$t->id]));
    $pgField->save();
    echo "- Registered 'card' with pagegrid field\n";
} else {
    echo "- Warning: pagegrid field not found — register the block manually in Setup > PageGrid\n";
}

echo "- $name has been applied\n";
```

---

## File Locations

| Purpose | Path |
|---------|------|
| Block template file | `site/templates/blocks/my_block.php` |
| Block CSS (auto-loaded) | `site/templates/blocks/my_block.css` |
| Block JS (auto-loaded) | `site/templates/blocks/my_block.js` |
| Migration files | `site/assets/at/migrations/` |
| PageGrid field settings | **Setup > PageGrid** in admin |
