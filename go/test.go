package main

import "fmt"

func main()  {
	var  arr [3] int  = [3]int  {1,2,3}

	for _,v := range arr{
		fmt.Println(v)
	}

	q := [...] int {1,2,4}
	for _,v :=range q{
		fmt.Println(v)
	}
}
