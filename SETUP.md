# Portal Setup Guide

## 🚀 Quick Start (After Pulling Changes)

Run these commands in order:

### 1. Install Node Dependencies
```bash
cd c:\All\GitHub\portal
npm install
```

### 2. Install PHP Dependencies (if needed)
```bash
composer install
```

### 3. Build Frontend Assets
```bash
# For production build
npm run build

# OR for development with hot reload
npm run dev
```

### 4. Clear Laravel Caches
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

### 5. Run Migrations (if needed)
```bash
php artisan migrate
```

### 6. Start Development Server
```bash
php artisan serve
```

Your application should now be running at `http://localhost:8000`

---

## 📦 Complete Setup Commands (One-Liner)

Copy and paste this entire block:

```bash
cd c:\All\GitHub\portal ; npm install ; composer install ; npm run build ; php artisan cache:clear ; php artisan config:clear ; php artisan view:clear ; php artisan route:clear
```

---

## 🔧 Development Workflow

### Running with Hot Module Replacement (Recommended)
```bash
# Terminal 1: Start Vite dev server (hot reload)
npm run dev

# Terminal 2: Start Laravel server
php artisan serve
```

Then visit `http://localhost:8000` - your CSS/JS changes will auto-reload!

### Building for Production
```bash
npm run build
php artisan optimize
```

---

## 🐛 Troubleshooting

### Issue: "Unable to locate file in Vite manifest"
**Solution:**
```bash
# Delete old build files
rm -rf public/build

# Rebuild assets
npm run build
```

### Issue: Styles not loading
**Solution:**
```bash
# Clear all caches
php artisan cache:clear
php artisan view:clear

# Rebuild assets
npm run build

# Hard refresh browser (Ctrl+Shift+R)
```

### Issue: "Module not found" errors
**Solution:**
```bash
# Delete node_modules and reinstall
rm -rf node_modules
rm package-lock.json
npm install
npm run build
```

### Issue: Database connection errors
**Solution:**
```bash
# Check your .env file
# Make sure database credentials are correct

# Run migrations
php artisan migrate
```

---

## 📋 Package.json Scripts

| Command | Description |
|---------|-------------|
| `npm run dev` | Start Vite dev server with hot reload |
| `npm run build` | Build for production (minified) |

---

## 🔑 Environment Setup

### First Time Setup

1. **Copy environment file:**
```bash
cp .env.example .env
```

2. **Generate application key:**
```bash
php artisan key:generate
```

3. **Configure database in `.env`:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

4. **Configure Authentik API in `.env`:**
```env
AUTHENTIK_BASE_URL=https://your-authentik-instance.com
AUTHENTIK_API_TOKEN=your_api_token_here
```

5. **Run setup commands:**
```bash
composer install
npm install
php artisan migrate
npm run build
```

---

## 📁 Important Files

| File | Purpose |
|------|---------|
| `package.json` | Node dependencies (Fluent UI, Tailwind, etc.) |
| `vite.config.js` | Vite build configuration |
| `tailwind.config.js` | Tailwind + Fluent UI design tokens |
| `resources/css/app.css` | Main CSS entry point |
| `resources/css/dashboard.css` | Dashboard-specific styles |
| `resources/js/app.js` | Main JavaScript entry point |

---

## 🎨 Fluent UI Dependencies

These packages are now installed:

- `@fluentui/web-components@^2.6.0` - Fluent UI components
- `@fluentui/tokens@^1.0.0-alpha.22` - Design tokens (fixed version)
- `@fluentui/svg-icons@^1.1.231` - Icon library

---

## 🔄 Update Workflow

When pulling new changes from git:

```bash
# Pull changes
git pull origin main

# Install any new dependencies
composer install
npm install

# Rebuild assets
npm run build

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Run any new migrations
php artisan migrate

# If needed, restart server
php artisan serve
```

---

## ⚡ Performance Tips

### Production Optimization
```bash
# Build optimized assets
npm run build

# Cache config and routes
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev
```

### Development Speed
```bash
# Use Vite dev server for instant updates
npm run dev

# Keep Laravel running in separate terminal
php artisan serve
```

---

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=TestName

# Run tests with coverage
php artisan test --coverage
```

---

## 📊 Build Output

After running `npm run build`, you should see:

```
✓ 42 modules transformed.
public/build/manifest.json           x.xx kB
public/build/assets/app-xxxxx.css    xx.xx kB │ gzip: x.xx kB
public/build/assets/app-xxxxx.js     xx.xx kB │ gzip: x.xx kB
✓ built in xxxms
```

---

## 🆘 Common Commands Reference

```bash
# Laravel
php artisan serve              # Start development server
php artisan migrate            # Run database migrations
php artisan migrate:fresh      # Fresh database (destructive!)
php artisan cache:clear        # Clear application cache
php artisan config:clear       # Clear config cache
php artisan view:clear         # Clear compiled views
php artisan route:clear        # Clear route cache
php artisan optimize           # Optimize for production
php artisan optimize:clear     # Clear optimization caches

# NPM
npm install                    # Install dependencies
npm run dev                    # Development mode (hot reload)
npm run build                  # Production build
npm update                     # Update packages

# Composer
composer install               # Install PHP dependencies
composer update                # Update PHP dependencies
composer dump-autoload         # Regenerate autoloader
```

---

## 💡 Tips

1. **Always run `npm run build`** after pulling changes
2. **Use `npm run dev`** during development for instant updates
3. **Clear caches** if you see unexpected behavior
4. **Hard refresh browser** (Ctrl+Shift+R) after rebuilding assets
5. **Check `.env` file** for correct credentials
6. **Keep terminal open** with `npm run dev` running

---

## 🔗 Useful Links

- [Laravel Documentation](https://laravel.com/docs)
- [Vite Documentation](https://vitejs.dev/)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [Fluent UI Documentation](https://developer.microsoft.com/en-us/fluentui)
- [Alpine.js Documentation](https://alpinejs.dev/)

---

## ✅ Verification Checklist

After setup, verify everything works:

- [ ] Run `npm run build` without errors
- [ ] Visit `http://localhost:8000` - homepage loads
- [ ] Login page displays correctly
- [ ] Dashboard shows with Fluent UI styling
- [ ] Navigation menu works (collapse/expand)
- [ ] No console errors in browser DevTools
- [ ] Assets load correctly (check Network tab)

---

**Need Help?** Check the troubleshooting section above or review the error messages in terminal/browser console.
