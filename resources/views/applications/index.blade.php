@section('title', 'Applications - ' . config('app.name'))

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Applications') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('dashboard') }}" 
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Dashboard
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Status Messages -->
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            @if(isset($error))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ $error }}</span>
                </div>
            @endif

            <!-- Search and Controls -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                        <!-- Search Form -->
                        <div class="flex-1 max-w-lg">
                            <form method="GET" action="{{ route('applications.index') }}" class="flex">
                                <div class="relative flex-1">
                                    <input type="text" 
                                           name="search" 
                                           value="{{ $search ?? '' }}"
                                           placeholder="Search applications..." 
                                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-l-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </div>
                                <button type="submit" 
                                        class="inline-flex items-center px-4 py-2 border border-l-0 border-gray-300 rounded-r-md bg-gray-50 text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                    Search
                                </button>
                            </form>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center space-x-3">
                            @if($search)
                                <a href="{{ route('applications.index') }}" 
                                   class="text-gray-600 hover:text-gray-900 text-sm">
                                    Clear Search
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Applications List -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-medium text-gray-900">
                            @if($search)
                                Search Results for "{{ $search }}"
                            @else
                                All Applications
                            @endif
                        </h3>
                        
                        @if(isset($pagination))
                            <div class="text-sm text-gray-500">
                                Showing {{ count($applications) }} of {{ number_format($pagination['total']) }} applications
                            </div>
                        @endif
                    </div>

                    @if(count($applications) > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($applications as $application)
                                <!-- Debug: PK={{ $application['pk'] ?? 'NO_PK' }}, Name={{ $application['name'] ?? 'NO_NAME' }} -->
                                <div class="border border-gray-200 rounded-lg p-6 hover:border-gray-300 transition-colors">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h4 class="text-lg font-medium text-gray-900 mb-2">
                                                {{ $application['name'] }}
                                            </h4>
                                            
                                            @if(isset($application['slug']) && $application['slug'])
                                                <p class="text-sm text-gray-600 mb-2">
                                                    <span class="font-medium">Slug:</span> {{ $application['slug'] }}
                                                </p>
                                            @endif
                                            
                                            @if(isset($application['meta_description']) && $application['meta_description'])
                                                <p class="text-sm text-gray-600 mb-3">
                                                    {{ Str::limit($application['meta_description'], 100) }}
                                                </p>
                                            @endif

                                            <!-- Application Status -->
                                            <div class="flex items-center space-x-4 mb-4">
                                                @if(isset($application['provider']) && $application['provider'])
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        Provider: {{ $application['provider_obj']['name'] ?? 'Configured' }}
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        No Provider
                                                    </span>
                                                @endif
                                                
                                                @if(isset($application['meta_launch_url']) && $application['meta_launch_url'])
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        Launchable
                                                    </span>
                                                @endif
                                            </div>

                                            <!-- Action Buttons -->
                                            <div class="flex space-x-2">
                                                <a href="{{ route('applications.show', $application['pk']) }}" 
                                                   class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                    View Details
                                                </a>
                                                <a href="{{ route('applications.edit', $application['pk']) }}" 
                                                   class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                    Manage Access
                                                </a>
                                                @if(isset($application['meta_launch_url']) && $application['meta_launch_url'])
                                                    <a href="{{ $application['meta_launch_url'] }}" 
                                                       target="_blank"
                                                       class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                        Launch
                                                        <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                                        </svg>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if(isset($pagination) && $pagination['last_page'] > 1)
                            <div class="mt-6 flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
                                <div class="flex flex-1 justify-between sm:hidden">
                                    @if($pagination['current_page'] > 1)
                                        <a href="{{ route('applications.index', array_merge(request()->query(), ['page' => $pagination['current_page'] - 1])) }}" 
                                           class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                            Previous
                                        </a>
                                    @endif
                                    
                                    @if($pagination['current_page'] < $pagination['last_page'])
                                        <a href="{{ route('applications.index', array_merge(request()->query(), ['page' => $pagination['current_page'] + 1])) }}" 
                                           class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                            Next
                                        </a>
                                    @endif
                                </div>
                                
                                <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                                    <div>
                                        <p class="text-sm text-gray-700">
                                            Showing page {{ $pagination['current_page'] }} of {{ $pagination['last_page'] }}
                                            ({{ number_format($pagination['total']) }} total applications)
                                        </p>
                                    </div>
                                    <div>
                                        <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                                            @if($pagination['current_page'] > 1)
                                                <a href="{{ route('applications.index', array_merge(request()->query(), ['page' => $pagination['current_page'] - 1])) }}" 
                                                   class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                                                    Previous
                                                </a>
                                            @endif
                                            
                                            @for($i = max(1, $pagination['current_page'] - 2); $i <= min($pagination['last_page'], $pagination['current_page'] + 2); $i++)
                                                <a href="{{ route('applications.index', array_merge(request()->query(), ['page' => $i])) }}" 
                                                   class="relative inline-flex items-center px-4 py-2 text-sm font-semibold {{ $i == $pagination['current_page'] ? 'bg-indigo-600 text-white focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600' : 'text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0' }}">
                                                    {{ $i }}
                                                </a>
                                            @endfor
                                            
                                            @if($pagination['current_page'] < $pagination['last_page'])
                                                <a href="{{ route('applications.index', array_merge(request()->query(), ['page' => $pagination['current_page'] + 1])) }}" 
                                                   class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                                                    Next
                                                </a>
                                            @endif
                                        </nav>
                                    </div>
                                </div>
                            </div>
                        @endif

                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No applications found</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                @if($search)
                                    Try adjusting your search terms or clearing the search to see all applications.
                                @else
                                    There are no applications configured in Authentik yet.
                                @endif
                            </p>
                            @if($search)
                                <div class="mt-6">
                                    <a href="{{ route('applications.index') }}" 
                                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        View All Applications
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>