<?php

require_once "./MyControl.php";
require_once "../../lib/Canvas.php";

use PHPDui\Canvas;

class MyPage extends Canvas
{
    function __construct()
    {
        parent::__construct();
    }

    protected function createControl($name)
    {
        switch ($name) {
            case 'MyControl':
                return new MyControl;
                break;
            default:
                # code...
                break;
        }
        return parent::createControl($name);
    }
}





