# -*- coding: utf-8 -*-
import scrapy

from ..items import WysItem

class ThreeSpider(scrapy.Spider):
    name = "three_spider"
    allowed_domains = ["lab.scrapyd.cn"]
    start_urls = ['http://lab.scrapyd.cn/page/1/']

    def parse(self, response):
        response.body.decode()
        item = WysItem()
        title = response.xpath("//a[@id='logo']/text()").extract_first()
        item['title'] = title
        yield item
