<<<<<<< HEAD
import scrapy
from ..items import MyItem
class YevesSpider(scrapy.Spider):
    name = 'yeves'
    page=1
    next_page = 1
    host  = 'http://yeves.cn/welcome?page={}'
    start_urls = [host.format(page)]

    def parse(self, response):
        print(response)
        item = MyItem()
        desc_list = response.xpath('//*[@id="article"]/div[@class="container"]/div/article/div/p[2]/text()').extract()
        title_list = response.xpath('//*[@id="article"]/div[@class="container"]/div/article/div/header/h2/a/text()').extract()
        for title, desc in zip(title_list, desc_list):
            item['title'] = title
            item['desc'] = desc
            yield item

        #获取下一页
        self.next_page = self.page + 1;
        if self.next_page <= 7:
            next_url = self.host.format(self.next_page)
=======
import scrapy
from ..items import MyItem
class YevesSpider(scrapy.Spider):
    name = 'yeves'
    page=1
    next_page = 1
    host  = 'http://yeves.cn/welcome?page={}'
    start_urls = [host.format(page)]

    def parse(self, response):
        print(response)
        item = MyItem()
        desc_list = response.xpath('//*[@id="article"]/div[@class="container"]/div/article/div/p[2]/text()').extract()
        title_list = response.xpath('//*[@id="article"]/div[@class="container"]/div/article/div/header/h2/a/text()').extract()
        for title, desc in zip(title_list, desc_list):
            item['title'] = title
            item['desc'] = desc
            yield item

        #获取下一页
        self.next_page = self.page + 1;
        if self.next_page <= 7:
            next_url = self.host.format(self.next_page)
>>>>>>> 069d225b498d4ce3c199324fad03bb6a2ee1d884
            yield scrapy.Request(next_url,callback=self.parse)