# *_*coding:utf-8 *_*
import pygame
from pygame.locals import  *
from sys import exit
from pygame.color import THECOLORS
pygame.init()

screencaption=pygame.display.set_caption('hello world')#定义窗口的标题为'hello world'
screen=pygame.display.set_mode([640,480]) #定义窗口大小为640*480

screen.fill([255, 255, 255])  # 用白色填充窗口
pygame.draw.circle(screen,THECOLORS["red"],[100,100],30,0)
pygame.draw.rect(screen,[255,0,0],[250,150,300,200],0)
pygame.display.update()
while True:

    for event in pygame.event.get():
        if event.type == QUIT:
            exit()
