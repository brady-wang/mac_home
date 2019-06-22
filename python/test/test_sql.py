#coding=utf-8
from sqlalchemy import create_engine
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy import Column, String, Integer
from sqlalchemy.orm import sessionmaker

engine = create_engine('mysql+mysqldb://root:root@192.168.33.30:3306/python',echo=True)
Base = declarative_base()

class User(Base):

    __tablename__ = 'user'

    id = Column(Integer, primary_key=True)
    name = Column(String(64), nullable=False, index=True)


    def __repr__(self):
        return '%s(%r)' % (self.__class__.__name__, self.username)
#Base.metadata.create_all(engine)
# 创建session对象:
DBSession = sessionmaker(bind=engine)
session = DBSession()
# 创建新User对象:
new_user = User(id='1', name='Bob')
# 添加到session:
session.add(new_user)
# 提交即保存到数据库:
session.commit()
# 关闭session:
session.close()

