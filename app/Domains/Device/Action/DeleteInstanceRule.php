<?php declare(strict_types=1);

namespace App\Domains\Device\Action;

use App\Domains\Device\Model\DeviceCvedixrtInstanceRule;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Throwable;
use TypeError;

class DeleteInstanceRule extends ActionAbstract
{
    /**
     * @throws Exception
     */
    public function handle(?DeviceCvedixrtInstanceRule $instanceRule): void
    {
        $this->instanceRule = $instanceRule;
        $this->delete();
    }

    /**
     * @throws Exception
     */
    protected function delete(): void
    {
        try {
            $this->instanceRule->delete();
        } catch (QueryException $exception) {
            if (str_contains($exception->getMessage(), 'foreign key constraint fails')) {
                throw new Exception(__('rt-analytics-rules.error.in_use'), 0, $exception);
            }
            throw new Exception(
                __('rt-analytics-rules.error.database'),
                0,
                $exception
            );
        } catch (ModelNotFoundException $exception) {
            throw new Exception(
                __('rt-analytics-rules.error.not_found', ['id' => $this->instanceRule->id]),
                0,
                $exception
            );
        } catch (TypeError $exception) {
            throw new Exception(
                __('rt-analytics-rules.error.type'),
                0,
                $exception
            );
        } catch (Throwable $exception) {
            throw new Exception(
                __('rt-analytics-rules.error.unknown'),
                0,
                $exception
            );
        }
    }
}
