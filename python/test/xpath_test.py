html_str = """
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>网页名</title>
</head>
<body>
    <div>
        div-text
        <span>span-text</span>
        <a>a-text</a>
        <p>p-text</p>
    </div>
    <table>
        <tr>
            <th>Heading</th>
            <th>Another Heading</th>
        </tr>
        <tr>
            <td>row 1, cell 1</td>
            <td>row 1, cell 2</td>
        </tr>
        table-text-2
    </table>
</body>
</html>
"""

from lxml import etree
html = etree.HTML(html_str)
res = html.xpath('head/title/text()')
res = html.xpath('normalize-space(body/div/text())')
print(res)