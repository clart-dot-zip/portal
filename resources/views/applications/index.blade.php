@section('title', 'Applications - ' . config('app.name'))
@section('page_title', 'Applications')

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold text-fluent-neutral-30">{{ __('Applications') }}</h1>
                <p class="text-sm text-fluent-neutral-26 mt-1">{{ __('Browse and manage your applications') }}</p>
            </div>
        </div>
    </x-slot>
    @if(session('success'))
        <x-fluent-card padding="small" class="bg-green-50 border-green-200 mb-4">
            <div class="flex items-start gap-3">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor" class="text-fluent-success flex-shrink-0">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <p class="text-sm font-medium text-fluent-success flex-1">{{ session('success') }}</p>
            </div>
        </x-fluent-card>
    @endif

    @if(session('error') || isset($error))
        <x-fluent-card padding="small" class="bg-red-50 border-red-200 mb-4">
            <div class="flex items-start gap-3">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor" class="text-fluent-error flex-shrink-0">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <p class="text-sm font-medium text-fluent-error flex-1">{{ session('error') ?? $error ?? '' }}</p>
            </div>
        </x-fluent-card>
    @endif

    <x-fluent-card title="Search Applications" class="mb-4">
        <form method="GET" action="{{ route('applications.index') }}" class="flex flex-col md:flex-row gap-3 items-end">
            <div class="flex-1">
                <x-fluent-input
                    type="text"
                    name="search"
                    :value="$search ?? ''"
                    placeholder="{{ __('Search applications...') }}"
                    icon='<svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/></svg>'
                />
            </div>
            <div class="flex gap-2">
                <x-fluent-button type="submit" variant="primary">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                    </svg>
                    {{ __('Search') }}
                </x-fluent-button>
                @if($search ?? false)
                    <x-fluent-button variant="secondary" onclick="window.location.href='{{ route('applications.index') }}'">
                        {{ __('Clear') }}
                    </x-fluent-button>
                @endif
            </div>
        </form>
    </x-fluent-card>

            <!-- Applications List -->
            <x-fluent-card>
                <x-slot name="header">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                        <div>
                            <h3 class="text-base font-semibold text-fluent-neutral-30">
                                @if($search)
                                    {{ __('Search Results for ":term"', ['term' => $search]) }}
                                @else
                                    {{ __('All Applications') }}
                                @endif
                            </h3>
                            @if(isset($pagination))
                                <p class="text-xs text-fluent-neutral-26 mt-0.5">
                                    {{ __('Showing :count of :total applications', ['count' => count($applications), 'total' => number_format($pagination['total'])]) }}
                                </p>
                            @endif
                        </div>
                    </div>
                </x-slot>

                @if(count($applications) > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($applications as $application)
                            <div class="fluent-card bg-white border border-fluent-neutral-10 rounded-lg p-4 hover:shadow-md hover:border-fluent-brand-60 transition-all duration-200">
                                <div class="flex items-start gap-3 mb-3">
                                    <div class="w-12 h-12 rounded-lg flex items-center justify-center flex-shrink-0 bg-gradient-to-br from-fluent-brand-60 to-fluent-brand-70 text-white">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M4 4h7v7H4V4zm9 0h7v7h-7V4zM4 13h7v7H4v-7zm9 0h7v7h-7v-7z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-semibold text-fluent-neutral-30 mb-1">
                                            {{ $application['name'] }}
                                        </h4>
                                        @if(isset($application['slug']) && $application['slug'])
                                            <p class="text-xs text-fluent-neutral-26">{{ $application['slug'] }}</p>
                                        @endif
                                    </div>
                                </div>
                                
                                @if(isset($application['meta_description']) && $application['meta_description'])
                                    <p class="text-xs text-fluent-neutral-26 mb-3 line-clamp-2">
                                        {{ Str::limit($application['meta_description'], 100) }}
                                    </p>
                                @endif

                                <div class="flex flex-wrap gap-1.5 mb-3">
                                    @if(isset($application['provider']) && $application['provider'])
                                        <x-fluent-badge variant="success" size="small">
                                            {{ $application['provider_obj']['name'] ?? 'Configured' }}
                                        </x-fluent-badge>
                                    @else
                                        <x-fluent-badge variant="warning" size="small">No Provider</x-fluent-badge>
                                    @endif
                                    
                                    @if(isset($application['meta_launch_url']) && $application['meta_launch_url'])
                                        <x-fluent-badge variant="info" size="small">Launchable</x-fluent-badge>
                                    @endif
                                </div>

                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('applications.show', $application['pk']) }}" 
                                       class="flex-1 text-center px-3 py-1.5 bg-fluent-brand-60 hover:bg-fluent-brand-70 text-white text-xs font-semibold rounded transition-colors">
                                        Details
                                    </a>
                                    <a href="{{ route('applications.edit', $application['pk']) }}" 
                                       class="flex-1 text-center px-3 py-1.5 bg-orange-600 hover:bg-orange-700 text-white text-xs font-semibold rounded transition-colors">
                                        Manage
                                    </a>
                                    @if(isset($application['meta_launch_url']) && $application['meta_launch_url'])
                                        <a href="{{ $application['meta_launch_url'] }}" 
                                           target="_blank"
                                           class="flex items-center justify-center px-3 py-1.5 bg-fluent-success hover:bg-green-700 text-white text-xs font-semibold rounded transition-colors">
                                            <svg width="12" height="12" viewBox="0 0 12 12" fill="currentColor">
                                                <path d="M10.5 1.5h-3a.5.5 0 000 1h1.793L4.646 7.146a.5.5 0 00.708.708L10 3.207V5a.5.5 0 001 0V2a.5.5 0 00-.5-.5z"/>
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>


                    @if(isset($pagination) && $pagination['last_page'] > 1)
                        <div class="flex flex-col md:flex-row items-center justify-between gap-3 pt-4 border-t border-fluent-neutral-10 mt-6">
                            <p class="text-sm text-fluent-neutral-26">
                                {{ __('Page :current of :last (:total applications)', [
                                    'current' => $pagination['current_page'],
                                    'last' => $pagination['last_page'],
                                    'total' => number_format($pagination['total']),
                                ]) }}
                            </p>
                            <div class="flex gap-2">
                                @if($pagination['current_page'] > 1)
                                    <x-fluent-button 
                                        variant="secondary" 
                                        size="small"
                                        onclick="window.location.href='{{ route('applications.index', array_merge(request()->query(), ['page' => $pagination['current_page'] - 1])) }}'"
                                    >
                                        {{ __('Previous') }}
                                    </x-fluent-button>
                                @endif
                                @if($pagination['current_page'] < $pagination['last_page'])
                                    <x-fluent-button 
                                        variant="secondary" 
                                        size="small"
                                        onclick="window.location.href='{{ route('applications.index', array_merge(request()->query(), ['page' => $pagination['current_page'] + 1])) }}'"
                                    >
                                        {{ __('Next') }}
                                    </x-fluent-button>
                                @endif
                            </div>
                        </div>
                    @endif
                @else
                    <div class="text-center py-12">
                        <svg width="64" height="64" viewBox="0 0 64 64" fill="currentColor" class="mx-auto text-fluent-neutral-22 mb-4">
                            <path d="M8 8h20v20H8V8zm28 0h20v20H36V8zM8 36h20v20H8V36zm28 0h20v20H36V36z"/>
                        </svg>
                        <h5 class="text-lg font-semibold text-fluent-neutral-30 mb-2">{{ __('No applications found') }}</h5>
                        <p class="text-sm text-fluent-neutral-26 mb-4">
                            @if($search)
                                {{ __('Try adjusting your search terms or clearing the search.') }}
                            @else
                                {{ __('There are no applications configured in Authentik yet.') }}
                            @endif
                        </p>
                        @if($search)
                            <x-fluent-button variant="primary" onclick="window.location.href='{{ route('applications.index') }}'">
                                {{ __('View All Applications') }}
                            </x-fluent-button>
                        @endif
                    </div>
                @endif
            </x-fluent-card>
</x-app-layout>