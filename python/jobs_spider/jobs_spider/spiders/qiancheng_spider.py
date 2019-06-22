# -*- coding: utf-8 -*-
import scrapy
from scrapy.linkextractors import LinkExtractor
from scrapy.spiders import CrawlSpider, Rule

from ..items import JobsSpiderItem
class QianchengSpiderSpider(CrawlSpider):
    name = 'qiancheng_spider'
    allowed_domains = ['51job.com']
    start_urls = ['https://search.51job.com/list/040000,000000,0000,00,9,99,python,2,1.html?lang=c&stype=&postchannel=0000&workyear=99&cotype=99&degreefrom=99&jobterm=99&companysize=99&providesalary=99&lonlat=0%2C0&radius=-1&ord_field=0&confirmdate=9&fromType=&dibiaoid=0&address=&line=&specialarea=00&from=&welfare=']

    rules = (
        Rule(LinkExtractor(allow=r'https://jobs.51job.com/shenzhen.*/.*\.html.*'), callback='parse_item', follow=True),
    )

    def parse_item(self, response):
        i = JobsSpiderItem()

        job_name = response.xpath("//div[@class='cn']/h1/@title").extract_first()
        job_area = response.xpath("//div[@class='cn']/p[@class='msg ltype']/@title").extract_first()
        i['job_price'] = response.xpath("//div[@class='cn']/strong/text()").extract_first()
        i['job_company'] =   response.xpath("//div[@class='cn']/p[@class='cname']/a[1]/@title").extract_first()
        i['job_url'] = response.url
        i['flag'] = '前程无忧'
        if job_name is not None and job_area is not None:
            i['job_name'] = job_name
            i['job_area'] = job_area
            print("crawl page ",response.url,i['job_company'])
            yield(i)
