# PageGrid Migrations

## Overview

Migrations are PHP files that create repeatable, version-controlled changes to your PageGrid blocks. They live in `/site/assets/at/migrations/` and follow a naming convention.

Benefits:
- Version controlled alongside code
- Repeatable across environments
- Consistent block creation
- Easy to review and rollback
- Can include logging for debugging

---

## Migration File Structure

### Location
```
/site/assets/at/migrations/
```

### Naming Convention
```
{YYYYMMDDHHMMSS}_{description}.php
```

**Examples:**
```
20260410120000_create-hello-world-template.php
20260410120006_add-text-block-to-group-page-1049.php
20260410120007_update-item-1058-yellow-background.php
```

**Rules:**
- Timestamp must be unique
- Description in kebab-case (lowercase, hyphens)
- Must be PHP file
- Processed in alphabetical order (timestamps ensure order)

### Basic Structure

```php
<?php
namespace ProcessWire;

// Get wire instances
$log = wire('log');
$pages = wire('pages');
$templates = wire('templates');

// Wrap in try-catch for error handling
try {
    $log->save('migrations', 'Starting migration: create-text-block');
    
    // Your block creation code here
    
    $log->save('migrations', 'Migration completed successfully');
    
} catch (\Exception $e) {
    $log->save('migrations', 'ERROR: ' . $e->getMessage());
    throw $e;
}
```

---

## Common Migration Patterns

### Pattern 1: Simple Block Creation

Creates a basic text block on a page.

```php
<?php
namespace ProcessWire;

// Get wire instances
$log = wire('log');
$pages = wire('pages');
$templates = wire('templates');

try {
    $log->save('migrations', 'Creating text block');
    
    // Get main page
    $mainPage = $pages->get(1049);
    if (!$mainPage->id) {
        throw new \Exception('Page 1049 not found');
    }
    
    // Find the first FieldtypePageGrid field on the page
    $pgField = null;
    foreach ($mainPage->template->fieldgroup as $f) {
        if ($f->type instanceof FieldtypePageGrid) {
            $pgField = $f;
            break;
        }
    }
    
    // Get or create items parent (pg-{pageId})
    $itemsParent = $pages->get("name=pg-{$mainPage->id}");
    if (!$itemsParent->id) {
        $itemsParent = new Page();
        $itemsParent->template = 'pg_container';
        $itemsParent->parent = $pages->get('name=pg-items');
        $itemsParent->name = "pg-{$mainPage->id}";
        $itemsParent->title = $mainPage->title . ' items';
        $itemsParent->save();
    }
    
    // Get or create field container (pg-{fieldId})
    $fieldContainer = $itemsParent->get("name=pg-{$pgField->id}, template=pg_container");
    if (!$fieldContainer->id) {
        $fieldContainer = new Page();
        $fieldContainer->template = 'pg_container';
        $fieldContainer->parent = $itemsParent;
        $fieldContainer->name = "pg-{$pgField->id}";
        $fieldContainer->title = "pg-{$pgField->id}";
        $fieldContainer->save();
    }
    
    // Create block with temporary name
    $block = new Page();
    $block->template = $templates->get('pg_text');
    $block->parent = $fieldContainer;
    $block->name = 'pg_text' . time();
    $block->title = $block->name;
    $block->save();
    
    // Set content
    $block->of(false);
    $block->pg_text = 'Hello world';
    $block->save();
    
    // Finalize name with ID
    $block->setAndSave('name', 'pg-text-' . $block->id);
    $block->setAndSave('title', 'pg-text-' . $block->id);
    
    $log->save('migrations', 'Block created: ' . $block->name . ' (ID: ' . $block->id . ')');
    
} catch (\Exception $e) {
    $log->save('migrations', 'ERROR: ' . $e->getMessage());
    throw $e;
}
```

---

### Pattern 2: Block with CSS Grid Positioning

Creates a block positioned at a specific grid location.

```php
<?php
namespace ProcessWire;

$log = wire('log');
$pages = wire('pages');
$templates = wire('templates');

try {
    $log->save('migrations', 'Creating positioned text block at row 2, column 3');
    
    // Get main page (1049 is a group)
    $mainPage = $pages->get(1049);
    if (!$mainPage->id) {
        throw new \Exception('Page 1049 not found');
    }
    
    // Find the first FieldtypePageGrid field on the page
    $pgField = null;
    foreach ($mainPage->template->fieldgroup as $f) {
        if ($f->type instanceof FieldtypePageGrid) {
            $pgField = $f;
            break;
        }
    }
    
    // Get or create items parent
    $itemsParent = $pages->get("name=pg-{$mainPage->id}");
    if (!$itemsParent->id) {
        $itemsParent = new Page();
        $itemsParent->template = 'pg_container';
        $itemsParent->parent = $pages->get('name=pg-items');
        $itemsParent->name = "pg-{$mainPage->id}";
        $itemsParent->title = $mainPage->title . ' items';
        $itemsParent->save();
    }
    
    // Get or create field container
    $fieldContainer = $itemsParent->get("name=pg-{$pgField->id}, template=pg_container");
    if (!$fieldContainer->id) {
        $fieldContainer = new Page();
        $fieldContainer->template = 'pg_container';
        $fieldContainer->parent = $itemsParent;
        $fieldContainer->name = "pg-{$pgField->id}";
        $fieldContainer->title = "pg-{$pgField->id}";
        $fieldContainer->save();
    }
    
    // Create block
    $block = new Page();
    $block->template = $templates->get('pg_text');
    $block->parent = $fieldContainer;
    $block->name = 'pg_text' . time();
    $block->title = $block->name;
    $block->save();
    
    // Set content
    $block->of(false);
    $block->pg_text = 'Positioned at row 2, column 3';
    $block->save();
    
    // Set positioning metadata (CSS Grid)
    $styles = [
        'pgitem' => [
            'id' => 'pgitem',
            'tagName' => 'div',
            'breakpoints' => [
                'base' => [
                    'grid-column-start' => '3',    // Column 3
                    'grid-column-end' => 'span 1', // Width of 1 column
                    'grid-row-start' => '2',       // Row 2
                    'grid-row-end' => 'span 1',    // Height of 1 row
                ]
            ]
        ]
    ];
    $block->meta('pg_styles', $styles);
    
    // Finalize name
    $block->setAndSave('name', 'pg-text-' . $block->id);
    $block->setAndSave('title', 'pg-text-' . $block->id);
    
    $log->save('migrations', 'Block created at grid position: ' . $block->name);
    
} catch (\Exception $e) {
    $log->save('migrations', 'ERROR: ' . $e->getMessage());
    throw $e;
}
```

---

### Pattern 3: Block with Styling

Creates a block with background color and other styling.

```php
<?php
namespace ProcessWire;

$log = wire('log');
$pages = wire('pages');
$templates = wire('templates');

try {
    $log->save('migrations', 'Creating styled text block with green background');
    
    $mainPage = $pages->get(1049);
    if (!$mainPage->id) {
        throw new \Exception('Page 1049 not found');
    }
    
    // Find the first FieldtypePageGrid field on the page
    $pgField = null;
    foreach ($mainPage->template->fieldgroup as $f) {
        if ($f->type instanceof FieldtypePageGrid) {
            $pgField = $f;
            break;
        }
    }
    
    // Get or create items parent
    $itemsParent = $pages->get("name=pg-{$mainPage->id}");
    if (!$itemsParent->id) {
        $itemsParent = new Page();
        $itemsParent->template = 'pg_container';
        $itemsParent->parent = $pages->get('name=pg-items');
        $itemsParent->name = "pg-{$mainPage->id}";
        $itemsParent->title = $mainPage->title . ' items';
        $itemsParent->save();
    }
    
    // Get or create field container
    $fieldContainer = $itemsParent->get("name=pg-{$pgField->id}, template=pg_container");
    if (!$fieldContainer->id) {
        $fieldContainer = new Page();
        $fieldContainer->template = 'pg_container';
        $fieldContainer->parent = $itemsParent;
        $fieldContainer->name = "pg-{$pgField->id}";
        $fieldContainer->title = "pg-{$pgField->id}";
        $fieldContainer->save();
    }
    
    // Create block
    $block = new Page();
    $block->template = $templates->get('pg_text');
    $block->parent = $fieldContainer;
    $block->name = 'pg_text' . time();
    $block->title = $block->name;
    $block->save();
    
    // Set content
    $block->of(false);
    $block->pg_text = 'Block with green background';
    $block->save();
    
    // Set styling metadata
    $styles = [
        'pgitem' => [
            'id' => 'pgitem',
            'tagName' => 'div',
            'breakpoints' => [
                'base' => [
                    'grid-column-start' => '1',
                    'grid-column-end' => 'span 1',
                    'grid-row-start' => '1',
                    'grid-row-end' => 'span 1',
                    // Styling
                    'css' => [
                        'background-color' => 'rgba(0, 255, 0, 1)',  // Green
                        'padding' => '20px',
                        'border-radius' => '8px',
                    ]
                ]
            ]
        ]
    ];
    $block->meta('pg_styles', $styles);
    
    // Finalize name
    $block->setAndSave('name', 'pg-text-' . $block->id);
    $block->setAndSave('title', 'pg-text-' . $block->id);
    
    $log->save('migrations', 'Styled block created: ' . $block->name);
    
} catch (\Exception $e) {
    $log->save('migrations', 'ERROR: ' . $e->getMessage());
    throw $e;
}
```

---

### Pattern 4: Modify Existing Block Metadata

Updates styling on an existing block without recreating it.

```php
<?php
namespace ProcessWire;

$log = wire('log');
$pages = wire('pages');

try {
    $log->save('migrations', 'Updating block 1058 background to yellow');
    
    // Get the existing block
    $block = $pages->get(1058);
    if (!$block->id) {
        throw new \Exception('Block 1058 not found');
    }
    
    // Get current metadata
    $styles = $block->meta('pg_styles');
    
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
    
    // Update background color to yellow
    $styles['pgitem']['breakpoints']['base']['css']['background-color'] = 'rgba(255, 255, 0, 1)';
    
    // Save updated metadata
    $block->meta('pg_styles', $styles);
    
    $log->save('migrations', 'Block ' . $block->name . ' updated with yellow background');
    
} catch (\Exception $e) {
    $log->save('migrations', 'ERROR: ' . $e->getMessage());
    throw $e;
}
```

---

### Pattern 5: Responsive Block with Multiple Breakpoints

Creates a block with different layouts for different screen sizes.

```php
<?php
namespace ProcessWire;

$log = wire('log');
$pages = wire('pages');
$templates = wire('templates');

try {
    $log->save('migrations', 'Creating responsive block for all breakpoints');
    
    $mainPage = $pages->get(1049);
    if (!$mainPage->id) {
        throw new \Exception('Page 1049 not found');
    }
    
    // Find the first FieldtypePageGrid field on the page
    $pgField = null;
    foreach ($mainPage->template->fieldgroup as $f) {
        if ($f->type instanceof FieldtypePageGrid) {
            $pgField = $f;
            break;
        }
    }
    
    // Get or create items parent
    $itemsParent = $pages->get("name=pg-{$mainPage->id}");
    if (!$itemsParent->id) {
        $itemsParent = new Page();
        $itemsParent->template = 'pg_container';
        $itemsParent->parent = $pages->get('name=pg-items');
        $itemsParent->name = "pg-{$mainPage->id}";
        $itemsParent->title = $mainPage->title . ' items';
        $itemsParent->save();
    }
    
    // Get or create field container
    $fieldContainer = $itemsParent->get("name=pg-{$pgField->id}, template=pg_container");
    if (!$fieldContainer->id) {
        $fieldContainer = new Page();
        $fieldContainer->template = 'pg_container';
        $fieldContainer->parent = $itemsParent;
        $fieldContainer->name = "pg-{$pgField->id}";
        $fieldContainer->title = "pg-{$pgField->id}";
        $fieldContainer->save();
    }
    
    // Create block
    $block = new Page();
    $block->template = $templates->get('pg_text');
    $block->parent = $fieldContainer;
    $block->name = 'pg_text' . time();
    $block->title = $block->name;
    $block->save();
    
    // Set content
    $block->of(false);
    $block->pg_text = 'Responsive block';
    $block->save();
    
    // Set responsive metadata with all breakpoints
    $styles = [
        'pgitem' => [
            'id' => 'pgitem',
            'tagName' => 'div',
            'breakpoints' => [
                'base' => [
                    // Desktop: 3 columns wide
                    'grid-column-start' => '1',
                    'grid-column-end' => 'span 3',
                    'grid-row-start' => '1',
                    'grid-row-end' => 'span 1',
                    'css' => [
                        'background-color' => 'rgba(220, 240, 250, 1)',
                        'padding' => '20px',
                    ]
                ],
                'l' => [
                    // Large screen: 4 columns wide
                    'grid-column-start' => '1',
                    'grid-column-end' => 'span 4',
                    'css' => [
                        'background-color' => 'rgba(200, 230, 255, 1)',
                        'padding' => '24px',
                    ]
                ],
                'm' => [
                    // Tablet: 2 columns wide
                    'grid-column-start' => '1',
                    'grid-column-end' => 'span 2',
                    'grid-row-start' => '1',
                    'grid-row-end' => 'span 1',
                    'css' => [
                        'padding' => '16px',
                    ]
                ],
                's' => [
                    // Mobile: 1 column
                    'grid-column-start' => '1',
                    'grid-column-end' => 'span 1',
                    'grid-row-start' => 'auto',
                    'grid-row-end' => 'auto',
                    'css' => [
                        'background-color' => 'rgba(240, 245, 250, 1)',
                        'padding' => '12px',
                    ]
                ]
            ]
        ]
    ];
    $block->meta('pg_styles', $styles);
    
    // Finalize name
    $block->setAndSave('name', 'pg-text-' . $block->id);
    $block->setAndSave('title', 'pg-text-' . $block->id);
    
    $log->save('migrations', 'Responsive block created: ' . $block->name);
    
} catch (\Exception $e) {
    $log->save('migrations', 'ERROR: ' . $e->getMessage());
    throw $e;
}
```

---

## Best Practices

### 1. Always Check Resources Exist

```php
// ✅ Good - Check before using
$page = $pages->get(1049);
if (!$page->id) {
    throw new \Exception('Page not found');
}

// ❌ Bad - Assume it exists
$page = $pages->get(1049);
$parent = $page->parent;  // Might fail silently
```

### 2. Be Defensive with Metadata

```php
// ✅ Good - Create structure if missing
if (!isset($styles['pgitem'])) {
    $styles['pgitem'] = [];
}

// ❌ Bad - Assume structure
$styles['pgitem']['breakpoints']['base'] = [];  // May cause error
```

### 3. Use Meaningful Log Messages

```php
// ✅ Good - Clear, actionable messages
$log->save('migrations', 'Block created: pg-text-1041 at row 2, column 3');

// ❌ Bad - Vague messages
$log->save('migrations', 'Done');
```

### 4. Include Error Context

```php
// ✅ Good - Include what was attempted
try {
    // ...
} catch (\Exception $e) {
    $log->save('migrations', 'ERROR creating block on page 1049: ' . $e->getMessage());
}

// ❌ Bad - No context
catch (\Exception $e) {
    throw $e;  // Lost context
}
```

### 5. Test Migrations Locally Before Deploying

- Always run on a test database first
- Check the log: `/site/assets/logs/migrations.txt`
- Verify blocks look correct in admin UI
- Test responsive layouts on mobile

---

## Running Migrations

### Via Admin UI

1. Go to `/admin/setup/agenttools/migrations/`
2. Click "Apply" on pending migrations
3. Check the log for status

### Via CLI (If AgentTools CLI Enabled)

```bash
pw migrations:apply
```

### Check Logs

```bash
tail -f /site/assets/logs/migrations.txt
```

---

## Debugging Migrations

### Common Issues

**"Page not found"**
```php
// Solution: Verify page exists first
$page = $pages->get(1049);
if (!$page->id) {
    throw new \Exception('Page 1049 not found');
}
```

**"Template not found"**
```php
// Solution: Check template exists
$template = $templates->get('pg_text');
if (!$template->id) {
    throw new \Exception('Template pg_text not found');
}
```

**"Block created but not visible"**
```php
// Solution: Make sure you finalize the name (Step 4)
$block->setAndSave('name', 'pg-text-' . $block->id);
$block->setAndSave('title', 'pg-text-' . $block->id);
```

### Debug Output

```php
// Add debug info to logs
$log->save('migrations', 'DEBUG: Block ID = ' . $block->id);
$log->save('migrations', 'DEBUG: Block name = ' . $block->name);
$log->save('migrations', 'DEBUG: Parent ID = ' . $block->parent->id);

// Use var_export for complex data
$log->save('migrations', 'DEBUG: Styles = ' . var_export($styles, true));
```

---

## Migration Rollback

ProcessWire doesn't automatically track which migrations have been applied, so each migration should be designed to check if it's already been applied:

```php
// ✅ Good - Check if block already exists
$existing = $pages->findOne('name=pg-text-custom-block');
if ($existing->id) {
    $log->save('migrations', 'Block already exists, skipping');
    return;
}

// Create block...
```

To reverse/delete a block:

```php
$block = $pages->get('name=pg-text-1041');
if ($block->id) {
    $block->delete(true);  // true = recursive
    $log->save('migrations', 'Block deleted');
}
```

---

## Summary

- **Use migrations** for all repeatable changes
- **Follow the naming convention** for proper ordering
- **Include logging** for debugging and verification
- **Check resources exist** before using them
- **Test locally** before deploying to production
- **Be defensive** with metadata structure
- **Use try-catch** to catch and log errors

---

**Related Documentation:**
- [blocks.md](blocks.md) - Block creation details
- [metadata.md](metadata.md) - Metadata structure
- [reference.md](reference.md) - Quick lookup
- [architecture.md](architecture.md) - Understanding structure
