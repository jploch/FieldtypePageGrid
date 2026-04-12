# PageGrid Quick Reference

## Quick Facts

| Concept | Details |
|---------|---------|
| **What is a block?** | A ProcessWire page with a PageGrid template |
| **Where do blocks live?** | `/admin/setup/pagegrid/pg-items/pg-{page-id}/pg-{field-id}/` |
| **Block naming** | `pg-{template-name}-{block-id}` (e.g., `pg-text-1041`) |
| **Key metadata** | `pg_styles` - Stores positioning and styling via CSS Grid |
| **Positioning** | CSS Grid properties: `grid-column-start`, `grid-row-start`, etc. |
| **Styling** | Background color, padding, margin, border-radius, opacity, etc. |
| **Breakpoints** | base, s (mobile), m (tablet), l (large) |
| **Create blocks** | Via mutations or migrations (see migrations.md) |
| **Access** | Use standard ProcessWire APIs: `$pages->find()`, `$pages->get()` |
| **Modify** | Change fields with `$block->of(false)` before editing |

---

## Page Path Patterns

**Main page block container:**
```
/admin/setup/pagegrid/pg-items/pg-{main-page-id}/
```

**Field container:**
```
/admin/setup/pagegrid/pg-items/pg-{main-page-id}/pg-{field-id}/
```

**Individual block:**
```
/admin/setup/pagegrid/pg-items/pg-{main-page-id}/pg-{field-id}/pg-{template-name}-{id}/
```

**Example (page 1049 with a PageGrid field of ID 98, text block 1041):**
```
/admin/setup/pagegrid/pg-items/pg-{pageId}/pg-{fieldId}/pg-text-{blockId}/
/admin/setup/pagegrid/pg-items/pg-1049/pg-98/pg-text-1041/
```

---

## Common API Calls

### Get a Block

```php
// By ID
$block = wire('pages')->get(1041);

// By name
$block = wire('pages')->get('name=pg-text-1041');

// By template
$textBlocks = wire('pages')->find('template=pg_text');
```

### Get Blocks in a Field

```php
// Dynamic: find the first PageGrid field on the page's template
$page = $pages->get(1049);
$pgField = null;
foreach ($page->template->fieldgroup as $f) {
    if ($f->type instanceof FieldtypePageGrid) {
        $pgField = $f;
        break;
    }
}

// Items parent: pg-{pageId}, Field container: pg-{fieldId}
$itemsParent = $pages->get("name=pg-{$page->id}, template=pg_container");
$fieldContainer = $pages->get("name=pg-{$pgField->id}, parent={$itemsParent}");

// Get all blocks in that field
$blocks = $fieldContainer->children();

// Get specific type
$textBlocks = $fieldContainer->find('template=pg_text');
```

### Create a Block (Simple)

```php
$block = new Page();
$block->template = wire('templates')->get('pg_text');
$block->parent = $fieldContainer;
$block->name = 'pg_text' . time();
$block->save();

$block->of(false);
$block->pg_text = 'Content';
$block->save();

$block->setAndSave('name', 'pg-text-' . $block->id);
$block->setAndSave('title', 'pg-text-' . $block->id);
```

### Modify Block Content

```php
$block->of(false);  // Turn off output formatting
$block->pg_text = 'New content';
$block->save();
```

### Get Block Metadata

```php
$styles = $block->meta('pg_styles');
$bgColor = $styles['pgitem']['breakpoints']['base']['css']['background-color'] ?? 'transparent';
```

### Update Block Metadata

```php
$styles = $block->meta('pg_styles') ?? [];
$styles['pgitem']['breakpoints']['base']['css']['background-color'] = 'rgba(255, 0, 0, 1)';
$block->meta('pg_styles', $styles);
```

### Style Inner Elements

Blocks can style child HTML elements within their markup (e.g., `<img>` in `pg_image`, `<p>` in `pg_editor`):

```php
$imgBlock = wire('pages')->get('name=pg-image-1079');
$styles = $imgBlock->meta('pg_styles') ?? [];

// Add border to the <img> inside the block
$styles['img'] = [
    'id' => 'img',
    'cssClass' => '',
    'tagName' => 'img',
    'breakpoints' => [
        'base' => [
            'css' => [
                'border-radius' => '12px',
                'border-style' => 'solid solid solid solid',
                'border-width' => '2px',
                'border-color' => 'rgba(0,0,0,1)',
            ],
            'size' => '@media (min-width: 640px)',
            'name' => 'base',
        ]
    ]
];
$imgBlock->meta('pg_styles', $styles);
```

**Known inner element keys:**

| Block Template | Key | Tag |
|---------------|-----|-----|
| `pg_image` | `img` | `<img>` |
| `pg_editor` | `p` | `<p>` |

### Delete a Block

```php
$block->delete(true);  // true = recursive delete
```

---

## CSS Grid Properties Quick Reference

### Positioning Properties

```php
'grid-column-start' => '1',    // Which column starts (1-based)
'grid-column-end' => 'span 3', // How many columns to span (span N)
'grid-row-start' => '2',       // Which row starts (1-based)
'grid-row-end' => 'span 2',    // How many rows to span (span N)
```

**Common Values:**
- `grid-column-start` / `grid-row-start`: plain number (1-based grid line), e.g. `'1'`, `'3'`
- `grid-column-end` / `grid-row-end`: **always `span N`** (number of columns/rows to span), e.g. `'span 2'`, `'span 12'`
- Auto: `'auto'` (let grid calculate)

### Visual Properties

```php
'background-color' => 'rgba(255, 0, 0, 1)',  // RGBA color
'padding' => '20px',                          // All sides
'padding-top' => '10px',                      // Individual sides
'margin' => '10px',                           // All sides
'border-radius' => '8px',                     // Rounded corners
'opacity' => '0.8',                           // Transparency
'box-shadow' => '0 2px 8px rgba(0,0,0,0.1)', // Drop shadow
'min-height' => '200px',                      // Minimum height
```

### Color Format

Always use RGBA:
```
rgba(red, green, blue, alpha)

Red:        rgba(255, 0, 0, 1)
Green:      rgba(0, 255, 0, 1)
Blue:       rgba(0, 0, 255, 1)
White:      rgba(255, 255, 255, 1)
Black:      rgba(0, 0, 0, 1)
Gray:       rgba(128, 128, 128, 1)
Transparent: rgba(0, 0, 0, 0)
```

---

## Metadata Example

```php
$styles = [
    'pgitem' => [
        'id' => 'pgitem',
        'tagName' => 'div',
        'cssClasses' => 'featured card',
        'attributes' => 'data-id="card-1"',
        'breakpoints' => [
            'base' => [
                // Desktop layout
                'grid-column-start' => '1',
                'grid-column-end' => 'span 2',
                'grid-row-start' => '1',
                'grid-row-end' => 'span 1',
                'css' => [
                    'background-color' => 'rgba(255, 255, 255, 1)',
                    'padding' => '20px',
                    'border-radius' => '8px',
                ]
            ],
            's' => [
                // Mobile layout (max-width: 640px)
                'grid-column-start' => '1',
                'grid-column-end' => 'span 1',
                'css' => [
                    'padding' => '12px',
                ]
            ],
            'm' => [
                // Tablet layout (max-width: 960px)
                'grid-column-start' => '1',
                'grid-column-end' => 'span 1',
                'css' => [
                    'padding' => '16px',
                ]
            ],
            'l' => [
                // Large layout (min-width: 1600px)
                'grid-column-start' => '1',
                'grid-column-end' => 'span 3',
                'css' => [
                    'padding' => '24px',
                ]
            ]
        ]
    ]
];
```

---

## Breakpoints Table

| Breakpoint | Media Query | Device | Width Range |
|-----------|------------|--------|------------|
| **base** | (min-width: 640px) | Desktop | >= 640px |
| **s** | (max-width: 640px) | Mobile | <= 640px |
| **m** | (max-width: 960px) | Tablet | <= 960px |
| **l** | (min-width: 1600px) | Large Desktop | >= 1600px |

**Order Matters:**
CSS applies in this order (later overrides earlier):
1. base
2. s
3. m
4. l

---

## Block Templates List

Common templates available:

| Template | Purpose | Key Fields |
|----------|---------|-----------|
| pg_text | Single-line text | pg_text |
| pg_editor | Rich text editor | pg_editor |
| pg_gallery | Image gallery | pg_gallery |
| pg_group | Container for nested blocks | (none - parent only) |
| pg_slider | Image carousel | pg_slider_imgs |
| pg_html | Custom HTML | pg_html |
| pg_image | Single image | pg_image |
| pg_video | Video embed | pg_video_url |
| ... | + 10+ more in PageGridBlocks | See module |

---

## Common Tasks

### Create a Text Block at Row 2, Column 3

```php
$block = new Page();
$block->template = wire('templates')->get('pg_text');
$block->parent = $fieldContainer;  // Your field container
$block->name = 'pg_text' . time();
$block->save();

$block->of(false);
$block->pg_text = 'My content';
$block->save();

// Position it
$styles = [
    'pgitem' => [
        'id' => 'pgitem',
        'breakpoints' => [
            'base' => [
                'grid-column-start' => '3',  // Column 3
                'grid-column-end' => 'span 1', // Width 1
                'grid-row-start' => '2',     // Row 2
                'grid-row-end' => 'span 1',  // Height 1
            ]
        ]
    ]
];
$block->meta('pg_styles', $styles);

$block->setAndSave('name', 'pg-text-' . $block->id);
$block->setAndSave('title', 'pg-text-' . $block->id);
```

### Change Block Background Color to Green

```php
$block = wire('pages')->get('name=pg-text-1041');
$styles = $block->meta('pg_styles') ?? [];

if (!isset($styles['pgitem']['breakpoints']['base'])) {
    $styles['pgitem']['breakpoints']['base'] = [];
}
if (!isset($styles['pgitem']['breakpoints']['base']['css'])) {
    $styles['pgitem']['breakpoints']['base']['css'] = [];
}

$styles['pgitem']['breakpoints']['base']['css']['background-color'] = 'rgba(0, 255, 0, 1)';
$block->meta('pg_styles', $styles);
```

### Get All Blocks with 'featured' Class

```php
// Use dynamic field lookup (see "Get Blocks in a Field" above)
foreach ($fieldContainer->children() as $block) {
    $styles = $block->meta('pg_styles');
    if (isset($styles['pgitem']['cssClasses']) && 
        strpos($styles['pgitem']['cssClasses'], 'featured') !== false) {
        echo 'Block: ' . $block->name . "\n";
    }
}
```

### Make Block Full-Width on Mobile

```php
$block = wire('pages')->get('name=pg-text-1041');
$styles = $block->meta('pg_styles') ?? [];

// Add mobile breakpoint
$styles['pgitem']['breakpoints']['s'] = [
    'grid-column-start' => '1',
    'grid-column-end' => 'span 1',  // Single column
    'css' => [
        'padding' => '12px',
    ]
];

$block->meta('pg_styles', $styles);
```

---

## Global Classes

Global classes are reusable style definitions shared across blocks. They live under `/admin/setup/pagegrid/pg-classes/` and use the `pg_container` template.

### Query a Global Class

```php
$class = wire('pages')->get('name=container, template=pg_container');
echo $class->path;  // /admin/setup/pagegrid/pg-classes/container/
$styles = $class->meta('pg_styles');
```

### List All Global Classes

```php
$classes = wire('pages')->find('parent=/admin/setup/pagegrid/pg-classes/, template=pg_container');
foreach ($classes as $c) {
    echo "{$c->name}: " . json_encode($c->meta('pg_styles')) . "\n";
}
```

### Create a Global Class

```php
$classParent = wire('pages')->get('/admin/setup/pagegrid/pg-classes/');

$class = new Page();
$class->template = wire('templates')->get('pg_container');
$class->parent = $classParent;
$class->name = 'my-class';
$class->title = 'my-class';
$class->save();

$styles = [
    'pgitem' => [
        'id' => 'pgitem',
        'cssClass' => 'my-class',
        'cssClasses' => 'my-class ',
        'tagName' => 'div',
        'breakpoints' => [
            'base' => [
                'css' => [
                    'padding-left' => '30px',
                    'padding-right' => '30px',
                    'max-width' => '1200px',
                    'margin' => '0 auto',
                ],
                'size' => '@media (min-width: 640px)',
                'name' => 'base',
            ]
        ]
    ]
];
$class->meta('pg_styles', $styles);
```

### Apply a Global Class to a Block

Blocks reference global classes via the `cssClass` or `cssClasses` key in their `pgitem` metadata:

```php
$block = wire('pages')->get('name=pg-group-1072');
$styles = $block->meta('pg_styles') ?? [];
$styles['pgitem']['cssClass'] = 'container';
$styles['pgitem']['cssClasses'] = 'container ';
$block->meta('pg_styles', $styles);
```

### Important Notes

- Global classes live under `/admin/setup/pagegrid/pg-classes/` (not `pg-items/`)
- The `cssClass` and `cssClasses` keys store the class name(s) on the block
- `cssClasses` typically has a trailing space
- The class page's `pg_styles` defines the CSS that gets applied to any block using that class
- Don't confuse `pg_container` pages under `pg-classes/` (global classes) with those under `pg-items/` (field containers)

---

## File Locations

```
Documentation:
/site/modules/FieldtypePageGrid/ai-docs/

Migration Files:
/site/assets/at/migrations/

PageGrid Admin:
/admin/setup/pagegrid/

Block Pages Storage:
/admin/setup/pagegrid/pg-items/

Global Classes Storage:
/admin/setup/pagegrid/pg-classes/

Log Files:
/site/assets/logs/migrations.txt
```

---

## Important Notes

1. **Blocks are pages** - Treat them like any ProcessWire page
2. **Always two-step name** - Temporary name with timestamp, then final name with ID
3. **turn off output formatting** - Use `$block->of(false)` before modifying fields
4. **Check resources exist** - Verify page, template, parent before using
5. **Metadata is separate** - Field data and styling are stored separately
6. **Color format** - Always use RGBA, not hex or color names
7. **Grid is 1-based** - First column/row is 1, not 0
8. **Responsive from smallest** - Define all breakpoints for full responsive support

---

## Troubleshooting

### Block Not Appearing in PageGrid
- ✅ Did you set the final name with ID?
- ✅ Is the parent correct (field container)?
- ✅ Does the template exist?

### Metadata Not Saving
- ✅ Did you call `$block->meta('pg_styles', $array)`?
- ✅ Check for syntax errors in the array
- ✅ Verify the block exists

### Grid Position Wrong
- ✅ Check grid-column-start/end values
- ✅ Verify grid-row-start/end values
- ✅ Confirm breakpoint is defined for your device width

### Color Not Showing
- ✅ Use RGBA format: `rgba(255, 0, 0, 1)`
- ✅ Alpha (last number) must be 0-1, not 0-255

### Responsive Not Working
- ✅ Define breakpoint 's' for mobile
- ✅ Check media queries match device width
- ✅ Verify breakpoint values are different from base

---

## Code Snippets

### Initialize Metadata Structure
```php
$styles = $block->meta('pg_styles') ?? [];
if (!isset($styles['pgitem'])) $styles['pgitem'] = [];
if (!isset($styles['pgitem']['breakpoints'])) $styles['pgitem']['breakpoints'] = [];
if (!isset($styles['pgitem']['breakpoints']['base'])) $styles['pgitem']['breakpoints']['base'] = [];
if (!isset($styles['pgitem']['breakpoints']['base']['css'])) $styles['pgitem']['breakpoints']['base']['css'] = [];
```

### Defensive Get with Default
```php
$bgColor = $block->meta('pg_styles')['pgitem']['breakpoints']['base']['css']['background-color'] ?? 'transparent';
```

### Update Specific Breakpoint
```php
$styles = $block->meta('pg_styles');
$styles['pgitem']['breakpoints']['s']['css']['padding'] = '10px';
$block->meta('pg_styles', $styles);
```

### Delete a Property
```php
$styles = $block->meta('pg_styles');
unset($styles['pgitem']['breakpoints']['base']['margin']);
$block->meta('pg_styles', $styles);
```

---

## Official PageGrid Template API

These functions are provided by the `$pagegrid` variable and are used in your template files to render PageGrid content.

### Required Core Functions

These three functions are **required** in your template (e.g., `site/templates/pagegrid-page.php`):

#### renderGrid($page)

Renders all PageGrid items and their markup for a page.

```php
<?= $pagegrid->renderGrid($page) ?>
```

**Returns:** string (HTML markup)

**Multiple fields:** If you have multiple PageGrid fields on one page, you can render them individually:

```php
<?= $page->mygrid; ?>
<?= $page->mygrid2; ?>
```

#### styles($page, $loadDefaults = true)

Renders the CSS required for the page. Automatically detects and loads `.css` files from block folders.

```php
<?= $pagegrid->styles($page) ?>
```

**Returns:** string (HTML `<link>` and `<style>` tags)

**Advanced:** Load styles for a specific item only, skipping core styles:

```php
<?php
$header = $pages->get('pg_group_3224');
echo $pagegrid->styles($header, 0);  // 0 = skip core styles
?>
```

#### scripts($page)

Renders the JavaScript required for the page, including enabled plugins. Automatically detects and loads `.js` files from block folders.

```php
<?= $pagegrid->scripts($page) ?>
```

**Returns:** string (HTML `<script>` tags)

### Utility Functions

#### renderItem($page)

Renders a single PageGrid item. Used in two scenarios:

**A. Nesting in block templates** - Render child items inside a parent block (sliders, accordions, tabs):

```php
<section pg-children="true" pg-wrapper>
  <?php foreach($page->children() as $item) { ?>
    <?= $pagegrid->renderItem($item) ?>
  <?php } ?>
</section>
```

**B. Cross-rendering from page templates** - Pull a block from another page and render it:

```php
<?php
$header = $pages->get('pg_group_3224');
echo $pagegrid->styles($header);
echo $pagegrid->renderItem($header);
echo $pagegrid->scripts($header);
?>
```

**Returns:** string (HTML markup for the item)

#### getPage($page)

In block templates, `$page` refers to the block item. Use this function to get the main ProcessWire page that contains the grid.

```php
// Inside a block template
$mainPage = $pagegrid->getPage($page);
```

**Returns:** Page object (The parent ProcessWire page)

#### isBackend()

Check whether the template is rendering in ProcessWire admin (backend) or on the frontend:

```php
<?php
if($pagegrid->isBackend()) {
    // render admin-only markup
} else {
    // render frontend markup
}
?>
```

**Returns:** boolean

**JavaScript equivalent:**

```javascript
var isBackend = document.querySelectorAll('.pg-is-backend').length;
if(isBackend) {
    // run JS only for the backend
} else {
    // run JS only for the frontend
}
```

#### noAppendFile($page)

Disables automatic inclusion of ProcessWire's `_init.php` and `_main.php` files for a specific template:

```php
<?= $pagegrid->noAppendFile($page) ?>
```

Place this at the very top of your template file. This is useful when you want to define the HTML structure completely within the template file.

**Global alternative:** Uncomment `$config->prependTemplateFile` and `$config->appendTemplateFile` in `site/config.php` to disable globally.

---

## Custom CSS and Layout Customization

### Default Layout

PageGrid uses a **12-column CSS grid** by default. You can customize this layout globally using CSS:

```css
/* Change to 6-column grid */
.pg {
    grid-template-columns: repeat(6, 1fr);
}

/* Use custom column widths */
.pg {
    grid-template-columns: 1fr 2fr 1fr;
}

/* Switch to flexbox */
.pg {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

/* Use block layout for simple vertical stacking */
.pg {
    display: block;
}
```

### Wrapper Styles

The `.pg` class is the main PageGrid wrapper:

```css
/* Customize the main wrapper */
.pg {
    display: grid;
    grid-template-columns: repeat(12, 1fr);
    gap: 20px;
    padding: 20px;
}

/* Use block for vertical-only dragging/sorting */
.pg-main {
    display: block;
}
```

### Item Styles

Control how items flow in the grid:

```css
/* Items take available space (no overlap) */
.pg .pg-item {
    grid-row-start: auto;
    grid-column-start: auto;
}

/* Items auto-flow vertically, fixed columns */
.pg .pg-item {
    grid-row-start: auto;
}

/* Default size for newly created items */
.pg .pg-item-added {
    grid-column-end: span 3;
    grid-row-start: auto;
}
```

**Default behavior:** Items are placed manually and can overlap. Use `grid-row-start: auto;` to make items flow automatically into available space.

### Loading Custom Styles

Load your custom CSS **after** PageGrid styles to override defaults:

```php
<?= $pagegrid->styles($page) ?>
<!-- Load custom CSS after -->
<link rel="stylesheet" href="/site/templates/custom.css">
```

**Auto-loading:** PageGrid automatically loads `.css` files matching block template names:

```
site/templates/blocks/
├── my_block.php
├── my_block.css  ← automatically included
└── my_block.js   ← automatically included
```

### Style Panel

PageGrid includes an optional **Style Panel** for visual CSS editing:

- Edit CSS properties directly on the page
- Add custom CSS classes to elements
- Available for superusers by default
- Can enable for other users via permissions
- Can be disabled in module settings

**Enable styling on elements:**

```php
<span class="myclass" data-class="myclass">
    Can be styled via Style Panel
</span>
```

### Important Notes

1. **No assumptions** - PageGrid works with CSS Grid, Flexbox, or Block layout
2. **12-column default** - Override with CSS as needed
3. **Load after PageGrid** - Custom CSS should come after `$pagegrid->styles()` to override
4. **Item overlap** - Items can overlap by default; use `grid-row-start: auto` to prevent
5. **Per-page customization** - Use CSS media queries or per-page stylesheets for variations

---

## When You're Stuck

1. **Check the log:** `/site/assets/logs/migrations.txt`
2. **Verify resources exist:** Does the page/template/block really exist?
3. **Review similar blocks:** Look at existing blocks for pattern matching
4. **Check metadata structure:** Use `bd()` or `var_dump()` to inspect
5. **Read documentation:** Start with architecture.md
6. **Defensive coding:** Always check before accessing nested arrays

---

**Related Documentation:**
- [architecture.md](architecture.md) - Core concepts
- [blocks.md](blocks.md) - Block creation details
- [metadata.md](metadata.md) - Metadata deep dive
- [migrations.md](migrations.md) - Migration patterns
- [README.md](README.md) - Navigation and learning paths
