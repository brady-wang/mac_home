# *_*coding:utf-8 *_*
import requests

url = 'https://music.163.com/#/song?id=513360721'
headers = {
    'Referer':'https://music.163.com/',
    'User-Agent':'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36',
}
data = {
    'id':513360721
}
response = requests.get(url,headers=headers,data=data)
print(response.content.decode('utf-8'))