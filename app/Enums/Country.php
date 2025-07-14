<?php


namespace App\Enums;


use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use StackTrace\Ui\SelectOption;

enum Country: string
{
    case Slovakia = 'sk';
    case Germany = 'de';
    case Austria = 'at';
    case CzechRepublic = 'cz';
    case UnitedArabEmirates = 'ae';

    /**
     * Get the name of the country.
     */
    public function name(): string
    {
        // TODO: Pridať z jazykoveho suboru

        return Str::headline($this->name);
    }

    /**
     * Get the list of counries.
     */
    public static function all(): Collection
    {
        return collect(Country::cases())
            ->sortBy(fn (Country $country) => $country->name())
            ->values();
    }

    /**
     * Get the list of countries as select options.
     *
     * @return \Illuminate\Support\Collection<int, SelectOption>
     */
    public static function options(): Collection
    {
        return Country::all()->map(fn (Country $country) => new SelectOption($country->name(), $country->value));
    }
}
