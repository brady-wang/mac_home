# *_*coding:utf-8 *_*
import os
import re
import urllib.request

import pymysql
import math

def validateTitle(title):
    rstr = r"[\/\\\:\*\?\"\<\>\|]"  # '/ \ : * ? " < > |'
    new_title = re.sub(rstr, "_", title)  # 替换为下划线
    return new_title

conn   =  pymysql.connect(host='192.168.33.10', user='root', passwd='root', db='spider',charset='utf8')
cur = conn.cursor()

# 查询
sql = "select count(*) as number from imgs_7160_copy1 limit 1"
cur.execute(sql)  # 返回受影响的行数
res = cur.fetchone()  # 返回数据,返回的是tuple类型

total = res[0]
page_size = 10000
total_page = math.ceil(total / page_size)

for page in range(46,total_page+1):
    path = 'd:\\crawl\\imgs\\img'+str(page)
    if not os.path.exists(path):
        os.makedirs(path)

    start = (page - 1) * page_size
    sql = "select * from imgs_7160_copy1 order by title asc limit {},{}".format(start,page_size)
    cur.execute(sql)  # 返回受影响的行数
    res = cur.fetchall()  # 返回数据,返回的是tuple类型
    if len(res) > 0:
        for item in res:
            print("page:",page,"id:",item[0])
            print(item[2])
            file_name = path +"/"+validateTitle(item[1])+".jpg"
            try:
                urllib.request.urlretrieve(item[2], file_name)
            except Exception as e:
                print(str(e))
cur.close()
conn.close()