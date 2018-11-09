<?php
namespace PHPDui;
include_once 'MyTools.php';
class Control
{
    //控件位置
    protected $m_rectItem; // x y w h

    //控件属性
    protected $m_width = 0;
    protected $m_height = 0;
    protected $m_rectPadding = array(
        't' => 0,
        'b' => 0,
        'l' => 0,
        'r' => 0,

    ); // t b l r

    //背景颜色
    protected $m_bkColor = -1;

    //背景图片
    protected $m_bkImage = '';
    protected $m_isBinary = false;
    protected $m_bkImageSrcRect = array(
    );
    protected $m_alphaColor = -1;

    function __construct()
    {
    }

    public function SetAlphaColor($color)
    {
        $this->m_alphaColor = $color;
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
                't' => $paddingArr[1],
                'r' => $paddingArr[2],
                'b' => $paddingArr[3],

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

        if(array_key_exists('alphaColor', $data)){
            $this->SetAlphaColor($data['alphaColor']);
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
            if($this->m_alphaColor != -1){
                $tempImg = imageCreatetruecolor($paintRect['w'],$paintRect['h']);
                imagecopyresampled($tempImg, $bkImage, 0, 0, $imageSrcRect['x'], $imageSrcRect['y'], 
                    $paintRect['w'], $paintRect['h'], $imageSrcRect['w'], $imageSrcRect['h']);
                imagecolortransparent($tempImg,$this->m_alphaColor);
                // imagecopymerge(dst_im, src_im, dst_x, dst_y, src_x, src_y, src_w, src_h, pct)
                imagecopymerge($desImg, $tempImg, $paintRect['x'], $paintRect['y'], 0, 0,
                 $paintRect['w'], $paintRect['h'], 100);

            }else{
                imagecopyresampled($desImg, $bkImage, $paintRect['x'], $paintRect['y'], $imageSrcRect['x'], $imageSrcRect['y'],
                    $paintRect['w'], $paintRect['h'], $imageSrcRect['w'], $imageSrcRect['h']);
            }

            
        }

    }

    protected function getPaintRect()
    {
        if(empty($this->m_rectPadding)){
            return $this->m_rectItem;
        }else{
            $paintRect = array(
                'x' => $this->m_rectItem['x'] + $this->m_rectPadding['l'],
                'y' => $this->m_rectItem['y'] + $this->m_rectPadding['t'],
                'w' => $this->m_rectItem['w'] - $this->m_rectPadding['l'] - $this->m_rectPadding['r'],
                'h' => $this->m_rectItem['h'] - $this->m_rectPadding['t'] - $this->m_rectPadding['b'],
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
                    if(version_compare(PHP_VERSION, "7",">=")){
                        return imagecreatefrombmp($fileUrl);
                    }else{
                        return $this->myimagecreatefrombmp($fileUrl);
                    }
                    break;
                
                default:
                    # code...
                    break;
            }
        }
        //不支持的情况 返回默认图片
        return imageCreatetruecolor(100,100);
    }

    private function myimagecreatefrombmp($fileUrl)
    {
        $f=fopen($fileUrl,"r");
        $Header=fread($f,2);

        if($Header=="BM")
        {
            $Size=$this->freaddword($f);
            $Reserved1=$this->freadword($f);
            $Reserved2=$this->freadword($f);
            $FirstByteOfImage=$this->freaddword($f);

            $SizeBITMAPINFOHEADER=$this->freaddword($f);
            $Width=$this->freaddword($f);
            $Height=$this->freaddword($f);
            $biPlanes=$this->freadword($f);
            $biBitCount=$this->freadword($f);
            $RLECompression=$this->freaddword($f);
            $WidthxHeight=$this->freaddword($f);
            $biXPelsPerMeter=$this->freaddword($f);
            $biYPelsPerMeter=$this->freaddword($f);
            $NumberOfPalettesUsed=$this->freaddword($f);
            $NumberOfImportantColors=$this->freaddword($f);

            if($biBitCount<24)
            {
                $img=imagecreate($Width,$Height);
                $Colors=pow(2,$biBitCount);
                for($p=0;$p<$Colors;$p++)
                {
                    $B=$this->freadbyte($f);
                    $G=$this->freadbyte($f);
                    $R=$this->freadbyte($f);
                    $Reserved=$this->freadbyte($f);
                    $Palette[]=imagecolorallocate($img,$R,$G,$B);
                }

                if($RLECompression==0)
                {
                    $Zbytek=(4-ceil(($Width/(8/$biBitCount)))%4)%4;

                    for($y=$Height-1;$y>=0;$y--)
                    {
                        $CurrentBit=0;
                        for($x=0;$x<$Width;$x++)
                        {
                            $C=freadbits($f,$biBitCount);
                            imagesetpixel($img,$x,$y,$Palette[$C]);
                        }
                        if($CurrentBit!=0) {$this->freadbyte($f);}
                        for($g=0;$g<$Zbytek;$g++)
                        $this->freadbyte($f);
                    }

                }
            }
            if($RLECompression==1) //$BI_RLE8
            {
                $y=$Height;

                $pocetb=0;

                while(true)
                {
                    $y--;
                    $prefix=$this->freadbyte($f);
                    $suffix=$this->freadbyte($f);
                    $pocetb+=2;

                    $echoit=false;

                    if($echoit)echo "Prefix: $prefix Suffix: $suffix<BR>";
                    if(($prefix==0)and($suffix==1)) break;
                    if(feof($f)) break;

                    while(!(($prefix==0)and($suffix==0)))
                    {
                        if($prefix==0)
                        {
                            $pocet=$suffix;
                            $Data.=fread($f,$pocet);
                            $pocetb+=$pocet;
                            if($pocetb%2==1) {$this->freadbyte($f); $pocetb++;}
                        }
                        if($prefix>0)
                        {
                            $pocet=$prefix;
                            for($r=0;$r<$pocet;$r++)
                            $Data.=chr($suffix);
                        }
                        $prefix=$this->freadbyte($f);
                        $suffix=$this->freadbyte($f);
                        $pocetb+=2;
                        if($echoit) echo "Prefix: $prefix Suffix: $suffix<BR>";
                    }

                    for($x=0;$x<strlen($Data);$x++)
                    {
                        imagesetpixel($img,$x,$y,$Palette[ord($Data[$x])]);
                    }
                    $Data="";

                }

            }


            if($RLECompression==2) //$BI_RLE4
            {
                $y=$Height;
                $pocetb=0;

                while(true)
                {
                    //break;
                    $y--;
                    $prefix=$this->freadbyte($f);
                    $suffix=$this->freadbyte($f);
                    $pocetb+=2;

                    $echoit=false;

                    if($echoit)echo "Prefix: $prefix Suffix: $suffix<BR>";
                    if(($prefix==0)and($suffix==1)) break;
                    if(feof($f)) break;

                    while(!(($prefix==0)and($suffix==0)))
                    {
                        if($prefix==0)
                        {
                            $pocet=$suffix;

                            $CurrentBit=0;
                            for($h=0;$h<$pocet;$h++)
                            $Data.=chr(freadbits($f,4));
                            if($CurrentBit!=0) freadbits($f,4);
                            $pocetb+=ceil(($pocet/2));
                            if($pocetb%2==1) {$this->freadbyte($f); $pocetb++;}
                        }
                        if($prefix>0)
                        {
                            $pocet=$prefix;
                            $i=0;
                            for($r=0;$r<$pocet;$r++)
                            {
                                if($i%2==0)
                                {
                                    $Data.=chr($suffix%16);
                                }
                                else
                                {
                                    $Data.=chr(floor($suffix/16));
                                }
                                $i++;
                            }
                        }
                        $prefix=$this->freadbyte($f);
                        $suffix=$this->freadbyte($f);
                        $pocetb+=2;
                        if($echoit) echo "Prefix: $prefix Suffix: $suffix<BR>";
                    }

                    for($x=0;$x<strlen($Data);$x++)
                    {
                        imagesetpixel($img,$x,$y,$Palette[ord($Data[$x])]);
                    }
                    $Data="";

                }

            }


            if($biBitCount==24)
            {
                $img=imagecreatetruecolor($Width,$Height);
                $Zbytek=$Width%4;

                for($y=$Height-1;$y>=0;$y--)
                {
                    for($x=0;$x<$Width;$x++)
                    {
                        $B=$this->freadbyte($f);
                        $G=$this->freadbyte($f);
                        $R=$this->freadbyte($f);
                        $color=imagecolorexact($img,$R,$G,$B);
                        if($color==-1) $color=imagecolorallocate($img,$R,$G,$B);
                        imagesetpixel($img,$x,$y,$color);
                    }
                    for($z=0;$z<$Zbytek;$z++)
                    $this->freadbyte($f);
                }
            }

            return $img;

        }


    fclose($f);

    }

    private function freadbyte($f)
    {
        return ord(fread($f,1));
    }

    private function freadword($f)
    {
        $b1=$this->freadbyte($f);
        $b2=$this->freadbyte($f);
        return $b2*256+$b1;
    }

    private function freaddword($f)
    {
        $b1=$this->freadword($f);
        $b2=$this->freadword($f);
        return $b2*65536+$b1;
    }
}