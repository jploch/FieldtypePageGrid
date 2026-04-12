# PageGrid Metadata - pg_styles Structure

## Overview

Block layout, positioning, and styling are stored in the `pg_styles` metadata key. This separate metadata storage keeps block content (fields) separate from layout (styling).

Access via:
```php
$styles = $block->meta('pg_styles');
$block->meta('pg_styles', $updatedStyles);
```

---

## pg_styles Root Structure

The `pg_styles` metadata contains entries for the block itself (`pgitem`) and optionally for **inner HTML elements** within the block's markup:

```php
$styles = [
    'pgitem' => [
        // Main item styling and layout (the outer wrapper)
        'id' => 'pgitem',                      // REQUIRED — must always be present
        'tagName' => 'div',                    // HTML tag
        'cssClasses' => 'class-name other',   // CSS classes
        'attributes' => 'data-custom="value"',// Custom HTML attributes
        'breakpoints' => [/* ... */]          // Layout by breakpoint
    ],
    // Optional: style inner elements within the block's markup
    'img' => [
        'id' => 'img',
        'cssClass' => '',
        'tagName' => 'img',
        'breakpoints' => [/* ... */]
    ],
];
```

> **⚠️ The `'id' => 'pgitem'` key is required.** Omitting it causes a PHP warning
> (`Undefined array key "id"`) in the admin backend. Always include it when
> creating or initialising `pg_styles` metadata.

### Inner Element Styling

Blocks can store styles for child HTML elements that live inside the block's template markup. The `pgitem` key always refers to the **block's outer wrapper element**. Additional keys target inner elements identified by their tag name or `data-class` attribute.

#### Standard Inner Elements (by tag name)

Some inner elements are referenced by their HTML tag name:

| Block Template | Inner Key | Tag | Description |
|---------------|-----------|-----|-------------|
| `pg_image` | `img` | `<img>` | The image element inside the wrapper div |
| `pg_editor` | `p` | `<p>` | Paragraph elements inside the rich text editor |

#### Custom Inner Elements (via `data-class`)

Block templates can define styleable inner elements using the `data-class` HTML attribute. These elements get a custom ID as their key in `pg_styles`. Some use the block's page ID to create unique identifiers:

| Block Template | `data-class` | Example Key | Element |
|---------------|-------------|-------------|---------|
| `pg_image` | `caption-{id}` | `caption-1079` | Image caption overlay |
| `pg_image` | `image-link` | `image-link` | Link wrapper around image |
| `pg_navigation` | `hamburger-{id}` | `hamburger-1127` | Hamburger menu icon |
| `pg_navigation` | `pg-nav-menu` | `pg-nav-menu` | Navigation menu container |
| `pg_navigation` | `nav-main` | `nav-main` | Main `<ul>` navigation list |

**Example: Style the `<img>` inside an image block**

```php
$block = wire('pages')->get('name=pg-image-1079');
$styles = $block->meta('pg_styles') ?? [];

// Style the outer wrapper (pgitem)
$styles['pgitem'] = [
    'id' => 'pgitem',
    'tagName' => 'div',
    'cssClass' => '',
    'breakpoints' => [
        'base' => [
            'css' => [
                'grid-column-start' => '1',
                'grid-column-end' => 'span 6',
            ],
            'size' => '@media (min-width: 640px)',
            'name' => 'base',
        ]
    ]
];

// Style the inner <img> element
$styles['img'] = [
    'id' => 'img',
    'cssClass' => '',
    'tagName' => 'img',
    'breakpoints' => [
        'base' => [
            'css' => [
                'border-style' => 'solid solid solid solid',
                'border-width' => '5px',
                'border-color' => 'rgba(255,0,0,1)',
                'border-radius' => '12px',
            ],
            'size' => '@media (min-width: 640px)',
            'name' => 'base',
        ]
    ]
];

$block->meta('pg_styles', $styles);
```

**Example: Style a `data-class` element (hamburger icon)**

```php
$nav = wire('pages')->get('name=pg-navigation-1127');
$styles = $nav->meta('pg_styles') ?? [];

// Style the hamburger icon using data-class key
$styles['hamburger-1127'] = [
    'id' => 'hamburger-1127',
    'cssClass' => 'hamburger-1127',
    'tagName' => 'span',
    'breakpoints' => [
        'base' => [
            'css' => [
                'background-color' => 'rgba(255,255,255,1)',
            ],
            'size' => '@media (min-width: 640px)',
            'name' => 'base',
        ]
    ]
];

$nav->meta('pg_styles', $styles);
```

> **⚠️ `cssClass` and `tagName` are REQUIRED on every inner element key.** Without them, the CSS selector is skipped and properties are emitted without a rule — no styles apply. Use `cssClass => ''` (empty string) and `tagName => 'h2'` (the HTML tag). `id` must also match the key name.

---

## Breakpoints

PageGrid supports 4 responsive breakpoints:

| Breakpoint | Media Query | Device | Use Case |
|------------|-------------|--------|----------|
| **base** | min-width: 640px | Desktop | **Default for all screens** - applies to all devices |
| **l** | min-width: 1600px | Large Desktop | Override base on extra-wide screens |
| **m** | max-width: 960px | Tablet | Override base on medium/tablet devices |
| **s** | max-width: 640px | Mobile | Override base on small/mobile devices |

**Important: Desktop-First Approach (Not Mobile-First)**
- Always define the **base** breakpoint first - this is the default layout that applies to all screens
- Use **s**, **m**, and **l** breakpoints only when you need to override the base layout for specific screen sizes
- CSS applies in cascade: base → then s, m, or l overwrites base values as needed
- Start with your desktop/base design, then add modifications for other screen sizes as needed

**Breakpoint Override Example:**
```php
'breakpoints' => [
    'base' => [
        // This applies to ALL screens by default
        'css' => [
            'grid-column-start' => '1',
            'grid-column-end' => 'span 3',
            'padding' => '20px',
        ]
    ],
    's' => [
        // This ONLY overrides specific properties on mobile
        // Properties not listed here inherit from base
        'css' => [
            'grid-column-start' => '1',
            'grid-column-end' => 'span 1',  // narrower on mobile
            'padding' => '10px',             // less padding on mobile
        ]
    ],
]
```

**Important:** Media query order matters! CSS applies in order:
1. Base (no media query) - applies to all
2. Mobile (s) - overrides base only on small screens
3. Tablet (m) - overrides base only on medium screens
4. Large (l) - overrides base only on large screens

### Example with All Breakpoints

```php
$styles = [
    'pgitem' => [
        'id' => 'pgitem',
        'tagName' => 'div',
        'breakpoints' => [
            'base' => [
                // Desktop layout
                'css' => [
                    'grid-column-start' => '1',
                    'grid-column-end' => 'span 2',
                    'grid-row-start' => '1',
                    'grid-row-end' => 'span 1',
                ]
            ],
            's' => [
                // Mobile override
                'css' => [
                    'grid-column-start' => '1',
                    'grid-column-end' => 'span 1',  // Only 1 column on mobile
                ]
            ],
            'm' => [
                // Tablet override
                'css' => [
                    'grid-column-start' => '1',
                    'grid-column-end' => 'span 1',
                ]
            ],
            'l' => [
                // Large screen - spans more columns
                'css' => [
                    'grid-column-start' => '1',
                    'grid-column-end' => 'span 3',
                ]
            ]
        ]
    ]
];
```

---

## CSS Grid Properties

### Grid Configuration

PageGrid uses a **12-column grid layout** by default. When adding blocks to a container, the system should:

1. **Check the parent container** for `grid-template-columns` in its CSS properties
2. **Use that column count** if defined (e.g., `'grid-template-columns' => 'repeat(6, 1fr)'` means 6 columns)
3. **Default to 12 columns** if no `grid-template-columns` is found

When creating migrations or adding blocks, always check the parent's metadata first:

```php
// Get parent container
$parent = $pages->get(1041);
$parentStyles = $parent->meta('pg_styles') ?? [];

// Check for grid-template-columns in parent's CSS
$parentCss = $parentStyles['pgitem']['breakpoints']['base']['css'] ?? [];
$gridTemplateColumns = $parentCss['grid-template-columns'] ?? '';

// Determine column count
if (preg_match('/repeat\((\d+),/', $gridTemplateColumns, $matches)) {
    $columnCount = (int)$matches[1];
} else {
    $columnCount = 12;  // Default to 12 columns
}
```

This means block positioning should adapt based on the actual container:
- For a **12-column container**: full-width = `'span 12'`, half = `'span 6'`, third = `'span 4'`, quarter = `'span 3'`
- For a **6-column container**: full-width = `'span 6'`, half = `'span 3'`, third = `'span 2'`
- For a **4-column container**: full-width = `'span 4'`, half = `'span 2'`

### Position Properties

These define where the block sits on the grid:

```php
'base' => [
    'css' => [
        'grid-column-start' => '1',    // Column position (1-based)
        'grid-column-end' => 'span 2', // Span 2 columns
        'grid-row-start' => '1',       // Row position (1-based)
        'grid-row-end' => 'span 1',    // Span 1 row
        // All modern CSS properties go here
    ]
]
```

**Examples:**

```php
// Block at column 1, span 1 column, row 1 (single cell)
'css' => [
    'grid-column-start' => '1',
    'grid-column-end' => 'span 1',
    'grid-row-start' => '1',
    'grid-row-end' => 'span 1',
]

// Block at column 1, span 2 columns, row 1 (2 columns wide)
'css' => [
    'grid-column-start' => '1',
    'grid-column-end' => 'span 2',
    'grid-row-start' => '1',
    'grid-row-end' => 'span 1',
]

// Block at column 2, span 2 columns, row 2 (offset to column 2, 2 columns wide)
'css' => [
    'grid-column-start' => '2',
    'grid-column-end' => 'span 2',
    'grid-row-start' => '2',
    'grid-row-end' => 'span 1',
]
```

### CSS Properties

The `css` key accepts all modern CSS properties. Here are common examples:

```php
'base' => [
    'css' => [
        // Grid positioning
        'grid-column-start' => '1',
        'grid-column-end' => 'span 2',
        'grid-row-start' => '1',
        'grid-row-end' => 'span 1',
        // Colors and opacity
        'background-color' => 'rgba(255, 0, 0, 1)',   // RGBA format
        'background-image' => 'url(...)',              // Background image
        'opacity' => '0.8',                            // Transparency (0-1)
        // Spacing
        'padding' => '20px',                           // All sides
        'padding-top' => '10px',                       // Individual sides
        'padding-right' => '15px',
        'margin' => '10px',                            // All sides
        'margin-top' => '5px',                         // Individual sides
        // Borders and shadows
        'border-radius' => '8px',                      // Rounded corners
        'box-shadow' => '0 2px 8px rgba(0,0,0,0.1)',  // Drop shadow
        // Sizing
        'min-height' => '200px',                       // Minimum height
        'min-width' => '100px',                        // Minimum width
        // ... any other modern CSS properties you need
    ]
]
```

**Common Combinations:**

```php
// Colored container with padding
'css' => [
    'background-color' => 'rgba(230, 240, 250, 1)',  // Light blue
    'padding' => '20px',
    'border-radius' => '4px',
]

// White card with shadow
'css' => [
    'background-color' => 'rgba(255, 255, 255, 1)',
    'padding' => '16px',
    'border-radius' => '8px',
    'box-shadow' => '0 2px 8px rgba(0,0,0,0.1)',
]

// Full-width section with background
'css' => [
    'background-color' => 'rgba(50, 50, 50, 1)',
    'padding' => '40px 20px',  // Different vertical/horizontal
    'min-height' => '300px',
]
```

---

## Complete Metadata Example

Here's a realistic block with all properties:

```php
$styles = [
    'pgitem' => [
        'id' => 'pgitem',
        'tagName' => 'div',
        'cssClasses' => 'featured card',
        'attributes' => 'data-id="item-123"',
        'breakpoints' => [
            'base' => [
                // Desktop: 2 columns wide at column 1
                'css' => [
                    'grid-column-start' => '1',
                    'grid-column-end' => 'span 2',
                    'grid-row-start' => '1',
                    'grid-row-end' => 'span 1',
                    'background-color' => 'rgba(255, 255, 255, 1)',
                    'padding' => '20px',
                    'border-radius' => '8px',
                    'box-shadow' => '0 2px 8px rgba(0,0,0,0.1)',
                    'margin' => '10px',
                ]
            ],
            's' => [
                // Mobile: 1 column (full width in 1-column grid)
                'css' => [
                    'grid-column-start' => '1',
                    'grid-column-end' => 'span 1',
                    'padding' => '16px',
                    'margin' => '5px',
                ]
            ],
            'm' => [
                // Tablet: 1 column
                'css' => [
                    'grid-column-start' => '1',
                    'grid-column-end' => 'span 1',
                    'padding' => '16px',
                    'margin' => '8px',
                ]
            ],
            'l' => [
                // Large: 3 columns wide
                'css' => [
                    'grid-column-start' => '1',
                    'grid-column-end' => 'span 3',
                ]
                // Can add other overrides here
            ]
        ]
    ]
];
```

---

## Working with Metadata in Code

### Reading Metadata

```php
$block = wire('pages')->get('name=pg-text-1041');

// Get all metadata
$styles = $block->meta('pg_styles');

// Access specific values
$tagName = $styles['pgitem']['tagName'] ?? 'div';
$baseLayout = $styles['pgitem']['breakpoints']['base'] ?? [];
$bgColor = $baseLayout['css']['background-color'] ?? 'transparent';

// Check if breakpoint exists
if (isset($styles['pgitem']['breakpoints']['base'])) {
    $css = $styles['pgitem']['breakpoints']['base']['css'] ?? [];
    $columnStart = $css['grid-column-start'] ?? '1';
    $padding = $css['padding'] ?? '0';
}
```

### Modifying Metadata

```php
$block = wire('pages')->get('name=pg-text-1041');

// Get current metadata
$styles = $block->meta('pg_styles') ?? [];

// Ensure structure exists
// Ensure structure exists
if (!isset($styles['pgitem'])) {
    $styles['pgitem'] = [];
}
if (!isset($styles['pgitem']['breakpoints'])) {
    $styles['pgitem']['breakpoints'] = [];
}
if (!isset($styles['pgitem']['breakpoints']['base'])) {
    $styles['pgitem']['breakpoints']['base'] = [];
}
if (!isset($styles['pgitem']['breakpoints']['base']['css'])) {
    $styles['pgitem']['breakpoints']['base']['css'] = [];
}

// Modify a value
$styles['pgitem']['breakpoints']['base']['css']['background-color'] = 'rgba(0, 255, 0, 1)';
$styles['pgitem']['breakpoints']['base']['css']['padding'] = '16px';

// Save it back
$block->meta('pg_styles', $styles);
```

### Adding a New Breakpoint

```php
// Get current metadata
$styles = $block->meta('pg_styles');

// Copy base breakpoint and modify for mobile
$styles['pgitem']['breakpoints']['s'] = [
    'css' => [
        'grid-column-start' => '1',
        'grid-column-end' => 'span 1',
        // CSS properties here (supports all modern CSS)
        'padding' => '10px',
    ]
];

// Save
$block->meta('pg_styles', $styles);
```

### Removing a Property

```php
// Get current metadata
$styles = $block->meta('pg_styles');

// Remove a CSS property
unset($styles['pgitem']['breakpoints']['base']['css']['margin']);

// Save
$block->meta('pg_styles', $styles);
```

---

## Color Format

Colors must be in RGBA format:

```
rgba(red, green, blue, alpha)
```

Where:
- **red, green, blue:** 0-255
- **alpha:** 0-1 (0=transparent, 1=opaque)

**Examples:**

```php
'rgba(255, 0, 0, 1)'       // Red
'rgba(0, 255, 0, 1)'       // Green
'rgba(0, 0, 255, 1)'       // Blue
'rgba(255, 255, 255, 1)'   // White
'rgba(0, 0, 0, 1)'         // Black
'rgba(128, 128, 128, 1)'   // Gray
'rgba(255, 0, 0, 0.5)'     // Red at 50% opacity
```

To convert hex to RGBA:
```php
// #FF0000 = rgb(255, 0, 0)
'rgba(255, 0, 0, 1)'

// #00FF00 = rgb(0, 255, 0)
'rgba(0, 255, 0, 1)'
```

---

## Responsive Design Patterns

### Desktop-First Approach (Recommended)

Start with the **base** breakpoint for desktop, then override for smaller screens as needed:

```php
'breakpoints' => [
    'base' => [
        // Desktop: featured 3-column layout
        'css' => [
            'grid-column-start' => '1',
            'grid-column-end' => 'span 3',
            'padding' => '20px',
        ]
    ],
    'm' => [
        // Tablet: override to 2 columns
        'css' => [
            'grid-column-start' => '1',
            'grid-column-end' => 'span 2',
            'padding' => '15px',
        ]
    ],
    's' => [
        // Mobile: override to 1 column
        'css' => [
            'grid-column-start' => '1',
            'grid-column-end' => 'span 1',
            'padding' => '10px',
        ]
    ],
]
```

**Best Practice:** Always define the **base** breakpoint. Add other breakpoints only when you need to override specific properties for smaller screens. Properties not defined in override breakpoints inherit from base.

---

## Important Notes

1. **Metadata is serialized JSON** - Store as arrays, not JSON strings
2. **Color format matters** - Use RGBA, not hex or color names
3. **12-column grid default** - PageGrid checks the container's `grid-template-columns` for the grid structure; if not defined, it defaults to 12 columns
4. **Grid coordinates** - Use 1-based for start, 'span N' for end (e.g., 'span 2' spans 2 columns)
5. **Breakpoints apply in order** - Larger breakpoints override smaller ones
6. **Media queries are automatic** - Just define the values, CSS is generated
7. **Check structure before accessing** - Use empty checks and null coalescing
8. **Save after changes** - metadata won't persist without explicit save
9. **'auto' for responsive layout** - Let the grid handle row assignment on mobile

---

## Troubleshooting

**Colors not showing:**
- Check RGBA format: `rgba(255, 0, 0, 1)`
- Verify alpha is between 0 and 1

**Grid not positioning correctly:**
- Verify grid-column-start is a valid integer (e.g., '1', '2')
- Verify grid-column-end uses 'span' format (e.g., 'span 2')
- Ensure breakpoints are defined for your device size

**Metadata not saving:**
- Call `$block->meta('pg_styles', $array)` explicitly
- Check for syntax errors in the array
- Verify the block exists before saving

**Responsive not working:**
- Check that mobile breakpoint (s) is defined
- Verify media queries match device width
- Remember that 's' is for max-width: 640px

---

## Global Classes and Metadata

Global classes are reusable style definitions that multiple blocks can share. They use the same `pg_styles` metadata structure but live as pages under `/admin/setup/pagegrid/pg-classes/` with the `pg_container` template.

### How Global Classes Work

A global class page stores its styles in `pg_styles` just like any block:

```php
// Query an existing global class
$class = wire('pages')->get('name=container, template=pg_container');
$styles = $class->meta('pg_styles');
// Returns:
// [
//     'pgitem' => [
//         'id' => 'pgitem',
//         'cssClass' => 'container',
//         'cssClasses' => 'container ',
//         'tagName' => 'div',
//         'breakpoints' => [
//             'base' => [
//                 'css' => [
//                     'padding-left' => '30px',
//                     'padding-right' => '30px',
//                     ...
//                 ],
//                 'size' => '@media (min-width: 640px)',
//                 'name' => 'base',
//             ]
//         ]
//     ]
// ]
```

### Applying a Global Class to a Block

Blocks reference global classes through the `cssClass`/`cssClasses` keys in their metadata:

```php
$block = wire('pages')->get('name=pg-group-1072');
$styles = $block->meta('pg_styles') ?? [];
$styles['pgitem']['cssClass'] = 'container';
$styles['pgitem']['cssClasses'] = 'container ';
$block->meta('pg_styles', $styles);
```

> **Note:** `pg_container` pages under `pg-classes/` are global classes. Those under `pg-items/` are field containers — don't confuse them.

See [reference.md](reference.md) for full create/query examples.

---

## Page Wrapper and Body Styles (Field Container)

Page-level wrapper and body styles are stored on the **field container page** (the `pg-{fieldId}` page, e.g. `pg-98`), not on the main content page.

| Key | Targets | Scope |
|-----|---------|-------|
| `pgitem` | Page wrapper div (`.pg-main`) | This page only |
| `body` | `<body>` tag | This page only |

```php
// Get the field container for a page
$mainPage = $pages->get(1049);
$pgField = null;
foreach ($mainPage->template->fieldgroup as $f) {
    if ($f->type instanceof FieldtypePageGrid) { $pgField = $f; break; }
}
$itemsParent = $pages->get("name=pg-{$mainPage->id}");
$fieldContainer = $itemsParent->get("name=pg-{$pgField->id}, template=pg_container");

$meta = $fieldContainer->meta('pg_styles') ?? [];

// Page wrapper (background color, padding, etc.)
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

// Body styles for this page only
$meta['body'] = [
    'id' => 'body',
    'cssClass' => 'body',
    'tagName' => 'body',
    'breakpoints' => [
        'base' => [
            'css' => ['font-family' => 'Inter, sans-serif'],
            'size' => '@media (min-width: 640px)',
            'name' => 'base',
        ]
    ]
];
$fieldContainer->meta('pg_styles', $meta);
```

**For global body styles across all pages**, use the `body` global class instead:

```php
$globalBody = $pages->get('name=body, template=pg_container');
// Its pgitem key holds styles applied to <body> on every page
```

> ❌ **NEVER** set page wrapper/body styles on the main page itself — they must go on the field container.

---

**Related Documentation:**
- [blocks.md](blocks.md) - Managing blocks
- [migrations.md](migrations.md) - Creating migrations
- [reference.md](reference.md) - Quick reference (includes Global Classes section)
- [architecture.md](architecture.md) - Understanding structure
