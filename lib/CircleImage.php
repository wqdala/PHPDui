<?php
namespace PHPDui;
include_once 'Control.php';
    /**
     * CircleImage
     */
class CircleImage extends Control
{
    public function __construct()
    {
        parent::__construct();
        $this->m_alphaColor = 0xFFFFFF;
    }

    public function DoPaint(&$desImg,$paintRc)
    {
        $paintRect = $this->getPaintRect();

        //绘制背景色
        if($this->m_bkColor != -1)
        {
            if(imagefilledrectangle($desImg, $paintRect['x'], $paintRect['y'], $paintRect['x'] + $paintRect['w'] - 1, 
                $paintRect['y'] + $paintRect['h'] - 1, $this->m_bkColor)){
            }
        }

        // 背景图片
        if(!empty($this->m_bkImage))
        {
            $radius = $paintRect['w'] < $paintRect['h'] ? $paintRect['w'] : $paintRect['h'];
            // var_dump($paintRect);
            $baseCanvas = imagecreatetruecolor($paintRect['w'], $paintRect['h']);
            if(!$this->m_isBinary){
                $bkImage = $this->safeLoadImage($this->m_bkImage);
            }else{
                $bkImage = imagecreatefromstring($this->m_bkImage);
            }

            $bgcolor = imagecolorallocate($baseCanvas,  MyTools::GetColorR($this->m_alphaColor),
                MyTools::GetColorG($this->m_alphaColor),  MyTools::GetColorB($this->m_alphaColor));


            $fgcolor = imagecolorallocate($baseCanvas, 0x00, 0x00, 0x01);
            imagefill($baseCanvas, 0, 0, $bgcolor);
            imagefilledarc($baseCanvas, $radius / 2, $radius / 2, $radius, $radius, 0, 360, $fgcolor, IMG_ARC_PIE);
            imagecolortransparent($baseCanvas,$fgcolor);
            $backImage = imageCreatetruecolor($radius,$radius);
            imagecopyresampled($backImage, $bkImage, 0, 0, 0, 0, $radius, $radius, imagesx($bkImage), imagesy($bkImage));
            imagecopymerge($backImage, $baseCanvas, 0, 0, 0, 0, $radius, $radius, 100);

            $bgcolor = imagecolorallocate($backImage,  MyTools::GetColorR($this->m_alphaColor),
                MyTools::GetColorG($this->m_alphaColor),  MyTools::GetColorB($this->m_alphaColor));
            
            imagecolortransparent($backImage,$bgcolor);
            imagecopymerge($desImg, $backImage, $paintRect['x'], $paintRect['y'], 0, 0, $paintRect['w'],
             $paintRect['h'], 100);
        }
    }

}