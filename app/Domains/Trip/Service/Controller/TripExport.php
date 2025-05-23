<?php declare(strict_types=1);

namespace App\Domains\Trip\Service\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Domains\Trip\Model\Trip as Model;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class TripExport
{
    protected Request $request;
    protected array $selectedRows;

    public function __construct(Request $request, array $selectedRows)
    {
        $this->request = $request;
        $this->selectedRows = $selectedRows;
    }

    public function export(): Response|BinaryFileResponse
    {
        // Tạo spreadsheet mới
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Đặt tiêu đề cột
        $headers = [
            'Người dùng',
            'Phương tiện',
            'Thiết bị',
            'Tên',
            'Thời gian bắt đầu',
            'Thời gian kết thúc',
            'Quãng đường',
            'Thời gian',
            'Chia sẻ',
            'Chia sẻ công khai',
        ];
        $sheet->fromArray($headers, null, 'A1');

        // Lấy dữ liệu từ database
        $trips = Model::whereIn('id', $this->selectedRows)
            ->with(['user', 'vehicle', 'device'])
            ->get()
            ->map(function ($trip) {
                return [
                    $trip->user->name ?? '',
                    $trip->vehicle->name ?? '',
                    $trip->device->name ?? '',
                    $trip->name,
                    $trip->start_at,
                    $trip->end_at,
                    $trip->distance,
                    $trip->time,
                    $trip->shared ? 'Có' : 'Không',
                    $trip->shared_public ? 'Có' : 'Không',
                ];
            })->toArray();

        // Điền dữ liệu vào sheet
        $sheet->fromArray($trips, null, 'A2');

        // Tạo file Excel
        $writer = new Xlsx($spreadsheet);
        $fileName = 'trips_export.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($tempFile);

        // Trả về response để tải file
        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
}