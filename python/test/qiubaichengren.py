import urllib

import requests
from lxml import etree

response = requests.get("http://www.qiubaichengren.com/")
def requests_view(response):
    import webbrowser
    requests_url = response.url
    base_url = '<head><base href="%s">' %(requests_url)
    base_url = base_url.encode('utf-8')
    content = response.content.replace(b"<head>",base_url)
    tem_html = open('tmp.html','wb')
    tem_html.write(content)
    tem_html.close()
    webbrowser.open_new_tab("tmp.html")
#requests_view(response)
html = etree.HTML(response.content)
imgs = html.xpath('.//div[@id="wrapper"]//div[@class="ui-module"]//img/@src')
for img in imgs:
    file_name = img.split('/')[-1]
    urllib.request.urlretrieve(img, "d:\\www\\spider\\images\\" + file_name)