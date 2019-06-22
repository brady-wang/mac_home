# *_*coding:utf-8 *_*
import pymysql
from lxml import etree

import requests

class GetParentTopic(object):
    def __init__(self):
        self.conn = pymysql.connect(host='192.168.33.10', user='root', passwd='root', db='spider', charset='utf8')
        self.cur = self.conn.cursor()

    def get_parent_data(self):
        headers = {
            'User-Agent': 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36'
        }
        url = 'https://www.zhihu.com/topics'

        response = requests.get(url, headers=headers)
        res = response.text

        html = etree.HTML(res)
        ul = html.xpath("//ul[@class='zm-topic-cat-main clearfix']/li");

        parent_topic = {}

        for li in ul:
            title = li.xpath('./a/text()')[0];
            topic_id = li.xpath('./@data-id')[0];
            parent_topic[topic_id] = title
            import time

            # 格式化成2016-03-20 11:45:39形式
            now = time.strftime("%Y-%m-%d %H:%M:%S", time.localtime())
            # 插入数据
            sql = "insert ignore   into topic(`title`,`topic_id`,`create_time`) values('{}','{}','{}')".format(title,
                                                                                                         topic_id, now)
            #print(sql)
            reCount = self.cur.execute(sql)
            self.conn.commit()

        self.cur.close()
        self.conn.close()
        return parent_topic