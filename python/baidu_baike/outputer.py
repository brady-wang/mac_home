# *_*coding:utf-8 *_*
class Outputer(object):
    def __init__(self):
        self.data = []

    def collect_data(self, page_url,data):
        if page_url is None or data is None or len(data) <= 0:
            return None
        self.data.append(data)


    def save_to_file(self):
         print(self.data)
         fout = open('output.html','w',encoding='utf-8');
         fout.write('<html><head><meta charset="utf-8"></head>')
         for item in self.data:
             fout.write("<a href = '%s' target='_blank'> %s </a>" % ( item['url'], item['title'] ))
             fout.write("<p> %s </p>" % item['desc'])
         fout.write("</body></html>")



