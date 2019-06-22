#coding=utf-8
import requests


def requests_view(response):
    import webbrowser
    requests_url = response.url
    base_url = '<head><base href="%s">' %(requests_url)
    base_url = base_url.encode('utf-8')
    content = response.content.replace(b"<head>",base_url)
    tem_html = open('tmp.html','wb')
    tem_html.write(content)
    tem_html.close()
    webbrowser.open_new_tab("tmp.html")
headers = {"User-Agent":'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36'}
proxies = {'https':"114.215.107.94:60443",'http':"211.147.67.150:80"}
requests_view(requests.get("http://www.spbeen.com/tool/request_info/",headers=headers,proxies=proxies))