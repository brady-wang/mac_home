package controllers

import (
	"github.com/astaxie/beego"
)

type HelloController struct {
	beego.Controller //这里相当于继承beego.Controller
}

//重写Get方法
func (hello *HelloController) Get() {
	hello.Ctx.WriteString("hello")
}