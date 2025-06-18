<?php

declare(strict_types=1);

namespace App\Domains\CvedixtModel\Action;

use App\Domains\CvedixtModel\Model\CvedixtModel;
use Illuminate\Support\Facades\Log;

class Create
{
    protected array $data;

    public function handle(array $data): CvedixtModel
    {
        try {
            return CvedixtModel::create($data);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    protected function createMedia(): CvedixtModel
    {
        try {
            // Kiểm tra xem media đã tồn tại dựa trên model_url
            $existingModel = CvedixtModel::where('model_url', $this->data['model_url'])->first();

            if ($existingModel) {
                throw new \Exception(__('media-create.media-exists', ['name' => $this->data['file_name'] ?? $this->data['name']]));
            }

            // Nếu chưa tồn tại, tạo mới
            $model = CvedixtModel::create($this->data);

            return $model;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
