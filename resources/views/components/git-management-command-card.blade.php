@props([
    'server',
    'action',
    'defaults',
    'label',
    'description' => null,
])

@php
    $baseRemote = $server->remote_name ?? ($defaults['remote'] ?? 'origin');
    $baseBranch = $server->default_branch ?? ($defaults['branch'] ?? 'main');
    $needsRemote = in_array($action, ['fetch', 'pull'], true);
    $needsBranch = in_array($action, ['pull', 'checkout'], true);
@endphp

<form method="POST" action="{{ route('git-management.command', $server) }}" class="bg-gray-50 border border-gray-200 rounded-lg p-4 space-y-3">
    @csrf
    <input type="hidden" name="action" value="{{ $action }}">
    <div>
        <h5 class="text-sm font-semibold text-gray-700">{{ $label }}</h5>
        @if ($description)
            <p class="text-xs text-gray-500">{{ $description }}</p>
        @endif
    </div>

    @if ($needsRemote)
        <div>
            <label for="remote-{{ $action }}-{{ $server->id }}" class="text-xs text-gray-500 uppercase tracking-wide">{{ __('Remote') }}</label>
            <input type="text" id="remote-{{ $action }}-{{ $server->id }}" name="remote" value="{{ $baseRemote }}" class="mt-1 w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm" required>
        </div>
    @else
        <input type="hidden" name="remote" value="{{ $baseRemote }}">
    @endif

    @if ($needsBranch)
        <div>
            <label for="branch-{{ $action }}-{{ $server->id }}" class="text-xs text-gray-500 uppercase tracking-wide">{{ __('Branch') }}</label>
            <input type="text" id="branch-{{ $action }}-{{ $server->id }}" name="branch" value="{{ $baseBranch }}" class="mt-1 w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm" required>
        </div>
    @else
        <input type="hidden" name="branch" value="{{ $baseBranch }}">
    @endif

    <div class="flex justify-end">
        <button type="submit" class="inline-flex items-center px-3 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            {{ __('Execute') }}
        </button>
    </div>
</form>
