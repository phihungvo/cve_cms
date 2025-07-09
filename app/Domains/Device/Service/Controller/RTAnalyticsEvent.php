<?php declare(strict_types=1);

namespace App\Domains\Device\Service\Controller;

use App\Domains\Device\Model\Device;
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
        return $this->row->events()
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

    protected function rules(): Collection
    {
        $instanceId = $this->request->input('instance_id');
        $query = $this->row->instances();

        if ($instanceId) {
            $query->whereHas('instanceRules', fn ($q) => $q->where('device_cvedixrt_instance_id', (int)$instanceId));
        }

        return $query->get()->flatMap(function ($instance) {
            $instanceRules = $instance->instanceRules ?? collect();

            return collect($instanceRules)->map(function ($rule) {
                return [
                    'id' => $rule->id,
                    'name' => $rule->name,
                ];
            });
        });
    }

    protected function ruleTypes(): Collection
    {
        // lấy trực tiếp giá trị 'event_type' trong event
        $instanceId = $this->request->input('instance_id');
        $ruleId = $this->request->input('rule_id');
        $query = $this->row->events()
            ->byInstance((int)$instanceId)
            ->byRule((int)$ruleId);

        return $query->get()
            ->unique('event_type')
            ->map(function ($rule) {
                return [
                    'name' => $rule->event_type,
                    'value' => ucfirst(str_replace('_', ' ', $rule->event_type)),
                ];
            });

    }

    protected function detectedObjects(): Collection
    {
        // lấy trực tiếp giá trị 'detected_object' trong event
        $instanceId = $this->request->input('instance_id');
        $ruleId = $this->request->input('rule_id');
        $ruleType = $this->request->input('rule_type');

        $query = $this->row->events()
            ->byInstance((int)$instanceId)
            ->byRule((int)$ruleId)
            ->byRuleType($ruleType);

        return $query->get()
            ->unique('detected_object')
            ->map(function ($event) {
                return [
                    'name' => $event->detected_object,
                    'value' => ucfirst(str_replace('_', ' ', $event->detected_object)),
                ];
            });
    }
}
