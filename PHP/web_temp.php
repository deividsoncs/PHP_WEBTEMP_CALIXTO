
<!-- 
PHP WEBTEMP v1.0
Este código realiza a leitura da temperatura através do pino A8 do Arduino Mega 2560
e através do shield Ethernet realiza a persistência da temperatura coletada, aliado
a um script PHP que realiza exibição dos dados.

Desenvolvido por Deividson Calixto da Silva.
BLOG: https://dcalixtoblog.wordpress.com';
 -->
<?php	
	date_default_timezone_set('America/Sao_Paulo');
	// Instancia o objeto PDO
	$pdo = new PDO('mysql:host=10.0.10.3;dbname=arduino_db', 'root', 'senhaBanco1234');
	// executa a instrução SQL
	$consulta = $pdo->query("select * from historico_temp where data between now() - interval 1 day and now() order by 1 desc;");
	$temperaturas = "<br/>";
	$ultimaTemp = 0;
	while ($linha = $consulta->fetch(PDO::FETCH_ASSOC)) {
		if($ultimaTemp == 0){
			$ultimaTemp = $linha['temperatura'];
		}   
   	if($linha['temperatura'] >= 26){
    		$temperaturas .= "<font color=red>{$linha['data']} | {$linha['temperatura']} C</font><br/>";
	 	}else{
	 		$temperaturas .= "<font color=blue>{$linha['data']} | {$linha['temperatura']} C</font><br/>";
	 	}
	}	
?>
<html>
	<head>
		<title>WebTemp - Monitor de Temperatura</title>
		
		<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	   <script type="text/javascript">	   
		   google.charts.load('current', {'packages':['gauge']});
	      google.charts.setOnLoadCallback(drawChart);
	
	      function drawChart() {
	
	        var data = google.visualization.arrayToDataTable([
	          ['Label', 'Value'],
	          ['Temperatura', <?= $ultimaTemp ?>],
	        ]);
	
	        var options = {
	          width: 400, height: 120, max: 40,
	          redFrom: 26, redTo: 40,          
	          minorTicks: 5
	        };
	
	        var chart = new google.visualization.Gauge(document.getElementById('chart_div'));
	
	        chart.draw(data, options);
	
	        
	      }
	    </script>		
		
		
	</head>
<body>
	<h3><b>WebTemp - Monitor Sala dos Servidores:</b></h3>
	<form>
		<input type="button" value="Atualizar" onClick="history.go(0)">
	</form>
	<div id="chart_div" style="width: 400px; height: 120px;"></div>
	<small>obs.: Atualizacao da temperatura &eacute; realizada a cada 30 min</small>
	<br/>
	<strong>Data/Hora------------ |Temperatura</strong>
	<br/>	
	<?php		
		echo $temperaturas;	
	?>
</body>
</html>