<?php

header('Content-Type: text/html; charset=utf-8');

session_start();

require './api/EurasiaAPI.php';
require './api/Misc.php';


$langs = ['ru' => ['Русский', 'ru_RU', 'RUSSIAN'], 'kz' => ['Қазақша', 'kk_KZ', 'KAZAKH']/*, 'en' => ['English', 'en_US']*/];


$lang = (isset($_GET['lang']) && array_key_exists($_GET['lang'], $langs)) ? $_GET['lang'] : 'ru';
$apiLang = $lang == 'kz' ? 'kk' : $lang;

$_SESSION['apiLang'] = $apiLang;

putenv("LC_ALL=".$langs[$lang][1]);
setlocale(LC_ALL, $langs[$lang][1]);

bindtextdomain("$lang", "./i18n");
textdomain("$lang");
bind_textdomain_codeset($lang, 'utf-8');


$timestamp = time();

$tsstring = gmdate('D, d M Y H:i:s ', $timestamp) . 'GMT';
$etag = $lang . $timestamp;

$if_modified_since = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : false;
$if_none_match = isset($_SERVER['HTTP_IF_NONE_MATCH']) ? $_SERVER['HTTP_IF_NONE_MATCH'] : false;
if ((($if_none_match && $if_none_match == $etag) || (!$if_none_match)) &&
    ($if_modified_since && $if_modified_since == $tsstring)) {

    header('HTTP/1.1 304 Not Modified');
    exit();
} else {
    header("Last-Modified: $tsstring");
    header("ETag: \"{$etag}\"");
}

// Не нашли счёт
$orderNotFound = true;

$orderId = isset($_GET['order']) ? $_GET['order'] : null;

if(null != $orderId) {

    $data = [
            'id' => $orderId,
            'returnUri' => (isset($_SERVER['HTTPS']) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']
        ];

    $data = json_encode($data);

    $url = 'epayment/ebill/fetch/';

    $order = EurasiaAPI::request($url, $data, 'post', $apiLang);

    $order = json_decode($order, TRUE);

    if(isset($order['error'])) {

        $msg = json_decode($order['message'], true);

//    echo '<pre>';
//    print_r($order);
//    die('</pre>');

        header("HTTP/1.0 ".$order['code']);
//                ." ".$msg[0]['message']);

        if($order['code'] == 404 || $order['code'] == 503) {
            $serverError = true;
        }
    } else {
        // Нашли счёт
        $orderNotFound = false;

        switch($order['status']) {
            case 'READY':
                $status = _('ожидает оплаты');
                break;

            case 'PAID':
                $status = _('оплачен');
                break;

            case 'CANCELED':
                $status = _('отменен');
                break;

            case 'FAILED':
                $status = _('оплата не прошла');
                break;

            default:
                $status = '';
                break;
        }
    }

//    echo '<pre>';
//    print_r($order);
//    die('</pre>');
} else {
    if (defined('HOME_HOST')) {
        header("Location: ".HOME_HOST, true, 303);
    } else {
        http_response_code(404);
    }
    exit;
}

?>
<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <meta name="description" content="" />
        <meta name="keywords" content="" />
        <link rel="icon" type="image/png" href="/favicon.png" />
        <link rel="apple-touch-icon" href="/apple-touch-favicon.png" />

        <title><?= _("Просмотр счёта") ?></title>

        <!-- Bootstrap core CSS -->
        <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet"/>
        <link href="/css/styles.css?<?= filemtime(__DIR__ .'/css/styles.css') ?>" rel="stylesheet"/>

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

        <?php include './__GA.php'; ?>
        <?php include './__YM.php'; ?>
    </head>

    <body class="payment-page">
        <header>
            <nav class="navbar navbar-static-top">
                <div class="container">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a href="<?=HOME_HOST?>" class="navbar-brand"><img src="/i/logo-<?= $lang ?>.svg" alt="Евразия" class="navbar-brand__logo" /></a>

                        <?php
                        /**
                        <div class="dropdown langs">
                            <button class="btn dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                <?= $langs[$lang][0] ?>
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                <?php foreach($langs as $key => $l): ?>
                                <?php if($key != $lang): ?>
                                <li><a href="/<?= $key != 'ru' ? $key : '' ?>"><?= $l[0] ?></a></li>
                                <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                         *
                         */
                        ?>
                    </div>
                    <div id="navbar" class="collapse navbar-collapse">
                        <ul class="nav navbar-nav navbar-right">
                            <li><span><a href="tel:88000800099" class="header-tel">8 800 080-00-99</a> <br class="visible-sm-inline"/><?= _("или") ?> <a href="tel:5678" class="header-tel">5678</a><br/><small><?= _("звонок бесплатный") ?></small></span></li>

                            <li><a href="https://eurasia36.kz/contacts.php<?= $lang != 'ru' ? "?lang=$lang" : '' ?>"><?= _("Адреса и телефоны") ?></a></li>
                            <li class="lang-li first-lang-li"><?php if($lang == 'ru'): ?><span class="current-lang">RU</span><?php else: ?><a href="/invoice/<?= $orderId ?>">RU</a><?php endif; ?></li>
                            <li class="lang-li"><?php if($lang == 'kz'): ?><span class="current-lang">KZ</span><?php else: ?><a href="/kz/invoice/<?= $orderId ?>">KZ</a><?php endif; ?></li>
                        </ul>
                    </div><!--/.nav-collapse -->
                </div>
            </nav>
        </header>
        <section>
            <div class="container">
                <div class="order-body">
                    <?php if(isset($serverError)): ?>
                    <h1><?= _("Кажется, что-то пошло не так") ?></h1>

                    <p><?= _("Пожалуйста, загляните чуть позже, мы всё исправим.") ?></p>

                    <p><?= _("Или позвоните: <a href=\"tel:88000800099\">8 800 080-00-99</a> или <a href=\"tel:5678\">5678</a> с мобильного (звонок бесплатный).") ?></p>
                    <?php elseif($orderNotFound === false): ?>
                    <h1><?= _("Заказ №").$order['payment']['externalId'] ?> <span class="order-status <?= strtolower($order['status']) ?>"><?= $status ?></span></h1>
                    <?php if($order['status'] == 'PAID'): ?>
                    <span class="order-date"><?= _("Дата оплаты")?> <?= $lang == 'kz' ? Util_Misc::tstamp2kz(strtotime($order['paid']), true, true) : Util_Misc::tstamp2rus(strtotime($order['paid']), true, true) ?></span>
                    <?php else: ?>
                    <span class="order-date"><?= $lang == 'kz' ? Util_Misc::tstamp2kz(strtotime($order['created']), false, true) : Util_Misc::tstamp2rus(strtotime($order['created']), false, true) ?></span>
                    <?php endif; ?>

                    <?php foreach($order['payment']['items'] as $i => $item): ?>
                        <div class="row order-details">
                            <div class="col-xs-12 col-md-4">
                                <?php if($i == 0): ?>
                                <span class="order-details__name"><?= _("Получатель") ?></span>
                                <hr/>
                                <?= htmlspecialchars(mb_convert_case(mb_strtolower($order['payer']['name'], "UTF-8"), MB_CASE_TITLE, "UTF-8")) ?><br/>
                                <small class="gray"><?= htmlspecialchars($order['payer']['email']) ?></small>
                                <?php endif; ?>
                            </div>
                            <div class="col-xs-6 col-md-4">
                                <?php if($i == 0): ?>
                                <span class="order-details__name"><?= _("Что&nbsp;оплачиваем") ?></span>
                                <hr/>
                                <?php endif; ?>
                                <?= htmlspecialchars($item['title']); ?>
                            </div>
                            <div class="col-xs-6 col-md-4">
                                <?php if($i == 0): ?>
                                <span class="order-details__name"><?= _("К&nbsp;оплате") ?></span>
                                <hr/>
                                <?php endif; ?>
                                <span class="order-cost"><?= Util_Misc::numberFormat($item['amount']) ?> тг</span>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div class="row">
                        <div class="col-xs-12 order-fin">
                            <hr/>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12 col-md-4 order-form__col pull-right">
                            <?php
                            if($order['status'] == 'READY') {
                                foreach($order['availableMethods'] as $method) {
                                    if($method['type'] == 'QAZKOM') {
                                        echo '<form method="'.$method['httpForm']['method'].'" action="'.$method['httpForm']['uri'].'">';

                                        foreach($method['httpForm']['params'] as $param) {
                                            echo '<input type="hidden" name="'.$param['name'].'" value="'.$param['value'].'" />';
                                        }

                                        echo '<button class="btn btn-blue">'._("Оплатить картой").'&nbsp;&nbsp;<img src="/i/card.svg" alt="" /></button>';
                                        echo '</form>';
                                    }
                                }
                            }
                            ?>
                        </div>
                        <div class="col-xs-12 col-md-8 order-number">
                            <small class="gray"><?= _("Счёт") ?> <?= htmlspecialchars($order['id']); ?></small>
                            <?php if(!empty($order['result'])): ?>
                            <small class="gray"> &middot; Референс <?= htmlspecialchars($order['result']['paymentReference']); ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php else: ?>
                    <h1><?= _("Счёт не найден") ?></h1>
                    <?php endif; ?>

                </div>

            </div>
        </section>



        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="/bootstrap/js/bootstrap.min.js"></script>
    </body>
</html>