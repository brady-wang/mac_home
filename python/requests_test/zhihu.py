# *_*coding:utf-8 *_*
import pymysql
import requests
from lxml import etree

from requests_test.child_topic import GetChildTopic
from requests_test.parent_topic import GetParentTopic

if __name__ == "__main__":
    parent = GetParentTopic()
    res = parent.get_parent_data()
    # child  = GetChildTopic()
    # child.get_child_data(1027,2)
    child = GetChildTopic()
    for i in res:
        print("parent_id:",i)
        child.get_child_data(i,50)