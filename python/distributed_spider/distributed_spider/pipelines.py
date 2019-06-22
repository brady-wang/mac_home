# -*- coding: utf-8 -*-

# Define your item pipelines here
#
# Don't forget to add your pipeline to the ITEM_PIPELINES setting
# See: http://doc.scrapy.org/en/latest/topics/item-pipeline.html
import pymysql


class DistributedSpiderPipeline(object):
    def __init__(self):
        self.conn = pymysql.connect(host='192.168.33.10', user='root', passwd='root', db='spider', charset='utf8')
        self.cur = self.conn.cursor()

    def open_spider(self, spider):
        print('spider start')

    def process_item(self, item, spider):
        import time
        # 格式化成2016-03-20 11:45:39形式
        now = time.strftime("%Y-%m-%d %H:%M:%S", time.localtime())
        # 插入数据
        sql = "insert ignore  into pexels(img_name,img_url,create_time) values('{}','{}','{}')".format(item['img_name'], item['img_url'],now)
        #print(sql)
        reCount = self.cur.execute(sql)
        self.conn.commit()
        return item

    def close_spider(self, spider):
        self.cur.close()
        self.conn.close()
