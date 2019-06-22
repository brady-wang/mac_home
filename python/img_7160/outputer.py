# *_*coding:utf-8 *_*
import pymysql
class Outputer(object):
    def __init__(self):
        self.conn  = pymysql.connect(host='192.168.33.10', user='root', passwd='root', db='spider',charset='utf8')
        self.cur = self.conn.cursor()

    def collect_data(self, page_url,data):
        if page_url is None or data is None or len(data) <= 0 :
            return None
        if  data['url'] is None or data['title'] is None:
            return None

        if len(data['title']) <= 0 or len(data['url']) <= 0:
            return None

        self.save_to_mysql(data)

    def save_to_mysql(self,data):
        import time
        # 格式化成2016-03-20 11:45:39形式
        now = time.strftime("%Y-%m-%d %H:%M:%S", time.localtime())
        # 插入数据
        sql = "insert  into imgs_7160(title,url,create_time) values('{}','{}','{}')".format(data['title'],data['url'],now)
        #print(sql)
        reCount = self.cur.execute(sql)
        self.conn.commit()


    def __del__(self):
        self.cur.close()
        self.conn.close()







