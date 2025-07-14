<?php

namespace App\Models;

use App\Enums\Country;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string|null $line_one
 * @property string|null $line_two
 * @property string|null $line_three
 * @property string|null $postal_code
 * @property string|null $city
 * @property Country|null $country_code
 */
class Address extends Model
{
    /** @use HasFactory<\Database\Factories\AddressFactory> */
    use HasFactory;

    protected $guarded = false;

    protected function casts(): array
    {
        return [
            'country_code' => Country::class,
        ];
    }
}
