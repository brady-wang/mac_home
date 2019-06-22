# -*- coding: utf-8 -*-
import scrapy
from scrapy.linkextractors import LinkExtractor
from scrapy.spiders import CrawlSpider, Rule

from scrapy_redis.spiders import RedisCrawlSpider
from scrapy_redis.pipelines import RedisPipeline
class YevesSpider(RedisCrawlSpider):
    name = 'yeves'
    redis_key = "yeves:urls"
    # allowed_domains = ["angelimg.com"]
    # start_urls = ['http://angelimg.com/']
    custom_settings = {
        'REDIS_URL': 'http://192.168.33.10:6379',
        'SCHEDULER': 'scrapy_redis.scheduler.Scheduler',
        'DUPEFILTER_CLASS': 'scrapy_redis.dupefilter.RFPDupeFilter',
        'ITEM_PIPELINES' : {
           'distributed_spider.pipelines.DistributedSpiderPipeline': 400,
        },
        'REDIS_ITEM_KEY':'yeves:items',
        'REDIS_ITEMS_SERIALIZER':'json.dumps',
        #'DOWNLOAD_DELAY':0.2,
        'USER_AGENT' :'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36'
    }

    rules = (
        Rule(LinkExtractor(allow=r'pexels'), callback='parse_item', follow=True),
    )

    def parse_item(self, response):
        i = {}
        print(response.url)
        img_url = response.xpath("//img[@class='image-section__image js-photo-zoom']/@src").extract_first()
        if img_url is not None:
            if "?" in img_url:
                tmp_url = img_url.split("?")[0]
            else:
                tmp_url = img_url
            i['img_url'] = tmp_url

            img_name = tmp_url.split('/')[-1]
            if "." not in img_name:
                img_name = img_name + '.png'
            i['img_name'] = img_name
            print("crawled image ----",i['img_url']+i['img_name'])
            yield  i
