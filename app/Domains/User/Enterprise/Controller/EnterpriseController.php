<?php

namespace App\Domains\User\Enterprise\Controller;

use App\Domains\User\Enterprise\Service\EnterpriseService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class EnterpriseController extends ControllerAbstract
{
    private EnterpriseService $service;

    public function __construct(EnterpriseService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    /**
     * @return Response
     */
    public function index(): Response
    {
        $data = $this->service->getAll($this->request, $this->auth);

        return $this->page('user.enterprise.index', compact('data'));
    }

    /**
     * @return Response
     */
    public function create(): Response
    {
        $users = $this->service->getUsersWithoutEnterprise($this->request, $this->auth);

        return $this->page('user.enterprise.create', compact('users'));
    }

    public function store(Request $request)
    {
        // validation data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|unique:enterprise,code',
            'address' => 'nullable|string',
            'phone_number' => 'nullable|digits_between:10,15',
            'email' => 'required|email|unique:enterprise,email',
            'owner_id' => 'required|exists:user,id',

        ]);

        $this->service->store($validated, $this->auth);

        return redirect()->route('user.enterprise.index');
    }

    public function show()
    {

        $id = $this->request->route('id');
        $enterprises = $this->service->getEnterpriseById((int)$id, $this->auth);
        if (!$enterprises) {
            return redirect()->route('user.enterprise.index');
        }

        $users = $this->service->getUsersWithoutEnterprise($this->request, $this->auth);
        // thêm thông tin của user hiện tại có trong enterprise vào users
        $owner = [
            'id' => $enterprises->owner->id,
            'name' => $enterprises->owner->name,
            'email' => $enterprises->owner->email,
        ];
        if (!collect($users)->contains('id', $owner['id'])) {
            array_unshift($users, $owner);
        }

        return $this->page('user.enterprise.show', compact('enterprises', 'users'));
    }

    public function update(Request $request)
    {
        $id = $request->route('id');

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'code' => 'required|unique:enterprise,code,'.$id,
                'address' => 'nullable|string',
                'phone_number' => 'nullable|digits_between:10,15',
                'email' => 'required|email|unique:enterprise,email,'.$id,
                'owner_id' => 'required|exists:user,id',
            ]);
        } catch (ValidationException $e) {
            return redirect()->route('user.enterprise.show', ['id' => $id])
                ->withErrors($e->validator)
                ->withInput();

        }
        $this->service->update($validated, $id, $this->auth);

        return redirect()->route('user.enterprise.index');
    }

    /**
     * Soft delete enterprise
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        $id = $request->route('id');

        $this->service->softDelete($id, $this->auth);

        return redirect()->route('user.enterprise.index');
    }

    /**
     * Restore enterprise
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(Request $request)
    {
        $id = $request->route('id');

        $this->service->restore($id, $this->auth);

        return redirect()->route('user.enterprise.index');
    }

    /**
     * Force delete enterprise
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forceDelete(Request $request)
    {
        $id = $request->route('id');

        $this->service->forceDelete($id, $this->auth);

        return redirect()->route('user.enterprise.index');
    }
}
