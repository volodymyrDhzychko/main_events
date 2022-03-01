<?php

/**
 * Helper functions for multilingualpress
 *
 * @link       https://usoftware.co/
 * @since      1.0.0
 *
 * @package    Events_Main_Plugin
 * @subpackage Events_Main_Plugin/admin/partials
 */


 /**
  * Generic multilingualpress plugin function
  *
  * sourse: https://multilingualpress.org/docs/create-custom-language-switcher-multilingualpress-3/
  *
  * @return array of objects
  */
function dffmain_mlp_get_translations() {

   $args = \Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs::forContext(new \Inpsyde\MultilingualPress\Framework\WordpressContext())
      ->forSiteId(get_current_blog_id())
      ->includeBase();

   $translations = \Inpsyde\MultilingualPress\resolve(
      \Inpsyde\MultilingualPress\Framework\Api\Translations::class
   )->searchTranslations($args);

   return $translations;
}

/**
 * Generic multilingualpress plugin function
 * 
 * sourse: https://multilingualpress.org/docs/get-connected-post-ids-current-post-id/
 * 
 * @returns array [site_id -> post_id]
 */
function multilingualpress_get_ids( $curr_post_id, $curr_site_id ) {

    $translations = \Inpsyde\MultilingualPress\translationIds( $curr_post_id, 'post', $curr_site_id );

    return $translations;
}

/**
 * Checks if site has RTL direction. Uses multilingualpress plugin function.
 * 
 * @returns boolean 
 */
function dffmain_mlp_check_if_is_rtl() {

    $translations = dffmain_mlp_get_translations();
    $current_is_rtl = false;

    foreach ($translations as $translation) {

        $language = $translation->language();
        $remote_site_id = $translation->remoteSiteId();
        if ( get_current_blog_id() == $remote_site_id ) {
            $current_is_rtl = $language->isRtl();
        }
    }
    return $current_is_rtl;
}

/**
 * Get array of IDs connected translations
 * 
 * @returns [array] $site_id => $post_id
 */
function get_translations_ids( $curr_post_id = 0, $curr_site_id = 0 ) {
    
    if ( !$curr_post_id ) {
        $curr_post_id = get_the_ID();
    }

    if ( !$curr_site_id ) {
        $curr_site_id = get_current_blog_id();
    }
    
    $translations = \Inpsyde\MultilingualPress\translationIds($curr_post_id, 'post', $curr_site_id);
    unset($translations[$curr_site_id]); 

    return $translations;
}

/**
 * Get array of connected translations with languages
 * 
 * @param site_id
 * 
 * @returns array 
 * [ 
 * language_name, 
 * language_locale, 
 * is_rtl, 
 * remote_site_id, 
 * remote_post_id, 
 * remote_site_url, 
 * source_site_id, 
 * source_post_id 
 * ]
 */
function get_translations_data() {

    $data_array = [];
    $main_site_id = get_main_site_id();
    $curr_site_id = get_current_blog_id();  
    $curr_post_id = get_the_ID();
    $connections = \Inpsyde\MultilingualPress\translationIds( $curr_post_id, 'post', $curr_site_id );
    
    $args = \Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs::forContext(new \Inpsyde\MultilingualPress\Framework\WordpressContext())
    ->forSiteId( $main_site_id )
    ->includeBase();
    $translations = \Inpsyde\MultilingualPress\resolve(\Inpsyde\MultilingualPress\Framework\Api\Translations::class)->searchTranslations( $args );

    $i = 0;
    foreach ($translations as $translation) {
        $language        = $translation->language();
        $language_locale = $language->locale();
        $language_name   = $language->isoName();
        $is_rtl          = $language->isRtl();

        $remote_site_id = $translation->remoteSiteId();
        $remote_post_id = $connections[$remote_site_id];
        $remote_site_url = !empty( $remote_post_id ) ? get_site_url( $remote_site_id, '?p=' . $remote_post_id ) : get_site_url( $remote_site_id, 'events' );

        $source_site_id  = $translation->sourceSiteId();
        $source_post_id  = $connections[$source_site_id];
        
        if ( $curr_site_id != $remote_site_id ) {
            $data_array[$i]['language_name']    = $language_name;
            $data_array[$i]['language_locale']  = $language_locale;
            $data_array[$i]['is_rtl']           = $is_rtl;

            $data_array[$i]['remote_site_id']   = $remote_site_id;
            $data_array[$i]['remote_post_id']   = $remote_post_id;
            $data_array[$i]['remote_site_url']  = $remote_site_url;
            $data_array[$i]['source_site_id']   = $source_site_id;
            $data_array[$i]['source_post_id']   = $source_post_id;

            $i++;
        }
    }
    return $data_array;
}

/**
 * Get converts locale (en_US) to full language name
 * 
 * @param $locale
 * @param $get_code 
 * 
 * @returns string - current language name OR locale 2 signs code if not found
 */
function convert_locale_to_full_name( $locale, $get_code = false ) {

    $locale_short = substr( $locale, 0, 2 );
    $wp_locale_conversion = [
        'af' => array(
            'name' => 'Afrikaans',
            'code' => 'af',
            'wp_locale' => 'af'
        ) ,
        'ak' => array(
            'name' => 'Akan',
            'code' => 'ak',
            'wp_locale' => 'ak'
        ) ,
        'sq' => array(
            'name' => 'Albanian',
            'code' => 'sq',
            'wp_locale' => 'sq'
        ) ,
        'am' => array(
            'name' => 'Amharic',
            'code' => 'am',
            'wp_locale' => 'am'
        ) ,
        'ar' => array(
            'name' => 'Arabic',
            'code' => 'ar',
            'wp_locale' => 'ar'
        ) ,
        'hy' => array(
            'name' => 'Armenian',
            'code' => 'hy',
            'wp_locale' => 'hy'
        ) ,
        'rup_MK' => array(
            'name' => 'Aromanian',
            'code' => 'rup',
            'wp_locale' => 'rup_MK'
        ) ,
        'as' => array(
            'name' => 'Assamese',
            'code' => 'as',
            'wp_locale' => 'as'
        ) ,
        'az' => array(
            'name' => 'Azerbaijani',
            'code' => 'az',
            'wp_locale' => 'az'
        ) ,
        'az_TR' => array(
            'name' => 'Azerbaijani (Turkey)',
            'code' => 'az-tr',
            'wp_locale' => 'az_TR'
        ) ,
        'ba' => array(
            'name' => 'Bashkir',
            'code' => 'ba',
            'wp_locale' => 'ba'
        ) ,
        'eu' => array(
            'name' => 'Basque',
            'code' => 'eu',
            'wp_locale' => 'eu'
        ) ,
        'bel' => array(
            'name' => 'Belarusian',
            'code' => 'bel',
            'wp_locale' => 'bel'
        ) ,
        'bn_BD' => array(
            'name' => 'Bengali',
            'code' => 'bn',
            'wp_locale' => 'bn_BD'
        ) ,
        'bs_BA' => array(
            'name' => 'Bosnian',
            'code' => 'bs',
            'wp_locale' => 'bs_BA'
        ) ,
        'bg_BG' => array(
            'name' => 'Bulgarian',
            'code' => 'bg',
            'wp_locale' => 'bg_BG'
        ) ,
        'my_MM' => array(
            'name' => 'Burmese',
            'code' => 'mya',
            'wp_locale' => 'my_MM'
        ) ,
        'ca' => array(
            'name' => 'Catalan',
            'code' => 'ca',
            'wp_locale' => 'ca'
        ) ,
        'bal' => array(
            'name' => 'Catalan (Balear)',
            'code' => 'bal',
            'wp_locale' => 'bal'
        ) ,
        'zh_CN' => array(
            'name' => 'Chinese (China)',
            'code' => 'zh-cn',
            'wp_locale' => 'zh_CN'
        ) ,
        'zh_HK' => array(
            'name' => 'Chinese (Hong Kong)',
            'code' => 'zh-hk',
            'wp_locale' => 'zh_HK'
        ) ,
        'zh_TW' => array(
            'name' => 'Chinese (Taiwan)',
            'code' => 'zh-tw',
            'wp_locale' => 'zh_TW'
        ) ,
        'co' => array(
            'name' => 'Corsican',
            'code' => 'co',
            'wp_locale' => 'co'
        ) ,
        'hr' => array(
            'name' => 'Croatian',
            'code' => 'hr',
            'wp_locale' => 'hr'
        ) ,
        'cs_CZ' => array(
            'name' => 'Czech',
            'code' => 'cs',
            'wp_locale' => 'cs_CZ'
        ) ,
        'da_DK' => array(
            'name' => 'Danish',
            'code' => 'da',
            'wp_locale' => 'da_DK'
        ) ,
        'dv' => array(
            'name' => 'Dhivehi',
            'code' => 'dv',
            'wp_locale' => 'dv'
        ) ,
        'nl_NL' => array(
            'name' => 'Dutch',
            'code' => 'nl',
            'wp_locale' => 'nl_NL'
        ) ,
        'nl_BE' => array(
            'name' => 'Dutch (Belgium)',
            'code' => 'nl-be',
            'wp_locale' => 'nl_BE'
        ) ,
        'en_US' => array(
            'name' => 'English',
            'code' => 'en',
            'wp_locale' => 'en_US'
        ) ,
        'en_AU' => array(
            'name' => 'English (Australia)',
            'code' => 'en-au',
            'wp_locale' => 'en_AU'
        ) ,
        'en_CA' => array(
            'name' => 'English (Canada)',
            'code' => 'en-ca',
            'wp_locale' => 'en_CA'
        ) ,
        'en_GB' => array(
            'name' => 'English (UK)',
            'code' => 'en-gb',
            'wp_locale' => 'en_GB'
        ) ,
        'eo' => array(
            'name' => 'Esperanto',
            'code' => 'eo',
            'wp_locale' => 'eo'
        ) ,
        'et' => array(
            'name' => 'Estonian',
            'code' => 'et',
            'wp_locale' => 'et'
        ) ,
        'fo' => array(
            'name' => 'Faroese',
            'code' => 'fo',
            'wp_locale' => 'fo'
        ) ,
        'fi' => array(
            'name' => 'Finnish',
            'code' => 'fi',
            'wp_locale' => 'fi'
        ) ,
        'fr_BE' => array(
            'name' => 'French (Belgium)',
            'code' => 'fr-be',
            'wp_locale' => 'fr_BE'
        ) ,
        'fr_FR' => array(
            'name' => 'French (France)',
            'code' => 'fr',
            'wp_locale' => 'fr_FR'
        ) ,
        'fy' => array(
            'name' => 'Frisian',
            'code' => 'fy',
            'wp_locale' => 'fy'
        ) ,
        'fuc' => array(
            'name' => 'Fulah',
            'code' => 'fuc',
            'wp_locale' => 'fuc'
        ) ,
        'gl_ES' => array(
            'name' => 'Galician',
            'code' => 'gl',
            'wp_locale' => 'gl_ES'
        ) ,
        'ka_GE' => array(
            'name' => 'Georgian',
            'code' => 'ka',
            'wp_locale' => 'ka_GE'
        ) ,
        'de_DE' => array(
            'name' => 'German',
            'code' => 'de',
            'wp_locale' => 'de_DE'
        ) ,
        'de_CH' => array(
            'name' => 'German (Switzerland)',
            'code' => 'de-ch',
            'wp_locale' => 'de_CH'
        ) ,
        'el' => array(
            'name' => 'Greek',
            'code' => 'el',
            'wp_locale' => 'el'
        ) ,
        'gn' => array(
            'name' => 'Guaraní',
            'code' => 'gn',
            'wp_locale' => 'gn'
        ) ,
        'gu_IN' => array(
            'name' => 'Gujarati',
            'code' => 'gu',
            'wp_locale' => 'gu_IN'
        ) ,
        'haw_US' => array(
            'name' => 'Hawaiian',
            'code' => 'haw',
            'wp_locale' => 'haw_US'
        ) ,
        'haz' => array(
            'name' => 'Hazaragi',
            'code' => 'haz',
            'wp_locale' => 'haz'
        ) ,
        'he_IL' => array(
            'name' => 'Hebrew',
            'code' => 'he',
            'wp_locale' => 'he_IL'
        ) ,
        'hi_IN' => array(
            'name' => 'Hindi',
            'code' => 'hi',
            'wp_locale' => 'hi_IN'
        ) ,
        'hu_HU' => array(
            'name' => 'Hungarian',
            'code' => 'hu',
            'wp_locale' => 'hu_HU'
        ) ,
        'is_IS' => array(
            'name' => 'Icelandic',
            'code' => 'is',
            'wp_locale' => 'is_IS'
        ) ,
        'ido' => array(
            'name' => 'Ido',
            'code' => 'ido',
            'wp_locale' => 'ido'
        ) ,
        'id_ID' => array(
            'name' => 'Indonesian',
            'code' => 'id',
            'wp_locale' => 'id_ID'
        ) ,
        'ga' => array(
            'name' => 'Irish',
            'code' => 'ga',
            'wp_locale' => 'ga'
        ) ,
        'it_IT' => array(
            'name' => 'Italian',
            'code' => 'it',
            'wp_locale' => 'it_IT'
        ) ,
        'ja' => array(
            'name' => 'Japanese',
            'code' => 'ja',
            'wp_locale' => 'ja'
        ) ,
        'jv_ID' => array(
            'name' => 'Javanese',
            'code' => 'jv',
            'wp_locale' => 'jv_ID'
        ) ,
        'kn' => array(
            'name' => 'Kannada',
            'code' => 'kn',
            'wp_locale' => 'kn'
        ) ,
        'kk' => array(
            'name' => 'Kazakh',
            'code' => 'kk',
            'wp_locale' => 'kk'
        ) ,
        'km' => array(
            'name' => 'Khmer',
            'code' => 'km',
            'wp_locale' => 'km'
        ) ,
        'kin' => array(
            'name' => 'Kinyarwanda',
            'code' => 'kin',
            'wp_locale' => 'kin'
        ) ,
        'ky_KY' => array(
            'name' => 'Kirghiz',
            'code' => 'ky',
            'wp_locale' => 'ky_KY'
        ) ,
        'ko_KR' => array(
            'name' => 'Korean',
            'code' => 'ko',
            'wp_locale' => 'ko_KR'
        ) ,
        'ckb' => array(
            'name' => 'Kurdish (Sorani)',
            'code' => 'ckb',
            'wp_locale' => 'ckb'
        ) ,
        'lo' => array(
            'name' => 'Lao',
            'code' => 'lo',
            'wp_locale' => 'lo'
        ) ,
        'lv' => array(
            'name' => 'Latvian',
            'code' => 'lv',
            'wp_locale' => 'lv'
        ) ,
        'li' => array(
            'name' => 'Limburgish',
            'code' => 'li',
            'wp_locale' => 'li'
        ) ,
        'lin' => array(
            'name' => 'Lingala',
            'code' => 'lin',
            'wp_locale' => 'lin'
        ) ,
        'lt_LT' => array(
            'name' => 'Lithuanian',
            'code' => 'lt',
            'wp_locale' => 'lt_LT'
        ) ,
        'lb_LU' => array(
            'name' => 'Luxembourgish',
            'code' => 'lb',
            'wp_locale' => 'lb_LU'
        ) ,
        'mk_MK' => array(
            'name' => 'Macedonian',
            'code' => 'mk',
            'wp_locale' => 'mk_MK'
        ) ,
        'mg_MG' => array(
            'name' => 'Malagasy',
            'code' => 'mg',
            'wp_locale' => 'mg_MG'
        ) ,
        'ms_MY' => array(
            'name' => 'Malay',
            'code' => 'ms',
            'wp_locale' => 'ms_MY'
        ) ,
        'ml_IN' => array(
            'name' => 'Malayalam',
            'code' => 'ml',
            'wp_locale' => 'ml_IN'
        ) ,
        'mr' => array(
            'name' => 'Marathi',
            'code' => 'mr',
            'wp_locale' => 'mr'
        ) ,
        'xmf' => array(
            'name' => 'Mingrelian',
            'code' => 'xmf',
            'wp_locale' => 'xmf'
        ) ,
        'mn' => array(
            'name' => 'Mongolian',
            'code' => 'mn',
            'wp_locale' => 'mn'
        ) ,
        'me_ME' => array(
            'name' => 'Montenegrin',
            'code' => 'me',
            'wp_locale' => 'me_ME'
        ) ,
        'ne_NP' => array(
            'name' => 'Nepali',
            'code' => 'ne',
            'wp_locale' => 'ne_NP'
        ) ,
        'nb_NO' => array(
            'name' => 'Norwegian (Bokmål)',
            'code' => 'nb',
            'wp_locale' => 'nb_NO'
        ) ,
        'nn_NO' => array(
            'name' => 'Norwegian (Nynorsk)',
            'code' => 'nn',
            'wp_locale' => 'nn_NO'
        ) ,
        'ory' => array(
            'name' => 'Oriya',
            'code' => 'ory',
            'wp_locale' => 'ory'
        ) ,
        'os' => array(
            'name' => 'Ossetic',
            'code' => 'os',
            'wp_locale' => 'os'
        ) ,
        'ps' => array(
            'name' => 'Pashto',
            'code' => 'ps',
            'wp_locale' => 'ps'
        ) ,
        'fa_IR' => array(
            'name' => 'Persian',
            'code' => 'fa',
            'wp_locale' => 'fa_IR'
        ) ,
        'fa_AF' => array(
            'name' => 'Persian (Afghanistan)',
            'code' => 'fa-af',
            'wp_locale' => 'fa_AF'
        ) ,
        'pl_PL' => array(
            'name' => 'Polish',
            'code' => 'pl',
            'wp_locale' => 'pl_PL'
        ) ,
        'pt_BR' => array(
            'name' => 'Portuguese (Brazil)',
            'code' => 'pt-br',
            'wp_locale' => 'pt_BR'
        ) ,
        'pt_PT' => array(
            'name' => 'Portuguese (Portugal)',
            'code' => 'pt',
            'wp_locale' => 'pt_PT'
        ) ,
        'pa_IN' => array(
            'name' => 'Punjabi',
            'code' => 'pa',
            'wp_locale' => 'pa_IN'
        ) ,
        'rhg' => array(
            'name' => 'Rohingya',
            'code' => 'rhg',
            'wp_locale' => 'rhg'
        ) ,
        'ro_RO' => array(
            'name' => 'Romanian',
            'code' => 'ro',
            'wp_locale' => 'ro_RO'
        ) ,
        'ru_RU' => array(
            'name' => 'Russian',
            'code' => 'ru',
            'wp_locale' => 'ru_RU'
        ) ,
        'ru_UA' => array(
            'name' => 'Russian (Ukraine)',
            'code' => 'ru-ua',
            'wp_locale' => 'ru_UA'
        ) ,
        'rue' => array(
            'name' => 'Rusyn',
            'code' => 'rue',
            'wp_locale' => 'rue'
        ) ,
        'sah' => array(
            'name' => 'Sakha',
            'code' => 'sah',
            'wp_locale' => 'sah'
        ) ,
        'sa_IN' => array(
            'name' => 'Sanskrit',
            'code' => 'sa-in',
            'wp_locale' => 'sa_IN'
        ) ,
        'srd' => array(
            'name' => 'Sardinian',
            'code' => 'srd',
            'wp_locale' => 'srd'
        ) ,
        'gd' => array(
            'name' => 'Scottish Gaelic',
            'code' => 'gd',
            'wp_locale' => 'gd'
        ) ,
        'sr_RS' => array(
            'name' => 'Serbian',
            'code' => 'sr',
            'wp_locale' => 'sr_RS'
        ) ,
        'sd_PK' => array(
            'name' => 'Sindhi',
            'code' => 'sd',
            'wp_locale' => 'sd_PK'
        ) ,
        'si_LK' => array(
            'name' => 'Sinhala',
            'code' => 'si',
            'wp_locale' => 'si_LK'
        ) ,
        'sk_SK' => array(
            'name' => 'Slovak',
            'code' => 'sk',
            'wp_locale' => 'sk_SK'
        ) ,
        'sl_SI' => array(
            'name' => 'Slovenian',
            'code' => 'sl',
            'wp_locale' => 'sl_SI'
        ) ,
        'so_SO' => array(
            'name' => 'Somali',
            'code' => 'so',
            'wp_locale' => 'so_SO'
        ) ,
        'azb' => array(
            'name' => 'South Azerbaijani',
            'code' => 'azb',
            'wp_locale' => 'azb'
        ) ,
        'es_AR' => array(
            'name' => 'Spanish (Argentina)',
            'code' => 'es-ar',
            'wp_locale' => 'es_AR'
        ) ,
        'es_CL' => array(
            'name' => 'Spanish (Chile)',
            'code' => 'es-cl',
            'wp_locale' => 'es_CL'
        ) ,
        'es_CO' => array(
            'name' => 'Spanish (Colombia)',
            'code' => 'es-co',
            'wp_locale' => 'es_CO'
        ) ,
        'es_MX' => array(
            'name' => 'Spanish (Mexico)',
            'code' => 'es-mx',
            'wp_locale' => 'es_MX'
        ) ,
        'es_PE' => array(
            'name' => 'Spanish (Peru)',
            'code' => 'es-pe',
            'wp_locale' => 'es_PE'
        ) ,
        'es_PR' => array(
            'name' => 'Spanish (Puerto Rico)',
            'code' => 'es-pr',
            'wp_locale' => 'es_PR'
        ) ,
        'es_ES' => array(
            'name' => 'Spanish (Spain)',
            'code' => 'es',
            'wp_locale' => 'es_ES'
        ) ,
        'es_VE' => array(
            'name' => 'Spanish (Venezuela)',
            'code' => 'es-ve',
            'wp_locale' => 'es_VE'
        ) ,
        'su_ID' => array(
            'name' => 'Sundanese',
            'code' => 'su',
            'wp_locale' => 'su_ID'
        ) ,
        'sw' => array(
            'name' => 'Swahili',
            'code' => 'sw',
            'wp_locale' => 'sw'
        ) ,
        'sv_SE' => array(
            'name' => 'Swedish',
            'code' => 'sv',
            'wp_locale' => 'sv_SE'
        ) ,
        'gsw' => array(
            'name' => 'Swiss German',
            'code' => 'gsw',
            'wp_locale' => 'gsw'
        ) ,
        'tl' => array(
            'name' => 'Tagalog',
            'code' => 'tl',
            'wp_locale' => 'tl'
        ) ,
        'tg' => array(
            'name' => 'Tajik',
            'code' => 'tg',
            'wp_locale' => 'tg'
        ) ,
        'tzm' => array(
            'name' => 'Tamazight (Central Atlas)',
            'code' => 'tzm',
            'wp_locale' => 'tzm'
        ) ,
        'ta_IN' => array(
            'name' => 'Tamil',
            'code' => 'ta',
            'wp_locale' => 'ta_IN'
        ) ,
        'ta_LK' => array(
            'name' => 'Tamil (Sri Lanka)',
            'code' => 'ta-lk',
            'wp_locale' => 'ta_LK'
        ) ,
        'tt_RU' => array(
            'name' => 'Tatar',
            'code' => 'tt',
            'wp_locale' => 'tt_RU'
        ) ,
        'te' => array(
            'name' => 'Telugu',
            'code' => 'te',
            'wp_locale' => 'te'
        ) ,
        'th' => array(
            'name' => 'Thai',
            'code' => 'th',
            'wp_locale' => 'th'
        ) ,
        'bo' => array(
            'name' => 'Tibetan',
            'code' => 'bo',
            'wp_locale' => 'bo'
        ) ,
        'tir' => array(
            'name' => 'Tigrinya',
            'code' => 'tir',
            'wp_locale' => 'tir'
        ) ,
        'tr_TR' => array(
            'name' => 'Turkish',
            'code' => 'tr',
            'wp_locale' => 'tr_TR'
        ) ,
        'tuk' => array(
            'name' => 'Turkmen',
            'code' => 'tuk',
            'wp_locale' => 'tuk'
        ) ,
        'ug_CN' => array(
            'name' => 'Uighur',
            'code' => 'ug',
            'wp_locale' => 'ug_CN'
        ) ,
        'uk' => array(
            'name' => 'Ukrainian',
            'code' => 'uk',
            'wp_locale' => 'uk'
        ) ,
        'ur' => array(
            'name' => 'Urdu',
            'code' => 'ur',
            'wp_locale' => 'ur'
        ) ,
        'uz_UZ' => array(
            'name' => 'Uzbek',
            'code' => 'uz',
            'wp_locale' => 'uz_UZ'
        ) ,
        'vi' => array(
            'name' => 'Vietnamese',
            'code' => 'vi',
            'wp_locale' => 'vi'
        ) ,
        'wa' => array(
            'name' => 'Walloon',
            'code' => 'wa',
            'wp_locale' => 'wa'
        ) ,
        'cy' => array(
            'name' => 'Welsh',
            'code' => 'cy',
            'wp_locale' => 'cy'
        ) ,
        'or' => array(
            'name' => 'Yoruba',
            'code' => 'yor',
            'wp_locale' => 'yor'
        )
    ];
    if ( isset( $wp_locale_conversion[$locale] ) ) {

        $language_name = $wp_locale_conversion[$locale]['name'];
        if ( $get_code ) {
            $language_name = $wp_locale_conversion[$locale]['code'];
        }
    }elseif ( isset( $wp_locale_conversion[$locale_short] ) ) {

        $language_name = $wp_locale_conversion[$locale_short]['name'];
        if ( $get_code ) {
            $language_name = $wp_locale_conversion[$locale_short]['code'];
        }
    }else{
        $language_name = $locale_short;
    }
    

    return $language_name;
}

/**
 * Get current site language name
 * 
 * @returns string - current language name
 */
function get_current_language_name() {

    $locale = get_locale();

    $current_language_name = convert_locale_to_full_name( $locale );

    return $current_language_name;
}

/**
 * get post meta based on site_id post_id meta_key
 * 
 * param $site_id
 * param $post_id
 * param $key
 * 
 * return post meta value
 * 
 */
function multisite_post_meta( $site_id, $post_id, $key ){

	global $wpdb;
	$blogPostsTable = 'wp_' . $site_id . '_postmeta';
	$query = "SELECT meta_value FROM {$blogPostsTable} WHERE post_id ='{$post_id}' AND meta_key='{$key}'";
	$meta_value = $wpdb->get_var($query);

	return $meta_value;
}

/**
 * get site option based on site_id meta_key
 * 
 * param $site_id
 * param $key
 * 
 * return option value
 */
function multisite_get_option( $site_id, $key ){
	switch_to_blog( $site_id );
	$val = get_option( $key );
	restore_current_blog();	
	return $val;
}

/**
 * Get Main site option based on  meta_key
 * 
 * @param $key
 * 
 * @return option value
 */
function main_site_get_option( $key ){
	switch_to_blog( get_main_site_id() );
	$val = get_option( $key );
	restore_current_blog();	
	return $val;
}

/**
 * Update site option based on site_id meta_key
 * 
 * @param $site_id
 * @param $key
 * @param $newvalue
 * 
 * @return boolean updated or not
 */
function multisite_update_option( $site_id, $key, $newvalue ){
	switch_to_blog( $site_id );
	$result = update_option( $key, $newvalue, false );
	restore_current_blog();	
	return $result;
}

/**
 * Update Main site option 
 * 
 * @param $key
 * @param $newvalue
 * @param $autoload - optional
 * 
 * @return boolean - updated or not
 */
function main_site_update_option( $key, $newvalue, $autoload = false ){
	switch_to_blog( get_main_site_id() );
	$result = update_option( $key, $newvalue, $autoload );
	restore_current_blog();	
	return $result;
}

/**
 * Update translation sites meta which is common to all
 *
 * @param [array] $translations
 * @param [int] $source_site_id
 * @param [int] $source_post_id
 * @return void
 */
function update_common_meta_fields( $translations, $source_site_id, $source_post_id ){

    if ( !is_array( $translations ) ) return;
    if ( get_current_blog_id() != $source_site_id ) return;

    // get data from source
    // Cost settings
    $event_cost_name = get_post_meta( $source_post_id, 'event_cost_name' );

    // Reminder settings
    $event_reminder_select_box = get_post_meta( $source_post_id, 'event_reminder_select_box' );

    // Date settings
    $event_date_select = get_post_meta( $source_post_id, 'event_date_select' );

    // End date settings
    $event_end_date_select = get_post_meta( $source_post_id, 'event_end_date_select' );

    // Time settings
    $event_time_start_select = get_post_meta( $source_post_id, 'event_time_start_select' );
    $event_time_end_select = get_post_meta( $source_post_id, 'event_time_end_select' );

    // Google map settings
    $event_google_map_input = get_post_meta( $source_post_id, 'event_google_map_input' );

    // Detail image
    $event_detail_img = get_post_meta( $source_post_id, 'event_detail_img' );

    // Security Code Setting
    $security_code_checkbox = get_post_meta( $source_post_id, 'security_code_checkbox' );
    $event_security_code = get_post_meta( $source_post_id, 'event_security_code' );

    // Reminder date
    $event_reminder_date = get_post_meta( $source_post_id, 'event_reminder_date' );

    // Special instruction
    $event_special_instruction = get_post_meta( $source_post_id, 'event_special_instruction' );

    // Google maps embeded code
    $google_embed_maps_code = get_post_meta( $source_post_id, 'google_embed_maps_code' );


    // Attendee meta data
    $event_attendee_limit_count = get_post_meta( $source_post_id, 'event_attendee_limit_count' );
    $event_registration_close_messagez = get_post_meta( $source_post_id, 'event_registration_close_message' );


    foreach ( $translations as $site_id => $post_id ) {

        switch_to_blog( $site_id );

            // Cost settings
			update_post_meta( $post_id, 'event_cost_name', $event_cost_name );

			// Reminder settings
			update_post_meta( $post_id, 'event_reminder_select_box', $event_reminder_select_box );

			// Date settings
			update_post_meta( $post_id, 'event_date_select', $event_date_select );

			// End date settings
			update_post_meta( $post_id, 'event_end_date_select', $event_end_date_select );

			// Time settings
			update_post_meta( $post_id, 'event_time_start_select', $event_time_start_select );
			update_post_meta( $post_id, 'event_time_end_select', $event_time_end_select );

			// Google map settings
			update_post_meta( $post_id, 'event_google_map_input', $event_google_map_input );

			// Detail image
			update_post_meta( $post_id, 'event_detail_img', sanitize_text_field( $meta_key ) );

			// Security Code Setting
			update_post_meta( $post_id, 'security_code_checkbox', $security_code_checkbox );
			update_post_meta( $post_id, 'event_security_code', $event_security_code );

			// Reminder date
			update_post_meta( $post_id, 'event_reminder_date', $event_reminder_date );

			// Special instruction
			update_post_meta( $post_id, 'event_special_instruction', $event_special_instruction );

			// Google maps embeded code
			update_post_meta( $post_id, 'google_embed_maps_code', $google_embed_maps_code );


			// Attendee meta data
			update_post_meta( $post_id, 'event_attendee_limit_count', $event_attendee_limit_count );
			update_post_meta( $post_id, 'event_registration_close_message', $event_registration_close_message );

        restore_current_blog();
    }
}