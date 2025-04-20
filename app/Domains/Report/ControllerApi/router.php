<?php declare(strict_types=1);

namespace App\Domains\Report\ControllerApi;

use Illuminate\Support\Facades\Route;

use App\Domains\Report\ControllerApi\DeviceDistanceImpressionReach as DeviceDistanceImpressionReachController;
use App\Domains\Report\Service\ControllerApi\DeviceDistanceImpressionReach as DeviceDistanceImpressionReachService;

use App\Domains\Report\ControllerApi\DailyDistanceImpressionReach as DailyDistanceImpressionReachController;
use App\Domains\Report\Service\ControllerApi\DailyDistanceImpressionReach as DailyDistanceImpressionReachService;

// use App\Domains\Report\ControllerApi\GetScreenCaptureRecognition as GetScreenCaptureRecognitionController;
// use App\Domains\Report\Service\ControllerApi\GetScreenCaptureRecognition as GetScreenCaptureRecognitionService;


use Illuminate\Http\Request; // Sử dụng đúng namespace cho Request

Route::get('/report/device/distance-impression-reach', function (Request $request) {
    $service = DeviceDistanceImpressionReachService::new($request, auth()->user());
    return (new DeviceDistanceImpressionReachController($service))->data($request);
});

Route::get('/report/daily/distance-impression-reach', function (Request $request) {
    $service = DailyDistanceImpressionReachService::new($request, auth()->user());
    return (new DailyDistanceImpressionReachController($service))->data($request);
});


Route::post('/report/image/media-report', [SendImageReport::class, 'store']);

Route::get('/report/image/by-vehicle', [GetImageReportByVehicleId::class, 'index']);

Route::get('/report/image/recognition/fpp', [GetScreenCaptureRecognition::class, 'index']);
