@php
    $url = '';

    $MyNavBar = \Menu::make('MenuList', function ($menu) use($url){
        //Admin Dashboard
        $menu->add('<span>'.__('message.dashboard').'</span>', ['route' => 'tracker.index'])
            ->prepend('<i class="fas fa-home"></i>')
            ->link->attr(['class' => '']);
        })->filter(function ($item) {
            return $item;
        });
@endphp

<div class="mm-sidebar sidebar-default">
    <div class="mm-sidebar-logo d-flex align-items-center justify-content-between">
        <a href="{{ route('tracker.index') }}" class="header-logo">
            {{__('Drivers map')}}
        </a>
        <div class="side-menu-bt-sidebar">
            <i class="fas fa-bars wrapper-menu"></i>
        </div>
    </div>

    <div class="data-scrollbar" data-scroll="1">
        <nav class="mm-sidebar-menu">
            <ul id="mm-sidebar-toggle" class="side-menu">
                @include(config('laravel-menu.views.bootstrap-items'), ['items' => $MyNavBar->roots()])
            </ul>
        </nav>
        <div class="pt-5 pb-2"></div>
    </div>
</div>
