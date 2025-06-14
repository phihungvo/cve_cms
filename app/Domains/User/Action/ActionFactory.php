<?php declare(strict_types=1);

namespace App\Domains\User\Action;

use App\Domains\Core\Action\ActionFactoryAbstract;
use App\Domains\User\Model\User as Model;

class ActionFactory extends ActionFactoryAbstract
{
    /**
     * @var ?Model
     */
    protected ?Model $row;

    /**
     * @return Model
     */
    public function authApi(): Model
    {
        return $this->actionHandle(AuthApi::class);
    }

    /**
     * @return Model
     */
    public function authCredentials(): Model
    {
        // return $this->actionHandle(AuthCredentials::class, $this->validate()->authCredentials());
        return $this->actionHandle(AuthCredentials::class);
    }

    /**
     * @return Model
     */
    public function authModel(): Model
    {
        return $this->actionHandle(AuthModel::class);
    }

    /**
     * @return Model
     */
    public function create(): Model
    {
        return $this->actionHandleTransaction(Create::class, $this->validate()->create());
    }

    /**
     * @return void
     */
    public function delete(): void
    {
        $this->actionHandle(Delete::class);
    }

    /**
     * @return void
     */
    public function logout(): void
    {
        $this->actionHandle(Logout::class);
    }

    /**
     * @return Model
     */
    public function request(): Model
    {
        return $this->actionHandle(Request::class);
    }

    /**
     * @return Model
     */
    public function set(): Model
    {
        return $this->actionHandle(Set::class);
    }

    /**
     * @return Model
     */
    public function update(): Model
    {
        return $this->actionHandleTransaction(Update::class, $this->validate()->update());
    }
}
