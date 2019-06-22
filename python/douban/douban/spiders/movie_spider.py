# -*- coding: utf-8 -*-
import scrapy


class MovieSpiderSpider(scrapy.Spider):
    name = "movie_spider"
    allowed_domains = ["movie.douban.com"]
    start_urls = ['https://movie.douban.com/j/search_subjects?type=tv&tag=%E7%83%AD%E9%97%A8&sort=recommend&page_limit=20&page_start=40']

    def parse(self, response):
        pass
