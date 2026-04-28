# PageGrid Reference

> **CLI agents with AgentTools:** you can delegate migration creation to the Engineer sub-agent instead of writing PHP manually: `php index.php --at-engineer-migrate "REQUEST"`

## Page Hierarchy

Blocks live under a three-level container tree, auto-created by a hook when any page with a PageGrid field is saved:

```
/admin/setup/pagegrid/pg-items/
  pg-{pageId}/          ← one per content page
    pg-{fieldId}/       ← one per PageGrid field (the "field container")
      pg-group-1049/    ← top-level blocks
        pg-text-1051/   ← nested blocks (children of a group/slider/accordion)
```

Never create containers manually — use `getFieldContainer()` to look them up.

---

## Block Templates

| Template | Purpose | Content field |
|----------|---------|---------------|
| `pg_text` | Plain text | `pg_text` (Textarea) |
| `pg_editor` | Rich text / CKEditor | `pg_editor` (Textarea) |
| `pg_html` | Raw HTML | `pg_html` (Textarea) |
| `pg_image` | Single image | `pg_image` (Image, use `->add()`) |
| `pg_gallery` | Single image (a "gallery" = multiple pg_gallery blocks on the grid) | `pg_gallery` (Image, use `->add()`) |
| `pg_video` | Video file | `pg_video` (File, use `->add()`) |
| `pg_group` | Grid container — items are child pages | `addItem()` |
| `pg_slider` | Carousel — slides are child pages (`pg_image`, `pg_video`, `pg_editor`, `pg_group`) | `addItem()` |
| `pg_accordion` | Accordion — items are child pages (any template) | `addItem()` |
| `pg_code` | Code block | `pg_code` (Textarea) |
| `pg_iframe` | Embed iframe | `pg_iframe_url` (Text) |
| `pg_spacer` | Empty spacer | (none) |

More templates available in the PageGridBlocks module.

---

## The Three Helpers

Access via: `$pagegrid = wire('modules')->get('InputfieldPageGrid');`

### `getFieldContainer(Page $page): ?Page`

Pure lookup — returns the `pg-{fieldId}` container for a page.

```php
$fc = $pagegrid->getFieldContainer($mainPage);
```

### `addItem(string $templateName, Page $parent): ?Page`

Creates a block with correct two-step naming (`pg-text-1041` etc.). Returns the page ready for content.
`$parent` is the field container for top-level blocks, or a group/slider/accordion page for nested blocks.

```php
$block  = $pagegrid->addItem('pg_text',  $fc);       // top-level
$nested = $pagegrid->addItem('pg_editor', $group);   // inside a group
```

#### Setting content after `addItem()`

**Scalar fields** (text, textarea, integer, checkbox, URL, CKEditor):
```php
$block->setAndSave('pg_text',   'Hello');
$block->setAndSave('pg_editor', '<p>Rich text</p>');
$block->setAndSave('pg_html',   '<div>Raw HTML</div>');
```

**Image / file fields** — use `->add()` then `->save()`:
```php
$block->of(false);
$block->pg_image->add('/absolute/path/to/file.jpg');
$block->save();
```

**Child-page blocks** (`pg_group`, `pg_slider`, `pg_accordion`) — items are child pages:
```php
$slide = $pagegrid->addItem('pg_image', $sliderBlock);
$slide->of(false);
$slide->pg_image->add('/path/to/slide.jpg');
$slide->save();
```

**Single page reference:**
```php
$block->setAndSave('my_page_ref', 1042);
```

**Multi-value page reference:**
```php
$block->of(false);
$block->my_pages_field->add(1042);
$block->save();
```

### `setStyles(Page $block, array $cssProps, string $breakpoint = 'base', string $elementId = 'pgitem', array $options = []): void`

Merges CSS into `pg_styles` metadata. Handles all structural rules. Pass `null` to remove a property.

```php
// Block wrapper — base breakpoint (default)
$pagegrid->setStyles($block, ['background-color' => 'rgba(255,0,0,1)', 'padding' => '20px']);

// Different breakpoint
$pagegrid->setStyles($block, ['padding' => '10px'], 's');

// Inner element by tag (tagName required)
$pagegrid->setStyles($block, ['border-radius' => '12px'], 'base', 'img', ['tagName' => 'img', 'cssClass' => '']);

// Inner element by data-class
$pagegrid->setStyles($block, ['color' => 'rgba(255,255,255,1)'], 'base', 'my-el-1127', ['tagName' => 'span', 'cssClass' => 'my-el-1127']);

// Page wrapper styles — set on the field container, not the main page
$pagegrid->setStyles($fc, ['background-color' => 'rgba(0,0,0,1)', 'padding' => '60px']);

// Body styles for this page only — also on field container
$pagegrid->setStyles($fc, ['font-family' => 'Inter, sans-serif'], 'base', 'body', ['tagName' => 'body', 'cssClass' => 'body']);

// Remove a property
$pagegrid->setStyles($block, ['background-color' => null]);
```

---

## Global Classes

Global classes are reusable style definitions that any block can reference. They live under `/admin/setup/pagegrid/pg-classes/` with template `pg_container`.

### Create a Global Class

```php
$classParent = $pages->get('template=pg_container, name=pg-classes');

$class = new Page();
$class->template = 'pg_container';
$class->parent   = $classParent;
$class->name     = 'my-class';
$class->title    = 'my-class';
$class->save();

// Set styles — use setStyles() on the class page
$pagegrid->setStyles($class, [
    'padding-left'  => '30px',
    'padding-right' => '30px',
    'max-width'     => '1200px',
    'margin'        => '0 auto',
]);
```

### Apply a Global Class to a Block

Global classes are referenced via `cssClass` / `cssClasses` in the block's `pgitem` metadata. These keys are not set by `setStyles()` — set them directly:

```php
$styles = $block->meta('pg_styles') ?? [];
$styles['pgitem']['cssClass']   = 'my-class';
$styles['pgitem']['cssClasses'] = 'my-class ';  // trailing space is intentional
$block->meta('pg_styles', $styles);
```

### Query Global Classes

```php
// Get one
$class = $pages->get('name=container, template=pg_container');

// List all
$classes = $pages->find('template=pg_container, parent.name=pg-classes');
```

> **Don't confuse** `pg_container` pages under `pg-classes/` (global classes) with those under `pg-items/` (field containers).

---

## CSS Quick Rules

**Colors** — always RGBA, never hex or color names:
```
'rgba(255, 0, 0, 1)'        // red, fully opaque
'rgba(0, 0, 0, 0)'          // transparent
'rgba(255, 255, 255, 0.5)'  // white 50% opacity
```

**Grid positions** — start is an integer, end is `'span N'`:
```
'grid-column-start' => '1'       ✅
'grid-column-end'   => 'span 6'  ✅
'grid-column-end'   => '7'       ❌
```

**Dynamic column count** — `pg_group` defaults to 12 columns, but check the parent before assuming span values:
```php
$parentCss = $parent->meta('pg_styles')['pgitem']['breakpoints']['base']['css'] ?? [];
$gtc = $parentCss['grid-template-columns'] ?? '';
$cols = preg_match('/repeat\((\d+),/', $gtc, $m) ? (int) $m[1] : 12;
// 12-col: full='span 12', half='span 6', third='span 4'
// 6-col:  full='span 6',  half='span 3', third='span 2'
```

**Breakpoints:**

| Key | Media query | Use for |
|-----|------------|---------|
| `base` | (no query — applies to all) | Desktop / default |
| `s` | `max-width: 640px` | Mobile |
| `m` | `max-width: 960px` | Tablet |
| `l` | `max-width: 1280px` | Large screens |

Define `base` first; only override what changes in other breakpoints.

**`pg_group` defaults to `display:grid` / 12 columns.** Check before overriding:
```php
$css = $group->meta('pg_styles')['pgitem']['breakpoints']['base']['css'] ?? [];
$display = $css['display'] ?? 'grid';  // default is already grid
```

---

## Complete Migration Example

A full migration using all three helpers — copy and adapt. For boilerplate, naming conventions,
and CLI commands see `site/modules/AgentTools/AGENTS.md`. If AgentTools is not installed,
inform the user it is required for running migrations.

```php
<?php namespace ProcessWire;

$name = wire('at')->migrations->getName(__FILE__);
echo "# $name\n\n";

if ($pages->get('name=my-landing-page')->id) {
    echo "- Skipped: already applied.\n";
    return;
}

$pagegrid = wire('modules')->get('InputfieldPageGrid');

// 1. Create the content page — hook auto-creates containers on save
$p = new Page();
$p->template = 'pagegrid-page';
$p->parent   = $pages->get('/');
$p->name     = 'my-landing-page';
$p->title    = 'My Landing Page';
$p->save();
echo "- Created page: {$p->name} (ID: {$p->id})\n";

// 2. Get field container (pure lookup — already created by hook)
$fc = $pagegrid->getFieldContainer($p);
if (!$fc) { echo "- Fatal: field container not found.\n"; return; }

// 3. Full-width group
$group = $pagegrid->addItem('pg_group', $fc);
$pagegrid->setStyles($group, [
    'grid-column-start' => '1',
    'grid-column-end'   => 'span 12',
    'gap'               => '20px',
    'padding'           => '40px',
]);

// 4. Two blocks side by side inside the group
$left = $pagegrid->addItem('pg_text', $group);
$left->setAndSave('pg_text', 'Left column');
$pagegrid->setStyles($left, ['grid-column-start' => '1', 'grid-column-end' => 'span 6']);

$right = $pagegrid->addItem('pg_editor', $group);
$right->setAndSave('pg_editor', '<h2>Right</h2><p>Content.</p>');
$pagegrid->setStyles($right, ['grid-column-start' => '7', 'grid-column-end' => 'span 6']);

// Responsive: stack to full width on mobile
$pagegrid->setStyles($left,  ['grid-column-start' => '1', 'grid-column-end' => 'span 12'], 's');
$pagegrid->setStyles($right, ['grid-column-start' => '1', 'grid-column-end' => 'span 12'], 's');

// 5. Update an existing block's styles
// $block = $pages->get('name=pg-text-1041');
// $pagegrid->setStyles($block, ['background-color' => 'rgba(0, 255, 0, 1)']);

echo "- $name has been applied\n";
```

---

## Reading Raw Styles

Use `meta('pg_styles')` when you need to read existing values (e.g. to calculate a position or check a current color):

```php
$styles = $block->meta('pg_styles');
$css    = $styles['pgitem']['breakpoints']['base']['css'] ?? [];
$bg     = $css['background-color'] ?? 'transparent';
$colEnd = $css['grid-column-end']  ?? 'span 1';
```

For writing, always use `setStyles()` — not raw `meta('pg_styles', $array)`.

---

## File Locations

| Purpose | Path |
|---------|------|
| Migrations | `/site/assets/at/migrations/` |
| PageGrid admin | `/admin/setup/pagegrid/` |
| Block pages | `/admin/setup/pagegrid/pg-items/` |
| Global classes | `/admin/setup/pagegrid/pg-classes/` |
| Animations | `/admin/setup/pagegrid/pg-animations/` |
| Migration logs | `/site/assets/logs/migrations.txt` |
