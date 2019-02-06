<?php

require_once './vendor/autoload.php';

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverBy;

const CSV_PATH = "test.csv";
function loadCSV ($path) 
{
    $file = new SplFileObject($path);
    while (!$file->eof()) {
        $records [] = $file->fgetcsv();
    }
    return $records;
} 

/**
 * Mantisの期日を自動更新する
 */
function sample()
{
    //アクセスするURLを取得
    $url_obj = loadCSV(CSV_PATH);
    // selenium
    $host = 'http://localhost:4444/wd/hub';
    // chrome ドライバーの起動
    $driver = RemoteWebDriver::create($host,DesiredCapabilities::chrome());
    // 画面サイズをMAXに
    $driver->manage()->window()->maximize();


    foreach ($url_obj as $url) {
        // 指定URLへ遷移 
        $driver->get("{$url[0]}?id={$url[1]}");
        // titleのエレメント取得
        $element = $driver->findElement(WebDriverBy::name('q'));
        // titleの現在値取得
        $title = $element->getText();
        // 10`文字取得｀
        $str = substr($title,0,10);
        if (preg_match("#[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}#", $str, $match)) {
            // $match[1]になんか出てくる。
            $title = str_replace($match[1],"", $title);
        }
        trim($title);
        // 日付入力
        $element->sendKeys("{$url[2]} ${title}");
        // 更新実行
        $element->submit();

    }
    // ブラウザを閉じる
    $driver->close();
}

// 実行
sample();

