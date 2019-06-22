# -*- coding: utf-8 -*-

# Define your item pipelines here
#
# Don't forget to add your pipeline to the ITEM_PIPELINES setting
# See: http://doc.scrapy.org/en/latest/topics/item-pipeline.html

import redis
class TaskPipeline(object):
    def __init__(self):
        self.rds = redis.StrictRedis(host='192.168.33.10',port='6379')

    def process_item(self, item, spider):
        self.rds.rpush('yeves:urls',item['task_url'])
        return item
