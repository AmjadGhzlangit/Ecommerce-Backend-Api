<?php

namespace App\Http\API\V1\Repositories\User;

use App\Filters\UserNameFilter;
use App\Http\API\V1\Core\PaginatedData;
use App\Http\API\V1\Repositories\BaseRepository;
use App\Models\ContactUs;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;

class UserRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function index(): PaginatedData
    {
        $filters = [
            AllowedFilter::exact('id'),
            AllowedFilter::partial('first_name'),
            AllowedFilter::partial('last_name'),
            AllowedFilter::partial('email'),
            AllowedFilter::partial('phone'),
            AllowedFilter::custom('search', new UserNameFilter),
        ];

        $sorts = [
            AllowedSort::field('id'),
            AllowedSort::field('first_name'),
            AllowedSort::field('last_name'),
            AllowedSort::field('email'),
        ];

        return $this->filter(User::class, $filters, $sorts);
    }

    public function store($data): User|Model
    {
        $user = parent::store($data);
        $user->setPassword($data['password']);
        $user->save();
        $user->refresh();

        return $user;
    }

    public function update(User|Model $user, $data): Model|User
    {
        if (Arr::exists($data, 'password')) {
            $data['password'] = Hash::make($data['password']);
        }

        $userUpdated = parent::update($user, $data);
        $userUpdated->refresh();

        return $userUpdated;
    }

    public function indexRoles(User $user): PaginatedData
    {
        $filters = [
            AllowedFilter::partial('name'),
            AllowedFilter::partial('description'),
            AllowedFilter::partial('id'),
        ];

        $sorts = [
            AllowedSort::field('name'),
            AllowedSort::field('description'),
            AllowedSort::field('id'),
        ];

        return $this->filter($user->roles(), $filters, $sorts);
    }

    public function editRoles($data, User $user): void
    {
        $user->roles()->sync($data);
    }

    public function profile(): User
    {
        return auth()->user();
    }

    public function contactUs($data): ContactUs|Model
    {
        $suggestion = ContactUs::create($data);
        $suggestion->save();

        return $suggestion;
    }
}
