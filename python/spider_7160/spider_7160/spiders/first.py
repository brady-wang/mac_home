# -*- coding: utf-8 -*-
import scrapy ,re
from scrapy.linkextractors import LinkExtractor
from scrapy.spiders import CrawlSpider, Rule
from ..items import Spider7160Item

class FirstSpider(CrawlSpider):
    name = 'first'
    allowed_domains = ['www.7160.com']
    start_urls = ['http://www.7160.com/']

    rules = (
        Rule(LinkExtractor(allow=r'7160'), callback='parse_item', follow=True),
    )

    def validateTitle(seft,title):
        rstr = r"[\/\\\:\*\?\"\<\>\|]"  # '/ \ : * ? " < > |'
        new_title = re.sub(rstr, "_", title)  # 替换为下划线
        return new_title

    def parse_item(self, response):
        item = Spider7160Item()
        title = response.xpath("//div[@class='picmainer']/h1/text()").extract()
        title = ",".join(title)
        url = response.xpath("//div[@class='picsbox picsboxcenter']/p/a/img/@src").extract()
        url = ','.join(url)
        if len(title) >0 and len(url) > 0 :
            print("crawl :successful",title)
            title = self.validateTitle(title)
            item['title'] = title
            item['url'] = url
            return  item
