# -*- coding: utf-8 -*-
import scrapy

import redis
url = "https://www.pexels.com/photo/aerial-photo-of-high-rise-building-754587/"
url1 = "https://www.pexels.com/photo/waterfalls-688559/"
rds = redis.StrictRedis(host='192.168.33.10',port='6379')
# res = rds.rpush('yeves:urls',url)
# res = rds.rpush('yeves:urls',url1)

