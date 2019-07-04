<!DOCTYPE html>
  <html>
    <head>
      <title>PAUTA do $epoca->trimestre trimestre da turma $turma->nome</title>     
      <style type="text/css">
            .cor_vermelho{
              background-color: red;
            }
           .vermelhado td{
              color:red;
              border: solid 1px black;
           }
           .vtexto{
              -ms-transform: rotate(60deg); <!-- IE 9 -->
              -webkit-transform: rotate(90deg); <!-- Safari 3-8 -->
               transform: rotate(90deg);
               font-weight:bold;
               font-size:11px;       
           }
           .otexto{
              -ms-transform: rotate(30deg); <!-- IE 9 -->
              -webkit-transform: rotate(30deg); <!-- Safari 3-8 -->
               transform: rotate(30deg);
               font-weight:bold;
               font-size:11px;
               height:50px;
               width:1px;
               word-break: keep-all;
               
           }    
           .ct {
            
           }
           .centro {
                text-align:center;
           }
            #cabecalho,#seccao_topo_grupo,#rodape{
                text-transform: uppercase;
            }
            #cabecalho p>span{
                color:red;
            }
            #cabecalho{
                margin-top:-50px;         
            }
            #cabecalho p{
                margin-bottom:-12px;
            }
            #seccao_topo_grupo{
                position:relative;
                top:10px;
            }
            #seccao_topo_grupo > div{
                float:left;
                width:20%;
            }
            #seccao_topo_grupo > #seccao_5,#seccao_topo_grupo > #seccao_6,#seccao_topo_grupo > #seccao_7,#seccao_topo_grupo > #seccao_8{
                float:left;
                width:5%;
                margin-top:-2px;
                font-size:10px;
           }
            #seccao_topo_grupo > #seccao_5>p,#seccao_topo_grupo > #seccao_6>p,#seccao_topo_grupo > #seccao_7>p,#seccao_topo_grupo > #seccao_8>p{
                margin-bottom:-12px;
           }
            #seccao_topo_grupo #seccao_1>p,#seccao_topo_grupo #seccao_5>p{
                margin-bottom:-12px;
           }
            #rodape{;
                position:relative;          
                top:9%;
           }
            #rodape p{
                margin-top:-15px;
           }
            #rodape > div{
                float:left;
                width:25%;
           }       
            #mytable{
                width:100%;
                border-collapse:collapse;
                font-size:11px;

          }
           #tabela_area{
                background:transparent;
                position:relative;
                top:80px;
          }
          #mytable>th{
                text-align:center;
          }
          th,td{
                border:1px solid;
                padding: 1px;
                margin:-200px;
          }
      </style>
     </head>
     <body>
        <div id='tabela_area'>
          <table id='mytable'>
              <thead border:1px solid;>
                  <tr style='font-weight: bold;'>
                      <th class='centro' scope='col' rowspan='3' >Nº</th>  
                      <th scope='col' rowspan='3'style='width:1%;'><p class='otexto' style='font-size:10px;'>Nº_MATR.</p></th>  
                      <th scope='col' rowspan='3'style='width:15%;'><span style='font-size:14;padding-left:5px;'>NOME</span> </th>  
                      <th class='centro' scope='col' rowspan='3'><p class='vtexto'>IDADE</p></th>
                  
                          @foreach($listaCabecalho2 as $key => $value)        
                                <!--10ª CLASSE-->
                                @if($classe->nome == "10ª")
                                     @if($disciplinas_terminais->contains($value->id))   
                                          <th style='font-size:12px;font-weight:bold;'scope='col' colspan='7'class='centro'>{{$value->acronimo}}</th>

                                     @elseif(isset($disc_terminadas_10) && $disc_terminadas_10->contains($value->id))
                                          <th scope='col' colspan='1'class='centro'>{{$value->acronimo}}</th> 

                                     @else            
                                          <th scope='col' colspan='5'class='centro'>{{$value->acronimo}}</th>
                                     @endif       
                                
                                <!--11ª CLASSE-->
                                @elseif($classe->nome == "11ª")
                                     $disc_terminal = $disciplinas_terminais->where("id",$value->id);          
                                     $disc_terminada_10 = $disc_terminadas_10->where("id",$value->id);          
                                     $disc_anterior = $disciplinas_10->where("id",$value->id);          

                                     <!--DISCIPLINA TERMINADA-->
                                     @if($disc_terminada_10->isNotEmpty())      
                                          <th scope='col' colspan='1' rowspan='3' class='centro'><p class='vtexto' style='margin:0px -15px;font-size:11px;'>{{$value->acronimo}}</p></th>
                                      
                                      <!--DISCIPLINA TERMINAL COM ANTECEDENTE-->
                                     @elseif($disc_terminal->isNotEmpty() && $disc_anterior->isNotEmpty())   
                                          <th scope='col' colspan='8'class='centro'>{{$value->acronimo}}</th>
                                      
                                      <!--DISCIPLINA TERMINAL SEM ANTECEDENTE-->
                                     @elseif($disc_terminal->isNotEmpty() && $disc_anterior->isEmpty())  
                                          <th scope='col' colspan='7'class='centro'>{{$value->acronimo}}</th>                
                                      <!--DISCIPLINA DE CONTINUIDADE COM ANTECEDENTE-->
                                     @elseif($disc_terminal->isEmpty() && $disc_anterior->isNotEmpty())     
                                          <th scope='col' colspan='6'class='centro'>{{$value->acronimo}}</th>
                                     @else
                                      <!--DISCIPLINA DE CONTINUIDADE SEM ANTECEDENTE-->            
                                          <th scope='col' colspan='5'class='centro'>{{$value->acronimo}}</th>
                                     @endif  
                                <!--12ª CLASSE-->
                                @elseif($classe->nome == "12ª")
                                     $disc_terminal = $disciplinas_terminais->where("id",$value->id);          
                                     ($disciplinas_terminais);
                                     $disc_terminada_10 = $disc_terminadas_10->where("id",$value->id);          
                                     $disc_terminada_11 = $disc_terminadas_11->where("id",$value->id);          
                                     $disc_terminada_12 = $disc_terminadas_12->where("id",$value->id);          
                                    
                                     $disc_anterior = $disciplinas_11->where("id",$value->id);         
                                     $disc_anterior_10 = $disciplinas_10->where("id",$value->id);          
                                     $disc_anterior_11 = $disciplinas_11->where("id",$value->id);          
                                     $qtd = intVal($disc_anterior_10->count() + $disc_anterior_11->count());

                                     <!--DISCIPLINA TERMINADA-->

                                     @if($disc_terminada_10->isNotEmpty() || $disc_terminada_11->isNotEmpty())      
                                         <th scope='col' colspan='1' rowspan='3' class='centro'><p class='vtexto' style='margin:0px -15px;font-size:11px;'>{{$value->acronimo}}</p></th>    
                                         <!--DISCIPLINA TERMINAL COM ANTECEDENTE-->

                                     @elseif($disc_terminal->isNotEmpty() && $disc_anterior->isNotEmpty())          
                                         @if($qtd == 2)     
                                             <th scope='col' colspan='9'class='centro'>{{$value->acronimo}}</th>
                                         @else              
                                             <th scope='col' colspan='8'class='centro'>{{$value->acronimo}}</th>
                                     @endif     
                                     <!--DISCIPLINA TERMINAL SEM ANTECEDENTE-->
                                     @elseif($disc_terminal->isNotEmpty() && $disc_anterior->isEmpty())     
                                         <th scope='col' colspan='7'class='centro'>{{$value->acronimo}}</th>                    
                                         <!--DISCIPLINA DE CONTINUIDADE COM ANTECEDENTE-->
                                         @elseif($disc_terminal->isEmpty() && $disc_anterior->isNotEmpty()) 

                                         @if($qtd == 2)                      
                                            <th scope='col' colspan='7'class='centro'>{{$value->acronimo}}</th> 
                                         @else                      
                                            <th scope='col' colspan='6'class='centro'>{{$value->acronimo}}</th>
                                         @endif

                                     @else
                                         <!--DISCIPLINA DE CONTINUIDADE SEM ANTECEDENTE-->              
                                         <th scope='col' colspan='5'class='centro'>{{$value->acronimo}}</th>
                                     @endif

                                @elseif($classe->nome == "13ª")
                                      $disc_terminal = $disciplinas_terminais->where("id",$value->id);          
                                      ($disciplinas_terminais);
                                      $disc_terminada_10 = $disc_terminadas_10->where("id",$value->id);          
                                      $disc_terminada_11 = $disc_terminadas_11->where("id",$value->id);          
                                      $disc_terminada_12 = $disc_terminadas_12->where("id",$value->id);          
                                      
                                      $disc_anterior = $disciplinas_12->where("id",$value->id);         
                                      $disc_anterior_10 = $disciplinas_10->where("id",$value->id);          
                                      $disc_anterior_11 = $disciplinas_11->where("id",$value->id);          
                                      $disc_anterior_12 = $disciplinas_12->where("id",$value->id);          
                                      $qtd = intVal($disc_anterior_10->count() + $disc_anterior_11->count() + 
                                      $disc_anterior_11->count());

                                      <!--DISCIPLINA TERMINADA-->
                                      @if($disc_terminada_10->isNotEmpty() || $disc_terminada_11->isNotEmpty() || $disc_terminada_12->isNotEmpty())              
                                          <th scope='col' colspan='1' rowspan='3' class='centro' style='font-size:5px'>{{$value->acronimo}}</th>    
                                          <!--DISCIPLINA TERMINAL COM ANTECEDENTE-->

                                      @elseif($disc_terminal->isNotEmpty() && $disc_anterior->isNotEmpty())         
                                          @if($qtd == 3)           
                                              <th scope='col' colspan='10'class='centro'>{{$value->acronimo}}</th>
                                          @elseif($qtd == 2)               
                                              <th scope='col' colspan='9'class='centro'>{{$value->acronimo}}</th>
                                          @else                      
                                              <th scope='col' colspan='8'class='centro'>{{$value->acronimo}}</th>
                                          @endif

                                          <!--DISCIPLINA TERMINAL SEM ANTECEDENTE-->
                                      @elseif($disc_terminal->isNotEmpty() && $disc_anterior->isEmpty())        
                                        <th scope='col' colspan='7'class='centro'>{{$value->acronimo}}</th>
                                        
                                      <!--DISCIPLINA DE CONTINUIDADE COM ANTECEDENTE-->
                                      @elseif($disc_terminal->isEmpty() && $disc_anterior->isNotEmpty())
                                          @if($qtd == 3)                  
                                              <th scope='col' colspan='8'class='centro'>{{$value->acronimo}}</th> 
                                              @if($qtd == 2)                  
                                                  <th scope='col' colspan='7'class='centro'>{{$value->acronimo}}</th> 
                                              @else                  
                                                  <th scope='col' colspan='6'class='centro'>{{$value->acronimo}}</th>
                                          @endif
                                          @else
                                              <!--DISCIPLINA DE CONTINUIDADE SEM ANTECEDENTE-->                
                                              <th scope='col' colspan='5'class='centro'>{{$value->acronimo}}</th>
                                         @endif    
                                    @endif
                                @endif
                          @endforeach 

                  
                        <th scope='col' rowspan='3' class='centro'><p class='vtexto' style='margin:20px -18px;'>GÊNERO</p></th>  
                        <th scope='col' rowspan='3' class='centro'><p class='vtexto' style='margin:0px -13.5px;'>MÉDIA</p></th>  
                        <th scope='col' rowspan='3' class='centro'>OBS</th>  
                        <th scope='col' rowspan='3' class='centro'>RESUL.</th>  
                    </tr>
                    <tr>      
                        @foreach($listaCabecalho2 as $key => $value)
                            <!--10º CLASSE-->
                            @if($classe->nome == '10ª')  
                                <th scope='col' colspan='2' class='centro'>F</th>  
                                <th scope='col' rowspan='2' class='centro'>CF</th>
                                <th scope='col' rowspan='2' class='centro'>PG</th>
                                <th scope='col' rowspan='2' class='centro'><p style='font-size:11px;' class='vtexto'>CA10ª</p></th>        
                          
                                @if($disciplinas_terminais->contains($value->id))  
                                    <th scope='col' rowspan='2' class='centro'><p style='font-size:11px;margin:0px -15px;' class='vtexto'>EXAME</p></th>
                                    <th scope='col' style='font-size:8px' rowspan='2' class='centro'><p style='font-size:11px;' class='vtexto'>CFD</p></th>        
                                @endif

                            <!--11º CLASSE-->
                            @elseif($classe->nome == '11ª')
                                $disc_terminal = $disciplinas_terminais->where("id",$value->id);          
                                $disc_terminada_10 = $disc_terminadas_10->where("id",$value->id);          
                                $disc_anterior_10 = $disciplinas_10->where("id",$value->id);
                              
                                @if($disc_terminada_10->isNotEmpty())         
                                
                                @elseif($disc_anterior_10->isNotEmpty())      
                                    <th scope='col' style='font-size:5px' rowspan='2' class='centro'><p class='vtexto' style='margin:0px -15px;font-size:11px;'>CA10ª</p></th>        
                                @endif

                                @if($disc_terminada_10->isEmpty())          
                                    <th scope='col' colspan='2' class='centro'>F</th>  
                                    <th scope='col' rowspan='2' class='centro' style='font-size:8px'><p class='vtexto' style='margin:0px -13.5px;'>CF</p></th>
                                    <th scope='col' rowspan='2' class='centro' style='font-size:8px'><p class='vtexto' style='margin:0px -13.5px;'>PG</p></th>
                                    <th scope='col' rowspan='2' class='centro'><p class='vtexto' style='margin:0px -15px;font-size:11px;'>CA$modulo_nome[1]ª</p></th>         
                                @endif
                                @if($disc_terminal->isNotEmpty())    
                                    <th scope='col' rowspan='2' class='centro'><p class='vtexto' style='margin:20px -15px;font-size:11px;'>EXAME</p></th>
                                    <th scope='col'rowspan='2' class='centro'><p class='vtexto' style='margin:0px -15px;font-size:11px;font-size:11px;'>CFD</p></th>          
                                @endif

                            @elseif($classe->nome == '12ª')
                                $disc_terminal = $disciplinas_terminais->where("id",$value->id);          
                                ($disciplinas_terminais);
                                $disc_terminada_10 = $disc_terminadas_10->where("id",$value->id);          
                                $disc_terminada_11 = $disc_terminadas_11->where("id",$value->id);

                                $disc_anterior = $disciplinas_11->where("id",$value->id);         
                                $disc_anterior_10 = $disciplinas_10->where("id",$value->id);          
                                $disc_anterior_11 = $disciplinas_11->where("id",$value->id);          
                                $qtd = intVal($disc_anterior_10->count() + $disc_anterior_11->count());            
                                @if($disc_terminada_10->isNotEmpty() || $disc_terminada_11->isNotEmpty())      
                                @elseif($disc_anterior->isNotEmpty())
                                    @if($qtd == 2)                  
                                       <th scope='col' style='font-size:5px' rowspan='2' class='centro'><p class='vtexto' style='margin:0px -15px;font-size:11px;'>CA10ª</p></th>
                                       <th scope='col' style='font-size:5px' rowspan='2' class='centro'><p class='vtexto' style='margin:0px -15px;font-size:11px;'>CA11ª</p></th>
                                    @elseif($qtd == 1)                
                                        <th scope='col' style='font-size:5px' rowspan='2' class='centro'><p class='vtexto' style='margin:0px -15px;font-size:11px;'>CA11ª</p></th> 
                                    @endif
                                @endif
                                @if($disc_terminada_10->isEmpty() && $disc_terminada_11->isEmpty())
                                    <th scope='col' colspan='2' class='centro'>F</th>  
                                    <th scope='col' rowspan='2' class='centro' style='font-size:8px'><p class='vtexto' style='margin:0px -13.5px;'>CF</p></th>
                                    <th scope='col' rowspan='2' class='centro' style='font-size:8px'><p class='vtexto' style='margin:0px -13.5px;'>PG</p></th>
                                    <th scope='col' rowspan='2' class='centro'><p class='vtexto' style='margin:0px -15px;font-size:11px;'>CA$modulo_nome[1]ª</p></th>             
                                @endif
                                @if($disc_terminal->isNotEmpty())        
                                    <th scope='col' rowspan='2' class='centro'><p class='vtexto' style='margin:20px -15px;font-size:11px;'>EXAME</p></th>
                                    <th scope='col'rowspan='2' class='centro'><p class='vtexto' style='margin:0px -15px;font-size:11px;font-size:11px;'>CFD</p></th>            
                                @endif
                            @elseif($classe->nome == '13ª')
                                $disc_terminal = $disciplinas_terminais->where("id",$value->id);          
                                ($disciplinas_terminais);
                                $disc_terminada_10 = $disc_terminadas_10->where("id",$value->id);          
                                $disc_terminada_11 = $disc_terminadas_11->where("id",$value->id);          
                                $disc_terminada_12 = $disc_terminadas_11->where("id",$value->id);          
                                
                                $disc_anterior = $disciplinas_12->where("id",$value->id);         
                                $disc_anterior_10 = $disciplinas_10->where("id",$value->id);          
                                $disc_anterior_11 = $disciplinas_11->where("id",$value->id);          
                                $disc_anterior_12 = $disciplinas_12->where("id",$value->id);
                                $disciplina_13 = $disciplinas_13->where("id",$value->id);
                                $qtd = intVal($disc_anterior_10->count() + $disc_anterior_11->count() + $disc_anterior_12->count());
                                
                             
                                @if($disc_terminada_10->isNotEmpty() || $disc_terminada_11->isNotEmpty() || 
                                    $disc_terminada_12->isNotEmpty())   
                                @elseif($disc_anterior->isNotEmpty() && $disciplina_13->isNotEmpty())
                                    @if($qtd == 3)              
                                        <th scope='col' style='font-size:7px' rowspan='2' class='centro'>C10</th>
                                        <th scope='col' style='font-size:7px' rowspan='2' class='centro'>C11</th>
                                        <th scope='col' style='font-size:7px' rowspan='2' class='centro'>C12</th>              
                                    @endif
                                    @if($qtd == 2)                
                                        <th scope='col' style='font-size:7px' rowspan='2' class='centro'>C11</th>
                                        <th scope='col' style='font-size:7px' rowspan='2' class='centro'>C12</th>
                                    @elseif($qtd == 1)                
                                        <th scope='col' style='font-size:7px' rowspan='2' class='centro'>C12</th> 
                                    @endif
                                @endif
                                @if((($disc_terminada_10->isEmpty() && $disc_terminada_11->isEmpty()) && 
                                $disc_terminada_12->isEmpty()) && $disciplina_13->isNotEmpty())       
                                    <th scope='col' colspan='2' class='centro'>F</th>  
                                    <th scope='col' rowspan='2' class='centro'>CF</th>
                                    <th scope='col' rowspan='2' class='centro'>PG</th>
                                    <th scope='col' rowspan='2' class='centro' style='font-size:6px'>C$modulo_nome[1]</th>          
                                @endif
                                @if($disc_terminal->isNotEmpty())
                                    <th scope='col' rowspan='2' class='centro'>E</th>
                                    <th scope='col' style='font-size:5px' rowspan='2' class='centro'>CFD</th>        
                                @endif
                            @endif
                        @endforeach    
                  </tr>
                  <tr>
                    @foreach($listaCabecalho2 as $key => $value)
                        @if($classe->nome == '10ª')           
                            <th scope='col' class='centro'>J</th>  
                            <th scope='col' class='centro'>I</th>                         
                        @elseif($classe->nome == '11ª')
                            $disc_terminal = $disciplinas_terminais->where("id",$value->id);          
                            $disc_terminada_10 = $disc_terminadas_10->where("id",$value->id);          
                            $disc_anterior = $disciplinas_10->where("id",$value->id);

                            @if($disc_terminada_10->isNotEmpty())         
                            @else 
                              <th scope='col' class='centro'>J</th>  
                              <th scope='col' class='centro'>I</th>
                            @endif
                        @elseif($classe->nome == '12ª')
                            $disc_terminal = $disciplinas_terminais->where("id",$value->id);          
                            $disc_terminada_10 = $disc_terminadas_10->where("id",$value->id);          
                            $disc_terminada_11 = $disc_terminadas_11->where("id",$value->id);          
                            $disc_anterior = $disciplinas_11->where("id",$value->id);

                            @if($disc_terminada_10->isNotEmpty() || $disc_terminada_11->isNotEmpty())                     
                            @else       
                              <th scope='col' class='centro'>J</th>  
                              <th scope='col' class='centro'>I</th>
                            @endif
                        @elseif($classe->nome == '13ª')
                            $disc_terminal = $disciplinas_terminais->where("id",$value->id);          
                            $disc_terminada_10 = $disc_terminadas_10->where("id",$value->id);          
                            $disc_terminada_11 = $disc_terminadas_11->where("id",$value->id);          
                            $disc_terminada_12 = $disc_terminadas_11->where("id",$value->id);          
                            $disc_anterior = $disciplinas_11->where("id",$value->id);
                            $disciplina_13 = $disciplinas_13->where("id",$value->id);

                            @if($disc_terminada_10->isNotEmpty() || $disc_terminada_11->isNotEmpty() || $disc_terminada_12->isNotEmpty())
                                     
                            @elseif($disciplina_13->isNotEmpty())    
                              <th scope='col' class='centro'>J</th>  
                              <th scope='col' class='centro'>I</th>
                            @else
                            @endif
                        @endif        
                    @endforeach
                  </tr>
              </thead>
              <tbody>
                @foreach($listaModelo['data'] as $key=>$aluno)
                  <?php 
                    $count = 0;    
                    $fundo = '';      
                    $vermelhado = '';                         
                   ?>            
                    @if($aluno['Result'] == 'Desistido' || $aluno['Result'] == 'Suspenso' || $aluno['Result'] == 'Transferido')
                        $aluno['Result']=='Desistido'? $fundo = 'yellow':($aluno['Result']=='Suspenso'? $fundo = 'rgb(255,212,227)' : $fundo ='rgb(247,221,252)');    
                    @endif
                    <?php $ct = 9; ?>
                    <tr style='font-size:10px;background:$fundo;'>
                    @foreach($aluno as $key=>$value)
                       <?php
                          $count++;
                          $cor = 'black';  
                          $key = explode('_', $key);
                        ?>                       
                          @if($key[0] == 'Result')         
                              @if($value != '' & $value == 'Desistido' || $value == 'Suspenso' || $value == 'Transferido' || $value == 'N/Trans.')              
                                  <?php $cor = 'red';  ?>              
                              @elseif($value != '' & $value == 'Trans.')
                                  <?php $cor = 'black';  ?>                
                              @elseif($value != '' & floatval($value) < 10)
                                  <?php $cor = 'red'; ?>                               
                              @endif

                          <td class='ct' style='text-align:center;'>
                              <span style='color:$cor;'>$value</span>
                          </td>          
                          <?php $ct +=3; ?> 
                          @elseif($key[0] == 'Media')
                              <?php floatval($value) < 10 ? $cor = 'red':$cor = 'black';?>
                              <td class='centro'><span style='color:$cor;'>{{$value}}</span></td>          
                          @elseif($key[0] == 'ca')
                              <?php floatval($value) < 10 ? $cor = 'red':$cor = 'black';?>
                              <td class='centro'><span style='color:$cor;'>{{$value}}</span> </td>            
                          @elseif($key[0] == 'pg')
                              <?php floatval($value) < 4 ? $cor = 'red':$cor = 'black';?>                              
                              <td class='centro'><span style='color:$cor;'>{{$value}}</span></td>                    
                          @elseif($key[0] == 'cf')
                              <?php floatval($value) < 6 ? $cor = 'red':$cor = 'black';?>                              
                              <td class='centro'><span style='color:$cor;'>{{$value}}</span></td>
                          @elseif($key[0] == 'cfd')
                              <?php floatval($value) < 10 ? $cor = 'red':$cor = 'black';?>
                              <td class='centro'><span style='color:$cor;'>{{$value}}</span></td>
                          @elseif($key[0] == 'exame')
                              <?php floatval($value) < 10 ? $cor = 'red':$cor = 'black';?>
                              <td class='centro'><span style='color:$cor;'>{{$value}}</span></td>
                          @elseif($key[0] == 'Genero' || $key[0] == 'numero' || $key[0] == 'idade')           
                              <td class='centro'><span>{{$value}}</span></td>
                          @elseif($key[0] == 'ca')                     
                              <td class='centro'><span>{{$value}}</span></td>
                          @elseif($key[0] == 'idmatricula')           
                              <td><span style='font-size:6px;'>{{$value}}</span></td>
                          @elseif($key[0] == 'OBS')           
                              <td><span style='font-size:5px;color:red;'>{{$value}}</span></td>
                          @else
                              <td>{{$value}}</td>
                          @endif

                          
                          @endforeach
                    </tr>    
                @endforeach     
              </tbody>
          </table>
        </div>
    </body>
</html>

  


  

