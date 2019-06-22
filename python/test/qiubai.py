import requests
def test_links(url):
    print(url)
    response = requests.get(url)
    from lxml import etree
    html = etree.HTML(response.content)
    links = html.xpath('normalize-space(//*[@id="content-left"]/ul/li[1]/span/text())')
    print(links)
    if len(links) < 1 :
        pass
    else:
        host = 'https://www.qiushibaike.com/text/'
        new_url = host+"page/"+str( int(links) + 1);
        test_links(new_url)
try:
    test_links("https://www.qiushibaike.com/text/")
except Exception as e:
    print(str(e))
