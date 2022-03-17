<?php

namespace App\Models;

use App\Contracts\Genderable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $gender
 * @property string $lifestage
 * @property Subscription $subscription
 */
class Pet extends Model implements Genderable
{
    use HasFactory;

    protected $table = 'pets';

    protected $fillable = [
        'name',
        'gender',
        'lifestage',
        'subscription_id'
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public static function getGenders(): array
    {
        return [
            self::GENDER__MALE,
            self::GENDER__FEMALE
        ];
    }

    public static function getFullGender($abbreviated): ?string
    {
        $genderMap = [
            self::GENDER__MALE => self::GENDER__MALE__FULL,
            self::GENDER__FEMALE => self::GENDER__FEMALE__FULL,
        ];

        return $genderMap[strtoupper($abbreviated)] ?? null;
    }
}
