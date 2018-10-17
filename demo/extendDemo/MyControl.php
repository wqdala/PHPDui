<?php

require_once "../../lib/Control.php";

use PHPDui\Control;

/**
 * MyControl 用来演示PHPDui的扩展
 */
class MyControl extends Control
{
    
    function __construct()
    {
        parent::__construct();
    }

    public function SetAttribute(&$data)
    {
        parent::SetAttribute($data);
        
        //TO DO  添加自定义属性
    }

    public function NeedSize($availableRect)
    {
        //除特殊需求外，一般不需要修改这个方法
        return parent::NeedSize($availableRect);
    }

    public function DoPaint(&$desImg,$paintRc)
    {
        parent::DoPaint($desImg,$paintRc);
        //处理你自己的绘制信息  如果不需要父类的绘制信息可以直接移除上面一行代码
        //测试绘制部分
        imagefilledrectangle ($desImg, 0, 0, 100, 100, 0xFFAAFF);
        
    }


}