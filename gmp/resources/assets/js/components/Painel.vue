<template>
    <div v-bind:class="defineCor">
        <div v-if="titulo" class="panel-heading">{{ titulo }}</div>

        <div class="panel-body">
            <slot></slot>
        </div>
    </div>
</template>

<style media="screen">
  .blue {
    border-color: #366c8a;
  }
  .blue > .panel-heading {
    color: #fff;
    background-color: #366c8a;
    border-color: #366c8a;
  }

  .orange {
    border-color: #e85e0d;
  }

  .orange > .panel-heading {
    color: #f7f7f7;
    background-color: #e85e0d;
    border-color: #e85e0d;
  }

</style>

<script>
    export default {
        props:['titulo','cor'],
        computed:{
            defineCor:function(){
                return "panel " + (this.cor || "panel-default"); 
            }
        }
    }
</script>

