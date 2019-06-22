# -*- coding: utf-8 -*-
import scrapy


class FirstSpider(scrapy.Spider):
    name = 'first'
    allowed_domains = ['yeves.cn']
    start_urls = ['http://yeves.cn/']

    def parse(self, response):
        pass
