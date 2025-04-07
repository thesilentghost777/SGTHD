<nav x-data="{ open: false }" class="bg-gray-50 border-b border-gray-200 shadow-sm">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-10">
        <div class="flex justify-between h-16 items-center">
            <!-- Left side with logo -->

            <div class="w-1/4" style="position: relative; left: 5cm; margin-right: 20px;">
                <div class="shrink-0">
                    <img src="{{ asset('assets/logos/TH_LOGO.png') }}" alt="TH Logo" style="height: 60px; width: auto; display: inline-block;">
                </div>
            </div>


            <!-- Centered navigation links -->
            <div class="flex-1 flex justify-center">
                <div class="hidden space-x-6 sm:flex">
                  <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')"
                  class="px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200 hover:bg-gray-100 hover:text-blue-600
                  @if(request()->routeIs('dashboard')) bg-blue-50 text-blue-600 @endif">
                    {{ __('Accueil') }}
                  </x-nav-link>
                  <x-nav-link :href="route('workspace.redirect')" :active="request()->routeIs('workspace.redirect')"
                  class="px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200 hover:bg-gray-100 hover:text-blue-600
                  @if(request()->routeIs('workspace.redirect')) bg-blue-50 text-blue-600 @endif">
                    {{ __('Espace de travail') }}
                  </x-nav-link>
                  <x-nav-link :href="route('about')" :active="request()->routeIs('about')"
                  class="px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200 hover:bg-gray-100 hover:text-blue-600
                  @if(request()->routeIs('about')) bg-blue-50 text-blue-600 @endif">
                    {{ __('À propos') }}
                  </x-nav-link>
                  <x-nav-link :href="route('account-access.return')" :active="request()->routeIs('account-access.return')"
                  class="px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200 hover:bg-gray-100 hover:text-blue-600
                  @if(request()->routeIs('account-access.return')) bg-blue-50 text-blue-600 @endif">
                    {{ __('Retour au compte original') }}
                  </x-nav-link>
                  <x-nav-link :href="route('workspace.switcher')" :active="request()->routeIs('workspace.switcher')"
                  class="px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200 hover:bg-gray-100 hover:text-blue-600
                  @if(request()->routeIs('workspace.switcher')) bg-blue-50 text-blue-600 @endif">
                    {{ __('Changement de mode') }}
                  </x-nav-link>
                </div>
            </div>

            <!-- Right side with settings -->
            <div class="w-1/4 flex justify-end">
                <div class="hidden sm:flex sm:items-center">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-600 bg-white hover:bg-gray-100 hover:text-gray-800 transition ease-in-out duration-150">
                                <div>{{ Auth::user()->name }}</div>
                                <div class="ms-2">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            <!-- Hamburger -->
            <div class="flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-600 hover:text-gray-800 hover:bg-gray-200 transition duration-150">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
<!-- Responsive Settings Options -->
<div class="pt-4 pb-1 border-t border-gray-200">
    <div class="px-4">
        <div class="font-medium text-base text-gray-900">{{ Auth::user()->name }}</div>
        <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
    </div>
    <div class="mt-3 space-y-1">
        <x-responsive-nav-link :href="route('profile.edit')">
            {{ __('Profile') }}
        </x-responsive-nav-link>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                {{ __('Log Out') }}
            </x-responsive-nav-link>
        </form>
    </div>
</div>
</div>    </div>
</nav>


