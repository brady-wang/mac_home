# -*- coding: utf-8 -*-
import scrapy


class SpbeenSpider(scrapy.Spider):
    name = "spbeen"
    allowed_domains = ["www.spbeen.com"]
    start_urls = ['http://www.spbeen.com/tool/request_info/']

    def start_requests(self):
        for i in range(0,10):
            yield scrapy.Request(self.start_urls[0],dont_filter=True)

    def parse(self, response):
        info = {}
        info['user_agent'] = response.xpath("normalize-space(//div[@class='ui red segment']/div[@class='container']/text())").extract_first()
        info['ip'] = response.xpath("normalize-space(//div[@class='ui blue segment']/div[@class='container']/text())").extract_first()
        info['lan'] = response.xpath("normalize-space(/html/body/div[2]/div[3]/div/div[5]/div[2]/text())").extract_first()
        info['encoding'] = response.xpath("normalize-space(/html/body/div[2]/div[3]/div/div[6]/div[2]/text())").extract_first()
        info['url'] = response.url

        print(info)