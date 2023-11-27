<?php

namespace App\Http\API\V1\Controllers\Admin\Category;

use App\Http\API\V1\Controllers\Admin\Controller;
use App\Http\API\V1\Repositories\Category\CategoryRepository;
use App\Http\Resources\Api\CategoryResource;
use App\Models\Category;
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
class CategoryController extends Controller
{
    public function __construct(protected CategoryRepository $categoryRepository)
    {
        $this->middleware(['auth:sanctum']);
        $this->authorizeResource(Category::class);
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
        $paginatedData = $this->categoryRepository->index();

        return $this->showAll($paginatedData->getData(), CategoryResource::class, $paginatedData->getPagination());
    }

    /**
     * Show specific permission
     *
     * This endpoint lets you show specific permission
     *
     * @responseFile storage/responses/admin/permissions/show.json
     */
    public function show(Category $category): JsonResponse
    {
        return $this->showOne($this->categoryRepository->show($category), CategoryResource::class);
    }
}
