# *_*coding:utf-8 *_*
import time
from selenium import webdriver

browser = webdriver.Chrome()
browser.get("http://yeves.cn/login")

input_first = browser.find_element_by_id("username")
input_first.send_keys("brady")
time.sleep(1)
input_second = browser.find_element_by_xpath('//*[@id="password"]')
input_second.send_keys("aa5421010")
time.sleep(1)
input_button = browser.find_element_by_id("login")


# input_second = browser.find_element_by_css_selector("#q")
# input_third = browser.find_element_by_xpath('//*[@id="q"]')
