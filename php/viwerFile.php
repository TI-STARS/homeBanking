<?
  session_start();
  if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
      // Redirecionar para a página de login com mensagem
      header('Location: ../login.html?error=not_logged_in');
      exit(); // Importante para parar a execução
  }
  require '../config.php';
  require '../DBFunctions.php';
  require '../Functions.php';
  $conn = DbConect();

  extract($_GET);
  
  $query = "SELECT 
	            a.typeResumo as [resumoIA],
	            b.grupo as [grupoBem],
	            b.codigo as [codigoBem],
	            b.descricao as [descricaoBem],
	            b.valorAnterior as [valorAnterior],
	            b.valorAtual as [valorAtual],
                b.valorPago as [valorPago]
            FROM stakeholdersProspectFile a 
                INNER JOIN ProspectFileIrBens as b ON a.ID = b.IDFile 
            WHERE a.ID = $IDFile";
  //print($query);
  $result = DBQuery($query, $conn) or die ("Erro: ".DbError($conn)." na query: <pre> $query </pre>");
  
  $i = 0;
  $BenStrtuturados = array();
  while($r = DbFetchAssoc($result)){
    extract($r);
    
    if($grupoBem == 1 ){
        $qtdimoveis++;
        $valorImoveis += $valorAtual;
    }
    elseif($grupoBem == 2){
        $qtdveiculos++;
        $valorVeiculos += $valorAtual;
    }
    elseif($grupoBem == 3){
        $qtdinvestimentos++;
        $valorInvestimentos += $valorAtual;
    }
    elseif($grupoBem == 4){
        $qtcreditos++;
        $valorcreditos += $valorAtual;
    }
    elseif($grupoBem == 5){
        $qtdoutros++;
        $valoroutros += $valorAtual;
    }

    if($grupoBem != 99){
        $BenStrtuturados[$i] = [
            'grupoBem' => $grupoBem,
            'codigoBem' => $codigoBem,
            'descricaoBem' => $descricaoBem,
            'valorAnterior' => $valorAnterior,
            'valorAtual' => $valorAtual
        ];
        $i++;
    }else{
        $onusStrtuturados[$i] = [
            'grupoBem' => $grupoBem,
            'codigoBem' => $codigoBem,
            'descricaoBem' => $descricaoBem,
            'valorAnterior' => $valorAnterior,
            'valorAtual' => $valorAtual,
            'valorPago' => $valorPago
        ];
        $i++;
    }    
  }

  $query = "SELECT * from prospectFileIrDeclarante where IDFile = $IDFile";
  $result = DBQuery($query, $conn) or die ("Erro: ".DbError($conn)." na query: <pre> $query </pre>");
  $r = DbFetchAssoc($result);
  extract($r);

  // print("<pre>");
  // print_r($onusStrtuturados);
  // print("</pre>");

?>

<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../css/style.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <script src='../js/functions.js'></script>
        <title>Visualizar Documento</title>
    </head>
  <body style ='display: block;'>

    <div style="display: flex; background-color: #34495e; color: white; padding: 10px; border-radius: 5px; margin-left: 2%; margin-right: 2%; margin-top: 2%;">
        <span style="font-weight: bold; margin-left: 2%; margin-right: 2%; margin-top: 2%; margin-bottom: 2%;">Resumo da IA</span>
        <span style='margin-left: 2%; margin-top: 2%;'><?=utf8_encode($resumoIA)?></span>
    </div>
    <div style="padding: 10px; border-radius: 5px; margin-left: 2%; margin-right: 2%; margin-top: 2%;">
        <div style="display: flex; align-items: center;">
           <span style="font-weight: bold;margin-left: 2%;">Nome: </span> 
           <span style="margin-left: 0.2%;"><?=$nome?></span> 
           <span style="font-weight: bold;margin-left: 2%;">CPF: </span> 
           <span style="margin-left: 0.2%;"> <?=($cpfCnpj)?> </span> 
           <span style="font-weight: bold;margin-left: 2%;">Data de Nascimento: </span> 
           <span style="margin-left: 0.2%;"> <?= date('d/m/Y', strtotime($dataNascimentoConstituicao))?></span>
        </div> 
    </div>
    
    
    <table style="width: 96%; margin: 0 auto; margin-top: 2%;">
      <thead>
        <tr>
            <th colspan="5" style="text-align: center; border-bottom: 1px solid #fff;">DECLARAÇÃO DE BENS E DIREITOS</th>
        </tr>
        <tr>
          <th style="width: 5%; text-align: center;">Grupo</th>
          <th style="width: 5%; text-align: center;">Código</th>
          <th style="width: 50%; text-align: center;">Descrição</th>
          <th style="width: 10%; text-align: center;">Valor Anterior</th>
          <th style="width: 10%; text-align: center;">Valor Atual</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($BenStrtuturados as $bem) { ?>
          <tr>
            <td  style="width: 5%; text-align: center;"><?=$bem['grupoBem']?></td>
            <td  style="width: 5%; text-align: center;"><?=$bem['codigoBem']?></td>
            <td  style="width: 5%; text-align: left;"> <?=utf8_encode($bem['descricaoBem'])?></td>
            <td  style="width: 5%; text-align: center;"><?='R$ '.formataValor($bem['valorAnterior'])?></td>
            <td  style="width: 5%; text-align: center;"><?='R$ '.formataValor($bem['valorAtual'])?></td>
          </tr>
        <?php 
            $totalValorAnterior += $bem['valorAnterior'];
            $totalValorAtual += $bem['valorAtual'];
            } 
        ?>
        <tfoot>
          <tr>
            <th style="width: 5%; text-align: center;">Grupo</th>
            <th style="width: 5%; text-align: center;">Código</th>
            <th style="width: 50%; text-align: center;">Descrição</th>
            <th style="width: 10%; text-align: center;"><?='R$ '.formataValor($totalValorAnterior)?></th>
            <th style="width: 10%; text-align: center;"><?='R$ '.formataValor($totalValorAtual)?></th>
          </tr>
        </tfoot>
      </tbody>
    </table>
    <br>
    <div>
        <table style="width: 96%; margin: 0 auto; margin-top: 2%;">
            <thead>
                <tr>
                    <th colspan="5" style="text-align: center; border-bottom: 1px solid #fff;">DECLARAÇÃO DE OBRIGAÇÕES</th>
                </tr>
                <tr>
                    <th style="width: 5%; text-align: center;">Código</th>
                    <th style="width: 5%; text-align: center;">Descrição</th>
                    <th style="width: 5%; text-align: center;">Valor Anterior</th>
                    <th style="width: 5%; text-align: center;">Valor Atual</th>
                    <th style="width: 5%; text-align: center;">Valor Pago</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($onusStrtuturados as $onus) { ?>
                    <tr>
                        <td style="width: 10%; text-align: center;"><?=$onus['codigoBem']?></td>
                        <td style="width: 50%; text-align: center;"><?=utf8_encode($onus['descricaoBem'])?></td>
                        <td style="width: 10%; text-align: center;"><?='R$ '.formataValor($onus['valorAnterior'])?></td>
                        <td style="width: 10%; text-align: center;"><?='R$ '.formataValor($onus['valorAtual'])?></td>
                        <td style="width: 10%; text-align: center;"><?='R$ '.formataValor($onus['valorPago'])?></td>
                    </tr>
                <?php 
                    $totalOnusValorPago += $onus['valorPago'];
                    $totalOnusValorAtual += $onus['valorAtual'];
                    $totalOnusValorAnterior += $onus['valorAnterior'];
                    } 
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <th>Código</th>
                    <th>Descrição</th>
                    <th><?='R$ '.formataValor($totalOnusValorAnterior)?></th>
                    <th><?='R$ '.formataValor($totalOnusValorAtual)?></th>
                    <th><?='R$ '.formataValor($totalOnusValorPago)?></th>
                </tr>
            </tfoot>
        </table>
    </div>
    <div>
        <table style="width: 96%; margin: 0 auto; margin-top: 2%;">
            <thead>
                <tr>
                    <th>Grupo</th>
                    <th>Quantidade</th>
                    <th>Valor</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td> Imóveis</td>
                    <td><?=$qtdimoveis?></td>
                    <td><?='R$ '.formataValor($valorImoveis)?></td>
                </tr>
                <tr>
                    <td> Veículos</td>
                    <td><?=$qtdveiculos?></td>
                    <td><?='R$ '.formataValor($valorVeiculos)?></td>
                </tr>
                <tr>
                    <td> Investimentos</td>
                    <td><?=$qtdinvestimentos?></td>
                    <td><?='R$ '.formataValor($valorInvestimentos)?></td>
                </tr>
                <tr>
                    <td> Créditos</td>
                    <td><?=$qtcreditos?></td>
                    <td><?='R$ '.formataValor($valorcreditos)?></td>
                </tr>
                <tr>
                    <td> Outros</td>
                    <td><?=$qtdoutros?></td>
                    <td><?='R$ '.formataValor($valoroutros)?></td>
                </tr>
            </tbody>
        </table>
    </div>
  </body>
</html>

