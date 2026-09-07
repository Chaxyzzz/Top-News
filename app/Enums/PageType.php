<?php

namespace App\Enums;

enum PageType: string
{
    case About = 'about';
    case EditorialTeam = 'editorial_team';
    case EditorialGuidelines = 'editorial_guidelines';
    case PrivacyPolicy = 'privacy_policy';
    case Terms = 'terms';
    case Disclaimer = 'disclaimer';
    case AdvertisingInfo = 'advertising_info';
    case Contact = 'contact';

    /**
     * Get human-readable label in Indonesian.
     */
    public function label(): string
    {
        return match ($this) {
            self::About => 'Tentang TopNews',
            self::EditorialTeam => 'Susunan Dewan Redaksi',
            self::EditorialGuidelines => 'Pedoman & Standar Etika Redaksi',
            self::PrivacyPolicy => 'Kebijakan Privasi',
            self::Terms => 'Syarat & Ketentuan',
            self::Disclaimer => 'Sanggahan (Disclaimer)',
            self::AdvertisingInfo => 'Informasi Kerja Sama Iklan',
            self::Contact => 'Kontak Redaksi',
        };
    }

    /**
     * Get the dedicated public route name.
     */
    public function routeName(): string
    {
        return match ($this) {
            self::About => 'about',
            self::EditorialTeam => 'editorial.team',
            self::EditorialGuidelines => 'editorial.guidelines',
            self::PrivacyPolicy => 'privacy',
            self::Terms => 'terms',
            self::Disclaimer => 'disclaimer',
            self::AdvertisingInfo => 'advertise',
            self::Contact => 'contact',
        };
    }

    /**
     * Get standard slug for the system page.
     */
    public function defaultSlug(): string
    {
        return match ($this) {
            self::About => 'tentang-kami',
            self::EditorialTeam => 'dewan-redaksi',
            self::EditorialGuidelines => 'pedoman-redaksi',
            self::PrivacyPolicy => 'kebijakan-privasi',
            self::Terms => 'syarat-ketentuan',
            self::Disclaimer => 'disclaimer',
            self::AdvertisingInfo => 'pasang-iklan',
            self::Contact => 'kontak',
        };
    }

    /**
     * Check if this is a core protected institutional page.
     */
    public function isCoreProtected(): bool
    {
        return in_array($this, [
            self::About,
            self::EditorialGuidelines,
            self::PrivacyPolicy,
            self::Terms,
            self::Disclaimer,
            self::Contact,
        ], true);
    }
}
