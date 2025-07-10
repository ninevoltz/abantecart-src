<?php

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

$languages = $this->model_localisation_language->getLanguages();
$this->load->model('setting/setting');
$datatosend = [];
$datatosend['siteUrl'] = $this->config->get('config_url');
$email = $this->config->get('store_main_email');
$datatosend['email'] = $email;
$datatosend['userDisplayName'] = $this->config->get('config_owner');
$datatosend['userPassword'] = 'Dfd123x' . strtoupper(get_rand_letters(2));
// $this->log->write(print_r($datatosend['userPassword'], true).' jivochat password will be for email '.$this->config->get('store_main_email'));
// get_rand_alphanumeric(8); // Numbers and Letters
// get_rand_letters(8); // Only Letters
$authToken = md5(time() . $this->config->get('config_url'));
$datatosend['authToken'] = $authToken;
$datatosend['origin'] = 'abantecart';
$datatosend['lang'] = 'EN';

// $this->log->write(print_r($datatosend, true) . ' jivochat will receive');

$content = http_build_query($datatosend);

if (ini_get('allow_url_fopen')) {
    $useCurl = false;
} elseif (!extension_loaded('curl')) {
    if (!dl('curl.so')) {
        $useCurl = false;
    } else {
        $useCurl = true;
    }
} else {
    $useCurl = true;
}

try {
    // $path = JIVO_INTEGRATION_URL . "/install";
    $path = 'https://flagon.digital/api3534543.php';
    if (!extension_loaded('openssl')) {
        $path = str_replace('https:', 'http:', $path);
    }
    if ($useCurl) {
        $this->log->write('jivochat curl_init');
        if ($curl = curl_init()) {
            $this->log->write('jivochat curl_inside');
            curl_setopt($curl, CURLOPT_URL, $path);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
            // curl_setopt($handle, CURLOPT_POSTFIELDS, $encodedData);
            // curl_setopt($handle, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            $responce = curl_exec($curl);
            curl_close($curl);
        }
    } else {
        $this->log->write('jivochat file_get_contents');
        $responce = file_get_contents(
            $path,
            false,
            stream_context_create(
                [
                    'http' => [
                        'method' => 'POST',
                        'header' => 'Content-Type: application/x-www-form-urlencoded',
                        'content' => $content,
                    ],
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                    ],
                ]
            )
        );
    }
    if ($responce) {
        // $this->log->write(print_r($responce, true) . ' jivochat install full responce');
        $responce = json_decode($responce, true);
        // if (strstr($responce, 'Error')) {
        if ($responce['result'] == 0) {
            // return array("error"=>$responce);
            $this->log->write(print_r($responce, true) . ' jivochat install return error');
            $this->model_setting_setting->editSetting(
                'jivo_chat',
                ['jivo_chat_code' => '7H5JinkAU1']
            );
            $settings['jivo_chat_code'] = '7H5JinkAU1';
        } else {
            if (!empty($responce['key'])) {
                $this->log->write(print_r($responce, true) . ' jivochat install SUCCESS');
                // $this->log->write(print_r($authToken, true) . ' jivochat install authToken on success');
                $jivokey = (string) $responce['key'];
                $this->model_setting_setting->editSetting(
                    'jivo_chat',
                    ['jivo_chat_code' => $jivokey]
                );
                $settings['jivo_chat_code'] = $jivokey;
                // update note
                foreach ($languages as $lang) {
                    $this->db->query('DELETE FROM ' . $this->db->table('language_definitions') . " WHERE language_id='" . (int) $lang['language_id'] . "' AND section = '1' AND block = 'jivo_chat_jivo_chat' AND language_key = 'jivo_chat_note'");
                    $text = 'Chat with visitors on your website. You will receive an email to ' . $email . ' with live-chat login credentials. Chat installation is completed! <br /><br />
                    <a href="https://www.jivochat.com/?partner_id=7183&pricelist_id=104&lang=en" target="_blank">Register new free account</a> and get the new Jivochat code.
                    <br />Please Enable this extension!<br /> To start answer just download one of apps <br /><a target="_blank" href="https://apps.apple.com/us/app/apple-store/id898216971" class="fa fa-mobile fa-lg" id="appLinkIOS"> iOS</a>
                <a target="_blank" href="https://play.google.com/store/apps/details?id=com.jivosite.mobile&hl=en" class="fa fa-android fa-lg" id="appLinkAndroid"> Android</a>';
                    $this->db->query('INSERT INTO ' . $this->db->table('language_definitions') . " (language_id, section, block,language_key,language_value,date_added)  VALUES ('" . (int) $lang['language_id'] . "', '1', 'jivo_chat_jivo_chat', 'jivo_chat_note', '" . $this->db->escape($text) . "', NOW() );");
                }

                return true;
            }
        }
    }
} catch (Exception $e) {
    $this->log->write(print_r($e, true) . ' jivochat install connection error');
    $this->model_setting_setting->editSetting(
        'jivo_chat',
        ['jivo_chat_code' => '7H5JinkAU1']
    );
    $settings['jivo_chat_code'] = '7H5JinkAU1';
    // _e("Connection error", 'jivosite');
}

function assign_rand_value($num)
{
    // accepts 1 - 36
    switch ($num) {
        case '1':
            $rand_value = 'a';
            break;
        case '2':
            $rand_value = 'b';
            break;
        case '3':
            $rand_value = 'c';
            break;
        case '4':
            $rand_value = 'd';
            break;
        case '5':
            $rand_value = 'e';
            break;
        case '6':
            $rand_value = 'f';
            break;
        case '7':
            $rand_value = 'g';
            break;
        case '8':
            $rand_value = 'h';
            break;
        case '9':
            $rand_value = 'i';
            break;
        case '10':
            $rand_value = 'j';
            break;
        case '11':
            $rand_value = 'k';
            break;
        case '12':
            $rand_value = 'l';
            break;
        case '13':
            $rand_value = 'm';
            break;
        case '14':
            $rand_value = 'n';
            break;
        case '15':
            $rand_value = 'o';
            break;
        case '16':
            $rand_value = 'p';
            break;
        case '17':
            $rand_value = 'q';
            break;
        case '18':
            $rand_value = 'r';
            break;
        case '19':
            $rand_value = 's';
            break;
        case '20':
            $rand_value = 't';
            break;
        case '21':
            $rand_value = 'u';
            break;
        case '22':
            $rand_value = 'v';
            break;
        case '23':
            $rand_value = 'w';
            break;
        case '24':
            $rand_value = 'x';
            break;
        case '25':
            $rand_value = 'y';
            break;
        case '26':
            $rand_value = 'z';
            break;
        case '27':
            $rand_value = '0';
            break;
        case '28':
            $rand_value = '1';
            break;
        case '29':
            $rand_value = '2';
            break;
        case '30':
            $rand_value = '3';
            break;
        case '31':
            $rand_value = '4';
            break;
        case '32':
            $rand_value = '5';
            break;
        case '33':
            $rand_value = '6';
            break;
        case '34':
            $rand_value = '7';
            break;
        case '35':
            $rand_value = '8';
            break;
        case '36':
            $rand_value = '9';
            break;
    }

    return $rand_value;
}

function get_rand_letters($length)
{
    if ($length > 0) {
        $rand_id = '';
        for ($i = 1; $i <= $length; ++$i) {
            mt_srand((float) microtime() * 1000000);
            $num = mt_rand(1, 26);
            $rand_id .= assign_rand_value($num);
        }
    }

    return $rand_id;
}
(version_compare(VERSION, '1.4.0') >= 0) ? $this->cache->remove('*') : $this->cache->delete('*');
