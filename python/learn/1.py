# *_*coding:utf-8 *_* 

import requests

url = "http://www.baidu.com"

res = requests.get(url)
print(res.headers)
print(res.status_code)

print(res.content.decode('utf-8'))