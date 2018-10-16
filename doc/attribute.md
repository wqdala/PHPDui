# 属性列表

---
## Control
**说明**: 基本控件,包含控件所拥有的必须要素
- **width** 控件宽度 (默认 0 例如 10)
- **height** 控件高度 (默认 0 例如 10)
- **bkcolor** 背景颜色 (默认 0x000000 例如0xFFFFFF)
- **bkimage**  背景图片(默认 "" 例如  "http://www.xx.xxx.png")
- **imageSrc** 背景图片选择区域(默认"0,0,0,0" x,y,w,h 例如 "0,0,10,10")
- **isBinary** 背景图片是否是二进制流 (默认 false 例如 false)
## Label
**extends** Control 

**说明**: 文本控件,可以展示文本信息
- **text** 文本内容 (默认 "" 例如 "你好，你好")
- **fontFile** 字体文件 (默认 当前文件里面的temp.ttf文件，具体需要自己配置 例如 "./temp.ttf")
- **fontcolor** 文字颜色 (默认 0x000000 例如0xFFFFFF)
- **fontSize**  字体大小(默认 20 例如  "10")
- **textPadding** 文本与当前控件的边距(默认"0,0,0,0" l,u,r,d 例如 "2,2,2,2")
- **textAlign** 文本横向对齐方式 (默认 "left" 取值 "left" "right" "center" 例如 "left")
- **textVAlign** 文本纵向对齐方式 (默认 "center" 取值 "top" "bottom" "center" 例如 "top")
- **autoCalcWitdh** 自动计算文本控件宽度 设置之后，控件宽度会根据文本内容及字体等因素自动计算(默认false  例如 true )
- **autoCalcHeight** 自动计算文本控件高度 设置之后，控件高度会根据文本内容及字体等因素自动计算(默认 false  例如 true)
- **endellipsis** 文本超出边界时 使用...代替 (默认 false  例如 true)
## CircleImage
**extends** Control 

**说明**: 圆形图片,可以展示圆形的图片 用于显示头像等元素
- **alphaColor** 将控件中某一颜色值设置为透明色 (默认 0xFFFFFF例如 0xFFAAAA)

--- 
## Container
**extends** Control 

**说明**: 基本布局控件,包含基本布局操作 (该布局下所有控件会依次绘制在当前控件区域内)
## HorizontalLayout
**extends** Container

**说明**: 横向布局  (该布局下所有控件会从左向右依次排列)
## VerticalLayout
**extends** Container

**说明**: 纵向布局 (该布局下所有控件会从上向下依次排列)

