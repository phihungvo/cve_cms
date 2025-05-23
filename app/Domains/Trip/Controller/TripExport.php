<?php declare(strict_types=1);

namespace App\Domains\Trip\Controller;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Domains\Trip\Service\Controller\TripExport as TripExportService;

class TripExport extends ControllerAbstract
{
    /**
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function __invoke(): Response|BinaryFileResponse
    {
        $selectedRows = json_decode(request()->input('selected_rows'), true);
        $exportTypes = request()->input('export_type', []);

        if (empty($selectedRows)) {
            return response()->json(['error' => 'Vui lòng chọn ít nhất một hàng để xuất.'], 400);
        }

        if (empty($exportTypes)) {
            return response()->json(['error' => 'Vui lòng chọn ít nhất một kiểu xuất.'], 400);
        }

        return (new TripExportService($this->request, $selectedRows, $exportTypes))->export();
    }
}