import random


class Die:
    """
    一个骰子类
    """

    def __init__(self, num_sides=6):
        self.num_sides = num_sides

    def roll(self):
        return random.randint(1, self.num_sides)


import pygal

die_1 = Die()
die_2 = Die()

result_list = []
for roll_num in range(5000):
    # 两个骰子的点数和
    result = die_1.roll() + die_2.roll()
    result_list.append(result)

frequencies = []
# 能掷出的最大数
max_result = die_1.num_sides + die_2.num_sides

for value in range(2, max_result + 1):
    frequency = result_list.count(value)
    frequencies.append(frequency)

# 可视化
hist = pygal.Bar()
hist.title = 'Results of rolling two D6 dice 5000 times'
hist.x_labels = [x for x in range(2, max_result + 1)]
hist.x_title = 'Result'
hist.y_title = 'Frequency of Result'
# 添加数据
hist.add('two D6', frequencies)
# 格式必须是svg
hist.render_to_file('2_die_visual.svg')

