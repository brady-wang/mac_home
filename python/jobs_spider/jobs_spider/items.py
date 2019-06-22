# -*- coding: utf-8 -*-

# Define here the models for your scraped items
#
# See documentation in:
# http://doc.scrapy.org/en/latest/topics/items.html

import scrapy


class JobsSpiderItem(scrapy.Item):
    # define the fields for your item here like:
    # name = scrapy.Field()
    job_name = scrapy.Field()
    job_area = scrapy.Field()
    job_price = scrapy.Field()
    job_company = scrapy.Field()
    job_url = scrapy.Field()
    flag = scrapy.Field()

