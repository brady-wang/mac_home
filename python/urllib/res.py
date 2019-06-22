<<<<<<< HEAD
#coding=utf-8
import json

import requests

response = requests.get("http://www.baidu.com")
print(response.cookies)

for key,value in response.cookies.items():
=======
#coding=utf-8
import json

import requests

response = requests.get("http://www.baidu.com")
print(response.cookies)

for key,value in response.cookies.items():
>>>>>>> 069d225b498d4ce3c199324fad03bb6a2ee1d884
    print(key+"="+value)