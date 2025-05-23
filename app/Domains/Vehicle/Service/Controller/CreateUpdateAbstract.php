<?php declare(strict_types=1);

namespace App\Domains\Vehicle\Service\Controller;

use App\Domains\Timezone\Model\Timezone as TimezoneModel;
use App\Domains\Timezone\Model\Collection\Timezone as TimezoneCollection;
use App\Domains\User\Enterprise\Model\Enterprise;
use App\Domains\User\Model\Collection\User as UserCollection;
use App\Domains\User\Model\User as UserModel;

abstract class CreateUpdateAbstract extends ControllerAbstract
{
    /**
     * @return array
     */
    protected function dataCreateUpdate(): array
    {
        return
            $this->dataCore() + [
                'customUsers' => $this->customUsers(), // Ghi đè users
                'timezones' => $this->timezones(),
                'enterprises' => $this->enterprises(),
            ];
    }

    /**
     * @return TimezoneCollection
     */
    protected function timezones(): TimezoneCollection
    {
        return $this->cache(
            fn () => TimezoneModel::query()
                ->list()
                ->get()
        );
    }

    protected function customUsers(): UserCollection
    {
        if ($this->auth->enterprise_id == null) {
            // user system
            if ($this->request->input('enterprise_id')) {
                return UserModel::query()
                    ->where('enterprise_id', $this->request->input('enterprise_id'))
                    ->listSimple()
                    ->get();
            } else {
                return new UserCollection();
            }
        } else {
            // user enterprise
            return UserModel::query()
                ->where('enterprise_id', $this->auth->enterprise->id)
                ->listSimple()
                ->get();
        }
    }

    protected function enterprises()
    {
        // case user system
        if ($this->auth->enterprise_id == null) {
            return Enterprise::all()->map(function ($enterprise) {
                return [
                    'id' => $enterprise->id,
                    'name' => $enterprise->name,
                ];
            });
        } else {
            // case owner enterprise
            return collect([
                [
                    'id' => $this->auth->enterprise->id,
                    'name' => $this->auth->enterprise->name,
                ],
            ]);
        }
    }
}
