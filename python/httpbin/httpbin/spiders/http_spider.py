# -*- coding: utf-8 -*-
import scrapy


class HttpSpiderSpider(scrapy.Spider):
    name = "http_spider"
    allowed_domains = ["httpbin.org"]
    start_urls = ['http://httpbin.org']

    def parse(self, response):
        print(response.text)
