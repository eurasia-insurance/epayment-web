<?php

class Util_Misc {

    static $shortMonths = array(
        'ru' => array(
                'января' => 'янв.',
                'февраля' => 'фев.',
                'марта' => 'мар.',
                'апреля' => 'апр.',
                'мая' => 'мая',
                'июня' => 'июня',
                'июля' => 'июля',
                'августа' => 'авг.',
                'сентября' => 'сен.',
                'октября' => 'окт.',
                'ноября' => 'ноя.',
                'декабря' => 'дек.'),
        'kz' => array(
                'қаңтар' => 'қаң.',
                'ақпан'  => 'ақп.',
                'наурыз' => 'нау.',
                'сәуір'  => 'сәу.',
                'мамыр'  => 'мам.',
                'маусым' => 'мау.',
                'шілде'  => 'шіл.',
                'тамыз'  => 'там.',
                'қыркүйек' => 'қыр.',
                'қазан'  => 'қаз.',
                'қараша' => 'қар.',
                'желтоқсан' => 'желт.',
                )
        );

    /**
     * Возвращает 1 из 3 переданных параметров на основе анализа параметра $num.
     * Если $num попадает в группу числительных, генерирующих окончания русского
     * языка типа "1 предмет" - возвращает $v1, если "2 предмета" - $v2, если
     * "5 предметов" - $v5.
     *
     * @param int $num  число
     * @param mixed $v1 результат 1
     * @param mixed $v2 результат 2
     * @param mixed $v5 результат 5
     *
     * @return mixed
     */
    public static function ruEnding($num, $v1, $v2, $v5 = null) {

        $mod = $num % 10;
        $cond = floor(($num % 100) / 10) != 1;

        if($mod == 1 && $cond) {
            return $v1;
        } elseif ($mod >= 2 && $mod <= 4 && $cond || $v5 === null) {
            return $v2;
        } else {
            return $v5;
        }
    }

    /**
     * Преобразует массив ключ-значение в строку запроса URI, т.е. вида
     * key1=valye1&key2=value2...
     * Все значения урл-кодируются.
     *
     * @param array  $array        массив значений
     * @param string $objectPrefix префикс, который следует добавить ко всем
     *                             ключам, т.е. если префикс obj, а в массиве
     *                             1 ключ foo со значением bar, то строка будет
     *                             выглядеть как &obj[foo]=bar
     *
     * @return string
     */
    public static function arrayToQueryString($array, $objectPrefix = null) {

        $s = '';
        foreach($array as $key => $value) {

            $key = urlencode($key);
            if($objectPrefix) {
                $key = "{$objectPrefix}[$key]";
            }

            $s .= "&$key=".urlencode($value);
        }

        return $s;
    }

    /**
     * Обрезает строку до $lengthWords слов. Если нужно, при этом оставляет не больше, чем
     * $lengthChars символов. Если $clearHtml = true, очищает от ХТМЛ-разметки через strip_tags.
     * Если строка реально обрезалась, то в конец подставляет строку $ending (ее кол-во
     * символов входит в $lengthChars).
     *
     * Разделителем слов является \s (PREG).
     *
     * @param string $str
     * @param int    $lengthWords
     * @param int    $lengthChars
     * @param bool   $clearHtml
     * @param string $ending
     *
     * @return string
     */
    public static function cutWords($str, $lengthWords, $lengthChars = null, $clearHtml = true, $ending = '...') {

        if($clearHtml) {
            $str = strip_tags($str);
        }

        $el = mb_strlen($ending);

        $tokens = preg_split('/\s+/u', $str, $lengthWords + 1);
        $cnt = count($tokens);
        $add = false;
        if($cnt > $lengthWords) {
            $tokens = array_slice($tokens, 0, $cnt = $lengthWords);
            $add = true;
        }

        $s = implode(' ', $tokens).($add ? $ending : '');
        if($lengthChars <= $el || mb_strlen($s) <= $lengthChars) {
            return $s;
        }

        while($cnt > 1) {
            $last = array_pop($tokens);
            -- $cnt;

            if('' == $last)
                {
                continue;
                }

            $ss = explode($last, $s);
            array_pop($ss);
            $s = implode($last, $ss);

            if(mb_strlen($s) + $el <= $lengthChars) {
                return $s.$ending;
            }
        }

        return mb_substr($s, 0, $lengthChars - $el).$ending;
    }

    /**
     * Возвращает массив месяцев в указанном падеже. Пока только родительный!
     * TODO: остальные падежи и число
     *
     * @param string $padezh i, r, d, v, t, p
     *
     * @return array
     */
    public static function getMonths($padezh = 'r') {
        return array(
            'января',
            'февраля',
            'марта',
            'апреля',
            'мая',
            'июня',
            'июля',
            'августа',
            'сентября',
            'октября',
            'ноября',
            'декабря',
        );
    }

    /**
     * Возвращает массив месяцев в указанном падеже. Пока только родительный!
     * TODO: остальные падежи и число
     *
     * @param string $padezh i, r, d, v, t, p
     *
     * @return array
     */
    public static function getKzMonths($padezh = 'r') {
        return array(
            'қаңтар',
            'ақпан',
            'наурыз',
            'сәуір',
            'мамыр',
            'маусым',
            'шілде',
            'тамыз',
            'қыркүйек',
            'қазан',
            'қараша',
            'желтоқсан',
        );
    }

    /**
     * Возвращает массив месяцев в указанном падеже. Пока только родительный!
     * TODO: остальные падежи и число
     *
     * @param string $padezh i, r, d, v, t, p
     *
     * @return array
     */
    public static function getEnMonths($padezh = 'r') {
        return array(
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December',
        );
    }

    /**
     * Формирует строку с датой и временем по-русски, например "21 августа 1985 в 17:12"
     *
     * @param int  $tstamp         Метка времени
     * @param bool $wMins          Нужно ли вставлять часы и минуты
     * @param bool $yearObligatory Нужно ли всегда вставлять год, если нет, то будет вставлен,
     *                             только если он не равен текущему
     * @param bool $hideYear       Прятать год
     * @param bool $isShortMonth   Показывать сокращенное название месяца (ex. Jan, Feb, Apr)
     *
     * @return string
     */
    public static function tstamp2rus($tstamp, $wMins = false, $yearObligatory = false, $hideYear = false, $isShortMonth = false) {

        $months = Util_Misc::getMonths('r');
        $date = date('Y-m-d H:i:s', $tstamp);

        $days = substr($date, 0, 10);
        $mins = substr($date, 11, 5);

        list($year, $month, $day) = explode('-', $days);
        $s = ((int)$day).' '.($isShortMonth ? Util_Misc::$shortMonths['ru'][$months[(int)$month - 1]] : $months[(int)$month - 1]).($hideYear || !$yearObligatory && $year == date('Y') ? '' : " $year");

        if($wMins) {
            list($hours, $mins) = explode(':', $mins);
            $s .= " в $hours:$mins";
        }

        return $s;
    }

    /**
     * Формирует строку с датой и временем по-казахски, например "21 кантар 1985 17:12"
     *
     * @param int  $tstamp         Метка времени
     * @param bool $wMins          Нужно ли вставлять часы и минуты
     * @param bool $yearObligatory Нужно ли всегда вставлять год, если нет, то будет вставлен,
     *                             только если он не равен текущему
     * @param bool $hideYear       Прятать год
     * @param bool $isShortMonth   Показывать сокращенное название месяца (ex. Jan, Feb, Apr)
     *
     * @return string
     */
    public static function tstamp2kz($tstamp, $wMins = false, $yearObligatory = false, $hideYear = false, $isShortMonth = false) {

        $months = Util_Misc::getKzMonths('r');
        $date = date('Y-m-d H:i:s', $tstamp);

        $days = substr($date, 0, 10);
        $mins = substr($date, 11, 5);

        list($year, $month, $day) = explode('-', $days);
        $s = ((int)$day).' '.($isShortMonth ? Util_Misc::$shortMonths['kz'][$months[(int)$month - 1]] : $months[(int)$month - 1]).($hideYear || !$yearObligatory && $year == date('Y') ? '' : " $year");

        if($wMins) {
            list($hours, $mins) = explode(':', $mins);
            $s .= " $hours:$mins";
        }

        return $s;
    }

    /**
     * Формирует строку с датой и временем по-английски, например "August 21, 1985 in 17:12"
     *
     * @param int  $tstamp         Метка времени
     * @param bool $wMins          Нужно ли вставлять часы и минуты
     * @param bool $yearObligatory Нужно ли всегда вставлять год, если нет, то будет вставлен,
     *                             только если он не равен текущему
     * @param bool $hideYear       Прятать год
     * @param bool $isShortMonth   Показывать сокращенное название месяца (ex. Jan, Feb, Apr)
     *
     * @return string
     */
    public static function tstamp2en($tstamp, $wMins = false, $yearObligatory = false, $hideYear = false, $isShortMonth = false) {

        $months = Util_Misc::getEnMonths('r');
        $date = $isShortMonth ? date('Y-M-d H:i:s', $tstamp) : date('Y-m-d H:i:s', $tstamp);

        $days = substr($date, 0, strpos($date, ' '));
        $mins = substr($date, 11, 5);

        list($year, $month, $day) = explode('-', $days);

        $s = ($isShortMonth ? $month : $months[(int)$month - 1]).' '.((int)$day).($hideYear || !$yearObligatory && $year == date('Y') ? '' : " $year");

        if($wMins) {
            list($hours, $mins) = explode(':', $mins);
            $s .= ", $hours:$mins";
        }

        return $s;
    }

    /**
     * Формирует строку с датой и временем по языку
     *
     * @param string  $lang        На каком языку показать (kz/ru/en)
     * @param int  $tstamp         Метка времени
     * @param bool $wMins          Нужно ли вставлять часы и минуты
     * @param bool $yearObligatory Нужно ли всегда вставлять год, если нет, то будет вставлен,
     *                             только если он не равен текущему
     * @param bool $hideYear       Прятать год
     * @param bool $isShortMonth   Показывать сокращенное название месяца (ex. Jan, Feb, Apr)
     *
     * @return string
     */
    public static function tstampByLang($lang, $tstamp, $wMins = false, $yearObligatory = false, $hideYear = false, $isShortMonth = false)
    {

        if ($lang == 'kz')
            return self::tstamp2kz($tstamp, $wMins, $yearObligatory, $hideYear, $isShortMonth);

        if ($lang == 'en')
            return self::tstamp2en($tstamp, $wMins, $yearObligatory, $hideYear, $isShortMonth);

        return self::tstamp2rus($tstamp, $wMins, $yearObligatory, $hideYear, $isShortMonth);
    }

    /**
     * Получает дату выхода следующего номера газеты/журнала.
     *
     * Разные услуги имеют разное время завершения приема в текущий номер.
     * Поэтому необходимо указывать $type. Если тип не указан, то время завершения
     * приема в текущий номер считается вторник 18:00.
     *
     * @param string $type - тип издания/услуги.
     * @return string
     */
    public static function paperPublicationDate($type = null, $now = null) {
        switch($type) {
            case 'np.text.payfree':
                $closed = array('week_day'=>2, 'hour'=>13, 'minute'=>0);
                break;
            case 'np.text':
                $closed = array('week_day'=>2, 'hour'=>12, 'minute'=>0);
                break;
            case 'np.photo':
                $closed = array('week_day'=>2, 'hour'=>18, 'minute'=>0);
            case 'mag.photo':
                $closed = array('week_day'=>2, 'hour'=>17, 'minute'=>0);
                break;
            default:
                // после вторника 18-00 считать следующий номер выхода
                $closed = array('week_day'=>2, 'hour'=>18, 'minute'=>0);
        }

        if($now>0) {
            if(date('w', $now)<4 && (date('w', $now)>$closed['week_day'] || (date('w', $now)==$closed['week_day'] && date('Hi', $now)*1+1 > ($closed['hour'].substr('00'.$closed['minute'],-2))*1 ))) {
                $pubDate = strtotime('next Thursday', $now) + 86400*7;
            } else {
                $pubDate = strtotime('next Thursday', $now);
            }
        } else {
            if(date('w')<4 && (date('w')>$closed['week_day'] || (date('w')==$closed['week_day'] && date('Hi')*1+1 > ($closed['hour'].substr('00'.$closed['minute'],-2))*1 ))) {
                $pubDate = strtotime('next Thursday') + 86400*7;
            } else {
                $pubDate = strtotime('next Thursday');
            }
        }

        return self::tstamp2rus($pubDate);
    }

    /**
     * Формирует строку вида "через 5 минут", или "2 часа назад" из метки времени.
     *
     * Возможные форматы:
     *
     *   default    - стандартный вариант, если период меньше суток, пишет
     *                "через ...", если больше — пишет "29 августа"
     *   onlydate   - только "29 августа"
     *   longperiod - пишет через 3 часа", "6 месяцев назад", т.е. всегда период
     *
     * @param string $tstamp - метка времени, которую нужно показать
     * @param int    $now    - метка времени, которую использовать как текущую
     * @param string $format - формат отображения
     *
     * @return string
     */
    public static function whenTstamp($tstamp, $now = null, $format = 'default') {

        $now = $now ? $now : time();

        if($format == 'onlydate') {
            $months = self::getMonths();
            $data = getdate($tstamp);
            return $data['mday'] . ' ' . $months[$data['mon'] - 1];
        }

        $diff = $now - $tstamp;

        if($diff >= 0) {

            // прошлое и настоящее
            if($diff < 10)
                return 'только что';

            $diffMin = floor($diff / 60);
            if($diffMin == 0)
                return 'меньше минуты назад';

            if($diffMin < 60) {
                $ending = Util_Misc::ruEnding($diffMin, 'у', 'ы', '');
                return "$diffMin минут$ending назад";
            }

            // "длинный" формат, нужно показать период целиком, а не дату
            if($format == 'longperiod') {

                $diffHours = floor($diffMin / 60);
                if($diffHours < 24) {

                    if(1 == $diffHours) {
                        return 'час назад';
                    }

                    $ending = Util_Misc::ruEnding($diffHours, '', 'а', 'ов');
                    return "$diffHours час$ending назад";
                }

                $diffDays = floor($diffHours / 24);
                if($diffDays < 31) {

                    if(1 == $diffDays) {
                        return 'день назад';
                    }

                    $ending = Util_Misc::ruEnding($diffDays, 'день', 'дня', 'дней');
                    return "$diffDays $ending назад";
                }

                $diffMonths = floor($diffDays / 30);
                if($diffMonths < 12) {

                    if(1 == $diffMonths) {
                        return 'месяц назад';
                    }

                    $ending = Util_Misc::ruEnding($diffMonths, '', 'а', 'ев');
                    return "$diffMonths месяц$ending назад";
                }

                $diffYears = floor($diffMonths / 12);

                if(1 == $diffYears) {
                    return 'год назад';
                }

                $ending = Util_Misc::ruEnding($diffYears, 'год', 'года', 'лет');
                return "$diffYears $ending назад";
            }

        } else {

            // будущее
            $diff = -1 * $diff;

            $diffMin = ceil($diff / 60);
            if($diffMin == 0)
                return 'в течение минуты';

            if($diffMin < 60) {
                $ending = Util_Misc::ruEnding($diffMin, 'у', 'ы', '');
                return "через $diffMin минут$ending";
            }

            // "длинный" формат, нужно показать период целиком, а не дату
            if($format == 'longperiod') {

                $diffHours = ceil($diffMin / 60);
                if($diffHours < 24) {

                    if(1 == $diffHours) {
                        return 'через час';
                    }

                    $ending = Util_Misc::ruEnding($diffHours, '', 'а', 'ов');
                    return "через $diffHours час$ending";
                }

                $diffDays = ceil($diffHours / 24);
                if($diffDays < 31) {

                    if(1 == $diffDays) {
                        return 'через день';
                    }

                    $ending = Util_Misc::ruEnding($diffDays, 'день', 'дня', 'дней');
                    return "через $diffDays $ending";
                }

                $diffMonths = ceil($diffDays / 30);
                if($diffMonths < 12) {

                    if(1 == $diffMonths) {
                        return 'через месяц$ending';
                    }

                    $ending = Util_Misc::ruEnding($diffMonths, '', 'а', 'ев');
                    return "через $diffMonths месяц$ending";
                }

                $diffYears = ceil($diffMonths / 12);

                if(1 == $diffYears) {
                    return 'через год';
                }

                $ending = Util_Misc::ruEnding($diffYears, 'год', 'года', 'лет');
                return "через $diffYears $ending";
            }

        }

        // покажем дату "(29 августа (2011)?)? в 13:08"
        $data = getdate($tstamp);
        $result = 'в ' . date('G:i', $tstamp);

        if(date('Y-m-d', $now) != date('Y-m-d', $tstamp)) {
            $months = self::getMonths('r');

            $month = isset($months[$data['mon']]) ? $months[$data['mon']] : '';

            $result = $data['mday'] . ' ' . $month . ' ' . ($data['year'] == (int) date('Y', $now) ? '' : $data['year'] . ' года ') . $result;
        }

        return $result;
    }

    /**
     * Формирует строку вида "через 5 минут", или "2 часа назад" из строки с датой.
     *
     * @param string $date
     * @param int    $now    - метка времени, которую использовать как текущую
     * @param string $format
     *
     * @return string
     */
    public static function whenDate($date, $now = null, $format = 'default') {
        $now = $now ? $now : time();
        return self::whenTstamp(strtotime($date, $now), $now, $format);
    }


    /**
     * Упрощенная версия arrayKeysFromField
     *
     * @param ArrayAccess | array $array - массив с которым работаем
     * @param type $field - поле по которому группируем (должно быть уникальным)
     * @return array - вернем ассоц массив
     */
    public static function toAssocArray($array, $field = 'id')
        {
        // проверим вид
        if( !is_array($array) && !($array instanceof ArrayAccess) )
            {
            throw new Exception ('Error instance $array');
            }

        $result = array();

        foreach ($array as $value)
            {
            $result[$value[$field]] = $value;
            }

        return $result;
        }

    /**
     * @var string
     */
    protected static $_phonePrefix = '8';


    /**
     * Храним (показываем) телефоны в одном виде
     * Переводим массив в строку
     *
     * @param array $phones
     * @return string
     */
    public static function phonesFormatArrayToString($phones)
        {
        $return = '';

        if($phones)
            {
            $result = array();

            $fClean = function($string)
                {
                return trim(preg_replace('`[^0-9]+`uis', '', $string));
                };

            foreach($phones as $phone)
                {
                if(!is_array($phone))
                    {
                    continue;
                    }

                $tmp = array();

                if(isset($phone['code']) && is_scalar($phone['code']) && ($code = $fClean($phone['code'])))
                    {
                    $tmp[] = isset($phone['prefix']) && is_scalar($phone['prefix']) && ($prefix = $fClean($phone['prefix'])) ? $prefix : self::$_phonePrefix;
                    $tmp[] = $code;
                    }

                if(isset($phone['number']) && is_scalar($phone['number']) && ($number = $fClean($phone['number'])))
                    {
                    $tmp[] = $number;
                    }
                else
                    {
                    continue;
                    }

                $result[] = implode(' ', $tmp);
                }

            $result = array_filter(array_map('trim', $result), 'trim');

            if($result)
                {
                $return = implode(', ', $result);
                }
            }

        return $return;
        }

    /**
     * Храним (показываем) телефоны в одном виде
     * Переводим строку в массив
     *
     * @param string $phones
     * @return array
     */
    public static function phonesFormatStringToArray($phones)
        {
        $tpl = array
            (
            'prefix' => self::$_phonePrefix,
            'code' => '',
            'number' => '',
            );

        $result = array_filter(array_map('trim', explode(',', $phones)), 'trim');

        $result2 = array();
        foreach($result as $r)
            {
            $tmp = explode(' ', $r);

            while(count($tmp) < count($tpl))
                {
                array_unshift($tmp, '');
                }

            $_tmp = $tpl;

            if( ($prefix = array_shift($tmp)) )
                {
                $_tmp['prefix'] = $prefix;
                }

            if( ($code = array_shift($tmp)) )
                {
                $_tmp['code'] = $code;
                }

            $_tmp['number'] = implode('', $tmp);

            $result2[] = $_tmp;
            }

        return $result2;
        }

    /**
     * Храним (показываем) телефоны в одном виде
     * редактируем телефоны
     *
     * @param array|string $phones
     * @param array $options
     * @param array $tpls
     * @return string
     */
    public static function phonesFormatEdit($phones, array $options = array(), array $tpls = array())
        {
        if(is_scalar($phones))
            {
            $phones = Util_Misc::phonesFormatStringToArray($phones);
            }

        $options += array
            (
            'name' => 'model[phonesArray]',
            'count' => 4,
            );

        $tpls += array
            (
            'inputPrefix' => '
                %2$s
                ',
            'inputCode' => '
                <input
                    type="text"
                    name="%1$s"
                    value="%2$s"
                    %3$s
                    />
                ',
            'inputNumber' => '
                <input
                    type="text"
                    name="%1$s"
                    value="%2$s"
                    %3$s
                    />
                ',
            'tds' => '
                <td%2$s>%1$s</td>
                <td%4$s>%3$s</td>
                <td%6$s>%5$s</td>
                ',
            'table' => '
                <table%2$s>
                    %1$s
                </table>
                ',
            'trs' => '
                <tr%2$s>
                    %1$s
                </tr>
                ',
            );

        /*
         *
         *
         *
         *
         */

        $return = '';

        $count = max (count($phones), $options['count'] - 1) + 1;

        $result = array();

        for($a = 0, $b = $count; $a < $b; $a++)
            {
            $td1 = sprintf
                (
                $tpls['inputPrefix'],
                $options['name'] ."[$a][prefix]",
                (isset($phones[$a]['prefix']) ? $phones[$a]['prefix'] : Util_Misc::$_phonePrefix),
                (isset($options['inputPrefixTextAttrs']) ? $options['inputPrefixTextAttrs'] : '')
                );

            $td2 = sprintf
                (
                $tpls['inputCode'],
                $options['name'] ."[$a][code]",
                (isset($phones[$a]['code']) ? $phones[$a]['code'] : ''),
                (isset($options['inputCodeTextAttrs']) ? $options['inputCodeTextAttrs'] : '')
                );

            $td3 = sprintf
                (
                $tpls['inputNumber'],
                $options['name'] ."[$a][number]",
                (isset($phones[$a]['number']) ? $phones[$a]['number'] : ''),
                (isset($options['inputCodeTextAttrs']) ? $options['inputNumberTextAttrs'] : '')
                );

            $tds = sprintf($tpls['tds'],
                    $td1,
                    (isset($options['tdsTextAttrs'][0]) ? $options['tdsTextAttrs'][0] : ''),
                    $td2,
                    (isset($options['tdsTextAttrs'][1]) ? $options['tdsTextAttrs'][1] : ''),
                    $td3,
                    (isset($options['tdsTextAttrs'][2]) ? $options['tdsTextAttrs'][2] : '')
                    );

            $result[] = sprintf($tpls['trs'], $tds, (isset($options['trsTextAttrs']) ? $options['trsTextAttrs'] : ''));
            }

        if($result)
            {
            $return = sprintf($tpls['table'], implode('', $result), (isset($options['tableTextAttrs']) ? $options['tableTextAttrs'] : ''));
            }

        return $return;
        }


    /**
     *
     * @param type $phones
     * @param array $options
     */
    public static function phonesFormatShow($phones, array $options = array())
        {
        if(is_scalar($phones))
            {
            $phones = Util_Misc::phonesFormatStringToArray($phones);
            }

        $options += array
            (
            'separator' => ', ',
            'format' => '%1$s (%2$s) %3$s',
            );

        $return = '';

        //
        // 257-37-67
        // 57-37-67
        // 7-37-67
        $result = array();

        foreach($phones as $phone)
            {
            $phone += array(
                'prefix' => '',
                'code' => '',
                'number' => '',
                );

            if(mb_strlen($phone['number']) > 4)
                {
                $tmp = array();

                if(mb_strlen($phone['number']) == 5)
                    {
                    $tmp[] = mb_substr($phone['number'], 0, 1);
                    $tmp[] = mb_substr($phone['number'], 1, 2);
                    $tmp[] = mb_substr($phone['number'], 3);
                    }
                elseif(mb_strlen($phone['number']) == 6)
                    {
                    $tmp[] = mb_substr($phone['number'], 0, 2);
                    $tmp[] = mb_substr($phone['number'], 2, 2);
                    $tmp[] = mb_substr($phone['number'], 4);
                    }
                else
                    {
                    $tmp[] = mb_substr($phone['number'], 0, 3);
                    $tmp[] = mb_substr($phone['number'], 3, 2);
                    $tmp[] = mb_substr($phone['number'], 5);
                    }

                $phone['number'] = implode('-', $tmp);
                }

            if($phone['code'])
                {
                $result[] = sprintf($options['format'], $phone['prefix'], $phone['code'], $phone['number']);
                }
            else
                {
                $result[] = $phone['number'];
                }
            }

        if($result)
            {
            $return = implode($options['separator'], $result);
            }

        return $return;
        }

    /**
     * Устал везде писать:
     * date('Y-m-d H:i:s')
     * и
     * date('Y-m-d H:i:s', strtotime('-3 DAYS'))
     *
     * Вместо этого пишем:
     * Util_Misc::date() и Util_Misc::date('-3 DAYS') - соответ.
     */
    public static function datetime($time = null)
        {
        $time = $time === null ? time() : strtotime($time);

        return date('Y-m-d H:i:s', $time);
        }

    /**
     * Формирует строку с ХТМЛ-параметрами из заданных массивов.
     *
     * @param array $params - Индексный массив с разрешенными параметрами, например,
     *                        array('class', 'style', 'width')
     *                        В результирующую строку попадут только те параметры
     *                        из массива со значениями, ключи которых перечислены здесь.
     *
     * @param array $values - Ассоц. массив с реальными значениями, например,
     *                        array('class' => 'txt2', 'style' => 'width:100%')
     *
     * @return string
     */
    public static function htmlParams($params, $values)
        {
        $s = '';

        foreach ($params as $param)
            {
            if (isset($values[$param]))
                {
                $value = htmlspecialchars($values[$param]);
                $s .= "$param=\"$value\" ";
                }
            }

        return $s;
        }

    /**
     * @param int $size
     * @param array $names
     * @param string $block
     * @return string
     */
    public static function filesize($size, array $names = array('б', 'Кб', 'Мб', 'Гб', 'Тб'), $block = '%s&nbsp;%s')
        {
        for($a = 0; $size > 1024; $size /= 1024, $a++);

        return sprintf($block, round($size, 1), $names[$a]);
        }

    /**
     * Красиво показываем большие цифры
     *
     * @param int $num
     * @param int $decimal
     * @param string $separate
     * @return string
     */
    public static function numberFormat($num, $decimal = 0, $separate = '&nbsp;')
        {
        return str_replace(' ', $separate, number_format($num, $decimal, '.', ' '));
        }

    /**
     * Первая буква заглавная
     * @param $string
     * @return string
     */
    public static function ucfirst($string)
    {
        return mb_strtoupper(mb_substr($string, 0, 1)) . mb_substr($string, 1);
    }

    /**
     * Удаляет эмоджи из строки,
     * MySQL не умеет их хранить =(
     * @param $string
     * @return mixed
     */
    public static function removeEmoji($string)
    {
        return preg_replace('/([0-9#][\x{20E3}])|[\x{00ae}\x{00a9}\x{203C}\x{2047}\x{2048}\x{2049}\x{3030}\x{303D}\x{2139}\x{2122}\x{3297}\x{3299}][\x{FE00}-\x{FEFF}]?|[\x{2190}-\x{21FF}][\x{FE00}-\x{FEFF}]?|[\x{2300}-\x{23FF}][\x{FE00}-\x{FEFF}]?|[\x{2460}-\x{24FF}][\x{FE00}-\x{FEFF}]?|[\x{25A0}-\x{25FF}][\x{FE00}-\x{FEFF}]?|[\x{2600}-\x{27BF}][\x{FE00}-\x{FEFF}]?|[\x{2900}-\x{297F}][\x{FE00}-\x{FEFF}]?|[\x{2B00}-\x{2BF0}][\x{FE00}-\x{FEFF}]?|[\x{1F000}-\x{1F6FF}][\x{FE00}-\x{FEFF}]?/uis', '', $string);
    }

    /**
     * Обрезаем до последней точки
     *
     * @param $text
     * @param $len
     * @param int $lastStringLen
     * @return string
     */
    public static function cutTextToLastDot($text, $len, $lastStringLen = 80)
    {
        $text = strip_tags($text);
        $text = trim($text);

        if(mb_strlen($text) <= $len) {
            return $text;
        }

        $text = mb_substr($text, 0, $len);

        $fSubstrToLastDot = function ($string) use ($lastStringLen) {
            $tmpString = $string;
            $tmpString = preg_replace('` ([а-яa-z]{1})\.`uis', ' ___\\1___', $tmpString);

            if (preg_match('`^(.* [а-я]{2,})([\.\?]+)([^\.\?]+)$`uis', $tmpString, $matches)) {
                if (mb_strlen($matches[3]) < $lastStringLen) {
                    $matches[1] = preg_replace('` ___([а-яa-z]{1})___`uis', ' \\1.', $matches[1]);
                    $matches[3] = preg_replace('` ___([а-яa-z]{1})___`uis', ' \\1.', $matches[3]);
                    return $matches[1] . $matches[2];
                }
            }

            return $string . '...';
        };

        if (preg_match('`([а-яa-z]{1})$`uis', $text, $matches)) {
            if (preg_match('`^(.*?)[^ ]+$`uis', $text, $matches2)) {
                $text = $fSubstrToLastDot(trim($matches2[1], ', '));
            } else {
                $text = $fSubstrToLastDot($text);
            }
        } else if (preg_match('`^(.*?)([^а-яa-z0-9\?\.]+)$`uis', $text, $matches)) {
            $text = $fSubstrToLastDot($matches[1]);
        }

        return $text;
    }

    }
