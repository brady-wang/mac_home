# *_*coding:utf-8 *_*
import json
import urllib
from time import sleep

import pymysql
from lxml import etree
import requests

class GetChildTopic(object):
    def __init__(self):
        self.conn = pymysql.connect(host='192.168.33.10', user='root', passwd='root', db='spider', charset='utf8')
        self.cur = self.conn.cursor()

    def sql_filter(self,sql, max_length=20):
        dirty_stuff = ["\"", "\\", "/", "*", "'", "=", "-", "#", ";", "<", ">", "+", "%", "$", "(", ")", "%", "@", "!"]
        for stuff in dirty_stuff:
            sql = sql.replace(stuff, "")
        return sql[:max_length]

    def get_child_data(self,parent_id, total_pages):
        int(parent_id)

        for page in range(1, total_pages + 1):
            #sleep(1)
            output = []
            print("now_parent_id",parent_id,"now_page:",page)
            url = "https://www.zhihu.com/node/TopicsPlazzaListV2"
            headers = {
                "User-Agent": "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36",
            }
            offset = (page - 1) * 20
            data = {'method': 'next', "params": json.dumps({"topic_id": parent_id, "offset": offset, "hash_id": ""})}
            response = requests.post(url, data=data, headers=headers)
            print(url,response,);
            print(data)

            res = response.json()['msg']
            if(len(res) < 0):
                break;
            for item in res:
                html = etree.HTML(item)
                title = html.xpath('//img/@alt')[0]
                img_url = html.xpath('//img/@src')[0]
                topic_url = html.xpath('//a[1]/@href')[0]
                topic_id = topic_url.split('/')[-1]
                topic_url = urllib.parse.urljoin(url, topic_url)
                desc = html.xpath('//p/text()')
                if desc is not None and len(desc) == 1:
                    desc = desc[0]
                else:
                    desc = ''

                title = self.sql_filter(title, 200)
                img_url = self.sql_filter(img_url, 200)
                topic_url = self.sql_filter(topic_url, 200)
                desc = self.sql_filter(desc, 200)

                output.append({'title': title, 'img_url': img_url, "topic_url": topic_url, "desc": desc, "topic_id": topic_id,'parent_id': parent_id})
            print(output)
            self.save_child_topic(output)




    def save_child_topic(self,data):
        for item in data:
            import time
            # 格式化成2016-03-20 11:45:39形式
            now = time.strftime("%Y-%m-%d %H:%M:%S", time.localtime())
            # 插入数据
            sql = "insert  ignore into topic(`title`,`topic_id`,`img_url`,`parent_id`,`desc`,`topic_url`,`level`,`create_time`) values('{}','{}','{}','{}','{}','{}','{}','{}')".format(
                item['title'], item['topic_id'], item['img_url'], item['parent_id'], item['desc'], item['topic_url'], 1,
                now)
            #print(sql)
            reCount = self.cur.execute(sql)
            self.conn.commit()


    def __del__(self):
        self.cur.close()
        self.conn.close()