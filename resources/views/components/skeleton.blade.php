@props([
    'type' => 'box',
    'loading' => true,
    'rows' => 5,
    'cols' => 4,
    'count' => 3,
    'lines' => 3,
    'fields' => 4,
    'header' => true,
    'action' => true,
    'image' => false,
    'avatar' => false,
    'footer' => true,
    'buttons' => true,
    'headers' => [],
    'search' => true,
    'pagination' => true,
    'length' => true,
    'width' => null,
    'height' => null,
    'radius' => null,
    'class' => '',
])

@php
    // If used via @include instead of <x-skeleton>, normalize variables
    $hasSlot = isset($slot) && !empty(trim((string)$slot));
    $type = strtolower($type ?? 'box');
@endphp

@if(!$loading && $hasSlot)
    {{ $slot }}
@else
    @switch($type)
        {{-- ==================== DATATABLE SKELETON (MATCHING REAL DATATABLES UI) ==================== --}}
        @case('datatable')
            <div {{ $attributes->merge(['class' => 'skeleton-datatable-wrapper ' . $class]) }}>
                {{-- Top Controls: Length Menu + Search Input --}}
                <div class="row mb-3 align-items-center">
                    <div class="col-md-6 d-flex align-items-center gap-2">
                        @if($length)
                            <span class="skeleton-line" style="width: 40px; height: 14px; margin-bottom: 0;"></span>
                            <span class="skeleton-btn sm" style="width: 50px; height: 32px; border-radius: 6px;"></span>
                            <span class="skeleton-line" style="width: 45px; height: 14px; margin-bottom: 0;"></span>
                        @endif
                    </div>
                    <div class="col-md-6 d-flex justify-content-md-end justify-content-start mt-md-0 mt-2">
                        @if($search)
                            <span class="skeleton-input" style="width: 220px; height: 34px; border-radius: 6px;"></span>
                        @endif
                    </div>
                </div>

                {{-- Table Component --}}
                <div class="skeleton-table-wrapper">
                    <table class="skeleton-table table mb-0">
                        @if($header)
                            <thead>
                                <tr>
                                    @if(!empty($headers) && is_array($headers))
                                        @foreach($headers as $idx => $h)
                                            @php
                                                $isActionCol = (strtolower($h) === 'aksi' || strtolower($h) === 'action' || $loop->last);
                                            @endphp
                                            <th @if($isActionCol) class="text-center" style="width: 90px;" @endif>
                                                <span class="skeleton-line {{ $isActionCol ? 'w-50 mx-auto' : 'w-65' }}"></span>
                                            </th>
                                        @endforeach
                                    @else
                                        @for($c = 0; $c < $cols; $c++)
                                            @php
                                                $colWidths = ['w-50', 'w-65', 'w-75', 'w-65', 'w-50'];
                                                $w = $colWidths[$c % count($colWidths)];
                                                $isActionCol = ($action && $c === $cols - 1);
                                            @endphp
                                            <th @if($isActionCol) class="text-center" style="width: 90px;" @endif>
                                                <span class="skeleton-line {{ $isActionCol ? 'w-50 mx-auto' : $w }}"></span>
                                            </th>
                                        @endfor
                                    @endif
                                </tr>
                            </thead>
                        @endif
                        <tbody>
                            @php
                                $colCount = (!empty($headers) && is_array($headers)) ? count($headers) : $cols;
                            @endphp
                            @for($r = 0; $r < $rows; $r++)
                                <tr>
                                    @for($c = 0; $c < $colCount; $c++)
                                        @php
                                            $isActionCol = ($action && $c === $colCount - 1);
                                            $isBadgeCol = ($c === 2 && $colCount >= 4);
                                            $cellWidths = ['w-75', 'w-65', 'w-50', 'w-85', 'w-65'];
                                            $cw = $cellWidths[($r + $c) % count($cellWidths)];
                                        @endphp
                                        @if($isActionCol)
                                            <td class="text-center">
                                                <span class="skeleton-btn sm" style="width: 34px; height: 30px; border-radius: 6px;"></span>
                                            </td>
                                        @elseif($isBadgeCol)
                                            <td>
                                                <span class="skeleton-badge" style="width: 80px; height: 22px;"></span>
                                            </td>
                                        @else
                                            <td>
                                                @if($c === 0 && $avatar)
                                                    <div class="skeleton-td-inner">
                                                        <span class="skeleton-avatar sm"></span>
                                                        <span class="skeleton-line {{ $cw }}"></span>
                                                    </div>
                                                @else
                                                    <span class="skeleton-line {{ $cw }}"></span>
                                                @endif
                                            </td>
                                        @endif
                                    @endfor
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>

                {{-- Bottom Controls: Info + Pagination --}}
                @if($pagination)
                    <div class="row mt-3 align-items-center">
                        <div class="col-md-6 text-start">
                            <span class="skeleton-line" style="width: 170px; height: 14px; margin-bottom: 0;"></span>
                        </div>
                        <div class="col-md-6 d-flex justify-content-md-end justify-content-start gap-1 mt-md-0 mt-2">
                            <span class="skeleton-btn sm" style="width: 60px; height: 28px; border-radius: 6px;"></span>
                            <span class="skeleton-btn sm" style="width: 28px; height: 28px; border-radius: 6px;"></span>
                            <span class="skeleton-btn sm" style="width: 28px; height: 28px; border-radius: 6px;"></span>
                            <span class="skeleton-btn sm" style="width: 28px; height: 28px; border-radius: 6px;"></span>
                            <span class="skeleton-btn sm" style="width: 50px; height: 28px; border-radius: 6px;"></span>
                        </div>
                    </div>
                @endif
            </div>
            @break
        {{-- ==================== TABLE SKELETON ==================== --}}
        @case('table')
            <div {{ $attributes->merge(['class' => 'skeleton-table-wrapper ' . $class]) }}>
                <table class="skeleton-table table mb-0">
                    @if($header)
                        <thead>
                            <tr>
                                @for($c = 0; $c < $cols; $c++)
                                    @php
                                        $colWidths = ['w-35', 'w-50', 'w-75', 'w-25', 'w-65'];
                                        $w = $colWidths[$c % count($colWidths)];
                                    @endphp
                                    <th>
                                        <span class="skeleton-line {{ $w }}"></span>
                                    </th>
                                @endfor
                                @if($action)
                                    <th style="width: 100px; text-align: center;">
                                        <span class="skeleton-line w-50 mx-auto"></span>
                                    </th>
                                @endif
                            </tr>
                        </thead>
                    @endif
                    <tbody>
                        @for($r = 0; $r < $rows; $r++)
                            <tr>
                                @for($c = 0; $c < $cols; $c++)
                                    @php
                                        $cellWidths = ['w-35', 'w-65', 'w-50', 'w-75', 'w-25'];
                                        $cw = $cellWidths[($r + $c) % count($cellWidths)];
                                    @endphp
                                    <td>
                                        @if($c === 0 && $avatar)
                                            <div class="skeleton-td-inner">
                                                <span class="skeleton-avatar sm"></span>
                                                <span class="skeleton-line {{ $cw }}"></span>
                                            </div>
                                        @else
                                            <span class="skeleton-line {{ $cw }}"></span>
                                        @endif
                                    </td>
                                @endfor
                                @if($action)
                                    <td style="text-align: center;">
                                        <span class="skeleton-btn sm" style="width: 65px;"></span>
                                    </td>
                                @endif
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
            @break

        {{-- ==================== TABLE ROWS ONLY ==================== --}}
        @case('table-rows')
        @case('rows')
            @for($r = 0; $r < $rows; $r++)
                <tr>
                    @for($c = 0; $c < $cols; $c++)
                        @php
                            $cellWidths = ['w-35', 'w-65', 'w-50', 'w-75', 'w-25'];
                            $cw = $cellWidths[($r + $c) % count($cellWidths)];
                        @endphp
                        <td>
                            @if($c === 0 && $avatar)
                                <div class="skeleton-td-inner">
                                    <span class="skeleton-avatar sm"></span>
                                    <span class="skeleton-line {{ $cw }}"></span>
                                </div>
                            @else
                                <span class="skeleton-line {{ $cw }}"></span>
                            @endif
                        </td>
                    @endfor
                    @if($action)
                        <td style="text-align: center;">
                            <span class="skeleton-btn sm" style="width: 65px;"></span>
                        </td>
                    @endif
                </tr>
            @endfor
            @break

        {{-- ==================== CARDS GRID SKELETON ==================== --}}
        @case('card')
        @case('cards')
            @php
                $colClass = $cols == 1 ? 'col-12' :
                           ($cols == 2 ? 'col-md-6 col-12' :
                           ($cols == 4 ? 'col-xl-3 col-md-6 col-12' :
                           'col-lg-4 col-md-6 col-12'));
            @endphp
            <div {{ $attributes->merge(['class' => 'row g-3 ' . $class]) }}>
                @for($i = 0; $i < $count; $i++)
                    <div class="{{ $colClass }}">
                        <div class="skeleton-card h-100">
                            @if($image)
                                <div class="skeleton-thumb mb-3"></div>
                            @endif
                            <div class="skeleton-card-header">
                                <span class="skeleton-title mb-0" style="width: 55%;"></span>
                                <span class="skeleton-badge"></span>
                            </div>
                            <div class="skeleton-card-body">
                                @for($l = 0; $l < $lines; $l++)
                                    @php
                                        $lw = ($l === $lines - 1) ? 'w-50' : (($l % 2 === 0) ? 'w-85' : 'w-100');
                                    @endphp
                                    <span class="skeleton-line {{ $lw }}"></span>
                                @endfor
                            </div>
                            @if($footer)
                                <div class="skeleton-card-footer">
                                    <span class="skeleton-line w-25 mb-0"></span>
                                    <span class="skeleton-btn sm"></span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endfor
            </div>
            @break

        {{-- ==================== STAT / METRIC CARDS ==================== --}}
        @case('stat')
        @case('stats')
        @case('metric')
        @case('metrics')
            @php
                $colClass = $cols == 1 ? 'col-12' :
                           ($cols == 2 ? 'col-md-6 col-12' :
                           ($cols == 3 ? 'col-lg-4 col-md-6 col-12' :
                           'col-xl-3 col-md-6 col-12'));
            @endphp
            <div {{ $attributes->merge(['class' => 'row g-3 ' . $class]) }}>
                @for($i = 0; $i < $count; $i++)
                    <div class="{{ $colClass }}">
                        <div class="skeleton-stat-card">
                            <div class="skeleton stat-icon"></div>
                            <div class="stat-info">
                                <span class="skeleton-line w-50 sm mb-1"></span>
                                <span class="skeleton-line w-75 lg mb-0"></span>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
            @break

        {{-- ==================== LIST SKELETON ==================== --}}
        @case('list')
            <div {{ $attributes->merge(['class' => 'skeleton-list-container ' . $class]) }}>
                @for($i = 0; $i < $count; $i++)
                    <div class="skeleton-list-item">
                        <div class="skeleton-list-left">
                            @if($avatar)
                                <span class="skeleton-avatar"></span>
                            @endif
                            <div class="skeleton-list-meta">
                                <span class="skeleton-line {{ $i % 2 === 0 ? 'w-65' : 'w-50' }}"></span>
                                @for($l = 1; $l < $lines; $l++)
                                    <span class="skeleton-line sm w-35"></span>
                                @endfor
                            </div>
                        </div>
                        @if($action)
                            <div>
                                <span class="skeleton-badge"></span>
                            </div>
                        @endif
                    </div>
                @endfor
            </div>
            @break

        {{-- ==================== FORM SKELETON ==================== --}}
        @case('form')
            @php
                $colClass = $cols == 2 ? 'col-md-6 col-12' : 'col-12';
            @endphp
            <div {{ $attributes->merge(['class' => 'skeleton-form-container ' . $class]) }}>
                <div class="row">
                    @for($i = 0; $i < $fields; $i++)
                        <div class="{{ $colClass }}">
                            <div class="skeleton-form-group">
                                <div class="skeleton skeleton-form-label"></div>
                                <div class="skeleton skeleton-input"></div>
                            </div>
                        </div>
                    @endfor
                </div>
                @if($buttons)
                    <div class="d-flex justify-content-end gap-2 mt-3 pt-3 border-top" style="border-color: var(--skeleton-border)!important;">
                        <div class="skeleton skeleton-btn" style="width: 80px;"></div>
                        <div class="skeleton skeleton-btn" style="width: 120px;"></div>
                    </div>
                @endif
            </div>
            @break

        {{-- ==================== DETAIL / PROFILE SKELETON ==================== --}}
        @case('detail')
        @case('profile')
            <div {{ $attributes->merge(['class' => 'skeleton-detail-card ' . $class]) }}>
                <div class="skeleton-detail-header">
                    <div class="skeleton skeleton-avatar xl"></div>
                    <div class="d-flex flex-column gap-2 flex-grow-1">
                        <div class="skeleton skeleton-title mb-0" style="width: 35%;"></div>
                        <div class="skeleton skeleton-line w-25 sm mb-0"></div>
                        <div class="d-flex gap-2 mt-1">
                            <div class="skeleton skeleton-badge"></div>
                            <div class="skeleton skeleton-badge"></div>
                        </div>
                    </div>
                </div>
                <div class="skeleton-detail-grid">
                    @for($g = 0; $g < 6; $g++)
                        <div>
                            <div class="skeleton skeleton-line w-35 sm mb-1"></div>
                            <div class="skeleton skeleton-line w-75 mb-0"></div>
                        </div>
                    @endfor
                </div>
            </div>
            @break

        {{-- ==================== DASHBOARD SKELETON ==================== --}}
        @case('dashboard')
            <div {{ $attributes->merge(['class' => 'skeleton-dashboard-wrapper ' . $class]) }}>
                {{-- Desktop View --}}
                <div class="big-screen">
                    {{-- Welcome title --}}
                    <div class="skeleton mb-3" style="width: 280px; height: 32px; border-radius: 8px;"></div>

                    {{-- Banner / Toolbox Card --}}
                    <div class="skeleton-card mb-4" style="padding: 1.25rem; border-radius: 14px;">
                        {{-- Banner Shimmer --}}
                        <div class="skeleton mb-4" style="width: 100%; height: 160px; border-radius: 12px;"></div>
                        
                        {{-- Menu Label Shimmer --}}
                        <div class="skeleton mb-3" style="width: 70px; height: 18px; border-radius: 4px;"></div>

                        {{-- Toolbox Items (Grid of 8 items) --}}
                        <div class="d-flex flex-wrap gap-4 align-items-center justify-content-between px-2 py-1">
                            @for($tb = 0; $tb < 8; $tb++)
                                <div class="d-flex flex-column align-items-center gap-2" style="min-width: 60px;">
                                    <div class="skeleton" style="width: 52px; height: 52px; border-radius: 14px;"></div>
                                    <div class="skeleton-line" style="width: 55px; height: 11px; margin: 0; border-radius: 3px;"></div>
                                </div>
                            @endfor
                        </div>
                    </div>

                    {{-- Bottom Cards Grid (2 Columns matching .role-dashboard-bottom-container) --}}
                    <div class="row g-4">
                        {{-- Card 1: Invoice / Stats --}}
                        <div class="col-lg-6 col-12">
                            <div class="skeleton-card h-100" style="padding: 1.25rem; border-radius: 14px;">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="skeleton" style="width: 140px; height: 20px; border-radius: 5px;"></div>
                                    <div class="skeleton-badge" style="width: 90px; height: 22px;"></div>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-6">
                                        <div class="skeleton-stat-card" style="padding: 0.85rem; border-radius: 10px;">
                                            <div class="skeleton stat-icon" style="width: 42px; height: 42px; border-radius: 10px;"></div>
                                            <div class="stat-info">
                                                <span class="skeleton-line w-50 sm mb-1"></span>
                                                <span class="skeleton-line w-75 lg mb-0"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="skeleton-stat-card" style="padding: 0.85rem; border-radius: 10px;">
                                            <div class="skeleton stat-icon" style="width: 42px; height: 42px; border-radius: 10px;"></div>
                                            <div class="stat-info">
                                                <span class="skeleton-line w-50 sm mb-1"></span>
                                                <span class="skeleton-line w-75 lg mb-0"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- 3 list items --}}
                                <div class="d-flex flex-column gap-2">
                                    @for($li = 0; $li < 3; $li++)
                                        <div class="d-flex align-items-center justify-content-between p-2 rounded" style="background: var(--skeleton-base); border-radius: 8px;">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="skeleton" style="width: 36px; height: 36px; border-radius: 8px;"></div>
                                                <div>
                                                    <div class="skeleton-line mb-1" style="width: 120px; height: 12px;"></div>
                                                    <div class="skeleton-line sm mb-0" style="width: 80px; height: 10px;"></div>
                                                </div>
                                            </div>
                                            <div class="skeleton-badge" style="width: 70px; height: 20px;"></div>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        </div>

                        {{-- Card 2: Schedules / Status --}}
                        <div class="col-lg-6 col-12">
                            <div class="skeleton-card h-100" style="padding: 1.25rem; border-radius: 14px;">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="skeleton" style="width: 160px; height: 20px; border-radius: 5px;"></div>
                                    <div class="skeleton-badge" style="width: 80px; height: 22px;"></div>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-6">
                                        <div class="skeleton-stat-card" style="padding: 0.85rem; border-radius: 10px;">
                                            <div class="skeleton stat-icon" style="width: 42px; height: 42px; border-radius: 10px;"></div>
                                            <div class="stat-info">
                                                <span class="skeleton-line w-50 sm mb-1"></span>
                                                <span class="skeleton-line w-75 lg mb-0"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="skeleton-stat-card" style="padding: 0.85rem; border-radius: 10px;">
                                            <div class="skeleton stat-icon" style="width: 42px; height: 42px; border-radius: 10px;"></div>
                                            <div class="stat-info">
                                                <span class="skeleton-line w-50 sm mb-1"></span>
                                                <span class="skeleton-line w-75 lg mb-0"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- 3 list items --}}
                                <div class="d-flex flex-column gap-2">
                                    @for($li = 0; $li < 3; $li++)
                                        <div class="d-flex align-items-center justify-content-between p-2 rounded" style="background: var(--skeleton-base); border-radius: 8px;">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="skeleton" style="width: 36px; height: 36px; border-radius: 8px;"></div>
                                                <div>
                                                    <div class="skeleton-line mb-1" style="width: 140px; height: 12px;"></div>
                                                    <div class="skeleton-line sm mb-0" style="width: 90px; height: 10px;"></div>
                                                </div>
                                            </div>
                                            <div class="skeleton-badge" style="width: 75px; height: 20px;"></div>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Mobile View --}}
                <div class="mobile-screen flex-column w-100" style="display: flex; flex-direction: column; width: 100%;">
                    {{-- Mobile Top Banner --}}
                    <div class="skeleton mb-3" style="width: 100%; height: 170px; border-radius: 0 0 16px 16px;"></div>
                    
                    {{-- Mobile Toolbox (4x2 grid) --}}
                    <div class="skeleton-card mb-3 p-3 mx-auto" style="width: 95%; border-radius: 14px;">
                        <div class="row g-3 text-center">
                            @for($mtb = 0; $mtb < 8; $mtb++)
                                <div class="col-3 d-flex flex-column align-items-center gap-1">
                                    <div class="skeleton" style="width: 46px; height: 46px; border-radius: 12px;"></div>
                                    <div class="skeleton-line" style="width: 48px; height: 10px; margin: 0;"></div>
                                </div>
                            @endfor
                        </div>
                    </div>

                    {{-- Mobile Card --}}
                    <div class="skeleton-card p-3 mx-auto mb-3" style="width: 95%; border-radius: 14px;">
                        <div class="skeleton mb-3" style="width: 130px; height: 18px; border-radius: 4px;"></div>
                        <div class="row g-2 mb-3">
                            <div class="col-6"><div class="skeleton" style="width: 100%; height: 50px; border-radius: 8px;"></div></div>
                            <div class="col-6"><div class="skeleton" style="width: 100%; height: 50px; border-radius: 8px;"></div></div>
                        </div>
                        <div class="skeleton" style="width: 100%; height: 42px; border-radius: 8px; margin-bottom: 8px;"></div>
                        <div class="skeleton" style="width: 100%; height: 42px; border-radius: 8px;"></div>
                    </div>
                </div>
            </div>
            @break

        {{-- ==================== TEXT PARAGRAPH SKELETON ==================== --}}
        @case('text')
            <div {{ $attributes->merge(['class' => $class]) }}>
                @if($header)
                    <div class="skeleton skeleton-title"></div>
                @endif
                @for($l = 0; $l < $lines; $l++)
                    @php
                        $lw = ($l === $lines - 1) ? 'w-50' : (($l % 2 === 0) ? 'w-85' : 'w-100');
                    @endphp
                    <div class="skeleton skeleton-line {{ $lw }}"></div>
                @endfor
            </div>
            @break

        {{-- ==================== CIRCLE / AVATAR SKELETON ==================== --}}
        @case('circle')
        @case('avatar')
            @php
                $style = ($width ? 'width:' . $width . ';' : '') . ($height ? 'height:' . $height . ';' : '');
            @endphp
            <span {{ $attributes->merge(['class' => 'skeleton skeleton-avatar ' . $class]) }} @if($style) style="{{ $style }}" @endif></span>
            @break

        {{-- ==================== BUTTON SKELETON ==================== --}}
        @case('btn')
        @case('button')
            @php
                $style = ($width ? 'width:' . $width . ';' : '') . ($height ? 'height:' . $height . ';' : '');
            @endphp
            <span {{ $attributes->merge(['class' => 'skeleton skeleton-btn ' . $class]) }} @if($style) style="{{ $style }}" @endif></span>
            @break

        {{-- ==================== BADGE SKELETON ==================== --}}
        @case('badge')
            @php
                $style = ($width ? 'width:' . $width . ';' : '') . ($height ? 'height:' . $height . ';' : '');
            @endphp
            <span {{ $attributes->merge(['class' => 'skeleton skeleton-badge ' . $class]) }} @if($style) style="{{ $style }}" @endif></span>
            @break

        {{-- ==================== CUSTOM / BOX SKELETON (DEFAULT) ==================== --}}
        @default
            @php
                $style = '';
                if ($width) $style .= 'width:' . $width . ';';
                if ($height) $style .= 'height:' . $height . ';';
                if ($radius) $style .= 'border-radius:' . $radius . ';';
            @endphp
            <div {{ $attributes->merge(['class' => 'skeleton skeleton-box ' . $class]) }} @if($style) style="{{ $style }}" @endif></div>
            @break
    @endswitch
@endif
