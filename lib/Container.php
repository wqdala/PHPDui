<?php
include_once 'Control.php';
    /**
     * Container
     */
class Container extends Control
{        

    protected $m_items = array();

    public function __construct()
    {
        # code...
    }

    public function DoPaint(&$desImg,$paintRc)
    {
        $paintRect = $paintRc;
        parent::DoPaint($desImg,$paintRect);

        // echo 'do sub painting'.count($this->m_items);
        //绘制子项
        foreach ($this->m_items as $value) {
            $value->DoPaint($desImg,$paintRect);
        }
    }

    public function SetPos($rc)
    {
        Control::SetPos($rc);
        $subRect = array();
        if(empty($this->m_rectPadding)){
            $subRect = $this->m_rectItem;
        }else{
            $subRect = array(
                'x' => $this->m_rectItem['x'] + $this->m_rectPadding['l'],
                'y' => $this->m_rectItem['y'] + $this->m_rectPadding['u'],
                'w' => $this->m_rectItem['w'] - $this->m_rectPadding['l'] - $this->m_rectPadding['r'],
                'h' => $this->m_rectItem['h'] - $this->m_rectPadding['u'] - $this->m_rectPadding['d'],
            );
        }
        foreach ($this->m_items as $value) {
            $value->SetPos($subRect);
        }
    }

    public function IsContainer()
    {
        return true;
    }

    public function AddItem($control)
    {
        $this->m_items[] = $control;
    }
}