<?php

namespace App\Enums;

use App\Enums\Icons\PhosphorIcons;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum PatientGender: int implements HasLabel, HasIcon
{
    case Male = 1;
    case Female = 2;
    case Other = 3;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Male => __('enums.patient_gender.male'),
            self::Female => __('enums.patient_gender.female'),
            self::Other => __('enums.patient_gender.other'),
        };
    }

    public function getIcon(): PhosphorIcons
    {
        return match ($this) {
            self::Male => PhosphorIcons::GenderMale,
            self::Female => PhosphorIcons::GenderFemale,
            self::Other => PhosphorIcons::GenderIntersex,
        };
    }
}
