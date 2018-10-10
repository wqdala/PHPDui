<?php

include_once 'Container.php';
    /**
     * Container
     */
class HorizontalLayout extends Container
{

    public function __construct()
    {
        # code...
    }

    public function SetPos($rc)
    {
        Control::SetPos($rc);
        $paintRect = $this->getPaintRect();
        $availableWidth = $paintRect['w'];
        $adjustNumber = 0;
        $availableSize = array(
            'w' => $paintRect['w'],
            'h' => $paintRect['h'],

        );
        //计算需要自动分配的
        foreach ($this->m_items as $value) {
            $size = $value->NeedSize($availableSize);
            if($size['w'] == 0){
                ++$adjustNumber;
            }else{
                $availableWidth -= $size['w'];
            }
        }

        //分配
        $averageWidth = 0;
        if($adjustNumber != 0){
            $averageWidth = $availableWidth / $adjustNumber;
        }
        $usedWidth = $paintRect['x'];
        foreach ($this->m_items as $value) {
            $size = $value->NeedSize($availableSize);
            if($size['w'] == 0){
                $allocatePos = array(
                    'x' => $usedWidth,
                    'y' => $paintRect['y'],
                    'w' => $averageWidth,
                );
                if($size['h'] == 0){
                    $allocatePos['h'] = $paintRect['h'];
                }else{
                    $allocatePos['h'] = $size['h'];
                }
                $usedWidth += $averageWidth;

            }else{
                $allocatePos = array(
                    'x' => $usedWidth,
                    'y' => $paintRect['y'],
                    'w' => $size['w'],
                );
                if($size['h'] == 0){
                    $allocatePos['h'] = $paintRect['h'];
                }else{
                    $allocatePos['h'] = $size['h'];
                }

                $usedWidth += $size['w'];
            }
            $value->SetPos($allocatePos);

        }
    }
}