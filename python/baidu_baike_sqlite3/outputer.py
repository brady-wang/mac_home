# *_*coding:utf-8 *_*
class Outputer(object):
    def __init__(self):
        self.data = []

    def collect_data(self, page_url,data):
        if page_url is None or data is None or len(data) <= 0:
            return None
        self.save_to_sqllite(data)


    def save_to_file(self):
         print(self.data)
         fout = open('output.html','w',encoding='utf-8');
         fout.write('<html><head><meta charset="utf-8"></head>')
         for item in self.data:
             fout.write("<a href = '%s' target='_blank'> %s </a>" % ( item['url'], item['title'] ))
             fout.write("<p> %s </p>" % item['desc'])
         fout.write("</body></html>")

    def save_to_sqllite(self, data):
        import sqlite3
        import time
        # 格式化成2016-03-20 11:45:39形式
        now = time.strftime("%Y-%m-%d %H:%M:%S", time.localtime())
        conn = sqlite3.connect('baidu.db')
        cursor = conn.cursor()

        sql = "insert into baike(title,desc,url,create_time) values('{}','{}','{}','{}')".format(data['title'],
                                                                                                 data['desc'],
                                                                                                 data['url'], now)
        cursor.execute(sql)
        conn.commit()
        conn.close()




