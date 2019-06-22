# *_*coding:utf-8 *_*
import string
from urllib.parse import quote

import requests
class Downloader(object):
    def Download(self,url):
        s = requests.session()
        s.keep_alive = False
        requests.adapters.DEFAULT_RETRIES = 5

        headers = {"User-Agent": "User-Agent:Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0;"}
        url_ = quote(url, safe=string.printable)
        response = requests.get(url_,headers=headers)

        if response.status_code == 200:
            response.encoding='gb2312'
            return response.text
        else :
            return None