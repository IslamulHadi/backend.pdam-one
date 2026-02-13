<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand">
        <a href="{{ route('admin.home') }}" class="app-brand-link">
            <img src="{{ asset('pdam.png') }}" alt="logo" width="160" height="40">
        </a>
    </div>

    <div class="menu-divider mt-0"></div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Page -->
        <li class="menu-item">
            <a href="{{ config('iwsa.sso_url') . '/admin/home' }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home"></i>
                <div data-i18n="My Profile">PDAMOne Home</div>
            </a>
        </li>
        <li class="menu-item {{ request()->routeIs('admin.home') ? 'active' : '' }}">
            <a href="{{ route('admin.home') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-chart"></i>
                <div data-i18n="Dashboard">Dashboard</div>
            </a>
        </li>
        {{--        <li class="menu-header small text-uppercase"><span class="menu-header-text">Menu Aplikasi</span></li> --}}
        @php $userMenus = auth()->user()->menu->pluck("id")->toArray() @endphp
        @php $parentMenus = auth()->user()->is_super() ? \App\Models\Menu::whereNull('parent_id')->where('aplikasi', 'website')->orderBy('created_at')->get() : auth()->user()->menu->whereNull('parent_id') @endphp
        @foreach ($parentMenus->groupBy('aplikasi') as $app => $menus)
            <li class="menu-header small text-uppercase"><span class="menu-header-text">Menu {{ $app }}</span>
            </li>
            @foreach ($menus as $menu)
                {{-- adding active and open class if child is active --}}
                {{-- active menu method --}}

                @php

                    $activeClass = null;
                    $currentRouteName = request()->segment(2);

                    if ($currentRouteName === $menu->slug) {
                        $activeClass = 'active';
                    } elseif (count($menu->child) > 0) {
                        if ($menu->child->where('slug', $currentRouteName)->count() > 0) {
                            $activeClass = 'active open';
                        } else {
                            $submenuChild = $menu->child->filter(
                                fn($item) => $item->child->where('slug', $currentRouteName)->count() > 0,
                            );
                            $activeClass = count($submenuChild) > 0 ? 'active open' : null;
                        }
                    }

                @endphp

                @php $haveAccess  = in_array($menu->id,$userMenus) @endphp
                @if ($haveAccess || auth()->user()->is_super())
                    {{-- main menu --}}
                    <li class="menu-item {{ $activeClass }}">
                        <a href="{{ isset($menu->url) ? url($menu->url) : 'javascript:void(0);' }}"
                            class="{{ count($menu->child) > 0 ? 'menu-link menu-toggle' : 'menu-link' }}"
                            @if (isset($menu->target) and !empty($menu->target)) target="_blank" @endif>
                            @isset($menu->icon)
                                <i class="menu-icon tf-icons {{ $menu->icon }}"></i>
                            @endisset
                            <div>{{ isset($menu->name) ? __($menu->name) : '' }}</div>
                        </a>

                        {{-- submenu --}}
                        @if (count($menu->child) > 0)
                            @include('includes.submenu', ['menu' => $menu->child])
                        @endif
                    </li>
                @endif
            @endforeach
        @endforeach
    </ul>
</aside>
<!-- / Menu -->

<form id="frm-logout" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>
