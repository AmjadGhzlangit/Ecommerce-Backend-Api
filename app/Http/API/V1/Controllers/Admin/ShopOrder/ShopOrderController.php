<?php

namespace App\Http\API\V1\Controllers\Admin\ShopOrder;

use App\Http\API\V1\Controllers\Admin\Controller;
use App\Http\API\V1\Repositories\ShopOrder\ShopOrderRepository;
use App\Http\API\V1\Requests\ShopOrder\StoreShopOrderRequest;
use App\Http\API\V1\Requests\ShopOrder\UpdateShopOrderRequest;
use App\Http\Resources\Api\ShopOrderResource;
use App\Models\ShopOrder;
use Illuminate\Http\JsonResponse;

class ShopOrderController extends Controller
{
    public function __construct(protected ShopOrderRepository $shopOrderRepository)
    {
        $this->middleware(['auth:sanctum']);
        $this->authorizeResource(ShopOrder::class);
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
        $paginatedData = $this->shopOrderRepository->index();

        return $this->showAll($paginatedData->getData(), ShopOrderResource::class, $paginatedData->getPagination());
    }

    public function store(StoreShopOrderRequest $request): JsonResponse
    {
        $shop_order_data = $request->validated();
        $shop_order_data['user_id'] = auth()->user()->id;

        $shop_order = $this->shopOrderRepository->store($shop_order_data);
        return $this->showOne($shop_order, ShopOrderResource::class, __('The ShopOrder added successfully'));
    }

    public function show(ShopOrder $shop_order): JsonResponse
    {
        return $this->showOne($this->shopOrderRepository->show($shop_order), ShopOrderResource::class);
    }

    public function update(UpdateShopOrderRequest $request, ShopOrder $shop_order): JsonResponse
    {
        $update_shop_order = $this->shopOrderRepository->update($shop_order, $request->validated());


        return $this->showOne($update_shop_order, ShopOrderResource::class, __('The ShopOrder updated successfully'));

    }

    public function destroy(ShopOrder $shop_order): JsonResponse
    {
        $this->shopOrderRepository->delete($shop_order);
        return $this->responseMessage(__('The ShopOrder deleted successfully'));
    }
}
