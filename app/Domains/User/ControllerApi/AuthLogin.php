<?php declare(strict_types=1);

namespace App\Domains\User\ControllerApi;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Domains\User\Model\User as UserModel;
use App\Domains\User\Action\ActionFactory;
use Illuminate\Support\Facades\Log;
use App\Domains\Display\Model\Display;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;
use PDOException;

class AuthLogin extends Controller
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $username = $request->input('email/phone');
            $password = $request->input('password');
            $this->validateInput($username, $password);
            $user = $this->authenticate($username, $password);
            $data = $this->prepareResponseData($user);
            return response()->json($data, 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Authentication failed: ' . $e->getMessage());
            return response()->json([
                'message' => $e->getMessage(),
                'error' => 'Authentication failed',
            ], $e->getCode() ?: 401); // Mặc định trả về 401 trừ khi có mã lỗi cụ thể
        }
    }

    protected function validateInput(?string $username, ?string $password): void
    {
        $validator = \Illuminate\Support\Facades\Validator::make(
            $this->request->all(),
            [
                'email/phone' => [
                    'required',
                    function ($attribute, $value, $fail) {
                        if (!filter_var($value, FILTER_VALIDATE_EMAIL) && !preg_match('/^[0-9]{8,15}$/', $value)) {
                            $fail(__('user-auth-credentials.error.email-or-phone-invalid'));
                        }
                    }
                ],
                'password' => 'required|string',
            ]
        );

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }
    }

    protected function authenticate(string $username, string $password): UserModel
    {
        try {
            // Kiểm tra xem username là email hay số điện thoại
            $user = UserModel::where('email', $username)
                ->orWhere('phone', $username)
                ->first();

            // Kiểm tra xem user có tồn tại và mật khẩu có khớp không
            if (!$user || !Hash::check($password, $user->password)) {
                throw new \Exception('Kiểm tra thông tin và trả về phản hồi 401', 401);
            }

            // Nếu thông tin đúng, gọi ActionFactory để xử lý thêm nếu cần
            $this->request->merge([
                'email' => $username,
                'password' => $password,
            ]);
            $factory = new ActionFactory($this->request, null);
            return $factory->authCredentials();
        } catch (QueryException | PDOException $e) {
            // Xử lý lỗi kết nối cơ sở dữ liệu
            Log::error('Database connection failed: ' . $e->getMessage());
            throw new \Exception('Lỗi kết nối cơ sở dữ liệu. Vui lòng thử lại sau.', 500);
        }
    }

    protected function prepareResponseData(UserModel $user): array
    {
        $user->load([
            'roles',
            'campaigns' => function ($query) {
                $query->with([
                    'performance',
                    'media' => function ($query) {
                        $query->with('playlists');
                    }
                ]);
            },
            'devices.vehicle',
            'devices.displays',
        ]);

        $roleAliases = $user->roles->pluck('alias')->filter()->toArray();

        $isClient = array_intersect($roleAliases, ['client-goads-portal-led', 'client-goads-portal-decal']);
        $isDriver = array_intersect($roleAliases, [
            'driver-motocycle-led',
            'driver-motocycle-decal',
            'driver-car-led',
            'driver-car-decal'
        ]);

        $baseData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'api_key' => $user->api_key_full,
            'api_key_prefix' => $user->api_key_prefix,
            'api_key_enabled' => $user->api_key_enabled,
            'minio_access_key' => $user->minio_access_key,
            'minio_secrect_key' => $user->minio_secrect_key,
            'minio_region' => $user->minio_region,
            'roles' => $user->roles->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'alias' => $role->alias ?? null,
                ];
            })->all(),
        ];

        if ($isClient) {
            $baseData['campaigns'] = $user->campaigns->map(function ($campaign) {
                return [
                    'id' => $campaign->id,
                    'name' => $campaign->name,
                    'start_time' => $campaign->start_time->toDateTimeString(),
                    'end_time' => $campaign->end_time->toDateTimeString(),
                    'enterprise_id' => $campaign->enterprise_id,
                    'location_id' => $campaign->location_id,
                    'budget' => $campaign->budget,
                    'status' => $campaign->status,
                    'logo_url' => $campaign->logo_url,
                    'performance' => $campaign->performance ? [
                        'id' => $campaign->performance->id,
                        'reach' => $campaign->performance->reach,
                        'actual_reach' => $campaign->performance->actual_reach,
                        'impression' => $campaign->performance->impression,
                        'actual_impression' => $campaign->performance->actual_impression,
                        'distance' => $campaign->performance->distance,
                        'actual_distance' => $campaign->performance->actual_distance,
                        'cpm' => $campaign->performance->cpm,
                        'actual_cpm' => $campaign->performance->actual_cpm,
                        'actual_cost' => $campaign->performance->actual_cost,
                    ] : null,
                    'media' => $campaign->media->map(function ($media) {
                        $playlistIds = $media->playlists->pluck('id')->toArray();
                        $deviceCount = Display::whereIn('playlist_id', $playlistIds)
                            ->distinct('device_id')
                            ->count('device_id');

                        return [
                            'id' => $media->id,
                            'name' => $media->name,
                            'file_name' => $media->file_name,
                            'media_url' => $media->media_url,
                            'size' => $media->size,
                            'type' => $media->type,
                            'duration' => $media->duration,
                            'enterprise_id' => $media->enterprise_id,
                            'created_at' => $media->created_at->toDateTimeString(),
                            'updated_at' => $media->updated_at->toDateTimeString(),
                            'device_count' => $deviceCount,
                            'thumbnail_url' => $$media->thumbnail_url,
                        ];
                    })->all(),
                ];
            })->all();
        }

        if ($isDriver) {
            $baseData['devices'] = $user->devices->map(function ($device) {
                return [
                    'id' => $device->id,
                    'serial' => $device->serial,
                    'name' => $device->name,
                    'enabled' => $device->enabled,
                    'shared' => $device->shared,
                    'shared_public' => $device->shared_public,
                    'vehicle' => $device->vehicle ? [
                        'id' => $device->vehicle->id,
                        'name' => $device->vehicle->name,
                        'enabled' => $device->vehicle->enabled,
                        'timezone_id' => $device->vehicle->timezone_id,
                    ] : null,
                    'display' => $device->displays->map(function ($display) {
                        return [
                            'id' => $display->id,
                            'status_id' => $display->status_id,
                            'type' => $display->type,
                            'description' => $display->description,
                            'location_id' => $display->location_id,
                            'schedule_id' => $display->schedule_id,
                            'playlist_published' => $display->playlist_published,
                            'schedule_published' => $display->schedule_published,
                            'playlist_id' => $display->playlist_id,
                        ];
                    })->all(),
                ];
            })->all();
        }

        return $baseData;
    }
}