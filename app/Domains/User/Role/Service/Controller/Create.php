<?php

declare(strict_types=1);

namespace App\Domains\User\Role\Service\Controller;

use App\Domains\User\Permission\Model\Permission;
use App\Domains\User\Role\Enum\RoleEnum;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Domains\User\Role\Model\Role as Model;
use App\Domains\User\Role\Service\Create as CreateService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\ValidationException;
use Throwable;

class Create extends CreateMapAbstract
{
    /**
     * @var Request
     */
    protected Request $request;

    /**
     * @var mixed
     */
    protected mixed $auth;

    /**
     * @param Request $request
     * @param mixed $auth
     * @return void
     */
    public function __construct(Request $request, mixed $auth)
    {
        $this->request = $request;
        $this->auth = $auth;
    }

    /**
     * Create a new instance.
     *
     * @param Request $request
     * @param mixed $auth
     * @return static
     */
    public static function new(Request $request, mixed $auth): static
    {
        return new static($request, $auth);
    }

    /**
     * Get data for the create/edit page.
     *
     * @return array
     */
    public function data(): array
    {
        if (auth()->user()->isRoleRoot()) {
            $permission = Permission::select('id', 'name', 'alias', 'description')
                ->groupBy('alias')
                ->get();
        } else {
            $permission = session('userPermission_' . auth()->user()->id)['all'][RoleEnum::OWNER->value] ?? [];

            // Chuyển danh sách từ array sang Collection rồi chuyển từng phần tử thành object
            $permission = collect($permission)->map(function ($item) {
                return (object) $item; // Convert array to object
            });
        }

        return [
            'errors' => session('errors') ?? new MessageBag(),
            'permissions' => $permission,
        ];
    }

    /**
     * Store a new role.
     *
     * @return array
     * @throws Throwable
     */
    public function store(): array
    {
        try {
            Log::info('Starting store method');

            DB::beginTransaction();

            $data = $this->request->all();

            $alias = Str::slug($data['name']);
            $originalAlias = $alias;
            $counter = 1;

            while (Model::where('alias', $alias)->exists()) {
                $alias = $originalAlias . '-' . $counter;
                $counter++;
            }

            $data['alias'] = $alias;
            if (auth()->user()->isOwner()) {
                $data['enterprise_id'] = $this->auth->enterprise_id;
            }

            $role = CreateService::make($data)
                ->validate()
                ->create();

            if ($this->request->has('permission_ids')) {
                $featureIds = array_map('intval', $this->request->get('permission_ids'));
                $result = $role->permissions()->sync($featureIds);
                Log::info('Sync result: ', (array) $result);
            }

            DB::commit();

            return [
                'status' => true,
                'message' => __('role-create.success'),
                'role' => $this->formatRole($role)
            ];
        } catch (Exception $e) {
            Log::error('Error in store: ' . $e->getMessage(), $e->getTrace());
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Show the form for editing the specified role.
     *
     * @param int $id
     * @return Model
     */
    public function edit(int $id): Model
    {
        return Model::findOrFail($id);
    }

    /**
     * Update the specified role.
     *
     * @param int $id
     * @return array
     */
    public function update(int $id): array
    {
        try {
            Log::info('Starting update method');

            DB::beginTransaction();

            $role = Model::findOrFail($id);
            Log::info('Role found: ', $role->toArray());

            $validator = Validator::make($this->request->all(), [
                'name' => 'required|string|max:100|unique:role,name,' . $role->id,
                'alias' => 'nullable|string|max:100|unique:role,alias,' . $role->id,
            ]);

            Log::info('Validator data: ', $this->request->all());
            if ($validator->fails()) {
                Log::error('Validation failed: ', $validator->errors()->toArray());
                throw new ValidationException($validator);
            }

            $newAlias = $role->alias; // Giữ alias cũ làm mặc định

            // Chỉ tạo alias mới nếu tên thay đổi
            if ($role->name !== $this->request->get('name')) {
                $newAlias = Str::slug($this->request->get('name'));
                $originalAlias = $newAlias;
                $counter = 1;

                while (Model::where('alias', $newAlias)->where('id', '!=', $role->id)->exists()) {
                    $newAlias = $originalAlias . '-' . $counter;
                    $counter++;
                    Log::info('Checking alias conflict, new alias: ' . $newAlias);
                }
            }

            $role->update([
                'name' => $this->request->get('name'),
                'permission_id' => $this->request->get('permission_id'),
                'alias' => $newAlias,
            ]);

            Log::info('Role updated: ', $role->toArray());

            if ($this->request->has('permission_ids')) {
                $permissonIds = !empty($this->request->get('permission_ids'))
                    ? array_map('intval', $this->request->get('permission_ids'))
                    : [];

                Log::info('Enterprise IDs before sync: ', $permissonIds);
                $result = $role->permissions()->sync($permissonIds);
                Log::info('Sync result: ', (array) $result);
            }

            DB::commit();
            Log::info('Transaction committed');

            return [
                'status' => true,
                'message' => __('role-update.success'),
                'role' => $this->formatRole($role)
            ];
        } catch (Exception $e) {
            Log::error('Error in update: ' . $e->getMessage(), $e->getTrace());
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Remove the specified role.
     *
     * @param int $id
     * @return array
     * @throws Exception
     */
    public function destroy(int $id): array
    {
        try {
            $role = Model::findOrFail($id);
        } catch (ModelNotFoundException) {
            /* Role not found */
            return [
                'status' => false,
                'message' => __('role-delete.not_found'),
            ];
        }
        if (!$role->delete()) {
            /* Delete failed */
            throw new Exception(__('role-delete.failed'));
        }
        /* Delete successful */
        return [
            'status' => true,
            'message' => __('role-delete.success'),
        ];
    }

    /**
     * Get JSON response for create/edit.
     *
     * @return array
     */
    public function responseJson(): array
    {
        return $this->data();
    }

    /**
     * Format role data.
     *
     * @param Model $role
     * @return array
     */
    public function formatRole(Model $role): array
    {
        return [
            'id' => $role->id,
            'name' => $role->name,
            'enterprise_id' => $role->enterprise_id,
            'alias' => $role->alias,
            'created_at' => $role->created_at ? Carbon::parse($role->created_at)->toDateTimeString() : null,
        ];
    }

    /**
     * Restore the specified role.
     *
     * @param int $id
     * @return array
     * @throws Exception
     */
    public function restore(int $id): array
    {
        try {
            $role = Model::withTrashed()->findOrFail($id);
        } catch (ModelNotFoundException) {
            /* Role not found */
            return [
                'status' => false,
                'message' => __('role-restore.not_found'),
            ];
        }

        if (!$role->restore()) {
            /* Restore failed */
            throw new Exception(__('role-restore.failed'));
        }
        /* Restore successful */
        return [
            'status' => true,
            'message' => __('role-restore.success'),
        ];
    }

    /**
     * Force delete the specified role.
     * @param int $id
     * @return array
     * @throws Exception
     */
    public function forceDelete(int $id): array
    {
        try {
            $role = Model::withTrashed()->findOrFail($id);
        } catch (ModelNotFoundException) {
            /* Role not found */
            return [
                'status' => false,
                'message' => __('role-force-delete.not_found'),
            ];
        }

        if (!$role->forceDelete()) {
            /* Force delete failed */
            throw new Exception(__('role-force-delete.failed'));
        }
        /* Force delete successful */
        return [
            'status' => true,
            'message' => __('role-force-delete.success'),
        ];
    }
}
