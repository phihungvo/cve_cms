<?php

namespace App\Livewire;

use Livewire\Component;

class CameraGrid extends Component
{
    // ╔══════════════════════════════════════════════════════════╗
    // ║ Thuộc tính nhận dữ liệu từ parent                        ║
    // ╚══════════════════════��══════════════════════════════════╝
    public $list; // Danh sách camera

    public $cameras; // Danh sách camera sau khi lọc

    public $enterprises = []; // Danh sách các enterprise

    public $selectEnterpriseFilter = ''; // ID của enterprise được chọn để lọc camera

    public $page = 1; // Trang hiện tại mặc định là 1

    public $perPage;  // Số lượng camera hiển thị trên mỗi trang

    public $selectedCamera = null; // Camera được chọn để hiển thị chi tiết

    public $showModal = false; // Trạng thái hiển thị modal

    // ╔═════════════════════════════════════════════════════════╗
    // ║ Thuộc tính điều khiển layout của grid                   ║
    // ╚════════════════════════��═══════════════════════════════╝
    public $gridLayout = '3x3'; // Giá trị mặc định cho layout (3x3)

    /**
     * Tính toán số cột dựa trên layout
     *
     * @return string
     */
    public function getGridColumnProperty(): string
    {
        return match ($this->gridLayout) {
            '3x3' => 3,
            '4x4' => 4,
            '6x6' => 6,
            '8x8' => 8,
            default => 3,
        };
    }

    /**
     * Tính toán số hàng dựa trên layout
     *
     * @return string
     */
    public function getGridRowsProperty(): string
    {
        return match ($this->gridLayout) {
            '3x3' => 3,
            '4x4' => 4,
            '6x6' => 6,
            '8x8' => 8,
            default => 3,
        };
    }

    /**
     * Phương thức sử lý khi nhận được sự kiện từ parent
     *
     * @param $enterprise
     *
     * @return void
     */
    // Lọc danh sách camera theo enterprise được chọn
    public function filterCameraByEnterprise($enterprise): void
    {
        $this->selectEnterpriseFilter = $enterprise;
        $this->loadCamera(); // Tải lại danh sách camera
    }

    /**
     * Xử lý khi người dùng chọn enterprise từ dropdown
     *
     * @param $enterpriseId
     *
     * @return void
     */
    public function handleSelectEnterprise($enterpriseId): void
    {
        $this->selectEnterpriseFilter = (int)$enterpriseId;
        $this->loadCamera(); // Tải lại danh sách camera
        $this->render(); // Cập nhật giao diện
    }

    /**
     * Tải danh sách camera dựa trên bộ lọc
     *
     * @return void
     */
    public function loadCamera(): void
    {
        if ($this->selectEnterpriseFilter) {
            // Lọc camera theo enterprise_id
            $this->cameras = $this->list->filter(function ($camera) {
                return $camera->device->enterprise_id === $this->selectEnterpriseFilter;
            });
        } else {
            $this->cameras = $this->list; // Hiển thị toàn bộ camera nếu không có bộ lọc
        }

        $this->perPage = $this->gridColumn * $this->gridRows; // Cập nhật lại perPage nếu layout thay đổi

        // Giới hạn danh sách camera theo trang
        $this->cameras = $this->cameras->forPage($this->page, $this->perPage);

        // Gửi sự kiện tới JS sau khi dữ liệu thay đổi
        $this->js('window.dispatchEvent(new CustomEvent("livewire-video-updated"));');
    }

    public function updatedGridLayout($value): void
    {
        $this->loadCamera();
        $this->render();
    }

    public function nextPage(): void
    {
        if ($this->page * $this->perPage < $this->list->count()) {
            $this->page++;
            $this->loadCamera();
            $this->render();
        }
    }

    public function previousPage(): void
    {
        if ($this->page > 1) {
            $this->page--;
            $this->loadCamera();
            $this->render();
        }
    }

    /**
     * Phương thức hiển thị chi tiết camera
     *
     * @param $cameraId
     *
     * @return void
     */
    public function showCameraDetail($cameraId): void
    {
        // Timf camera trong danh sách
        $this->selectedCamera = $this->list->first(function ($camera) use ($cameraId) {
            return $camera->id === $cameraId;
        });

        if ($this->selectedCamera) {
            $this->showModal = true; // Hiển thị modal
            $this->render(); // Cập nhật giao diện
        }
        // Gửi sự kiện tới JS sau khi dữ liệu thay đổi
        $this->js('window.dispatchEvent(new CustomEvent("livewire-video-updated"));');
    }

    /**
     * Phương thức đóng modal
     *
     * Được gọi khi người dùng muốn đóng modal
     *
     * @return void
     */
    public function closeModal(): void
    {
        $this->showModal = false; // Ẩn modal
        $this->selectedCamera = null; // Đặt camera đã chọn về null
        $this->render(); // Cập nhật giao diện
    }

    /**
     * Phương thức khởi tạo component
     *
     * Một phương thức đặc biệt được Livewire sử dụng để khởi tạo dữ liệu hoặc trạng thái của component
     * khi nó được mount (tức là khi component được khởi tạo lần đầu).
     *
     * @param $list
     * @param $enterprises
     *
     * @return void
     */
    public function mount($list, $enterprises): void
    {
        $this->list = $list ?? collect(); // Đảm bảo danh sách camera là một collection
        $this->loadCamera(); // Tải danh sách camera lần đầu
        $this->enterprises = $enterprises; // Gán danh sách enterprises
        $this->perPage = $this->gridColumn * $this->gridRows; // Ví dụ: 9 cho 3x3
    }

    /**
     * Phương thức render view
     *
     * @return \Illuminate\View\View
     *
     * @overide
     */
    public function render(): \Illuminate\View\View
    {
        $limit = $this->gridColumn * $this->gridRows; // Giới hạn số lượng camera hiển thị

        return view('livewire.camera-grid', [
            'cameras' => $this->cameras->take($limit), // Truyền danh sách camera vào view
            'enterprises' => $this->enterprises, // Truyền danh sách enterprises vào view
            'gridColumns' => $this->gridColumn, // Số cột của grid
            'gridRows' => $this->gridRows, // Số hàng của grid
            'selectedCamera' => $this->selectedCamera, // Truyền camera được chọn
            'showModal' => $this->showModal, // Truyền trạng thái modal
        ]);
    }
}
