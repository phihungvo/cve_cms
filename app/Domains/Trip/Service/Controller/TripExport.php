<?php declare(strict_types=1);

namespace App\Domains\Trip\Service\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Domains\Trip\Model\Trip as Model;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class TripExport
{
    protected Request $request;
    protected array $selectedRows;
    protected array $exportTypes;

    public function __construct(Request $request, array $selectedRows, array $exportTypes)
    {
        $this->request = $request;
        $this->selectedRows = $selectedRows;
        $this->exportTypes = $exportTypes;
    }

    public function export(): Response|BinaryFileResponse
    {
        // Tạo spreadsheet mới
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Đặt tiêu đề cột dựa trên export types
        $headers = [];
        if (in_array('user', $this->exportTypes)) {
            $headers[] = 'Người dùng';
        }
        if (in_array('vehicle', $this->exportTypes)) {
            $headers[] = 'Phương tiện';
        }
        if (in_array('device', $this->exportTypes)) {
            $headers[] = 'Thiết bị';
        }
        if (in_array('day', $this->exportTypes)) {
            $headers[] = 'Ngày';
        }
        if (in_array('month', $this->exportTypes)) {
            $headers[] = 'Tháng';
        }
        $headers[] = 'Tổng quãng đường (km)';
        $sheet->fromArray($headers, null, 'A1');

        // Lấy dữ liệu từ database
        $query = Model::whereIn('id', $this->selectedRows)
            ->with(['user', 'vehicle', 'device']);

        // Nếu không có export types, xuất dữ liệu gốc
        if (empty($this->exportTypes)) {
            $data = $query->get()->map(function ($trip) {
                return [
                    $trip->user->name ?? 'Không xác định',
                    $trip->vehicle->name ?? 'Không xác định',
                    $trip->device->name ?? 'Không xác định',
                    number_format($trip->distance / 1000, 2, '.', ','), // Quy đổi mét sang km, thêm dấu phân cách hàng nghìn
                ];
            })->toArray();
        } else {
            // Nhóm dữ liệu theo các tiêu chí được chọn
            $data = $query->get()->groupBy(function ($trip) {
                $keys = [];
                if (in_array('user', $this->exportTypes)) {
                    $keys[] = $trip->user->name ?? 'Không xác định';
                }
                if (in_array('vehicle', $this->exportTypes)) {
                    $keys[] = $trip->vehicle->name ?? 'Không xác định';
                }
                if (in_array('device', $this->exportTypes)) {
                    $keys[] = $trip->device->name ?? 'Không xác định';
                }
                if (in_array('day', $this->exportTypes)) {
                    $keys[] = Carbon::parse($trip->start_at)->format('Y-m-d');
                }
                if (in_array('month', $this->exportTypes)) {
                    $keys[] = Carbon::parse($trip->start_at)->format('Y-m');
                }
                return implode('|', $keys);
            })->map(function ($group) {
                $first = $group->first();
                $row = [];
                if (in_array('user', $this->exportTypes)) {
                    $row[] = $first->user->name ?? 'Không xác định';
                }
                if (in_array('vehicle', $this->exportTypes)) {
                    $row[] = $first->vehicle->name ?? 'Không xác định';
                }
                if (in_array('device', $this->exportTypes)) {
                    $row[] = $first->device->name ?? 'Không xác định';
                }
                if (in_array('day', $this->exportTypes)) {
                    $row[] = Carbon::parse($first->start_at)->format('Y-m-d');
                }
                if (in_array('month', $this->exportTypes)) {
                    $row[] = Carbon::parse($first->start_at)->format('Y-m');
                }
                $row[] = number_format($group->sum('distance') / 1000, 2, '.', ','); // Quy đổi mét sang km, thêm dấu phân cách hàng nghìn
                return $row;
            })->values()->toArray();
        }

        // Điền dữ liệu vào sheet
        $sheet->fromArray($data, null, 'A2');

        // Tạo file Excel
        $writer = new Xlsx($spreadsheet);
        $fileName = 'trips_export.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($tempFile);


        // Trả về response để tải file
        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
}