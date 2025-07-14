<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property \App\Models\Upload|null $signature
 * @property \App\Models\Upload|null $logo
 * @property \App\Models\Company $supplier
 * @property \App\Models\Company $customer
 */
class Invoice extends Model
{
    use HasUuid;

    protected $guarded = false;

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function signature(): BelongsTo
    {
        return $this->belongsTo(Upload::class);
    }

    public function logo(): BelongsTo
    {
        return $this->belongsTo(Upload::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
