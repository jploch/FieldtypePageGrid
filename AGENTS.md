# PageGrid Agent Guide

Welcome! This guide helps AI agents (like Claude) understand how to work with PageGrid in ProcessWire.

## What is PageGrid?

PageGrid is a **content builder field** for ProcessWire that allows creating complex, responsive layouts using a CSS Grid-based system. Everything is stored as ProcessWire pages with metadata, making it incredibly flexible.

**Key facts:**
- Blocks are ProcessWire pages (you use the standard ProcessWire API)
- Layout/styling is stored in metadata under `pg_styles`
- Uses a **12-column CSS grid by default** (can be customized)
- Fully responsive with 4 breakpoints: base, s (mobile), m (tablet), l (large)
- Built with migrations system for reproducible changes

## Getting Started

### Learning Path

Read the documentation in this order:

1. **[architecture.md](ai-docs/architecture.md)** (5 min)
   - Understand the core concepts and structure
   - Learn how blocks organize hierarchy
   - See the metadata structure at a glance

2. **[metadata.md](ai-docs/metadata.md)** (10 min)
   - Deep dive into the `pg_styles` metadata structure
   - **CRITICAL:** Understand that CSS properties ALWAYS go under `['css']` key
   - Learn about the 12-column grid detection logic
   - See breakpoint examples

3. **[blocks.md](ai-docs/blocks.md)** (10 min)
   - Learn how blocks are created (4-step process)
   - Understand block templates and customization
   - See wrapper element attributes

4. **[migrations.md](ai-docs/migrations.md)** (5 min)
   - Learn the migration patterns for common tasks
   - Copy patterns when creating new migrations

5. **[reference.md](ai-docs/reference.md)** (ongoing reference)
   - Use for quick lookups and function references
   - API documentation and code snippets

## What You Can Do

### Creating Content
- Create blocks and arrange them on the grid
- Add text, images, groups (containers for nested blocks)
- Style blocks with colors, padding, margins, shadows, etc.
- Make responsive layouts with different styles per breakpoint
- Create and apply **global classes** for reusable styles (see [reference.md](ai-docs/reference.md#global-classes))

### Modifying Existing Content
- Change block styling (colors, padding, positioning)
- Move blocks to different grid positions
- Modify text content
- Add/remove responsive breakpoint overrides

### Common Workflows

**Create a text block with styling:**
```php
// Create block
$block = new Page();
$block->template = 'pg_text';
$block->parent = $fieldContainer;
$block->name = 'pg_text' . time();
$block->save();

// Add content
$block->of(false);
$block->pg_text = 'Hello World';
$block->save();

// Finalize name
$block->setAndSave('name', 'pg-text-' . $block->id);

// Style it
$styles = [
    'pgitem' => [
        'id' => 'pgitem',
        'breakpoints' => [
            'base' => [
                'css' => [
                    'background-color' => 'rgba(255, 0, 0, 1)',
                    'padding' => '20px',
                    'grid-column-start' => '1',
                    'grid-column-end' => 'span 4',
                ]
            ]
        ]
    ]
];
$block->meta('pg_styles', $styles);
```

**Change block background color:**
```php
$block = wire('pages')->get('name=pg-text-1041');
$styles = $block->meta('pg_styles') ?? [];

if (!isset($styles['pgitem']['breakpoints']['base']['css'])) {
    $styles['pgitem']['breakpoints']['base']['css'] = [];
}

$styles['pgitem']['breakpoints']['base']['css']['background-color'] = 'rgba(0, 255, 0, 1)';
$block->meta('pg_styles', $styles);
```

**Create a group with children:**
```php
$group = new Page();
$group->template = 'pg_group';
$group->parent = $fieldContainer;
$group->name = 'pg_group' . time();
$group->save();

// Style the group
$styles = [
    'pgitem' => [
        'id' => 'pgitem',
        'breakpoints' => [
            'base' => [
                'css' => [
                    'background-color' => 'rgba(0, 0, 0, 1)',
                    'padding' => '30px',
                    'gap' => '30px',
                    'grid-column-start' => '1',
                    'grid-column-end' => 'span 12',
                ]
            ]
        ]
    ]
];
$group->meta('pg_styles', $styles);

// Finalize name
$group->setAndSave('name', 'pg-group-' . $group->id);

// Create children inside the group...
```

## Critical Rules

### pgitem ID
- ✅ `'pgitem' => ['id' => 'pgitem', 'breakpoints' => [...]]` - CORRECT
- ❌ `'pgitem' => ['breakpoints' => [...]]` - WRONG (missing `id`)

The `'id' => 'pgitem'` key is **required** inside every `pgitem` metadata entry. Omitting it causes a PHP warning in the admin backend.

### Metadata Structure
- ✅ `'css' => ['background-color' => 'rgba(...)']` - CORRECT
- ❌ `'background-color' => 'rgba(...)'` - WRONG

CSS properties can NEVER be direct children of the breakpoint. They ALWAYS go under the `['css']` key.

### Inner Element Styling
Blocks can style child HTML elements within their markup. `pgitem` is always the **block wrapper element**. Additional keys target inner elements by tag name (e.g., `img`, `p`) or by `data-class` attribute (e.g., `caption-{id}`, `hamburger-{id}`).

> **⚠️ `cssClass` and `tagName` are REQUIRED on every inner element key.** Without them, the CSS selector is skipped and no styles are applied on the frontend.

```php
// ✅ CORRECT — style the <img> inside an image block
$styles['img'] = [
    'id' => 'img',
    'cssClass' => '',
    'tagName' => 'img',
    'breakpoints' => [
        'base' => [
            'css' => ['border-radius' => '12px'],
            'size' => '@media (min-width: 640px)',
            'name' => 'base',
        ]
    ]
];

// ✅ CORRECT — style a data-class element (e.g., hamburger icon)
$styles['hamburger-1127'] = [
    'id' => 'hamburger-1127',
    'cssClass' => 'hamburger-1127',
    'tagName' => 'span',
    'breakpoints' => [
        'base' => [
            'css' => ['background-color' => 'rgba(255,255,255,1)'],
            'size' => '@media (min-width: 640px)',
            'name' => 'base',
        ]
    ]
];
```

### ⚠️ NEVER Use Inline Styles in Content Fields
Always store styles in `pg_styles` metadata — never as inline `style="..."` attributes in HTML content fields (`pg_editor`, `pg_text`). Use inner element keys (`h2`, `p`, etc.) to target elements inside blocks.

```php
// ❌ WRONG — inline styles in content
$block->pg_editor = '<h2 style="font-size: 26px;">Title</h2>';

// ✅ CORRECT — clean HTML + styles in metadata
$block->pg_editor = '<h2>Title</h2>';
$block->meta('pg_styles', [
    'pgitem' => [ ... ],
    'h2' => [
        'id' => 'h2',
        'breakpoints' => [
            'base' => [
                'css' => ['font-size' => '26px', 'font-weight' => '700'],
                'size' => '@media (min-width: 640px)',
                'name' => 'base',
            ]
        ]
    ],
]);
```

### Page Wrapper and Body Styles

Page-level styles (background, padding) and body styles are stored on the **field container page** (e.g., `pg-98`), NOT on the main page itself.

- `pgitem` key → styles for the **page wrapper** (the `.pg-main` div wrapping all blocks)
- `body` key → styles for the **`<body>` tag** of that specific page only

```php
// Get the field container for a page
$mainPage = $pages->get(1049);
$pgField = null;
foreach ($mainPage->template->fieldgroup as $f) {
    if ($f->type instanceof FieldtypePageGrid) { $pgField = $f; break; }
}
$itemsParent = $pages->get("name=pg-{$mainPage->id}");
$fieldContainer = $itemsParent->get("name=pg-{$pgField->id}, template=pg_container");

// Set page wrapper background (pgitem)
$meta = $fieldContainer->meta('pg_styles') ?? [];
$meta['pgitem'] = [
    'id' => 'pgitem',
    'tagName' => 'div',
    'cssClass' => '',
    'breakpoints' => [
        'base' => [
            'css' => ['background-color' => 'rgba(185,190,228,1)', 'padding' => '60px'],
            'size' => '@media (min-width: 640px)',
            'name' => 'base',
        ]
    ]
];

// Set page-specific body styles (body key)
$meta['body'] = [
    'id' => 'body',
    'cssClass' => 'body',
    'tagName' => 'body',
    'breakpoints' => [
        'base' => [
            'css' => ['font-family' => 'Inter, sans-serif', 'color' => 'rgba(0,0,0,1)'],
            'size' => '@media (min-width: 640px)',
            'name' => 'base',
        ]
    ]
];
$fieldContainer->meta('pg_styles', $meta);
```

**For global body styles (all pages):** Use the global `body` class instead:

```php
// Global body class — applies to ALL pages
$globalBody = $pages->get('name=body, template=pg_container');
// Styles are in pgitem key (not body key) for global classes
$globalBody->meta('pg_styles')['pgitem']['breakpoints']['base']['css']['font-family'];
```

> ❌ **NEVER** set page wrapper/body styles on the main page itself — they belong on the field container.

### Grid Positioning
- Start positions: integers (1, 2, 3, etc.)
- End positions: 'span N' format ('span 2', 'span 3', etc.)

```php
// ✅ CORRECT
'css' => [
    'grid-column-start' => '1',
    'grid-column-end' => 'span 2',
]

// ❌ WRONG
'grid-column-start' => '1',
'grid-column-end' => '3',  // Should be 'span 2'
```

### Color Format
- Always RGBA: `'rgba(red, green, blue, alpha)'`
- Alpha: 0-1 (0 = transparent, 1 = opaque)

```php
// ✅ CORRECT
'rgba(255, 0, 0, 1)'      // Red
'rgba(0, 0, 0, 0)'        // Transparent
'rgba(255, 255, 255, 0.5)' // White at 50% opacity

// ❌ WRONG
'#FF0000'     // Hex not supported
'red'         // Color names not supported
```

### Responsive Design
- Define `'base'` breakpoint first (applies to all screens)
- Only define s/m/l breakpoints if you need to override base
- Properties not defined in override breakpoints inherit from base

```php
'breakpoints' => [
    'base' => [
        // Desktop - applies to all screens
        'css' => ['padding' => '20px']
    ],
    's' => [
        // Mobile - only overrides what's different
        'css' => ['padding' => '10px']
    ]
]
```

### Two-Step Naming
Always create blocks with temporary name, then finalize with ID:

```php
// Step 1: Temporary name
$block->name = 'pg_text' . time();  // pg_text1712757600
$block->save();

// Step 2: Final name with ID
$block->setAndSave('name', 'pg-text-' . $block->id);  // pg-text-1041
```

## Grid Configuration

### Default: 12-column grid
When adding blocks to a container, check the parent's `grid-template-columns`:

```php
$parentStyles = $parent->meta('pg_styles') ?? [];
$parentCss = $parentStyles['pgitem']['breakpoints']['base']['css'] ?? [];
$gridTemplateColumns = $parentCss['grid-template-columns'] ?? '';

// Detect column count
if (preg_match('/repeat\((\d+),/', $gridTemplateColumns, $matches)) {
    $columnCount = (int)$matches[1];
} else {
    $columnCount = 12;  // Default to 12
}
```

Then size blocks proportionally:
```php
$spanWidth = floor($columnCount / $childCount);
// Position children equally across the grid
```

## Migration System

Create migrations for reproducible changes:

```php
<?php
namespace ProcessWire;

$log = wire('log');
$pages = wire('pages');

try {
    $log->save('migrations', 'Migration: Add styled group');
    
    // Your code here
    
    $log->save('migrations', 'Migration completed successfully');
} catch (\Exception $e) {
    $log->save('migrations', 'ERROR: ' . $e->getMessage());
    throw $e;
}
```

Migrations go in: `/site/assets/at/migrations/`

File format: `YYYYMMDDhhmmss_description.php`

## Common Patterns

### "Position blocks next to each other"
1. Get parent's column count from `grid-template-columns`
2. Calculate span width: `floor(columnCount / blockCount)`
3. Set each block's `grid-column-start` and `grid-column-end: 'span ' . $spanWidth`

### "Make blocks same row"
1. Set all blocks to `'grid-row-start' => '1'`
2. Set all blocks to `'grid-row-end' => 'span 1'`

### "Add responsive override"
1. Get existing metadata
2. Ensure `$styles['pgitem']['breakpoints']['s']` exists
3. Set only the properties that differ from base
4. Save metadata

### "Detect if block has styling"
```php
$styles = $block->meta('pg_styles');
$hasCss = isset($styles['pgitem']['breakpoints']['base']['css']);
```

## Useful ProcessWire API Functions

```php
// Get blocks
wire('pages')->get('name=pg-text-1041');
wire('pages')->get(1041);  // By ID
wire('pages')->find('template=pg_text');  // By template

// Get field container (dynamic lookup)
$pgField = null;
foreach ($mainPage->template->fieldgroup as $f) {
    if ($f->type instanceof FieldtypePageGrid) { $pgField = $f; break; }
}
$fieldContainer = wire('pages')->get("name=pg-{$pgField->id}, parent.name=pg-{$mainPage->id}");

// Get all children
$children = $fieldContainer->children();

// Get specific child
$child = $fieldContainer->get('name=pg-text-1041');

// Get metadata
$styles = $block->meta('pg_styles');

// Save metadata
$block->meta('pg_styles', $updatedStyles);

// Turn off output formatting (for field edits)
$block->of(false);
$block->save();
```

## What to Ask Me

Ask me to:
- ✅ Create blocks with content
- ✅ Style blocks (colors, padding, positioning)
- ✅ Position blocks on the grid
- ✅ Create responsive layouts
- ✅ Create groups with nested blocks
- ✅ Modify existing block styling
- ✅ Create migrations for changes
- ✅ Explain metadata structure

## Debugging Tips

**Block not appearing:**
- Did you finalize the name with ID? (`pg-text-1041` not `pg_text1234567`)
- Is the parent correct? (Should be the field container, not the main page)
- Does the template exist?

**Styling not applying:**
- Check log file: `/site/assets/logs/migrations.txt`
- Verify metadata syntax: `var_dump($block->meta('pg_styles'))`
- Is CSS under the `['css']` key?

**Grid position wrong:**
- Verify `grid-column-start` is a number (1, 2, 3)
- Verify `grid-column-end` is in 'span' format ('span 2', 'span 3')
- Check parent column count

**Responsive not working:**
- Is `'s'` breakpoint defined?
- Check if media query width matches device width

## Documentation Files

- **[README.md](ai-docs/README.md)** - Navigation and index
- **[architecture.md](ai-docs/architecture.md)** - Core concepts
- **[blocks.md](ai-docs/blocks.md)** - Block creation and templates
- **[metadata.md](ai-docs/metadata.md)** - Metadata structure deep dive
- **[migrations.md](ai-docs/migrations.md)** - Migration patterns
- **[reference.md](ai-docs/reference.md)** - API reference and quick lookup

---

**Ready to start?** Read ai-docs/architecture.md next!

