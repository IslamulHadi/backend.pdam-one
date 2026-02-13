<ul class="menu-sub">
    @if (isset($menu))
        @php $userMenus = auth()->user()->menu->pluck("id")->toArray() @endphp
        @foreach ($menu as $submenu)

            {{-- active menu method --}}
            @php
                $activeClass = null;
                $active = 'active';
                $currentRouteName =  request()->segment(2);

                if ($currentRouteName === $submenu->slug) {
                    $activeClass = 'active';
                }
                elseif (count($submenu->child)>0 && $submenu->child->where('slug',$currentRouteName)->count()>0) {
                    $activeClass = 'active open';
                }
            @endphp
            @php $haveAccess  = in_array($submenu->id,$userMenus) @endphp
            @if($haveAccess || auth()->user()->is_super())
            <li class="menu-item {{$activeClass}}">
                <a href="{{ isset($submenu->url) ? url($submenu->url) : 'javascript:void(0)' }}" class="{{ count($submenu->child)>0 ? 'menu-link menu-toggle' : 'menu-link' }}" @if (isset($submenu->target) and !empty($submenu->target)) target="_blank" @endif>
{{--                    @if (isset($submenu->icon))--}}
{{--                        <i class="menu-icon tf-icons {{ $submenu->icon }}"></i>--}}
{{--                    @endif--}}
                    <div>{{ isset($submenu->name) ? __($submenu->name) : '' }}</div>
                </a>

                {{-- submenu --}}
                @if (isset($submenu->child) && count($submenu->child)>0)
                    @include('includes.submenu',['menu' => $submenu->child])
                @endif
            </li>
            @endif
        @endforeach
    @endif
</ul>
