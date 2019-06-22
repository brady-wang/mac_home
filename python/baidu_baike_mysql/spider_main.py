# *_*coding:utf-8 *_*
from baidu_baike import downloader, outputer, url_manager, parser


class SpiderMain(object):
    #初始化
    def __init__(self):
        self.downloader = downloader.Downloader()
        self.url_manager = url_manager.UrlManager()
        self.outputer = outputer.Outputer()
        self.parser = parser.Parser()

    def crawl(self, root_url):
        count = 1
        max_count = 100000
        self.url_manager.add_new_url(root_url)
        while self.url_manager.has_new_url():
            try:
                new_url = self.url_manager.get_new_url()
                print("crawl %d : %s"%(count,new_url))
                content = self.downloader.Download(new_url)

                new_urls,new_data = self.parser.parse(content,new_url)
                self.url_manager.add_new_urls(new_urls)
                self.outputer.collect_data(new_url,new_data)

                if count >= max_count:
                    break;
                count = count + 1
            except Exception as e:
                print("错误:"+str(e))
        #self.outputer.save_to_sqllite()

if __name__ == "__main__":
    print("crawl start:")
    root_url = "https://baike.baidu.com/item/Python/407313";
    spider =  SpiderMain()
    spider.crawl(root_url)