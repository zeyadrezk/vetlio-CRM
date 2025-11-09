<?php

namespace App\Services;

use App\Models\Sequence;
use Illuminate\Support\Facades\DB;

class SequenceGenerator
{
    protected ?string $model = null;
    protected ?string $pattern = null;
    protected array $context = [];
    protected bool $resetYearly = false;
    protected ?int $padding = null;

    public static function make(): static
    {
        return new static();
    }

    /** @return $this */
    public function withModel(string $model): static
    {
        $this->model = $model;
        return $this;
    }

    /** @return $this */
    public function withPattern(string $pattern): static
    {
        $this->pattern = $pattern;
        return $this;
    }

    /** @return $this */
    public function withContext(array $context): static
    {
        $this->context = $context;
        return $this;
    }

    /** @return $this */
    public function withResetYearly(bool $reset = true): static
    {
        $this->resetYearly = $reset;
        return $this;
    }

    /** @return $this */
    public function withPadding(int $length): static
    {
        $this->padding = $length;
        return $this;
    }

    public function generate(): array
    {
        return $this->process(increment: true);
    }

    public function preview(): array
    {
        return $this->process(increment: false);
    }

    /**
     * Core logic (shared by generate and preview)
     */
    protected function process(bool $increment = true): array
    {
        if (! $this->model || ! $this->pattern) {
            throw new \InvalidArgumentException('Model and pattern must be set before generating a sequence.');
        }

        $year = now()->year;
        $contextHash = $this->makeContextHash();

        return DB::transaction(function () use ($contextHash, $year, $increment) {
            $sequence = Sequence::lockForUpdate()
                ->where('model', $this->model)
                ->where('pattern', $this->pattern)
                ->where('context_hash', $contextHash)
                ->when($this->resetYearly, fn($q) => $q->where('year', $year))
                ->first();

            if (! $sequence) {
                $sequence = Sequence::create([
                    'model' => $this->model,
                    'pattern' => $this->pattern,
                    'context_hash' => $contextHash,
                    'current_number' => 0,
                    'year' => $this->resetYearly ? $year : null,
                ]);
            }

            // Ako je resetYearly i promijenila se godina
            if ($this->resetYearly && $sequence->year !== $year) {
                $sequence->current_number = 0;
                $sequence->year = $year;
            }

            // Ako je preview, NE povećavaj broj
            $number = $increment
                ? $sequence->current_number + 1
                : $sequence->current_number + 1; // prikazuje idući broj, ali ne sprema

            if ($increment) {
                $sequence->current_number++;
                $sequence->save();
            }

            // Formatiraj s paddingom
            $padded = $this->padding
                ? str_pad($number, $this->padding, '0', STR_PAD_LEFT)
                : $number;

            $parts = array_merge([
                'raw_number' => $number,
                'number' => $padded,
                'padded_number' => $padded,
                'year' => $year,
            ], $this->context);

            $sequenceString = strtr(
                $this->pattern,
                collect($parts)->mapWithKeys(fn ($v, $k) => ["{{{$k}}}" => $v])->toArray()
            );

            return [
                'sequence' => $sequenceString,
                'parts' => $parts,
                'is_preview' => ! $increment,
            ];
        });
    }

    protected function makeContextHash(): string
    {
        if (empty($this->context)) {
            return 'default';
        }

        ksort($this->context);

        return md5(json_encode($this->context));
    }
}
