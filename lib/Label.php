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
    protected $m_fontFile = '';
    protected $m_fontColor = 0;
    protected $m_textPadding = array(
        't' => 0,
        'b' => 0,
        'l' => 0,
        'r' => 0,

    );// u d l r
    protected $m_align = 'left';
    protected $m_valign = 'center';
    protected $m_lineSpace = 20;
    protected $m_endEllipsis = false;
    protected $m_bAutoCalcWidth = false;
    protected $m_bAutoCalcHeight = false;

    public function __construct()
    {
        parent::__construct();
        $this->m_fontFile = __DIR__."/temp.ttf";

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

    public function setLineSpace($lineSpace)
    {
        $this->m_lineSpace = $lineSpace;
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
        $this->m_fontSize = $fontSize * 72 / 96;
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

        if(array_key_exists('lineSpace', $data)){
            $this->setLineSpace($data['lineSpace']);
        }

        if(array_key_exists('textPadding', $data)){
            $paddingArr = explode(',', $data['textPadding']);
            $padding = array(
                'l' => $paddingArr[0],
                't' => $paddingArr[1],
                'r' => $paddingArr[2],
                'b' => $paddingArr[3],

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
            $textWidth = $textBox[2] - $textBox[0];
            $textHeight = $textBox[3] - $textBox[5];
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

            $textArr = explode("\n", $text);
            $basePosX = $textRect['x'];
            $basePosY = $textRect['y'];

            $singleBox = imagettfbbox($this->m_fontSize, 0, $this->m_fontFile, '1');
            $singleHeight = $singleBox[3] - $singleBox[5];

            $textHeight = count($textArr) * ($singleHeight + $this->m_lineSpace) - $this->m_lineSpace;


            switch ($this->m_valign) {
                case 'top':
                    $basePosY = $textRect['y'];
                    break;
                case 'bottom':
                    $basePosY = $textRect['y'] +  $textRect['h'] - $textHeight;
                    break;
                case 'center':
                    $basePosY = ($textRect['h'] - $textHeight) / 2 + $textRect['y'];
                    break;
                
                default:
                    $basePosY = $textRect['y'];
                    break;
            }
            $basePosY += $singleHeight; //下移一个单字距离 因为gd 绘制的时候在指定位置上面绘制
            foreach ($textArr as $itemText) {
                $itemTextBox = imagettfbbox($this->m_fontSize, 0, $this->m_fontFile, $itemText);
                $itemTextWidth = $itemTextBox[2] - $itemTextBox[0];
                $itemTextHeight = $itemTextBox[3] - $itemTextBox[5];

                switch ($this->m_align) {
                    case 'left':
                        $textPosX = $basePosX;
                        break;

                    case 'right':
                        $textPosX = ($textRect['w'] - $itemTextWidth) + $basePosX;
                        break;

                    case 'center':
                        $textPosX = ($textRect['w'] - $itemTextWidth) / 2 + $basePosX;
                        break;
                    default:
                        $textPosX = $basePosX;
                        break;
                }

                $textPosY = $basePosY;

                imagettftext($desImg,$this->m_fontSize,0,$textPosX,$textPosY,$this->m_fontColor,$this->m_fontFile,$itemText);
                $basePosY += $this->m_lineSpace;
                $basePosY += $itemTextHeight;


            }
        }
    }


    protected function toEndEllipsis($fontsize, $angle, $fontFile, $text, $width)
    {
        $content = "";

         // 将字符串拆分成一个个单字 保存到数组 letter 中
        for($i=0; $i < mb_strlen($text); $i++) {
            $letter[] = mb_substr($text, $i, 1);
        }

        foreach ($letter as $l) {
            $teststr = $content.$l;
            $textBox = imagettfbbox($fontsize, $angle, $fontFile, $teststr);
            // 判断拼接后的字符串是否超过预设的宽度
            if (($textBox[2] - $textBox[0] > $width) && ($content !== "")) {
                $content = mb_substr($content, 0,-1);
                $content .= '...';
                break;
            }
            $content .= $l;
        }
        return $content;
    }


    protected function getTextPaintRect()
    {
        if(empty($this->m_textPadding)){
            return $this->getPaintRect();
        }else{
            $paintRect = $this->getPaintRect();
            $textPaddingRect = array(
                'x' => $paintRect['x'] + $this->m_textPadding['l'],
                'y' => $paintRect['y'] + $this->m_textPadding['t'],
                'w' => $paintRect['w'] - $this->m_textPadding['l'] - $this->m_textPadding['r'],
                'h' => $paintRect['h'] - $this->m_textPadding['t'] - $this->m_textPadding['b'],
            );
            return $textPaddingRect;
        }
    }

    protected function createFontColor(&$desImg)
    {
        return imagecolorallocate($desImg,MyTools::GetColorR($this->m_fontColor),MyTools::GetColorG($this->m_fontColor),MyTools::GetColorB($this->m_fontColor));
    }

    protected function autowrap($fontsize, $angle, $fontFile, $text, $width)
    {
        mb_internal_encoding("UTF-8");
        $content = "";

         // 将字符串拆分成一个个单字 保存到数组 letter 中
        for($i=0; $i < mb_strlen($text); $i++) {
            $letter[] = mb_substr($text, $i, 1);
        }
        $increaseStr = "";
        foreach ($letter as $l) {
            $teststr = $increaseStr.$l;

            $textBox = imagettfbbox($fontsize, $angle, $fontFile, $teststr);
            $textWidth = $textBox[2] - $textBox[0];
            // 判断拼接后的字符串是否超过预设的宽度
            if (( $textWidth > $width) && ($increaseStr !== "")) {
                $content .=$increaseStr. "\n";
                $increaseStr = "";
            }
            $increaseStr .= $l;
        }
        $content .= $increaseStr;
        return $content;
    }


}