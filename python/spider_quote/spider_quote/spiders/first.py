# -*- coding: utf-8 -*-
import scrapy
from ..items import SpiderQuoteItem

class FirstSpider(scrapy.Spider):
    name = "first"
    allowed_domains = ["quotes.toscrape.com"]
    start_urls = ['http://quotes.toscrape.com/']
    host = "http://quotes.toscrape.com{}"

    def parse(self, response):
        # with open('index.html', 'wb') as f:
        #     f.write(response.body)

        item  = SpiderQuoteItem()
        title = response.xpath("//div[@class='quote']/span[1]/text()").extract()
        author = response.xpath("//small[@class='author']/text()").extract()
        for i,j in zip(author,title):
            item['author'] = i
            item['title'] = j
            yield item

        #获取下一页 并且进行请求
        # next_links = response.xpath("//li[@class='next']/a[1]/@href").extract()
        # if len(next_links) > 0:
        #     next_link = self.host.format(next_links[0])
        #     yield scrapy.Request(next_link,callback=self.parse)