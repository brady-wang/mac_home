# -*- coding: utf-8 -*-
import scrapy
from scrapy.linkextractors import LinkExtractor
from scrapy.spiders import CrawlSpider, Rule
from ..items import SpiderQuoteItem

class SecondSpider(CrawlSpider):
    name = 'second'
    allowed_domains = ['www.7160.com']
    start_urls = ['http://www.7160.com']

    rules = (
        Rule(LinkExtractor(allow=r'7160'), callback='parse_item', follow=True),
    )

    def parse_item(self, response):
        #i['domain_id'] = response.xpath('//input[@id="sid"]/@value').extract()
        #i['name'] = response.xpath('//div[@id="name"]').extract()
        #i['description'] = response.xpath('//div[@id="description"]').extract()
        item = SpiderQuoteItem()
        title = response.xpath("//div[@class='picmainer']/h1/text()").extract()
        title = ",".join(title)
        print(title)
        url = response.xpath("//div[@class='picsbox picsboxcenter']/p/a/img/@src").extract()
        url = ','.join(url)
        print(url)




