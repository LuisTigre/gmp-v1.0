<template>
<div v-bind:class="defineTamanho">
          <div class="col-md-9" style="padding-top:-10px;background: white;">
            <div>            
            <div id="List_area">
                <ul class="list-group">
	                <li v-for="item in itens.objs" class="list-group-item d-flex justify-content-between align-items-center">	                  
	                  <a style="color:#636b6f;" v-bind:href="item.url"><span v-bind:class="item.icon"></span>{{"  "+item.nome}}</a>
	                </li>                
               </ul>
            </div>          
            </div>
          </div>
        </div>

</template>



<script>
    export default {
    	props:['tamanho','itens'],
    	computed:{
    		defineTamanho:function(){
		        if(!this.tamanho || (parseInt(this.tamanho) <= 1)){
		          return "col-md-12";		          
		        }else{
		          return "col-md-"+(parseInt(this.tamanho));

		        }
		      }
    	}
      
      }
</script>
