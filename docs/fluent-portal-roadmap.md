# Fluent Portal Redesign Roadmap

> Goal: Rebuild every surface to visually match Microsoft 365 / Entra / Intune portals using the Fluent Design System (light mode first) without regressing existing Laravel features.

## 1. Theming & Tokens
- Replace legacy AdminLTE remnants with Fluent UI design tokens (brand, neutrals, semantic) sourced from `@fluentui/tokens` and Tailwind extensions.
- Centralize typography, spacing, radii, shadow, and motion variables under `:root` to keep CSS + Blade components aligned.
- Deliver a single `fluent-portal.css` entry (imported by `app.css`) that controls surfaces, shells, command bars, tables, and form primitives.

## 2. Layout Shells
- Rebuild `resources/views/layouts/app.blade.php` to mirror the Entra portal chrome: top global command bar, secondary breadcrumb zone, collapsible navigation rail with icon-only state, and responsive page canvas.
- Add a guest/auth shell to mirror the Microsoft account login experience.
- Ensure Alpine store drives nav width, focus outlines, and keyboard support.

## 3. Component System
- Standardize on `<x-fluent-*>` components for every interactive element (buttons, inputs, selects, tables, badges, dialogs, cards, tabs, empty states, toast banners).
- Map each component variant directly to a Fluent counterpart (e.g., primary = `AccentButton`, subtle = `GhostButton`, tables = `DataGridRow`).
- Bake in icon slots (left/right), helper text, and error states so feature views never reach for ad-hoc utility classes.

## 4. Feature View Audit
- Iterate through each folder under `resources/views` (dashboard, users, groups, applications, git-management, pim, profile) and refactor markup to compose the new components.
- Replace bespoke markup (Bootstrap cards, AdminLTE boxes, legacy forms) with Fluent cards + layout grids.
- Unify table headers, filters, flyouts, command bars, and detail panes so each screen reads like an Azure blade.

## 5. Utilities & Data Visualization
- Move chart color palettes, quick-action tiles, and status badges to reusable classes; match Azure portal tiles (icon box, label, chevron) and info cards.
- Expand helper JS to expose `FluentUI.flyout`, `FluentUI.toast`, and async loading indicators for API-bound interactions.

## 6. Quality Gates
- Vite/Tailwind build must tree-shake unused utilities; enforce Prettier/Lint rules for Blade + CSS formatting consistency.
- Validate responsive behavior (≤480px through ≥1440px), keyboard navigation, and color contrast (>= 4.5:1) per view.
- Smoke-test all Laravel forms, modals, and AJAX flows to confirm no regression while styling.

Tracking this checklist as we touch each area ensures the entire portal—not only dashboard or users—adopts the Microsoft 365/Entra presentation the product owner expects.
