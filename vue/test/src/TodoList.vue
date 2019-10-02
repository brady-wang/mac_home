<template>
    <div>
            <input type="text" v-model="inputValue">
            <button @click="handClick">提交 </button>

            <ul>
                <todo-item 
                v-for="(item ,index) in list"
                 :key="index"
                 :content="item"
                 :index="index"
                 @delete="handDelete"
                 >{{item}}</todo-item>
            </ul>
    </div>
</template>

<script>
import TodoItem from "./components/TodoItem"
export default {
    components:{
        'todo-item':TodoItem
    },
    data () {
        return {
            inputValue: '',
            list:[]
        }
    },
    methods:{
        handClick(){
            this.list.push(this.inputValue)
            this.inputValue = ''
        },
        handDelete(index){
            this.list.splice(index,1)
        }
    }
}
</script>


<style>

</style>