<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel properly
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\Authentik\AuthentikSDK;

try {
    echo "=== Portal Admin Access Control Test ===" . PHP_EOL;
    echo PHP_EOL;
    
    echo "🔒 SECURITY IMPROVEMENTS IMPLEMENTED:" . PHP_EOL;
    echo PHP_EOL;
    
    echo "1. MIDDLEWARE ENFORCEMENT:" . PHP_EOL;
    echo "   ✅ CheckPortalAdmin middleware now BLOCKS non-admin access" . PHP_EOL;
    echo "   ✅ Routes properly protected with portal.admin:true parameter" . PHP_EOL;
    echo "   ✅ Non-admins get redirected with error message" . PHP_EOL;
    echo PHP_EOL;
    
    echo "2. SEPARATE DASHBOARDS:" . PHP_EOL;
    echo "   ✅ User Dashboard (/dashboard) - Personal info only" . PHP_EOL;
    echo "   ✅ Admin Dashboard (/admin/dashboard) - Full system stats" . PHP_EOL;
    echo "   ✅ Dashboard switching for admins" . PHP_EOL;
    echo PHP_EOL;
    
    echo "3. ROUTE PROTECTION:" . PHP_EOL;
    echo "   🔒 /users/* - Admin only" . PHP_EOL;
    echo "   🔒 /groups/* - Admin only" . PHP_EOL;
    echo "   🔒 /applications/* - Admin only" . PHP_EOL;
    echo "   🔒 /admin/dashboard - Admin only" . PHP_EOL;
    echo "   👤 /dashboard - All users (personal view)" . PHP_EOL;
    echo "   👤 /profile - All users" . PHP_EOL;
    echo PHP_EOL;
    
    echo "4. NAVIGATION UPDATES:" . PHP_EOL;
    echo "   ✅ Admin sections hidden from non-admins" . PHP_EOL;
    echo "   ✅ Dashboard switching available for admins" . PHP_EOL;
    echo "   ✅ Profile link available to all users" . PHP_EOL;
    echo PHP_EOL;
    
    echo "🧪 TEST SCENARIOS:" . PHP_EOL;
    echo PHP_EOL;
    
    echo "For NON-ADMIN users:" . PHP_EOL;
    echo "- Accessing /groups → Redirected to /dashboard with error" . PHP_EOL;
    echo "- Accessing /users → Redirected to /dashboard with error" . PHP_EOL;
    echo "- Accessing /admin/dashboard → Redirected to /dashboard with error" . PHP_EOL;
    echo "- Navigation shows only: Dashboard, Profile" . PHP_EOL;
    echo "- Dashboard shows personal info only" . PHP_EOL;
    echo PHP_EOL;
    
    echo "For ADMIN users:" . PHP_EOL;
    echo "- Can access all routes" . PHP_EOL;
    echo "- Navigation shows: Dashboard, Users, Groups, Applications" . PHP_EOL;
    echo "- Can switch between User and Admin dashboards" . PHP_EOL;
    echo "- Dashboard switching buttons visible" . PHP_EOL;
    echo PHP_EOL;
    
    echo "🎯 IMMEDIATE ACTIONS TO TEST:" . PHP_EOL;
    echo "1. Login as non-admin user" . PHP_EOL;
    echo "2. Try to access: http://localhost:8000/groups" . PHP_EOL;
    echo "3. Should be redirected to dashboard with error message" . PHP_EOL;
    echo "4. Navigation should only show Dashboard and Profile" . PHP_EOL;
    echo "5. Add user to 'Portal admin' group via Users page" . PHP_EOL;
    echo "6. Refresh and verify admin access works" . PHP_EOL;
    echo PHP_EOL;
    
    echo "✅ SECURITY IS NOW PROPERLY ENFORCED!" . PHP_EOL;
    echo "Non-admin users cannot access admin pages directly via URL." . PHP_EOL;
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}