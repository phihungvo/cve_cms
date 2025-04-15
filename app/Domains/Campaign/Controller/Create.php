<?php

declare(strict_types=1);

namespace App\Domains\Campaign\Controller;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use App\Domains\Campaign\Model\Campaign;
use App\Domains\Campaign\Service\Controller\Create as ControllerService;
use App\Domains\CoreApp\Controller\ControllerWebAbstract;
use App\Domains\Campaign\Media\Model\Media;

class Create extends ControllerWebAbstract
{
    /**
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse|\Illuminate\Contracts\View\View
     */
    public function __invoke(): Response|JsonResponse|View
    {
        if ($this->request->wantsJson()) {
            return $this->responseJson();
        }

        $this->meta('title', __('campaign-create.meta-title'));

        return $this->page('campaign.create', $this->getService()->data());
    }

    /**
     * Store a new campaign.
     *
     * @return \Illuminate\Http\Response|JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function store(): Response|JsonResponse|RedirectResponse
    {
        try {
            $response = $this->getService()->store();

            if ($this->request->wantsJson()) {
                return $this->json($response);
            }

            return redirect()->route('campaign.index')->with('success', __('campaign-create.success'));
        } catch (\Exception $e) {
            $errorResponse = [
                'status' => false,
                'message' => $e->getMessage(),
            ];

            if ($this->request->wantsJson()) {
                return $this->json($errorResponse, 422);
            }

            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function getMediaByEnterprise(int $enterpriseId): JsonResponse
    {
        $media = Media::where('enterprise_id', $enterpriseId)->get();

        return response()->json([
            'data' => $media->map(function ($mediaItem) {
                return [
                    'id' => $mediaItem->id,
                    'name' => $mediaItem->name,
                    'media_url' => $mediaItem->media_url,
                    'type' => $mediaItem->type,
                ];
            })->all(),
        ]);
    }
    public function getUsersByEnterprise(int $enterpriseId): JsonResponse
    {
        $users = \App\Domains\User\Model\User::where('enterprise_id', $enterpriseId)->get();

        return response()->json([
            'data' => $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ];
            })->all(),
        ]);
    }

    /**
     * Show the form for editing the specified campaign.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response|JsonResponse|View
     */
    public function edit(int $id): Response|JsonResponse|View
    {
        $campaign = Campaign::with(['media', 'performance', 'location', 'enterprise'])->findOrFail($id);

        if ($this->request->wantsJson()) {
            return $this->json([
                'data' => array_merge($this->getService()->data(), ['campaign' => $this->getService()->formatCampaign($campaign)]),
            ]);
        }

        $this->meta('title', __('campaign-edit.meta-title'));

        return $this->page('campaign.edit', array_merge($this->getService()->data(), ['campaign' => $this->getService()->formatCampaign($campaign)]));
    }

    /**
     * Update the specified campaign.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response|JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function update(int $id): Response|JsonResponse|RedirectResponse
    {
        try {
            $response = $this->getService()->update($id);

            if ($this->request->wantsJson()) {
                return $this->json($response);
            }

            return redirect()->route('campaign.index')->with('success', __('campaign-edit.success'));
        } catch (\Exception $e) {
            $errorResponse = [
                'status' => false,
                'message' => $e->getMessage(),
            ];

            if ($this->request->wantsJson()) {
                return $this->json($errorResponse, 422);
            }

            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified campaign.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response|JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function destroy(int $id): Response|JsonResponse|RedirectResponse
    {
        try {
            $response = $this->getService()->destroy($id);

            if ($this->request->wantsJson()) {
                return $this->json($response);
            }

            return redirect()->route('campaign.index')->with('success', __('campaign-delete.success'));
        } catch (\Exception $e) {
            $errorResponse = [
                'status' => false,
                'message' => $e->getMessage(),
            ];

            if ($this->request->wantsJson()) {
                return $this->json($errorResponse, 422);
            }

            return redirect()->route('campaign.index')->withErrors($e->getMessage());
        }
    }

    /**
     * Restore the specified campaign.
     *
     * @param int $id
     *
     * @return JsonResponse|RedirectResponse
     */
    public function restore(int $id): JsonResponse|RedirectResponse
    {
        try {
            $response = $this->getService()->restore($id);
            if ($this->request->wantsJson()) {
                return $this->json($response);
            }

            return redirect()->route('campaign.index')->with('success', __('campaign-restore.success'));
        } catch (\Exception $e) {
            $errorResponse = [
                'status' => false,
                'message' => $e->getMessage(),
            ];
            if ($this->request->wantsJson()) {
                return $this->json($errorResponse, 422);
            }

            return redirect()->route('campaign.index')->withErrors($e->getMessage());
        }
    }

    /**
     * Force delete the specified campaign.
     *
     * @param int $id
     *
     * @return JsonResponse|RedirectResponse
     */
    public function forceDelete(int $id): JsonResponse|RedirectResponse
    {
        try {
            $response = $this->getService()->forceDelete($id);
            if ($this->request->wantsJson()) {
                return $this->json($response);
            }

            return redirect()->route('campaign.index')->with('success', __('campaign-delete.success'));
        } catch (\Exception $e) {
            $errorResponse = [
                'status' => false,
                'message' => $e->getMessage(),
            ];
            if ($this->request->wantsJson()) {
                return $this->json($errorResponse, 422);
            }

            return redirect()->route('campaign.index')->withErrors($e->getMessage());
        }
    }

    /**
     * Get JSON response for create/edit.
     *
     * @return JsonResponse
     */
    protected function responseJson(): JsonResponse
    {
        return $this->json([
            'data' => $this->getService()->data(),
        ]);
    }

    /**
     * Get the service instance.
     *
     * @return ControllerService
     */
    protected function getService(): ControllerService
    {
        return ControllerService::new($this->request, $this->auth);
    }
}
