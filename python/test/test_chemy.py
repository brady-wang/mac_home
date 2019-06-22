#coding=utf-8
from sqlalchemy import create_engine, Column, Integer, String, DateTime
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.orm import sessionmaker

engine = create_engine('mysql+mysqldb://root:root@192.168.33.30:3306/python',echo=True,encoding="utf-8")
Base = declarative_base()
class Test(Base):
    __tablename__ = 'test'
    id = Column(Integer,primary_key=True)
    username = Column(String(64),nullable=False,index=True,comment="用户名")
    password = Column(String(32),nullable=False)
    email = Column(String(100),nullable=False,index=True)
    create_time = Column(DateTime(),nullable=False)

    def __repr__(self):
        return '%s(%r)' % (self.__class__.__name__,self.username)
#Base.metadata.create_all(engine)
#新增
# 创建session对象:
DBSession = sessionmaker(bind=engine)
session = DBSession()
# 创建新User对象:
new_user = Test( username='Bob',password="tst",email="test@qq.com",create_time = "2017-01-05 14:00:00")
# 添加到session:
#session.add(new_user)

user_list = session.query(Test).order_by(-Test.id)
for user in user_list:
    print(user.id)

# 提交即保存到数据库:
session.commit()
# 关闭session:
session.close()