{{--
    Index Menu Standard toolbar (index-menu.php section 2): search + [Ringkasan | Status | Filter | Aksi] + drawers.
    Assets: public/css/microspace/index-menu.css, public/js/microspace/index-menu.js (IdxMenu.init).

    Props
      placeholder   search placeholder
      statusLabels  [key => label]; [] = no Status button
      defaultStatus key of the active status
      sortLabels    [serverColIndex => [label, firstDir]]; [] = no sort items
      defaultSort   [serverColIndex, 'asc'|'desc']
      summary       [[label, value], ...]; [] = no Ringkasan button
      filters       2+ inputs -> drawer: [['name'=>, 'label'=>, 'type'=>'select|date', 'all'=>'Semua ..', 'options'=>[value=>label]], ...]
      filterOptions exactly 1 select -> plain dropdown: ['name'=>, 'all'=>, 'items'=>[value=>label]]
                    optional 'default'=>value: no "all" item, that item starts active (value is always sent)
    Slots
      lead          content rendered before the search bar (e.g. a type switch)
      actions       extra <li> items for the Aksi dropdown (after a divider)
--}}
@props([
    'placeholder' => 'Cari ...',
    'statusLabels' => [],
    'defaultStatus' => null,
    'sortLabels' => [],
    'defaultSort' => [0, 'asc'],
    'summary' => [],
    'filters' => [],
    'filterOptions' => [],
])
@php
    $hasActions = isset($actions) && !$actions->isEmpty();
    $hasAksi = !empty($sortLabels) || $hasActions;
    $hasGroup = !empty($summary) || !empty($filters) || !empty($filterOptions) || !empty($statusLabels) || $hasAksi;
@endphp

<div class="idx-toolbar mb-3">
    @isset($lead){{ $lead }}@endisset
    <form class="idx-search" role="search" onsubmit="return false;">
        <div class="input-group">
            <input type="search" id="idxSearch" value="{{ request('search') }}" class="form-control" maxlength="100" placeholder="{{ $placeholder }}" aria-label="Cari">
            <button class="btn btn-outline-secondary" type="submit" aria-label="Cari" id="idxSearchBtn"><i class="bi {{ request('search') ? 'bi-arrow-clockwise' : 'bi-search' }}" id="idxSearchIcon"></i></button>
        </div>
    </form>

    @if ($hasGroup)
        <div class="btn-group idx-group" role="group">
            @if (!empty($summary) && empty($filters))
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="offcanvas" data-bs-target="#idxSummaryDrawer" aria-controls="idxSummaryDrawer" aria-label="Ringkasan">
                        <i class="bi bi-bar-chart"></i><span class="d-none d-sm-inline">Ringkasan</span>
                    </button>
                </div>
            @endif
            @if (!empty($statusLabels))
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Status">
                        <i class="bi bi-funnel"></i><span class="d-none d-sm-inline" id="idxStatusLabel">{{ $statusLabels[$defaultStatus] }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        @foreach ($statusLabels as $key => $label)
                            <li><a class="dropdown-item {{ (string) $defaultStatus === (string) $key ? 'active' : '' }}" href="#" data-idx-status="{{ $key }}" data-label="{{ $label }}">{{ $label }}</a></li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if (!empty($filterOptions))
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Filter">
                        <i class="bi bi-sliders"></i><span class="d-none d-sm-inline" id="idxFilterLabel">{{ $filterOptions['default'] ?? false ? $filterOptions['items'][$filterOptions['default']] : $filterOptions['all'] }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        @if (!isset($filterOptions['default']))
                            <li><a class="dropdown-item active" href="#" data-idx-filter-opt="{{ $filterOptions['name'] }}" data-value="" data-label="{{ $filterOptions['all'] }}">{{ $filterOptions['all'] }}</a></li>
                        @endif
                        @foreach ($filterOptions['items'] as $value => $label)
                            <li><a class="dropdown-item {{ ($filterOptions['default'] ?? null) === (string) $value ? 'active' : '' }}" href="#" data-idx-filter-opt="{{ $filterOptions['name'] }}" data-value="{{ $value }}" data-label="{{ $label }}">{{ $label }}</a></li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if (!empty($filters))
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-secondary position-relative" data-bs-toggle="offcanvas" data-bs-target="#idxFilterDrawer" aria-controls="idxFilterDrawer" aria-label="Filter">
                        <i class="bi bi-sliders"></i><span class="d-none d-sm-inline">Filter</span>
                        <span id="idxFilterDot" class="position-absolute top-0 start-100 translate-middle p-1 bg-primary rounded-circle d-none"></span>
                    </button>
                </div>
            @endif
            @if ($hasAksi)
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Aksi">
                        @if (!empty($sortLabels))
                            <i class="bi bi-sort-{{ $defaultSort[1] === 'desc' ? 'down' : 'up' }}" id="idxSortIcon"></i><span class="d-none d-sm-inline" id="idxSortLabel">{{ $sortLabels[$defaultSort[0]][0] }}</span>
                        @else
                            <i class="bi bi-three-dots"></i><span class="d-none d-sm-inline">Aksi</span>
                        @endif
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        @foreach ($sortLabels as $col => [$label, $firstDir])
                            <li><a class="dropdown-item {{ $defaultSort[0] === $col ? 'active' : '' }}" href="#" data-idx-sort="{{ $col }}" data-label="{{ $label }}" data-first-dir="{{ $firstDir }}">{{ $label }}</a></li>
                        @endforeach
                        @if ($hasActions)
                            @if (!empty($sortLabels))<li><hr class="dropdown-divider"></li>@endif
                            {{ $actions }}
                        @endif
                    </ul>
                </div>
            @endif
        </div>
    @endif
</div>

@if (!empty($filters))
    <x-side-drawer id="idxFilterDrawer" title="Filter" icon="bi-sliders">
        @if (!empty($summary))
            <div class="d-flex flex-column gap-2 pt-2 pb-3 mb-2 border-bottom">
                <div class="fw-semibold small text-uppercase text-secondary"><i class="bi bi-bar-chart me-1"></i>Ringkasan</div>
                @foreach ($summary as $i => [$label, $value])
                    <div class="border rounded-3 p-3">
                        <div class="text-secondary small">{{ $label }}</div>
                        <div class="fs-5 fw-bold" data-idx-summary="{{ $i }}">{{ $value }}</div>
                    </div>
                @endforeach
            </div>
        @endif
        <div class="row g-2 py-2">
            @foreach ($filters as $f)
                <div class="col-12">
                    <label class="form-label small mb-1" for="idxF_{{ $f['name'] }}">{{ $f['label'] }}</label>
                    @if (($f['type'] ?? 'select') === 'date')
                        <input type="date" id="idxF_{{ $f['name'] }}" class="form-control" data-idx-filter="{{ $f['name'] }}">
                    @else
                        <select id="idxF_{{ $f['name'] }}" class="form-select" data-idx-filter="{{ $f['name'] }}">
                            <option value="">{{ $f['all'] ?? 'Semua' }}</option>
                            @foreach ($f['options'] as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
            @endforeach
        </div>
        <x-slot:footer>
            <button type="button" class="btn btn-outline-secondary" id="idxFilterReset">Reset Filter</button>
            <button type="button" class="btn btn-primary ms-auto" id="idxFilterApply">Terapkan Filter</button>
        </x-slot:footer>
    </x-side-drawer>
@endif

@if (!empty($summary) && empty($filters))
    <x-side-drawer id="idxSummaryDrawer" title="Ringkasan" icon="bi-bar-chart">
        <div class="d-flex flex-column gap-2 py-2">
            @foreach ($summary as $i => [$label, $value])
                <div class="border rounded-3 p-3">
                    <div class="text-secondary small">{{ $label }}</div>
                    <div class="fs-5 fw-bold" data-idx-summary="{{ $i }}">{{ $value }}</div>
                </div>
            @endforeach
        </div>
    </x-side-drawer>
@endif
