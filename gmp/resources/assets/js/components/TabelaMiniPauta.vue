<template v-model="defineCor">
  <div v-on:mousemove="atribueCor" v-bind:class="defineTamanho">
  <div class="table-responsive">   
    <div class="form-inline">
    <slot></slot>     
      <a v-if="criar && !modal" v-bind:href="criar">Criar</a>
      <modallink v-if="criar && modal" tipo="link" nome="adicionar" titulo="Criar" css=""></modallink>
      <div class="form-group pull-right">
        <input type="search" class="form-control" placeholder="buscar" v-model="buscar">
      </div>&nbsp;&nbsp;      
    </div>
  <div class="scrollbar scrollbar-default">      
   <table id="mytable" class="table table-striped table-bordered table-sm table-condensed" style="font-size:10px;">
    <thead>
        <tr style="font-weight: bold;">
          <th scope="col" rowspan="2" style="">#</th>    
          <th scope="col" rowspan="2" style="">Nº</th>    
          <th scope="col" rowspan="2" style="text-align:left;">Nome completo</th>  
          <!-- <th scope="col" rowspan="2" style="text-orientation:vertical);">Id</th><th scope="col" rowspan="2" style="text-orientation:vertical);">F</th> -->        
          
          <th scope="col" rowspan="2">F</th>  
          <th scope="col" colspan="4" style="">I TRIMESTRE</th>
          <th scope="col" rowspan="2">F</th>  
          <th scope="col" colspan="6" style="">II TRIMESTRE</th>
          <th scope="col" rowspan="2">F</th>  
          <th scope="col" colspan="5" style="">III TRIMESTRE</th>     
          <th scope="col" colspan="6" style="">Classificação Anual</th> 
        </tr>
      <tr>      
        <th scope="col">Mac</th>  
        <th scope="col">P1</th>      
        <th scope="col">P2</th>      
        <th scope="col">CT1</th>  
        <th scope="col">Mac</th>  
        <th scope="col">P1</th>      
        <th scope="col">P2</th>      
        <th scope="col">CF2</th>      
        <th scope="col">CT1</th>
        <th scope="col">CT2</th>      
        <th scope="col">Mac</th>  
        <th scope="col">P1</th>      
        <th scope="col">CF3</th>      
        <th scope="col">CT2</th>      
        <th scope="col">CT3</th>  
        <th scope="col">MTC</th>      
        <th scope="col">60%</th>      
        <th scope="col">PG</th>      
        <th scope="col">40%</th>      
        <th style="width:5%;font-size:8px;" scope="col">60% + 40%</th>    
        <th style="width:4%;" v-if="detalhe || editar || deletar ">Acção</th>   
      </tr>    
    </thead>
  <tbody>    
      <!-- <th scope="row">1</th>       -->   
      <tr v-for="(item,index) in lista" style="font-size: 12px;">
         <td class="centro">{{item.id}}</td>   
         <td class="centro">{{item.numero}}</td>   
         <td>{{item.nome}}</td>   
        <!--  <td></td> -->   
         <!-- I TRIMESTRE -->
         <td class="centro"><span>{{item.fnj1}}</span></td>     
         <td class="centro nota"><span>{{item.mac1}}</span></td>     
         <td class="centro nota"><span>{{item.p11}}</span></td>     
         <td class="centro nota"><span>{{item.p12}}</span></td>     
         <td class="centro nota"><span>{{item.ct1}}</span></td>     
         <!-- II TRIMESTRE -->
         <td class="centro"><span>{{item.fnj2}}</span></td>
         <td class="centro nota"><span>{{item.mac2}}</span></td>     
         <td class="centro nota"><span>{{item.p21}}</span></td>     
         <td class="centro nota"><span>{{item.p22}}</span></td>     
         <td class="centro nota"><span>{{item.cf2}}</span></td>     
         <td class="centro nota"><span>{{item.ct1}}</span></td>    
         <td class="centro nota"><span>{{item.ct2}}</span></td>           
         <!-- III TRIMESTRE -->
         <td class="centro"><span>{{item.fnj3}}</span></td>  
         <td class="centro nota"><span>{{item.mac3}}</span></td>     
         <td class="centro nota"><span>{{item.p31}}</span></td>     
         <td class="centro nota"><span>{{item.cf3}}</span></td>     
         <td class="centro nota"><span>{{item.ct2}}</span></td>     
         <td class="centro nota"><span>{{item.ct3}}</span></td>    
         <!-- classificacao anual -->
         <td class="centro nota"><span>{{item.mtc}}</span></td>  
         <td class="centro">{{item.sessenta}}</td>     
         <td class="centro nota"><span>{{item.p32}}</span></td>     
         <td class="centro">{{item.quarenta}}</td>     
         <td class="centro nota"><span>{{item.notafinal}}</span></td> 
         <td v-if="detalhe || editar || deletar">
              <form v-bind:id="index" v-if="deletar && token" v-bind:action="deletar + item.id" method="post">
              <input type="hidden" name="_method" value="DELETE">
              <input type="hidden" name="_token" v-bind:value="token">
              
              <a v-if="detalhe && !modal" v-bind:href="detalhe">Detalhe</a>
              <modallink v-if="detalhe && modal" v-bind:item="item" v-bind:url="detalhe" tipo="link" nome="detalhe" titulo="Detalhe " css=""></modallink>

              <a v-if="editar && !modal" v-bind:href="editar"> Editar </a>
              <modallink v-if="editar && modal" v-bind:item="item" v-bind:url="editar" tipo="link" nome="editar" titulo="Editar " css=""></modallink>

              <a href="#" v-on:click="executaForm(index)"> Deletar</a>

            </form>
              <!-- *************** WITHOUT TOKEN ***************** -->
              <span v-if="!token">             
              
              <modallink v-if="detalhe && modal" v-bind:item="item" tipo="link" v-bind:url="detalhe" nome="detalhe" titulo="detalhe " css=""></modallink>

              <a v-if="editar && !modal" v-bind:href="editar"> Editar</a>
              <modallink v-if="editar && modal" tipo="link" nome="editar" titulo="Editar " v-bind:item="item" v-bind:url="editar" css=""></modallink>

              <a v-if="deletar" v-bind:href="deletar">Deletar</a>   
              </span>

              <!-- ******************* WITH TOKEN ************************ -->
              <span v-if="token && !deletar">              

              <a v-if="detalhe && !modal" v-bind:href="detalhe">Detalhe </a>
              <modallink v-if="detalhe && modal" v-bind:item="item" v-bind:url="detalhe" tipo="link" nome="detalhe" titulo="detalhe " css=""></modallink>
              
              <a v-if="editar && !modal" v-bind:href="editar"> Editar</a>
              <modallink v-if="editar && modal" tipo="link" v-bind:item="item" v-bind:url="editar" nome="editar" titulo="Editar " css=""></modallink>
            </span>            
          </span>            
        </td>     
      </tr>   
   </tbody>
  </table>
 </div>
</div>
</div>   
   
</template>
<style media="screen">
  table{
    padding:0px;
    margin:0px;
  }
  th{
    text-align: center;
    border:1px solid;
    padding: 1px;
    margin:-200px;
      
  }
  th.ct1{
    background: #fafafa;
  }
  .fundo{
    background: #fafafa;
  }
  .desistido{
    background: rgb(255,212,227);
  }
  .centro{
    text-align:center;
  }
  /*width*/
  -webkit-scrollbar-track{
    width:10px;
  }

</style>

<script>
  export default {
    props:['titulos','itens','ordem','ordemcol','criar','detalhe','editar','deletar','token','modal','tamanho'],
      data: function(){
        return {          
          buscar:'',
          ordemAux: this.ordem || 'asc',
          ordemcolAux: this.ordemcol ||  0
        }
    },
    methods:{
        executaForm: function(index){
          document.getElementById(index).submit();
        },
        ordenaColuna: function(coluna){
          this.ordemAuxCol = coluna;
          if(this.ordemAux.toLowerCase() == "asc"){
            this.ordemAux = 'desc';
          }else{
            this.ordemAux = 'asc';
          }
        },
        atribueCor: function(event){

          var table = document.getElementById("mytable");
          var exceptions = [8,15,22,24]
          var row = 2;
          var col = 4;
          for (var i = row; i < table.rows.length; i++) {
            for (var a = 4; a < table.rows[i].cells.length; a++) {
              
              // table.rows[i].cells[a].style.background = "#fafafa";
            
              let valor = table.rows[i].cells[a].innerText;
              let avaliacao_id = parseInt(table.rows[i].cells[0].innerText);
              let avaliacao;
              let aluno;
            if(valor != null && valor != ''){         
            if(parseInt(valor) < 10){
            table.rows[i].cells[a].style.color = "red";     
            table.rows[i].cells[8].style.color = "black";     
            table.rows[i].cells[15].style.color = "black";     
            table.rows[i].cells[22].style.color = "black";     
            table.rows[i].cells[24].style.color = "black";

           axios.get('/admin/avaliacaos/' + avaliacao_id).then(res => { avaliacao = res.data; 
           // console.log(avaliacao.turma_id);                            
           });                 
             } 
            }     
          }
         }
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
      },
      nota: function(){ 
        
        }
      }
      
    }

</script>





