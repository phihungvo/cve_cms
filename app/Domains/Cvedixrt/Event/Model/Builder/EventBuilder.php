<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Event\Model\Builder;

use App\Domains\CoreApp\Model\Builder\BuilderAbstract;
use App\Domains\Cvedixrt\Instance\Enums\RuleType;
use Carbon\Carbon;

class EventBuilder extends BuilderAbstract
{
    # Khởi tạo các phương thức tuỳ chỉnh có Eloquent Builder

    /**
     * Lọc truy vấn theo instance ID cụ thể.
     *
     * Thêm điều kiện whereHas để chỉ lấy các bản ghi liên quan đến instance ID được truyền vào.
     *
     * @param int|null $instanceId ID của instance cần lọc
     *
     * @return $this
     */
    public function byInstance(?int $instanceId): self
    {
        if ($instanceId) {
            $this->whereHas('instanceRule.instance', function ($q) use ($instanceId) {
                $q->where('id', (int)$instanceId);
            });
        }

        return $this;
    }

    /**
     * Lọc truy vấn theo rule ID cụ thể.
     *
     * Thêm điều kiện whereHas để chỉ lấy các bản ghi liên quan đến rule ID được truyền vào.
     *
     * @param int|null $ruleId ID của rule cần lọc
     *
     * @return $this
     */
    public function byRule(?int $ruleId): self
    {
        if ($ruleId) {
            $this->whereHas('instanceRule', function ($q) use ($ruleId) {
                $q->where('id', (int)$ruleId);
            });
        }

        return $this;
    }

    /**
     * Lọc truy vấn theo rule_type.
     *
     * Thêm điều kiện where để chỉ lấy các bản ghi liên quan đến rule_type được truyền vào.
     *
     * @param string|null $ruleType rule_type cần lọc
     *
     * @return $this
     */
    public function byRuleType(?string $ruleType): self
    {
        if ($ruleType) {
            $ruleTypeEnum = RuleType::tryFrom($ruleType);
            if ($ruleTypeEnum) {
                $this->where('event_type', $ruleTypeEnum->value);
            }
        }

        return $this;
    }

    /**
     * Lọc truy vấn theo detected_object.
     *
     * Thêm điều kiện where để chỉ lấy các bản ghi liên quan đến detected_object được truyền vào.
     *
     * @param string|null $detectedObject detected_object cần lọc
     *
     * @return $this
     */
    public function byDetectedObject(?string $detectedObject): self
    {
        if ($detectedObject) {
            $this->where('detected_object', $detectedObject);
        }

        return $this;
    }

    /**
     * Lọc truy vấn theo khoảng thời gian bắt đầu.
     *
     * Thêm điều kiện where để chỉ lấy các bản ghi có created_at lớn hơn hoặc bằng ngày bắt đầu.
     *
     * @param string|null $startAt Ngày bắt đầu cần lọc (định dạng 'Y-m-d')
     *
     * @return $this
     */
    public function byStartAt(?string $startAt): self
    {
        if ($startAt) {
            $startAt = Carbon::parse($startAt)->startOfDay(); // Set to 00:00:00
            $this->where('created_at', '>=', $startAt);
        }

        return $this;
    }

    /**
     * Lọc truy vấn theo khoảng thời gian kết thúc.
     *
     * Thêm điều kiện where để chỉ lấy các bản ghi có created_at nhỏ hơn hoặc bằng ngày kết thúc.
     *
     * @param string|null $endAt Ngày kết thúc (định dạng 'Y-m-d')
     *
     * @return $this
     */
    public function byEndAt(?string $endAt): self
    {
        if ($endAt) {
            $endAt = Carbon::parse($endAt)->endOfDay(); // Set to 23:59:59
            $this->where('created_at', '<=', $endAt);
        }

        return $this;
    }
}
