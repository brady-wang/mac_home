<<<<<<< HEAD
#coding=utf-8
import urllib.request
import urllib.parse

try:

    data = bytes(urllib.parse.urlencode({'id':1,'name':'wang'}),encoding='utf-8')
    headers = {
        'User-agent':"Mozilla/4.0 (compatible; MSIE 5.5; Windows NT)",
    }
    url = "http://wang.com/python/test_python.php"

    request = urllib.request.Request(url=url,data=data,headers=headers,method="POST")
    response = urllib.request.urlopen(request)
    print(response.read().decode('utf-8'))
    #response.status、response.getheaders().response.getheader("server")

except Exception as e:
=======
#coding=utf-8
import urllib.request
import urllib.parse

try:

    data = bytes(urllib.parse.urlencode({'id':1,'name':'wang'}),encoding='utf-8')
    headers = {
        'User-agent':"Mozilla/4.0 (compatible; MSIE 5.5; Windows NT)",
    }
    url = "http://wang.com/python/test_python.php"

    request = urllib.request.Request(url=url,data=data,headers=headers,method="POST")
    response = urllib.request.urlopen(request)
    print(response.read().decode('utf-8'))
    #response.status、response.getheaders().response.getheader("server")

except Exception as e:
>>>>>>> 069d225b498d4ce3c199324fad03bb6a2ee1d884
    print(str(e))