<?php

namespace App\Http\API\V1\Controllers\Admin\Category;

use App\Http\API\V1\Controllers\Admin\Controller;
use App\Http\API\V1\Repositories\Category\CategoryRepository;
use App\Http\API\V1\Requests\Category\StoreCategoryRequest;
use App\Http\API\V1\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\Api\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

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

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category_data = $request->validated();


        $category = $this->categoryRepository->store($category_data);
        return $this->showOne($category, CategoryResource::class, __('The Category added successfully'));
    }

    public function show(Category $category): JsonResponse
    {
        return $this->showOne($this->categoryRepository->show($category), CategoryResource::class);
    }

    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $category_data = $request->validated();

        $update_category = $this->categoryRepository->update($category, $category_data);

        return $this->showOne($update_category, CategoryResource::class, __('The Category updated successfully'));

    }

    public function destroy(Category $category): JsonResponse
    {
        $this->categoryRepository->delete($category);
        return $this->responseMessage(__('The Category deleted successfully'));
    }
}
