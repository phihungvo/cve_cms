<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Event\Service\Controller;

use App\Domains\Cvedixrt\Event\Action\CreateUpdateAbstractAction;
use App\Domains\Cvedixrt\Event\Model\CvedixrtEventModel as Model;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use PDOException;
use RuntimeException;
use Throwable;

class CreateAction extends CreateUpdateAbstractAction
{
    /**
     * Create Event
     *
     * @return void
     *
     * @override
     */
    protected function save(): void
    {
        try {
            $this->row = Model::query()->create([
            ]);

        } catch (AuthorizationException $e) {
            // Lỗi xảy ra khi người dùng không có quyền thực hiện hành động này
            throw new RuntimeException(
                __('event-create.error.unauthorized', ['message' => $e->getMessage()]),
                0,
                $e
            );
        } catch (PDOException|QueryException $e) {
            // Lỗi xảy ra khi có vấn đề với cơ sở dữ liệu (ví dụ: vi phạm ràng buộc)
            throw new RuntimeException(
                __('event-create.error.database', ['message' => $e->getMessage()]),
                0,
                $e
            );
        } catch (ValidationException $e) {
            // Lỗi xảy ra khi dữ liệu không hợp lệ
            throw new RuntimeException(
                __('event-create.validation-error', ['message' => $e->getMessage()]),
                0,
                $e
            );
        } catch (Throwable $e) {
            // Lỗi chung cho tất cả các ngoại lệ khác không được xử lý cụ thể
            throw new RuntimeException(
                __('event-create.unknown-error', ['message' => $e->getMessage()]),
                0,
                $e
            );
        }
    }
}

