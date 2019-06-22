# -*- coding: utf-8 -*-

# Define your item pipelines here
#
# Don't forget to add your pipeline to the ITEM_PIPELINES setting
# See: http://doc.scrapy.org/en/latest/topics/item-pipeline.html
import sqlite3
import time

# 格式化成2016-03-20 11:45:39形式
now = time.strftime("%Y-%m-%d %H:%M:%S", time.localtime())

class MyPipeline(object):

    def open_spider(self,spider):
        self.conn = sqlite3.connect('yeves.sqlite')
        self.cursor = self.conn.cursor()


    def process_item(self, item, spider):
        print(spider.name,"爬虫 入库 ****************************")
        sql = "insert OR IGNORE into  article(title,desc,create_time) values('{}','{}','{}')".format(item['title'], item['desc'], now)
        self.cursor.execute(sql)
        self.conn.commit()
        return item

    def spider_close(self,spider):
        self.conn.close()