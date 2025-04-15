<?php

declare(strict_types=1);

namespace App\Domains\Video\Service\Controller;

use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection; // Import Collection
use App\Domains\Video\Model\Video as Model;

class Index
{
    protected Request $request;
    protected ?Authenticatable $auth;

    public function __construct(Request $request, ?Authenticatable $auth)
    {
        $this->request = $request;
        $this->auth = $auth;
    }

    public static function new(Request $request, ?Authenticatable $auth): self
    {
        return new self($request, $auth);
    }

    public function data(): array
    {
        return [
            'items' => $this->list(),
            'search' => $this->request->input('search'),
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function list(): Collection
    {
        $query = Model::query();

        if ($this->request->filled('search')) {
            $search = $this->request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        $query->orderBy('id', 'DESC');

        return $query->get(); // Dùng get() thay vì paginate()
    }
}
