template  v-model="defineCor">
  <div v-on:mousemove="atribueCor" v-bind:class="defineTamanho"> 
  <div class="table-responsive">  
    <div class="form-inline">     
      <!-- <a v-if="criar && !modal" v-bind:href="criar">Criar</a> -->
      <!-- <modallink v-if="criar && modal" tipo="link" nome="adicionar" titulo="Criar" css=""></modallink> -->
      <div class="form-group pull-right">
        <input type="search" class="form-control" placeholder="buscar" v-model="buscar">
      </div>      
    </div>
    <table id="mytable" class="table table-bordered table-sm table-condensed table-striped " style="font-size:10px;">
    <thead>
      <tr style="font-weight: bold;">
        <th rowspan="3" style="cursor:pointer;" v-on:click="ordenaColuna(index)">#</th>  
        <!-- <th rowspan="3" style="cursor:pointer;" v-on:click="ordenaColuna(index)">Nº Mat</th>   -->
        <th class="nome" rowspan="3" style="cursor:pointer;width:30%;" v-on:click="ordenaColuna(index)">Nome</th>  
        <!-- <th rowspan="3" style="cursor:pointer;" v-on:click="ordenaColuna(index)">Id</th> --> 
        <th colspan="3" style="cursor:pointer;text-align:center" v-on:click="ordenaColuna(index)" v-for="(titulo,index) in titulos.data">{{ titulo.acronimo}}</th>       
        <!-- @foreach($listaCabecalho2 as $key => $value),
        @endforeach -->
        <!-- <th rowspan="3">Gê</th> -->  
        <th rowspan="3">Md</th>  
        <th rowspan="3">OBS</th>  
      </tr>
      <tr>
        <!-- <th colspan="2" style="cursor:pointer" v-on:click="ordenaColuna(index)" v-for="(titulo,index) in titulos">Faltas</th>
        <th colspan="2" style="cursor:pointer" v-on:click="ordenaColuna(index)" v-for="(titulo,index) in titulos">CT1</th> -->
        <template v-for="(titulo,index) in titulos.data">         
          <th class="faltas" colspan="2" style="cursor:pointer;" v-on:click="ordenaColuna(index)">Faltas</th>
          <th class="ct1" rowspan="2" style="cursor:pointer" v-on:click="ordenaColuna(index)">CT1</th>
        </template>
                        
      </tr>
      <tr>
        <template v-for="(titulo,index) in titulos">
          <th style="cursor:pointer" v-on:click="ordenaColuna(index)">J</th>
          <th style="cursor:pointer" v-on:click="ordenaColuna(index)">I</th>
        </template> 
        
        <!-- @foreach($listaCabecalho2 as $key => $value)
        <th>J</th>  
        <th>N</th>      
        @endforeach    -->       
      </tr>
    </thead>
  <tbody v-model="defineCor">    
      <!-- <th scope="row">1</th>       -->
      <tr v-for="(item,index) in lista">
         <td class="dados" v-for="i in item">{{ i | formataData }}</td>
      </tr> 
  </tbody>
</table>
      

  </div>   
</div>   
   
</template>
<style media="screen">
  table{
    padding:0px;
    margin:0px;
  }
  th.ct1{
    background: #fafafa;
  }
  .fundo{
    background: #fafafa;
  }
  .desistido{
    background = rgb(255,212,227);
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
          var row = 3;
          var col = 4;
          var limite = table.rows.length;
          /*PERCORRE CADA LINHA DA TABELA*/
          for (var i = row; i < limite; i++) {
            /*PERCORRE CADA CELULA DA LINHA*/
            for (var a = col; a < table.rows[i].cells.length; a++) {

              /*ATRIBUI O STRIPPED COLOR NA TABELA*/
              // table.rows[i].cells[a].style.background = "#fafafa";
              /*FIM...*/

            /*RECUPERA O VALOR DA ACTUAL CELULA DA TABELA PERCORIDA*/
            let valor = table.rows[i].cells[a].innerText;

           if(valor != null && valor != '')         
            if(valor == 'Reprovado'){
                table.rows[i].cells[a].style.color = "red";
            }else if(valor == 'Desistido' || valor == 'Suspenso' || valor == 'Transferido'){

            /*PERCORRE CADA CELULA DA LINHA DA TABELA DE */            
            for (var b = 0; b <= table.rows[i].cells.length-1; b++) {
            console.log(table.rows[i].cells.length-1);

            /*COLORI O A CELULA TODA MEDIANTE O STATUS DO ALUNO*/ 
            valor == 'Desistido' ? table.rows[i].cells[b].style.background = "rgb(249,239,184)" : (valor == 'Suspenso' ? table.rows[i].cells[b].style.background = "rgb(255,212,227)" : table.rows[i].cells[b].style.background = "rgb(247,221,252)");        
            }        
            }else if(parseInt(valor) < 10){
            table.rows[i].cells[a].style.color = "red";        
            }else{                         

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
      defineCor: function(){ 
        var x = document.getElementsByClassName("ct1");
        var x = document.getElementById("mytable");          
        console.log(x);
        }
      }
      
    }

</script>





