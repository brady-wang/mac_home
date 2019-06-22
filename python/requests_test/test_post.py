# *_*coding:utf-8 *_*
import requests
from faker import Faker

url = 'http://test.yeves.cn/test_header.php'
params = {'id':'1','name':'海笑磊'}
fake = Faker()
user_agent = fake.user_agent()
print(user_agent)
headers = {
    'User-Agent': user_agent,
    'Referer':url
}
cookies = dict(name='working')
response = requests.post(url,data=params,headers=headers,cookies=cookies)
response.encoding = 'utf-8'
print(response)
print(response.status_code)
print(response.encoding)
print(response.headers)
print(response.url)
print(response.text)
print(response.cookies)
print(response.headers)

