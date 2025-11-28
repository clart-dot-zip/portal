# Quick Command Reference

## 🚀 After Pulling Changes (Copy & Run)

```bash
cd c:\All\GitHub\portal ; npm install ; npm run build ; php artisan view:clear ; php artisan cache:clear
```

## 🔄 Common Commands

### Build Assets
```bash
npm run build          # Production build
npm run dev            # Development with hot reload
```

### Clear Caches
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

### Start Server
```bash
php artisan serve      # http://localhost:8000
```

## 🐛 If Something Breaks

```bash
# Nuclear option - fixes most issues
rm -rf node_modules public/build
npm install
npm run build
php artisan cache:clear
php artisan view:clear
```

## 💡 Development Workflow

### Terminal 1 (Hot Reload)
```bash
npm run dev
```

### Terminal 2 (Laravel Server)
```bash
php artisan serve
```

Then visit: http://localhost:8000

---

See [SETUP.md](SETUP.md) for detailed documentation.
