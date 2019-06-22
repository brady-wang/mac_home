# -*- coding: utf-8 -*-
import scrapy

from scrapy.downloadermiddlewares.robotstxt import  RobotsTxtMiddleware
from scrapy.downloadermiddlewares.httpauth import HttpAuthMiddleware
from scrapy.downloadermiddlewares.downloadtimeout import DownloadTimeoutMiddleware
from scrapy.downloadermiddlewares.defaultheaders import DefaultHeadersMiddleware
from scrapy.downloadermiddlewares.useragent import UserAgentMiddleware
from scrapy.downloadermiddlewares.retry import RetryMiddleware
from scrapy.downloadermiddlewares.redirect import MetaRefreshMiddleware
from scrapy.downloadermiddlewares.httpcompression import HttpCompressionMiddleware
from scrapy.downloadermiddlewares.redirect import RedirectMiddleware
from scrapy.downloadermiddlewares.cookies import CookiesMiddleware
from scrapy.downloadermiddlewares.stats import DownloaderStats

class FirstSpider(scrapy.Spider):
    name = "first"
    allowed_domains = ["test.yeves.cn"]
    start_urls = ['http://test.yeves.cn/test_header.php']

    def parse(self, response):
        print(response.text)

