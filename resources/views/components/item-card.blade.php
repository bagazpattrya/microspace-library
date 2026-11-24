@props([
    'item' => null,
    'title' => null,
    'name' => null,
    'subtitle' => null,
    'category' => null,
    'price' => null,
    'priceMin' => null,
    'priceMax' => null,
    'image' => null,
    'imageAlt' => null,
    'badge' => null,
    'isNew' => null,
    'rank' => null,
    'href' => null,
    'url' => null,
    'tooltip' => true,
    'selectedOutlet' => null,
])

@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;

    // 1. Resolve Title / Name
    $cardTitle = $title ?? $name ?? ($item->name ?? ($item->title ?? ''));

    // 2. Resolve Subtitle / Category
    $cardSubtitle = $subtitle ?? $category ?? ($item->category?->name ?? ($item->category_name ?? ($item->subtitle ?? null)));

    // 3. Resolve Image Path
    $cardImage = $image ?? ($item->image ?? ($item->photo ?? ($item->thumbnail ?? null)));

    // 4. Resolve Badges
    $badgeText = $badge;
    $hasNewBadge = false;
    if ($isNew !== null) {
        $hasNewBadge = (bool)$isNew;
    } elseif (isset($item->is_new)) {
        $hasNewBadge = (bool)$item->is_new;
    }

    // 5. Resolve Link / Href
    $cardHref = $href ?? $url ?? null;
    if (!$cardHref && $item) {
        if (isset($item->url)) {
            $cardHref = $item->url;
        } elseif (method_exists($item, 'showUrl')) {
            $cardHref = $item->showUrl();
        } elseif (Route::has('menu-items.show') && $item instanceof \App\Models\MenuItem) {
            $cardHref = route('menu-items.show', $item);
        }
    }

    // 6. Resolve Price / Value Display
    $priceDisplay = $price;
    if ($priceDisplay === null && $item) {
        if (method_exists($item, 'priceRange')) {
            $range = $item->priceRange($selectedOutlet);
            $min = $range['min'] ?? 0;
            $max = $range['max'] ?? 0;
            $priceDisplay = 'Rp ' . number_format((float)$min);
            if ($min != $max && $max > 0) {
                $priceDisplay .= ' - Rp ' . number_format((float)$max);
            }
        } elseif (isset($item->price)) {
            $priceDisplay = is_numeric($item->price) ? 'Rp ' . number_format((float)$item->price) : $item->price;
        } elseif (isset($item->formatted_price)) {
            $priceDisplay = $item->formatted_price;
        }
    } elseif ($priceDisplay === null && (isset($priceMin) || isset($priceMax))) {
        $min = $priceMin ?? 0;
        $max = $priceMax ?? $min;
        $priceDisplay = 'Rp ' . number_format((float)$min);
        if ($min != $max && $max > 0) {
            $priceDisplay .= ' - Rp ' . number_format((float)$max);
        }
    }

    // 7. Popularity Rank
    $isTopRank = !empty($rank) && is_numeric($rank) && $rank <= 3;
    $rankClass = $isTopRank ? 'rank-card-' . $rank : '';

    // 8. Resolve Image URL
    $imageUrl = null;
    if (!empty($cardImage)) {
        if (Str::startsWith($cardImage, ['http://', 'https://', '//', 'data:'])) {
            $imageUrl = $cardImage;
        } elseif (Str::startsWith($cardImage, ['images/', 'assets/', 'storage/'])) {
            $imageUrl = asset($cardImage);
        } else {
            $imageUrl = Storage::url($cardImage);
        }
    }
    $placeholderUrl = brand_asset('images/no-image.jpg');
    $altText = $imageAlt ?? $cardTitle;
@endphp

@if ($cardHref)
<a href="{{ $cardHref }}" class="text-decoration-none w-100 d-flex flex-column" {{ $attributes->except(['class', 'style']) }}>
@endif

<div {{ $attributes->merge(['class' => 'microspace-item-card ' . $rankClass]) }}>
    {{-- Card Image Area --}}
    <div class="microspace-item-card-image">
        {{-- Badges --}}
        <div class="microspace-item-card-badges">
            @if ($isTopRank)
                <span class="microspace-badge-popularity rank-{{ $rank }}">
                    <i class="bi bi-fire me-1"></i>#{{ $rank }}
                </span>
            @endif

            @if ($hasNewBadge)
                <span class="microspace-badge-new">NEW</span>
            @endif

            @if ($badgeText)
                <span class="microspace-badge-new">{{ $badgeText }}</span>
            @endif

            @if (isset($badges))
                {{ $badges }}
            @endif
        </div>

        {{-- Image or Image Slot --}}
        @if (isset($imageSlot))
            {{ $imageSlot }}
        @elseif ($imageUrl)
            <img src="{{ $imageUrl }}" alt="{{ $altText }}" onerror="this.onerror=null;this.src='{{ $placeholderUrl }}';this.classList.add('default-no-image');">
        @else
            <img src="{{ $placeholderUrl }}" alt="{{ $altText }}" class="default-no-image">
        @endif

        {{-- Optional Floating Tag on Image (e.g. Free Drink) --}}
        @if (isset($imageTag))
            <div class="microspace-item-card-image-tag">
                {{ $imageTag }}
            </div>
        @endif
    </div>

    {{-- Card Body / Info Area --}}
    <div class="microspace-item-card-info">
        {{-- Title --}}
        <div class="microspace-item-card-title-wrap">
            <div class="microspace-item-card-title">
                {{ $slotTitle ?? $cardTitle }}
            </div>
            @if ($tooltip && !empty($cardTitle))
                <div class="microspace-item-card-tooltip">
                    {{ $cardTitle }}
                </div>
            @endif
        </div>

        {{-- Subtitle / Category Pill --}}
        @if (isset($subtitleSlot) || isset($categorySlot))
            <div class="microspace-item-card-category-wrap">
                {{ $subtitleSlot ?? $categorySlot }}
            </div>
        @elseif ($cardSubtitle)
            <div class="microspace-item-card-category-wrap">
                <div class="microspace-item-card-category">
                    {{ $cardSubtitle }}
                </div>
            </div>
        @endif

        {{-- Price / Value --}}
        @if (isset($priceSlot))
            <div class="microspace-item-card-price">
                {{ $priceSlot }}
            </div>
        @elseif ($priceDisplay !== null && $priceDisplay !== '')
            <div class="microspace-item-card-price">
                {{ $priceDisplay }}
            </div>
        @endif

        {{-- Footer / Custom Slot --}}
        @if (isset($footer))
            <div class="microspace-item-card-footer mt-auto pt-2">
                {{ $footer }}
            </div>
        @endif

        {{ $slot }}
    </div>
</div>

@if ($cardHref)
</a>
@endif
