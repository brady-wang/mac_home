# -*- coding: utf-8 -*-
import random
import re

import pymysql
import scrapy
from ..items import  ZufangItem

class GanjiSpider(scrapy.Spider):
    name = "ganji"
    allowed_domains = ["sz.ganji.com/"]
    i = random.randint(1,20)
    start_urls = ['http://sz.ganji.com/fang1/o'+str(i)+'/']
    print(start_urls)

    def __init__(self):
        self.conn  = pymysql.connect(host='192.168.33.10', user='root', passwd='root', db='spider',charset='utf8')
        self.cur = self.conn.cursor()

    def parse(self, response):
        money_list = response.xpath("//div[@class='price']/span[@class='num']/text()").extract()
        title_list = response.xpath("//dd[@class='dd-item title']/a[1]/text()").extract()

        zf = ZufangItem()
        for title,money in zip(title_list,money_list):
            #self.save_to_mysql(title,money)
            zf['title'] = title
            zf['money'] = money
            yield zf
        # self.cur.close()
        # self.conn.close()

    def validateTitle(self,title):
        rstr = r"[\/\\\:\*\?\"\<\>\|]"  # '/ \ : * ? " < > |'
        new_title = re.sub(rstr, "_", title)  # 替换为下划线
        return new_title

    def save_to_mysql(self,title,money):
        import time
        # 格式化成2016-03-20 11:45:39形式
        now = time.strftime("%Y-%m-%d %H:%M:%S", time.localtime())
        # 插入数据
        title = self.validateTitle(title)
        sql = "insert  into ganji(title,money,create_time) values('{}','{}','{}')".format(title,money,now)
        print(sql)
        reCount = self.cur.execute(sql)
        self.conn.commit()



