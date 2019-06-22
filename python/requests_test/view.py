# *_*coding:utf-8 *_*
from lxml import etree

import requests


def requests_view(response):
    request_url = response.url
    base_url = '<head><base href= "%s"> ' % (request_url)
    base_url = base_url.encode()
    content = response.content.replace(b"<head>",base_url)
    tmp_html = open("tmp.html",'wb')
    tmp_html.write(content)
    tmp_html.close()
    import webbrowser
    webbrowser.open_new_tab("tmp.html")


url = 'https://music.163.com/#/song?id=513360721'
headers = {
    'Referer':'https://music.163.com/',
    'User-Agent':'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36',
}
data = {
    'id':513360721
}
response = requests.get(url,headers=headers,data=data)
#requests_view(response)
content = response.content.decode()
tree = etree.HTML(content)
title = tree.xpath('//*[@id="lh"]/a[1]/text()')
print(title)