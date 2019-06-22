# -*- coding: utf-8 -*-
import scrapy


class BasicSpider(scrapy.Spider):
    name = "basic"
    allowed_domains = ["www.zhihu.cocm"]
    start_urls = ['https://www.zhihu.com/people/hubertuswi/followers']

    def parse(self, response):
        print(response.url)
        with open('test.html','wb') as f:
            f.write(response.body)
            f.close()
        name = response.xpath("//h1[@class='ProfileHeader-title']/span[1]/text()").extract_first()
        avators = response.xpath("//div[@class='List-item']//img[@class='Avatar Avatar--large UserLink-avatar']/@src").extract()
        author = response.xpath("//a[@class='UserLink-link']/text()").extract()
        print(author)
