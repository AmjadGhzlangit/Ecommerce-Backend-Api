<?php

namespace App\Http\API\V1\Controllers\Admin\Role;

use App\Http\API\V1\Controllers\Admin\Controller;
use App\Http\API\V1\Repositories\Role\RoleRepository;
use App\Http\API\V1\Requests\Role\Permission\EditRolePermissionRequest;
use App\Http\API\V1\Requests\Role\StoreRoleRequest;
use App\Http\API\V1\Requests\Role\UpdateRoleRequest;
use App\Http\Resources\Permission\PermissionResource;
use App\Http\Resources\Role\RoleResource;
use App\Models\Role;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;

/**
 * @group Admin
 * APIs for Admin Management
 *
 * @subgroup Roles
 *
 * @subgroupDescription APIs for interacting with roles
 */
class RoleController extends Controller
{
    public function __construct(protected RoleRepository $roleRepository)
    {
        $this->middleware(['auth:sanctum']);
        $this->authorizeResource(Role::class);
    }

    /**
     * Show All
     *
     * This endpoint lets you show all roles
     *
     * @responseFile storage/responses/admin/roles/index.json
     */
    public function index(): JsonResponse
    {
        $paginatedData = $this->roleRepository->index();

        return $this->showAll($paginatedData->getData(), RoleResource::class, $paginatedData->getPagination());
    }

    /**
     * Show specific role
     *
     * This endpoint lets you show specific role
     *
     * @responseFile storage/responses/admin/roles/show.json
     */
    public function show(Role $role): JsonResponse
    {
        return $this->showOne($this->roleRepository->show($role), RoleResource::class);
    }

    /**
     * Add role
     *
     * This endpoint lets you add role
     *
     * @responseFile storage/responses/admin/roles/store.json
     */
    public function store(StoreRoleRequest $request): JsonResponse
    {
        $role = $this->roleRepository->store($request->validated());

        return $this->showOne($role, RoleResource::class, __('The role added successfully'));
    }

    /**
     * Update specific role
     *
     * This endpoint lets you update specific role
     *
     * @responseFile storage/responses/admin/roles/update.json
     */
    public function update(UpdateRoleRequest $request, Role $role): JsonResponse
    {
        $roleUpdated = $this->roleRepository->update($role, $request->validated());

        return $this->showOne($roleUpdated, RoleResource::class, __('The role updated successfully'));
    }

    /**
     * Delete specific role
     *
     * This endpoint lets you delete specific role
     *
     * @responseFile storage/responses/admin/roles/delete.json
     */
    public function destroy(Role $role): JsonResponse
    {
        $this->roleRepository->delete($role);

        return $this->responseMessage(__('The role deleted successfully'));
    }

    /**
     * Show all permissions to specific rule
     *
     * This endpoint lets you show all permissions to specific rule
     *
     * @responseFile storage/responses/admin/roles/permissions/index.json
     *
     * @queryParam page int Field to select page. Defaults to '1'.
     * @queryParam per_page int Field to select items per page. Defaults to '15'.
     * @queryParam filter string Field to filter by id,name,description.
     * @queryParam sort string Field to sort items by id,name,description.
     *
     * @throws AuthorizationException
     */
    public function indexPermissions(Role $role): JsonResponse
    {
        $this->authorize('viewAnyRolePermissions', $role);
        $paginatedData = $this->roleRepository->indexPermissions($role);

        return $this->showAll($paginatedData->getData(), PermissionResource::class, $paginatedData->getPagination());
    }

    /**
     * Edit rule's permissions
     *
     * This endpoint lets you edit rule's permissions (add,update,delete)
     *
     * @responseFile storage/responses/admin/roles/permissions/store.json
     *
     * @throws AuthorizationException
     */
    public function storePermissions(EditRolePermissionRequest $request, Role $role): JsonResponse
    {
        $this->authorize('createRolePermissions', $role);
        $this->roleRepository->editPermissions($role, $request->validated());

        return $this->responseMessage(__("The role's permissions updated successfully."));
    }
}
