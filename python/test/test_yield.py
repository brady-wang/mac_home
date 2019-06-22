# *_*coding:utf-8 *_*

def test(n):
    print("start")
    for i in range(1,n+1):
        print('yield start')
        yield i*i
        print("=======i*i========",i*i)
        print("yield end")
item = test(2)
item.__next__()
item.__next__()


a = [1,2,3,4]
b = ['a','b','c','d']
print(zip(a,b))
for i,j in zip(a,b):
    print (i,j)