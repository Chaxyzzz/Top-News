<?php

namespace App\Enums;

enum ReactionType: string
{
    case Useful = 'useful';
    case Interesting = 'interesting';
    case Important = 'important';

    /**
     * Get the human-readable Indonesian label.
     */
    public function label(): string
    {
        return match ($this) {
            self::Useful => 'Bermanfaat',
            self::Interesting => 'Menarik',
            self::Important => 'Penting',
        };
    }
}
