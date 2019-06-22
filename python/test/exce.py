#coding=utf-8
import re
from lxml import etree
import requests

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

response = requests.get("http://www.qiubaichengren.net/27.html")
#requests_view(response)
html = etree.HTML(response.content)
res = html.xpath('.//*[@id="wrapper"]/div/div[1]/div[9]/div/a[contains(text(),"下一页")]/@href')
print(res)
