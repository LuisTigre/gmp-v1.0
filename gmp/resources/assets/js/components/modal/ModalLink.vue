<template>
  <span>
    <span v-if="item">
      <button v-on:click="preencheFormulario()" v-if="!tipo || (tipo != 'button' && tipo != 'link')" type="button" v-bind:class="css || 'btn btn-primary'" data-toggle="modal" v-bind:data-target="'#' + nome">{{titulo}}</button>
      <button v-on:click="preencheFormulario()" v-if="tipo == 'button'" type="button" v-bind:class="css || 'btn btn-primary'" data-toggle="modal" v-bind:data-target="'#' + nome">{{titulo}}</button>
      <a v-on:click="preencheFormulario()" v-if="tipo == 'link'" href="#" v-bind:class="css || ''" data-toggle="modal" v-bind:data-target="'#' + nome">{{titulo}}</a>
    </span>

    <span v-if="!item">
      <button v-if="!tipo || (tipo != 'button' && tipo != 'link')" type="button" v-bind:class="css || 'btn btn-primary'" data-toggle="modal" v-bind:data-target="'#' + nome">{{titulo}}</button>
      <button v-if="tipo == 'button'" type="button" v-bind:class="css || 'btn btn-primary'" data-toggle="modal" v-bind:data-target="'#' + nome">{{titulo}}</button>
      <a v-if="tipo == 'link'" href="#" v-bind:class="css || ''" data-toggle="modal" v-bind:data-target="'#' + nome">{{titulo}}</a>
    </span>

  </span>

</template>


<script>
    export default {
      props:['tipo','nome','titulo','css','item','url'],
      methods:{         
        preencheFormulario:function(){
          let url_parts = this.url.split('/');
          let req_url  = this.url + this.item.id;
          
          if(url_parts[0] == 'itself'){
            req_url = '/'+url_parts[1]+'/'+url_parts[2]+'/'+this.item.id;            

          }else if(url_parts.length > 5){
            req_url = '/'+url_parts[1]+'/'+url_parts[2]+'/'+this.item.id+'/'+url_parts[4]+'/'+this.item.id;
          }
          
           axios.get(req_url).then(res => {
           if(url_parts[0] != 'itself'){
            this.$store.commit('setItem',res.data);              
           }         
           });

           if(url_parts[0] == 'itself'){
            this.$store.commit('setItem',this.item);
              console.log(this.item);             

           }  
            
        }
      }
    }
</script>

