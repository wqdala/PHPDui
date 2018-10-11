<?php
namespace PHPDui;
include_once 'Container.php';
    /**
     * Container
     */
class VerticalLayout extends Container
{

    public function __construct()
    {
        # code...
    }

    public function SetPos($rc)
    {
        Control::SetPos($rc);
        $paintRect = $this->getPaintRect();
        $availableHeight = $paintRect['h'];
        $adjustNumber = 0;
        $availableSize = array(
            'w' => $paintRect['w'],
            'h' => $paintRect['h'],

        );
        //计算需要自动分配的
        foreach ($this->m_items as $value) {
            $size = $value->NeedSize($availableSize);
            if($size['h'] == 0){
                ++$adjustNumber;
            }else{
                $availableHeight -= $size['h'];
            }
            

        }

        //分配
        $averageHeight = 0;
        if($adjustNumber != 0){
            $averageHeight = $availableHeight / $adjustNumber;
        }
        $usedHeight = $paintRect['y'];
        foreach ($this->m_items as $value) {
            $size = $value->NeedSize($availableSize);
            if($size['h'] == 0){
                $allocatePos = array(
                    'x' => $paintRect['x'],
                    'y' => $usedHeight,
                    'h' => $averageHeight,
                );
                if($size['w'] == 0){
                    $allocatePos['w'] = $paintRect['w'];
                }else{
                    $allocatePos['w'] = $size['w'];
                }
                $usedHeight += $averageHeight;

            }else{
                $allocatePos = array(
                    'x' => $paintRect['x'],
                    'y' => $usedHeight,
                    'h' => $size['h'],
                );
                if($size['w'] == 0){
                    $allocatePos['w'] = $paintRect['w'];
                }else{
                    $allocatePos['w'] = $size['w'];
                }

                $usedHeight += $size['h'];
            }
            $value->SetPos($allocatePos);

        }
    }
}