@props(['todos' => [], 'forceUrl' => null])

@if(!empty($todos))
    <div class="todo-alerts-container my-3 d-flex flex-column gap-2 w-100 flex-shrink-0">
        @foreach($todos as $todo)
            @php
                $type = $todo['type'] ?? 'warning';
                
                $theme = match($type) {
                    'danger' => [
                        'border' => 'border-danger-subtle',
                        'accent' => '#ef4444',
                        'bg'     => 'rgba(239, 68, 68, 0.08)',
                        'icon_bg'=> 'rgba(239, 68, 68, 0.15)',
                        'badge'  => 'bg-danger text-white',
                    ],
                    'info' => [
                        'border' => 'border-info-subtle',
                        'accent' => '#0ea5e9',
                        'bg'     => 'rgba(14, 165, 233, 0.08)',
                        'icon_bg'=> 'rgba(14, 165, 233, 0.15)',
                        'badge'  => 'bg-info text-white',
                    ],
                    'success' => [
                        'border' => 'border-success-subtle',
                        'accent' => '#10b981',
                        'bg'     => 'rgba(16, 185, 129, 0.08)',
                        'icon_bg'=> 'rgba(16, 185, 129, 0.15)',
                        'badge'  => 'bg-success text-white',
                    ],
                    default => [
                        'border' => 'border-warning-subtle',
                        'accent' => '#f59e0b',
                        'bg'     => 'rgba(245, 158, 11, 0.08)',
                        'icon_bg'=> 'rgba(245, 158, 11, 0.15)',
                        'badge'  => 'bg-warning text-dark',
                    ],
                };
            @endphp
            <a href="{{ $forceUrl ?? $todo['action_url'] ?? '#' }}" 
               class="todo-alert-card d-flex align-items-center justify-content-between p-3 rounded-3 text-decoration-none border"
               style="background: {{ $theme['bg'] }}; border-color: rgba(255,255,255,0.08) !important; border-left: 4px solid {{ $theme['accent'] }} !important;">
                
                <div class="d-flex align-items-center gap-3">
                    <div class="todo-icon-wrapper rounded-circle d-flex align-items-center justify-content-center"
                         style="width: 42px; height: 42px; background: {{ $theme['icon_bg'] }}; color: {{ $theme['accent'] }};">
                        <span class="material-symbols-outlined fs-5">
                            {{ $todo['icon'] ?? 'notifications' }}
                        </span>
                    </div>
                    <div>
                        <div class="fw-semibold text-body d-flex align-items-center gap-2" style="font-size: 0.95rem;">
                            {{ $todo['title'] }}
                        </div>
                        <div class="text-body-secondary mt-1" style="font-size: 0.825rem;">
                            {{ $todo['message'] }}
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-3">
                    @if(!empty($todo['count']))
                        <span class="badge rounded-pill {{ $theme['badge'] }} px-2.5 py-1.5 fw-bold shadow-sm" style="font-size: 0.75rem;">
                            {{ $todo['count'] }}
                        </span>
                    @endif
                    <div class="arrow-indicator text-secondary d-flex align-items-center">
                        <span class="material-symbols-outlined fs-5">chevron_right</span>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
@endif

<style>
    .todo-alert-card {
        transition: all 0.2s ease-in-out;
        backdrop-filter: blur(8px);
    }
    .todo-alert-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2) !important;
        border-color: rgba(255, 255, 255, 0.18) !important;
    }
    .todo-alert-card:hover .arrow-indicator {
        transform: translateX(3px);
        color: #fff !important;
    }
    .arrow-indicator {
        transition: transform 0.2s ease, color 0.2s ease;
    }
</style>
