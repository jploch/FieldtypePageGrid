# PageGrid Architecture

## Core Concept: Everything is a Page

This is the most important concept to understand: **In PageGrid, every block is a complete ProcessWire page.**

Blocks are not abstract builder objects or virtual items. They're real pages in the ProcessWire database with:
- A template (pg_text, pg_group, pg_gallery, etc.)
- Fields that store content
- Metadata for styling and positioning
- Parent-child relationships in the page tree
- All the capabilities of ProcessWire pages

This design enables:
- Full ProcessWire API access
- Database queries using `$pages->find()`
- Version control through migrations
- Standard ProcessWire field types
- Flexible nesting and relationships

---

## Page Tree Hierarchy

PageGrid pages live in a specific location in the page tree:

```
/admin/setup/pagegrid/pg-items/
└── pg-{page-id}/                    # Container for one page's blocks
    └── pg-{field-id}/               # Container for one field's blocks
        ├── pg-text-1041             # A text block
        ├── pg-gallery-1042          # A gallery block
        └── pg-group-1043            # A group block
            ├── pg-text-1044         # Nested text block
            └── pg-editor-1045       # Nested editor block
```

### Breaking Down the Path

1. **pg-items** - Global container for all PageGrid items
2. **pg-{page-id}** - Container for blocks on a specific page
   - Example: `pg-1049` = blocks for page 1049
   - Auto-created when you add the first block to a page
3. **pg-{field-id}** - Container for blocks in a specific field
   - Example: `pg-98` = blocks in field 98
   - Auto-created when needed
4. **Block pages** - The actual block pages
   - Named: `pg-{template-name}-{id}`
   - Example: `pg-text-1041` = text block with ID 1041

### Key Point: Containers Auto-Create

You don't manually create these container pages. ProcessWire module hooks automatically create them when needed. Your migration only needs to create the actual block page; the parent containers are handled for you.

---

## Templates

PageGrid uses special templates for different component types:

### Block Templates (in `/site/modules/PageGridBlocks/blocks/`)
- **pg_text** - Simple text content (usually single line)
- **pg_editor** - Rich text editor (WYSIWYG)
- **pg_gallery** - Image gallery with lightbox
- **pg_group** - Container for nested blocks
- **pg_slider** - Image/content carousel
- And 18+ more...

### Container Template
- **pg_container** - Wraps PageGrid field containers
  - Used for: `pg-{page-id}` and `pg-{field-id}` pages
  - Has special hooks to manage child pages
  - Not a visual block, but infrastructure

---

## Block Naming Convention

PageGrid block naming happens in **two steps**:

### Step 1: Creation (Temporary Name)
Create the block with a temporary name that includes timestamp:
```php
$block = new Page();
$block->template = 'pg_text';
$block->name = 'pg_text' . time();  // Creates: pg_text1712757600
$block->save();  // ProcessWire assigns ID (e.g., 1041)
```

**Why timestamp?** Prevents collision if multiple blocks created in same second.

### Step 2: Finalization (Final Name)
After save, use the assigned ID for the final name:
```php
$block->setAndSave('name', 'pg-text-' . $block->id);  // Creates: pg-text-1041
$block->setAndSave('title', 'pg-text-' . $block->id); // Same for title
```

**Why this two-step process?** ProcessWire assigns IDs only after the page is saved. We need the ID to create the final unique name.

### Format

**Final block page name:** `pg-{template-name}-{page-id}`

Examples:
- `pg-text-1041` - Text block, ID 1041
- `pg-group-1043` - Group block, ID 1043
- `pg-gallery-5002` - Gallery block, ID 5002

---

## Field Containers

Each PageGrid field on each page gets its own container:

```php
// Dynamic: find the PageGrid field from the page's template
$pgField = null;
foreach ($mainPage->template->fieldgroup as $f) {
    if ($f->type instanceof FieldtypePageGrid) {
        $pgField = $f;
        break;
    }
}

// For the page, get the field container: pg-{fieldId}
$fieldContainer = $itemsParent->get("name=pg-{$pgField->id}, template=pg_container");

// If it doesn't exist, create it:
if (!$fieldContainer->id) {
    $fieldContainer = new Page();
    $fieldContainer->template = 'pg_container';
    $fieldContainer->parent = $itemsParent;
    $fieldContainer->name = "pg-{$pgField->id}";
    $fieldContainer->title = "pg-{$pgField->id}";
    $fieldContainer->save();
}
```

The field container acts as the parent for all blocks in that field/page combination.

---

## Key Objects & Methods

### Finding Pages

```php
// Get a specific page
$page = wire('pages')->get(1049);

// Get the items container for a page
$itemsParent = wire('pages')->get("name=pg-{$page->id}");

// Get the PageGrid field dynamically
$pgField = null;
foreach ($page->template->fieldgroup as $f) {
    if ($f->type instanceof FieldtypePageGrid) { $pgField = $f; break; }
}

// Get the field container
$fieldContainer = wire('pages')->get("name=pg-{$pgField->id}, parent={$itemsParent}");

// Find all blocks in a field
$blocks = $fieldContainer->children();

// Find blocks by template
$textBlocks = $fieldContainer->find('template=pg_text');
```

### Working with Blocks

```php
// Create a block (see blocks.md for full process)
$block = new Page();
$block->template = wire('templates')->get('pg_text');
$block->parent = $fieldContainer;
// ... set content ...
$block->save();

// Access block content
$block->of(true);  // Output formatting on
echo $block->pg_text;

// Modify block
$block->of(false); // Output formatting off
$block->pg_text = 'New content';
$block->save();

// Get block metadata
$styles = $block->meta('pg_styles');

// Set block metadata
$block->meta('pg_styles', $metadata);
```

### Metadata Access

```php
// Get metadata
$data = $page->meta('pg_styles');

// Set metadata
$page->meta('pg_styles', $array);

// Remove metadata
$page->meta()->remove('pg_styles');
```

---

## Output Formatting

PageGrid pages use the standard ProcessWire output formatting:

```php
// Turn output formatting ON (for display)
$block->of(true);

// Turn output formatting OFF (for modification)
$block->of(false);

// Check state
if ($block->isOutputFormatting()) { ... }
```

When output formatting is ON, field values might be processed (formatted, sanitized). When OFF, you get raw database values.

---

## Database Structure

### Pages Table
Block pages live in the standard ProcessWire `pages` table:
- Each block is a row
- Fields: id, name, parent_id, template_id, etc.
- Standard ProcessWire fields stored in related tables

### Meta Table
Metadata (like pg_styles) lives in the `pages_meta` table:
- Accessed via `$page->meta()`
- Stored as name-value pairs
- Values are JSON-encoded
- Supports any custom metadata key

### Fields Table
Block field data stored like normal pages:
- If field is `pg_text` (Text fieldtype), stored in `field_pg_text` table
- If field is `pg_editor` (TinyMCE), stored in `field_pg_editor` table
- Each fieldtype handles storage according to its rules

---

## Common Queries

```php
// Get all blocks on a page (dynamic field lookup)
$page = wire('pages')->get(1049);
$pgField = null;
foreach ($page->template->fieldgroup as $f) {
    if ($f->type instanceof FieldtypePageGrid) { $pgField = $f; break; }
}
$itemsParent = wire('pages')->get("name=pg-{$page->id}");
$fieldContainer = $itemsParent->get("name=pg-{$pgField->id}, template=pg_container");
$blocks = $fieldContainer->children();

// Get blocks of specific template
$textBlocks = $fieldContainer->find('template=pg_text');

// Get blocks with specific CSS class
// (Note: CSS classes stored in metadata, so must iterate)
foreach ($blocks as $block) {
    $styles = $block->meta('pg_styles');
    if (isset($styles['pgitem']['cssClasses'])) {
        if (strpos($styles['pgitem']['cssClasses'], 'featured') !== false) {
            // This block has the 'featured' class
        }
    }
}

// Get blocks in a group
$group = wire('pages')->get(1043);
$nestedBlocks = $group->children();

// Delete a block
$block->delete(true);  // true = recursive delete
```

---

## Understanding the Module Hooks

The PageGridBlocks and FieldtypePageGrid modules use hooks to automate container creation:

```
When you save a block to a field container:
  1. ProcessWire saves the page
  2. Module hooks detect it's a block (based on parent)
  3. Hooks update the parent field container
  4. Hooks trigger cache clearing
  5. Block is now visible in the PageGrid editor
```

You don't need to understand these hooks to write migrations, but they ensure that container pages are automatically created and managed for you.

---

## Summary

- **Blocks are pages:** Full ProcessWire pages with all capabilities
- **Page tree location:** `/admin/setup/pagegrid/pg-items/pg-{page-id}/pg-{field-id}/`
- **Two-step naming:** Temporary name with timestamp, then final name with ID
- **Containers auto-create:** Don't manually create `pg-*` container pages
- **Metadata separate:** Layout/styling stored in `pg_styles` via meta() API
- **Standard ProcessWire:** Use regular ProcessWire APIs to work with blocks
- **Queryable:** Use `find()`, `get()`, etc. to select and filter blocks

---

**Related Documentation:**
- [blocks.md](blocks.md) - How to create and modify blocks
- [metadata.md](metadata.md) - Understanding pg_styles
- [migrations.md](migrations.md) - Creating migration files
- [reference.md](reference.md) - Quick reference
