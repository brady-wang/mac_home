#coding=utf-8
import requests
from lxml import etree
from sqlalchemy import create_engine
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy import Column, String, Integer
from sqlalchemy.orm import sessionmaker

def requests_view(response):
    import webbrowser
    requests_url = response.url
    base_url = '<head><base href="%s">' %(requests_url)
    base_url = base_url.encode('utf-8')
    content = response.content.replace(b"<head>",base_url)
    tem_html = open('tmp.html','wb')
    tem_html.write(content)
    tem_html.close()
    webbrowser.open_new_tab("tmp.html")

host  = "http://sz.ganji.com/fang1/o{}"
max = 10

engine = create_engine('mysql+mysqldb://root:root@192.168.33.30:3306/python?charset=utf8',echo=True,encoding='utf8')
Base = declarative_base()

class Ganji(Base):

    __tablename__ = 'ganji'

    id = Column(Integer, primary_key=True)
    title = Column(String(100))
    money = Column(String(100))
    info = Column(String(100))
    create_time  = Column(String(30))


    def __repr__(self):
        return '%s(%r)' % (self.__class__.__name__, self.username)
# Base.metadata.create_all(engine)
# exit()
def save_data(title,money,info):
    # 创建session对象:
    DBSession = sessionmaker(bind=engine)
    session = DBSession()
    # 创建新User对象:
    import datetime
    create_time = datetime.datetime.now()
    new_ganji = Ganji( title=title,money=money,info=info,create_time="test")
    # 添加到session:
    session.add(new_ganji)
    # 提交即保存到数据库:
    session.commit()
    # 关闭session:
    session.close()

def get_html(url):
    headers = {'Referer':'http://callback.ganji.com/firewall/valid/1902788594.do?namespace=ganji_zufang_list_pc&url=http%3A%2F%2Fsz.ganji.com%2Ffang1%2F','User-Agent':'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.79 Safari/537.36'}
    response = requests.get(url,headers=headers)
    if response.status_code == 200:
        #requests_view(response)
        #strip
        html = etree.HTML(response.content.decode('utf-8'))
        items = html.xpath(".//div[@class='f-main-list']/div/div")
        print(len(items))
        for i in items:
            title = i.xpath(".//dd[@class='dd-item title']/a/text()")
            money = i.xpath(".//dd[@class='dd-item info']/div[@class='price']/span/text()")
            info = i.xpath(".//dd[@class='dd-item size']/span/text()")
            print(info)
            title = ' '.join(title)
            money = ' '.join(money)
            info = ' '.join(info)
            if len(title) > 0 and len(money) >0 and len(info) > 0 :
                save_data(title,money,info)
            else:
                print("未获取到数据");

    else:
        print("请求失败")
try:
    for i in range(1,max):
        url = host.format(i)
        print(url)
        get_html(url)
except Exception as e:
    print(str(e))


