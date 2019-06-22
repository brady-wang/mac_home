# *_*coding:utf-8 *_*
import requests

payload = {'key1': 'value1', 'key2': 'value2'}
headers = {'user-agent': 'my-app/0.0.1'}
page_size = "1000"
page = "1"
for page in range(1,11):

    #url = "https://movie.douban.com/j/search_subjects?type=tv&tag=%E7%83%AD%E9%97%A8&sort=recommend&page_limit="+page_size+"&page_start="+str(page)
    url = "https://movie.douban.com/j/search_subjects?type=movie&tag=%E7%83%AD%E9%97%A8&sort=recommend&page_limit="+page_size+"&page_start="+str(page)
    response = requests.post(url, data=payload,headers=headers)
    res = response.json()['subjects']
    with open('douban_movie_'+str(page)+'.html',"w",encoding='utf-8') as f :
        print("save douban file success",page)
        for data in res:
            f.write("<div style='float:left;'><p><img style='width:100px;height:100px;padding:10px' src='"+data['cover']+"'/></p><p>标题:("+data['title']+")</p><p>评分:"+data['rate']+"</p><a  href = '"+data['url']+"' target='_blank'>查看详情</a></div>")
        f.write("</body></html>")
        f.close()