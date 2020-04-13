
      <!DOCTYPE html>
      <html>
      <head>
      <title>PAUTA do $epoca->trimestre trimestre da turma $turma->nome</title>
      <link type='text/css' href='css/minipauta.css' rel='stylesheet'>      
      <style>
          body{
              font-family:calibri;
          }
         .vermelhado td{
              color:red;
              border: solid 1px black;
         }         
         .centro {
              text-align:center;
         }
         .vtexto{
             -ms-transform: rotate(270deg); /* IE 9 */
             -webkit-transform: rotate(270deg); /* Safari 3-8 */
              transform: rotate(270deg);
              font-weight:bold;
              font-size:8px;             
              text-align:center;
              margin: 15px -10px;
             
         }
         #cabecalho,#seccao_topo_grupo,#rodape{
              text-transform: uppercase;
              font-size:11px;
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
         #seccao_topo_grupo  #seccao_1{
          float:left;
          width:17%;          
         }
         #seccao_topo_grupo  #seccao_2{
          float:left;
          width:15%;          
         }
         #seccao_topo_grupo  #seccao_3{
          float:left;
          width:35%;          
         }
         #seccao_topo_grupo > div{
          float:left;
          width:24%;          
         }
         #seccao_topo_grupo #seccao_1>p,#seccao_topo_grupo #seccao_5>p{
          margin-bottom:-12px;
          font-size: 10px;
         }
          #seccao_3>p{
            margin-bottom: -6px;
          }
          #seccao_3>p>span{
            margin-right: 5px;
          }
           #rodape{;
              position:relative;          
              top:9%;
           }
           #rodape p{
             margin-top:-11px;
           }
           #rodape > div{
              float:left;
              width:30%;
           }       
           #mytable{
           width:100%;
           border-collapse:collapse;
           font-size:11px;

          }
           #tabela_area{
            background:transparent;
            position:relative;
            top:50px;
          }
          #mytable>th{
            text-align:center;
          }
          th{
            text-align:center;
          }
          th,td{
            border:1px solid;
            padding: -1px;
            padding-left: 1px;
            margin:-300px;        
          }
      </style>
      </head>
      <body onload='atribueCor()'>
  <div style='font-size: 10.5px;'>
   <div id='cabecalho' align='center' style='font-size: 9.5px;font-weight: bold;' class='table-responsive text-uppercase'>
      <p>$instituicao->nome</p>                           
      <p>$instituicao->lema</p>                                       
      <p>ENSINO SECUNDÁRIO TÉCNICO PROFISSIONAL</p>                  
      <p style='color:red;'>MINI-PAUTA</p> 
      <p>CABINDA - ANGOLA</p>                  
    </div>
    <div id='seccao_topo_grupo'>    
    <div id='seccao_1' class='col-md-3'>
        <p>ÁREA DE FORMAÇÃO:$area->nome</p>
        <p>Curso: $curso->nome</p>      
       <p>$classe->nome CLASSE TURMA: $turma_info[2] </p>         
    </div>
    <div id='seccao_2' align='center' class='col-md-6'>
       <p style='font-size:10px;margin-top:13.5px;'>ANO LECTIVO $turma->ano_lectivo</p> 
    </div>
    <div id='seccao_3' align='center' class='col-md-6'>
       <p><span style='font-weight:bold;font-size:10px;'>DISCIPLINA:</span> <span style='color:red;font-size:10px;' >$disciplina->acronimo</span> <span style='font-size:8px;'>($disciplina->nome)</span></p>     
    </div>
    <div id='seccao_3' align='right' class='col-md-6' style='margin-top:3.5px;margin-left:-20px;'>
       <p style='font-size: 9px;'>
        <span style='font-weight: bold;'>Aptos: $aptos%</span>
        <span style='font-weight: bold;'>MÉDIA: $media</span>
      </p> 
      <p style='font-size: 9px;'>    
        <span style='font-weight: bold;'>Nota Max.: $max</span>
        <span style='font-weight: bold;'>Nota Min.: $min</span>
      </p>
    </div>     
    </div>
    </div>
     <div id='tabela_area'>
      <table id='mytable' class='table table-bordered table-xs table-condensed' style='font-size:8px;'>
      <thead>
      <tr style='font-weight: bold;'>
      <th scope='col' rowspan='3'>Nº</th>    
      <th scope='col' rowspan='3' style='text-align:left;font-size:8;'><span style='margin-left:2px;''>NOME COMPLETO</span></th>  
      <th scope='col' rowspan='3' style='text-orientation:vertical);'><p class='vtexto' style='margin:17px -5px;font-size:8px;'>IDADE</p></p></th>
      <th scope='col' rowspan='3'><p class='vtexto' style='margin:17px -8px;font-size:8px;'>FALTAS</p></p></th>";
      if($ano_lectivo < 2019){
          $output .="      
          <th scope='col' colspan='4'>I TRIMESTRE</th>";
      }else{
          $output .="      
          <th scope='col' colspan='3'>I TRIMESTRE</th>";
        }
         $output .="
        <th scope='col' rowspan='3'><p class='vtexto' style='margin:17px -8px;font-size:8px;'>FALTAS</p></p></th>";

      if($ano_lectivo < 2019){
        $output .="      
         <th scope='col' colspan='6'>II TRIMESTRE</th>";
      }else{
        $output .="      
        <th scope='col' colspan='5'>II TRIMESTRE</th>";
      }
       $output .="
      <th scope='col' rowspan='3'><p class='vtexto' style='margin:17px -8px;font-size:8px;'>FALTAS</p></p></th>  
      <th scope='col' colspan='5'>III TRIMESTRE</th>     
      <th scope='col' colspan='5'>CLASSIFICAÇÃO ANUAL</th> 
    </tr>
    <tr>      
      <th scope='col' rowspan='2'>MAC</th>  
      <th scope='col' rowspan='2'>P1</th>";
      if($ano_lectivo < 2019){
        $output .="      
        <th scope='col' rowspan='2'>P2</th>";
      }
        $output .="      
      <th scope='col' rowspan='2'>CT1</th>";
      if($ano_lectivo < 2019){
        $output .="      
        <th scope='col' rowspan='2'>P2</th>";
      }
        $output .="            
       <th scope='col' colspan='2'>AVALIAÇÕES</th>  
      <th scope='col' rowspan='2'>CF2</th>      
      <th scope='col' rowspan='2'>CT1</th>
      <th scope='col' rowspan='2'>CT2</th>
      <th scope='col' colspan='2'>AVALIAÇÕES</th>  
      <th scope='col' rowspan='2'>CF3</th>      
      <th scope='col' rowspan='2'>CT2</th>      
      <th scope='col' rowspan='2'>CT3</th>  
      <th scope='col' rowspan='2'>MTC</th>      
      <th scope='col' rowspan='2'>60%</th>      
      <th scope='col' rowspan='2'>PG</th>      
      <th scope='col' rowspan='2'>40%</th>      
      <th scope='col' rowspan='2'>60% + 40%</th>
      </tr>
      <tr>     
        <th scope='col'>MAC</th>  
        <th scope='col'>P1</th>
        <th scope='col' >MAC</th>  
        <th scope='col' >P1</th>      
      </tr>    
  </thead>
  <tbody>
  	    <?php

        $counter = 0;    
        $counter2 = 0;    
        $fundo = '';
        $font_size = 0;
        
        foreach($listaModelo as $aluno){
          if($aluno->status == 'Desistido'){
            $fundo ='yellow';
          }else{
            $fundo = '';

          }
          if($listaModelo->count() > 45){
              $font_size = 9.8;              

          }else{
              $font_size = 10;
          }
          // $counter2++;
          // if($counter2%2!=0){
          //   $fundo = '#fafafa';
          // }else{
          //   $fundo = '#fff';
          // }
        ?>
        <tr style='font-size: $font_size px;background:$fundo;'>
        <?php
        foreach ($aluno as $key => $value){         

         if(is_numeric($value)){
           $cor = $value < 10 ? 'red':'black';
         if($key == 'id' || 
            $key == 'numero' || 
            $key == 'nome' || 
            $key == 'idade' ||
            $key == 'fnj1' ||
            $key == 'fnj2' ||
            $key == 'fnj3' ||
            $key == 'sessenta' ||
            $key == 'quarenta'
           ){
           $cor = 'black';
        
         }
         if($key == 'sessenta'
           ){
           $cor = $value < 6 ? 'red':'black';
         
         }
         if($key == 'quarenta'
           ){
           $cor = $value < 4 ? 'red':'black';
         
         }
         } 
        if($counter!=0 && $key != 'status' &&  $key !='ca10' &&  $key !='ca11' &&  $key !='ca12'){          
          if($counter == 2){
          ?>
          <td style='background:$fundo'><span style='color:$cor;'>$value</span></td>
          <?php
          }else{
          ?>
          <td style='background:$fundo' class='centro'><span style='color:$cor'>$value</span></td>
          <?php
          }
        }       
        $counter++;             
                 }
        $counter = 0;         
        $output 
        ?>
        </tr>
    <?php
    }
    ?>
  </tbody>
</table>
</div>

<div id='rodape' align='center'>
      <div class='col-md-6 text-uppercase'>
       <p>O(A) PROFESSOR(A)                 
       <p>_________________________________</p> 
       <p>{{$professor->nome}}</p> 
      </div>      
      <div class='col-md-6'>
        CABINDA, {{strftime('%d de %B de %Y', strtotime('today'))}}
      </div>   
      <div class='col-md-6'>
       <p>O SUB-DIRECTOR PEDAGÓGICO</p>
       <p>_________________________________</p>  
       <p>{{$instituicao->director_pedagogico}}</p>
      </div>    
    </body>
</html";