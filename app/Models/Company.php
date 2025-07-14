<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string|null $business_name
 * @property string|null $business_id
 * @property string|null $vat_id
 * @property string|null $eu_vat_id
 * @property string|null $email
 * @property string|null $phone_number
 * @property string|null $website
 * @property string|null $bank_name
 * @property string|null $bank_address
 * @property string|null $bank_bic
 * @property string|null $bank_account_number
 * @property string|null $bank_account_iban
 * @property \App\Models\Address|null $address
 */
class Company extends Model
{
    /** @use HasFactory<\Database\Factories\CompanyFactory> */
    use HasFactory;

    protected $guarded = false;

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }
}
