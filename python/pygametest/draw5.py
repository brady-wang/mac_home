# *_*coding:utf-8 *_*
import pygame, sys


def lineleft():
    plotpoints = []
    for x in range(0, 640):
        y = -5 * x + 1000
        plotpoints.append([x, y])
    pygame.draw.lines(screen, [0, 0, 0], False, plotpoints, 5)
    pygame.display.flip()


def lineright():
    plotpoints = []
    for x in range(0, 640):
        y = 5 * x - 2000
        plotpoints.append([x, y])
    pygame.draw.lines(screen, [0, 0, 0], False, plotpoints, 5)
    pygame.display.flip()


def linemiddle():
    plotpoints = []
    x = 300
    for y in range(0, 480, 20):
        plotpoints.append([x, y])
        if len(plotpoints) == 2:
            pygame.draw.lines(screen, [0, 0, 0], False, plotpoints, 5)
            plotpoints = []
    pygame.display.flip()


def loadcar(yloc):
    my_car = pygame.image.load('ok1.jpg')
    locationxy = [310, yloc]
    screen.blit(my_car, locationxy)
    pygame.display.flip()


if __name__ == '__main__':
    pygame.init()
    screen = pygame.display.set_caption('hello world!')
    screen = pygame.display.set_mode([640, 480])
    screen.fill([255, 255, 255])
    lineleft()
    lineright()
    linemiddle()

    looper = 480

    while True:
        for event in pygame.event.get():
            if event.type == pygame.QUIT:
                sys.exit()
            elif event.type == pygame.KEYDOWN:

                if event.key == pygame.K_UP:
                    looper = looper - 50
                    if looper < -480:
                        looper = 480

                    pygame.draw.rect(screen, [255, 255, 255], [310, (looper + 132), 83, 132], 0)
                    loadcar(looper)
                if event.key == pygame.K_DOWN:
                    looper = looper + 50
                    if looper > 480:
                        looper = -480
                    pygame.draw.rect(screen, [255, 255, 255], [310, (looper - 132), 83, 132], 0)
                    loadcar(looper)
