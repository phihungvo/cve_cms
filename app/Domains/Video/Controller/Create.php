<?php

declare(strict_types=1);

namespace App\Domains\Video\Controller;

use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use App\Domains\Video\Service\Controller\Create as ControllerService;

class Create extends ControllerAbstract
{
    public function __invoke(): Response
    {
        $this->meta('title', __('video-create.meta-title'));
        return $this->page('video.create'); // Đảm bảo đường dẫn đúng
    }

    public function store(): RedirectResponse
    {
        try {
            ControllerService::new($this->request, $this->auth)->create();
            return redirect()->route('video.index')->with('success', __('video-create.success'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function edit(int $id): Response
    {
        $video = $this->row($id);
        $this->meta('title', __('video-edit.meta-title'));
        return $this->page('video.edit', ['video' => $video]); // Đảm bảo đường dẫn đúng
    }

    public function update(int $id): RedirectResponse
    {
        try {
            $video = $this->row($id);
            ControllerService::new($this->request, $this->auth)->update($video);
            return redirect()->route('video.index')->with('success', __('video-edit.success'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->row($id)->delete();
            return redirect()->route('video.index')->with('success', __('video-delete.success'));
        } catch (\Exception $e) {
            return redirect()->route('video.index')->withErrors($e->getMessage());
        }
    }
}
