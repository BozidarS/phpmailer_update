<?php
/*
Plugin Name: PHPMailer Apdejter
Description: It replaces outdated phpmailer and smtp classes, defined in Wordpress, with those from github on https://github.com/PHPMailer/PHPMailer
Version:     0.1
Author:      BozidarS
License:     Unlicense
License URI: http://unlicense.org
Text Domain: wporg
Domain Path: /languages

This is free and unencumbered software released into the public domain.

Anyone is free to copy, modify, publish, use, compile, sell, or
distribute this software, either in source code form or as a compiled
binary, for any purpose, commercial or non-commercial, and by any
means.

In jurisdictions that recognize copyright laws, the author or authors
of this software dedicate any and all copyright interest in the
software to the public domain. We make this dedication for the benefit
of the public at large and to the detriment of our heirs and
successors. We intend this dedication to be an overt act of
relinquishment in perpetuity of all present and future rights to this
software under copyright law.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
IN NO EVENT SHALL THE AUTHORS BE LIABLE FOR ANY CLAIM, DAMAGES OR
OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE,
ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
OTHER DEALINGS IN THE SOFTWARE.

For more information, please refer to <http://unlicense.org>

Basically, do whatever you like with this, just don't ask or blame me for anything.
*/

/*
 * On direct call
 */
if ( !function_exists( 'add_action' ) ) {
    echo "I guess you've made a mistake. Let's leave it there";
    exit;
}

$webMailer = file_get_contents("https://raw.githubusercontent.com/PHPMailer/PHPMailer/master/class.phpmailer.php");
$munjinSukses = '<p>There\'s no need for update.</p>';

if ( isset($_POST) && $_POST['action'] == 'prepisi') {
    prepisivanje();
    $munjinSukses = '<h3>You have successfuly updated PHPMailer components.</h3>';
}


add_action( 'admin_menu', 'apdejterMeni' );
//add_action( 'admin_post_prepisi', 'prepisivanje' );

function apdejterMeni() {
    add_management_page( 'Update PHPMailer', 'Update PHPMailer', 'manage_options', 'phpmapdejt', 'sadrzajTeksta' );
}

function sadrzajTeksta() {
    if ( !current_user_can( 'manage_options' ) )
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );

    global $webMailer;
    global $munjinSukses;

    $wmRes = substr($webMailer,strpos($webMailer,'public $Version ='),30);
    preg_match("#(?<=')(.*?)(?=')#",$wmRes,$nasaoMailer);

    require_once ABSPATH."/wp-includes/class-phpmailer.php";
    $currVer = new PHPMailer;
    $whatDoWeHaveHere = $currVer->Version;

    echo '<div class="wrap">';
    if ( isset($currVer) && isset($whatDoWeHaveHere) ) {
        echo '<p>Current PHPMailer version is ' . $nasaoMailer[0] . ' and your version is ' . $whatDoWeHaveHere . '</p>';
        if ($nasaoMailer[0] == $whatDoWeHaveHere)
            echo $munjinSukses;
        else {
            echo '<p>If you want to update it, click on "update" button.</p>';
            echo '<form action="" method="post">';
            echo '<input type="hidden" name="action" value="prepisi">';
            submit_button("Update");
            echo '</form>';
        }
    }
    else
        echo '<p>Something glitched. Try refreshing it. If that doesn\'t help, tough luck...</p>';
    echo '</div>';
}

function prepisivanje() {
    global $webMailer;
    $webSMTP = file_get_contents("https://raw.githubusercontent.com/PHPMailer/PHPMailer/master/class.smtp.php");

    $fp = fopen(ABSPATH . "/wp-includes/class-phpmailer.php", "w");
    fwrite($fp, $webMailer);
    fclose($fp);

    $fs = fopen(ABSPATH . "/wp-includes/class-smtp.php", "w");
    fwrite($fs, $webSMTP);
    fclose($fs);
}