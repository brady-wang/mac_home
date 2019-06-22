<<<<<<< HEAD
#coding=utf-8
import random
import urllib.request

import pymysql
from bs4 import BeautifulSoup
from urllib import error
import re
import os

totalSize = 0
fileNum = 0
dirNum = 0
import os
#ls = ['zhenrenxiu','meinv',"lianglichemo",'rentiyishu','xiaohua']
category = 'meinv'
start = 1030
end = 100000
max_files = "50000";




def mkdir(path):
    path = path.strip()
    path = path.rstrip("\\")
    isExists = os.path.exists(path)
    # 判断结果
    if not isExists:
        os.makedirs(path)
        return True
    else:
        return False



def validateTitle(title):
    rstr = r"[\/\\\:\*\?\"\<\>\|]"  # '/ \ : * ? " < > |'
    new_title = re.sub(rstr, "_", title)  # 替换为下划线
    return new_title
def get_total_files(mkpath):
    return sum([len(x) for _, _, x in os.walk(os.path.dirname(mkpath))])

def save_img(title,src,):
    file_name = validateTitle(title) + ".jpg"

    #改为保存 到数据库
    conn = pymysql.connect(host='192.168.33.30', port=3306, user='root', passwd='root', db='test', charset='utf8')
    cursor = conn.cursor()
    sql = "INSERT INTO imgs(title,url) VALUES ('%s', '%s')" % (str(file_name),str(src))
    cursor.execute(sql)
    conn.commit()
    cursor.close()
    conn.close()
    print("保存成功")




def get_total_page(url_origin):
    page_obj = urllib.request.urlopen(url_origin)
    page_soup = BeautifulSoup(page_obj, 'lxml')
    total_page_obj = page_soup.find(text=re.compile('共')).string
    pattern = re.compile(r'\d+')
    match = pattern.search(total_page_obj)

    if match == None:
        total_page = 0;
    else:
        total_page = match.group();
    return total_page

for j in range(start,end):
   url_origin = "http://www.7160.com/"+category+"/"+str(j)
   try:
      total_page = get_total_page(str(url_origin))

      for i in range(1,int(total_page)+1):
         if i == 1 :
            url = url_origin+"/index.html"
         else:
            url = url_origin+"/index_"+str(i)+".html"
         request = urllib.request.Request(url)

         res = urllib.request.urlopen(request)
         soup = BeautifulSoup(res,'lxml')
         title_obj = soup.find(attrs={"class":"picmainer"})
         if title_obj is not None:
           print(url)
           title = title_obj.h1.string
           content = soup.find('img')
           src = content.get("src")
           save_img(title,src)

   except Exception  as e:
=======
#coding=utf-8
import random
import urllib.request

import pymysql
from bs4 import BeautifulSoup
from urllib import error
import re
import os

totalSize = 0
fileNum = 0
dirNum = 0
import os
#ls = ['zhenrenxiu','meinv',"lianglichemo",'rentiyishu','xiaohua']
category = 'meinv'
start = 1030
end = 100000
max_files = "50000";




def mkdir(path):
    path = path.strip()
    path = path.rstrip("\\")
    isExists = os.path.exists(path)
    # 判断结果
    if not isExists:
        os.makedirs(path)
        return True
    else:
        return False



def validateTitle(title):
    rstr = r"[\/\\\:\*\?\"\<\>\|]"  # '/ \ : * ? " < > |'
    new_title = re.sub(rstr, "_", title)  # 替换为下划线
    return new_title
def get_total_files(mkpath):
    return sum([len(x) for _, _, x in os.walk(os.path.dirname(mkpath))])

def save_img(title,src,):
    file_name = validateTitle(title) + ".jpg"

    #改为保存 到数据库
    conn = pymysql.connect(host='192.168.33.30', port=3306, user='root', passwd='root', db='test', charset='utf8')
    cursor = conn.cursor()
    sql = "INSERT INTO imgs(title,url) VALUES ('%s', '%s')" % (str(file_name),str(src))
    cursor.execute(sql)
    conn.commit()
    cursor.close()
    conn.close()
    print("保存成功")




def get_total_page(url_origin):
    page_obj = urllib.request.urlopen(url_origin)
    page_soup = BeautifulSoup(page_obj, 'lxml')
    total_page_obj = page_soup.find(text=re.compile('共')).string
    pattern = re.compile(r'\d+')
    match = pattern.search(total_page_obj)

    if match == None:
        total_page = 0;
    else:
        total_page = match.group();
    return total_page

for j in range(start,end):
   url_origin = "http://www.7160.com/"+category+"/"+str(j)
   try:
      total_page = get_total_page(str(url_origin))

      for i in range(1,int(total_page)+1):
         if i == 1 :
            url = url_origin+"/index.html"
         else:
            url = url_origin+"/index_"+str(i)+".html"
         request = urllib.request.Request(url)

         res = urllib.request.urlopen(request)
         soup = BeautifulSoup(res,'lxml')
         title_obj = soup.find(attrs={"class":"picmainer"})
         if title_obj is not None:
           print(url)
           title = title_obj.h1.string
           content = soup.find('img')
           src = content.get("src")
           save_img(title,src)

   except Exception  as e:
>>>>>>> 069d225b498d4ce3c199324fad03bb6a2ee1d884
            print("异常"+str(j)+str(e))