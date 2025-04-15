<?php declare(strict_types=1);

namespace App\Domains\Video\ControllerApi;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Domains\Video\Model\Video;

class Index extends ControllerApiAbstract
{
    /**
     * Fetch videos by their IDs.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        // Retrieve the 'ids' parameter from the query
        $ids = $request->query('id');

        // Validate the 'ids' parameter
        if (!$ids) {
            return response()->json([
                'message' => __('The video IDs are required.'),
            ], 400);
        }

        // Convert comma-separated IDs into an array
        $idArray = explode(',', $ids);

        // Fetch videos matching the IDs
        $videos = Video::whereIn('id', $idArray)->get();

        // Check if videos are found
        if ($videos->isEmpty()) {
            return response()->json([
                'message' => __('No videos found for the provided IDs.'),
            ], 404);
        }

        // Return the list of videos
        return response()->json($videos, 200);
    }
}
