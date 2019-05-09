<template>
    <div id="cabecaho" align="center" style="font-size: 12px;font-weight: bold;" class="table-responsive">
      <p>COLÉGIO PADRE BUILU</p>                           
      <p>FÉ E CIÊNCIA</p>                                       
      <p>ENSINO SECUNDÁRIO TÉCNICO PROFISSIONAL</p>       
      <p>PAUTA DE APROVEITAMENTO</p>            
      <p>ÁREA DE FORMAÇÃO: {{$curso->nome}}</p>           
      <p style="color:red;">REGIME DIURNO ( {{$turma->periodo}} )</p> 
    </div>
    <div id="cabecaho" align="center" style="font-size: 12px;">
      <div class="col-md-3">
       <p>A DIRECTORA DO INSTITUTO </p> 
       <p>_________________________________</p> 
       <p>MARIA LUÍSA BUCA</p> 
       <p>DATA {{date('d')}}/{{date('m')}}/{{date('Y')}} </p> 
      </div>
      <div class="col-md-3">
       <p>ANO LECTIVO {{$turma->ano_lectivo}}</p> 
       <p>{{$classe->nome}} CLASSE TURMA: {{$turma_info[2]}} </p>         
      </div>
      <div class="col-md-3">
       <p>I  TRIMESTRE</p>
      </div>
      <div class="col-md-1" align="left">
       <p>TOTAL:</p>
       <p>M:</p>
       <p>F:</p>
       <p>SALA Nº</p>
      </div>
        <!-- FOOTER -->
      <div id="cabecaho" align="center">
      <div class="col-md-3">
       <p>O(A) DIRECTOR(A) DE TURMA                  
       <p>_________________________________</p> 
       <p>{{$director_turma->nome}}</p> 
      </div>
      <div class="col-md-3">
       <p>O COORDENADOR DO CURSO</p> 
       <p>_________________________________</p> 
       <p>{{$coordenador->nome}}</p>         
      </div>
      <div class="col-md-3">
       <p>O SUB-DIRECTOR PEDAGÓGICO</p>
       <p>_________________________________</p>  
       <p>ERNESTO TIGRE ISSAMBO</p>
      </div>
      <div class="col-md-3">
       <p> O DIRECTOR DO COLÉGIO</p>
       <p>_________________________________</p>  
       <p>JOÃO CAPITA CHINCOCOLO</p>       
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

