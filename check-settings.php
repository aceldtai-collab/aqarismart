<?php
// ملف فحص مؤقت - شغله من المتصفح
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$tenant = \App\Models\Tenant::first();

if (!$tenant) {
    die('No tenant found');
}

echo "<h1>Tenant Settings Check</h1>";
echo "<h2>Tenant: {$tenant->name}</h2>";
echo "<h3>Settings:</h3>";
echo "<pre>";
print_r($tenant->settings);
echo "</pre>";

echo "<h3>Colors:</h3>";
echo "<ul>";
echo "<li>Primary Color: " . ($tenant->settings['primary_color'] ?? 'NOT SET') . "</li>";
echo "<li>Accent Color: " . ($tenant->settings['accent_color'] ?? 'NOT SET') . "</li>";
echo "<li>Font Color: " . ($tenant->settings['font_color'] ?? 'NOT SET') . "</li>";
echo "</ul>";

echo "<h3>CSS Variables that should be generated:</h3>";
echo "<pre>";
echo "--brand: " . ($tenant->settings['primary_color'] ?? '#3b82f6') . ";\n";
echo "--accent: " . ($tenant->settings['accent_color'] ?? '#06b6d4') . ";\n";
echo "--font-color: " . ($tenant->settings['font_color'] ?? '#0b3849') . ";\n";
echo "</pre>";
