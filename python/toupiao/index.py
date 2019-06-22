# *_*coding:utf-8 *_*
import requests
import random,string
headers = {
    'Accept': '*/*',
    'Accept-Encoding': 'gzip',
    'Accept-Language': 'zh-CN',
    'Connection': 'keep-alive',
    'Content-Length': '16',
    'Content-Type': 'application/x-www-form-urlencoded',
    'Host': 'www.dingnf.com',
    'Origin': 'http://www.dingnf.com',
    'Referer': 'http://www.dingnf.com/active/wxws_s',
    'User-Agent': 'Mozilla/4.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.3029.110 Safari/537.36',
    'X-Requested-With': 'XMLHttpRequest'

}



def get_str():
    src_digits = string.digits  # string_数字
    src_uppercase = string.ascii_uppercase  # string_大写字母
    src_lowercase = string.ascii_lowercase  # string_小写字母
    count = 28
    for i in range(count):
        # 随机生成数字、大写字母、小写字母的组成个数（可根据实际需要进行更改）
        digits_num = random.randint(1, 6)
        uppercase_num = random.randint(1, 8 - digits_num - 1)
        lowercase_num = 8 - (digits_num + uppercase_num)

        # 生成字符串
        password = random.sample(src_digits, digits_num) + random.sample(src_uppercase, uppercase_num) + random.sample(
            src_lowercase, lowercase_num)

        # 打乱字符串
        random.shuffle(password)

        # 列表转字符串
        new_password = ''.join(password)
    return new_password

url = "http://m124111.nofollow.axfree.mvote.cn/op.php"
str = "oNrjcvkegFlPi3wdabLTeda0sN8Y"

print(str)
wxparam = str + "|914334da-d7fb-6954-e396-22fb0de77fbe|9dcc0e1253fe13db6d0bd7dfac82fcd0|1537407585|vote";
id = 4315252
data = {'action':'dovote','guid':'914334da-d7fb-6954-e396-22fb0de77fbe','ops':'2423','wxparam':wxparam}
res = requests.post(url,data=data,headers=headers)

print(res.text)