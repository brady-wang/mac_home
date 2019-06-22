# *_*coding:utf-8 *_*
import pygame,sys
pygame.init()
screen=pygame.display.set_caption('hello world!')
screen=pygame.display.set_mode([640,480])
screen.fill([255,255,255])
pygame.draw.rect(screen,[0,0,0],[150,50,1,1],1) #画1*1的矩形，线宽为1，这里不能是0，因为1*1无空白区域。
pygame.draw.circle(screen,[0,0,0],[150,200],1,1)
screen.set_at([150,150],[255,0,0])#将150，150改为红色。
pygame.display.flip()
while True:
    for event in pygame.event.get():
        if event.type==pygame.QUIT:
            sys.exit()