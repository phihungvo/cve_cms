<?php declare(strict_types=1);

namespace App\Domains\User\Enterprise\EService\Action;

use App\Domains\User\Enterprise\EService\Model\EService as Model;

class Update extends ActionAbstract
{
    protected array $data;
    protected Model $permission;

    public function handle(array $data): Model
    {
        $this->data = $data;
        return $this->updatePermission();
    }

    protected function updatePermission(): Model
    {
        // Xử lý giá trị is_menu
        $isMenu = isset($this->data['is_menu']) ? $this->data['is_menu'] : false;
        $isMenuValue = ($isMenu === true || $isMenu === 1 || $isMenu === '1') ? 1 : 0;

        // Chuẩn bị mảng dữ liệu cơ bản
        $dataToUpdate = [
            'alias' => $this->data['alias'],
            'name' => $this->data['name'],
            'description' => $this->data['description'],
            'menu_route' => $this->data['menu_route'],
            'is_menu' => $isMenuValue,
            'menu_name' => $this->data['menu_name'],
            'menu_icon' => $this->data['menu_icon'],
        ];

        // Chỉ thêm parent_id nếu nó tồn tại và khác 0
        if (isset($this->data['parent_id']) && $this->data['parent_id'] != 0) {
            $dataToUpdate['parent_id'] = $this->data['parent_id'];
        }

        $this->row->update($dataToUpdate);

        return $this->row;
    }
}