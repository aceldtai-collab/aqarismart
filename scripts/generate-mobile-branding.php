<?php

declare(strict_types=1);

$basePath = dirname(__DIR__);
$publicPath = $basePath.'/public';

$sourceCandidates = [
    $basePath.'/resources/branding/startup-screen.jpg',
    'C:/Users/aalothman/Downloads/Startup screen.jpeg',
];

$sourcePath = null;
foreach ($sourceCandidates as $candidate) {
    if (is_file($candidate)) {
        $sourcePath = $candidate;
        break;
    }
}

if ($sourcePath === null) {
    fwrite(STDERR, "Source image not found.\n");
    exit(1);
}

if (! extension_loaded('gd')) {
    fwrite(STDERR, "GD extension is required.\n");
    exit(1);
}

if (! is_dir($basePath.'/resources/branding')) {
    mkdir($basePath.'/resources/branding', 0777, true);
}

if ($sourcePath !== $basePath.'/resources/branding/startup-screen.jpg') {
    copy($sourcePath, $basePath.'/resources/branding/startup-screen.jpg');
    $sourcePath = $basePath.'/resources/branding/startup-screen.jpg';
}

if (! is_dir($publicPath)) {
    mkdir($publicPath, 0777, true);
}

$source = imagecreatefromjpeg($sourcePath);
if (! $source) {
    fwrite(STDERR, "Failed to load source JPEG.\n");
    exit(1);
}

$bounds = findContentBounds($source, 246);
$cropped = cropWithPadding($source, $bounds, 52);
$logoTransparent = makeWhiteTransparent($cropped, 246, 232);
$logoSoft = makeWhiteTransparent($cropped, 250, 240);

$icon = buildIcon($logoTransparent);
savePng($icon, $publicPath.'/icon.png');

$lightSplash = buildSplash($logoTransparent, false);
$darkSplash = buildSplash($logoSoft, true);

$lightTargets = [
    $publicPath.'/splash.png',
    $publicPath.'/splash@2x.png',
    $publicPath.'/splash@3x.png',
];

$darkTargets = [
    $publicPath.'/splash-dark.png',
    $publicPath.'/splash-dark@2x.png',
    $publicPath.'/splash-dark@3x.png',
];

foreach ($lightTargets as $target) {
    savePng($lightSplash, $target);
}

foreach ($darkTargets as $target) {
    savePng($darkSplash, $target);
}

imagedestroy($source);
imagedestroy($cropped);
imagedestroy($logoTransparent);
imagedestroy($logoSoft);
imagedestroy($icon);
imagedestroy($lightSplash);
imagedestroy($darkSplash);

echo "Generated public/icon.png and splash assets.\n";

function buildIcon(GdImage $logo): GdImage
{
    $canvas = imagecreatetruecolor(1024, 1024);
    imagealphablending($canvas, true);
    imagesavealpha($canvas, false);

    fillVerticalGradient($canvas, '#faf3e6', '#e6d4b3');
    drawGlow($canvas, 512, 356, 292, '#c39a53', 96, 127, 6);
    drawDiamondBand($canvas, 512, 96, 5, 18, '#b6842f', 78, 14);
    drawArchPanel($canvas, 166, 118, 858, 810, 180, '#f8f1e4', 4, '#d9c29a', 54, '#8b7445', 120);
    drawArcAccent($canvas, 512, 824, 520, 128, '#0f5a46', 118, 18, 162);

    $shadow = imagecolorallocatealpha($canvas, 22, 28, 23, 116);
    imagefilledellipse($canvas, 512, 742, 360, 56, $shadow);

    placeImageInBox($canvas, $logo, 176, 170, 672, 672);

    drawCompactFlagBadge($canvas, 378, 884, 268, 50, false);
    drawFrame($canvas, 1024, 1024, '#d7c4a0', 8, 70);

    return $canvas;
}

function buildSplash(GdImage $logo, bool $dark): GdImage
{
    $width = 1284;
    $height = 2778;
    $canvas = imagecreatetruecolor($width, $height);
    imagealphablending($canvas, true);
    imagesavealpha($canvas, false);

    if ($dark) {
        fillVerticalGradient($canvas, '#0a2018', '#17372c');
        drawGlow($canvas, 642, 900, 430, '#c7a15b', 102, 127, 8);
        drawDiamondBand($canvas, 642, 238, 7, 22, '#d3b372', 72, 18);
        drawArchPanel($canvas, 174, 338, 1110, 1576, 220, '#f2e8d5', 0, '#ffffff', 82, '#08150f', 118);
        drawPalmSpray($canvas, 126, 2170, 228, '#2f7a72', 114, false);
        drawPalmSpray($canvas, 1158, 2170, 228, '#2f7a72', 114, true);
        imagefilledellipse($canvas, 642, 1602, 620, 96, imagecolorallocatealpha($canvas, 8, 18, 14, 114));
        drawArcAccent($canvas, 642, 2298, 540, 132, '#b6842f', 114, 18, 162);
        placeImageInBox($canvas, $logo, 222, 470, 840, 840);
        drawCompactFlagBadge($canvas, 492, 2294, 300, 58, true);
        drawFrame($canvas, $width, $height, '#27503f', 10, 95);
    } else {
        fillVerticalGradient($canvas, '#fbf4e7', '#e6d3b0');
        drawGlow($canvas, 642, 956, 446, '#caa760', 96, 127, 8);
        drawDiamondBand($canvas, 642, 240, 7, 22, '#b6842f', 82, 18);
        drawArchPanel($canvas, 168, 360, 1116, 1612, 220, '#f9f2e6', 0, '#d9c39d', 68, '#8d7546', 116);
        drawPalmSpray($canvas, 130, 2190, 236, '#94a57b', 102, false);
        drawPalmSpray($canvas, 1154, 2190, 236, '#94a57b', 102, true);
        drawArcAccent($canvas, 642, 1714, 520, 124, '#0f5a46', 118, 18, 162);
        imagefilledellipse($canvas, 642, 1636, 382, 58, imagecolorallocatealpha($canvas, 48, 60, 45, 118));
        placeImageInBox($canvas, $logo, 222, 510, 840, 840);
        drawCompactFlagBadge($canvas, 492, 2312, 300, 58, false);
        drawFrame($canvas, $width, $height, '#d4c09a', 10, 85);
    }

    return $canvas;
}

function savePng(GdImage $image, string $path): void
{
    imagepng($image, $path, 9);
}

function findContentBounds(GdImage $image, int $threshold): array
{
    $width = imagesx($image);
    $height = imagesy($image);

    $minX = $width - 1;
    $minY = $height - 1;
    $maxX = 0;
    $maxY = 0;

    for ($y = 0; $y < $height; $y++) {
        for ($x = 0; $x < $width; $x++) {
            $rgba = imagecolorat($image, $x, $y);
            $r = ($rgba >> 16) & 0xFF;
            $g = ($rgba >> 8) & 0xFF;
            $b = $rgba & 0xFF;

            if ($r < $threshold || $g < $threshold || $b < $threshold) {
                $minX = min($minX, $x);
                $minY = min($minY, $y);
                $maxX = max($maxX, $x);
                $maxY = max($maxY, $y);
            }
        }
    }

    return [$minX, $minY, $maxX, $maxY];
}

function cropWithPadding(GdImage $image, array $bounds, int $padding): GdImage
{
    [$minX, $minY, $maxX, $maxY] = $bounds;

    $srcWidth = imagesx($image);
    $srcHeight = imagesy($image);

    $left = max(0, $minX - $padding);
    $top = max(0, $minY - $padding);
    $right = min($srcWidth - 1, $maxX + $padding);
    $bottom = min($srcHeight - 1, $maxY + $padding);

    $cropWidth = $right - $left + 1;
    $cropHeight = $bottom - $top + 1;

    $cropped = imagecreatetruecolor($cropWidth, $cropHeight);
    imagealphablending($cropped, false);
    imagesavealpha($cropped, true);
    $transparent = imagecolorallocatealpha($cropped, 0, 0, 0, 127);
    imagefill($cropped, 0, 0, $transparent);

    imagecopy($cropped, $image, 0, 0, $left, $top, $cropWidth, $cropHeight);

    return $cropped;
}

function makeWhiteTransparent(GdImage $image, int $threshold, int $softStart): GdImage
{
    $width = imagesx($image);
    $height = imagesy($image);
    $output = imagecreatetruecolor($width, $height);
    imagealphablending($output, false);
    imagesavealpha($output, true);
    $transparent = imagecolorallocatealpha($output, 0, 0, 0, 127);
    imagefill($output, 0, 0, $transparent);

    for ($y = 0; $y < $height; $y++) {
        for ($x = 0; $x < $width; $x++) {
            $rgba = imagecolorat($image, $x, $y);
            $r = ($rgba >> 16) & 0xFF;
            $g = ($rgba >> 8) & 0xFF;
            $b = $rgba & 0xFF;

            $min = min($r, $g, $b);
            $alpha = 0;

            if ($min >= $threshold) {
                $alpha = 127;
            } elseif ($min >= $softStart) {
                $ratio = ($min - $softStart) / max(1, $threshold - $softStart);
                $alpha = (int) round(127 * $ratio);
            }

            $color = imagecolorallocatealpha($output, $r, $g, $b, max(0, min(127, $alpha)));
            imagesetpixel($output, $x, $y, $color);
        }
    }

    return $output;
}

function fillVerticalGradient(GdImage $image, string $topHex, string $bottomHex): void
{
    [$r1, $g1, $b1] = hexToRgb($topHex);
    [$r2, $g2, $b2] = hexToRgb($bottomHex);

    $width = imagesx($image);
    $height = imagesy($image);

    for ($y = 0; $y < $height; $y++) {
        $ratio = $height > 1 ? $y / ($height - 1) : 0;
        $r = (int) round($r1 + (($r2 - $r1) * $ratio));
        $g = (int) round($g1 + (($g2 - $g1) * $ratio));
        $b = (int) round($b1 + (($b2 - $b1) * $ratio));
        $color = imagecolorallocate($image, $r, $g, $b);
        imageline($image, 0, $y, $width, $y, $color);
    }
}

function drawGlow(
    GdImage $image,
    int $centerX,
    int $centerY,
    int $radius,
    string $hex,
    int $startAlpha,
    int $endAlpha,
    int $step
): void {
    [$r, $g, $b] = hexToRgb($hex);

    for ($size = $radius; $size > 0; $size -= $step) {
        $ratio = $radius > 0 ? $size / $radius : 0;
        $alpha = (int) round($endAlpha + (($startAlpha - $endAlpha) * $ratio));
        $color = imagecolorallocatealpha($image, $r, $g, $b, max(0, min(127, $alpha)));
        imagefilledellipse($image, $centerX, $centerY, $size * 2, $size * 2, $color);
    }
}

function drawArcAccent(GdImage $image, int $centerX, int $centerY, int $width, int $height, string $hex, int $alpha, int $start, int $end): void
{
    [$r, $g, $b] = hexToRgb($hex);
    imagesetthickness($image, 22);
    $color = imagecolorallocatealpha($image, $r, $g, $b, $alpha);
    imagearc($image, $centerX, $centerY, $width, $height, $start, $end, $color);
    imagesetthickness($image, 1);
}

function drawDiamondBand(GdImage $image, int $centerX, int $centerY, int $count, int $size, string $hex, int $alpha, int $gap): void
{
    $startX = (int) round($centerX - ((($count - 1) * ($size + $gap)) / 2));

    for ($index = 0; $index < $count; $index++) {
        $x = $startX + ($index * ($size + $gap));
        drawDiamond($image, $x, $centerY, $size, $hex, $alpha);
    }
}

function drawDiamond(GdImage $image, int $centerX, int $centerY, int $size, string $hex, int $alpha): void
{
    $color = allocateHex($image, $hex, $alpha);
    imagefilledpolygon($image, [
        $centerX, $centerY - $size,
        $centerX + $size, $centerY,
        $centerX, $centerY + $size,
        $centerX - $size, $centerY,
    ], $color);
}

function drawArchPanel(
    GdImage $image,
    int $x1,
    int $y1,
    int $x2,
    int $y2,
    int $archHeight,
    string $fillHex,
    int $fillAlpha,
    string $strokeHex,
    int $strokeAlpha,
    string $shadowHex,
    int $shadowAlpha
): void {
    drawArchShape($image, $x1 + 18, $y1 + 26, $x2 + 18, $y2 + 26, $archHeight, $shadowHex, $shadowAlpha, false);
    drawArchShape($image, $x1, $y1, $x2, $y2, $archHeight, $fillHex, $fillAlpha, true);
    drawArchOutline($image, $x1, $y1, $x2, $y2, $archHeight, $strokeHex, $strokeAlpha);
}

function drawArchShape(
    GdImage $image,
    int $x1,
    int $y1,
    int $x2,
    int $y2,
    int $archHeight,
    string $hex,
    int $alpha,
    bool $fill
): void {
    $color = allocateHex($image, $hex, $alpha);
    $centerX = (int) round(($x1 + $x2) / 2);
    $width = $x2 - $x1;
    $ellipseHeight = $archHeight * 2;
    $bodyTop = $y1 + (int) round($archHeight * .72);

    if ($fill) {
        imagefilledellipse($image, $centerX, $y1 + $archHeight, $width, $ellipseHeight, $color);
        fillRoundedRect($image, $x1, $bodyTop, $x2, $y2, 64, $hex, $alpha);
    } else {
        imageellipse($image, $centerX, $y1 + $archHeight, $width, $ellipseHeight, $color);
    }
}

function drawArchOutline(
    GdImage $image,
    int $x1,
    int $y1,
    int $x2,
    int $y2,
    int $archHeight,
    string $hex,
    int $alpha
): void {
    $color = allocateHex($image, $hex, $alpha);
    $centerX = (int) round(($x1 + $x2) / 2);
    $width = $x2 - $x1;
    $ellipseHeight = $archHeight * 2;
    $bodyTop = $y1 + (int) round($archHeight * .72);

    imagesetthickness($image, 4);
    imagearc($image, $centerX, $y1 + $archHeight, $width, $ellipseHeight, 180, 360, $color);
    imageline($image, $x1, $bodyTop, $x1, $y2 - 64, $color);
    imageline($image, $x2, $bodyTop, $x2, $y2 - 64, $color);
    imageline($image, $x1 + 64, $y2, $x2 - 64, $y2, $color);
    imagearc($image, $x1 + 64, $y2 - 64, 128, 128, 90, 180, $color);
    imagearc($image, $x2 - 64, $y2 - 64, 128, 128, 0, 90, $color);
    imagesetthickness($image, 1);
}

function drawPalmSpray(GdImage $image, int $baseX, int $baseY, int $height, string $hex, int $alpha, bool $flip): void
{
    $color = allocateHex($image, $hex, $alpha);
    $direction = $flip ? -1 : 1;
    imagesetthickness($image, 6);
    imageline($image, $baseX, $baseY, $baseX + ($direction * 26), $baseY - $height, $color);

    $tipX = $baseX + ($direction * 26);
    $tipY = $baseY - $height;
    $leafSets = [
        [-116, -46],
        [-84, -88],
        [-48, -126],
        [8, -136],
        [54, -112],
        [92, -74],
    ];

    foreach ($leafSets as [$dx, $dy]) {
        imageline($image, $tipX, $tipY, $tipX + ($direction * $dx), $tipY + $dy, $color);
    }

    imagesetthickness($image, 1);
}

function drawRoundedPanel(
    GdImage $image,
    int $x1,
    int $y1,
    int $x2,
    int $y2,
    int $radius,
    string $fillHex,
    int $fillAlpha,
    string $strokeHex,
    int $strokeAlpha
): void {
    fillRoundedRect($image, $x1 + 20, $y1 + 26, $x2 + 20, $y2 + 26, $radius, '#000000', 118);
    fillRoundedRect($image, $x1, $y1, $x2, $y2, $radius, $fillHex, $fillAlpha);
    strokeRoundedRect($image, $x1, $y1, $x2, $y2, $radius, $strokeHex, $strokeAlpha);
}

function fillRoundedRect(GdImage $image, int $x1, int $y1, int $x2, int $y2, int $radius, string $hex, int $alpha): void
{
    $color = allocateHex($image, $hex, $alpha);

    imagefilledrectangle($image, $x1 + $radius, $y1, $x2 - $radius, $y2, $color);
    imagefilledrectangle($image, $x1, $y1 + $radius, $x2, $y2 - $radius, $color);
    imagefilledellipse($image, $x1 + $radius, $y1 + $radius, $radius * 2, $radius * 2, $color);
    imagefilledellipse($image, $x2 - $radius, $y1 + $radius, $radius * 2, $radius * 2, $color);
    imagefilledellipse($image, $x1 + $radius, $y2 - $radius, $radius * 2, $radius * 2, $color);
    imagefilledellipse($image, $x2 - $radius, $y2 - $radius, $radius * 2, $radius * 2, $color);
}

function strokeRoundedRect(GdImage $image, int $x1, int $y1, int $x2, int $y2, int $radius, string $hex, int $alpha): void
{
    $color = allocateHex($image, $hex, $alpha);
    imagesetthickness($image, 4);

    imageline($image, $x1 + $radius, $y1, $x2 - $radius, $y1, $color);
    imageline($image, $x1 + $radius, $y2, $x2 - $radius, $y2, $color);
    imageline($image, $x1, $y1 + $radius, $x1, $y2 - $radius, $color);
    imageline($image, $x2, $y1 + $radius, $x2, $y2 - $radius, $color);
    imagearc($image, $x1 + $radius, $y1 + $radius, $radius * 2, $radius * 2, 180, 270, $color);
    imagearc($image, $x2 - $radius, $y1 + $radius, $radius * 2, $radius * 2, 270, 360, $color);
    imagearc($image, $x1 + $radius, $y2 - $radius, $radius * 2, $radius * 2, 90, 180, $color);
    imagearc($image, $x2 - $radius, $y2 - $radius, $radius * 2, $radius * 2, 0, 90, $color);

    imagesetthickness($image, 1);
}

function drawCompactFlagBadge(GdImage $image, int $x, int $y, int $width, int $height, bool $dark): void
{
    $shadowHex = $dark ? '#08140f' : '#1e231f';
    fillRoundedRect($image, $x + 10, $y + 12, $x + $width + 10, $y + $height + 12, 18, $shadowHex, 112);

    $stripeHeight = (int) floor($height / 3);
    $radius = 18;

    fillRoundedRect($image, $x, $y, $x + $width, $y + $height, $radius, '#f3ead8', $dark ? 14 : 0);
    imagefilledrectangle($image, $x, $y, $x + $width, $y + $stripeHeight, allocateHex($image, '#b6403f', $dark ? 18 : 0));
    imagefilledrectangle($image, $x, $y + $stripeHeight, $x + $width, $y + ($stripeHeight * 2), allocateHex($image, '#f4efe5', $dark ? 8 : 0));
    imagefilledrectangle($image, $x, $y + ($stripeHeight * 2), $x + $width, $y + $height, allocateHex($image, '#1a1a1a', $dark ? 10 : 0));
    strokeRoundedRect($image, $x, $y, $x + $width, $y + $height, $radius, $dark ? '#d2b57a' : '#d1b37a', 76);

    $green = allocateHex($image, '#0f5a46', $dark ? 6 : 0);
    $centerX = $x + (int) round($width / 2);
    $midY = $y + $stripeHeight + (int) round($stripeHeight / 2);
    imagefilledrectangle($image, $centerX - 38, $midY - 4, $centerX - 18, $midY + 4, $green);
    imagefilledrectangle($image, $centerX - 6, $midY - 4, $centerX + 14, $midY + 4, $green);
    imagefilledrectangle($image, $centerX + 26, $midY - 4, $centerX + 46, $midY + 4, $green);
}

function drawFrame(GdImage $image, int $width, int $height, string $hex, int $thickness, int $alpha): void
{
    $color = allocateHex($image, $hex, $alpha);
    imagesetthickness($image, $thickness);
    imagerectangle($image, 18, 18, $width - 18, $height - 18, $color);
    imagesetthickness($image, 1);
}

function placeImageInBox(GdImage $canvas, GdImage $image, int $x, int $y, int $boxWidth, int $boxHeight): void
{
    $srcWidth = imagesx($image);
    $srcHeight = imagesy($image);

    $scale = min($boxWidth / $srcWidth, $boxHeight / $srcHeight);
    $destWidth = (int) round($srcWidth * $scale);
    $destHeight = (int) round($srcHeight * $scale);

    $destX = $x + (int) round(($boxWidth - $destWidth) / 2);
    $destY = $y + (int) round(($boxHeight - $destHeight) / 2);

    imagealphablending($canvas, true);
    imagecopyresampled($canvas, $image, $destX, $destY, 0, 0, $destWidth, $destHeight, $srcWidth, $srcHeight);
}

function allocateHex(GdImage $image, string $hex, int $alpha = 0): int
{
    [$r, $g, $b] = hexToRgb($hex);

    return imagecolorallocatealpha($image, $r, $g, $b, $alpha);
}

function hexToRgb(string $hex): array
{
    $hex = ltrim($hex, '#');

    return [
        hexdec(substr($hex, 0, 2)),
        hexdec(substr($hex, 2, 2)),
        hexdec(substr($hex, 4, 2)),
    ];
}
