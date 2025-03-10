<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {

    }

    public function index(){
        $permission = Permission::withCount('roles')
        ->orderBy('id', 'desc')
        ->paginate(10)
        ->through(function($permission){
            return [
                'id' => $permission->id,
                'name' => $permission->name,
                'roles_count' => $permission->roles_count,
                'guard_name' => $permission->guard_name,
                'created_at' => $permission->created_at->diffForHumans(),
            ];
        });

        return Inertia::render('Permissions/Index', [
            'permissions' => $permission,
            'can' => [
                'create_permissions' => Auth::user()->can('create roles'),
                'edit_permissions' => Auth::user()->can('edit roles'),
                'delete_permissions' => Auth::user()->can('delete roles'),
            ]
        ]);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Permissions/Create', [
            'roles' => Role::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
            'guard_name' => 'nullable|string|max:255',
        ]);

        if (!isset($validated['guard_name'])) {
            $validated['guard_name'] = config('auth.defaults.guard');
        }

        Permission::create($validated);

        return redirect()->route('permissions.index')
            ->with('success', 'Permission created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $permission = Permission::findOrFail($id);
        $permission->load('roles');

        return Inertia::render('Permissions/Show', [
            'permission' => $permission,
            'can' => [
                'edit_permissions' => Auth::user()->can('edit roles'),
                'delete_permissions' => Auth::user()->can('delete roles'),
            ]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $permission = Permission::findOrFail($id);

        return Inertia::render('Permissions/Edit', [
            'permission' => $permission
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $permission = Permission::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
            'guard_name' => 'nullable|string|max:255',
        ]);

        if (!isset($validated['guard_name'])) {
            $validated['guard_name'] = config('auth.defaults.guard');
        }

        $permission->update($validated);

        return redirect()->route('permissions.index')
            ->with('success', 'Permission updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $permission = Permission::findOrFail($id);

        // Check if the permission is used by roles
        if ($permission->roles()->count() > 0) {
            return redirect()->route('permissions.index')
                ->with('error', 'Cannot delete permission as it is assigned to roles');
        }

        $permission->delete();

        return redirect()->route('permissions.index')
            ->with('success', 'Permission deleted successfully');
    }
}
