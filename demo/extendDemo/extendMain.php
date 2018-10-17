<?php
require_once "./MyPage.php";
function extendShow()
{
    $data = array();
        $data[] = array(
            'name' => 'Container',
            'attribute' => array(
                'bkColor' => 0xFFFFFF,
            ),
            'subItem' => array(
                array(
                    'name' => 'MyControl',
                    'attribute' => array(
                        'bkImage' => 'http://dala.work/public/temp.jpg',
                        'padding' => '10,10,10,10',
                        'alphaColor' => 0xfe1c5c,
                    ),
                ),
            ),
        );

    
    $width = '400';
    $height = '400';
    $myPage = new MyPage;
    $myPage->loadData($data);

    //生成文件
    //jpeg
    // $savePath = './temp.jpeg';
    // $myCanvas->generationImage($savePath,$width,$height,JPEG);
    
    //png
    // $savePath = './temp.png';
    // $myCanvas->generationImage($savePath,$width,$height,PNG);


    //直接显示
    $myPage->showImg($width,$height);

}
extendShow();