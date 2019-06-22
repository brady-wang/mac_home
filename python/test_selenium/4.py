from selenium import webdriver
import time

# browser = webdriver.Chrome()
# browser = webdriver.Firefox()

# help(webdriver)

#加载谷歌浏览器驱动，这里我使用的谷歌，你也可以下载firefox或者ie的驱动
#使用什么驱动则模拟该操作的就是这个浏览器，需要注意的是，chromedriver.exe对不同浏览器版本也是有自己版本的
browser = webdriver.Chrome()

#这里通过get请求需要模拟登录的页面
browser.get("http://yeves.cn/login")
#
# print(browser.page_source)

# browser.switch_to_frame("page_source")

#这里通过name选择器获取登录名和密码并把需要set值给放进去
browser.find_element_by_id("username").send_keys("brady")
browser.find_element_by_id("password").send_keys("aa5421010")
#这一步模拟点击登录
browser.find_element_by_id("login").click()


# browser.implicitly_wait(10)
time.sleep(5)


#这一步模拟点击某个a标签连接
hrefs=browser.find_element_by_partial_link_text(u"新建文章")
hrefs.click()

time.sleep(5)
#我们可以通过browser.page_source把当前页面的静态资源打印出来看看，然后根据自己需求进行提取有用的资源
print(browser.page_source)
browser.close()