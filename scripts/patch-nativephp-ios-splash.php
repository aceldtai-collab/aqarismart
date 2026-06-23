<?php

declare(strict_types=1);

$basePath = dirname(__DIR__);
$target = $basePath.'/vendor/nativephp/mobile/src/Traits/InstallsSplashScreen.php';

if (! is_file($target)) {
    fwrite(STDOUT, "NativePHP mobile package not installed; skipping iOS splash patch.\n");
    exit(0);
}

$contents = file_get_contents($target);
if ($contents === false) {
    fwrite(STDERR, "Unable to read {$target}.\n");
    exit(1);
}

if (str_contains($contents, "'splash@1x.png' => ['filename' => 'splash@1x.png', 'idiom' => 'universal', 'scale' => '1x']")) {
    fwrite(STDOUT, "NativePHP iOS splash patch already applied.\n");
    exit(0);
}

$old = <<<'PHP'
        $splashVariants = [
            'splash.png' => ['filename' => 'splash.png', 'idiom' => 'universal'],
            'splash-dark.png' => ['filename' => 'splash-dark.png', 'idiom' => 'universal', 'appearances' => [['appearance' => 'luminosity', 'value' => 'dark']]],
            'splash@2x.png' => ['filename' => 'splash@2x.png', 'idiom' => 'universal', 'scale' => '2x'],
            'splash-dark@2x.png' => ['filename' => 'splash-dark@2x.png', 'idiom' => 'universal', 'scale' => '2x', 'appearances' => [['appearance' => 'luminosity', 'value' => 'dark']]],
            'splash@3x.png' => ['filename' => 'splash@3x.png', 'idiom' => 'universal', 'scale' => '3x'],
            'splash-dark@3x.png' => ['filename' => 'splash-dark@3x.png', 'idiom' => 'universal', 'scale' => '3x', 'appearances' => [['appearance' => 'luminosity', 'value' => 'dark']]],
        ];
PHP;

$new = <<<'PHP'
        $splashVariants = [
            'splash@1x.png' => ['filename' => 'splash@1x.png', 'idiom' => 'universal', 'scale' => '1x'],
            'splash-dark@1x.png' => ['filename' => 'splash-dark@1x.png', 'idiom' => 'universal', 'scale' => '1x', 'appearances' => [['appearance' => 'luminosity', 'value' => 'dark']]],
            'splash@2x.png' => ['filename' => 'splash@2x.png', 'idiom' => 'universal', 'scale' => '2x'],
            'splash-dark@2x.png' => ['filename' => 'splash-dark@2x.png', 'idiom' => 'universal', 'scale' => '2x', 'appearances' => [['appearance' => 'luminosity', 'value' => 'dark']]],
            'splash@3x.png' => ['filename' => 'splash@3x.png', 'idiom' => 'universal', 'scale' => '3x'],
            'splash-dark@3x.png' => ['filename' => 'splash-dark@3x.png', 'idiom' => 'universal', 'scale' => '3x', 'appearances' => [['appearance' => 'luminosity', 'value' => 'dark']]],
        ];
PHP;

$patched = str_replace($old, $new, $contents, $count);
if ($count !== 1) {
    fwrite(STDERR, "NativePHP iOS splash patch did not match the installed package.\n");
    exit(1);
}

if (file_put_contents($target, $patched) === false) {
    fwrite(STDERR, "Unable to write {$target}.\n");
    exit(1);
}

fwrite(STDOUT, "Patched NativePHP iOS splash variants to use 1x/2x/3x assets only.\n");
