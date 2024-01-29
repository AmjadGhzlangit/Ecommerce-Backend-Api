<?php

namespace App\Http\API\V1\Controllers\Admin\Product;

use App\Http\API\V1\Controllers\Admin\Controller;
use App\Http\API\V1\Repositories\Product\ProductRepository;
use App\Http\API\V1\Requests\Product\StoreProductRequest;
use App\Http\API\V1\Requests\Product\UpdateProductRequest;
use App\Http\Resources\Api\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function __construct(protected ProductRepository $productRepository)
    {
        $this->middleware(['auth:sanctum']);
        $this->authorizeResource(Product::class);
    }

    public function index(): JsonResponse
    {
        $paginatedData = $this->productRepository->index();

        return $this->showAll($paginatedData->getData(), ProductResource::class, $paginatedData->getPagination());
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product_data = $request->validated();
        if ($request->hasFile('image')) {
            $imagePath = $this->createFile($request->File('image'), Product::getDisk(), null);
            $product_data['image'] = $imagePath;

        }
        $product = $this->productRepository->store($product_data);
        return $this->showOne($product, ProductResource::class, __('The Category added successfully'));
    }

    public function show(Product $product): JsonResponse
    {
        return $this->showOne($this->productRepository->show($product), ProductResource::class);
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $product_data = $request->validated();
        if ($request->hasFile('image')) {
            $imagePath = $this->createFile($request->File('image'), Product::getDisk(), null);
            $product_data['image'] = $imagePath;
        }
        $UpdateProduct = $this->productRepository->update($product, $product_data);

        return $this->showOne($UpdateProduct, ProductResource::class, __('The Product updated successfully'));

    }

    public function destroy(Product $product): JsonResponse
    {
        $this->productRepository->delete($product);
        return $this->responseMessage(__('The Product deleted successfully'));
    }
}