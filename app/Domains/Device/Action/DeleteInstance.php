<?php declare(strict_types=1);

namespace App\Domains\Device\Action;

use App\Domains\Device\Model\DeviceCvedixrtInstance;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Throwable;
use TypeError;

class DeleteInstance extends ActionAbstract
{
    /**
     * @throws Exception
     */
    public function handle(?DeviceCvedixrtInstance $instance): void
    {
        $this->instance = $instance;
        $this->delete();
    }

    /**
     * @throws Exception
     */
    protected function delete(): void
    {
        try {
            $this->instance->delete();
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), 'foreign key constraint fails')) {
                throw new Exception(__('rt-analytics-update.error.in_use'), 0, $e);
            }
            throw new Exception(
                __('rt-analytics-update.error.database'),
                0,
                $e
            );
        } catch (ModelNotFoundException $e) {
            throw new Exception(
                __('rt-analytics-update.error.not_found', ['id' => $this->instance->id]),
                0,
                $e
            );
        } catch (TypeError $e) {
            throw new Exception(
                __('rt-analytics-update.error.type'),
                0,
                $e
            );
        } catch (Throwable $e) {
            throw new Exception(
                __('rt-analytics-update.error.unknown'),
                0,
                $e
            );
        }
    }
}
