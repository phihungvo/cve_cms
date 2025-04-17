<?php declare(strict_types=1);

namespace App\Domains\Report\ControllerApi;

use Illuminate\Support\Facades\Route;

use App\Domains\Report\ControllerApi\ReachAndDistance as ReachAndDistanceController;
use App\Domains\Report\Service\ControllerApi\ReachAndDistance as ReachAndDistanceService;

use Illuminate\Http\Request; // Sử dụng đúng namespace cho Request

Route::get('/report/reach-and-distance', function (Request $request) {
    $service = ReachAndDistanceService::new($request, auth()->user());
    return (new ReachAndDistanceController($service))->data($request);
});

Route::post('/report/send-image-report', [SendImageReport::class, 'store']);

Route::get('/reports/images/by-vehicle', [GetImageReportByVehicleId::class, 'index']);