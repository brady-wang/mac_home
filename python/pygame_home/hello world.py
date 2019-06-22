# *_*coding:utf-8 *_*
import pygame
from pygame.locals import *
from sys import exit

background_img = 'images/bk.jpg'
mouse_img = 'images/mouse.png'

#初始化pygame 为使用硬件做准备
pygame.init()

#创建一个窗口
width = 640
height = 480
screen = pygame.display.set_mode((width, height), 0, 32)
#设置窗口标题
pygame.display.set_caption("hello world")

#加载并转换图像
background = pygame.image.load(background_img).convert()
#mouse_cursor = pygame.image.load(mouse_img).convert_alpha()
mouse_cursor = pygame.image.load(mouse_img).convert()

while True:
    for event in pygame.event.get():
        if event.type == QUIT: #接收到退出事件后退出程序
            exit()
    screen.blit(background, (0, 0))
    x,y = pygame.mouse.get_pos() #获取鼠标位置
    #计算光标左上角的位置
    x -= mouse_cursor.get_width()/2
    y -= mouse_cursor.get_height()/2
    #画上光标
    screen.blit(mouse_cursor, (x, y))

    #刷新页面
    pygame.display.update()