import urllib
from time import sleep

import requests
from lxml import etree



try:
    def all_links(url):
        if "900.html" in url:
            print("结束");
            return None
        response = requests.get(url)
        print(url, response.status_code)
        html = etree.HTML(response.content.decode('gbk'))
        ## 获取图片 并且保存
        imgs = html.xpath('.//div[@id="wrapper"]//div[@class="ui-module"]//img/@src')
        for img in imgs:
            file_name = img.split('/')[-1]
            first = img.split('/')[0]
            if first != 'http:' and first != 'https:':
                print("错误图片"+img)
            else:
                dir_path = "d:\\www\\spider\\images\\"
                urllib.request.urlretrieve(img, dir_path + file_name)
                print("保存图片" + dir_path + file_name + "成功")
        links = html.xpath('.//div[@class="page"]//a[contains(text(),"下一页")]/@href')
        print(links)
        if len(links) < 1:
            pass
        else:
            host = 'http://www.qiubaichengren.net/'
            new_url = host + links[0];
            all_links(new_url)
    all_links("http://www.qiubaichengren.net/356.html")
except Exception as e:
    print(str(e))