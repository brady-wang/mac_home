# *_*coding:utf-8 *_*
import pygame, sys
from pygame.locals import *
import time
import random

pygame.init()
title = pygame.display.set_caption('draw')
screen = pygame.display.set_mode([640, 480])
screen.fill([255, 255, 255])
for i in range(20):
    zhijing = random.randint(0,100)
    width = random.randint(0,255)
    width = random.randint(0,255)
    height = random.randint(0,400)
    top = random.randint(0,200)
    left = random.randint(0,500)

    pygame.draw.circle(screen,[0,0,0],[top,left],zhijing,1)
    pygame.draw.rect(screen,[255,0,0],[left,top,width,height],3)

pygame.display.flip()

while True:
    for event in pygame.event.get():
        if event.type == QUIT:
            sys.exit()