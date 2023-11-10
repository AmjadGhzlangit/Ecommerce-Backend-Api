<?php

namespace App\Http\API\V1\Controllers\Admin\Permission;

use App\Http\API\V1\Controllers\Admin\Controller;
use App\Http\API\V1\Repositories\Permission\PermissionRepository;
use App\Http\Resources\Permission\PermissionResource;
use App\Models\Permission;
use Illuminate\Http\JsonResponse;

/**
 * @group Admin
 * APIs for Admin Management
 *
 * @subgroup Permissions
 *
 * @subgroupDescription APIs for getting permissions
 */
class PermissionController extends Controller
{
    public function __construct(protected PermissionRepository $permissionRepository)
    {
        $this->middleware(['auth:sanctum']);
        $this->authorizeResource(Permission::class);
    }

    /**
     * Show All
     *
     * This endpoint lets you show all permissions
     *
     * @responseFile storage/responses/admin/permissions/index.json
     */
    public function index(): JsonResponse
    {
        $paginatedData = $this->permissionRepository->index();

        return $this->showAll($paginatedData->getData(), PermissionResource::class, $paginatedData->getPagination());
    }

    /**
     * Show specific permission
     *
     * This endpoint lets you show specific permission
     *
     * @responseFile storage/responses/admin/permissions/show.json
     */
    public function show(Permission $permission): JsonResponse
    {
        return $this->showOne($this->permissionRepository->show($permission), PermissionResource::class);
    }
}
