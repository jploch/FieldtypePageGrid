# PageGrid Documentation Index

> **These docs are written for AI agents** working with PageGrid programmatically.
> For human-readable documentation, visit: **https://page-grid.com/ai-docs/developer/**

This directory contains comprehensive documentation for working with ProcessWire's PageGrid site builder. Use this index to find the right guide for your task.

## Quick Navigation

- **Just getting started?** → Start with [architecture.md](#architecture)
- **Need to create/modify blocks?** → See [blocks.md](#blocks)
- **Working with styling and layout?** → Check [metadata.md](#metadata)
- **Writing a migration?** → Review [migrations.md](#migrations)
- **Need quick lookup?** → Use [reference.md](#reference)

---

## Documents

### architecture.md {#architecture}

**Purpose:** Understand how PageGrid works at the fundamental level

**Topics:**
- Core concept: Everything is a page
- Page tree hierarchy and structure
- Block naming convention (two-step process)
- Templates and their purposes
- Key ProcessWire objects and methods

**Read this if you:**
- Are new to PageGrid
- Need to understand the page tree structure
- Want to know why blocks use two-step naming
- Are curious about how the module hooks work

**Key Sections:**
- Core Concept
- Page Tree Hierarchy
- Templates
- Block Naming Convention
- Related Objects & Methods

---

### blocks.md {#blocks}

**Purpose:** Learn how to create, modify, and delete blocks

**Topics:**
- Complete block creation process
- Creating blocks in groups (nesting)
- Working with block fields
- Modifying block metadata
- Deleting blocks
- Useful database queries

**Read this if you:**
- Need to create new blocks
- Want to understand field types in blocks
- Need to modify block content programmatically
- Are working with nested blocks in groups

**Key Sections:**
- Creating a Block (step-by-step)
- Creating a Block Inside a Group
- Block Templates
- Working with Block Fields
- Modifying Block Metadata
- Useful Queries

---

### metadata.md {#metadata}

**Purpose:** Master the pg_styles metadata structure and CSS Grid properties

**Topics:**
- pg_styles structure and format
- Responsive breakpoints (base, s, m, l)
- CSS Grid positioning properties
- Visual styling properties
- Responsive design patterns
- Working with metadata in code

**Read this if you:**
- Need to position blocks on a grid
- Want to add styling (background color, padding, etc.)
- Are implementing responsive layouts
- Need to understand CSS Grid properties
- Are modifying or creating metadata structures

**Key Sections:**
- pg_styles Metadata Structure
- Breakpoints
- CSS Grid Properties
  - Position Properties
  - Visual Properties
  - Custom CSS
- Complete Example
- Responsive Design

---

### migrations.md {#migrations}

**Purpose:** Know how to create repeatable migration files

**Topics:**
- Migration file structure and naming
- Common migration patterns
  1. Simple block creation
  2. Block creation with positioning
  3. Block creation with styling
  4. Modifying existing block metadata
  5. Responsive block creation
- Best practices
- Running and debugging migrations
- Error handling

**Read this if you:**
- Need to create a migration file
- Want migration code examples
- Are setting up repeatable changes
- Need to understand the migration workflow
- Are debugging migration issues

**Key Sections:**
- Migration File Structure
- Common Migration Patterns (5 detailed examples)
- Best Practices
- Running Migrations
- Debugging Migrations

---

### reference.md {#reference}

**Purpose:** Quick lookup and cheat sheet

**Topics:**
- Key facts table
- Page path patterns
- Common API calls
- CSS Grid properties quick reference
- Metadata example
- Breakpoints table
- Block templates list
- Common tasks with code
- Important notes

**Read this if you:**
- Need a quick code snippet
- Want to look up a specific property
- Need to remember API syntax
- Are writing code and need quick reference
- Want a comprehensive cheat sheet

**Key Sections:**
- Quick Facts
- Common API Calls
- CSS Grid Properties Quick Reference
- Common Tasks (with code)
- Important Notes

---

## Learning Path

### Path 1: Foundation Building
1. Read [architecture.md](#architecture) - Understand the concepts
2. Read [blocks.md](#blocks) - Learn block creation
3. Read [reference.md](#reference) - Practice with examples

### Path 2: Styling & Layout
1. Read [metadata.md](#metadata) - Understand metadata structure
2. Review [reference.md](#reference) - Look up CSS Grid properties
3. Practice with migration examples in [migrations.md](#migrations)

### Path 3: Writing Migrations
1. Skim [architecture.md](#architecture) for context
2. Review [blocks.md](#blocks) for block creation details
3. Study patterns in [migrations.md](#migrations)
4. Keep [reference.md](#reference) open for lookups

---

## Task-Based Guide

### "I need to create a simple text block"
1. Review: [blocks.md - Creating a Block](#blocks)
2. Copy pattern: [migrations.md - Pattern 1](#migrations)
3. Reference: [reference.md - Create Block](#reference)

### "I need to position a block at row 2, column 3"
1. Learn: [metadata.md - CSS Grid Properties](#metadata)
2. Copy pattern: [migrations.md - Pattern 2](#migrations)
3. Reference: [reference.md - Position Block](#reference)

### "I need to style a block with green background"
1. Learn: [metadata.md - Visual Properties](#metadata)
2. Copy pattern: [migrations.md - Pattern 3](#migrations)
3. Reference: [reference.md - Change Background Color](#reference)

### "I need update an existing block's styling"
1. Review: [metadata.md - Working with Metadata](#metadata)
2. Copy pattern: [migrations.md - Pattern 4](#migrations)
3. Reference: [reference.md - Modify Existing Block](#reference)

### "I'm creating a migration for production"
1. Study: [migrations.md - Migration File Structure](#migrations)
2. Choose pattern: [migrations.md - Common Patterns](#migrations)
3. Follow: [migrations.md - Best Practices](#migrations)
4. Reference: [reference.md - Logging & Debugging](#reference)

### "I need to make a responsive block"
1. Learn: [metadata.md - Breakpoints](#metadata)
2. Copy pattern: [migrations.md - Pattern 5](#migrations)
3. Understand: [metadata.md - Responsive Design Order](#metadata)

### "I'm debugging a migration that failed"
1. Check: [migrations.md - Debugging Migrations](#migrations)
2. Verify: [migrations.md - Check if Resources Exist](#migrations)
3. Reference: [reference.md - When You're Stuck](#reference)

---

## Key Concepts at a Glance

### The Fundamental Principle
**Everything in PageGrid is a ProcessWire Page.**

Blocks are not abstract builder objects—they're complete pages stored in the database with their own templates, fields, and metadata. This means you can:
- Query blocks like any other page
- Use ProcessWire APIs to work with them
- Store custom data via metadata
- Version control migrations alongside your code

### The Two-Step Naming Process
1. Create page with temporary name (gets ID)
2. Rename to final name using the ID

This is required because ProcessWire assigns IDs on save, and PageGrid needs the ID for final naming.

### The Four Layers

1. **Blocks** - The page objects themselves
   - Template, parent, name, fields
   
2. **Metadata** - Layout and styling
   - `pg_styles` stores positioning and CSS
   - CSS Grid properties (grid-column-start, etc.)

3. **Global Classes** - Reusable style definitions
   - Pages under `/admin/setup/pagegrid/pg-classes/` with `pg_container` template
   - Blocks reference them via `cssClass`/`cssClasses` in metadata
   - See [reference.md](#reference) for details
   
4. **Migrations** - Repeatable code
   - PHP files that create/modify blocks
   - Can be version controlled and deployed

---

## Common Questions

**Q: Are blocks saved in the database like regular pages?**  
A: Yes! They're full ProcessWire pages with templates, fields, and metadata.

**Q: Why do blocks need two-step naming?**  
A: ProcessWire assigns page IDs only after saving. The final name includes the ID for uniqueness.

**Q: How do I position blocks?**  
A: Using CSS Grid properties stored in `pg_styles` metadata: `grid-column-start`, `grid-row-start`, etc.

**Q: Can I create blocks with a migration?**  
A: Absolutely! Migrations are the recommended way to manage repeatable changes.

**Q: What if I need to nest blocks inside a group?**  
A: Create the block with the group as parent instead of the field container.

**Q: How do I make blocks responsive?**  
A: Add different breakpoint definitions (base, s, m, l) to `pg_styles`.

---

## File Locations

```
/site/modules/FieldtypePageGrid/
├── AGENT.md (AI entry point)
└── ai-docs/
    ├── README.md (this file)
    ├── architecture.md
    ├── blocks.md
    ├── metadata.md
    ├── migrations.md
    └── reference.md

/site/assets/at/migrations/
├── 20260410120000_create-hello-world-template.php
├── 20260410120003_add-text-block-to-pg-test.php
├── 20260410120006_add-text-block-to-group-page-1049.php
└── ... (more migrations)

/admin/setup/pagegrid/pg-items/
└── (PageGrid block pages - everything is a page here!)
```

---

## Tips for AI Agents

1. **Start with architecture.md** to understand the fundamentals
2. **Keep reference.md open** while coding for quick lookups
3. **Study migration patterns** in migrations.md before writing migrations
4. **Always check if resources exist** before using them (defensive programming)
5. **Use meaningful log messages** for debugging: `wire('log')->save('migrations', "...")`
6. **Test metadata structure** with `var_dump()` or `bd()` before deploying
7. **Reference existing blocks** when unsure about metadata structure

---

## Related Projects

- ProcessWire CMS: https://processwire.com
- FieldtypePageGrid Module: This folder
- PageGridBlocks: Located in `/site/modules/PageGridBlocks/`
- AgentTools: Located in `/site/modules/AgentTools/`

---

**Last Updated:** April 10, 2026  
**Documentation Version:** 1.0  
**PageGrid Version:** 0.2.0
