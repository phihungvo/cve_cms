<?php declare(strict_types=1);

namespace App\Domains\User\Service\Controller;

use App\Domains\UserGroup\Model\GroupModel;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use App\Domains\User\Model\User as Model;
use Illuminate\Support\Collection;

class Update extends CreateUpdateAbstract
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Contracts\Auth\Authenticatable $auth
     * @param \App\Domains\User\Model\User $row
     *
     * @return self
     */
    public function __construct(protected Request $request, protected Authenticatable $auth, protected Model $row)
    {
        $this->request();
    }

    /**
     * @return void
     */
    public function request(): void
    {
        $this->requestMergeWithRow(['api_key' => $this->requestApiKey()]);
    }

    /**
     * @return string
     */
    public function requestApiKey(): string
    {
        return $this->row->api_key_prefix
            ? ($this->row->api_key_prefix.'-*****-****-****-************')
            : '';
    }

    /**
     * @return array
     */
    public function data(): array
    {
        return $this->dataCommon() + [
            'row' => $this->row,
            'groups' => $this->groups(), // override
            'assignedRoles' => $this->row->roles(),
            'assignedGroups' => $this->row->groups(),
            'can_be_deleted' => ($this->row->id !== $this->auth->id),
        ];
    }

    /**
     * Override phương thức group ở lớp cha.
     *
     * @return Collection
     *
     * @override
     */
    protected function groups(): Collection
    {
        return GroupModel::query()
            ->whereBySystem($this->row->enterprise_id)
            ->whereByEnterprise($this->row->enterprise_id)
            ->get();
    }
}
