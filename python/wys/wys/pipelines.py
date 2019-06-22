# -*- coding: utf-8 -*-

# Define your item pipelines here
#
# Don't forget to add your pipeline to the ITEM_PIPELINES setting
# See: http://doc.scrapy.org/en/latest/topics/item-pipeline.html


class WysPipeline(object):

    def __init__(self):
        print('come into pipeline')

    def open_spider(self,spider):
        print("spider open",spider.name)

    def process_item(self, item, spider):
        print(item)
        return item

    def close_spider(self,spider):
        print('close spider ',spider.name)