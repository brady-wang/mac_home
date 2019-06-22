# *_*coding:utf-8 *_*
#对于一个长度为N的数组，我们需要排序 N-1 轮，每 i 轮 要比较 N-i 次。对此我们可以用双重循环语句，外层循环控制循环轮次，内层循环控制每轮的比较次数
def bubble_sort(sort_list,sort='asc'):
    if len(sort_list) <= 0:
        return []
    count = len(sort_list)
    sorted_list = sort_list
    for i in range(0,count-1):
        print('第%d趟排序:' % (i + 1))
        for j in range(0,count-i-1):
            if sorted_list[j] > sorted_list[j + 1]  :
                sorted_list[j], sorted_list[j + 1] = sorted_list[j + 1], sorted_list[j]
            print(sorted_list)



list = [3,4,2,6,5,9]
bubble_sort(list)
