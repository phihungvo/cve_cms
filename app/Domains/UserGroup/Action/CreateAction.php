<?php declare(strict_types=1);

namespace App\Domains\UserGroup\Action;

use App\Domains\UserGroup\Model\GroupModel as Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use PDOException;
use RuntimeException;
use Throwable;

class CreateAction extends CreatUpdateActionAbstract
{
    /**
     * Lưu thông tin user group vào database
     *
     * @throws RuntimeException Khi xảy ra bất kỳ lỗi nào trong quá trình xử lý
     *
     * @return void
     */
    protected function save(): void
    {
        try {
            $this->row = Model::query()->create([
                'name' => $this->data['name'],
                'description' => $this->data['description'],
                'enterprise_id' => $this->data['enterprise_id'] ?? null,
            ]);
        } catch (PDOException|QueryException $e) {
            throw new RuntimeException('Lỗi kết nối hoặc truy vấn database: '.$e->getMessage(), 0, $e);
        } catch (ModelNotFoundException $e) {
            throw new RuntimeException('Không tìm thấy dữ liệu: '.$e->getMessage(), 0, $e);
        } catch (ValidationException $e) {
            throw new RuntimeException('Dữ liệu không hợp lệ: '.$e->getMessage(), 0, $e);
        } catch (Throwable $e) {
            throw new RuntimeException('Lỗi không xác định khi tạo user group: '.$e->getMessage(), 0, $e);
        }
    }
}
