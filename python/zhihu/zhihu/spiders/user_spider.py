# -*- coding: utf-8 -*-
import scrapy
from scrapy.linkextractors import LinkExtractor
from scrapy.spiders import CrawlSpider, Rule


class UserSpiderSpider(CrawlSpider):
    name = 'user_spider'
    allowed_domains = ['www.zhihu.com']
    start_urls = ['https://www.zhihu.com/people/hubertuswi/followers']

    rules = (
        Rule(LinkExtractor(allow=r'zhihu/'), callback='parse_item', follow=False),
    )

    def parse_item(self, response):
        i = {}
        print(response.url)
        #i['domain_id'] = response.xpath('//input[@id="sid"]/@value').extract()
        #i['name'] = response.xpath('//div[@id="name"]').extract()
        #i['description'] = response.xpath('//div[@id="description"]').extract()
        return i
