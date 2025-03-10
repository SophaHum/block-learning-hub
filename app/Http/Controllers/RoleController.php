<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Inertia\Inertia;
class RoleController extends Controller
{
    public function __construct()
    {
        
    }
    public function index(){
        $role = Role::withCount('permissions', 'users')->paginate(10);
        return Inertia::render('Roles/Index', compact('role'));
    }

    public function create(){
        $permissions = Permission::all()->groupBy(function($permission){
            return explode(' ', $permission->name)[0];
        });
        return Inertia::render('Roles/Create', [
            'permissions' => $permissions
        ]);
    }

    public function store(Request $request){
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id'
        ]);
        $role = Role::create($validated['name']);
        $permissions = Permission::whereIn('id', $validated['permissions'])->get();
        $role->permissions()->sync($permissions);
        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
        
    }

}
