<?php

namespace App\Http\API\V1\Controllers\Admin\User;

use App\Http\API\V1\Controllers\Admin\Controller;
use App\Http\API\V1\Repositories\User\UserRepository;
use App\Http\API\V1\Requests\User\Role\EditUserRoleRequest;
use App\Http\API\V1\Requests\User\StoreUserRequest;
use App\Http\API\V1\Requests\User\UpdateUserRequest;
use App\Http\Resources\Role\RoleResource;
use App\Http\Resources\User\FullUserResource;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group Admin
 * APIs for Admin Management
 *
 * @subgroup Users
 *
 * @subgroupDescription APIs for user Management
 */
class UserController extends Controller
{
    public function __construct(protected UserRepository $userRepository)
    {
        $this->middleware(['auth:sanctum']);
        $this->authorizeResource(User::class);
    }

    /**
     * Show all users
     *
     * This endpoint lets you show all users
     *
     * @responseFile storage/responses/admin/users/index.json
     *
     * @queryParam page int Field to select page. Defaults to '1'.
     * @queryParam per_page int Field to select items per page. Defaults to '15'.
     * @queryParam filter[id] string Field to filter items by id.
     * @queryParam filter[name] string Field to filter items by name.
     * @queryParam filter[email] string Field to filter items by email.
     * @queryParam filter[phone] string Field to filter items by phone.
     * @queryParam filter[search] string Field to perform a custom search.
     * @queryParam sort string Field to sort items by id,name,email.
     */
    public function index(): JsonResponse
    {
        $paginatedData = $this->userRepository->index();

        return $this->showAll($paginatedData->getData(), FullUserResource::class, $paginatedData->getPagination());
    }

    /**
     * Show specific user
     *
     * This endpoint lets you show specific user
     *
     * @responseFile storage/responses/admin/users/show.json
     */
    public function show(User $user): JsonResponse
    {
        return $this->showOne($this->userRepository->show($user), FullUserResource::class);
    }

    /**
     * Add user
     *
     * This endpoint lets you add user
     *
     * @responseFile storage/responses/admin/users/store.json
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $user_data = $request->validated();
        $user = $this->userRepository->store($user_data);

        return $this->showOne($user, FullUserResource::class, __('The user added successfully'));
    }

    /**
     * Update specific user
     *
     * This endpoint lets you update specific user
     *
     * @responseFile storage/responses/admin/users/update.json
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $user_data = $request->validated();
        $updatedUser = $this->userRepository->update($user, $user_data);

        return $this->showOne($updatedUser, FullUserResource::class, __("User's information updated successfully"));
    }

    /**
     * Delete specific user
     *
     * This endpoint lets you user specific user
     *
     * @responseFile storage/responses/admin/users/delete.json
     */
    public function destroy(User $user): JsonResponse
    {
        $currentUser = auth()->user();
        if ($user->is($currentUser)) {
            return $this->responseMessage(__('This operation is not permitted,You can not delete yourself'), Response::HTTP_CONFLICT);
        }
        $this->userRepository->delete($user);

        return $this->responseMessage(__('The user deleted successfully'));
    }

    /**
     * Show all roles to specific user
     *
     * This endpoint lets you show all roles to specific user
     *
     * @responseFile storage/responses/admin/users/roles/index.json
     *
     * @queryParam page int Field to select page. Defaults to '1'.
     * @queryParam per_page int Field to select items per page. Defaults to '15'.
     * @queryParam filter string Field to filter items by id,name,description.
     * @queryParam sort string Field to sort items by id,name,description.
     *
     * @throws AuthorizationException
     */
    public function indexRoles(User $user): JsonResponse
    {
        $this->authorize('viewAnyUserRoles', $user);
        $paginatedData = $this->userRepository->indexRoles($user);

        return $this->showAll($paginatedData->getData(), RoleResource::class, $paginatedData->getPagination());
    }

    /**
     * Edit user's roles
     *
     * This endpoint lets you edit user's roles (add,update,delete)
     *
     * @responseFile storage/responses/admin/users/roles/store.json
     *
     * @throws AuthorizationException
     */
    public function storeRoles(EditUserRoleRequest $request, User $user): JsonResponse
    {
        $this->authorize('createUserRoles', $user);
        $this->userRepository->editRoles($request->validated()['role_ids'], $user);

        return $this->responseMessage(__("The user's roles updated successfully."));
    }

    /**
     * Show user's profile
     *
     * This endpoint lets you show user's authenticated profile
     *
     * @responseFile storage/responses/admin/users/profile.json
     */
    public function profile(): JsonResponse
    {
        return $this->showOne($this->userRepository->profile(), FullUserResource::class);
    }
}
