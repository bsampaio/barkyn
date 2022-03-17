<?php

namespace App\Models;

use App\Contracts\GenderableUndeclaredIncluded;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property string $name
 * @property string $email
 * @property string $gender
 * @property Carbon $birth_date
 * @property Subscription|null $subscription
 */
class Customer extends Model implements GenderableUndeclaredIncluded
{
    use HasFactory;

    protected $table = 'customers';

    protected $dates = ['updated_at', 'created_at', 'birth_date'];

    protected $fillable = [
        'name',
        'email',
        'gender',
        'birth_date'
    ];

    protected function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class, 'customer_id');
    }

    public static function getGenders(): array
    {
        return [
            self::GENDER__MALE,
            self::GENDER__FEMALE,
            self::GENDER__UNDECLARED
        ];
    }

    public static function getFullGender($abbreviated): ?string
    {
        $genderMap = [
            self::GENDER__MALE => self::GENDER__MALE__FULL,
            self::GENDER__FEMALE => self::GENDER__FEMALE__FULL,
            self::GENDER__UNDECLARED => self::GENDER__UNDECLARED__FULL
        ];

        return $genderMap[strtoupper($abbreviated)] ?? self::GENDER__UNDECLARED__FULL;
    }
}
