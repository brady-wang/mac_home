# -*- coding: utf-8 -*-
import scrapy
from scrapy.linkextractors import LinkExtractor
from scrapy.spiders import CrawlSpider, Rule

from ..items import JobsSpiderItem


class ZhilianSpiderSpider(CrawlSpider):
    name = 'zhilian_spider'
    allowed_domains = ['www.zhaopin.com']
    start_urls = ['https://jobs.zhaopin.com/CC528322337J00108903402.htm']

    rules = (
        Rule(LinkExtractor(allow=r'zhaopin'), callback='parse_item', follow=False),
    )

    def parse_item(self, response):
        i = JobsSpiderItem()

        job_name = response.xpath("//div[@class='inner-left fl'][1]/h1/text()").extract()
        print(job_name);
        job_area = response.xpath("//div[@class='new-info']//div[@class='info-three l']/span[1]/a/text()").extract_first()
        i['job_price'] = response.xpath("//div[@class='new-info']//div[@class='l info-money']/strong[1]/text()").extract_first()
        i['job_company'] = response.xpath("//div[@class='new-info']//div[@class='company l']/a/text()").extract_first()
        i['job_url'] = response.url
        i['flag'] = '智联招聘'
        if job_name is not None and job_area is not None:
            i['job_name'] = job_name
            i['job_area'] = job_area
            print("crawl page ", response.url, i['job_company'])
            #yield (i)
