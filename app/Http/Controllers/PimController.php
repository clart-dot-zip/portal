<?php

namespace App\Http\Controllers;

use App\Http\Requests\Pim\AssignPimGroupRequest;
use App\Http\Requests\Pim\StorePimGroupRequest;
use App\Http\Requests\Pim\UpdatePimGroupRequest;
use App\Models\PimActivation;
use App\Models\PimGroup;
use App\Models\PimPermission;
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

        $activationsQuery = PimActivation::with(['user', 'initiatedBy', 'pimGroup'])
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
                    ->orWhereHas('pimGroup', function ($groupQuery) use ($search) {
                        $groupQuery->where('name', 'like', "%{$search}%");
                    })
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

        $groups = PimGroup::with('permissions')->orderBy('name')->get();
        $permissions = PimPermission::orderBy('label')->get();

        return view('pim.index', [
            'activations' => $activations,
            'summary' => $summary,
            'statusOptions' => $statusOptions,
            'currentStatus' => $status,
            'search' => $search,
            'pimEnabled' => $this->pimService->isEnabled(),
            'pimOperational' => $this->pimService->isOperational(),
            'groups' => $groups,
            'permissions' => $permissions,
        ]);
    }

    public function activate(Request $request, string $authentikId): RedirectResponse
    {
        $request->validate([
            'pim_group_id' => 'required|integer|exists:pim_groups,id',
            'duration_minutes' => 'required|integer|min:1',
            'reason' => 'required|string|max:500',
        ]);

        $localUser = User::where('authentik_id', $authentikId)->first();

        if (!$localUser) {
            return redirect()
                ->route('users.show', ['id' => $authentikId, 'tab' => 'pim'])
                ->with('error', 'User must be synced locally before activating PIM groups.');
        }

        $group = $this->pimService->getGroupById((int) $request->integer('pim_group_id'));
        $duration = (int) $request->integer('duration_minutes');
        $reason = (string) $request->string('reason');

        try {
            $this->pimService->activate($localUser, $group, $duration, $reason, Auth::user());
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

    public function storeGroup(StorePimGroupRequest $request): RedirectResponse
    {
        $group = PimGroup::create([
            'name' => $request->string('name')->toString(),
            'slug' => $request->string('slug')->toString(),
            'description' => $request->input('description'),
            'default_duration_minutes' => (int) $request->integer('default_duration_minutes'),
            'min_duration_minutes' => (int) $request->integer('min_duration_minutes'),
            'max_duration_minutes' => (int) $request->integer('max_duration_minutes'),
            'auto_approve' => (bool) $request->boolean('auto_approve'),
        ]);

        $group->permissions()->sync($request->input('permissions', []));

        return redirect()->route('pim.index')->with('success', 'PIM group created successfully.');
    }

    public function updateGroup(UpdatePimGroupRequest $request, PimGroup $group): RedirectResponse
    {
        $group->update([
            'name' => $request->string('name')->toString(),
            'slug' => $request->string('slug')->toString(),
            'description' => $request->input('description'),
            'default_duration_minutes' => (int) $request->integer('default_duration_minutes'),
            'min_duration_minutes' => (int) $request->integer('min_duration_minutes'),
            'max_duration_minutes' => (int) $request->integer('max_duration_minutes'),
            'auto_approve' => (bool) $request->boolean('auto_approve'),
        ]);

        $group->permissions()->sync($request->input('permissions', []));

        return redirect()->route('pim.index')->with('success', 'PIM group updated successfully.');
    }

    public function destroyGroup(PimGroup $group): RedirectResponse
    {
        if ($group->activations()->exists()) {
            return redirect()->route('pim.index')->with('error', 'Cannot delete a group while activations reference it.');
        }

        $group->permissions()->detach();
        $group->users()->detach();
        $group->delete();

        return redirect()->route('pim.index')->with('success', 'PIM group deleted successfully.');
    }

    public function assignGroup(AssignPimGroupRequest $request, string $authentikId): RedirectResponse
    {
        $localUser = User::where('authentik_id', $authentikId)->first();

        if (!$localUser) {
            return redirect()
                ->route('users.show', ['id' => $authentikId, 'tab' => 'pim'])
                ->with('error', 'User must be synced locally before assigning groups.');
        }

        $groupId = (int) $request->integer('pim_group_id');
        $localUser->pimGroups()->syncWithoutDetaching([$groupId]);

        return redirect()
            ->route('users.show', ['id' => $authentikId, 'tab' => 'pim'])
            ->with('success', 'PIM group assigned to user.');
    }

    public function removeGroup(string $authentikId, PimGroup $group): RedirectResponse
    {
        $localUser = User::where('authentik_id', $authentikId)->first();

        if (!$localUser) {
            return redirect()
                ->route('users.show', ['id' => $authentikId, 'tab' => 'pim'])
                ->with('error', 'User must be synced locally before modifying assignments.');
        }

        $localUser->pimGroups()->detach($group->id);

        $activeActivation = $this->pimService->getActiveActivation($localUser, $group);
        if ($activeActivation) {
            try {
                $this->pimService->deactivate($activeActivation, 'Group assignment revoked', Auth::user());
            } catch (PimException $exception) {
                return redirect()
                    ->route('users.show', ['id' => $authentikId, 'tab' => 'pim'])
                    ->with('error', $exception->getMessage());
            }
        }

        return redirect()
            ->route('users.show', ['id' => $authentikId, 'tab' => 'pim'])
            ->with('success', 'PIM group unassigned from user.');
    }
}
