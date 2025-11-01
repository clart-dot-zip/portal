<?php

namespace App\Http\Controllers;

use App\Models\PimActivation;
use App\Models\User;
use App\Services\Pim\Exceptions\PimException;
use App\Services\Pim\PimService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PimController extends Controller
{
    private PimService $pimService;

    public function __construct(PimService $pimService)
    {
        $this->pimService = $pimService;
    }

    public function index(Request $request): View
    {
        $status = strtolower((string) $request->get('status', ''));
        $search = trim((string) $request->get('search', ''));

        $validStatuses = ['active', 'pending', 'failed', 'revoked', 'expired'];
        if (!in_array($status, $validStatuses, true)) {
            $status = null;
        }

        $activationsQuery = PimActivation::with(['user', 'initiatedBy'])
            ->latest('activated_at');

        if ($status) {
            if ($status === 'active') {
                $activationsQuery->where('status', 'active')->whereNull('deactivated_at');
            } elseif ($status === 'revoked') {
                $activationsQuery->where('status', 'revoked');
            } else {
                $activationsQuery->where('status', $status);
            }
        }

        if ($search !== '') {
            $activationsQuery->where(function ($query) use ($search) {
                $query
                    ->where('reason', 'like', "%{$search}%")
                    ->orWhere('role', 'like', "%{$search}%")
                    ->orWhere('server_username_snapshot', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('username', 'like', "%{$search}%");
                    });
            });
        }

        $activations = $activationsQuery->paginate(20)->withQueryString();

        $baseQuery = PimActivation::query();
        $summary = [
            'total' => (clone $baseQuery)->count(),
            'active' => (clone $baseQuery)->where('status', 'active')->whereNull('deactivated_at')->count(),
            'pending' => (clone $baseQuery)->where('status', 'pending')->count(),
            'failed' => (clone $baseQuery)->where('status', 'failed')->count(),
            'revoked' => (clone $baseQuery)->where('status', 'revoked')->count(),
            'expired' => (clone $baseQuery)->where('status', 'expired')->count(),
        ];

        $statusOptions = [
            '' => 'All statuses',
            'active' => 'Active',
            'pending' => 'Pending',
            'failed' => 'Failed',
            'revoked' => 'Revoked',
            'expired' => 'Expired',
        ];

        $roleCatalog = $this->pimService->roleCatalog()->keyBy('key');

        return view('pim.index', [
            'activations' => $activations,
            'summary' => $summary,
            'statusOptions' => $statusOptions,
            'currentStatus' => $status,
            'search' => $search,
            'pimEnabled' => $this->pimService->isEnabled(),
            'pimOperational' => $this->pimService->isOperational(),
            'roleCatalog' => $roleCatalog,
        ]);
    }

    public function activate(Request $request, string $authentikId): RedirectResponse
    {
        $request->validate([
            'role' => 'required|string',
            'duration_minutes' => 'required|integer|min:1',
            'reason' => 'required|string|max:500',
        ]);

        $localUser = User::where('authentik_id', $authentikId)->first();

        if (!$localUser) {
            return redirect()
                ->route('users.show', ['id' => $authentikId, 'tab' => 'pim'])
                ->with('error', 'User must be synced locally before activating PIM roles.');
        }

        $roleKey = $request->string('role')->toString();
        $duration = (int) $request->integer('duration_minutes');
        $reason = (string) $request->string('reason');

        try {
            $this->pimService->activate($localUser, $roleKey, $duration, $reason, Auth::user());
        } catch (PimException $exception) {
            return redirect()
                ->route('users.show', ['id' => $authentikId, 'tab' => 'pim'])
                ->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('users.show', ['id' => $authentikId, 'tab' => 'pim'])
            ->with('success', 'Privileged access activated successfully.');
    }

    public function deactivate(Request $request, string $authentikId, PimActivation $activation): RedirectResponse
    {
        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $localUser = User::where('authentik_id', $authentikId)->first();

        if (!$localUser || $activation->user_id !== $localUser->id) {
            return redirect()
                ->route('users.show', ['id' => $authentikId, 'tab' => 'pim'])
                ->with('error', 'Activation record does not match the selected user.');
        }

        $reason = $request->filled('reason')
            ? 'Manual revoke: ' . $request->string('reason')->toString()
            : 'Manually revoked';

        try {
            $this->pimService->deactivate($activation, $reason, Auth::user());
        } catch (PimException $exception) {
            return redirect()
                ->route('users.show', ['id' => $authentikId, 'tab' => 'pim'])
                ->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('users.show', ['id' => $authentikId, 'tab' => 'pim'])
            ->with('success', 'Privileged access revoked successfully.');
    }
}
