<template>
  <div v-bind:class="defineTamanho">   
    <!-- <div class="form-inline">     
      <a v-if="criar && !modal" v-bind:href="criar">Criar</a>
      <modallink v-if="criar && modal" tipo="link" nome="adicionar" titulo="Criar" css=""></modallink>
      <div class="form-group pull-right">
        <input type="search" class="form-control" placeholder="buscar" v-model="buscar">
      </div>      
    </div> -->
    <div id='principal' class="col-md-12">      
     <!-- TABELA LISTAGEM DAS DISCIPLINAS --> 
     <div id="tbl_listagem_area" class="col-md-3  table-responsive-sm">   
      <h5 style="font-weight:bold;background:#f9f8fe;margin-top:15px;">Aulas</h5>
      <table class="table table-condensed horario table-sm" >        
        <thead>
          <tr>     
            <th scope="col" colspan="2" style=" width:10%;text-align:center;">Disciplinas</th>                    
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item,index) in lista.nao_alocadas">           
            <td class='col-md-6' v-bind:id="'bandeja'+index" v-on:drop="drop" v-on:dragover="allowDrop"><div v-if='!item.dia' class='bg-primary aula' v-bind:id="item.id" v-on:dragstart="drag" draggable="true">{{item.disciplina}} {{item.professor}} {{item.sala}}</div>
            </td>                  
          </tr>                    
        </tbody>              
      </table>
     </div>
    <!-- INICIO DA PRIMEIRA TABELA HORARIO-->
    <div id="tbl_horario_area" class="col-md-9 table-responsive">
      <h5 style="font-weight:bold;background:#f9f8fe;margin-top:15px;">Manhã</h5>
      <table class="table table-bordered table-condensed horario table-xs">        
        <thead>
          <tr>     
            <th v-for="(titulo,index) in titulos[0]" scope="col" style="width:10%;text-align:center;">{{titulo}}</th>                    
          </tr>
        </thead>
        <tbody>
          <tr v-for="(titulo,index) in titulos[1]">
            <th style="height:30px;" scope="row">{{titulo}}</th>
            <td v-bind:id="'segunda_'+parseInt(index+7)" v-on:drop="drop" v-on:dragover="allowDrop">


             <template v-for="item in lista.alocadas">       

                <div v-if="item.dia == 'segunda' && parseInt(item.hora) == parseInt(index+7)" class='bg-success aula' v-bind:id="'drag'+parseInt(index+7)" v-on:dragstart="drag" draggable="true">{{item.disciplina}} {{item.professor}} sala nº:{{item.sala}}</div> 

             </form>
            </template>

            </td>
            <td v-bind:id="'terca_'+parseInt(index+7)" v-on:drop="drop" v-on:dragover="allowDrop">
              <template v-for="item in lista.alocadas">
                <div v-if="item.dia == 'terca' && parseInt(item.hora) == parseInt(index+7)" class='bg-success aula' v-bind:id="'drag'+parseInt(index+7)" v-on:dragstart="drag" draggable="true">{{item.disciplina}} {{item.professor}} sala nº:{{item.sala}}</div>            
              </template>

            </td>
            <td v-bind:id="'quarta_'+parseInt(index+7)" v-on:drop="drop" v-on:dragover="allowDrop">
              
              <template v-for="item in lista.alocadas">
                <div v-if="item.dia == 'quarta' && parseInt(item.hora) == parseInt(index+7)" class='bg-success aula' v-bind:id="'drag'+parseInt(index+7)" v-on:dragstart="drag" draggable="true">{{item.disciplina}} {{item.professor}} sala nº:{{item.sala}}</div>            
              </template>

            </td>
            <td v-bind:id="'quinta_'+parseInt(index+7)" v-on:drop="drop" v-on:dragover="allowDrop">
              
              <template v-for="item in lista.alocadas">
                <div v-if="item.dia == 'quinta' && parseInt(item.hora) == parseInt(index+7)" class='bg-success aula' v-bind:id="'drag'+parseInt(index+7)" v-on:dragstart="drag" draggable="true">{{item.disciplina}} {{item.professor}} sala nº:{{item.sala}}</div>            
              </template>

            </td>
            <td v-bind:id="'sexta_'+parseInt(index+7)" v-on:drop="drop" v-on:dragover="allowDrop">
              
              <template v-for="item in lista.alocadas">
                <div v-if="item.dia == 'sexta' && parseInt(item.hora) == parseInt(index+7)" class='bg-success aula' v-bind:id="'drag'+parseInt(index+7)" v-on:dragstart="drag" draggable="true">{{item.disciplina}} {{item.professor}} sala nº:{{item.sala}}</div>            
              </template>

            </td>
            <td v-bind:id="'sabado_'+parseInt(index+7)" v-on:drop="drop" v-on:dragover="allowDrop">
              
              <template v-for="item in lista.alocadas">
                <div v-if="item.dia == 'sabado' && parseInt(item.hora) == parseInt(index+7)" class='bg-success aula' v-bind:id="'drag'+parseInt(index+7)" v-on:dragstart="drag" draggable="true">{{item.disciplina}} {{item.professor}} sala nº:{{item.sala}}</div>            
              </template>

            </td>
          </tr>          
        </tbody>              
      </table>
      <!-- INICIO DA SEGUNDA TABELA HORARIO -->
      <h5 style="font-weight:bold;background:#f9f8fe;margin-top:15px;">Tarde</h5>
      <table class="table table-bordered table-condensed table-xs horario">        
        <thead>
          <tr>     
            <th v-for="(titulo,index) in titulos[0]" scope="col" style="width:10%;text-align:center;">{{titulo}}</th>                    
          </tr>
        </thead>
        <tbody>
          <tr v-for="(titulo,index) in titulos[2]">
            <th style="height:30px;" scope="row">{{titulo}}</th>            
            <td v-bind:id="'segunda_'+parseInt(index+13)" v-on:drop="drop" v-on:dragover="allowDrop">
              
              <template v-for="item in lista.alocadas">
                <div v-if="item.dia == 'segunda' && parseInt(item.hora) == parseInt(index+13)" class='bg-success aula' v-bind:id="'drag'+parseInt(index+13)" v-on:dragstart="drag" draggable="true">{{item.disciplina}} {{item.professor}} sala nº:{{item.sala}}</div>            
              </template>

            </td>
            <td v-bind:id="'terca_'+parseInt(index+13)" v-on:drop="drop" v-on:dragover="allowDrop">

              <template v-for="item in lista.alocadas">
                <div v-if="item.dia == 'terca' && parseInt(item.hora) == parseInt(index+13)" class='bg-success aula' v-bind:id="'drag'+parseInt(index+13)" v-on:dragstart="drag" draggable="true">{{item.disciplina}} {{item.professor}} sala nº:{{item.sala}}</div>            
              </template>

            </td>
            <td v-bind:id="'quarta_'+parseInt(index+13)" v-on:drop="drop" v-on:dragover="allowDrop">
              
              <template v-for="item in lista.alocadas">
                <div v-if="item.dia == 'quarta' && parseInt(item.hora) == parseInt(index+13)" class='bg-success aula' v-bind:id="'drag'+parseInt(index+13)" v-on:dragstart="drag" draggable="true">{{item.disciplina}} {{item.professor}} sala nº:{{item.sala}}</div>            
              </template>

            </td>
            <td v-bind:id="'quinta_'+parseInt(index+13)" v-on:drop="drop" v-on:dragover="allowDrop">
              
              <template v-for="item in lista.alocadas">
                <div v-if="item.dia == 'quinta' && parseInt(item.hora) == parseInt(index+13)" class='bg-success aula' v-bind:id="'drag'+parseInt(index+13)" v-on:dragstart="drag" draggable="true">{{item.disciplina}} {{item.professor}} sala nº:{{item.sala}}</div>            
              </template>

            </td>
            <td v-bind:id="'sexta_'+parseInt(index+13)" v-on:drop="drop" v-on:dragover="allowDrop">
              
              <template v-for="item in lista.alocadas">
                <div v-if="item.dia == 'sexta' && parseInt(item.hora) == parseInt(index+13)" class='bg-success aula' v-bind:id="'drag'+parseInt(index+13)" v-on:dragstart="drag" draggable="true">{{item.disciplina}} {{item.professor}} sala nº:{{item.sala}}</div>            
              </template>

            </td>
            <td v-bind:id="'sabado_'+parseInt(index+13)" v-on:drop="drop" v-on:dragover="allowDrop">
              
              <template v-for="item in lista.alocadas">
                <div v-if="item.dia == 'sabado' && parseInt(item.hora) == parseInt(index+13)" class='bg-success aula' v-bind:id="'drag'+parseInt(index+13)" v-on:dragstart="drag" draggable="true">{{item.disciplina}} {{item.professor}} sala nº:{{item.sala}}</div>            
              </template>

            </td>
          </tr>          
        </tbody>              
      </table>
    </div>
  </div>  
</div>
</template>
<style media="screen"> 
  body,html,table{
      width: 100%;
      height: 100%;      
      margin: 0;
      padding: 0;
      border:0;
    }  
   table.horario{
      background-color:#fafafa;
    } 
   table.horario td{      
      padding:-15px;            
    } 
    th,td{
      height: 100%;
      margin: 0px;
      padding: 0px;
      border:0px;
    }
      
   th,td,h5 {
    -webkit-user-select: none; /* Safari 3.1+ */
    -moz-user-select: none; /* Firefox 2+ */
    -ms-user-select: none; /* IE 10+ */
    user-select: none; /* Standard syntax */}
    
    div.aula{
      font-size: 9px;
      padding:0px;
      margin:0px;
      font-weight: bold;
    }
</style>
<script>
  export default {
    props:['titulos','itens','ordem','ordemcol','criar','detalhe','editar','deletar','buttons','token','modal','tamanho'],
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
        getTimeID: function(id){
          return id;
        },
        ordenaColuna: function(coluna){
          this.ordemAuxCol = coluna;
          if(this.ordemAux.toLowerCase() == "asc"){
            this.ordemAux = 'desc';
          }else{
            this.ordemAux = 'asc';
          }
        },
        allowDrop:function(ev){
          ev.preventDefault();          
        },
        drag:function(ev){
          ev.dataTransfer.setData("text", ev.target.id);
        },
        drop:function(ev){
          ev.preventDefault();
          var data = ev.dataTransfer.getData("text"); 
          // document.getElementById(ev.target.firstChild.id).submit();             
          if(ev.target.firstChild != null && ev.target.firstChild.length != 0){
            alert('Esta bandeja não está vazia !!!');
          }else{
          ev.target.appendChild(document.getElementById(data));
          let tempo_id = ev.target.id;
          let aula_id = document.getElementById(data).id; 
          console.log(aula_id);
          // axios({
          //   method: 'post',
          //   url: `/admin/aulas/${aula_id}`,
          //   data: {
          //     'tempo_id':tempo_id,
          //     'X-CSRF-TOKEN':this.token            
          //   },
          //   validateStatus: (status) => {
          //     return true; // I'm always returning true, you may want to do it depending on the status received
          //   },
          // }).catch(error => {

          // }).then(response => {
          //     // this is now called!
          // });           

          axios.put(`/admin/aulas/${aula_id}`, {'X-CSRF-TOKEN':this.token,'tempo_id':tempo_id}).then(() => {      
          // this.cruds.find(crud => crud.id === id).color = color;
        });
          // this.$store.commit('setItem',this.item);

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
          
          let lista = this.itens;                    
          let ordem = this.ordemAux || "asc";
          let ordemcol = this.ordemcolAux || 0;
          ordem.toLowerCase();
          ordemcol = parseInt(ordemcol);

          return lista;
        },
        defineTamanho:function(){          
        if(!this.tamanho || (parseInt(this.tamanho) <= 2)){
          return "col-md-12";          
        }else{
          return "col-md-"+(parseInt(this.tamanho));

        }
      },
       dados:function(){
        var valores = [{'id':'1','disciplina':'TLP','professor':'Luis Tigre','sala':'24','dia':'segunda','hora':'10'},{'id':'2','disciplina':'TIC','professor':'Osvaldo Vaba','sala':'23','dia':'terca','hora':'7'},{'id':'2','disciplina':'SEAC','professor':'Prence Nzau','sala':'11','dia':'quinta','hora':'15'}];
        return valores;
       }
      }
      
    }

</script>