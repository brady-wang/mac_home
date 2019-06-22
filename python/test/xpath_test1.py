html_str = """
<body>
<div class="container">
    <div id="first">
        <div class="one">都市</div>
        <div class="two">德玛西亚</div>
        <div class="two">王牌对王牌</div>
        <a>
            <div class="spe">特殊位置</div>
        </a>
    </div>
    <div id="second">
        <div class="three">水电费</div>
        <div class="three">说的话房间不开封</div>
        <div class="four">三顿饭黑客技术</div>
    </div>
    <div id="third">
        <div class="three">水电费</div>
        <div class="three">说的话房间开封</div>
    </div>
</div>
</body>
"""

from lxml import etree
html = etree.HTML(html_str)
print(html.xpath('.//div[@id="first"]/div/text()'))
print(html.xpath('.//div[@id="first"]//div/text()'))
print(html.xpath('.//div[@id="second"]/div[@class="three"]/text()'))
print(html.xpath('.//div[@class="three"]/text()'))
print(html.xpath('.//div[text()="水电费"]/@class'))
