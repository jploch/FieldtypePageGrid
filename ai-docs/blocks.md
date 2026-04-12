# PageGrid Blocks - Creation and Management

## Creating a Block (Complete Process)

Creating a PageGrid block is a **4-step process**:

### Step 1: Get Required Objects

```php
// Get the main page
$mainPage = wire('pages')->get(1049);  // Page ID 1049

// Find the first FieldtypePageGrid field on the page's template
$pgField = null;
foreach ($mainPage->template->fieldgroup as $f) {
    if ($f->type instanceof FieldtypePageGrid) {
        $pgField = $f;
        break;
    }
}

// Get or create the items parent container (pg-{pageId})
$itemsParent = wire('pages')->get("name=pg-{$mainPage->id}");
if (!$itemsParent->id) {
    $itemsParent = new Page();
    $itemsParent->template = 'pg_container';
    $itemsParent->parent = wire('pages')->get('name=pg-items');
    $itemsParent->name = "pg-{$mainPage->id}";
    $itemsParent->title = $mainPage->title . ' items';
    $itemsParent->save();
}

// Get or create the field container (pg-{fieldId})
$fieldContainer = $itemsParent->get("name=pg-{$pgField->id}, template=pg_container");
if (!$fieldContainer->id) {
    $fieldContainer = new Page();
    $fieldContainer->template = 'pg_container';
    $fieldContainer->parent = $itemsParent;
    $fieldContainer->name = "pg-{$pgField->id}";
    $fieldContainer->title = "pg-{$pgField->id}";
    $fieldContainer->save();
}
```

### Step 2: Create the Block Page (Temporary Name)

```php
// Create block with temporary name (includes timestamp to avoid collision)
$block = new Page();
$block->template = wire('templates')->get('pg_text');  // Use the template
$block->parent = $fieldContainer;  // Set field container as parent
$block->name = 'pg_text' . time();  // Temporary name: pg_text1712757600
$block->title = $block->name;
$block->save();  // ProcessWire assigns ID here
```

**Why the temporary name?** We need the ID before creating the final name, and IDs are assigned only after save().

### Step 3: Set Block Content

```php
// Turn off output formatting for modification
$block->of(false);

// Set field values
$block->pg_text = 'Hello world';
// If the block has other fields, set them too
// $block->other_field = 'value';

// Save changes
$block->save();
```

### Step 4: Finalize the Name (Two-Step Naming)

```php
// Now use the ID (assigned during first save) for the final name
$block->setAndSave('name', 'pg-text-' . $block->id);   // pg-text-1041
$block->setAndSave('title', 'pg-text-' . $block->id);  // Same for title
```

**Result:** Block is now named `pg-text-1041` and visible in PageGrid.

---

## Complete Block Creation Example

```php
<?php
// Create a text block on a page
wire('log')->save('migrations', "Creating text block");

// Get main page
$mainPage = wire('pages')->get(1049);
if (!$mainPage->id) {
    wire('log')->save('migrations', "ERROR: Page 1049 not found");
    return;
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
$itemsParent = wire('pages')->get("name=pg-{$mainPage->id}");
if (!$itemsParent->id) {
    $itemsParent = new Page();
    $itemsParent->template = 'pg_container';
    $itemsParent->parent = wire('pages')->get('name=pg-items');
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
$block->template = wire('templates')->get('pg_text');
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

wire('log')->save('migrations', "Block created: {$block->name} (ID: {$block->id})");
```

---

## Creating a Block Inside a Group

Groups are blocks with the `pg_group` template that can contain other blocks.

### Process

```php
// Get the group page (assuming group ID is 1043)
$group = wire('pages')->get(1043);

// The group itself is a page, so we use it as the parent
// instead of the field container

$block = new Page();
$block->template = wire('templates')->get('pg_text');
$block->parent = $group;  // Parent is the group, not the field container!
$block->name = 'pg_text' . time();
$block->title = $block->name;
$block->save();

// Set content
$block->of(false);
$block->pg_text = 'Content inside group';
$block->save();

// Finalize name
$block->setAndSave('name', 'pg-text-' . $block->id);
$block->setAndSave('title', 'pg-text-' . $block->id);
```

### Key Difference
- **Normal blocks:** Parent is field container (`pg-{field-id}`)
- **Nested blocks:** Parent is the group block itself

---

## Block Templates

These modular components are the building blocks of your website. Each is designed for a specific purpose. Activate the ones you need under **Setup > PAGEGRID** in your admin.

> **Developer note:** You can modify the markup of these blocks or build entirely new blocks from scratch — see [Creating a Custom Block Template](#creating-a-custom-block-template) below.

### Available Blocks

| Template | Display Name | Description | Key Fields |
|----------|-------------|-------------|------------|
| `pg_accordion` | Accordion | Collapsible accordion with expandable/collapsible items. Each item displays a title with expand/collapse icons and content that reveals when clicked. | *(children act as items)* |
| `pg_blueprint` | Reference | Renders a referenced page block by displaying another page's content. Acts as an include/embed mechanism for reusing page content. | `pg_blueprint_parent` (Page) |
| `pg_code` | Code | Allows embedding custom HTML, CSS, and JavaScript. Provides a code block that executes on the frontend while showing a disabled preview in the backend editor. | `pg_code` (Textarea) |
| `pg_datalist` | Datalist | Displays a list of child pages from a parent page with images, videos, and customizable fields. Creates a filterable/searchable list view with thumbnails. | `pg_datalist` (Page), `pg_datalist_fields` (Options), `pg_datalist_limit` (Integer) |
| `pg_editor` | Rich Text | Renders rich text content edited with TinyMCE. Supports formatted text, inline styles, links, and complex formatting. | `pg_editor` (Textarea/TinyMCE) |
| `pg_gallery` | Gallery | Displays images in a PhotoSwipe-compatible gallery container. Supports responsive images with multiple size variants for optimal loading. | `pg_gallery` (Images) |
| `pg_gallery_video` | Gallery Video | Renders a video in a PhotoSwipe gallery with playable preview. Includes thumbnail/poster support and video options. | `pg_gallery_video` (File), `pg_gallery_video_poster` (Image), `pg_gallery_video_options` (Text) |
| `pg_group` | Group | A container block that wraps child elements and optionally links to internal or external pages. Renders as a semantic HTML element with flexible styling. | `pg_group_link` (Text), `pg_group_link_page` (Integer) |
| `pg_iframe` | Iframe | Embeds iframes with privacy-friendly lazy loading for YouTube and Vimeo URLs. Automatically fetches and caches video thumbnails and titles for GDPR compliance. | `pg_iframe_url` (Text), `pg_iframe_thumbnail` (Image), `pg_iframe_title` (Text), `pg_iframe_ratio` (Options) |
| `pg_image` | Image | Displays a responsive image with optional link and caption. Generates responsive srcsets for multiple screen sizes and supports aspect ratio constraints. | `pg_image` (Image, max 1), `pg_image_link` (Page), `pg_image_link_external` (Text), `pg_image_caption` (Textarea) |
| `pg_prev_next` | Prev / Next | Displays previous/next navigation controls with an optional index link. Automatically cycles through page siblings for sequential navigation. | `pg_prev_next_index` (Page), `pg_prev_next_indexlabel` (Text), `pg_prev_next_prevlabel` (Text), `pg_prev_next_nextlabel` (Text) |
| `pg_slider` | Slider | Displays a carousel/slider with navigation arrows, dots, and autoplay support. Uses the Glide.js library for smooth slide transitions. | `pg_slider_autoplay` (Textarea) |
| `pg_spacer` | Spacer | Renders an empty spacing block for layout control. Used to add vertical spacing between blocks without content. | *(none)* |
| `pg_text` | Text | Renders rich text content with support for line breaks and links via TinyMCE. Simpler formatting options than Rich Text. | `pg_text` (Textarea) |
| `pg_video` | Video | Renders an HTML5 video player with optional poster image, captions, and external link support. Supports autoplay and lazy loading. | `pg_video` (File), `pg_video_poster` (Image), `pg_video_options` (Text), `pg_video_link` (Page), `pg_video_caption` (Textarea) |

> Additional blocks (Language, Lottie, Marker, Navigation, Plain Text, Sequenz) may be available via the PageGridBlocks module download. Check **Setup > PAGEGRID > Download block modules** for the full list.

### Discovering Templates Programmatically

```php
$templates = wire('templates')->find('name^=pg_');
foreach ($templates as $t) {
    echo $t->name . "\n";
}
```

---

## Working with Block Fields

### Understanding Block Fields

Each block template has its own set of fields. Access them like any ProcessWire page:

```php
$block->of(false);  // Turn off output formatting for editing

// Set values
$block->pg_text = 'New content';
$block->pg_image = 1234;  // Image page ID
$block->pg_gallery = '1001|1002|1003';  // Pipe-separated IDs

// Get values
$text = $block->pg_text;
$images = $block->pg_gallery;

$block->save();
```

### Common Block Fields

**Text fields:**
- `pg_text` - Single-line text
- `pg_editor` - Rich text (WYSIWYG)
- `pg_html` - Custom HTML

**Media fields:**
- `pg_image` - Single image
- `pg_gallery` - Multiple images
- `pg_slider_imgs` - Slider images

**Link/URL fields:**
- `pg_link` - Page/URL link
- `pg_video_url` - Video URL

Check the block template to see what fields are available.

---

## Modifying Block Metadata

Metadata (positioning, layout, styling) is stored separately in `pg_styles`:

```php
// Get current metadata
$styles = $block->meta('pg_styles');

// Update metadata (see metadata.md for structure)
if (!isset($styles['pgitem']['breakpoints']['base'])) {
    $styles['pgitem']['breakpoints']['base'] = [];
}
if (!isset($styles['pgitem']['breakpoints']['base']['css'])) {
    $styles['pgitem']['breakpoints']['base']['css'] = [];
}

$styles['pgitem']['breakpoints']['base']['grid-column-start'] = '1';
$styles['pgitem']['breakpoints']['base']['grid-row-start'] = '2';
$styles['pgitem']['breakpoints']['base']['css']['background-color'] = 'rgba(255, 0, 0, 1)';

// Save metadata
$block->meta('pg_styles', $styles);
```

See [metadata.md](metadata.md) for complete metadata structure.

---

## Deleting Blocks

```php
// Get the block
$block = wire('pages')->get('name=pg-text-1041');

// Delete it (true = recursive, deletes all children too)
$block->delete(true);

// Log it
wire('log')->save('migrations', "Block deleted: {$block->name}");
```

### Safety Checks

```php
// Make sure it exists and is a block
$block = wire('pages')->get('name=pg-text-1041');

if (!$block->id) {
    die("Block not found");
}

// Check if it's a PageGrid block
if (!in_array('Blocks', $block->template->tags)) {
    die("Not a PageGrid block");
}

// Safe to delete
$block->delete(true);
```

---

## Useful Queries

### Find All Blocks in a Field

```php
// Dynamic lookup: get the PageGrid field from the page's template
$page = wire('pages')->get(1049);
$pgField = null;
foreach ($page->template->fieldgroup as $f) {
    if ($f->type instanceof FieldtypePageGrid) { $pgField = $f; break; }
}
$itemsParent = wire('pages')->get("name=pg-{$page->id}");
$fieldContainer = $itemsParent->get("name=pg-{$pgField->id}, template=pg_container");
$blocks = $fieldContainer->children();

foreach ($blocks as $block) {
    echo $block->name . " - " . $block->template->name . "\n";
}
```

### Find Blocks of Specific Type

```php
$textBlocks = $fieldContainer->find('template=pg_text');

echo "Found " . count($textBlocks) . " text blocks";
```

### Find All Blocks in All Pages

```php
$pgItems = wire('pages')->get('name=pg-items');
$allBlocks = $pgItems->find('template!=pg_container, has_parent=' . $pgItems->id);

echo "Total blocks: " . count($allBlocks);
```

### Find Blocks with Specific CSS Class

```php
// Note: CSS classes are stored in metadata, so we must iterate
// (reuse $fieldContainer from dynamic lookup above)

foreach ($fieldContainer->children() as $block) {
    $styles = $block->meta('pg_styles');
    if (isset($styles['pgitem']['cssClasses']) && 
        strpos($styles['pgitem']['cssClasses'], 'featured') !== false) {
        echo "Block " . $block->name . " has class 'featured'\n";
    }
}
```

### Find Blocks by Content (Text)

```php
// Find text blocks with specific content
$results = wire('pages')->find('template=pg_text, pg_text%=keyword');

foreach ($results as $block) {
    if (in_array('Blocks', $block->template->tags)) {
        echo "Found: " . $block->name . "\n";
    }
}
```

---

## Best Practices

1. **Always check if parent exists** before creating a block
2. **Use meaningful comments** in migrations for clarity
3. **Test queries** before using them in production
4. **Use `of(false)` when modifying** and `of(true)` when displaying
5. **Log all block operations** for debugging
6. **Save after each major change** to ensure data persists
7. **Use defensive programming** - check if resources exist before using

---

## Common Errors

**"Parent not found"**
```
Solution: Create the parent container first (see Step 1 above)
```

**"Template not found"**
```
Solution: Verify the template exists: wire('templates')->get('pg_text')
```

**"Block saved but not visible in PageGrid"**
```
Solution: Make sure you completed Step 4 (finalize the name)
```

**"Can't modify field - output formatting error"**
```
Solution: Call $block->of(false) before modifying fields
```

---

## Block Templates (Official Reference)

### What is a Block Template?

A block template is a custom PHP file that defines how a block is rendered. To use PageGrid, you need to add block templates to your field.

You can:
- **Use ready-made blocks** from the PageGridBlocks module
- **Create custom block templates** from scratch
- **Modify existing blocks** by copying them to your `site/templates/blocks/` folder

### Ready-Made Blocks

To use ready-made block templates:

1. Go to **Setup > PAGEGRID** in ProcessWire admin
2. Click "Download block modules"
3. Select the blocks you want to use
4. Click Save

Your selected blocks are now available in the PageGrid editor.

### Creating a Custom Block Template

#### File Structure

Create block templates in `site/templates/blocks/`:

```
site/templates/blocks/
├── your_block_name.php      (Required - HTML/PHP markup)
├── your_block_name.css      (Optional - styling)
└── your_block_name.js       (Optional - JavaScript)
```

#### Step 1: Create the PHP Template

Create `site/templates/blocks/your_block_name.php`:

```php
<?php
// Your block's HTML and PHP logic go here
?>
<div pg-wrapper>
    <h2><?= $page->title ?></h2>
    <p><?= $page->body ?></p>
</div>
```

#### Step 2: Register with PageGrid

1. Go to **Setup > PAGEGRID** in ProcessWire admin
2. Select your `.php` file
3. Click Save

ProcessWire automatically creates a template with the same name.

#### Step 3: Define Block Fields (Optional)

Go to **Setup > Templates** and find your template:

1. Add fields that your block will use (text, image, etc.)
2. These correspond to `$page->fieldName` in your template

#### Step 4: Build Your Markup

In your block template, access field values using `$page`:

```php
<div pg-wrapper>
    <h1><?= $page->myText ?></h1>
    <img src="<?= $page->myImage->url ?>">
    <p><?= $page->body ?></p>
</div>
```

**Important:** Do NOT use the "pg" prefix for custom templates or fields (e.g., avoid "pg_myblock") to avoid naming conflicts.

### Customize Your Block Template

#### Wrapper Element

By default, PageGrid wraps your block in a `<div>`. Control this with the `pg-wrapper` attribute:

```php
<!-- Use an <h1> as the wrapper instead of <div> -->
<h1 pg-wrapper class="my-class">
    Hello World
</h1>
```

#### Wrapper Element Attributes

The wrapper element supports special attributes that control PageGrid behavior:

**`pg-children`** - Allow nested blocks:

```php
<section pg-children="true" pg-wrapper>
    <?php foreach($page->children() as $item) { ?>
        <?= $pagegrid->renderItem($item) ?>
    <?php } ?>
</section>
```

**Restrict child templates:**

```php
<section pg-children="pg_text pg_image" pg-wrapper>
    <!-- Only pg_text and pg_image blocks allowed as children -->
    <?php foreach($page->children() as $item) { ?>
        <?= $pagegrid->renderItem($item) ?>
    <?php } ?>
</section>
```

**`pg-children-label`** - Custom label for the children tab:

```php
<section pg-children="true" pg-children-label="Slides" pg-wrapper>
    <!-- Children tab will be labeled "Slides" -->
</section>
```

**`pg-children-tab`** - Control where children field appears:

```php
<!-- Append children field to content tab instead of separate tab -->
<section pg-children="true" pg-children-tab="append" pg-wrapper>
    <!-- ... -->
</section>
```

**`pg-autotitle`** - Disable automatic title generation for children:

```php
<section pg-children="true" pg-autotitle="false" pg-wrapper>
    <!-- Users must enter titles manually -->
</section>
```

**`pg-reload-script`** - Disable JavaScript execution on AJAX calls:

```php
<div pg-reload-script="false" pg-wrapper>
    <!-- JS won't re-execute after backend ajax calls -->
</div>
```

#### Backend/Frontend Detection

Check whether rendering in admin or frontend:

**PHP:**

```php
<?php
if($pagegrid->isBackend()) {
    // render admin-only markup
} else {
    // render frontend markup
}
?>
```

**JavaScript:**

```javascript
var isBackend = document.querySelectorAll('.pg-is-backend').length;
if(isBackend) {
    // run JS only in admin
} else {
    // run JS only on frontend
}
```

#### Style Panel

If enabled in field settings, users can style elements visually. Elements can be styled by tag name or class:

```php
<span class="myclass" data-class="myclass">
    This element can be styled
</span>
```

Add CSS classes to elements programmatically:

```php
<span class="myclass <?= $pagegrid->getCssClasses($page, 'myclass') ?>" data-class="myclass">
    Hello
</span>
```

#### Inline Editor

Superusers and users with `page-edit-front` permission can edit content directly on the page.

**Text fields** become editable automatically if enabled in PageGrid settings.

**File/Image uploads:**

```php
<pg-edit page='<?= $page->id ?>' field='image'>
    <?php if($page->image) { ?>
        <img src='<?= $page->image->url ?>'>
    <?php } ?>
</pg-edit>
```

#### Get Main Page

In block templates, `$page` refers to the block itself. To get the main page containing the grid:

```php
<?php
$mainPage = $pagegrid->getPage($page);
echo "Grid is on: " . $mainPage->title;
?>
```

### Custom CSS and Styling

#### Style Panel

PageGrid includes an optional **Style Panel** that allows users to manipulate CSS properties directly on the page visually.

**Features:**
- Style HTML elements by tag name or class
- Add custom CSS classes to block items
- Only enabled for superusers by default (but can be enabled for other users)
- Can be disabled in module settings if you prefer pure code approach

**Usage in templates:**

Use the `data-class` attribute to specify which class should be the default selector:

```php
<span class="myclass" data-class="myclass">
    This element can be styled via the Style Panel
</span>
```

Users with the `pagegrid-style-panel` permission can then style this element visually.

#### Custom CSS Code

You can customize PageGrid styling by writing custom CSS. It's recommended to load custom CSS **after** PageGrid styles so you can override defaults:

```php
<?= $pagegrid->styles($page) ?>
<!-- Your custom CSS loaded after PageGrid -->
<link rel="stylesheet" href="/site/templates/custom-styles.css">
```

**Auto-loading CSS:** PageGrid automatically loads `.css` files from your block folders that match the block template name:

```
site/templates/blocks/
├── my_block.php
├── my_block.css  (automatically loaded)
└── my_block.js   (automatically loaded)
```

#### Wrapper Styles

PageGrid uses a **12-column CSS grid by default**, but you can customize it:

```css
/* Change to 6-column grid */
.pg {
    grid-template-columns: repeat(6, 1fr);
}

/* Use flexbox instead */
.pg {
    display: flex;
    flex-direction: column;
}

/* Main wrapper as block layout (for vertical-only dragging) */
.pg-main {
    display: block;
}
```

You can use **CSS Grid, Flexbox, or Block** - PageGrid makes no assumptions about your layout method.

#### Item Styles

Control how items are positioned and sized in the grid:

```css
/* Items take available space (no overlap) */
.pg .pg-item {
   grid-row-start: auto; 
   grid-column-start: auto;
}

/* Items freely positioned on columns (vertical auto) */
.pg .pg-item {
   grid-row-start: auto; 
}

/* Default size for new items (span 3 columns) */
.pg .pg-item-added {
   grid-column-end: span 3;
   grid-row-start: auto;
}
```

**By default**, items are placed manually and can overlap. To make items flow automatically into available space, set `grid-row-start: auto;` on `.pg .pg-item`.

---

## Summary

- **4-step process:** Get objects → Create page → Set content → Finalize name
- **Nested blocks:** Use the group as parent, not the field container
- **Fields vary:** Different templates have different fields
- **Metadata separate:** Layout/styling stored in `pg_styles`, not in fields
- **Always log:** Include `wire('log')->save()` for debugging
- **Test queries:** Verify resources exist before using them

---

**Related Documentation:**
- [architecture.md](architecture.md) - Understanding the structure
- [metadata.md](metadata.md) - Working with pg_styles
- [migrations.md](migrations.md) - Creating migration files
- [reference.md](reference.md) - Quick lookup
