# -*- coding: utf-8 -*-
import hashlib
import random
from hashlib import md5

import scrapy


class SecondSpider(scrapy.Spider):
    name = "second_spider"
    allowed_domains = ["lab.scrapyd.cn"]
    start_urls = [
        'http://lab.scrapyd.cn/page/1/',
        #'http://lab.scrapyd.cn/page/2/'
    ]

    def parse(self, response):
        response.body.decode()
        texts = response.xpath("//div[@class='quote post']/span[@class='text']/text()").extract()
        authors = response.xpath("//div[@class='quote post']/span[2]/small/text()").extract()
        tags = response.xpath("//div[@class='quote post']/div[@class='tags']//text()").extract()
        print("crawl page "+response.url)
        for i,j,k in zip(authors,texts,tags):
            file_name = '%s语录.txt' % (i)
            f = open(file_name, "a+",encoding='utf8')
            f.write("author: %s" %(i))
            f.write('\n')
            f.write("语录: %s" % j)
            f.close()

            # 获取下一页
            next_links = response.xpath("//ol[@class='page-navigator']//li//a/@href").extract()
            if len(next_links) > 0 :
                for link in next_links :
                    yield scrapy.Request(link,callback=self.parse)