# *_*coding:utf-8 *_*
import pygame,sys
pygame.init()
pygame.mixer.init()
screen=pygame.display.set_mode([640,480])
pygame.time.delay(1000)
pygame.mixer.music.load("D:/www/python/pygametest/a.mp3")
pygame.mixer.music.play()
while 1:
    for event in pygame.event.get():
        if event.type==pygame.QUIT:
            sys.exit()
