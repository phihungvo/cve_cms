<?php declare(strict_types=1);

namespace App\Domains\UserGroup\Service\Controller;

use App\Domains\User\Enterprise\Model\Enterprise;
use App\Domains\UserGroup\Model\GroupModel;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Model\GroupModel as Model;

class IndexService extends ControllerAbstract{

    public function __construct(protected Request $request, Authenticatable $auth)
    {
        $this->filters();
    }

    protected function filters()
    {
    }

    public function data(): array
    {
        return [
            'enterprises' => $this->enterprise(),
            'list' => $this->list(),
        ];
    }

    protected function list(): Collection
    {
        return GroupModel::query()
            ->roleRoot()
            ->roleOwner()
            ->when($this->request->input('enterprise_id'), function($query){
                $query->where(Enterprise::FOREIGN, $this->request->input('enterprise_id'));
            })
            ->getEnterpriseName()
            ->get();
    }

    protected function enterprise()
    {
        return $this->cache(
            fn () => Enterprise::query()
              //  ->whereHas('playlists') // playlists() trong Enterprise model
                ->get()
        );
    }
}
