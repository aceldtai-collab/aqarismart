<?php

namespace Database\Seeders\Support;

final class IraqSeedVisualFactory
{
    public function tenantLogo(array $tenant): string
    {
        $palette = $this->palette($tenant);
        $name = $this->escape($tenant['name']);
        $region = $this->escape($tenant['region_en']);
        $tagline = $this->escape($tenant['tagline']);
        $initials = $this->escape($this->initials($tenant['name']));
        $flag = $this->flagStripe(0, 0, 900, 24);
        $motif = $this->listingIllustration('logo', 'apartment', 450, 430, 1.05, $palette, 0.95);

        return $this->wrap(900, 900, <<<SVG
<defs>
  <linearGradient id="logo-bg" x1="0%" y1="0%" x2="100%" y2="100%">
    <stop offset="0%" stop-color="{$palette['cream']}" />
    <stop offset="100%" stop-color="{$palette['sand']}" />
  </linearGradient>
  <radialGradient id="logo-halo" cx="50%" cy="42%" r="46%">
    <stop offset="0%" stop-color="{$palette['accent_soft']}" stop-opacity="0.90" />
    <stop offset="100%" stop-color="{$palette['accent_soft']}" stop-opacity="0" />
  </radialGradient>
</defs>
<rect width="900" height="900" rx="84" fill="url(#logo-bg)" />
{$flag}
<circle cx="450" cy="358" r="214" fill="url(#logo-halo)" />
<rect x="82" y="82" width="736" height="736" rx="68" fill="none" stroke="{$palette['border']}" stroke-width="4" />
{$motif}
<circle cx="450" cy="580" r="70" fill="{$palette['base']}" />
<text x="450" y="602" fill="{$palette['cream']}" font-size="54" font-weight="800" text-anchor="middle" font-family="Segoe UI, Tahoma, Arial, sans-serif">{$initials}</text>
<text x="450" y="712" fill="{$palette['ink']}" font-size="48" font-weight="800" text-anchor="middle" font-family="Segoe UI, Tahoma, Arial, sans-serif">{$name}</text>
<text x="450" y="758" fill="{$palette['base_soft']}" font-size="26" font-weight="700" text-anchor="middle" font-family="Segoe UI, Tahoma, Arial, sans-serif">{$region}</text>
<text x="450" y="804" fill="{$palette['muted_text']}" font-size="22" font-weight="500" text-anchor="middle" font-family="Segoe UI, Tahoma, Arial, sans-serif">{$tagline}</text>
SVG);
    }

    public function tenantHeader(array $tenant): string
    {
        $palette = $this->palette($tenant);
        $name = $this->escape($tenant['name']);
        $region = $this->escape($tenant['region_en']);
        $tagline = $this->escape($tenant['tagline']);
        $flag = $this->flagStripe(0, 0, 1600, 28);
        $ornament = $this->regionOrnament($tenant['state_code'], $palette);
        $motif = $this->listingIllustration('header', 'villa', 1200, 440, 1.55, $palette, 0.88);

        return $this->wrap(1600, 900, <<<SVG
<defs>
  <linearGradient id="header-bg" x1="0%" y1="0%" x2="100%" y2="100%">
    <stop offset="0%" stop-color="{$palette['base']}" />
    <stop offset="55%" stop-color="{$palette['base_soft']}" />
    <stop offset="100%" stop-color="{$palette['base_deep']}" />
  </linearGradient>
  <radialGradient id="header-glow" cx="26%" cy="28%" r="52%">
    <stop offset="0%" stop-color="{$palette['accent_soft']}" stop-opacity="0.44" />
    <stop offset="100%" stop-color="{$palette['accent_soft']}" stop-opacity="0" />
  </radialGradient>
</defs>
<rect width="1600" height="900" rx="72" fill="url(#header-bg)" />
{$flag}
<circle cx="420" cy="290" r="340" fill="url(#header-glow)" />
<path d="M0 736C210 644 380 620 590 648C760 670 960 740 1600 900H0Z" fill="{$palette['sand']}" fill-opacity="0.09" />
<path d="M0 804C240 714 470 694 672 726C862 756 1100 820 1600 900H0Z" fill="{$palette['accent']}" fill-opacity="0.13" />
{$ornament}
{$motif}
<rect x="110" y="128" width="176" height="50" rx="25" fill="{$palette['accent']}" fill-opacity="0.18" stroke="{$palette['accent_soft']}" stroke-width="2" />
<text x="198" y="160" fill="{$palette['cream']}" font-size="22" font-weight="700" text-anchor="middle" font-family="Segoe UI, Tahoma, Arial, sans-serif">IRAQ SEED</text>
<text x="112" y="278" fill="{$palette['cream']}" font-size="82" font-weight="800" font-family="Segoe UI, Tahoma, Arial, sans-serif">{$name}</text>
<text x="112" y="336" fill="{$palette['accent_soft']}" font-size="28" font-weight="700" font-family="Segoe UI, Tahoma, Arial, sans-serif">{$region}</text>
<text x="112" y="420" fill="{$palette['cream']}" font-size="34" font-weight="500" font-family="Segoe UI, Tahoma, Arial, sans-serif">{$tagline}</text>
<rect x="112" y="498" width="250" height="94" rx="30" fill="{$palette['cream']}" fill-opacity="0.10" stroke="{$palette['cream']}" stroke-opacity="0.24" stroke-width="2" />
<text x="152" y="538" fill="{$palette['accent_soft']}" font-size="20" font-weight="700" font-family="Segoe UI, Tahoma, Arial, sans-serif">REGION</text>
<text x="152" y="574" fill="{$palette['cream']}" font-size="34" font-weight="800" font-family="Segoe UI, Tahoma, Arial, sans-serif">{$region}</text>
<rect x="388" y="498" width="290" height="94" rx="30" fill="{$palette['cream']}" fill-opacity="0.10" stroke="{$palette['cream']}" stroke-opacity="0.24" stroke-width="2" />
<text x="428" y="538" fill="{$palette['accent_soft']}" font-size="20" font-weight="700" font-family="Segoe UI, Tahoma, Arial, sans-serif">STYLE</text>
<text x="428" y="574" fill="{$palette['cream']}" font-size="34" font-weight="800" font-family="Segoe UI, Tahoma, Arial, sans-serif">Governorate Storytelling</text>
SVG);
    }

    public function agentPortrait(array $tenant, array $agent): string
    {
        $palette = $this->palette($tenant);
        $name = $this->escape($agent['name']['en'] ?? $agent['name']);
        $region = $this->escape($tenant['region_en']);
        $license = $this->escape((string) ($agent['license_id'] ?? ''));
        $initials = $this->escape($this->initials($agent['name']['en'] ?? $agent['name']));
        $flag = $this->flagStripe(0, 0, 900, 22);
        $seed = $this->numberSeed($agent['slug'] ?? $name);

        return $this->wrap(900, 1120, <<<SVG
<defs>
  <linearGradient id="agent-bg" x1="0%" y1="0%" x2="100%" y2="100%">
    <stop offset="0%" stop-color="{$palette['cream']}" />
    <stop offset="100%" stop-color="{$palette['sand']}" />
  </linearGradient>
  <radialGradient id="agent-halo" cx="50%" cy="32%" r="48%">
    <stop offset="0%" stop-color="{$palette['accent_soft']}" stop-opacity="0.88" />
    <stop offset="100%" stop-color="{$palette['accent_soft']}" stop-opacity="0" />
  </radialGradient>
</defs>
<rect width="900" height="1120" rx="70" fill="url(#agent-bg)" />
{$flag}
<circle cx="450" cy="364" r="220" fill="url(#agent-halo)" />
<circle cx="450" cy="352" r="160" fill="{$palette['base']}" />
<circle cx="450" cy="318" r="70" fill="{$palette['cream']}" fill-opacity="0.92" />
<path d="M316 522C336 432 396 386 450 386C504 386 564 432 584 522" fill="{$palette['cream']}" fill-opacity="0.92" />
<text x="450" y="836" fill="{$palette['ink']}" font-size="46" font-weight="800" text-anchor="middle" font-family="Segoe UI, Tahoma, Arial, sans-serif">{$name}</text>
<text x="450" y="890" fill="{$palette['base_soft']}" font-size="28" font-weight="700" text-anchor="middle" font-family="Segoe UI, Tahoma, Arial, sans-serif">{$region}</text>
<rect x="154" y="936" width="592" height="88" rx="28" fill="{$palette['base']}" fill-opacity="0.08" stroke="{$palette['border']}" stroke-width="2" />
<text x="450" y="992" fill="{$palette['base_deep']}" font-size="28" font-weight="700" text-anchor="middle" font-family="Segoe UI, Tahoma, Arial, sans-serif">License {$license}</text>
<circle cx="754" cy="170" r="54" fill="{$palette['accent']}" fill-opacity="0.18" stroke="{$palette['accent']}" stroke-width="3" />
<text x="754" y="188" fill="{$palette['base_deep']}" font-size="38" font-weight="800" text-anchor="middle" font-family="Segoe UI, Tahoma, Arial, sans-serif">{$initials}</text>
<path d="M110 198C182 136 248 112 334 124" fill="none" stroke="{$palette['accent']}" stroke-opacity="0.36" stroke-width="8" stroke-linecap="round" />
<path d="M576 122C640 94 712 96 796 144" fill="none" stroke="{$palette['base_soft']}" stroke-opacity="0.26" stroke-width="8" stroke-linecap="round" />
<circle cx="170" cy="884" r="18" fill="{$palette['accent']}" fill-opacity="0.35" />
<circle cx="202" cy="844" r="8" fill="{$palette['accent']}" fill-opacity="0.50" />
<circle cx="128" cy="838" r="12" fill="{$palette['accent']}" fill-opacity="0.30" />
<circle cx="734" cy="720" r="20" fill="{$palette['base_soft']}" fill-opacity="0.16" />
<circle cx="788" cy="754" r="10" fill="{$palette['base_soft']}" fill-opacity="0.24" />
<circle cx="704" cy="778" r="8" fill="{$palette['base_soft']}" fill-opacity="0.22" />
<!-- variation seed: {$seed} -->
SVG);
    }

    public function propertyPhoto(array $tenant, array $record, int $variant): string
    {
        return $this->listingCanvas('property', $tenant, $record, $variant);
    }

    public function unitPhoto(array $tenant, array $record, int $variant): string
    {
        return $this->listingCanvas('unit', $tenant, $record, $variant);
    }

    private function listingCanvas(string $surface, array $tenant, array $record, int $variant): string
    {
        $palette = $this->palette($tenant);
        $title = $this->escape($surface === 'property' ? ($record['property_name']['en'] ?? $record['headline_en']) : ($record['title']['en'] ?? $record['headline_en']));
        $location = $this->escape(($record['district_en'] ?? '').', '.($record['city'] ?? ''));
        $price = $this->escape($this->priceLabel($record));
        $listingType = $this->escape($record['listing_type'] === 'rent' ? 'For Rent' : 'For Sale');
        $code = $this->escape($record['code']);
        $subcategory = $this->escape($this->subCategoryLabel($record['subcategory']));
        $metaOne = $this->escape($this->metaOne($record));
        $metaTwo = $this->escape($this->metaTwo($record));
        $metaThree = $this->escape($this->metaThree($record));
        $flag = $this->flagStripe(0, 0, 1600, 22);
        $motif = $this->listingIllustration($surface, $record['subcategory'], 1170, 430, 1.40, $palette, 0.92);
        $backdrop = $this->photoBackdrop($variant, $palette);
        $featureGrid = $this->featureGrid($metaOne, $metaTwo, $metaThree, $palette, $variant);
        $eyebrow = strtoupper($surface === 'property' ? 'AGENCY PORTFOLIO' : 'LISTING STORY');

        return $this->wrap(1600, 1066, <<<SVG
<defs>
  <linearGradient id="photo-bg-{$surface}-{$variant}" x1="0%" y1="0%" x2="100%" y2="100%">
    <stop offset="0%" stop-color="{$palette['cream']}" />
    <stop offset="100%" stop-color="{$palette['sand']}" />
  </linearGradient>
  <radialGradient id="photo-halo-{$surface}-{$variant}" cx="26%" cy="30%" r="54%">
    <stop offset="0%" stop-color="{$palette['accent_soft']}" stop-opacity="0.86" />
    <stop offset="100%" stop-color="{$palette['accent_soft']}" stop-opacity="0" />
  </radialGradient>
</defs>
<rect width="1600" height="1066" rx="62" fill="url(#photo-bg-{$surface}-{$variant})" />
{$flag}
<circle cx="426" cy="296" r="360" fill="url(#photo-halo-{$surface}-{$variant})" />
{$backdrop}
{$motif}
<rect x="78" y="84" width="232" height="48" rx="24" fill="{$palette['base']}" fill-opacity="0.08" stroke="{$palette['border']}" stroke-width="2" />
<text x="194" y="115" fill="{$palette['base_deep']}" font-size="22" font-weight="700" text-anchor="middle" font-family="Segoe UI, Tahoma, Arial, sans-serif">{$eyebrow}</text>
<rect x="80" y="160" width="170" height="54" rx="27" fill="{$palette['base']}" />
<text x="165" y="195" fill="{$palette['cream']}" font-size="24" font-weight="800" text-anchor="middle" font-family="Segoe UI, Tahoma, Arial, sans-serif">{$listingType}</text>
<rect x="268" y="160" width="220" height="54" rx="27" fill="{$palette['accent']}" fill-opacity="0.18" stroke="{$palette['accent']}" stroke-width="2" />
<text x="378" y="195" fill="{$palette['base_deep']}" font-size="24" font-weight="800" text-anchor="middle" font-family="Segoe UI, Tahoma, Arial, sans-serif">{$subcategory}</text>
<text x="82" y="320" fill="{$palette['ink']}" font-size="74" font-weight="800" font-family="Segoe UI, Tahoma, Arial, sans-serif">{$title}</text>
<text x="82" y="384" fill="{$palette['base_soft']}" font-size="30" font-weight="600" font-family="Segoe UI, Tahoma, Arial, sans-serif">{$location}</text>
<text x="82" y="462" fill="{$palette['base_deep']}" font-size="56" font-weight="900" font-family="Segoe UI, Tahoma, Arial, sans-serif">{$price}</text>
{$featureGrid}
<rect x="82" y="924" width="250" height="64" rx="28" fill="{$palette['base']}" />
<text x="208" y="965" fill="{$palette['cream']}" font-size="28" font-weight="800" text-anchor="middle" font-family="Segoe UI, Tahoma, Arial, sans-serif">{$code}</text>
SVG);
    }

    private function photoBackdrop(int $variant, array $palette): string
    {
        return match ($variant) {
            1 => <<<SVG
<path d="M0 802C210 702 416 660 630 686C850 712 1082 812 1600 1066H0Z" fill="{$palette['base']}" fill-opacity="0.12" />
<path d="M0 872C250 786 516 764 726 792C958 824 1192 914 1600 1066H0Z" fill="{$palette['accent']}" fill-opacity="0.14" />
SVG,
            2 => <<<SVG
<rect x="858" y="150" width="592" height="592" rx="72" fill="{$palette['base']}" fill-opacity="0.10" />
<rect x="910" y="202" width="488" height="488" rx="56" fill="{$palette['cream']}" fill-opacity="0.34" stroke="{$palette['border']}" stroke-width="2" />
<path d="M0 870C246 782 500 760 724 800C920 836 1170 940 1600 1066H0Z" fill="{$palette['accent']}" fill-opacity="0.12" />
SVG,
            default => <<<SVG
<path d="M0 732C174 658 352 642 590 670C864 702 1116 816 1600 1066H0Z" fill="{$palette['base']}" fill-opacity="0.11" />
<path d="M0 792C210 718 418 706 630 732C876 764 1140 876 1600 1066H0Z" fill="{$palette['accent']}" fill-opacity="0.15" />
<path d="M910 158L1340 158L1440 258L1440 642L1008 642L910 542Z" fill="{$palette['cream']}" fill-opacity="0.20" stroke="{$palette['border']}" stroke-width="2" />
SVG,
        };
    }

    private function featureGrid(string $metaOne, string $metaTwo, string $metaThree, array $palette, int $variant): string
    {
        $x = 82;
        $y = $variant === 2 ? 558 : 536;
        $gap = 24;
        $cardWidth = 246;

        return sprintf(
            '<rect x="%1$d" y="%2$d" width="%3$d" height="120" rx="28" fill="%4$s" fill-opacity="0.08" stroke="%5$s" stroke-width="2" />' .
            '<text x="%6$d" y="%7$d" fill="%8$s" font-size="20" font-weight="700" font-family="Segoe UI, Tahoma, Arial, sans-serif">FACT 01</text>' .
            '<text x="%6$d" y="%9$d" fill="%10$s" font-size="32" font-weight="800" font-family="Segoe UI, Tahoma, Arial, sans-serif">%11$s</text>' .
            '<rect x="%12$d" y="%2$d" width="%3$d" height="120" rx="28" fill="%4$s" fill-opacity="0.08" stroke="%5$s" stroke-width="2" />' .
            '<text x="%13$d" y="%7$d" fill="%8$s" font-size="20" font-weight="700" font-family="Segoe UI, Tahoma, Arial, sans-serif">FACT 02</text>' .
            '<text x="%13$d" y="%9$d" fill="%10$s" font-size="32" font-weight="800" font-family="Segoe UI, Tahoma, Arial, sans-serif">%14$s</text>' .
            '<rect x="%15$d" y="%2$d" width="%3$d" height="120" rx="28" fill="%4$s" fill-opacity="0.08" stroke="%5$s" stroke-width="2" />' .
            '<text x="%16$d" y="%7$d" fill="%8$s" font-size="20" font-weight="700" font-family="Segoe UI, Tahoma, Arial, sans-serif">FACT 03</text>' .
            '<text x="%16$d" y="%9$d" fill="%10$s" font-size="32" font-weight="800" font-family="Segoe UI, Tahoma, Arial, sans-serif">%17$s</text>',
            $x,
            $y,
            $cardWidth,
            $palette['base'],
            $palette['border'],
            $x + 28,
            $y + 38,
            $palette['muted_text'],
            $y + 82,
            $palette['base_deep'],
            $metaOne,
            $x + $cardWidth + $gap,
            $x + $cardWidth + $gap + 28,
            $metaTwo,
            $x + (($cardWidth + $gap) * 2),
            $x + (($cardWidth + $gap) * 2) + 28,
            $metaThree,
        );
    }

    private function listingIllustration(string $surface, string $subcategory, int $centerX, int $centerY, float $scale, array $palette, float $opacity): string
    {
        $transform = sprintf('translate(%d %d) scale(%0.3F)', $centerX, $centerY, $scale);
        $base = $palette['base_deep'];
        $accent = $palette['accent'];
        $soft = $palette['cream'];

        $shape = match ($subcategory) {
            'villa', 'duplex', 'guest_house', 'apartment', 'furnished_apartment' => <<<SVG
<rect x="-200" y="56" width="96" height="146" rx="16" fill="{$base}" opacity="{$opacity}" />
<rect x="-88" y="8" width="112" height="194" rx="16" fill="{$base}" opacity="{$opacity}" />
<rect x="40" y="42" width="88" height="160" rx="16" fill="{$base}" opacity="{$opacity}" />
<path d="M-128 88L0 -22L124 88L124 204H28V120H-24V204H-128Z" fill="{$soft}" opacity="0.96" />
<rect x="-36" y="88" width="32" height="34" rx="4" fill="{$base}" opacity="0.72" />
<rect x="10" y="88" width="32" height="34" rx="4" fill="{$base}" opacity="0.72" />
<path d="M-252 206C-118 116 34 116 222 206" fill="none" stroke="{$accent}" stroke-width="18" stroke-linecap="round" opacity="0.88" />
SVG,
            'office', 'showroom', 'retail_shop', 'hotel' => <<<SVG
<rect x="-202" y="-6" width="98" height="208" rx="18" fill="{$base}" opacity="{$opacity}" />
<rect x="-86" y="-56" width="118" height="258" rx="18" fill="{$base}" opacity="{$opacity}" />
<rect x="52" y="22" width="92" height="180" rx="18" fill="{$base}" opacity="{$opacity}" />
<rect x="-52" y="70" width="20" height="22" rx="2" fill="{$soft}" opacity="0.86" />
<rect x="-18" y="70" width="20" height="22" rx="2" fill="{$soft}" opacity="0.86" />
<rect x="16" y="70" width="20" height="22" rx="2" fill="{$soft}" opacity="0.86" />
<rect x="-52" y="106" width="20" height="22" rx="2" fill="{$soft}" opacity="0.86" />
<rect x="-18" y="106" width="20" height="22" rx="2" fill="{$soft}" opacity="0.86" />
<rect x="16" y="106" width="20" height="22" rx="2" fill="{$soft}" opacity="0.86" />
<rect x="-174" y="138" width="42" height="26" rx="2" fill="{$soft}" opacity="0.78" />
<rect x="82" y="88" width="36" height="18" rx="2" fill="{$soft}" opacity="0.78" />
<path d="M-246 206C-124 132 46 130 232 206" fill="none" stroke="{$accent}" stroke-width="18" stroke-linecap="round" opacity="0.88" />
SVG,
            'warehouse' => <<<SVG
<rect x="-210" y="44" width="420" height="150" rx="24" fill="{$base}" opacity="{$opacity}" />
<path d="M-220 80L-108 -10L0 80L108 -10L220 80V194H-220Z" fill="{$soft}" opacity="0.94" />
<rect x="-154" y="104" width="64" height="74" rx="10" fill="{$base}" opacity="0.62" />
<rect x="-44" y="104" width="64" height="74" rx="10" fill="{$base}" opacity="0.62" />
<rect x="66" y="104" width="64" height="74" rx="10" fill="{$base}" opacity="0.62" />
<path d="M-248 214H246" fill="none" stroke="{$accent}" stroke-width="16" stroke-linecap="round" opacity="0.82" />
SVG,
            'residential_land', 'commercial_land' => <<<SVG
<rect x="-198" y="-34" width="396" height="240" rx="28" fill="{$soft}" opacity="0.92" />
<path d="M-168 16H168M-168 76H168M-168 136H168M-108 -4V176M0 -4V176M108 -4V176" stroke="{$base}" stroke-opacity="0.42" stroke-width="10" stroke-linecap="round" />
<path d="M0 -88C42 -88 76 -54 76 -12C76 44 0 122 0 122C0 122 -76 44 -76 -12C-76 -54 -42 -88 0 -88Z" fill="{$accent}" opacity="0.92" />
<circle cx="0" cy="-14" r="28" fill="{$soft}" opacity="0.96" />
<path d="M-228 214H224" fill="none" stroke="{$accent}" stroke-width="16" stroke-linecap="round" opacity="0.82" />
SVG,
            default => <<<SVG
<rect x="-182" y="-18" width="96" height="220" rx="18" fill="{$base}" opacity="{$opacity}" />
<rect x="-58" y="-70" width="118" height="272" rx="18" fill="{$base}" opacity="{$opacity}" />
<rect x="84" y="24" width="82" height="178" rx="18" fill="{$base}" opacity="{$opacity}" />
<path d="M-240 206C-108 124 56 124 232 206" fill="none" stroke="{$accent}" stroke-width="18" stroke-linecap="round" opacity="0.88" />
SVG,
        };

        $glow = $surface === 'unit'
            ? '<circle cx="0" cy="12" r="250" fill="'.$palette['accent_soft'].'" opacity="0.18" />'
            : '<circle cx="-20" cy="12" r="280" fill="'.$palette['base'].'" opacity="0.09" />';

        return '<g transform="'.$transform.'">'.$glow.$shape.'</g>';
    }

    private function regionOrnament(string $stateCode, array $palette): string
    {
        return match ($stateCode) {
            'BGD' => '<path d="M936 194C1084 142 1248 142 1390 198" fill="none" stroke="'.$palette['accent'].'" stroke-opacity="0.36" stroke-width="10" stroke-linecap="round" /><path d="M970 238C1110 192 1248 192 1368 230" fill="none" stroke="'.$palette['cream'].'" stroke-opacity="0.24" stroke-width="8" stroke-linecap="round" />',
            'BSR' => '<path d="M936 214C1032 120 1190 114 1324 178C1388 208 1440 260 1480 324" fill="none" stroke="'.$palette['accent'].'" stroke-opacity="0.34" stroke-width="10" stroke-linecap="round" /><path d="M914 282C1036 208 1186 214 1318 282" fill="none" stroke="'.$palette['cream'].'" stroke-opacity="0.20" stroke-width="8" stroke-linecap="round" />',
            'ARB', 'SUL' => '<path d="M906 324L1036 186L1168 290L1278 160L1460 356" fill="none" stroke="'.$palette['accent'].'" stroke-opacity="0.34" stroke-width="12" stroke-linecap="round" stroke-linejoin="round" /><path d="M906 384L1052 264L1166 356L1300 236L1480 412" fill="none" stroke="'.$palette['cream'].'" stroke-opacity="0.20" stroke-width="8" stroke-linecap="round" stroke-linejoin="round" />',
            'NIN' => '<path d="M920 190H1450M920 258H1410M920 326H1450" fill="none" stroke="'.$palette['accent'].'" stroke-opacity="0.28" stroke-width="10" stroke-linecap="round" /><path d="M920 358C1060 278 1248 278 1450 368" fill="none" stroke="'.$palette['cream'].'" stroke-opacity="0.22" stroke-width="8" stroke-linecap="round" />',
            'NJF' => '<path d="M930 194C1106 134 1264 134 1438 194" fill="none" stroke="'.$palette['accent'].'" stroke-opacity="0.34" stroke-width="10" stroke-linecap="round" /><circle cx="1190" cy="270" r="126" fill="'.$palette['cream'].'" fill-opacity="0.08" /><path d="M960 374C1116 294 1278 300 1438 378" fill="none" stroke="'.$palette['cream'].'" stroke-opacity="0.22" stroke-width="8" stroke-linecap="round" />',
            default => '',
        };
    }

    private function palette(array $context): array
    {
        $palette = match ($context['state_code']) {
            'BGD' => [
                'base' => '#0f5d46',
                'base_soft' => '#2b7a63',
                'base_deep' => '#163a31',
                'accent' => '#b48a3a',
                'accent_soft' => '#e7c78e',
                'sand' => '#ead7b0',
                'cream' => '#faf4e7',
                'ink' => '#1f241d',
                'border' => '#d6c3a1',
                'muted_text' => '#5b675e',
            ],
            'BSR' => [
                'base' => '#13624c',
                'base_soft' => '#2e8870',
                'base_deep' => '#17443a',
                'accent' => '#c08c36',
                'accent_soft' => '#ebd29a',
                'sand' => '#e6d2a4',
                'cream' => '#fbf6eb',
                'ink' => '#1f261f',
                'border' => '#d9c9a3',
                'muted_text' => '#5f6f67',
            ],
            'ARB' => [
                'base' => '#155f49',
                'base_soft' => '#2b8468',
                'base_deep' => '#173f34',
                'accent' => '#c59a46',
                'accent_soft' => '#efd8a7',
                'sand' => '#e5d3ae',
                'cream' => '#fbf7ef',
                'ink' => '#1f241f',
                'border' => '#d5c6a3',
                'muted_text' => '#5d6b63',
            ],
            'NIN' => [
                'base' => '#1b5a45',
                'base_soft' => '#3c7f67',
                'base_deep' => '#214036',
                'accent' => '#b78d3f',
                'accent_soft' => '#ebd29c',
                'sand' => '#e8d6af',
                'cream' => '#faf5e9',
                'ink' => '#20241f',
                'border' => '#d5c4a0',
                'muted_text' => '#5f6a63',
            ],
            'NJF' => [
                'base' => '#155641',
                'base_soft' => '#35785f',
                'base_deep' => '#173a31',
                'accent' => '#c29641',
                'accent_soft' => '#eed9a5',
                'sand' => '#ead8b3',
                'cream' => '#fbf6eb',
                'ink' => '#1f241f',
                'border' => '#d5c6a4',
                'muted_text' => '#5f6a62',
            ],
            'SUL' => [
                'base' => '#145b47',
                'base_soft' => '#357d63',
                'base_deep' => '#163d33',
                'accent' => '#c79a46',
                'accent_soft' => '#f0d9a7',
                'sand' => '#e6d4ae',
                'cream' => '#fbf7ee',
                'ink' => '#1f241f',
                'border' => '#d4c4a3',
                'muted_text' => '#5c6961',
            ],
            default => [
                'base' => '#0f5d46',
                'base_soft' => '#2d7a63',
                'base_deep' => '#163a31',
                'accent' => '#b48a3a',
                'accent_soft' => '#e7c78e',
                'sand' => '#ead7b0',
                'cream' => '#faf4e7',
                'ink' => '#1f241d',
                'border' => '#d6c3a1',
                'muted_text' => '#5b675e',
            ],
        };

        if (! empty($context['primary_color'])) {
            $palette['base'] = $context['primary_color'];
        }

        if (! empty($context['accent_color'])) {
            $palette['accent'] = $context['accent_color'];
        }

        return $palette;
    }

    private function subCategoryLabel(string $subcategory): string
    {
        return match ($subcategory) {
            'apartment' => 'Apartment',
            'furnished_apartment' => 'Furnished Apartment',
            'duplex' => 'Duplex',
            'villa' => 'Villa',
            'office' => 'Office',
            'showroom' => 'Showroom',
            'retail_shop' => 'Retail Shop',
            'warehouse' => 'Warehouse',
            'guest_house' => 'Guest House',
            'hotel' => 'Hotel',
            'residential_land' => 'Residential Land',
            'commercial_land' => 'Commercial Land',
            default => 'Property',
        };
    }

    private function priceLabel(array $record): string
    {
        $value = $record['listing_type'] === 'rent' && ($record['market_rent'] ?? 0) > 0
            ? (int) $record['market_rent']
            : (int) $record['price'];

        return 'IQD '.number_format($value);
    }

    private function metaOne(array $record): string
    {
        if (in_array($record['subcategory'], ['residential_land', 'commercial_land'], true)) {
            return number_format((int) round(($record['land_sqm'] ?? 0) ?: ($record['sqft'] / 10.7639))).' sqm';
        }

        if (($record['beds'] ?? 0) > 0) {
            return $record['beds'].' bedrooms';
        }

        return $this->subCategoryLabel($record['subcategory']);
    }

    private function metaTwo(array $record): string
    {
        if (in_array($record['subcategory'], ['residential_land', 'commercial_land'], true)) {
            return ($record['district_en'] ?? '').' district';
        }

        if (($record['baths'] ?? 0) > 0) {
            return rtrim(rtrim(number_format((float) $record['baths'], 1), '0'), '.').' baths';
        }

        return $record['listing_type'] === 'rent' ? 'Ready to lease' : 'Ready to purchase';
    }

    private function metaThree(array $record): string
    {
        return number_format((int) $record['sqft']).' sqft';
    }

    private function flagStripe(int $x, int $y, int $width, int $height): string
    {
        $third = (int) round($height / 3);
        $middleY = $y + $third;
        $bottomY = $middleY + $third;

        return sprintf(
            '<rect x="%d" y="%d" width="%d" height="%d" fill="#CE1126" />'.
            '<rect x="%d" y="%d" width="%d" height="%d" fill="#FFFFFF" />'.
            '<rect x="%d" y="%d" width="%d" height="%d" fill="#000000" />'.
            '<text x="%d" y="%d" fill="#007A3D" font-size="%d" font-weight="700" text-anchor="middle" font-family="Cairo, Tahoma, Arial, sans-serif">الله أكبر</text>',
            $x,
            $y,
            $width,
            $third,
            $x,
            $middleY,
            $width,
            $third,
            $x,
            $bottomY,
            $width,
            $height - ($third * 2),
            $x + (int) round($width / 2),
            $middleY + (int) round($third * 0.75),
            max(10, (int) round($height * 0.70))
        );
    }

    private function initials(string $value): string
    {
        $words = preg_split('/[\s\-]+/u', trim($value)) ?: [];
        $initials = '';

        foreach (array_slice($words, 0, 2) as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }

        return $initials !== '' ? $initials : 'AS';
    }

    private function numberSeed(string $value): int
    {
        return abs((int) crc32($value)) % 997;
    }

    private function escape(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }

    private function wrap(int $width, int $height, string $content): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="{$width}" height="{$height}" viewBox="0 0 {$width} {$height}" role="img" aria-label="Seeded Iraq visual asset">
{$content}
</svg>
SVG;
    }
}
