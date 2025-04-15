<?php

namespace App\Domains\User\Enterprise\Model;

use App\Domains\Campaign\Schedule\Model\Schedule;
use App\Domains\Playlist\Model\PlaylistModel;
use App\Domains\User\Role\Model\Role;
use App\Domains\Core\Model\ModelAbstract;
use App\Domains\User\Model\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property mixed $owner
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string|null $address
 * @property string|null $phone_number
 * @property string|null $email
 * @property string|null $logo_url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $owner_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read Role|null $ownerRole
 * @property-read \App\Domains\Playlist\Model\Collection\PlaylistCollection<int, PlaylistModel> $playlists
 * @property-read int|null $playlists_count
 * @property-read \App\Domains\User\Role\Model\Collection\Role<int, Role> $roles
 * @property-read int|null $roles_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enterprise newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enterprise newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enterprise onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enterprise query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enterprise whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enterprise whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enterprise whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enterprise whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enterprise whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enterprise whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enterprise whereLogoUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enterprise whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enterprise whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enterprise wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enterprise whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enterprise withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enterprise withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Enterprise extends ModelAbstract
{
    use HasFactory, SoftDeletes; // Thêm SoftDeletes vào đây

    protected $table = 'enterprise';

    public const PRIMARY = 'id';

    public const FOREIGN = 'enterprise_id';

    protected $fillable = [
        'name',
        'code',
        'address',
        'phone_number',
        'email',
        'owner_id',
        'logo_url',
    ];

    protected $hidden = [];

    protected $dates = ['deteted_at']; // Khai báo cột deleted_at là kiểu ngày tháng

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /** Một Enterprise thuộc về một Role */
    public function ownerRole(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'owner_id');
    }

    /** Một Enterprise cos nhiều Role */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    /** Kiểm tra Enterprise có Role cụ thể hay không */
    public function hasRole($role): bool
    {
        return $this->roles->contains('name', $role);
    }

    public function owner(): HasOne
    {
        return $this->hasOne(User::class, self::PRIMARY, 'owner_id');
    }

    /**
     * Một Enterprise có nhiều Playlist
     *
     * Liên kết mờ giữa Enterprise và Playlist
     *
     * @return HasMany
     */
    public function playlists(): HasMany
    {
        return $this->hasMany(PlaylistModel::class, 'enterprise_id', self::PRIMARY);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'enterprise_id', self::PRIMARY);
    }
}
