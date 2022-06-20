<?php

require_once(ROOT_DIR . 'lang/AvailableLanguage.php');

class AvailableLanguages
{
    /**
     * @return array|AvailableLanguage[]
     */
    public static function GetAvailableLanguages()
    {
        return [
            'ar' => new AvailableLanguage('ar', 'ar.php', 'عربى'),
            'en_us' => new AvailableLanguage('en_us', 'en_us.php', 'English US'),
            'en_gb' => new AvailableLanguage('en_gb', 'en_gb.php', 'English GB'),
            'fr_fr' => new AvailableLanguage('fr_fr', 'fr_fr.php', 'Fran&ccedil;ais'),
        ];
    }

    public static function Contains($language)
    {
        $languages = self::GetAvailableLanguages();

        return array_key_exists(strtolower($language), $languages);
    }
}
