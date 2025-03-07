<aside
    x-data="{}"
    @if (config('filament.layout.sidebar.is_collapsible_on_desktop'))
        x-cloak
        x-bind:class="$store.sidebar.isOpen ? 'filament-sidebar-open translate-x-0 max-w-[20em] lg:max-w-[var(--sidebar-width)]' : '-translate-x-full lg:translate-x-0 lg:max-w-[var(--collapsed-sidebar-width)] rtl:lg:-translate-x-0 rtl:translate-x-full'"
    @else
        x-cloak="-lg"
        x-bind:class="$store.sidebar.isOpen ? 'filament-sidebar-open translate-x-0' : '-translate-x-full lg:translate-x-0 rtl:lg:-translate-x-0 rtl:translate-x-full'"
    @endif
    @class([
        'filament-sidebar fixed inset-y-0 left-0 rtl:left-auto rtl:right-0 z-20 flex flex-col h-screen overflow-hidden shadow-2xl transition-all bg-white lg:border-r rtl:lg:border-r-0 rtl:lg:border-l w-[var(--sidebar-width)] lg:z-0',
        'lg:translate-x-0' => ! config('filament.layout.sidebar.is_collapsible_on_desktop'),
        'dark:bg-gray-800 dark:border-gray-700' => config('filament.dark_mode'),
    ])
>
    <header @class([
        'filament-sidebar-header border-b h-[4rem] shrink-0 flex items-center justify-center',
        'dark:border-gray-700' => config('filament.dark_mode'),
    ])>
        <div
            x-cloak
            @class([
                'flex items-center jusify-center px-6 w-full',
                'lg:px-4' => config('filament.layout.sidebar.is_collapsible_on_desktop') && (config('filament.layout.sidebar.collapsed_width') !== 0),
            ])
            x-show="$store.sidebar.isOpen || @js(! config('filament.layout.sidebar.is_collapsible_on_desktop')) || @js(config('filament.layout.sidebar.collapsed_width') === 0)"
        >
            @if (config('filament.layout.sidebar.is_collapsible_on_desktop') && (config('filament.layout.sidebar.collapsed_width') !== 0))
                <button
                    type="button"
                    class="filament-sidebar-collapse-button shrink-0 hidden lg:flex items-center justify-center w-10 h-10 text-primary-500 rounded-full hover:bg-gray-500/5 focus:bg-primary-500/10 focus:outline-none"
                    x-on:click.stop="$store.sidebar.isOpen ? $store.sidebar.close() : $store.sidebar.open()"
                    x-transition:enter="lg:transition delay-100"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    data-turbo="false"
                >
                    {{-- https://github.com/codeat3/blade-google-material-design-icons/blob/main/resources/svg/menu-open-r.svg --}}

                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M0,0h24v24H0V0z" fill="none"/>
                        <path d="M4,18h11c0.55,0,1-0.45,1-1v0c0-0.55-0.45-1-1-1H4c-0.55,0-1,0.45-1,1v0C3,17.55,3.45,18,4,18z M4,13h8c0.55,0,1-0.45,1-1v0 c0-0.55-0.45-1-1-1H4c-0.55,0-1,0.45-1,1v0C3,12.55,3.45,13,4,13z M3,7L3,7c0,0.55,0.45,1,1,1h11c0.55,0,1-0.45,1-1v0 c0-0.55-0.45-1-1-1H4C3.45,6,3,6.45,3,7z M20.3,14.88L17.42,12l2.88-2.88c0.39-0.39,0.39-1.02,0-1.41l0,0 c-0.39-0.39-1.02-0.39-1.41,0l-3.59,3.59c-0.39,0.39-0.39,1.02,0,1.41l3.59,3.59c0.39,0.39,1.02,0.39,1.41,0l0,0 C20.68,15.91,20.69,15.27,20.3,14.88z"/>
                        <path d="M0,0h24v24H0V0z" fill="none"/>
                    </svg>
                </button>
            @endif

            <a
                href="{{ config('filament.home_url') }}"
                data-turbo="false"
                @class([
                    'block w-full',
                    'lg:ml-3' => config('filament.layout.sidebar.is_collapsible_on_desktop') && (config('filament.layout.sidebar.collapsed_width') !== 0),
                ])
            >
                <x-filament::brand />
            </a>
        </div>

        @if (config('filament.layout.sidebar.is_collapsible_on_desktop'))
            <button
                type="button"
                class="filament-sidebar-close-button shrink-0 flex items-center justify-center w-10 h-10 text-primary-500 rounded-full hover:bg-gray-500/5 focus:bg-primary-500/10 focus:outline-none"
                x-on:click.stop="$store.sidebar.isOpen ? $store.sidebar.close() : $store.sidebar.open()"
                x-show="(! $store.sidebar.isOpen) && @js(config('filament.layout.sidebar.collapsed_width') !== 0)"
                x-transition:enter="lg:transition delay-100"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                data-turbo="false"
            >
                <x-heroicon-o-menu class="w-6 h-6"/>
            </button>
        @endif
    </header>

    <nav class="flex-1 py-6 overflow-y-auto filament-sidebar-nav">
        <x-filament::layouts.app.sidebar.start />
        {{ \Filament\Facades\Filament::renderHook('sidebar.start') }}

        @php
            $navigation = \Filament\Facades\Filament::getNavigation();

            $collapsedNavigationGroupLabels = collect($navigation)
                ->filter(fn (\Filament\Navigation\NavigationGroup $group): bool => $group->isCollapsed())
                ->map(fn (\Filament\Navigation\NavigationGroup $group): string => $group->getLabel())
                ->values();
        @endphp

        <script>
            if (localStorage.getItem('collapsedGroups') === null) {
                localStorage.setItem('collapsedGroups', JSON.stringify(@js($collapsedNavigationGroupLabels)))
            }
        </script>

        <ul class="px-6 space-y-6">
            @foreach ($navigation as $group)
                <x-filament::layouts.app.sidebar.group :label="$group->getLabel()" :icon="$group->getIcon()" :collapsible="$group->isCollapsible()">
                    @foreach ($group->getItems() as $item)
                        <x-filament::layouts.app.sidebar.item
                            :active="$item->isActive()"
                            :icon="$item->getIcon()"
                            :url="$item->getUrl()"
                            :badge="$item->getBadge()"
                            :badgeColor="$item->getBadgeColor()"
                            :shouldOpenUrlInNewTab="$item->shouldOpenUrlInNewTab()"
                        >
                            {{ $item->getLabel() }}
                        </x-filament::layouts.app.sidebar.item>
                    @endforeach
                </x-filament::layouts.app.sidebar.group>

                @if (! $loop->last)
                    <li>
                        <div @class([
                            'border-t -mr-6 rtl:-mr-auto rtl:-ml-6',
                            'dark:border-gray-700' => config('filament.dark_mode'),
                        ])></div>
                    </li>
                @endif
            @endforeach
        </ul>

        <x-filament::layouts.app.sidebar.end />
        {{ \Filament\Facades\Filament::renderHook('sidebar.end') }}
    </nav>

    <x-filament::layouts.app.sidebar.footer />
</aside>
