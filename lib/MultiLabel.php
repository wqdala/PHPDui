<?php
namespace PHPDui;
require_once 'Label.php';
    /**
     * CircleImage
     */
class MultiLabel extends Label
{
    //文本
    protected $m_multiText;
    protected $m_wordSpace = 5;

    public function __construct()
    {
        parent::__construct();
        $this->m_multiText = array();

    }

    public function setMultiText($multiText)
    {
        $this->m_multiText = $multiText;
    }

    public function setWordSpace($wordSpace)
    {
        $this->m_wordSpace = $wordSpace;
    }

    public function SetAttribute(&$data)
    {
        parent::SetAttribute($data);
        if(array_key_exists('multiText', $data)){
            $this->setMultiText($data['multiText']);
        }

        if(array_key_exists('wordSpace', $data)){
            $this->setWordSpace($data['wordSpace']);
        }


    }

    public function NeedSize($availableRect)
    {
        return Control::NeedSize($availableRect);
    }

    public function DoPaint(&$desImg,$paintRc)
    {
        parent::DoPaint($desImg,$paintRc);

        //多重文本
        if(!empty($this->m_multiText)){
            $textRect = $this->getTextPaintRect();
            //1. 计算文本所占用的位置,以便于文字对齐
            $textHeight = 0;
            $textWidth = 0;
            foreach ($this->m_multiText as $value) {
                $itemText = '';
                if(array_key_exists('text', $value)){
                    $itemText = $value['text'];
                }

                $itemFontSize = $this->m_fontSize;
                if(array_key_exists('fontSize', $value)){
                    $itemFontSize = $value['fontSize'];
                }

                $itemFontFile = $this->m_fontFile;
                if(array_key_exists('fontFile', $value)){
                    $itemFontFile = $value['fontFile'];
                }

                $itemTextBox = imagettfbbox($itemFontSize, 0, $itemFontFile, $itemText);
                $itemWith = $itemTextBox[2] - $itemTextBox[0];
                $itemHeight = $itemTextBox[3] - $itemTextBox[5];

                $itemPadding = array(
                    'l' => 0,
                    't' => 0,
                    'r' => 0,
                    'b' => 0,
                );

                if(array_key_exists('padding', $value)){
                    $paddingArr = explode(',', $value['padding']);
                    $itemPadding = array(
                        'l' => $paddingArr[0],
                        't' => $paddingArr[1],
                        'r' => $paddingArr[2],
                        'b' => $paddingArr[3],

                    );
                }
                $itemHeight = $itemHeight + $itemPadding['t'] + $itemPadding['b'];
                if($textHeight < $itemHeight){
                    $textHeight = $itemHeight;
                }

                $textWidth += $itemWith + $itemPadding['r'] + $itemPadding['l'] + $this->m_wordSpace;
            }
            $textWidth -= $this->m_wordSpace;

            //2根据对齐方式 计算绘制初始位置
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
                    $textPosY = $textRect['y'] + $textRect['h'];
                    break;
                case 'center':
                    $textPosY = ($textRect['h'] - $textHeight) / 2 + $textRect['y'] + $textHeight;
                    break;
                
                default:
                    $textPosY = $textRect['y'] + $textHeight;
                    break;
            }
            $textPosY -= $textHeight;
            //3 开始绘制
            $paintBegin = 0;
            foreach ($this->m_multiText as $value) {
                $itemText = '';
                if(array_key_exists('text', $value)){
                    $itemText = $value['text'];
                }

                $itemFontSize = $this->m_fontSize;
                if(array_key_exists('fontSize', $value)){
                    $itemFontSize = $value['fontSize'];
                }

                $itemFontFile = $this->m_fontFile;
                if(array_key_exists('fontFile', $value)){
                    $itemFontFile = $value['fontFile'];
                }

                $itemFontColor = $this->m_fontColor;
                if(array_key_exists('fontColor', $value)){
                    $itemFontColor = $value['fontColor'];
                }

                $itemVAlign = 'center';
                if(array_key_exists('textVAlign', $value)){
                    $itemVAlign = $value['textVAlign'];
                }

                $itemPadding = array(
                    'l' => 0,
                    't' => 0,
                    'r' => 0,
                    'b' => 0,
                );

                if(array_key_exists('padding', $value)){
                    $paddingArr = explode(',', $value['padding']);
                    $itemPadding = array(
                        'l' => $paddingArr[0],
                        't' => $paddingArr[1],
                        'r' => $paddingArr[2],
                        'b' => $paddingArr[3],

                    );
                }

                $itemTextBox = imagettfbbox($itemFontSize, 0, $itemFontFile, $itemText);
                $itemWith = $itemTextBox[2] - $itemTextBox[0];
                $itemHeight = $itemTextBox[3] - $itemTextBox[5];
                switch ($itemVAlign) {
                    case 'top':
                        $itemPosY = $textPosY + $itemHeight;
                        break;
                    case 'bottom':
                        $itemPosY = $textPosY + $textHeight - $itemHeight;
                        break;
                    case 'center':
                        $itemPosY = ($textHeight - $itemHeight) / 2 + $textPosY + $itemHeight;
                        break;
                    
                    default:
                        $itemPosY = $textRect['y'] + $textHeight;
                        break;
                }

                imagettftext($desImg, $itemFontSize, 0, $textPosX + $paintBegin + $itemPadding['l'], $itemPosY + $itemPadding['t'], $itemFontColor, $itemFontFile, $itemText);

               $paintBegin += $itemWith + $itemPadding['r'] + $itemPadding['l'] + $this->m_wordSpace;
            }
        }
    }


}