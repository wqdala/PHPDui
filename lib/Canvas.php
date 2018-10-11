<?php
namespace PHPDui;
require_once "Container.php";
require_once 'HorizontalLayout.php';
require_once 'VerticalLayout.php';
require_once 'CircleImage.php';
require_once 'Label.php';

define('PNG', 1);
define('JPEG', 2);
define('BMP', 3);


class Canvas
{
    private $m_pageRoot = null;
    function __construct()
    {
    }
    
    public function loadData(&$data)
    {
        $this->m_pageRoot = new Container;
        $this->loadSubData($data,$this->m_pageRoot);

    }

    public function generationImage($savePath,$width,$height,$type = PNG)
    {
        if($this->m_pageRoot === null){
            echo "not load data";
            return;
        }
        $desImage = imagecreatetruecolor($width, $height);
        $rect = array(
            'x' => 0,
            'y' => 0,
            'w' => $width,
            'h' => $height,

        );
        $this->m_pageRoot->SetPos($rect);
        $this->m_pageRoot->DoPaint($desImage,$rect);
        switch ($type) {
            case PNG:
                imagepng($desImage,$savePath);
                break;
            
            case JPEG:
                imagejpeg($desImage,$savePath);
                break;
            default:
                imagepng($desImage,$savePath);
                break;
        }
        

    }

    public function showImg($width,$height)
    {
        if($this->m_pageRoot === null){
            echo "not load data";
            return;
        }
        $desImage = imagecreatetruecolor($width, $height);
        $rect = array(
            'x' => 0,
            'y' => 0,
            'w' => $width,
            'h' => $height,

        );
        $this->m_pageRoot->SetPos($rect);
        $this->m_pageRoot->DoPaint($desImage,$rect);
        header("content-type:image/png");
        imagepng($desImage);
    }


///////////////////////////////private///////////////////////////////////

    private function loadSubData(&$data,&$root)
    {
        foreach ($data as $value) {
            $control = $this->createControl($value['name']);
            if($control){
                
                if(array_key_exists('attribute', $value)) {
                    $control->SetAttribute($value['attribute']);
                }

                if($control->IsContainer() && array_key_exists('subItem', $value)){
                    $this->loadSubData($value['subItem'],$control);
                }

                $root->AddItem($control);

            }
        }
    }

    private function createControl($name)
    {
        switch ($name) {
            case 'Container':
                return new Container;
                break;
            case 'Control':
                return new Control;
                break;
            case 'HorizontalLayout':
                return new HorizontalLayout;
                break;
            case 'VerticalLayout':
                return new VerticalLayout;
                break;
            case 'CircleImage':
                return new CircleImage;
                break;
            case 'Label':
                return new Label;
                break;
            default:
            echo "unexpect control name";
                # code...
                break;
        }
        return null;
    }
}





