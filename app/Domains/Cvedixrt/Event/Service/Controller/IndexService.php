<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Event\Service\Controller;

use App\Domains\Cvedixrt\Instance\Model\CvedixrtInstanceModel as InstanceModel;
use App\Domains\Cvedixrt\Event\Model\CvedixrtEventModel as Model;
use App\Domains\Cvedixrt\Instance\Model\CvedixrtInstanceRuleModel;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;

class IndexService extends ControllerAbstract
{
    public function __construct(protected Request $request, protected Authenticatable $auth)
    {
        $this->filters();
    }

    protected function filters(): void
    {
        // Add filter logic here
    }

    public function data(): array
    {
        return [
            'list' => $this->list(),
            'instances' => $this->instances(),
            'rules' => $this->rules(),
            'ruleTypes' => $this->ruleTypes(),
            'detectedObjects' => $this->detectedObjects(),
        ];
    }

    protected function list(): Collection
    {
        return Model::query()
            ->byInstance((int)$this->request->input('instance_id'))
            ->byRule((int)$this->request->input('rule_id'))
            ->byRuleType($this->request->input('rule_type'))
            ->byDetectedObject($this->request->input('detected_object'))
            ->byStartAt($this->request->input('start_at'))
            ->byEndAt($this->request->input('end_at'))
            ->orderByDesc('created_at')
            ->get();
    }

    protected function instances(): Collection
    {
        return InstanceModel::query()
            ->listSimple()
            ->get();
    }

    protected function rules(): Collection
    {
        $instanceId = $this->request->input('instance_id');
        $query = CvedixrtInstanceRuleModel::query();

        if ($instanceId) {
            $query->where('cvedixrt_instance_id', (int)$instanceId);
        }

        return $query->listSimple()->get();
    }

    protected function ruleTypes(): Collection
    {
        $instanceId = $this->request->input('instance_id');
        $ruleId = $this->request->input('rule_id');
        $query = Model::query()
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
        // Lấy trực tiếp gía trị 'detected_object' trong event
        $instanceId = $this->request->input('instance_id');
        $ruleId = $this->request->input('rule_id');
        $ruleType = $this->request->input('rule_type');

        $query = Model::query()
            ->byInstance((int)$instanceId)
            ->byRule((int)$ruleId)
            ->byRuleType($ruleType);

        return $query->get()
            ->unique('detected_object')
            ->map(function ($rule) {
                return [
                    'name' => json_decode($rule->detected_object, true)[0]['detected_object'] ?? null,
                    'value' => ucfirst(str_replace('_', ' ', json_decode($rule->detected_object, true)[0]['detected_object'] ?? null)),
                ];
            });
    }
}
