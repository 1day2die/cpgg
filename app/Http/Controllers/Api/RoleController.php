<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\RoleResource;
use App\Models\User;
use App\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class RoleController extends Controller
{
    const ALLOWED_INCLUDES = ['permissions', 'users'];
    const ALLOWED_FILTERS = ['name'];

    /**
     * Show a list of roles.
     *
     * @param Request $request
     * @return RoleResource
     */
    public function index(Request $request)
    {
        $roles = QueryBuilder::for(Role::class)
            ->allowedIncludes(self::ALLOWED_INCLUDES)
            ->allowedFilters(self::ALLOWED_FILTERS)
            ->paginate($request->input('per_page') ?? 50);

        return RoleResource::collection($roles);
    }

    /**
     * Store a new role in the system.
     *
     * @param  Request  $request
     * @return RoleResource
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'color' => [
                'required',
                'regex:/^#([a-f0-9]{6}|[a-f0-9]{3})$/i'
            ],
            'power' => 'required',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'color' => $request->color,
            'power' => $request->power,
        ]);

        if ($request->permissions) {
            $permissions = explode(",",$request->permissions);
            $collectedPermissions = collect($permissions)->map(fn($val)=>(int)$val);
            foreach($collectedPermissions as $permission){
                $role->givePermissionTo($permission);
            }
        }

        return RoleResource::make($role);
    }

    /**
     * Show the specified role.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return RoleResource
     */
    public function show(Request $request, int $id)
    {
        $role = QueryBuilder::for(Role::class)
            ->where('id', '=', $id)
            ->allowedIncludes(self::ALLOWED_INCLUDES)
            ->firstOrFail();

        return RoleResource::make($role);
    }

    /**
     * Update the specified role in the system.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return RoleResource
     */
    public function update(Request $request, int $id)
    {
        $role = Role::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|string|max:191',
            'color' => [
                'sometimes',
                'regex:/^#([a-f0-9]{6}|[a-f0-9]{3})$/i'
            ],
            'power' => 'sometimes',
        ]);

        if ($request->permissions) {
            $permissions = explode(",",$request->permissions);
            $collectedPermissions = collect($permissions)->map(fn($val)=>(int)$val);
            $role->syncPermissions($collectedPermissions);
        }

        $role->update($request->except('permissions'));

        return RoleResource::make($role);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return RoleResource|\Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, int $id)
    {
        $role = Role::findOrFail($id);

        if($role->id == 1 || $role->id == 3|| $role->id == 4){ //cannot delete admin and User role
            return response()->json([
                'error' => 'Not allowed to delete Admin, Client or Member'], 400);
        }

        $users = User::role($role)->get();

        foreach($users as $user){
            $user->syncRoles([4]);
        }
        
        $role->delete();

        return RoleResource::make($role);
    }
}
