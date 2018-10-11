<?php
namespace PHPDui;
include_once 'Control.php';
    /**
     * CircleImage
     */
class Label extends Control
{
    //文本
    protected $m_text = '';
    protected $m_fontSize = 20;
    protected $m_fontFile = 'res/simhei.ttf';
    protected $m_fontColor = 0;
    protected $m_textPadding = array(
        'u' => 0,
        'd' => 0,
        'l' => 0,
        'r' => 0,

    );// u d l r
    protected $m_align = 'left';
    protected $m_valign = 'center';
    protected $m_endEllipsis = false;
    protected $m_bAutoCalcWidth = false;
    protected $m_bAutoCalcHeight = false;

    public function __construct()
    {
        # code...
    }

    public function SetTextPadding($textPadding)
    {
        $this->m_textPadding = $textPadding;
    }

    public function setAlgin($align)
    {
        $this->m_align = $align;
    }

    public function setValign($valign)
    {
        $this->m_valign = $valign;
    }

    public function SetText($text)
    {
        $this->m_text = $text;
    }

    public function SetFontFile($fontfile)
    {
        $this->m_fontFile = $fontfile;
    }

    public function SetFontColor($fontColor)
    {
        $this->m_fontColor = $fontColor;
    }

    public function SetFontSize($fontSize)
    {
        $this->m_fontSize = $fontSize;
    }

    public function SetAutoCalcWidth($autoCalcWidth)
    {
        $this->m_bAutoCalcWidth = $autoCalcWidth;
    }

    public function SetAutoCalcHeight($autoCalcHeight)
    {
        $this->m_bAutoCalcHeight = $autoCalcHeight;
    }

    public function SetEndEllipsis($endellipsis)
    {
        $this->m_endEllipsis = $endellipsis;
    }



    public function SetAttribute(&$data)
    {
        parent::SetAttribute($data);
        if(array_key_exists('text', $data)){
            $this->SetText($data['text']);
        }

        if(array_key_exists('fontFile', $data)){
            $this->SetFontFile($data['fontFile']);
        }
        
        if(array_key_exists('fontColor', $data)){
            $this->SetFontColor($data['fontColor']);
        }

        if(array_key_exists('fontSize', $data)){
            $this->SetFontSize($data['fontSize']);
        }

        if(array_key_exists('textPadding', $data)){
            $paddingArr = explode(',', $data['textPadding']);
            $padding = array(
                'l' => $paddingArr[0],
                'u' => $paddingArr[1],
                'r' => $paddingArr[2],
                'd' => $paddingArr[3],

            );
            $this->SetTextPadding($padding);
        }

        if(array_key_exists('textAlign', $data)){
            $this->setAlgin(strtolower($data['textAlign']));
        }

        if(array_key_exists('textVAlign', $data)){
            $this->setValign(strtolower($data['textVAlign']));
        }

        if(array_key_exists('autoCalcWidth', $data)){
            $this->SetAutoCalcWidth(strtolower($data['autoCalcWidth']));
        }

        if(array_key_exists('autoCalcHeight', $data)){
            $this->SetAutoCalcHeight(strtolower($data['autoCalcHeight']));
        }

        if(array_key_exists('endellipsis', $data)){
            $this->SetEndEllipsis($data['endellipsis']);
        }


    }

    public function NeedSize($availableRect)
    {
        $size = array(
            'w' => $this->m_width,
            'h' => $this->m_height,
        );
        if($this->m_bAutoCalcWidth || $this->m_bAutoCalcHeight){
            $text = $this->autowrap($this->m_fontSize,0,$this->m_fontFile,$this->m_text,$availableRect['w']);
            $textBox = imagettfbbox($this->m_fontSize, 0, $this->m_fontFile, $text);
            $textWidth = $textBox[2];
            $textHeight = abs($textBox[5]);
            if($this->m_bAutoCalcWidth){
                $size['w'] = $textWidth + $this->m_rectPadding['l'] + $this->m_rectPadding['r'];
            }

            if($this->m_bAutoCalcHeight){
                $size['h'] = $textHeight + $this->m_rectPadding['t'] + $this->m_rectPadding['b'];
            }
        }

        return $size;
    }

    public function DoPaint(&$desImg,$paintRc)
    {
        parent::DoPaint($desImg,$paintRc);

        //文本
        if(!empty($this->m_text)){
            $textRect = $this->getTextPaintRect();
            $fontColor = $this->createFontColor($desImg);
            if(!$this->m_endEllipsis){
                $text = $this->autowrap($this->m_fontSize,0,$this->m_fontFile,$this->m_text,$textRect['w']);
            }else{
                $text = $this->toEndEllipsis($this->m_fontSize,0,$this->m_fontFile,$this->m_text,$textRect['w']);
            }

            // $text = $this->m_text;
            $textBox = imagettfbbox($this->m_fontSize, 0, $this->m_fontFile, $text);
            $textWidth = $textBox[2];
            $textHeight = abs($textBox[5]);
            
            $textPosX = 0;
            $textPosY = 0;
            switch ($this->m_align) {
                case 'left':
                    $textPosX = $textRect['x'];
                    break;

                case 'right':
                    $textPosX = ($textRect['w'] - $textWidth) + $textRect['x'];
                    break;

                case 'center':
                    $textPosX = ($textRect['w'] - $textWidth) / 2 + $textRect['x'];
                    break;
                default:
                    $textPosX = $textRect['x'];
                    break;
            }

            switch ($this->m_valign) {
                case 'top':
                    $textPosY = $textRect['y'] + $textHeight;
                    break;
                case 'bottom':
                    $textPosY = $textRect['h'] + $textRect['y'];
                    break;
                case 'center':
                    $textPosY = ($textRect['h'] - $textHeight) / 2 + $textHeight + $textRect['y'];
                    break;
                
                default:
                    $textPosY = $textRect['y'] + $textHeight;
                    break;
            }

            imagettftext($desImg,$this->m_fontSize,0,$textPosX - 1,$textPosY - 1,$fontColor,$this->m_fontFile,$text);
        }
    }


    private function toEndEllipsis($fontsize, $angle, $fontFile, $text, $width)
    {
        $content = "";

         // 将字符串拆分成一个个单字 保存到数组 letter 中
        for($i=0; $i < mb_strlen($text); $i++) {
            $letter[] = mb_substr($text, $i, 1);
        }

        foreach ($letter as $l) {
            $teststr = $content.$l;
            $testbox = imagettfbbox($fontsize, $angle, $fontFile, $teststr);
            // 判断拼接后的字符串是否超过预设的宽度
            if (($testbox[2] > $width) && ($content !== "")) {
                $content = mb_substr($content, 0,-1);
                $content .= '...';
                break;
            }
            $content .= $l;
        }
        return $content;
    }


    private function getTextPaintRect()
    {
        if(empty($this->m_textPadding)){
            return $this->getPaintRect();
        }else{
            $paintRect = $this->getPaintRect();
            $textPaddingRect = array(
                'x' => $paintRect['x'] + $this->m_textPadding['l'],
                'y' => $paintRect['y'] + $this->m_textPadding['u'],
                'w' => $paintRect['w'] - $this->m_textPadding['l'] - $this->m_textPadding['r'],
                'h' => $paintRect['h'] - $this->m_textPadding['u'] - $this->m_textPadding['d'],
            );
            return $textPaddingRect;
        }
    }

    private function createFontColor(&$desImg)
    {
        return imagecolorallocate($desImg,MyTools::GetColorR($this->m_fontColor),MyTools::GetColorG($this->m_fontColor),MyTools::GetColorB($this->m_fontColor));
    }

    private function autowrap($fontsize, $angle, $fontFile, $text, $width)
    {
        $content = "";

         // 将字符串拆分成一个个单字 保存到数组 letter 中
        for($i=0; $i < mb_strlen($text); $i++) {
            $letter[] = mb_substr($text, $i, 1);
        }

        foreach ($letter as $l) {
            $teststr = $content.$l;
            $testbox = imagettfbbox($fontsize, $angle, $fontFile, $teststr);
            // 判断拼接后的字符串是否超过预设的宽度
            if (($testbox[2] > $width) && ($content !== "")) {
                $content .= "\n";
            }
            $content .= $l;
        }
        return $content;
    }


}