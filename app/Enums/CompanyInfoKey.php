<?php

declare(strict_types=1);

namespace App\Enums;

enum CompanyInfoKey: string
{
    // Contact
    case Phone = 'phone';
    case Email = 'email';
    case Whatsapp = 'whatsapp';
    case Address = 'address';
    case OfficeAddress = 'office_address';

    // Social Media
    case Facebook = 'facebook';
    case Instagram = 'instagram';
    case Twitter = 'twitter';

    // About - General
    case CompanyName = 'company_name';
    case Tagline = 'tagline';
    case FoundedYear = 'founded_year';
    case Vision = 'vision';
    case Mission1 = 'mission_1';
    case Mission2 = 'mission_2';
    case Mission3 = 'mission_3';

    // About - Hero Section
    case AboutHeroBadge = 'about_hero_badge';
    case AboutHeroTitle = 'about_hero_title';
    case AboutHeroSubtitle = 'about_hero_subtitle';
    case AboutHeroImage = 'about_hero_image';

    // About - History Section
    case AboutHistoryTitle = 'about_history_title';
    case AboutHistoryParagraph1 = 'about_history_paragraph_1';
    case AboutHistoryParagraph2 = 'about_history_paragraph_2';
    case CoveragePercentage = 'coverage_percentage';
    case ServiceHoursValue = 'service_hours_value';

    // About - Visi Misi Section
    case AboutVisiMisiSubtitle = 'about_visi_misi_subtitle';

    // About - Values Section
    case AboutValuesTitle = 'about_values_title';
    case AboutValuesSubtitle = 'about_values_subtitle';
    case Value1Title = 'value_1_title';
    case Value1Description = 'value_1_description';
    case Value2Title = 'value_2_title';
    case Value2Description = 'value_2_description';
    case Value3Title = 'value_3_title';
    case Value3Description = 'value_3_description';

    // About - Coverage Section
    case AboutCoverageTitle = 'about_coverage_title';
    case AboutCoverageDescription = 'about_coverage_description';

    // Operation
    case OfficeHours = 'office_hours';
    case WeekendHours = 'weekend_hours';
    case CallCenterHours = 'call_center_hours';

    public function getLabel(): string
    {
        return match ($this) {
            // Contact
            self::Phone => 'Nomor Telepon',
            self::Email => 'Email',
            self::Whatsapp => 'WhatsApp',
            self::Address => 'Alamat Lengkap',
            self::OfficeAddress => 'Alamat Kantor',

            // Social Media
            self::Facebook => 'URL Facebook',
            self::Instagram => 'URL Instagram',
            self::Twitter => 'URL Twitter',

            // About - General
            self::CompanyName => 'Nama Perusahaan',
            self::Tagline => 'Tagline',
            self::FoundedYear => 'Tahun Berdiri',
            self::Vision => 'Visi',
            self::Mission1 => 'Misi 1',
            self::Mission2 => 'Misi 2',
            self::Mission3 => 'Misi 3',

            // About - Hero
            self::AboutHeroBadge => 'Tentang - Hero Badge',
            self::AboutHeroTitle => 'Tentang - Hero Judul',
            self::AboutHeroSubtitle => 'Tentang - Hero Subjudul',
            self::AboutHeroImage => 'Tentang - Hero Gambar (URL)',

            // About - History
            self::AboutHistoryTitle => 'Tentang - Judul Sejarah',
            self::AboutHistoryParagraph1 => 'Tentang - Paragraf Sejarah 1',
            self::AboutHistoryParagraph2 => 'Tentang - Paragraf Sejarah 2',
            self::CoveragePercentage => 'Persentase Cakupan Wilayah',
            self::ServiceHoursValue => 'Jam Layanan (nilai)',

            // About - Visi Misi
            self::AboutVisiMisiSubtitle => 'Tentang - Subjudul Visi Misi',

            // About - Values
            self::AboutValuesTitle => 'Tentang - Judul Nilai Perusahaan',
            self::AboutValuesSubtitle => 'Tentang - Subjudul Nilai Perusahaan',
            self::Value1Title => 'Nilai 1 - Judul',
            self::Value1Description => 'Nilai 1 - Deskripsi',
            self::Value2Title => 'Nilai 2 - Judul',
            self::Value2Description => 'Nilai 2 - Deskripsi',
            self::Value3Title => 'Nilai 3 - Judul',
            self::Value3Description => 'Nilai 3 - Deskripsi',

            // About - Coverage
            self::AboutCoverageTitle => 'Tentang - Judul Cakupan Wilayah',
            self::AboutCoverageDescription => 'Tentang - Deskripsi Cakupan Wilayah',

            // Operation
            self::OfficeHours => 'Jam Kantor',
            self::WeekendHours => 'Jam Weekend',
            self::CallCenterHours => 'Jam Call Center',
        };
    }

    public function getGroup(): string
    {
        return match ($this) {
            self::Phone, self::Email, self::Whatsapp, self::Address, self::OfficeAddress => 'contact',
            self::Facebook, self::Instagram, self::Twitter => 'social',
            self::CompanyName, self::Tagline, self::FoundedYear, self::Vision,
            self::Mission1, self::Mission2, self::Mission3,
            self::AboutHeroBadge, self::AboutHeroTitle, self::AboutHeroSubtitle, self::AboutHeroImage,
            self::AboutHistoryTitle, self::AboutHistoryParagraph1, self::AboutHistoryParagraph2,
            self::CoveragePercentage, self::ServiceHoursValue,
            self::AboutVisiMisiSubtitle,
            self::AboutValuesTitle, self::AboutValuesSubtitle,
            self::Value1Title, self::Value1Description,
            self::Value2Title, self::Value2Description,
            self::Value3Title, self::Value3Description,
            self::AboutCoverageTitle, self::AboutCoverageDescription => 'about',
            self::OfficeHours, self::WeekendHours, self::CallCenterHours => 'operation',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function toArray(): array
    {
        return collect(self::cases())->mapWithKeys(fn ($case) => [$case->value => $case->getLabel()])->toArray();
    }

    /**
     * @return array<string, array<string, string>>
     */
    public static function groupedOptions(): array
    {
        $grouped = [];

        foreach (self::cases() as $case) {
            $group = $case->getGroup();
            $grouped[$group][$case->value] = $case->getLabel();
        }

        return $grouped;
    }
}
