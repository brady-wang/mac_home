<template>
    <li @click="handClick"> {{content}}</li>
</template>

<script>
export default {
    props:['content','index'],
    methods:{
        handClick(){
            this.$emit('delete',this.index)
        }
    }
}
</script>