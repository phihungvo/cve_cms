<?php declare(strict_types=1);

namespace App\Domains\User\Controller;

use Illuminate\Http\Response;
use App\Domains\User\Model\User as Model;

class Index extends ControllerAbstract
{
    /**
     * @return \Illuminate\Http\Response
     */
    public function __invoke(): Response
    {
        $this->meta('title', __('user-index.meta-title'));

        if (auth()->user()->isRoleRoot()) {
            $list = Model::query()->list()->get();
        } elseif (auth()->user()->isOwner()) {
            $list = Model::query()->where('enterprise_id', auth()->user()->enterprise_id)->list()->get();
        } else {
            // todo: nếu 1 user không có quyền root, mà cũng k có quyền owner thì sẽ làm gì?
            // thực tế chỉ có user root, hoặc owner mới có thể vào action này.
            $list = collect(); // or handle other roles accordingly
        }

        return $this->page('user.index', [
            'list' => $list,
        ]);
    }
}
