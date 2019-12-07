<div id='tabela_area2'>    
            <table>
                <thead>
                    <tr style='padding:2px;font-weight:bold;'>
                        <th><span style='font-size: 9px;margin-left:5px;'>CURSO</span></th></th>
                        <th><span style='font-size: 9px;margin-left:5px;'>ACRÓNIMO</span></th></th>
                        <th>
                          <span style='font-size: 9px;margin-left:5px;'>COORDENADOR</span>
                        </th>                        
                    </tr>
                </thead>
                <tbody>";
                foreach ($dados['data2'] as $dado2) {
                   $output.="<tr style='text-transform:uppercase;'>"; 
                   foreach ($dado2 as $value) {
                       $output .="                 
                        <td><span style='font-size: 9px;margin-left:5px;'>$value</span></th></td>";
                   }
                        $output.="</tr>";
                }
             $output.="   
                </tbody>
            </table>
        </div>