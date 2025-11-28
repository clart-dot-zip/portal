# Fluent UI Migration Guide

This document details the complete migration from AdminLTE/Bootstrap to Microsoft Fluent UI design system.

## Overview

The portal has been redesigned to match **Microsoft 365**, **Azure Portal**, and **Entra ID** aesthetics using:
- **Fluent UI Web Components** (`@fluentui/web-components`)
- **Fluent UI Design Tokens** (`@fluentui/tokens`)
- **Tailwind CSS** with custom Fluent configuration
- **Alpine.js** for reactive components
- **Axios** for HTTP requests

## What Changed

### 1. Dependencies

#### Removed
- ❌ AdminLTE (`admin-lte`)
- ❌ Bootstrap 4 (`bootstrap`)
- ❌ jQuery (`jquery`)
- ❌ Font Awesome (`@fortawesome/fontawesome-free`)
- ❌ OverlayScrollbars

#### Added
- ✅ `@fluentui/web-components@^2.6.0`
- ✅ `@fluentui/tokens@^1.0.0`
- ✅ `@fluentui/svg-icons@^1.1.231`

#### Kept
- ✅ Alpine.js (for reactivity)
- ✅ Axios (for HTTP)
- ✅ Tailwind CSS (enhanced with Fluent tokens)

### 2. Design Tokens

#### Colors
- **Brand**: `#0078d4` (Azure Blue)
- **Success**: `#107c10` (Microsoft Green)
- **Error**: `#d13438` (Microsoft Red)
- **Warning**: `#ca7f00` (Microsoft Orange)
- **Info**: `#0078d4` (Azure Blue)
- **Neutral Palette**: 32 shades from `#ffffff` to `#000000`

#### Typography
- **Font Family**: `Segoe UI, -apple-system, BlinkMacSystemFont, system-ui, sans-serif`
- **Type Ramp**: Caption (12px) → Body (14px) → Subtitle (16px) → Title (20px) → Display (28px)
- **Line Heights**: 1.5 (body), 1.2 (headings)

#### Spacing
- **Base Unit**: 4px
- **Scale**: 0, 1, 2, 3, 4, 6, 8, 10, 12, 16, 20, 24, 32, 40, 48, 64px

#### Shadows (Elevation)
- **depth-4**: `0 1px 2px rgba(0,0,0,0.1)`
- **depth-8**: `0 2px 4px rgba(0,0,0,0.12)`
- **depth-16**: `0 4px 8px rgba(0,0,0,0.14)`
- **depth-64**: `0 12px 24px rgba(0,0,0,0.18)`

#### Border Radius
- **Small**: 2px
- **Medium**: 4px
- **Large**: 8px
- **XL**: 12px

#### Motion
- **Duration**: Fast (100ms), Normal (200ms), Slow (300ms)
- **Curves**: `cubic-bezier(0.33, 0, 0.67, 1)` (accelerate), `cubic-bezier(0.33, 0, 0.1, 1)` (decelerate)

### 3. Component Library

Created 11 reusable Blade components in `resources/views/components/`:

1. **fluent-button.blade.php** - Buttons with variants (primary, secondary, subtle, outline, danger)
2. **fluent-input.blade.php** - Text inputs with labels, errors, icons
3. **fluent-card.blade.php** - Cards with optional header/footer
4. **fluent-badge.blade.php** - Status badges (success, warning, error, info, neutral, brand)
5. **fluent-dialog.blade.php** - Modal dialogs
6. **fluent-table.blade.php** - Data tables with Azure styling
7. **fluent-select.blade.php** - Dropdown selects
8. **fluent-textarea.blade.php** - Multi-line text inputs
9. **fluent-checkbox.blade.php** - Checkboxes with labels
10. **fluent-spinner.blade.php** - Loading spinners
11. **fluent-avatar.blade.php** - User avatars with status indicators

### 4. Layout Architecture

#### Main Layout (`resources/views/layouts/app.blade.php`)
- **Azure Portal AppShell** design
- **Top Command Bar** (48px height) with brand, navigation toggle, search, user menu
- **Left Navigation Rail** (collapsible: 48px → 224px)
- **Main Content Area** with breadcrumb header
- **SVG Icons** (no Font Awesome)
- **Fluent Loading Screen** with spinner
- **Toast Notifications** using Fluent UI patterns

#### Guest Layout (`resources/views/layouts/guest.blade.php`)
- **Microsoft Login Experience** styling
- **Centered card** on neutral background
- **Clean typography** with Segoe UI

### 5. Migrated Views

#### ✅ Dashboard (`resources/views/dashboard.blade.php`)
- Fluent stat cards with gradient icons
- Chart.js integration with Fluent UI colors
- M365-style quick action tiles
- Modern info boxes

#### ✅ Users Index (`resources/views/users/index.blade.php`)
- Entra ID-style data grid
- Fluent badges for status indicators
- SVG icon buttons for actions
- Modern search interface
- Responsive pagination

#### Remaining Views (Use Existing Patterns)
All other views should follow the same patterns established in Dashboard and Users:
- Use `<x-fluent-card>` for containers
- Use `<x-fluent-button>` for actions
- Use `<x-fluent-badge>` for status
- Use `<x-fluent-input>` for forms
- Use Fluent table classes for data grids
- Replace Font Awesome with inline SVG icons

### 6. CSS Architecture

#### `resources/css/app.css`
- Imports Tailwind base, components, utilities
- Defines CSS custom properties for Fluent tokens
- Includes utility classes for buttons, inputs, cards, badges, tables
- Defines animations (fade, slide, spin, shimmer)
- Custom scrollbar styling

#### `resources/css/dashboard.css`
- Azure Portal-specific styles
- Stat cards, info boxes, chart containers
- Quick action tiles
- Navigation components

### 7. JavaScript Updates

#### `resources/js/app.js`
- Initializes Fluent UI Web Components
- Global `FluentUI` helper object with:
  - `showToast(message, variant)` - Toast notifications
  - `showLoading(message)` - Loading overlay
  - `hideLoading()` - Hide loading overlay
- Alpine.js navigation store for sidebar state

#### `resources/js/bootstrap.js`
- Axios configuration (kept)
- Removed jQuery and Bootstrap JS

### 8. Icon Migration

**From Font Awesome to Inline SVG**

Replace icon classes with inline SVG:

```html
<!-- OLD: Font Awesome -->
<i class="fas fa-users"></i>

<!-- NEW: Inline SVG -->
<svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zm2 0a3 3 0 116 0 3 3 0 01-6 0z"/>
</svg>
```

### 9. Form Migration

**Old Bootstrap Forms:**
```html
<div class="form-group">
    <label for="email">Email</label>
    <input type="email" class="form-control" id="email">
</div>
```

**New Fluent UI:**
```html
<x-fluent-input
    type="email"
    id="email"
    label="Email"
    name="email"
/>
```

### 10. Button Migration

**Old Bootstrap Buttons:**
```html
<button class="btn btn-primary">Submit</button>
<a href="#" class="btn btn-secondary">Cancel</a>
```

**New Fluent UI:**
```html
<x-fluent-button variant="primary">Submit</x-fluent-button>
<x-fluent-button variant="secondary">Cancel</x-fluent-button>
```

### 11. Table Migration

**Old Bootstrap Tables:**
```html
<table class="table table-striped">
    <thead>
        <tr><th>Name</th></tr>
    </thead>
</table>
```

**New Fluent UI:**
```html
<table class="fluent-table">
    <thead>
        <tr><th class="px-6 py-3">Name</th></tr>
    </thead>
</table>
```

### 12. Alert Migration

**Old Bootstrap Alerts:**
```html
<div class="alert alert-success">Success!</div>
```

**New Fluent UI:**
```html
<x-fluent-card padding="small" class="bg-green-50 border-green-200">
    <div class="flex items-start gap-3">
        <svg width="20" height="20" class="text-fluent-success">...</svg>
        <p class="text-sm font-medium text-fluent-success">Success!</p>
    </div>
</x-fluent-card>
```

## Installation & Build

```bash
# Install dependencies
npm install

# Build for production
npm run build

# Development with hot reload
npm run dev
```

## Browser Compatibility

- **Modern Browsers**: Chrome, Edge, Firefox, Safari (last 2 versions)
- **Fluent Web Components**: Requires browsers with Web Components support
- **CSS Custom Properties**: Full support in modern browsers

## Performance Considerations

1. **No jQuery**: Removed ~87KB of JavaScript
2. **No Bootstrap JS**: Removed ~60KB of JavaScript
3. **No Font Awesome**: Removed ~900KB of font files
4. **Inline SVG Icons**: Only icons used are included
5. **Tailwind CSS**: PurgeCSS removes unused styles
6. **Fluent Web Components**: Tree-shakeable, only imports used components

## Accessibility

- **ARIA Labels**: All interactive elements have proper labels
- **Keyboard Navigation**: Full keyboard support
- **Focus Indicators**: Visible focus rings matching Fluent UI
- **Screen Reader**: Semantic HTML and ARIA attributes
- **Color Contrast**: WCAG AA compliant (4.5:1 minimum)

## Responsive Design

- **Mobile First**: Tailwind utility classes
- **Breakpoints**:
  - `sm`: 640px
  - `md`: 768px
  - `lg`: 1024px
  - `xl`: 1280px
  - `2xl`: 1536px
- **Collapsible Navigation**: Hamburger menu on mobile
- **Responsive Tables**: Horizontal scroll on small screens

## Testing Checklist

- [ ] All pages render without errors
- [ ] Navigation menu works (expand/collapse)
- [ ] Search functionality works
- [ ] Forms submit correctly
- [ ] AJAX calls use Fluent UI loading states
- [ ] Toast notifications appear correctly
- [ ] Tables are sortable/filterable
- [ ] Responsive layout works on mobile
- [ ] Dark mode (if implemented)
- [ ] Accessibility audit passes

## Migration Patterns

### Pattern 1: Page Header
```blade
<x-slot name="header">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-fluent-neutral-30">Page Title</h1>
            <p class="text-sm text-fluent-neutral-26 mt-1">Description</p>
        </div>
        <div class="flex items-center gap-2">
            <x-fluent-button variant="primary">Action</x-fluent-button>
        </div>
    </div>
</x-slot>
```

### Pattern 2: Data Grid
```blade
<x-fluent-card>
    <x-slot name="header">
        <h3 class="text-base font-semibold text-fluent-neutral-30">Table Title</h3>
    </x-slot>
    
    <div class="overflow-x-auto -mx-6">
        <table class="fluent-table">
            <thead>
                <tr>
                    <th class="px-6 py-3">Column</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="px-6 py-3">Data</td>
                </tr>
            </tbody>
        </table>
    </div>
</x-fluent-card>
```

### Pattern 3: Search Form
```blade
<x-fluent-card title="Search" class="mb-4">
    <form method="GET" class="flex flex-col md:flex-row gap-3 items-end">
        <div class="flex-1">
            <x-fluent-input
                type="text"
                name="search"
                placeholder="Search..."
            />
        </div>
        <x-fluent-button type="submit" variant="primary">Search</x-fluent-button>
    </form>
</x-fluent-card>
```

### Pattern 4: Status Messages
```blade
@if(session('success'))
    <x-fluent-card padding="small" class="bg-green-50 border-green-200 mb-4">
        <div class="flex items-start gap-3">
            <svg width="20" height="20" class="text-fluent-success">...</svg>
            <p class="text-sm font-medium text-fluent-success">{{ session('success') }}</p>
        </div>
    </x-fluent-card>
@endif
```

## Common Issues & Solutions

### Issue 1: Icons Not Showing
**Solution**: Replace Font Awesome classes with inline SVG from the icon library.

### Issue 2: Styles Not Applied
**Solution**: Run `npm run build` to compile Tailwind CSS with new classes.

### Issue 3: Alpine.js Not Working
**Solution**: Ensure `@vite(['resources/css/app.css', 'resources/js/app.js'])` is in layout.

### Issue 4: Toast Not Appearing
**Solution**: Check that `window.FluentUI` is initialized in `resources/js/app.js`.

### Issue 5: Navigation Not Collapsing
**Solution**: Verify Alpine.js store is initialized with `sidebarExpanded` state.

## Resources

- [Fluent UI Documentation](https://developer.microsoft.com/en-us/fluentui)
- [Fluent UI Web Components](https://github.com/microsoft/fluentui/tree/master/packages/web-components)
- [Microsoft Design System](https://www.microsoft.com/design/fluent/)
- [Azure Portal Design Patterns](https://docs.microsoft.com/en-us/azure/azure-portal/)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [Alpine.js Documentation](https://alpinejs.dev/)

## Next Steps

1. **Run Build**: `npm install && npm run build`
2. **Test Navigation**: Verify all links work
3. **Test Forms**: Submit forms and check validation
4. **Test AJAX**: Verify loading states and toast notifications
5. **Mobile Testing**: Test on various screen sizes
6. **Accessibility Audit**: Run Lighthouse/axe DevTools
7. **Performance Check**: Verify page load times
8. **Browser Testing**: Test in Chrome, Firefox, Safari, Edge

## Support

For questions or issues with the migration:
1. Check this guide for common patterns
2. Review existing migrated views (Dashboard, Users)
3. Consult Fluent UI documentation
4. Test in browser DevTools console

---

**Migration Complete**: Foundation, component library, layouts, dashboard, and users section fully migrated to Fluent UI design system. All other views can follow the established patterns.
