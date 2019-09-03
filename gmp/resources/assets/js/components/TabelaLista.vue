<template>
  <div v-bind:class="defineTamanho">   
   <div class="table-responsive">     
    <div class="form-inline">
    <slot></slot>     
      <a v-if="criar && !modal" v-bind:href="criar">Criar</a>
      <modallink v-if="criar && modal" tipo="link" nome="adicionar" titulo="Criar" css=""></modallink>
      <div class="form-group pull-right">
        <input type="search" class="form-control" placeholder="buscar" v-model="buscar">
      </div>      
    </div>
      <table class="table table-striped table-hover table-xs" style="font-size:11.5px;">
        <thead>
          <tr>
            <th v-if="multiselect" class="ckbColumn" v-bind:style="{visibility:visibilidade}" v-on:click="addiconarUmAoCheckList">           
                <span><a href=""><i class="glyphicon glyphicon-trash"></i></a></span>              
            </th>
            <th style="cursor:pointer" v-on:click="ordenaColuna(index)" v-for="(titulo,index) in titulos">{{ titulo }}</th>
            <th v-if="detalhe || editar || deletar ">Acção</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item,index) in lista">
            <td v-if="multiselect" class="ckbColumn">       
                <label><input type="checkbox" v-bind:value="item.id" v-bind:id="index + '_item'" v-bind:style="{visibility:visibilidade}" v-on:click="addiconarUmAoCheckList" ></label>              
            </td>            
            <td v-for="i in item" v-on:click="visualizarChecklist">{{ i | formataData }}</td>            
            <td v-if="detalhe || editar || deletar || buttons" v-on:click="visualizarChecklist">
              <form v-bind:id="index" v-if="deletar && token" v-bind:action="deletar + item.id" method="post">
                <input type="hidden" name="_method" value="DELETE">
                <input type="hidden" name="_token" v-bind:value="token">              

                <a v-if="detalhe && !modal" v-bind:href="detalhe"> Detalhe </a>
                <modallink v-if="detalhe && modal" v-bind:item="item" v-bind:url="detalhe" tipo="link" nome="detalhe" titulo="Detalhe " css=""></modallink>

                <a v-if="editar && !modal" v-bind:href="editar"> Editar </a>
                <modallink v-if="editar && modal" v-bind:item="item" v-bind:url="editar" tipo="link" nome="editar" titulo="Editar " css=""></modallink>

                <a href="#" v-on:click="executaForm(index)"> Deletar</a>
                                
                  <span class="dropdown dropleft" v-if="buttons && modal" style="position:absolute;margin-top:-8px;left:95%;">
                    <a class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                     <i class="glyphicon glyphicon-option-vertical"></i>
                    </a>                  
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu2">
                      <a class="list-group-item btn-xs" v-if="buttons && modal" v-for="button in buttons" v-bind:href="button.url +item.id+'/' + button.action">{{button.nome}} </a>
                    </div>
                  </span>   

              </form>
              <!-- *************** WITHOUT TOKEN ***************** -->
              <span v-if="!token">
                     
              <a v-if="detalhe && !modal" v-bind:href="detalhe">Detalhe </a>           
              
              <modallink v-if="detalhe && modal" v-bind:item="item" tipo="link" v-bind:url="detalhe" nome="detalhe" titulo="detalhe " css=""></modallink>

              <a v-if="editar && !modal" v-bind:href="editar"> Editar</a>
              <modallink v-if="editar && modal" tipo="link" nome="editar" titulo="Editar " v-bind:item="item" v-bind:url="editar" css=""></modallink>

              <a v-if="deletar" v-bind:href="deletar">Deletar</a>
                         
                <span class="dropdown dropleft" v-if="buttons && modal" style="position:absolute;;margin-top:-7.5px;">
                  <a class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                   <i class="glyphicon glyphicon-option-vertical"></i>
                  </a>
                  <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                    <a class="list-group-item" v-if="buttons && modal" v-for="button in buttons" v-bind:href="button.url +item.id+'/' + button.action">{{button.nome}} </a>
                  </div>
                </span>
              </span>   
             

              <!-- ******************* WITH TOKEN ************************ -->
              <span v-if="token && !deletar">

              <div class="dropdown" v-if="buttons && modal">
                  <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">                   
                  </button>
                  <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                    <a v-if="buttons && modal" v-for="button in buttons" v-bind:href="button.url +item.id+'/' + button.action">{{button.nome}} </a>
                  </div>
              </div>

              <a v-if="detalhe && !modal" v-bind:href="detalhe">Detalhe </a>
              <modallink v-if="detalhe && modal" v-bind:item="item" v-bind:url="detalhe" tipo="link" nome="detalhe" titulo="detalhe " css=""></modallink>
              
              <a v-if="editar && !modal" v-bind:href="editar"> Editar</a>
              <modallink v-if="editar && modal" tipo="link" v-bind:item="item" v-bind:url="editar" nome="editar" titulo="Editar " css=""></modallink>

                             
                   <span class="dropdown dropleft" v-if="buttons && modal" style="position:absolute;margin-top:-8px;left:95%;">
                    <a class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                     <i class="glyphicon glyphicon-option-vertical"></i>
                    </a>                  
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu2">
                      <a class="list-group-item btn-xs" v-if="buttons && modal" v-for="button in buttons" v-bind:href="button.url +item.id+'/' + button.action">{{button.nome}} </a>
                    </div>
                  </span>   
              </span>
                        
                       
            </td>
          </tr>
        </tbody>
      </table>
  </div>
  <div>
   <label>{{this.itens.data.length}} itens...</label>    
  </div>
  </div>
   
   
</template>

<style style media="screen">
    < tr .checked {
       background:crimson;
       color:white;
    }
    /*li:has(> a.active) {  styles to apply to the li tag  }*/
</style>

<script>
  export default {
    props:['titulos','itens','ordem','ordemcol','criar','detalhe','editar','deletar','index_url','buttons','multiselect','token','modal','tamanho'],
      mounted(){
        this.preecherDados;               
        this.lista;    
      },
      data: function(){
        return {
          buscar:'',
          visibilidade:"",                 
          itens_selecionados:[],
          ordemAux: this.ordem || 'asc',
          ordemcolAux: this.ordemcol ||  0
        }
    },
    methods:{
        executaForm: function(index){

          if (confirm("Deseja eliminar ?")) {
            // if(this.itens_selecionados.length != 0){
            //     for (let i = 0; i < this.itens_selecionados.length; i++) {   
            //         // console.log(this.deletar+this.itens_selecionados[i]+"/deleteMultiple");
            //         axios.delete(this.deletar+this.itens_selecionados[i]).then((res) => {
            //         }).catch((err) => {
            //           console.log(err);
            //         })           
            //     }
            // }else{
                document.getElementById(index).submit();
                // alert("Selecione pelo menos um item na lista..."); 
              
            // }         
          } 
                // window.location.replace(this.index_url);           
        },
        ordenaColuna: function(coluna){
          this.ordemAuxCol = coluna;
          if(this.ordemAux.toLowerCase() == "asc"){
            this.ordemAux = 'desc';
          }else{
            this.ordemAux = 'asc';
          }
        },
        addiconarUmAoCheckList: function(event){   
          let id = event.target.id;
          let element = document.getElementById(id);
          let isChecked = element.checked;
          if(isChecked == true){
            element.className = "checked";
            element.offsetParent.parentElement.className = "danger";              
          }else{
            element.className = "";
            element.offsetParent.parentElement.className = "";              
          }
          
          let checkedItems = document.getElementsByClassName("checked");
          this.itens_selecionados=[];   

          for (let i = 0; i < checkedItems.length; i++) {
              this.itens_selecionados.push(checkedItems[i].value);
          }          
                      
        },
        addiconarTodosAoCheckList: function(event){   
          let id = event.target.id;
          let element = document.getElementsByTagName('checkbox');
          let isChecked = element.checked;
          if(isChecked == true){
            element.className = "checked";
            element.offsetParent.parentElement.className = "danger";              
          }else{
            element.className = "";
            element.offsetParent.parentElement.className = "";              
          }
          
          let checkedItems = document.getElementsByClassName("checked");
          this.itens_selecionados=[];   

          for (let i = 0; i < checkedItems.length; i++) {
              this.itens_selecionados.push(checkedItems[i].value);
          }          
                      
        }
        ,
        visualizarChecklist: function(){       
           
          document.getElementsByClassName("ckbColumn");
          this.visibilidade = this.visibilidade == "collapse" ? "" : "collapse";          
        },
        preecherDados:function(){
          axios.get(this.index_url).then((res) => {
                  this.itens.data = res.data
                  console.log(this.itens.data);               
                }).catch((err) => {
                  console.log(err);
                })  

        }
      },
      filters:{
        formataData: function(valor){
          if(!valor) return '';
          valor = valor.toString();
          if(valor.split('-').length == 3){
            valor = valor.split('-');
            return valor[2] + '/' + valor[1] + '/' + valor[0];
          }
          return valor;
        }
      },
    computed:{
        lista:function(){          

          let lista = this.itens.data;
          let ordem = this.ordemAux || "asc";
          let ordemcol = this.ordemcolAux || 0;
          ordem.toLowerCase();
          ordemcol = parseInt(ordemcol);

          if(ordem =="asc"){
            lista.sort(function(a,b){
            if(Object.values(a)[ordemcol]>Object.values(b)[ordemcol]){return 1;}
            if(Object.values(a)[ordemcol]<Object.values(b)[ordemcol]){return -1;}            
            return 0;           
          });          
        }else{          
            lista.sort(function(a,b){
            if(Object.values(a)[ordemcol]<Object.values(b)[ordemcol]){return 1;}
            if(Object.values(a)[ordemcol]>Object.values(b)[ordemcol]){return -1;}            
            return 0; 
        });
        }          
          if(this.buscar){
          return lista.filter(res => {
            res = Object.values(res);
            for(let k = 0;k < res.length; k++){
              if((res[k] + "").toLowerCase().indexOf(this.buscar.toLowerCase()) >= 0){
                return true;
              }
            }
            return false;
          });
          }

          return lista;
        },        
        defineTamanho:function(){
        if(!this.tamanho || (parseInt(this.tamanho) <= 2)){
          return "col-md-12";          
        }else{
          return "col-md-"+(parseInt(this.tamanho));

        }
      }
      }
      
    }

</script>




</script>

