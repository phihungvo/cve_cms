<?php declare(strict_types=1);

namespace App\Domains\Device\Service\Controller;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use App\Domains\User\Enterprise\Model\Enterprise;

class Create extends CreateUpdateAbstract
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Contracts\Auth\Authenticatable $auth
     *
     * @return self
     */
    public function __construct(protected Request $request, protected Authenticatable $auth)
    {
        $this->request();
    }

    // /**
    //  * @return array
    //  */
    // public function data(): array
    // {
    //     return $this->dataCreateUpdate();
    // }
    public function data(): array
    {
        $data = $this->dataCreateUpdate();

        if ($this->auth->isRoot()) { // Giả sử có phương thức isRoot() trong User model
            $data['enterprises'] = Enterprise::all()->map(fn($e) => [
                'id' => $e->id,
                'name' => $e->name,
            ])->toArray();
        } else {
            $data['enterprise_name'] = $this->auth->enterprise->name ?? 'N/A';
            $data['enterprise_id'] = $this->auth->enterprise->id ?? null;
        }

        return $data;
    }
}
