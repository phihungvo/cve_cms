<?php declare(strict_types=1);

namespace App\Domains\Report\ControllerApi;

use Illuminate\Support\Facades\Route;

use App\Domains\Report\ControllerApi\DeviceDistanceImpressionReach as DeviceDistanceImpressionReachController;
use App\Domains\Report\Service\ControllerApi\DeviceDistanceImpressionReach as DeviceDistanceImpressionReachService;

use App\Domains\Report\ControllerApi\DailyDistanceImpressionReach as DailyDistanceImpressionReachController;
use App\Domains\Report\Service\ControllerApi\DailyDistanceImpressionReach as DailyDistanceImpressionReachService;


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

Route::get('/reports/image/by-vehicle', [GetImageReportByVehicleId::class, 'index']);