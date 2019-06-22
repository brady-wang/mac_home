# -*- coding: utf-8 -*-
import scrapy
from scrapy.linkextractors import LinkExtractor
from scrapy.spiders import CrawlSpider, Rule

from ..items import ImagesItem
from scrapy.pipelines.images import ImagesPipeline
from scrapy.pipelines.files import FilesPipeline
from scrapy.pipelines.media import MediaPipeline
class PexSpider(CrawlSpider):
    name = 'pex'
    allowed_domains = ['www.pexels.com']
    start_urls = ['https://www.pexels.com/photo/vehicle-on-road-along-green-grass-during-night-714023/']

    rules = (
        Rule(LinkExtractor(allow=r'/photo/'), callback='parse_item', follow=True),
    )

    def parse_item(self, response):
        from scrapy.mail import MailSender
        mailer = MailSender()
        mailer.send(to=["3414973501@qq.com"], subject="pexels download image", body="Some body", cc=["another@example.com"])

        i = ImagesItem()
        #i['domain_id'] = response.xpath('//input[@id="sid"]/@value').extract()
        #i['name'] = response.xpath('//div[@id="name"]').extract()
        #i['description'] = response.xpath('//div[@id="description"]').extract()
        #i['image_urls'] = response.xpath("//img[@class='image-section__image js-photo-zoom']/@src").extract()
        i['file_urls'] = response.xpath("//img[@class='image-section__image js-photo-zoom']/@src").extract()
        return i
