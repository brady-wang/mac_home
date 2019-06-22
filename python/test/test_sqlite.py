#coding=utf-8
import sqlite3
import time

# 格式化成2016-03-20 11:45:39形式
now = time.strftime("%Y-%m-%d %H:%M:%S", time.localtime())

conn = sqlite3.connect('test.db')
cursor = conn.cursor()
for i in range(1,101):
    sql = "insert into ganji(title,money,create_time) values('{}','{}','{}')".format('tttt','232',now)
    #sql  ="delete  from ganji where id =  1"
    #sql  ="select *  from ganji where 1"
    cursor.execute(sql)
    print(cursor.rowcount)
conn.commit()

conn.close()