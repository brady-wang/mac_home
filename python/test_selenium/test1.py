# *_*coding:utf-8 *_*
import time
from selenium import webdriver

browser = webdriver.Chrome()
browser.get('https://www.baidu.com/')
print(browser.page_source)
browser.close()