# PageGrid Page Designer

> **CLI agents with AgentTools:** you can delegate layout creation to the Engineer sub-agent instead of writing PHP manually: `php index.php --at-engineer-migrate "REQUEST"`

This skill covers everything you need to build or modify a PageGrid layout programmatically using PHP — adding blocks, setting content, applying styles, and writing migrations.

---

## Concepts

PageGrid stores blocks as standard ProcessWire pages in a container tree under `/admin/setup/pagegrid/pg-items/`. Every block is a page. Styles (CSS Grid layout, colors, spacing, etc.) are stored as metadata (`pg_styles`) on each block page — not as inline styles.

```
/admin/setup/pagegrid/pg-items/
  pg-{pageId}/          ← one per content page
    pg-{fieldId}/       ← "field container" — one per PageGrid field
      pg-group-1049/    ← top-level blocks
        pg-text-1051/   ← nested blocks (children of a group/slider/accordion)
```

**Never create containers manually.** Use `getFieldContainer()` to look them up.

---

## Getting Started

```php
// Always use wire('modules') — safer in migrations than the $pagegrid shortcut
$pagegrid = wire('modules')->get('InputfieldPageGrid');

// Get the field container for a content page (required before adding items)
$mainPage = $pages->get('name=home');  // never use IDs — use name or path
$fc = $pagegrid->getFieldContainer($mainPage);
```

---

## The Three Helpers

### 1. `getFieldContainer(Page $page): ?Page`

Returns the `pg-{fieldId}` container for a content page. This is the parent for all top-level blocks.

```php
$fc = $pagegrid->getFieldContainer($mainPage);
```

### 2. `addItem(string $templateName, Page $parent): ?Page`

Creates a new block with the correct two-step naming convention (`pg-text-1041`, etc.).
- Pass the **field container** as `$parent` for top-level blocks.
- Pass a **group / slider / accordion block** as `$parent` for nested blocks.

```php
$block = $pagegrid->addItem('pg_text', $fc);       // top-level
$child = $pagegrid->addItem('pg_text', $group);    // nested inside a group
```

### 3. `setStyles(Page $block, array $cssProps, string $breakpoint = 'base', string $elementId = 'pgitem', array $options = []): void`

Merges CSS properties into the block's `pg_styles` metadata. Always use this — never use inline `style=""` attributes.

```php
// Block wrapper (pgitem) at base breakpoint — the most common usage
$pagegrid->setStyles($block, [
    'grid-column-start' => '1',
    'grid-column-end'   => 'span 6',
    'padding'           => '20px',
    'background-color'  => 'rgba(255,255,255,1)',
]);

// Override at a smaller breakpoint (mobile)
$pagegrid->setStyles($block, ['padding' => '10px'], 's');

// Style an inner HTML element (e.g. an <img> inside the block)
$pagegrid->setStyles($block, ['border-radius' => '12px'], 'base', 'img', ['tagName' => 'img', 'cssClass' => '']);

// Style a data-class element (custom element inside the block template)
$pagegrid->setStyles($block, ['color' => 'rgba(255,255,255,1)'], 'base', 'my-el-1127', ['tagName' => 'span', 'cssClass' => 'my-el-1127']);

// Page-level background or body font — set on the field container, not the page
$pagegrid->setStyles($fc, ['background-color' => 'rgba(0,0,0,1)', 'padding' => '60px']);
$pagegrid->setStyles($fc, ['font-family' => 'Inter, sans-serif'], 'base', 'body', ['tagName' => 'body', 'cssClass' => 'body']);

// Remove a property
$pagegrid->setStyles($block, ['background-color' => null]);
```

---

## Block Templates

| Template | Purpose | How to set content |
|----------|---------|-------------------|
| `pg_text` | Plain text | `$block->setAndSave('pg_text', 'Hello')` |
| `pg_editor` | Rich text / CKEditor | `$block->setAndSave('pg_editor', '<p>...</p>')` |
| `pg_html` | Raw HTML | `$block->setAndSave('pg_html', '<div>...</div>')` |
| `pg_image` | Single image | `$block->of(false); $block->pg_image->add('/path/to/file.jpg'); $block->save();` |
| `pg_gallery` | Single image (one block per gallery image) | same as pg_image |
| `pg_video` | Video file | `$block->of(false); $block->pg_video->add('/path/to/file.mp4'); $block->save();` |
| `pg_group` | Grid container — children are blocks | `addItem('pg_text', $group)` |
| `pg_slider` | Carousel — slides are child blocks | `addItem('pg_image', $slider)` |
| `pg_accordion` | Accordion — items are child blocks | `addItem('pg_editor', $accordion)` |

More templates are available in the PageGridBlocks module — check `reference.md` for the full list.

---

## Setting Content Fields

### Scalar fields (text, textarea, integer, checkbox, URL, email, CKEditor)

Use `setAndSave()` — it handles `of(false)` and `save()` internally:

```php
$block->setAndSave('pg_text',   'Hello world');
$block->setAndSave('pg_editor', '<p>Rich text</p>');
$block->setAndSave('pg_html',   '<div>Raw HTML</div>');
```

### Image and file fields (`pg_image`, `pg_gallery`, `pg_video`)

Use `->add()` then `->save()`:

```php
$block->of(false);
$block->pg_image->add('/absolute/path/to/file.jpg');
$block->save();
```

### Child-page blocks (`pg_group`, `pg_slider`, `pg_accordion`)

Children are blocks themselves — use `addItem()` with the parent block:

```php
// Slider slides
$slide = $pagegrid->addItem('pg_image', $sliderBlock);
$slide->of(false);
$slide->pg_image->add('/path/to/slide.jpg');
$slide->save();

// Accordion items
$item = $pagegrid->addItem('pg_editor', $accordionBlock);
$item->setAndSave('pg_editor', '<p>Content</p>');
```

### Page reference fields

```php
// Single reference
$block->setAndSave('my_page_ref', 1042);

// Multi-value reference
$block->of(false);
$block->my_pages_field->add(1042);
$block->save();
```

---

## Complete Workflow Example

```php
$pagegrid = wire('modules')->get('InputfieldPageGrid');
$mainPage = $pages->get('name=home');
$fc = $pagegrid->getFieldContainer($mainPage);

// Full-width 12-column group
$group = $pagegrid->addItem('pg_group', $fc);
$pagegrid->setStyles($group, [
    'grid-column-start'     => '1',
    'grid-column-end'       => 'span 12',
    'display'               => 'grid',
    'grid-template-columns' => 'repeat(12, 1fr)',
    'gap'                   => '20px',
]);

// Two columns side by side
$left = $pagegrid->addItem('pg_text', $group);
$left->setAndSave('pg_text', 'Left column');
$pagegrid->setStyles($left, ['grid-column-start' => '1', 'grid-column-end' => 'span 6']);

$right = $pagegrid->addItem('pg_text', $group);
$right->setAndSave('pg_text', 'Right column');
$pagegrid->setStyles($right, ['grid-column-start' => '7', 'grid-column-end' => 'span 6']);

// Responsive: override padding on mobile
$pagegrid->setStyles($group, ['gap' => '10px'], 's');

// Modify an existing block — find it by name, then setStyles()
$target = $fc->children()->get('name=pg-text-1041');
$pagegrid->setStyles($target, ['color' => 'rgba(255, 0, 0, 1)']);
```

---

## Rules (read carefully — these are the most common mistakes)

**Colors** — always RGBA, never hex or named colors:
```php
// ✅ 'rgba(255, 0, 0, 1)'
// ❌ '#FF0000'  or  'red'
```

**Grid positions** — `grid-column-start` is a number, `grid-column-end` is `'span N'`:
```php
// ✅ 'grid-column-start' => '1',  'grid-column-end' => 'span 6'
// ❌ 'grid-column-end' => '7'
```

**No inline styles** — always use `setStyles()`, never `style="..."` in HTML content:
```php
// ❌ $block->pg_editor = '<h2 style="font-size:26px">Title</h2>';
// ✅ $block->setAndSave('pg_editor', '<h2>Title</h2>');
//    $pagegrid->setStyles($block, ['font-size' => '26px'], 'base', 'h2', ['tagName' => 'h2', 'cssClass' => '']);
```

**Page/body styles go on the field container** — not the main page:
```php
// ❌ $pagegrid->setStyles($mainPage, [...]);
// ✅ $pagegrid->setStyles($fc, [...]);
```

**`pg_group` defaults to `display:grid` with 12 columns** — don't blindly override it. Check first:
```php
$styles = $group->meta('pg_styles');
$currentDisplay = $styles['pgitem']['breakpoints']['base']['css']['display'] ?? 'grid';
```

**Modifying existing blocks** — find the block by name, call `setStyles()`. Never delete and recreate:
```php
// ✅
$target = $fc->children()->get('name=pg-text-1041');
$pagegrid->setStyles($target, ['color' => 'rgba(0,0,0,1)']);
```

**Responsive** — define `base` first, then override only what changes at smaller breakpoints:
```php
$pagegrid->setStyles($block, ['padding' => '40px']);        // base (desktop)
$pagegrid->setStyles($block, ['padding' => '16px'], 's');   // mobile only
```

**Never use database IDs to look up pages** — IDs differ between environments. Use `name`, `path`, or `name + parent`:
```php
// ✅ $pages->get('name=home')
// ❌ $pages->get(1049)
```

---

## Migration Boilerplate

The PHP below works in any context — AgentTools is not required to write migrations.

**CLI agents:** read `site/modules/AgentTools/AGENTS.md` for CLI commands, file naming, and applying. Or delegate: `php index.php --at-engineer-migrate "REQUEST"`.

**IDE agents:** write migration files directly using the pattern below. Files go in `site/assets/at/migrations/`, named `YYYYMMDDhhmmss_description.php`.

```php
<?php namespace ProcessWire;

$name = wire('at')->migrations->getName(__FILE__);
echo "# $name\n\n";

// State check — make the migration safe to run more than once
if($pages->get('name=my-page')->id) {
    echo "- Skipped: already applied.\n";
    return;
}

$pagegrid = wire('modules')->get('InputfieldPageGrid');
$mainPage = $pages->get('name=home');
$fc = $pagegrid->getFieldContainer($mainPage);

// ... layout code here ...

echo "- $name has been applied\n";
```
