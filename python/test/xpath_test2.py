html_str = """
<body>
<div class="ui container">

        <table class="ui striped  table">
            <tr>
                <th>姓名</th>
                <th>性别</th>
                <th>邮箱</th>
                <th>电话</th>
            </tr>
            <tr>
                <td><a href="zhangwei">张伟</a></td>
                <td>男</td>
                <td>zhangwei@haoren.com</td>
                <td>12138-111</td>
            </tr>
            <tr>
                <td><a href="yifei">一菲</a></td>
                <td>女</td>
                <td>yifei@haoren.com</td>
                <td>12138-112</td>
            </tr>
            <tr>
                <td><a href="xiaoxian">小贤</a></td>
                <td>男</td>
                <td>xiaoxian@haoren.com</td>
                <td>12138-113</td>
            </tr>
            <tr>
                <td><a href="meijia">美嘉</a></td>
                <td>女</td>
                <td>meijia@haoren.com</td>
                <td>12138-114</td>
            </tr>
            <tr>
                <td><a href="xiaobu">小布</a></td>
                <td>男</td>
                <td>xiaobu@hundan.com</td>
                <td>12138-115</td>
            </tr>

        </table>
</div>
</body>
"""
from lxml import etree

html = etree.HTML(html_str)
print(html.xpath('.//table/tr/th/text()'))
print(html.xpath('.//table/tr/td[4]/text()'))
print(html.xpath('.//table/tr/td[text()="男"]/../td[1]/a/text()'))
print(html.xpath('.//table/tr/td[contains(text(),"haoren")]/text()'))
print(html.xpath('.//table/tr[2]/td/text()'))