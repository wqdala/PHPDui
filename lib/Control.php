<?php
include_once 'MyTools.php';
class Control
{
    //控件位置
    protected $m_rectItem; // x y w h

    //控件属性
    protected $m_width = 0;
    protected $m_height = 0;
    protected $m_rectPadding = array(
        'u' => 0,
        'd' => 0,
        'l' => 0,
        'r' => 0,

    ); // u d l r

    //背景颜色
    protected $m_bkColor = -1;

    //背景图片
    protected $m_bkImage = '';
    protected $m_isBinary = false;
    protected $m_bkImageSrcRect = array(
    );

    function __construct()
    {
    }

    public function SetWidth($width)
    {
        $this->m_width = $width;
    }
    
    public function SetHeight($height)
    {
        $this->m_height = $height;
    }

    public function GetWidth()
    {
        return $this->m_width;
    }

    public function GetHeight()
    {
        return $this->m_height;
    }

    public function SetPadding($padding)
    {
        $this->m_rectPadding = $padding;
    }

    public function GetPadding()
    {
        return $this->m_rectPadding;
    }

    public function SetBkColor($color)
    {
        $this->m_bkColor = $color;
    }

    public function GetBkColor()
    {

        return $this->m_bkColor;
    }

    public function SetBkImage($imageStr)
    {
        $this->m_bkImage = $imageStr;
    }

    public function SetBkimageSrc($imageSrc)
    {
        $this->m_bkImageSrcRect = $imageSrc;
    }

    public function SetIsBinary($isBinary)
    {
        $this->m_isBinary = $isBinary;
    }




    public function IsContainer()
    {
        return false;
    }

    public function SetAttribute(&$data)
    {
        //padding
        if(array_key_exists('padding', $data)){
            $paddingArr = explode(',', $data['padding']);
            $padding = array(
                'l' => $paddingArr[0],
                'u' => $paddingArr[1],
                'r' => $paddingArr[2],
                'd' => $paddingArr[3],

            );
            $this->SetPadding($padding);
        }

        if(array_key_exists('width', $data)){
            $this->SetWidth($data['width']);
        }

        if(array_key_exists('height', $data)){
            $this->SetHeight($data['height']);
        }

        if(array_key_exists('bkColor', $data)){
            $this->SetBkColor($data['bkColor']);
        }

        if(array_key_exists('bkImage', $data)){
            $this->SetBkImage($data['bkImage']);
        }

        if(array_key_exists('imageSrc', $data)){
            $imageSrcArr = explode(',', $data['imageSrc']);
            $imageSrc = array(
                'x' => $imageSrcArr[0],
                'y' => $imageSrcArr[1],
                'w' => $imageSrcArr[2],
                'h' => $imageSrcArr[3],

            );
            $this->SetBkimageSrc($imageSrc);
        }

        if(array_key_exists('isBinary', $data)){
            $this->SetIsBinary($data['isBinary']);
        }



    }

    public function NeedSize($availableRect)
    {
        return array(
            'w' => $this->m_width,
            'h' => $this->m_height,
        );
    }

    public function SetPos($rc)
    {
        $this->m_rectItem = $rc;
    }
    
    public function DoPaint(&$desImg,$paintRc)
    {

        //绘制顺序  背景颜色 背景图片

        $paintRect = $this->getPaintRect();

        //背景颜色
        if($this->m_bkColor != -1)
        {
            if(imagefilledrectangle($desImg, $paintRect['x'], $paintRect['y'], $paintRect['x'] + $paintRect['w'] - 1, 
                $paintRect['y'] + $paintRect['h'] - 1, $this->m_bkColor)){
            }
        }

        // 背景图片
        if(!empty($this->m_bkImage))
        {
            if(!$this->m_isBinary){
                $bkImage = $this->safeLoadImage($this->m_bkImage);
            }else{
                $bkImage = imagecreatefromstring($this->m_bkImage);
            }
            $imageSrcRect = $this->m_bkImageSrcRect;
            if(empty($imageSrcRect)){
                $imageSrcRect['x'] = 0;
                $imageSrcRect['y'] = 0;
                $imageSrcRect['w'] = imagesx($bkImage);
                $imageSrcRect['h'] = imagesy($bkImage);
            }
            imagecopyresampled($desImg, $bkImage, $paintRect['x'], $paintRect['y'], $imageSrcRect['x'], $imageSrcRect['y'],
                $paintRect['w'], $paintRect['h'], $imageSrcRect['w'], $imageSrcRect['h']);
        }

    }

    protected function getPaintRect()
    {
        if(empty($this->m_rectPadding)){
            return $this->m_rectItem;
        }else{
            $paintRect = array(
                'x' => $this->m_rectItem['x'] + $this->m_rectPadding['l'],
                'y' => $this->m_rectItem['y'] + $this->m_rectPadding['u'],
                'w' => $this->m_rectItem['w'] - $this->m_rectPadding['l'] - $this->m_rectPadding['r'],
                'h' => $this->m_rectItem['h'] - $this->m_rectPadding['u'] - $this->m_rectPadding['d'],
            );
        return $paintRect;
        }

    }

    protected function safeLoadImage($fileUrl)
    {
        $data = getimagesize($fileUrl);
        if($data){
            switch ($data[2]) {
                case 1:
                    return imagecreatefromgif($fileUrl);
                    break;
                case 2:
                    return imagecreatefromjpeg($fileUrl);
                    break;
                case 3:
                    return imagecreatefrompng($fileUrl);
                    break;
                case 6:
                    return imagecreatefrombmp($fileUrl);
                    break;
                
                default:
                    # code...
                    break;
            }
        }
        //不支持的情况 返回默认图片
        return imageCreatetruecolor(100,100);
    }

}