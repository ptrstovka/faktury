<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property \App\Models\Account $account
 * @property string $sequence
 * @property string $format
 * @property int $next_number
 */
class NumberSequence extends Model
{
    protected $guarded = false;

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Increment next number of the sequence.
     */
    public function incrementNextNumber(): void
    {
        $this->next_number += 1;

        $this->save();
    }
}
