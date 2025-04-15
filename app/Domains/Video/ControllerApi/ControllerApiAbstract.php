<?php declare(strict_types=1);

namespace App\Domains\Video\ControllerApi;

use App\Domains\CoreApp\Controller\ControllerApiAbstract as CoreControllerApiAbstract;
use App\Domains\Video\Model\Video as Model;

abstract class ControllerApiAbstract extends CoreControllerApiAbstract
{
   /**
    * @var ?\App\Domains\Video\Model\Video
    */
   protected ?Model $row;

   /**
    * Lấy một Screen từ cơ sở dữ liệu theo ID.
    *
    * @param int $id
    *
    * @return \App\Domains\Video\Model\Video
    */
   protected function row(int $id): Model
   {
      return $this->row = Model::query()
         ->byId($id) // Lọc theo ID
         ->byUserOrManager($this->auth) // Kiểm tra quyền truy cập
         ->firstOr(fn() => $this->exceptionNotFound(__('video.error.not-found')));
   }
}
