# -*- coding: utf-8 -*-
import scrapy


class AddTaskSpider(scrapy.Spider):
    name = "add_task"
    allowed_domains = ["task.com"]
    start_urls = ['http://test.yeves.cn/test_header.php']

    ITEM_PIPELINES = {
       'distributed_spider.pipelines.DistributedSpiderPipeline': 300,
    }
    custom_settings = {
        'ITEM_PIPELINES' : {
            'task.pipelines.TaskPipeline': 300,
        }
    }

    def parse(self, response):
        item = {}
        item['task_url'] = "https://www.pexels.com/photo/vehicle-on-road-along-green-grass-during-night-714023/"
        yield item
