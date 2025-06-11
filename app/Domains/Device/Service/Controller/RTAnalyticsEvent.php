<?php declare(strict_types=1);

namespace App\Domains\Device\Service\Controller;

use App\Domains\Device\Enums\DetectedObject;
use App\Domains\Device\Enums\RuleType;
use App\Domains\Device\Model\Device;
use App\Domains\Device\Model\DeviceCvedixrtEvent;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class RTAnalyticsEvent extends ControllerAbstract
{
    public function __construct(protected Request $request, protected Authenticatable $auth, protected Device $row)
    {
        $this->filters();
    }

    protected function filters()
    {
    }

    public function data(): array
    {
        return [
            'row' => $this->row,
            'instance' => $this->instance ?? null,
            'events' => $this->events(),
            //            'devices' => $this->devices(),
            'instances' => $this->instances(),
            'rules' => $this->rules(),
            'ruleTypes' => $this->ruleTypes(),
            'detectedObjects' => $this->detectedObjects(),
        ];
    }

    protected function events(): Collection
    {
        return DeviceCvedixrtEvent::query()
            ->byInstance((int)$this->request->input('instance_id'))
            ->byRule((int)$this->request->input('rule_id'))
            ->byRuleType($this->request->input('rule_type'))
            ->byDetectedObject($this->request->input('detected_object'))
            ->byStartAt($this->request->input('start_at'))
            ->byEndAt($this->request->input('end_at'))
            ->get();
    }

    protected function instances()
    {
        return $this->row->instances->map(
            fn ($instance) => [
                'id' => $instance->id,
                'name' => $instance->instance_name,
            ]
        );
    }

    protected function rules()
    {
        return $this->row->allInstanceRules->map(
            fn ($rule) => [
                'id' => $rule->id,
                'name' => $rule->name,
            ]
        );
    }

    protected function ruleTypes(): Collection
    {
        return collect(RuleType::cases())->map(function ($case) {
            return ['name' => $case->value, 'value' => ucfirst(str_replace('_', ' ', $case->value))];
        });
    }

    protected function detectedObjects(): Collection
    {
        return collect(DetectedObject::cases())->map(fn ($case) => [
            'name' => $case->value,
            'value' => ucfirst(str_replace('_', ' ', $case->value)),
        ]);
    }
}
