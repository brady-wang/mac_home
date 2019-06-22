# *_*coding:utf-8 *_*
import urllib.request

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




for i in range(1,10000):
    print('crawl page : '+str(i))
    url ="http://www.qiubaichengnian.com/qiubaichengrenban/"+str(i)+".html"
    headers = {
        'User-Agent':'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36',
        'X-DevTools-Emulate-Network-Conditions-Client-Id':'B4A9B2BB626C6C97A8F85A8A06F79E68'
    }
    try:
        response = requests.get(url,headers=headers)
        tree = etree.HTML(response.content.decode("gb2312"))
        img = tree.xpath('//*[@id="wrapper"]/div/div[1]/div[1]/div[2]/div[2]/a/img/@src')
        title = tree.xpath('//*[@id="wrapper"]/div/div[1]/div[1]/div[2]/div[1]/a/text()')
        print(title)
        if len(img) == 1 and len(title) == 1 and "http" in img[0]:
            title = ','.join(title)
            img = ','.join(img)
            print(title,img)
            file_name = title + ".jpg"
            try:
                urllib.request.urlretrieve(img, file_name)
            except Exception as e:
                print(str(e))
    except Exception as e:
        print(e)