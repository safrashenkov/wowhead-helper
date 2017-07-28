<?php



class WowHeadHelper
{
    /**
     * WowHead locale
     * @var string
     */
    static $locale = 'ru';


    /**
     * @param $url string url
     * @return string Request result
     */
    private static function __request($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    private static function __getMainPage()
    {
        return self::__request('http://' . self::$locale . '.wowhead.com');
    }

    public static function getAffixes($world = 'EU')
    {
        $page = self::__getMainPage();
        $html = \Sunra\PhpSimple\HtmlDomParser::str_get_html($page);
        $text = $html->find('div.tiw-region-' . $world, 0);

        $affixes = $text->find('table.tiw-group-mythicaffix td.icon-both a');
        $affix_arr = array();
        $i = 0;
        foreach ($affixes as $affix) {
            $affix->find('img', 0)->outertext = '';
            $affix_arr[$i++] = trim($affix->innertext);
        }
        return $affix_arr;
    }

    public static function getBuilding($world = 'EU')
    {
        $page = self::__getMainPage();
        $html = \Sunra\PhpSimple\HtmlDomParser::str_get_html($page);
        $text = $html->find('div.tiw-region-' . $world, 0);

        $buildings = $text->find('.tiw-group-wrapper-building table');
        $building_arr = [];
        foreach ($buildings as $building) {
            $t['name'] = $building->find('div.tiw-bs-status-name a', 0)->innertext;
            $t['status'] = $building->find('div.imitation-heading', 0)->innertext;
            $t['count'] = trim($building->find('div.tiw-bs-status-progress span', 0)->innertext);
            $building_arr[] = $t;
        }
        return $building_arr;
    }

    public static function getEmissaries($world = 'EU')
    {
        $page = self::__getMainPage();
        $html = \Sunra\PhpSimple\HtmlDomParser::str_get_html($page);
        $text = $html->find('div.tiw-region-' . $world, 0);

        $emissaries = $text->find('table.tiw-group-emissary tr[!class]');
        $emissaries_arr = [];
        foreach ($emissaries as $emissary) {
            $t['time'] = $emissary->find('td.tiw-line-ending-short', 0)->innertext;
            $t['emissary'] = $emissary->find('td.icon-none a', 0)->innertext;
            $t['href'] = 'http://' . self::$locale . '.wowhead.com' . $emissary->find('td.icon-none a', 0)->href;

            $emissaries_arr[] = $t;
        }
        return $emissaries_arr;
    }
}

