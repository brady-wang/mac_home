<<<<<<< HEAD
#encoding=utf-8
import urllib.request

proxy_handler = urllib.request.ProxyHandler(
    {
       'http':'http://115.223.225.110:9000' ,
    }
)
url = "http://www.baidu.com"
opener = urllib.request.build_opener(proxy_handler)
response = opener.open(url)
=======
#encoding=utf-8
import urllib.request

proxy_handler = urllib.request.ProxyHandler(
    {
       'http':'http://115.223.225.110:9000' ,
    }
)
url = "http://www.baidu.com"
opener = urllib.request.build_opener(proxy_handler)
response = opener.open(url)
>>>>>>> 069d225b498d4ce3c199324fad03bb6a2ee1d884
print(response.read())