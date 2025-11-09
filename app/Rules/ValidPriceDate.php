<?php

namespace App\Rules;

use App\Models\Price;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Carbon;

class ValidPriceDate implements ValidationRule
{
    public function __construct(
        protected $price, protected $priceable,
    )
    {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->priceable) {
            return;
        }

        $latest = Price::query()
            ->when($this->price, fn($q) => $q->whereKeyNot($this->price->id))
            ->whereMorphedTo('priceable', $this->priceable)
            ->max('valid_from_at');

        if (!$latest) {
            return;
        }

        $latestDate = Carbon::parse($latest);
        $newDate = Carbon::parse($value);

        if ($newDate->lte($latestDate)) {
            $fail("Datum mora biti veći od zadnjeg važećeg datuma cijene ({$latestDate->format('d.m.Y')}).");
        }
    }
}
