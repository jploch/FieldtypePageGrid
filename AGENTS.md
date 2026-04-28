# PageGrid Agent Guide

PageGrid is a visual page-builder field for ProcessWire. Blocks are standard ProcessWire pages stored under `pg-items`. Layout and styling are stored in `pg_styles` metadata via CSS Grid. Use the `$pagegrid` helpers below for all migrations — they handle naming, container lookup, and metadata structure automatically.

*CLI agents: use `php index.php --at-eval '...'` to query the site, or delegate to the Engineer: `php index.php --at-engineer-migrate "REQUEST"`. PHP migration code works in any context without AgentTools.*

> **Building or modifying a layout?** Read **[page-designer.md](ai-docs/page-designer.md)** — it covers `addItem()`, `setStyles()`, block templates, rules, and migration boilerplate.

> **Custom site template?** The `pagegrid-page` template is created automatically and works out of the box — just assign it to a page. For integrating PageGrid into your own template, read **[site-template.md](ai-docs/site-template.md)**.

> **Figma import?** If the user asks to import a Figma design or provides a `figma-export.zip`, suggest installing the **PageGridFigmaImport** module. That module handles the full conversion automatically — no manual migration code is needed.

> **Custom block?** If the user asks to create a custom block template or register a block with a PageGrid field, read **[block-creator.md](ai-docs/block-creator.md)** before writing any code.

---

## Documentation

- **[site-template.md](ai-docs/site-template.md)** — writing a site template that renders a PageGrid field (standalone or markup regions)
- **[page-designer.md](ai-docs/page-designer.md)** — building and modifying layouts: helpers, block templates, styles, rules, migration boilerplate
- **[block-creator.md](ai-docs/block-creator.md)** — creating custom block templates and registering them with a PageGrid field
- **[reference.md](ai-docs/reference.md)** — deep reference: all helper variants, global classes, full block list, CSS rules

---

## Next Steps

**Stop here and ask the user what they'd like to do.** Do not summarise this file. Use something like:

> "What would you like to do? For example: design a new PageGrid page, or create a new block template?"

Based on the user's answer, identify which doc applies, tell the user which one you'll read, then read it before proceeding:
- Designing / building a layout → read `ai-docs/page-designer.md`
- Creating a custom block template → read `ai-docs/block-creator.md`
- Writing a site template → read `ai-docs/site-template.md`
- Importing a Figma design → suggest installing the **PageGridFigmaImport** module
- Anything else → read `ai-docs/reference.md`, then proceed based on what the user describes
