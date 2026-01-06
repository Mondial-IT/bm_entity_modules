# Migration Plan — bm_fefq to Drupal-native config (bm_aggrid compatible)

This plan describes how to migrate the legacy FEFQ YAML/taxonomy metadata into Drupal 11 config entities and admin UIs that integrate cleanly with bm_aggrid (or other consumers).

## Goals
- Replace FEFQ YAML/taxonomy-driven “virtual field” registry with Drupal config entities.
- Preserve capabilities: virtual fields, redirects/extends, grouping, filter components, AG Grid column templates, and custom conditions/filters.
- Make admin UX native: tabledrag for ordering/grouping, per-field settings, reusable column/filter templates.
- Keep config exportable via CMI and translatable.

## Proposed Config Entities
1) `bm_fefq_virtual_field` (translatable)
   - id, label (translatable), description
   - entity_type, bundle constraint(s)
   - field_path (supports chained references/paragraphs)
   - conditions_allowed (IN, NOT IN, LIKE, BETWEEN, etc.)
   - filter_type/component reference (text/date/select/taxonomy depth)
   - hierarchy hints: depth/base_level for taxonomy refs
   - aggrid_template reference (optional)
   - tooltip/placeholder (translatable)
   - parent + weight (for grouping/tabledrag)
   - extends (for redirect-like inheritance)
   - handler/plugin (optional for custom SQL cases)

2) `bm_fefq_column_template`
   - id, label
   - gridOptions fragment (width, editor, renderer, comparator, etc.)
   - CSS classes

3) `bm_fefq_filter_component`
   - id, label
   - component type/template (e.g., text input, date range)
   - UI strings: user_label, placeholder, tippy (translatable)

4) (Existing) `bm_aggrid_display` or equivalent consumer
   - Associate displays with selected virtual fields and per-display overrides.

## Migration Features (codex-style)

### Feature 1.0 — Scaffolding
- Create the bm_fefq module directory with info.yml, services, routing, and base config schemas.
- Define config entity types: virtual_field, column_template, filter_component (translatable where applicable).

### Feature 1.1 — Column templates import
- Parse `aggrid_shared_definitions` from legacy YAML into `bm_fefq_column_template` entities.
- Preserve gridOptions fragments and CSS classes; map `redirect` as template inheritance if needed.

### Feature 1.2 — Filter components import
- Parse `component__*` definitions into `bm_fefq_filter_component` entities.
- Store user_label/placeholder/tippy as translatable fields.

### Feature 1.3 — Virtual fields import (core registry)
- Parse legacy `name` entries (skip IGNORE) into `bm_fefq_virtual_field`:
  - entity_type/bundle, field_path
  - conditions_allowed, use_filter/filter_type
  - depth/base_level for taxonomy cases
  - user_label/tippy/placeholder (translatable)
  - aggrid template reference (if present)
  - extends: model `redirect` and merge overrides
  - parent/weight: derive grouping if available; otherwise flat
  - handler: flag SQL/custom cases for plugins
- Store grouping in parent+weight so tabledrag can be used in the UI.

### Feature 1.4 — Admin UI (tabledrag + forms)
- Provide an admin listing of virtual fields with tabledrag (parent/weight) and inline links to edit.
- Virtual field edit form: pick entity type/bundle, field path, conditions, filter component, aggrid template, hierarchy hints, parent/weight, tooltip/labels.
- Column template and filter component CRUD UIs.

### Feature 1.5 — Data service integration
- Add a service to resolve a set of virtual fields into:
  - Column definitions (using field types + referenced templates)
  - Field access enforcement (core field access + optional role restrictions)
  - Hierarchy/grouping hints for consumers
- Expose a helper to consumers (bm_aggrid or others) to fetch resolved columnDefs/metadata by display config.

### Feature 1.6 — Migration script
- One-off Drush command to import the legacy YAML into the new config entities:
  - Column templates
  - Filter components
  - Virtual fields (with extends/overrides)
- Map legacy aggrid redirects to template references.
- Map IGNORE entries to skips.
- Optionally emit a report of unmapped/custom SQL handlers to follow up with plugins.

### Feature 1.7 — Documentation & Help
- Add README and help_topics describing the new architecture and how to configure virtual fields, templates, and components.
- Include instructions for running the migration command and verifying imported data.

## Notes on Translation and Access
- Mark label/tooltip fields as translatable in config schema; use config_translation for UI labels.
- Respect entity/field translation for values; do not duplicate content values.
- Enforce field access via core field access; optionally add per-field role restrictions in virtual_field config.

## Notes on Custom SQL/Handlers
- For entries with bespoke SQL/filters, add a “handler” plugin on virtual_field to encapsulate logic rather than embedding raw SQL in config.
- Flag such entries during import for manual plugin assignment if needed.

## Deliverables
- bm_fefq module with config entities, schemas, and CRUD UIs.
- Migration Drush command to import legacy FEFQ YAML.
- Services to resolve virtual fields into consumer-friendly metadata (e.g., bm_aggrid columnDefs).
- Documentation/help covering usage and migration steps.
