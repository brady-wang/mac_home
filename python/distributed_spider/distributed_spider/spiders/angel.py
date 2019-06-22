# -*- coding: utf-8 -*-
import scrapy
import time

from scrapy_redis.spiders import RedisSpider

class AngelSpider(RedisSpider):
    name = "angel"
    redis_key = "angel:urls"
    # allowed_domains = ["angelimg.com"]
    # start_urls = ['http://angelimg.com/']
    custom_settings = {
        'REDIS_URL' : 'http://192.168.33.10:6379',
        'SCHEDULER' : 'scrapy_redis.scheduler.Scheduler',
        'DUPEFILTER_CLASS' : 'scrapy_redis.dupefilter.RFPDupeFilter'
    }

    def parse(self, response):
        time.sleep(1)
        print(response.text)
