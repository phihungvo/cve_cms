<?php declare(strict_types=1);

namespace App\Domains\Playlist\PlaylistGroup\Service\Controller;

use App\Domains\Playlist\PlaylistGroup\Model\PlaylistGroupModel;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class UpdateService extends CreateUpdateAbstractService
{
    public function __construct(protected Request $request, protected Authenticatable $auth, protected PlaylistGroupModel $row)
    {
        $this->request();
    }

    /**
     * Data update PlaylistGroup
     *
     * @return array
     */
    public function data(): array
    {
        return $this->dataCreateUpdate() + [
            'row' => $this->row,
            // and more ...
        ];
    }
}
