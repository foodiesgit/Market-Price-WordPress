<?php
global $wpdb;

//don't allow direct access via url
if ( ! defined('ABSPATH') ) {
    exit();
}
?>

<div class="wrap">
	<input type="button" class="btn clearAll-yum" value="Apagar Eventos">		

    <h2><?php esc_attr_e('Inserir Dados Automaticamente', 'betpress'); ?></h2>
    
    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST">
    
    <table class="form-table">

        <tr valign="top">

            <th scope="row"><?php esc_attr_e('Atenção', 'betpress'); ?></th>

            <td class="help-info">
                
                <?php esc_attr_e('Esta página pode precisar de vários segundos para carregar devido à grande quantidade de dados.', 'betpress'); ?>

            </td>

        </tr>

        <tr valign="top">

            <th scope="row">

                <label for="sports-dropdown"><?php esc_attr_e('Escolher um esporte', 'betpress'); ?></label>

            </th>

            <td>
                
                <select name="sport_id" id="sports-dropdown">
                    
                    <option value="0"><?php esc_attr_e('Selecionar esporte', 'betpress'); ?></option>
                
                <?php foreach ($xml_data as $sport => $sport_data): ?>
                    
                    <option value="<?php echo $sport_data['id']; ?>"><?php echo $sport; ?></option>
                
                <?php endforeach; ?>
                    
                </select>

            </td>

        </tr>
        
        <tr valign="top">

            <th scope="row">

                <label for="auto-activate"><?php esc_attr_e('Ativar apostas automaticamente', 'betpress'); ?></label>

            </th>

            <td>
                    
                <input 
                    type="checkbox"
                    id="auto-activate"
                    name="auto_activate"
                    value="1"
                />

                <span class="help-info"><?php esc_attr_e('Se marcado, você não precisa ativar manualmente todos os eventos da aposta.', 'betpress'); ?></span>

            </td>

        </tr>
        
        
        <tr valign="top">

            <th scope="row">

 <script type="text/javascript">
			function selectAll(){
				var items=document.getElementsByName('events[]');
				for(var i=0; i<items.length; i++){
					if(items[i].type=='checkbox')
						items[i].checked=true;
				}
				
				var items123=document.getElementsByName('test_events[]');
				for(var i=0; i<items123.length; i++){
					if(items123[i].type=='checkbox')
						items123[i].checked=true;
				}
        var items2=document.getElementsByName('bet_events[]');
				for(var i2=0; i2<items2.length; i2++){
					if(items2[i2].type=='checkbox')
						items2[i2].checked=true;
				}
  var items3=document.getElementsByName('categories[]');
				for(var i3=0; i3<items3.length; i3++){
					if(items3[i3].type=='checkbox')
						items3[i3].checked=true;
				}
			}

			function UnSelectAll(){
				var items4=document.getElementsByName('events[]');
				for(var i4=0; i4<items4.length; i4++){
					if(items4[i4].type=='checkbox')
						items4[i4].checked=false;
				}
        var items5=document.getElementsByName('bet_events[]');
				for(var i5=0; i5<items5.length; i5++){
					if(items5[i5].type=='checkbox')
						items5[i5].checked=false;
				}
  var items6=document.getElementsByName('categories[]');
				for(var i6=0; i6<items6.length; i6++){
					if(items6[i6].type=='checkbox')
						items6[i6].checked=false;
				}
			}
		</script>
        <input type="button" onclick='selectAll()' value="Select All"/>
		<input type="button" onclick='UnSelectAll()' value="Unselect All"/>
<input type="submit" name="inserting_xml_data" value="<?php esc_attr_e('Inserir', 'betpress'); ?>" class="button-primary" />               		
				
		<?php esc_attr_e('Escolher eventos', 'betpress'); ?>

            </th>

            <td>
                
<?php foreach ($xml_data as $sport_name => $events): ?>
                
    <?php if (count($events) === 1): ?>
                
                <div class="sport-<?php echo $events['id']; ?>" style="display:none;">

                    <?php esc_attr_e('Não há nenhum evento disponível para esta modalidade no momento.', 'betpress'); ?>

                </div>
                
    <?php endif; ?>
                
    <?php if (is_array($events)): ?>
                <?php 
				foreach($events as $kp => $sp){
				foreach($events[$kp] as $ku => $vl){					
		  unset($events[$kp][$ku]['Oportunidade dupla - 2.ª parte']);					
		  unset($events[$kp][$ku]['Resultado sem empate ao intervalo']);					
		  unset($events[$kp][$ku]['Empate Anula Aposta ao intervalo']);					
		  unset($events[$kp][$ku]['Resultado ao intervalo/final']);					
		  unset($events[$kp][$ku]['Total de pontos handicap']);					
		  unset($events[$kp][$ku]['Total de pontos no 1º set']);					
		  unset($events[$kp][$ku]['1.º marcador']);					
		  unset($events[$kp][$ku]['Último marcador']);
		  unset($events[$kp][$ku]['Totais do Jogo handicap']);
		  unset($events[$kp][$ku]['Handicap de sets']);
		  unset($events[$kp][$ku]['Totais do Jogo no 1º set']);
		  unset($events[$kp][$ku]['Vencedor da competição']);
		  unset($events[$kp][$ku]['Vencedor da série']);
		  unset($events[$kp][$ku]['Handicap de jogos']);
		  unset($events[$kp][$ku]['Resultado correcto do 1.º set']);
		  unset($events[$kp][$ku]['Resultado correcto do 2.º set']);					
		  unset($events[$kp][$ku]['Vencedor do grupo']);					
		  unset($events[$kp][$ku]['1.º a 3.º lugar']);								
		  unset($events[$kp][$ku]['Total de golos da equipa visitante']);
		  unset($events[$kp][$ku]['Total de golos da equipa caseira']);
		  unset($events[$kp][$ku]['Resultado duplo no 1.º período']);
          unset($events[$kp][$ku]['Resultado duplo no 2.º período']);
		  unset($events[$kp][$ku]['Resultado duplo no 3.º período']);
		  unset($events[$kp][$ku]['Total de golos no 1.º período']);
		  unset($events[$kp][$ku]['Total de golos no 2.º período']);
		  unset($events[$kp][$ku]['Total de golos no 3.º período']);
		  unset($events[$kp][$ku]['Hipótese Dupla no 1.º período']);
		  unset($events[$kp][$ku]['Hipótese Dupla no 2.º período']);
		  unset($events[$kp][$ku]['Hipótese Dupla no 3.º período']);
		  unset($events[$kp][$ku]['1.º a 4.º lugar']);
		  unset($events[$kp][$ku]['Despromoção']);
		  unset($events[$kp][$ku]['Vencedor da conferência']);
          unset($events['Copa do Mundo Div. 1A'][$ku]['Vencedor']);
          unset($events['Virtual Premier League'][$ku]['Resultado correcto']);
          unset($events['Virtual Premier League'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Portugal Campeonato de Portugal'][$ku]['Resultado correcto']);
          unset($events['Portugal Campeonato de Portugal'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Portugal - Liga Sub-23'][$ku]['Resultado correcto']);
          unset($events['Portugal - Liga Sub-23'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Vietname - V-League'][$ku]['Resultado correcto']);
          unset($events['Vietname - V-League'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Palestina - West Bank League'][$ku]['Resultado correcto']);
          unset($events['Palestina - West Bank League'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Inglaterra - Superliga F.'][$ku]['Resultado correcto']);
          unset($events['Inglaterra - Superliga F.'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Bélgica - Super Liga F.'][$ku]['Resultado correcto']);
          unset($events['Bélgica - Super Liga F.'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Alemanha - Liga Regional Bavaria'][$ku]['Resultado correcto']);
          unset($events['Alemanha - Liga Regional Bavaria'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Virtual LaLiga'][$ku]['Resultado correcto']);
          unset($events['Virtual LaLiga'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Virtual Serie A'][$ku]['Resultado correcto']);
          unset($events['Virtual Serie A'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Virtual Ligue 1'][$ku]['Resultado correcto']);
          unset($events['Virtual Ligue 1'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Virtual Turquia - Superliga'][$ku]['Resultado correcto']);
          unset($events['Virtual Turquia - Superliga'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Virtual Bundesliga'][$ku]['Resultado correcto']);
          unset($events['Virtual Bundesliga'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Virtual Rússia - Premier Liga'][$ku]['Resultado correcto']);
          unset($events['Virtual Rússia - Premier Liga'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Macedónia do Norte - Taça'][$ku]['Resultado correcto']);
          unset($events['Macedónia do Norte - Taça'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Itália - Primavera'][$ku]['Resultado correcto']);
          unset($events['Itália - Primavera'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Singapura - S-League'][$ku]['Resultado correcto']);
          unset($events['Singapura - S-League'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Malásia - Super League'][$ku]['Resultado correcto']);
          unset($events['Malásia - Super League'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Austrália - New South Wales - Premier League'][$ku]['Resultado correcto']);
          unset($events['Austrália - New South Wales - Premier League'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Noruega - Div. 2 Avdeling 1'][$ku]['Resultado correcto']);
          unset($events['Noruega - Div. 2 Avdeling 2'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Albânia - Taça'][$ku]['Resultado correcto']);
          unset($events['Albânia - Taça'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Bósnia - Taça'][$ku]['Resultado correcto']);
          unset($events['Bósnia - Taça'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Camp. AFC Sub-23'][$ku]['Resultado correcto']);
          unset($events['Camp. AFC Sub-23'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Espanha - Primera F.'][$ku]['Resultado correcto']);
          unset($events['Espanha - Primera F.'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Bangladesh - Premier League'][$ku]['Resultado correcto']);
          unset($events['Bangladesh - Premier League'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Nicaragua - Primera División'][$ku]['Resultado correcto']);
          unset($events['Nicaragua - Primera División'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Camp. AFC Sub-23'][$ku]['Resultado correcto']);
          unset($events['Camp. AFC Sub-23'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Turquia - Lig 3 Group 1'][$ku]['Resultado correcto']);
          unset($events['Turquia - Lig 3 Group 1'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Turquia - Lig 3 Group 2'][$ku]['Resultado correcto']);
          unset($events['Turquia - Lig 3 Group 2'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Turquia - Lig 3 Group 3'][$ku]['Resultado correcto']);
          unset($events['Turquia - Lig 3 Group 3'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Nova Zelândia - 1.ª Divisão'][$ku]['Resultado correcto']);
          unset($events['Nova Zelândia - 1.ª Divisão'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Azerbaijão - 1.ª Liga'][$ku]['Resultado correcto']);
          unset($events['Azerbaijão - 1.ª Liga'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Roménia - Taça'][$ku]['Resultado correcto']);
          unset($events['Roménia - Taça'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Roménia - 2.ª Liga'][$ku]['Resultado correcto']);
          unset($events['Roménia - 2.ª Liga'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Costa Rica - 1.ª Divisão'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Bahrein - Liga'][$ku]['Resultado correcto']);
          unset($events['Bahrein - Liga'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Itália - Serie C Girone A'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Itália - Serie C Girone B'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Itália - Serie C Girone C'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Espanha - Seg. B Gr. 1'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Espanha - Seg. B Gr. 2'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Espanha - Seg. B Gr. 3 '][$ku]['Resultado correcto ao intervalo']);
          unset($events['Espanha - Seg. B Gr. 4'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Espanha - Seg. B Gr. 5'][$ku]['Resultado correcto ao intervalo']);	
          unset($events['Israel - Premier League F.'][$ku]['Resultado correcto']);					
          unset($events['Israel - Premier League F.'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Malta - Prem. League'][$ku]['Resultado correcto']);
          unset($events['Malta - Prem. League'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Camp. Africano Nações'][$ku]['Resultado correcto']);
          unset($events['Camp. Africano Nações'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Argentina - Primera B Nacional'][$ku]['Resultado correcto']);
          unset($events['Argentina - Primera B Nacional'][$ku]['Resultado correcto ao intervalo']);
          unset($events['África do Sul - 1.ª Liga'][$ku]['Resultado correcto']);
          unset($events['África do Sul - 1.ª Liga'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Zambian Premier League'][$ku]['Resultado correcto']);
          unset($events['Zambian Premier League'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Inglaterra - Premier Lg. 2'][$ku]['Resultado correcto']);
          unset($events['Inglaterra - Premier Lg. 2'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Quénia - Premier League'][$ku]['Resultado correcto']);
          unset($events['Quénia - Premier League'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Nigéria - Premier League'][$ku]['Resultado correcto']);
          unset($events['Nigéria - Premier League'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Austrália - W-League'][$ku]['Resultado correcto']);
          unset($events['Austrália - W-League'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Gana - Premier League'][$ku]['Resultado correcto']);
          unset($events['Gana - Premier League'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Alemanha - Liga Regional Sudoeste'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Alemanha - Reg. Oeste'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Omã - Pro Liga'][$ku]['Resultado correcto']);
          unset($events['Omã - Pro Liga'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Irão - Liga Pro'][$ku]['Resultado correcto']);
          unset($events['Irão - Liga Pro'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Omã - Pro Liga'][$ku]['Resultado correcto']);
          unset($events['Omã - Pro Liga'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Inglaterra - National'][$ku]['Resultado correcto']);
          unset($events['Inglaterra - National'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Índia - I-League'][$ku]['Resultado correcto']);
          unset($events['Índia - I-League'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Kuwait - Premier League'][$ku]['Resultado correcto']);
          unset($events['Kuwait - Premier League'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Argélia - 1.ª Divisão'][$ku]['Resultado correcto']);
          unset($events['Argélia - 1.ª Divisão'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Polónia - 1.ª Liga'][$ku]['Resultado correcto']);
          unset($events['Polónia - 1.ª Liga'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Polónia - 2.ª Liga'][$ku]['Resultado correcto']);
          unset($events['Polónia - 2.ª Liga'][$ku]['Resultado correcto ao intervalo']);
          unset($events['Polónia - Taça'][$ku]['Resultado correcto']);
          unset($events['Polónia - Taça'][$ku]['Resultado correcto ao intervalo']);
          unset($events['França - Taça F.'][$ku]['Resultado correcto']);
          unset($events['França - Taça F.'][$ku]['Resultado correcto ao intervalo']);					
          unset($events['França - National'][$ku]['Resultado correcto']);
          unset($events['França - National'][$ku]['Resultado correcto ao intervalo']);					
          unset($events['Portugal - Taça'][$ku]['Resultado correcto']);
          unset($events['Portugal - Taça'][$ku]['Resultado correcto ao intervalo']);					
          unset($events['Nova Zelândia - 1.ª Divisão'][$ku]['Resultado correcto']);
          unset($events['Nova Zelândia - 1.ª Divisão'][$ku]['Resultado correcto ao intervalo']);					
          unset($events['Turquia - 1.ª Liga'][$ku]['Resultado correcto']);
          unset($events['Turquia - 1.ª Liga'][$ku]['Resultado correcto ao intervalo']);				
												
		  if ($sport_name == "Hóquei no Gelo") {
		  unset($events[$kp][$ku]['Resultado correcto']);								  
		  unset($events[$kp][$ku]['1.ª equipa a marcar']);								  
		  }
		  if ($sport_name == "Arqueado") {
		  unset($events[$kp][$ku]['Resultado']);								  
		  }
		  if ($sport_name == "Floorball") {
		  unset($events[$kp][$ku]['Resultado']);								  
		  unset($events[$kp][$ku]['Total Golos']);								  
		  }
		  if ($sport_name == "Hóquei em Patins") {
		  unset($events[$kp][$ku]['Resultado do jogo']);								  
		  unset($events[$kp][$ku]['Golos']);								  
		  }					
				}
				}
				//echo '<pre>';print_r($events);exit; 
				?>
<?php foreach ($events as $event_name => $bet_events): ?>
<?php if ('id' != $event_name): 
$event_name = str_replace("Brasil - ", "Brasil - Campeonato ", $event_name);
$event_name = str_replace("Brasil - Campeonato Copa", "Copa do Brasil", $event_name);
$event_name = str_replace("Brasil - Campeonato NBB", "Brasil - NBB", $event_name);
$event_name = str_replace("Taça MX", "Taça do México", $event_name);
$event_name = str_replace("Inglaterra - Premier Lg.", "Inglaterra - Premier League", $event_name);
$event_name = str_replace("Argentina - Primera", "Argentina - Superliga", $event_name);
$event_name = str_replace("Argentina - Superliga B Metro", "Argentina - Primera B Metropolitana", $event_name);
$event_name = str_replace("Argentina - Superliga C Metro", "Argentina - Primera C Metropolitana", $event_name);
$event_name = str_replace("Brasil Piauiense", "Brasil - Campeonato Piauiense", $event_name);
$event_name = str_replace("Inglaterra - Superliga F.", "Inglaterra - Superliga Feminina", $event_name);
$event_name = str_replace("Roménia - Taça", "Copa da Roménia", $event_name);
$event_name = str_replace("Hungria - Taça", "Copoa da Hungria", $event_name);
$event_name = str_replace("França - Ligue 1 F.", "França - 1ª Divisão - Mulheres", $event_name);
$event_name = str_replace("Holanda - Taça", "Copa da Holanda", $event_name);
$event_name = str_replace("França - Taça", "Copa da França", $event_name);
$event_name = str_replace("Itália - Taça", "Coppa Italia", $event_name);
$event_name = str_replace("Dinamarca - Taça", "Taça da Dinamarca", $event_name);
$event_name = str_replace("Rússia - Taça", "Copa da Rússia", $event_name);
$event_name = str_replace("Suécia - Taça", "Copa da Suécia", $event_name);
$event_name = str_replace("Polónia - Taça", "Copa da Polónia", $event_name);
$event_name = str_replace("Rússia - Premier Lg.", "Rússia - Premier League", $event_name);
$event_name = str_replace("Argentina - Superliga B Nacional", "Argentina - Primera B Nacional", $event_name);
$event_name = str_replace("Áustria - Taça", "Copa da Áustria", $event_name);
$event_name = str_replace("Bolívia - Liga", "Bolívia - Divisão Profissional", $event_name);
$event_name = str_replace("Bahrein - Liga", "Bahrein - Primeira Liga", $event_name);
$event_name = str_replace("Espanha - Segunda", "Espanha - Segunda Liga", $event_name);
$event_name = str_replace("Copa do Brasil do Nordeste", "Brasil - Copa do Nordeste", $event_name);
$event_name = str_replace("Espanha - Seg. B Gr.", "Espanha - Segunda Divisão B - Grupo", $event_name);
$event_name = str_replace("Cameroon", "Camarões -", $event_name);
$event_name = str_replace("El Salvador - Primera", "El Salvador - Primera Divisão", $event_name);
$event_name = str_replace("Equador - Primera A", "Equador - Liga Pro", $event_name);
$event_name = str_replace("Alemanha - Bundesliga F.", "Alemanha - Bundesliga Feminina", $event_name);
$event_name = str_replace("Irl. do Norte -", "Irlanda do Norte -", $event_name);
$event_name = str_replace("Malta - Prem. League", "Malta - Premier League", $event_name);
$event_name = str_replace("Itália - Serie C", "Itália - Serie C -", $event_name);
$event_name = str_replace("Sub-19", "Sub19", $event_name);
$event_name = str_replace("Sub-20", "Sub20", $event_name);
$event_name = str_replace("Sub-21", "Sub21", $event_name);
$event_name = str_replace("Sub-23", "Sub23", $event_name);
$event_name = str_replace("Uruguai - Primera", "Uruguai - Primera Divisão", $event_name);
$event_name = str_replace("Montenegro - 1. CFL", "Montenegro - Liga Prva Crnogorska", $event_name);
$event_name = str_replace("Gibraltar - Premier", "Gibraltar - Divisão Premier", $event_name);
$event_name = str_replace("Eslovénia - 1.ª Liga", "Eslovénia - Liga Prva", $event_name);
$event_name = str_replace("Eslováquia - 1.ª Liga", "Eslováquia - Fortuna Liga", $event_name);
$event_name = str_replace("Índia - Super Liga", "Índia - ISL", $event_name);
$event_name = str_replace("Bulgária - 1.ª Liga", "Bulgária - Parva Liga", $event_name);
$event_name = str_replace("Bélgica - Divisão 1A", "Bélgica - Liga Jupiler", $event_name);
$event_name = str_replace("Bélgica - Divisão 1B", "Bélgica - Proximus League", $event_name);
$event_name = str_replace("Bélgica - Super Liga F.", "Bélgica - Superliga Feminina", $event_name);
$event_name = str_replace("Vietname - V-League", "Vietnã - V-Liga", $event_name);
$event_name = str_replace("EAU - 1.ª Liga", "Emirados Árabes Unidos - Liga UAE", $event_name);
$event_name = str_replace("Alemanha - 3. Liga", "Alemanha - Bundesliga 3", $event_name);
$event_name = str_replace("Japão - Taça da Liga", "Japão - Taça J-League", $event_name);
$event_name = str_replace("Alemanha - Liga Regional Bavaria", "Alemanha - Regionalliga - Baviera", $event_name);
$event_name = str_replace("Gales - FA Cup", "País de Gales - Copa FA", $event_name);
$event_name = str_replace("Argentina - Copa", "Copa da Argentina", $event_name);
$event_name = str_replace("Taça dos Libertadores", "Copa Libertadores", $event_name);
$event_name = str_replace("Espanha - Segunda Liga Divisão B", "Espanha - Segunda Divisão B", $event_name);
$event_name = str_replace("Polónia - 3.ª Liga Gr.", "Polónia - 3.ª Liga - Grupo ", $event_name);
$event_name = str_replace("Turquia - Lig 3 Group", "Turquia - 3 Lig - Grupo", $event_name);
$event_name = str_replace("F.", "- Feminino", $event_name);
$event_name = str_replace("Chipre - Taça Int. Chipre - Feminino", "Internacional - Feminino", $event_name);
$event_name = str_replace("Espanha - Segunda Liga Divisão B", "Espanha - Segunda Divisão B", $event_name);
$event_name = str_replace("Copa da Itália Primavera", "Itália - Taça Primavera", $event_name);
$event_name = str_replace("Omã - Taça", "Omã - Sultans Cup", $event_name);
$event_name = str_replace("Omã - Pro Liga", "Liga de Omã", $event_name);
$event_name = str_replace("Aus.", "Austrália -", $event_name);
$event_name = str_replace("Chile - 1.ª Divisão", "Chile - Primeira Divisão", $event_name);
$event_name = str_replace("Chile - 2.ª Divisão", "Chile - Primera B", $event_name);
$event_name = str_replace("Gales - Premier League", "País de Gales - Premier League", $event_name);
$event_name = str_replace("Escócia - League 1", "Escócia - League One", $event_name);
$event_name = str_replace("Escócia - League 2", "Escócia - League Two", $event_name);
$event_name = str_replace("Estónia - 2.ª Divisão", "Estônia - Esiliiga", $event_name);
$event_name = str_replace("Estónia - Meistriliiga", "Estônia - Meistriliiga", $event_name);
$event_name = str_replace("Áustria - 1.ª Divisão", "Áustria - 2.Liga", $event_name);
$event_name = str_replace("Croácia - 1.ª Divisão", "Croácia - 1.HNL", $event_name);
$event_name = str_replace("Croácia - 2.ª Divisão", "Croácia - 2.HNL", $event_name);
$event_name = str_replace("Portugal - Primeira Liga", "Portugal - Liga Zon Sagres", $event_name);
$event_name = str_replace("Portugal League Sub23", "Portugal - Liga de Sub23", $event_name);
$event_name = str_replace("Rep. Checa - 1.ª Liga", "Republica Tcheca - First League", $event_name);
$event_name = str_replace("Rep. Checa - 2.ª Divisão", "Republica Tcheca - 2. Liga", $event_name);
$event_name = str_replace("CAF - Liga Campeões", "CAF - Liga Campeões Africanas", $event_name);
$event_name = str_replace("Rússia - 1.ª Divisão", "Rússia - Divizia I", $event_name);
$event_name = str_replace("Peru - 1.ª Divisão", "Peru - Primera División", $event_name);
$event_name = str_replace("Paraguai - 1.ª Division", "Paraguai - Division", $event_name);
$event_name = str_replace("Ucrânia - 1.ª Liga", "Ucrânia - Vyscha Liga", $event_name);
$event_name = str_replace("EAU - Arabian Gulf Cup", "EAU - Taça do Golfo Arábico", $event_name);
$event_name = str_replace("Azerbaijão - 1.ª Divisão", "Azerbaidjão - Division 1", $event_name);
$event_name = str_replace("Alemanha - Taça", "Alemanha - DFB Pokal", $event_name);
$event_name = str_replace("Alemanha - Liga Regional", "Alemanha - Liga Regional -", $event_name);
$event_name = str_replace("Alemanha - Reg. Oeste", "Alemanha - Liga Regional - Oeste", $event_name);
$event_name = str_replace("Alemanha - Region. Norte", "Alemanha - Liga Regional - Norte", $event_name);
$event_name = str_replace("Áustria - 2.Liga", "Áustria - Bundesliga 2", $event_name);
$event_name = str_replace("Bielorrússia - Taça", "Taça da Bielorrússia", $event_name);
$event_name = str_replace("Espanha - Taça do Rei", "Espanha - Copa do Rei", $event_name);
$event_name = str_replace("Copa da França da Liga", "França - Copa da Liga", $event_name);
$event_name = str_replace("Eslováquia - Taça", "Copa da Eslováquia", $event_name);
$event_name = str_replace("Rep. Checa - Taça", "Copa da República Tcheca", $event_name);
$event_name = str_replace("Camp. Europa - Sub19 - Q.", "Campeonato Europeu - Sub-19 - Classificação", $event_name);
$event_name = str_replace("Camp. Europa Sub21 - Qual.", "Campeonato Europeu - Sub-21 - Classificação", $event_name);
$event_name = str_replace("Camp. Europa - Qual.", "Euro 2020 - Classificação", $event_name);
$event_name = str_replace("Bélgica - Taça", "Copa da Bélgica", $event_name);
$event_name = str_replace("Colômbia - Copa", "Copa da Colômbia", $event_name);
$event_name = str_replace("Taça Sul-Americana", "Copa Sul-Americana", $event_name);
$event_name = str_replace("Croácia - 1.HNL", "Croácia - Liga 1.HNL", $event_name);
$event_name = str_replace("Amigáveis - Selecções", "Partidas Internacionais", $event_name);
$event_name = str_replace("Austrália - Queensland PL", "Austrália - NPL Queensland", $event_name);
$event_name = str_replace("Bulgária - Taça", "Copa da Bulgária", $event_name);
$event_name = str_replace("Chile - Taça", "Copa do Chile", $event_name);
$event_name = str_replace("Túnisia - Taça", "Copa da Túnisia", $event_name);
$event_name = str_replace("Camp. AFC Sub-22", "Copa da Ásia AFC Sub-23", $event_name);
$event_name = str_replace("Camp. AFC Sub-23", "Copa da Ásia AFC Sub-23", $event_name);
$event_name = str_replace("Turquia - Taça", "Copa da Turquia", $event_name);
$event_name = str_replace("Albânia - Taça", "Copa Albânia", $event_name);
$event_name = str_replace("Bósnia - Taça", "Copa da Bósnia", $event_name);
$event_name = str_replace("Chipre - Taça", "Copa da Chipre", $event_name);
$event_name = str_replace("Grécia - Taça", "Copa da Grécia", $event_name);
$event_name = str_replace("Israel - Taça", "Taça de Israel", $event_name);
$event_name = str_replace("Portugal - Taça", "Copa de Portugal", $event_name);
$event_name = str_replace("Irlanda - League Cup", "Irlanda - Copa da Liga", $event_name);
$event_name = str_replace("Paraguai - Division", "Paraguai - Division Profesional", $event_name);
$event_name = str_replace("CONCACAF - Liga dos Campeões", "Liga dos Campeões da CONCACAF", $event_name);
$event_name = str_replace("Japão - J-League 2", "Japão - Liga J2", $event_name);
$event_name = str_replace("Japão - J-League", "Japão - Liga J", $event_name);
$event_name = str_replace("Cazaquistão - 1.ª Liga", "Cazaquistão - Premier League", $event_name);
$event_name = str_replace("Noruega - Taça", "Copa da Noruega", $event_name);
$event_name = str_replace("Croácia - Taça", "Copa da Croácia", $event_name);
$event_name = str_replace("Ucrânia - Taça", "Copa da Ucrânia", $event_name);
$event_name = str_replace("Irlanda do Norte - Taça", "Copa da Irlanda do Norte", $event_name);
$event_name = str_replace("Islândia - Taça", "Copa da Islândia", $event_name);
$event_name = str_replace("Alemanha - Bundesliga 2", "Alemanha - Bundesliga II", $event_name);
$event_name = str_replace("Camp. do Mundo", "Copa do Mundo", $event_name);
$event_name = str_replace("Suécia - Div. 1 Norte", "Suécia - 1ª Divisão Norte", $event_name);
$event_name = str_replace("Suécia - Div. 1 Sul", "Suécia - 1ª Divisão Sul", $event_name);
$event_name = str_replace("Suécia - 2.ª Div. Norra Götaland", "Suécia - 2ª Divisão - Norra Götaland", $event_name);
$event_name = str_replace("Suécia - 2.ª Div. Västra Götaland", "Suécia - 2ª Divisão - Västra Götaland", $event_name);
$event_name = str_replace("Suécia - 2.ª Div. Östra Götaland", "Suécia - 2ª Divisão - Östra Gøtaland", $event_name);	$event_name = str_replace("Brasil - Campeonato Série", "Brasil - Série", $event_name);
$event_name = str_replace("Inglaterra - FA Cup", "Inglaterra - Taça FA", $event_name);
$event_name = str_replace("Espanha - Copa do Rei", "Espanha - Taça do Rei", $event_name);				
$event_name = str_replace("Cazaquistão - Taça", "Copa do Cazaquistão", $event_name);				
$event_name = str_replace("Arménia - Copa", "Copa da Armênia", $event_name);				
$event_name = str_replace("Copa Albânia", "Copa da Albânia", $event_name);				
$event_name = str_replace("Coppa Italia Serie C", "Copa da Itália - Serie C", $event_name);				
$event_name = str_replace("Estónia - Taça", "Copa da Estónia", $event_name);				
$event_name = str_replace("Campeonato do Mundo - Feminino", "Copa do Mundo - Feminino", $event_name);				
$event_name = str_replace("Copa do Brasil Sub20", "Brasil - Taça de Sub20", $event_name);				
$event_name = str_replace("Rússia - Liga da Juventude", "Rússia - Premier League - Reservas", $event_name);				
$event_name = str_replace("Argentina - Superliga Taça", "Argentina - Taça da Superliga", $event_name);				
$event_name = str_replace("Italie - Serie C Playoffs", "Itália - Série C - Play-Offs", $event_name);				
$event_name = str_replace("Escócia - Premiership Playoffs", "Escócia - Premiership - Play-Offs", $event_name);				
$event_name = str_replace("Escócia - Championship Playoffs", "Escócia - Championship - Play-Offs", $event_name);
$event_name = str_replace("Austrália - NPL NSW", "Austrália - New South Wales - Premier League", $event_name);				
$event_name = str_replace("Austrália - NPL Victoria", "Austrália - Victoria Premier League", $event_name);				
$event_name = str_replace("Austrália - NPL South Australia", "Austrália - South Australia Premier League", $event_name);	$event_name = str_replace("Rep. Checa - Liga Sub19", "República Tcheca - Liga de Juniores", $event_name);				
$event_name = str_replace("Noruega - Div. 2 Avdeling 1", "Noruega - 2ª Divisão Grupo 1", $event_name);	
$event_name = str_replace("Noruega - Div. 2 Avdeling 2", "Noruega - 2ª Divisão Grupo 2", $event_name);				
$event_name = str_replace("Suécia - 2.ª Div. Norra Svealand", "Suécia - 2ª Divisão - Norra Svealand", $event_name);	$event_name = str_replace("Suécia - 2.ª Div. Norrland", "Suécia - 2ª Divisão - Norrland", $event_name);				
$event_name = str_replace("Suécia - 2.ª Div. Södra Svealand", "Suécia - 2ª Divisão - Södra Svealand", $event_name);	$event_name = str_replace("Islândia - 1 Deild", "Islândia - 1.ª Divisão", $event_name);				
$event_name = str_replace("Islândia - 2 Deild", "Islândia - 2.ª Divisão", $event_name);
$event_name = str_replace("Itália - Supertaça Lega Pro", "Itália - Supertaça da Série C", $event_name);					
$event_name = str_replace("Holanda - Play-off", "Holanda - Eredivisie - Play-Offs", $event_name);					
$event_name = str_replace("Turquia - Superliga Sub21", "Turquia - Super Lig de Sub21", $event_name);
$event_name = str_replace("Turquia - 1.ª Liga Sub21", "Turquia - 1 Lig de Sub21", $event_name);
$event_name = str_replace("Copa da Chipre", "Taça do Chipre", $event_name);
$event_name = str_replace("Jordânia - Taça", "Taça da Jordânia", $event_name);
$event_name = str_replace("Finlândia Naisten Liiga", "Finlândia - Naisten Liiga", $event_name);
$event_name = str_replace("Camp. da Europa Sub-17 - Feminino", "Euro de Sub-17 - Feminino", $event_name);
$event_name = str_replace("Alemanha - Bundesliga II Playoffs", "Alemanha - Bundesliga II - Playoffs", $event_name);
$event_name = str_replace("Alemanha - Bundesliga Playoffs", "Alemanha - Bundesliga - Playoffs", $event_name);
$event_name = str_replace("AFC - Liga dos Campeões", "Liga dos Campeões da AFC", $event_name);
$event_name = str_replace("Amigáveis - Clubes", "Clubes do Mundo - Amigáveis", $event_name);
$event_name = str_replace("Japão - Taça", "Taça do Japão", $event_name);
$event_name = str_replace("Lituânia - Taça", "Copa da Lituânia", $event_name);
$event_name = str_replace("Montenegro - Taça", "Taça de Montenegro", $event_name);
$event_name = str_replace("Noruega - Div. 3 Avd.", "Noruega - 3.ª Divisão - Grupo", $event_name);
$event_name = str_replace("Republica Tcheca - First League", "República Tcheca - 1. Liga", $event_name);
$event_name = str_replace("Rep. Checa", "República Tcheca", $event_name);
$event_name = str_replace("República Tcheca - Liga Sub21", "República Tcheca - Liga de Sub-21", $event_name);
$event_name = str_replace("Eslovénia - Taça", "Copa da Eslovênia", $event_name);
$event_name = str_replace("Supertaça Sul-Americana", "Recopa Sul-Americana", $event_name);
$event_name = str_replace("CAF - Liga Campeões Africanas", "Liga dos Campeões da CAF", $event_name);
$event_name = str_replace("Supertaça Europeia", "Super Copa da UEFA", $event_name);
$event_name = str_replace("Torneio de Toulon", "Torneio Maurice Revello - Juniores", $event_name);
$event_name = str_replace("Argélia - Taça", "Taça da Argélia", $event_name);
$event_name = str_replace("Brasil - Série ", "Brasileirão - Série ", $event_name);
$event_name = str_replace("Amigáveis - Sub21", "Internacional de Sub-21", $event_name);
$event_name = str_replace("Camp. Mundo - Sub20", "FIFA - Copa do Mundo Sub-20", $event_name);
$event_name = str_replace("Camp. Europa - Sub21", "Campeonato Europeu Sub-21", $event_name);
$event_name = str_replace("Amigáveis - Sub21", "Torneio de Juniores Sub-21", $event_name);
$event_name = str_replace("Partidas Internacionais - Feminino", "Feminino - Internacional", $event_name);
$event_name = str_replace("Finlândia - Taça", "Copa da Finlândia", $event_name);
$event_name = str_replace("Inglaterra - Comm. Shield", "Inglaterra - Community Shield", $event_name);
$event_name = str_replace("Camp. Mundo - Ásia", "Eliminatórias da Copa do Mundo - Ásia", $event_name);
$event_name = str_replace("Roménia - Liga 1 Playoffs", "Romênia - Liga I - Eliminatórias", $event_name);
$event_name = str_replace("Austrália - NPL ACT", "Austrália - Capital Territory Premier League", $event_name);
$event_name = str_replace("Alemanha - Supertaça", "Super Copa da Alemanha", $event_name);
$event_name = str_replace("França - Supertaça", "Super Copa da França", $event_name);
$event_name = str_replace("Rússia - Supertaça", "Super Copa da Rússia", $event_name);
$event_name = str_replace("Gold Cup", "Copa de Ouro da CONCACAF", $event_name);
$event_name = str_replace("Austrália - NPL Tasmania", "Austrália - Tasmânia - Premier League", $event_name);
$event_name = str_replace("Polónia -", "Polônia -", $event_name);
$event_name = str_replace("China - Super Liga", "Superliga da China", $event_name);
$event_name = str_replace("Coreia do Sul - K-League", "Coreia do Sul - K League 1", $event_name);
$event_name = str_replace("Austrália - New South Wales - Premier League", "Austrália - New South País de Gales - Premier League", $event_name);
$event_name = str_replace("Taça Nações Africanas", "Copa das Nações Africanas", $event_name);
$event_name = str_replace("Finlândia - Lohko", "Finlândia - Kakkonen - Grupo", $event_name);
$event_name = str_replace("Ilhas Féroe - Premier Liga", "Ilhas Faroé - Premier League", $event_name);
$event_name = str_replace("Itália - Série C - Play-Offs", "Itália - Série C - Eliminatórias", $event_name);
$event_name = str_replace("Noruega - Eliteserien", "Noruega - EliteSérien", $event_name);
$event_name = str_replace("Malásia - Super League", "Malásia - Superliga", $event_name);
$event_name = str_replace("México Supercopa", "Super Copa do México", $event_name);	
$event_name = str_replace("Mex. Trofeo de Campeones", "México - Campeón de Campeones", $event_name);
$event_name = str_replace("Uruguai - Primera Divisão", "Uruguai - Torneo Intermedio", $event_name);
$event_name = str_replace("Holanda - Supertaça", "Super Copa da Holanda", $event_name);	
$event_name = str_replace("Turquia - Supertaça", "Super Copa da Turquia", $event_name);	
$event_name = str_replace("Equador - Taça", "Copa do Equador", $event_name);	
$event_name = str_replace("Singapura - S-League", "Singapura - Liga S", $event_name);	
$event_name = str_replace("Chinese - 2.ª Divisão", "China - 2.ª Divisão", $event_name);	
$event_name = str_replace("Int. Champions Cup", "Copa dos Campeões Internacionais", $event_name);				
$event_name = str_replace("Inglaterra - Taça EFL", "Inglaterra - Copa EFL", $event_name);
$event_name = str_replace("Brasil - Campeonato Camp. Sub20", "Brasil - Liga Sub-20", $event_name);
$event_name = str_replace("Camp. da Europa Sub19 - Feminino", "Campeonato Europeu Sub-19 - Feminino", $event_name);
$event_name = str_replace("Portugal - Supertaça", "Portugal - Super Copa Cândido de Oliveira", $event_name);
$event_name = str_replace("Bélgica - Supertaça", "Super Copa da Bélgica", $event_name);
$event_name = str_replace("Ucrânia - Supertaça", "Super Copa da Ucrânia", $event_name);
$event_name = str_replace("Camp. da Europa - Sub19", "Campeonato Europeu Sub-19", $event_name);	
$event_name = str_replace("Letónia - Taça", "Copa da Letônia", $event_name);					
$event_name = str_replace("Alemanha - Bayernliga", "Alemanha - Oberliga", $event_name);					
$event_name = str_replace("Coreia do S. - National Champ.", "Coreia do Sul - Liga Nacional", $event_name);					
$event_name = str_replace("Austrália - PL Oeste", "Austrália - Ocidental - Premier League", $event_name);
$event_name = str_replace("Copa do Mundo Sub19 - Feminino", "Mundial de Sub-19 - Feminino", $event_name);				
$event_name = str_replace("Hong Kong - A1", "Hong Kong - BL", $event_name);				
$event_name = str_replace("Australian QBL W.", "Austrália - QBL - Feminino", $event_name);				
$event_name = str_replace("Australian QBL", "Austrália - QBL", $event_name);				
$event_name = str_replace("Bolivian Libobasket", "Bolívia - Libobasquet", $event_name);				
$event_name = str_replace("Espanha - ACB", "Espanha - Liga ACB", $event_name);				
$event_name = str_replace("Austrália - PL Oeste", "Austrália - Ocidental - Premier League", $event_name);				$event_name = str_replace("Egipto - Taça", "Taça do Egito", $event_name);
$event_name = str_replace("Marrocos - Taça", "Copa do Marrocos", $event_name);				
$event_name = str_replace("Camp. da Europa - Feminino - Qual.", "Campeonato Europeu - Qualificação - Feminino", $event_name);				
$event_name = str_replace("Taça do Japão J-League", "Japão - Taça J-League", $event_name);				
$event_name = str_replace("Liga Europa", "UEFA - Liga Europa", $event_name);				
$event_name = str_replace("Liga dos Campeões", "UEFA - Liga dos Campeões", $event_name);
$event_name = str_replace("Serie C - Girone", "Série C - Grupo", $event_name);
$event_name = str_replace("UEFA - Liga dos Campeões da AFC", "Liga dos Campeões da AFC", $event_name);
$event_name = str_replace("UEFA - Liga dos Campeões da CAF", "Liga dos Campeões da CAF", $event_name);
$event_name = str_replace("Inglaterra - Taça FA Qual.", "Inglaterra - Taça FA - Qualificação", $event_name);
$event_name = str_replace("Taça do Marrocos", "Copa do Marrocos", $event_name);				
$event_name = str_replace("Gales - League Cup", "País de Gales - Copa da Liga", $event_name);				
$event_name = str_replace("Taça de Israel Toto", "Israel - Taça de Israel", $event_name);				
$event_name = str_replace("Moldávia - Taça", "Copa da Moldávia", $event_name);				
$event_name = str_replace("Taça Asiática", "Copa AFC", $event_name);
$event_name = str_replace("Taça da Holanda", "Copa da Holanda", $event_name);				
$event_name = str_replace("França - Taça da Liga", "França - Copa da Liga", $event_name);				
$event_name = str_replace("Taça da Bélgica", "Copa da Bélgica", $event_name);				
$event_name = str_replace("Escócia - Taça da Liga", "Escócia - Copa da Liga", $event_name);				
$event_name = str_replace("Taça da Áustria", "Copa da Áustria", $event_name);				
$event_name = str_replace("Taça da Polónia", "Copa da Polónia", $event_name);				
$event_name = str_replace("Taça da Rússia", "Copa da Rússia", $event_name);				
$event_name = str_replace("Taça da Dinamarca", "Copa da Dinamarca", $event_name);				
$event_name = str_replace("Taça da República Tcheca", "Copa da República Tcheca", $event_name);				
$event_name = str_replace("Taça da Noruega", "Copa da Noruega", $event_name);				
$event_name = str_replace("Copa de Portugal da Liga", "Portugal - Copa da Liga", $event_name);
$event_name = str_replace("Taça da Irlanda do Norte da Liga", "Irlanda do Norte - Copa da Liga", $event_name);
$event_name = str_replace("Taça da Eslováquia", "Copa da Eslováquia", $event_name);				
$event_name = str_replace("Taça da Roménia", "Copa da Roménia", $event_name);				
$event_name = str_replace("Itália - Primavera", "Itália - Taça Primavera", $event_name);
$event_name = str_replace("Sérvia - Taça", "Copa da Sérvia", $event_name);
$event_name = str_replace("Macedónia do Norte - Taça", "Copa da Macedônia", $event_name);
$event_name = str_replace("Taça da", "Copa da", $event_name);
$event_name = str_replace("Russian", "Rússia -", $event_name);
$event_name = str_replace("Austrália - FFA Copa", "Austrália - Copa FFA", $event_name);
$event_name = str_replace("Taça Naç. Afr. - Qual.", "Copa das Nações Africanas - Classificação", $event_name);
$event_name = str_replace("Copa da Itália - Serie C", "Itália - Copa da Série C", $event_name);				
				if ($sport_name == 'Basquetebol') {
$event_name = substr_replace($event_name, 'Basquetebol - ', 0, 0);
$event_name = str_replace("Alemanha - Bundesliga", "Alemanha - BBL", $event_name);
$event_name = str_replace("Clubes do Mundo - Amigáveis", "Amistosos de Basquetebol", $event_name);
$event_name = str_replace("Club Friendlies W.", "Amistosos de Basquetebol - Feminino", $event_name);
$event_name = str_replace("EUA - NCAA", "EUA - NCAAB", $event_name);
$event_name = str_replace("Bélgica - Ethias League", "Bélgica - Liga de Basquetebol", $event_name);					
$event_name = str_replace("UEFA - Liga dos Campeões", "Liga dos Campeões de Basquetebol", $event_name);
$event_name = str_replace("Dominican Republic LNB", "República Dominicana - LNB", $event_name);
$event_name = str_replace("Brazilian Paulista", "Brasil - Federação Paulista de Basquetebol", $event_name);
$event_name = str_replace("Itália - Série A", "Itália - Liga de Basquetebol", $event_name);
$event_name = str_replace("Itália - Supertaça", "Supercopa da Itália de Basquetebol", $event_name);
$event_name = str_replace("Espanha - Supertaça", "Supercopa da Espanha de Basquetebol", $event_name);	
$event_name = str_replace("Áustria - Supertaça", "Supercopa da Áustria de Basquetebol", $event_name);	
$event_name = str_replace("Polônia - Ekstraklasa", "Polônia - Liga de Basquetebol", $event_name);
$event_name = str_replace("Áustria - Bundesliga", "Áustria - Liga de Basquetebol", $event_name);
$event_name = str_replace("Liga Europeia", "Liga Europeia de Basquetebol", $event_name);
$event_name = str_replace("Liga Adriática", "Liga Adriática de Basquetebol", $event_name);
$event_name = str_replace("Euroliga", "Euroliga de Basquetebol", $event_name);
$event_name = str_replace("Copa AFC - Feminino", "FIBA - Campeonato da Ásia - Feminino", $event_name);					
$event_name = str_replace("FIBA América - Feminino", "FIBA - Campeonato da Americano - Feminino", $event_name);
$event_name = str_replace("AlpeAdria Cup", "Copa Alpe Adria", $event_name);
$event_name = str_replace("France Leaders Cup Pro", "França - Leaders Cup Pro", $event_name);
$event_name = str_replace("Polônia - Supertaça", "Polônia - Supercopa de Basquete", $event_name);
$event_name = str_replace("Japanese B2 League", "Japão - B2 League", $event_name);
$event_name = str_replace("Hungria - NB I", "Hungria - NB 1.A", $event_name);
$event_name = str_replace("Copa da França Fem.", "França - Taça de Basquetebol - Feminino", $event_name);
$event_name = str_replace("Alemanha - DFB Pokal", "Alemanha - Taça de Basquetebol", $event_name);
$event_name = str_replace("Copa da Eslovênia", "Copa da Eslovênia de Basquetebol", $event_name);					
$event_name = str_replace("EUA - NBA (Pré-época)", "NBA - Pré Temporada", $event_name);					
$event_name = str_replace("Polônia PLKK", "Polônia - PLKK", $event_name);					
$event_name = str_replace("Eslováquia - Extraliga", "Basquetebol - Eslováquia - Extraliga", $event_name);
$event_name = str_replace("Eslovénia - 1. A SKL", "Eslovênia - SKL", $event_name);					
$event_name = str_replace("Fiba Europe Cup", "FIBA - Taça da Europa", $event_name);					
$event_name = str_replace("Latvian Estonian League", "Letônia-Estônia BL", $event_name);
$event_name = str_replace("Islândia - Urvalsdeild - Feminino", "Basquetebol - Islândia - Premier League - Feminino", $event_name);
$event_name = str_replace("Croácia - A1", "Croácia - A1 Liga", $event_name);
$event_name = str_replace("Áustria - Superliga", "Áustria - Superliga de Basquete", $event_name);					
$event_name = str_replace("Polish I Liga", "Polônia - 1.ª Divisão de Basquete", $event_name);
$event_name = str_replace("Philippines Maharlika League", "Filipinas - MPBL", $event_name);
$event_name = str_replace("Islândia - Urvalsdeild", "Islândia - 1.ª Divisão de Basquete", $event_name);
$event_name = str_replace("Israel - Super Lig", "Israel - Superliga de Basquete", $event_name);
$event_name = str_replace("Copa da Grécia", "Copa da Grécia de Basquete", $event_name);
$event_name = str_replace("Copa da França", "Copa da França de Basquete", $event_name);
$event_name = str_replace("Feminino - Internacional", "Partidas de Basquetebol - Feminino", $event_name);					
				}
				if ($sport_name == 'Voleibol') {
$event_name = substr_replace($event_name, 'Voleibol - ', 0, 0);					
$event_name = str_replace("Camp. da Europa", "Campeonato Europeu", $event_name);
$event_name = str_replace("Kovo Cup W.", "Taça KOVO - Feminino", $event_name);
$event_name = str_replace("Copa do Mundo", "Taça do Mundo", $event_name);					
$event_name = str_replace("MEVZA", "Liga MEVZA", $event_name);					
$event_name = str_replace("UEFA - Liga dos Campeões", "Champions League", $event_name);
$event_name = str_replace("Itália - Serie", "Itália - Série", $event_name);
$event_name = str_replace("Baltic League", "Liga Báltica", $event_name);					
				}
				if ($sport_name == 'Futebol') {
$event_name = str_replace("Camp. da Europa", "Euro 2020", $event_name);
$event_name = str_replace("Chipre - Supertaça", "Chipre - Supercopa", $event_name);					
				}	
				if ($sport_name == 'Futsal') {
$event_name = substr_replace($event_name, 'Futsal - ', 0, 0);					
$event_name = str_replace("UEFA Futsal Champions League", "Liga dos Campeões de Futsal", $event_name);
$event_name = str_replace("Polish Ekstraklasa", "Polônia - Ekstraklasa", $event_name);					
$event_name = str_replace("Espanha - Div. Honra", "Espanha - Primera División - Div. Honra", $event_name);					
$event_name = str_replace("República Tcheca - Futsal Liga", "República Tcheca - Liga de Futsal", $event_name);
				}
				if ($sport_name == 'Andebol') {
$event_name = substr_replace($event_name, 'Andebol - ', 0, 0);						
$event_name = str_replace("UEFA - Liga dos Campeões", "Liga dos Campeões", $event_name);
$event_name = str_replace("Copa do Mundo", "Copa do Mundo", $event_name);
$event_name = str_replace("Camp. da Europa", "Campeonato Europeu", $event_name);				
$event_name = str_replace("Jogos Olímpicos - Qual.", "Jogos Olímpicos - Classificação", $event_name);	
				}				
				if ($sport_name == 'Hóquei no Gelo') {
$event_name = substr_replace($event_name, 'Hóquei no Gelo - ', 0, 0);					
$event_name = str_replace("Copa do Mundo", "Taça do Mundo de Hóquei", $event_name);
$event_name = str_replace("Alemanha - DEL", "Alemanha - Eishockey Liga", $event_name);					
$event_name = str_replace("Kazakhstan Championship", "Cazaquistão - Championship de Hóquei", $event_name);					
$event_name = str_replace("Swedish HockeyEttan", "Suécia - HockeyEttan", $event_name);					
$event_name = str_replace("Asia League", "Liga Asiática de Hóquei", $event_name);					
$event_name = str_replace("Suécia - Allsvenskan", "Suécia - HockeyAllsvenksan", $event_name);					
$event_name = str_replace("Polônia - Ekstraklasa", "Polônia - HockeyEkstraklasa", $event_name);					
$event_name = str_replace("Alemanha - Bundesliga II", "Alemanha - Eishockey Liga 2", $event_name);					
$event_name = str_replace("Dinamarca - Ligaen", "Dinamarca - Liga de Hóquei", $event_name);					
$event_name = str_replace("Noruega - Ligaen", "Noruega - Liga de Hóquei", $event_name);					
$event_name = str_replace("Inglaterra - Elite", "Inglaterra - Hockey Elite", $event_name);					
$event_name = str_replace("Hungria - Liga", "Hungria - Liga de Hóquei", $event_name);					
$event_name = str_replace("- Extraliga", "- Extraliga de Hóquei", $event_name);					
$event_name = str_replace("Suíça - NLB", "Suíça - NLB de Hóquei", $event_name);					
$event_name = str_replace("Letónia - Virsliga", "Letónia - Virsliga de Hóquei", $event_name);					
$event_name = str_replace("Suíça - NLA", "Suíça - NLA de Hóquei", $event_name);					
$event_name = str_replace("Finlândia - SM-Liiga", "Finlândia - SM-Liiga de Hóquei", $event_name);					
$event_name = str_replace("República Tcheca - Liga 1", "República Tcheca - Liga 1 de Hóquei", $event_name);					
				}
				if ($sport_name == 'Snooker') {
$event_name = substr_replace($event_name, 'Sinuca - ', 0, 0);				
				}
				if ($sport_name == 'Ténis') {
$event_name = substr_replace($event_name, 'Tênis - ', 0, 0);				
				}
				if ($sport_name == 'Futebol Americano') {
$event_name = substr_replace($event_name, 'Futebol Americano - ', 0, 0);				
				}
				if ($sport_name == 'Basebol') {
$event_name = substr_replace($event_name, 'Beisebol - ', 0, 0);				
				}				
				?>
<div class="sport-<?php echo $events['id']; ?>" style="display:none; color: #14805e !important">

                    <input 
                        type="checkbox"
                        id="event-<?php echo $bet_events['id']; ?>"
                        name="events[]"
                        value="<?php echo $bet_events['id'] . '/' . $events['id']; ?>"
                        />

                    <label for="event-<?php echo $bet_events['id']; ?>"><?php echo $event_name; ?></label>

                </div>
                
            <?php endif; ?>
                
            <?php if (is_array($bet_events)): ?>
                
                <?php foreach ($bet_events as $bet_event_name => $categories): ?>
                
<?php if ('id' != $bet_event_name):
$bet_event_name = str_replace("Universidad Central de Venezuela FC", "UCV FC", $bet_event_name);				
$bet_event_name = str_replace("Olimpia Asuncion", "Club Olimpia", $bet_event_name);
$bet_event_name = str_replace("San Jose Oruro", "San Jose", $bet_event_name);
$bet_event_name = str_replace("Libertad Asuncion", "Club Libertad", $bet_event_name);
$bet_event_name = str_replace("Atletico", "Atlético", $bet_event_name);
$bet_event_name = str_replace("Atlético Paranaense", "Atlético-PR", $bet_event_name);							
$bet_event_name = str_replace("Gremio", "Grêmio", $bet_event_name);
$bet_event_name = str_replace("Internacional RS", "Internacional", $bet_event_name);
$bet_event_name = str_replace("CA Huracan", "Huracán", $bet_event_name);
$bet_event_name = str_replace("Manchester United", "Man Utd", $bet_event_name);
$bet_event_name = str_replace("Manchester City", "Man City", $bet_event_name);
$bet_event_name = str_replace("Cerro Porteno", "Cerro Porteño", $bet_event_name);
$bet_event_name = str_replace("Atlético-MG", "Atlético Mineiro", $bet_event_name);							
$bet_event_name = str_replace("Nacional Montevideo", "Nacional (Uru)", $bet_event_name);
$bet_event_name = str_replace("Universidad Catolica", "Univ. Católica", $bet_event_name);
$bet_event_name = str_replace("Universidad Concepcion", "Univ. Concepción", $bet_event_name);
$bet_event_name = str_replace("Paris SG", "PSG", $bet_event_name);
$bet_event_name = str_replace("Liga de Quito", "LDU Quito", $bet_event_name);
$bet_event_name = str_replace("Penarol de Montevideo", "Peñarol", $bet_event_name);
$bet_event_name = str_replace("Zamora FC", "Zamora", $bet_event_name);
$bet_event_name = str_replace("Bordéus", "Bordeaux", $bet_event_name);
$bet_event_name = str_replace("Bahia Salvador BA", "Bahia", $bet_event_name);
$bet_event_name = str_replace("UANL Tigres", "Tigres UANL", $bet_event_name);
$bet_event_name = str_replace("CF Monterrey", "Monterrey", $bet_event_name);
$bet_event_name = str_replace("Atlético Independiente", "Independiente FC", $bet_event_name);
$bet_event_name = str_replace("Ceará CE", "Ceará", $bet_event_name);
$bet_event_name = str_replace("Uniclinic", "FC Atlético Cearense", $bet_event_name);
$bet_event_name = str_replace("Piaui PI", "Piaui", $bet_event_name);
$bet_event_name = str_replace("Goianesia GO", "Goianesia", $bet_event_name);
$bet_event_name = str_replace("Ipora GO", "Ipora EC", $bet_event_name);
$bet_event_name = str_replace("Boyaca Patriotas", "Patriotas FC", $bet_event_name);
$bet_event_name = str_replace("Valledupar", "Valledupar FC", $bet_event_name);
$bet_event_name = str_replace("Deportes Pereira", "Dep. Pereira", $bet_event_name);
$bet_event_name = str_replace("Red Bull Brasil SP", "Red Bull Brasil", $bet_event_name);
$bet_event_name = str_replace("Ituano SP", "Ituano", $bet_event_name);
$bet_event_name = str_replace("CS Alagoano", "CSA", $bet_event_name);
$bet_event_name = str_replace("Vitoria BA", "Vitória", $bet_event_name);
$bet_event_name = str_replace(" F.", " - Feminino", $bet_event_name);
$bet_event_name = str_replace("Colegiales", "CA Colegiales", $bet_event_name);
$bet_event_name = str_replace("CA All Boys Santa Rosa", "All Boys", $bet_event_name);
$bet_event_name = str_replace("CF America", "Club América", $bet_event_name);
$bet_event_name = str_replace("Tiburones Rojos de Veracruz", "Veracruz", $bet_event_name);
$bet_event_name = str_replace("Atlas de Guadalajara", "Atlas", $bet_event_name);
$bet_event_name = str_replace("Club Tijuana", "Tijuana", $bet_event_name);
$bet_event_name = str_replace("Deportivo Toluca", "Toluca", $bet_event_name);
$bet_event_name = str_replace("Gallos Blancos Queretaro", "Querétaro", $bet_event_name);
$bet_event_name = str_replace("Alebrijes De Oaxaca", "Oaxaca", $bet_event_name);
$bet_event_name = str_replace("CSD Dorados Sinaloa", "Dorados", $bet_event_name);
$bet_event_name = str_replace("Leones Negros UDEG", "Leones Negros", $bet_event_name);
$bet_event_name = str_replace("Cimarrones de Sonora", "Cimarrones", $bet_event_name);
$bet_event_name = str_replace("Mineros De Zacatecas", "M. Zacatecas", $bet_event_name);
$bet_event_name = str_replace("Club Celaya", "Celaya", $bet_event_name);
$bet_event_name = str_replace("Rio Ave FC", "Rio Ave", $bet_event_name);
$bet_event_name = str_replace("Sporting Braga", "SC Braga", $bet_event_name);
$bet_event_name = str_replace("Club Sport Emelec", "Emelec", $bet_event_name);
$bet_event_name = str_replace("Real Oviedo", "Oviedo", $bet_event_name);
$bet_event_name = str_replace("Gimnastic Tarragona", "Gimnastic", $bet_event_name);
$bet_event_name = str_replace("Albacete Balompie", "Albacete", $bet_event_name);
$bet_event_name = str_replace("CF Rayo Majadahonda", "Rayo Majadahonda", $bet_event_name);
$bet_event_name = str_replace("Zaragoza", "Zaragoça", $bet_event_name);
$bet_event_name = str_replace("Zenit São Petersburgo", "Zenit St Petersburg", $bet_event_name);
$bet_event_name = str_replace("Sydney Wanderers", "WS Wanderers", $bet_event_name);
$bet_event_name = str_replace("Adelaide United FC", "Adelaide United", $bet_event_name);
$bet_event_name = str_replace("Sydney FC Youth", "Sydney FC", $bet_event_name);
$bet_event_name = str_replace("Energie Cottbus", "Cottbus", $bet_event_name);
$bet_event_name = str_replace("Kickers Wurzburg", "Wurzburg Kickers", $bet_event_name);
$bet_event_name = str_replace("SpVgg Unterhaching", "Unterhaching", $bet_event_name);
$bet_event_name = str_replace("Meppen", "SV Meppen", $bet_event_name);
$bet_event_name = str_replace("Aalen", "VfR Aalen", $bet_event_name);
$bet_event_name = str_replace("NEC Nijmegen", "NEC", $bet_event_name);
$bet_event_name = str_replace("Jong PSV Eindhoven", "PSV - Reservas", $bet_event_name);
$bet_event_name = str_replace("Jong Utrecht", "Utrecht - Reservas", $bet_event_name);
$bet_event_name = str_replace("Jong Ajax Amsterdam", "Ajax - Reservas", $bet_event_name);
$bet_event_name = str_replace("Jong AZ Alkmaar", "AZ - Reservas", $bet_event_name);
$bet_event_name = str_replace("Volendam", "FC Volendam", $bet_event_name);
$bet_event_name = str_replace("RKC Waalwijk", "RKC", $bet_event_name);
$bet_event_name = str_replace("Cambuur Leeuwarden", "Cambuur", $bet_event_name);
$bet_event_name = str_replace("VVV Venlo", "VVV", $bet_event_name);
$bet_event_name = str_replace("SBV Excelsior", "Excelsior", $bet_event_name);
$bet_event_name = str_replace("PSV Eindhoven", "PSV", $bet_event_name);
$bet_event_name = str_replace("NAC Breda", "NAC", $bet_event_name);
$bet_event_name = str_replace("Zwolle", "PEC Zwolle", $bet_event_name);
$bet_event_name = str_replace("Borussia M'gladbach", "Mönchengladbach", $bet_event_name);
$bet_event_name = str_replace("Standard Liege", "Standard Liège", $bet_event_name);
$bet_event_name = str_replace("Royal Charleroi SC", "Charleroi", $bet_event_name);
$bet_event_name = str_replace("Antuérpia", "Antwerp", $bet_event_name);
$bet_event_name = str_replace("Oostende", "KV Oostende", $bet_event_name);
$bet_event_name = str_replace("Sint-Truidense VV", "Sint-Truidense", $bet_event_name);
$bet_event_name = str_replace("Royal Pari Sion", "Royal Pari FC", $bet_event_name);
$bet_event_name = str_replace("Deportivo Malacateco", "Malacateco", $bet_event_name);
$bet_event_name = str_replace("MIN T'wolves", "MIN Timberwolves", $bet_event_name);
$bet_event_name = str_replace("PHI Sixers", "PHI 76ers", $bet_event_name);
$bet_event_name = str_replace("PHO Suns", "PHX Suns", $bet_event_name);
$bet_event_name = str_replace("Carolina Hurricanes", "CAR Hurricanes", $bet_event_name);
$bet_event_name = str_replace("Boston Bruins", "BOS Bruins", $bet_event_name);
$bet_event_name = str_replace("New Jersey Devils", "NJ Devils", $bet_event_name);
$bet_event_name = str_replace("Pittsburgh Penguins", "PIT Penguins", $bet_event_name);
$bet_event_name = str_replace("Florida Panthers", "FLA Panthers", $bet_event_name);
$bet_event_name = str_replace("New York Islanders", "NY Islanders", $bet_event_name);
$bet_event_name = str_replace("Otawa Senators", "OTT Senators", $bet_event_name);
$bet_event_name = str_replace("Columbus Blue Jackets", "CLB Blue Jackets", $bet_event_name);
$bet_event_name = str_replace("Winnipeg Jets", "WIN Jets", $bet_event_name);
$bet_event_name = str_replace("Arizona Coyotes", "ARZ Coyotes", $bet_event_name);
$bet_event_name = str_replace("Anaheim Ducks", "ANA Ducks", $bet_event_name);
$bet_event_name = str_replace("Los Angeles Kings", "LA Kings", $bet_event_name);
$bet_event_name = str_replace("Los Angeles Galaxy", "LA Galaxy", $bet_event_name);
$bet_event_name = str_replace("Nashville Predators", "NAS Predators", $bet_event_name);
$bet_event_name = str_replace("Minnesota Wild", "MIN Wild", $bet_event_name);
$bet_event_name = str_replace("Tampa Bay Lightning", "TB Lightning", $bet_event_name);
$bet_event_name = str_replace("Detroit Red Wings", "DET Red Wings", $bet_event_name);
$bet_event_name = str_replace("Montreal Canadiens", "MON Canadiens", $bet_event_name);
$bet_event_name = str_replace("Vancouver Canucks", "VAN Canucks", $bet_event_name);
$bet_event_name = str_replace("Washington Capitals", "WAS Capitals", $bet_event_name);
$bet_event_name = str_replace("Philadelphia Flyers", "PHI Flyers", $bet_event_name);
$bet_event_name = str_replace("New York Rangers", "NY Rangers", $bet_event_name);
$bet_event_name = str_replace("Dallas Stars", "DAL Stars", $bet_event_name);
$bet_event_name = str_replace("Vegas Golden Knights", "VGS Golden Knights", $bet_event_name);
$bet_event_name = str_replace("Colorado Avalanche", "COL Avalanche", $bet_event_name);
$bet_event_name = str_replace("Toronto Maple Leafs", "TOR Maple Leafs", $bet_event_name);
$bet_event_name = str_replace("Calgary Flames", "CAL Flames", $bet_event_name);
$bet_event_name = str_replace("St Louis Blues", "STL Blues", $bet_event_name);
$bet_event_name = str_replace("St. Patrick's Athletic", "St Patricks", $bet_event_name);
$bet_event_name = str_replace("Waterford United", "Waterford", $bet_event_name);
$bet_event_name = str_replace("University College Dublin", "UC Dublin", $bet_event_name);
$bet_event_name = str_replace("Bohemians Dublin", "Bohemians", $bet_event_name);
$bet_event_name = str_replace("Krylia Sovetov Samara", "Krylia Sovetov", $bet_event_name);
$bet_event_name = str_replace("Ienissei Krasnoyarsk", "FK Yenisey", $bet_event_name);
$bet_event_name = str_replace("Ural Ecaterimburgo", "Ural", $bet_event_name);
$bet_event_name = str_replace("FC Krasnodar", "Krasnodar", $bet_event_name);
$bet_event_name = str_replace("Heart of Midlothian", "Heart", $bet_event_name);
$bet_event_name = str_replace("Greenock Morton", "Morton", $bet_event_name);
$bet_event_name = str_replace("Dundee United", "Dundee Utd", $bet_event_name);
$bet_event_name = str_replace("Queen of the South", "Queen of South", $bet_event_name);
$bet_event_name = str_replace("Inverness Caledonian Thistle", "Inverness CT", $bet_event_name);
$bet_event_name = str_replace("Náutico PE", "Náutico Capibaribe", $bet_event_name);
$bet_event_name = str_replace("Fortaleza CE", "Fortaleza EC", $bet_event_name);
$bet_event_name = str_replace("Confianca", "AD Confianca", $bet_event_name);
$bet_event_name = str_replace("Mixto MT", "Mixto", $bet_event_name);
$bet_event_name = str_replace("Chapecoense SC", "Chapecoense", $bet_event_name);
$bet_event_name = str_replace("Ypiranga RS", "Ypiranga", $bet_event_name);
$bet_event_name = str_replace("Brasil De Pelotas RS", "Brasil de Pelotas", $bet_event_name);
$bet_event_name = str_replace("América RN", "América de Natal", $bet_event_name);
$bet_event_name = str_replace("Salgueiro PE", "Salgueiro AC", $bet_event_name);
$bet_event_name = str_replace("Luch-Energia Vladivostok", "Luch-Energia", $bet_event_name);
$bet_event_name = str_replace("Krasnodar-2", "Krasnodar 2", $bet_event_name);
$bet_event_name = str_replace("SKA-Khabarovsk", "SKA Khabarovsk", $bet_event_name);
$bet_event_name = str_replace("Sibir Novosibirsk", "Sibir", $bet_event_name);
$bet_event_name = str_replace("Shinnik Iaroslavl", "Yaroslavl", $bet_event_name);
$bet_event_name = str_replace("Baltika Kaliningrado", "Baltika", $bet_event_name);
$bet_event_name = str_replace("FC Tiumen", "Tyumen", $bet_event_name);
$bet_event_name = str_replace("Mordovia Saransk", "M. Saransk", $bet_event_name);
$bet_event_name = str_replace("Avangard Kursk", "Kursk", $bet_event_name);
$bet_event_name = str_replace("Fakel Voronej", "F. Voronezh", $bet_event_name);
$bet_event_name = str_replace("Rotor Volgogrado", "R. Volgogrado", $bet_event_name);
$bet_event_name = str_replace("Torpedo Armavir", "Armavir", $bet_event_name);
$bet_event_name = str_replace("Karmiotissa Polemidion", "APK Karmiotissa", $bet_event_name);
$bet_event_name = str_replace("Real Espana", "Real España", $bet_event_name);
$bet_event_name = str_replace("Kapaz", "FK Kapaz", $bet_event_name);
$bet_event_name = str_replace("Sabah-2", "Sabah FK II", $bet_event_name);
$bet_event_name = str_replace("Fk Sumgayit Reserve", " Fk Sumgayit II", $bet_event_name);
$bet_event_name = str_replace("Pfk Neftci Baku-2", "Neftci Baku II", $bet_event_name);
$bet_event_name = str_replace("Garabagh-2", "FK Qarabag II", $bet_event_name);
$bet_event_name = str_replace("FK Sabayil-2", "Sabayil FC II", $bet_event_name);
$bet_event_name = str_replace("Deportivo La Equidad", "La Equidad", $bet_event_name);
$bet_event_name = str_replace("College Europa", "Europa FC", $bet_event_name);
$bet_event_name = str_replace("Club Almagro", "Almagro", $bet_event_name);
$bet_event_name = str_replace("Real Pilar FC", "Real Pilar", $bet_event_name);
$bet_event_name = str_replace("CD Honduras Progreso", "Honduras Progreso", $bet_event_name);
$bet_event_name = str_replace("Orlando City SC", "Orlando City", $bet_event_name);
$bet_event_name = str_replace("Cuiaba Esporte Clube", "Cuiabá EC", $bet_event_name);
$bet_event_name = str_replace("Operario MT", "Operário Várzea", $bet_event_name);							
$bet_event_name = str_replace("Operario FC MG", "Operário MT", $bet_event_name);							
$bet_event_name = str_replace("Lincoln Red Imps FC", "Lincoln Red Imps", $bet_event_name);							
$bet_event_name = str_replace("Mohun Bagan AC", "Mohun Bagan", $bet_event_name);							
$bet_event_name = str_replace("Operario MT", "Operário Várzea", $bet_event_name);							
$bet_event_name = str_replace("Operario FC MG", "Operário MT", $bet_event_name);							
$bet_event_name = str_replace("EM Deportivo Binacional", "Deportivo Binacional", $bet_event_name);							
$bet_event_name = str_replace("Universidad Cesar Vallejo", "Cesar Vallejo", $bet_event_name);							
$bet_event_name = str_replace("Union Comercio", "Unión Comercio", $bet_event_name);							
$bet_event_name = str_replace("Sport Boys Association", "Sport Boys", $bet_event_name);							
$bet_event_name = str_replace("UTC de Cajamarca", "U. de Cajamarca", $bet_event_name);							
$bet_event_name = str_replace("SC Paderborn 07", "Paderborn 07", $bet_event_name);							
$bet_event_name = str_replace("Hamburger SV", "Hamburgo", $bet_event_name);							
$bet_event_name = str_replace("FC St. Pauli - Hamburgo", "St. Pauli", $bet_event_name);							
$bet_event_name = str_replace("Dinamo Dresden", "Dynamo Dresden", $bet_event_name);							
$bet_event_name = str_replace("MVV Maastricht", "Maastricht", $bet_event_name);							
$bet_event_name = str_replace("Dinamo Dresden", "Dynamo Dresden", $bet_event_name);
$bet_event_name = str_replace("Newell's Old Boys", "Newells Old Boys", $bet_event_name);							
$bet_event_name = str_replace("Gimnasia Y Esgrima", "Gimnasia La Plata", $bet_event_name);							
$bet_event_name = str_replace("Estudiantes de La Plata", "Estudiantes LP", $bet_event_name);							
$bet_event_name = str_replace("San Martin San Juan", "San Martín de San Juan", $bet_event_name);
$bet_event_name = str_replace("Colon de Santa Fe", "Colón", $bet_event_name);							
$bet_event_name = str_replace("BRO Nets", "BKN Nets", $bet_event_name);							
$bet_event_name = str_replace("- FemininoC.", "F.C", $bet_event_name);							
$bet_event_name = str_replace("Erzgebirge Aue", "Aue", $bet_event_name);							
$bet_event_name = str_replace("Ferroviaria SP", "Ferroviária", $bet_event_name);							
$bet_event_name = str_replace("Botafogo SP", "Botafogo Ribeirão Preto", $bet_event_name);							
$bet_event_name = str_replace("São Bento SP", "São Bento", $bet_event_name);							
$bet_event_name = str_replace("Atlético Bucaramanga", "Bucaramanga", $bet_event_name);							
$bet_event_name = str_replace("Atlético Tubarao SC", "Atlético Tubarão", $bet_event_name);							
$bet_event_name = str_replace("Cucuta Deportivo", "Cúcuta", $bet_event_name);							
$bet_event_name = str_replace("Independiente Medellin", "Ind. Medellin", $bet_event_name);							
$bet_event_name = str_replace("Hercilio Luz FC", "Hercílio Luz", $bet_event_name);							
$bet_event_name = str_replace("Independiente Santa Fe", "Ind. Santa Fé", $bet_event_name);							
$bet_event_name = str_replace("Maidstone United", "Maidstone Utd", $bet_event_name);							
$bet_event_name = str_replace("Havant & Waterlooville", "H. & Waterlooville", $bet_event_name);							
$bet_event_name = str_replace("Tallinna FC Flora", "Flora Tallinn", $bet_event_name);							
$bet_event_name = str_replace("Parnu JK Vaprus", "JK Vaprus", $bet_event_name);							
$bet_event_name = str_replace("Rakvere JK Tarvas", "Rakvere JK", $bet_event_name);							
$bet_event_name = str_replace("Tallinna JK Legion", "TJK Legion", $bet_event_name);							
$bet_event_name = str_replace("Kohtla-Järve JK Järve", "JK Järve", $bet_event_name);							
$bet_event_name = str_replace("River Plate Asuncion", "Club River Plate", $bet_event_name);							
$bet_event_name = str_replace("FK Mladost Lucani", "FK Mladost", $bet_event_name);							
$bet_event_name = str_replace("FK Radnik Surdulica", "FK Radnik", $bet_event_name);							
$bet_event_name = str_replace("Proleter Novi Sad", "Novi Sad", $bet_event_name);							
$bet_event_name = str_replace("FK Backa BP", "FK Backa", $bet_event_name);							
$bet_event_name = str_replace("O'Higgins", "O Higgins", $bet_event_name);							
$bet_event_name = str_replace("Union La Calera", "La Calera", $bet_event_name);							
$bet_event_name = str_replace("Everton Vina", "Everton Viña", $bet_event_name);							
$bet_event_name = str_replace("Deportes Valdivia", "Valdivia", $bet_event_name);							
$bet_event_name = str_replace("Universidad de Chile", "Univ. de Chile", $bet_event_name);							
$bet_event_name = str_replace("Universitario De Popayan", "Popayan", $bet_event_name);							
$bet_event_name = str_replace("União San Felipe", "U. San Felipe", $bet_event_name);							
$bet_event_name = str_replace("Santiago Wanderers", "S. Wanderers", $bet_event_name);							
$bet_event_name = str_replace("Deportes Magallanes", "CD Magallanes", $bet_event_name);										
$bet_event_name = str_replace("Deportivo Saprissa", "Saprissa", $bet_event_name);							
$bet_event_name = str_replace("Sporting San Miguelito", "Miguelito", $bet_event_name);							
$bet_event_name = str_replace("Deportivo Walter Ferreti", "Walter Ferreti", $bet_event_name);							
$bet_event_name = str_replace("Juventus Managua", "Managua", $bet_event_name);							
$bet_event_name = str_replace("Instituto AC Cordoba", "Instituto AC", $bet_event_name);							
$bet_event_name = str_replace("Montevideo Wanderers", "M. Wanderers", $bet_event_name);							
$bet_event_name = str_replace("Talleres de Remedios de Escalada", "Talleres Remedios", $bet_event_name);					$bet_event_name = str_replace("Club Atlético Fenix", "CA Fênix", $bet_event_name);							
$bet_event_name = str_replace("SSD Monza 1912", "Monza 1912", $bet_event_name);							
$bet_event_name = str_replace("Deportivo Cuenca", "Dep. Cuenca", $bet_event_name);							
$bet_event_name = str_replace("Tecnico Universitario", "Universitario", $bet_event_name);							
$bet_event_name = str_replace("Jaguares De Cordoba", "Jaguares Córdoba", $bet_event_name);							
$bet_event_name = str_replace("Concordia Chiajna", "Concórdia", $bet_event_name);
$bet_event_name = str_replace("Imperatriz MA", "Imperatriz", $bet_event_name);							
$bet_event_name = str_replace("Sao Jose de Ribamar", "São José de Ribamar", $bet_event_name);							
$bet_event_name = str_replace("Sertaozinho", "Sertãozinho", $bet_event_name);							
$bet_event_name = str_replace("Santo Andre SP", "Santo Andre", $bet_event_name);							
$bet_event_name = str_replace("Atibaia SP", "Atibaia", $bet_event_name);							
$bet_event_name = str_replace("Maranhao MA", "Maranhão", $bet_event_name);							
$bet_event_name = str_replace("Queensland Lions", "Lions FC", $bet_event_name);							
$bet_event_name = str_replace("SKN St. Polten II", "St. Polten II", $bet_event_name);							
$bet_event_name = str_replace("SC Team Wiener Linien", "Team Wiener Linien", $bet_event_name);							
$bet_event_name = str_replace("Union Espanola", "Unión Española", $bet_event_name);							
$bet_event_name = str_replace("Independiente Del Valle", "Ind. Del Valle", $bet_event_name);							
$bet_event_name = str_replace("F. Bourg en Bresse Peronnas 01", "Bourg-Peronnas", $bet_event_name);							
$bet_event_name = str_replace("Boulogne Cote D'Opale", "Boulogne", $bet_event_name);							
$bet_event_name = str_replace("Le Mans UC 72", "Le Mans", $bet_event_name);							
$bet_event_name = str_replace("Rodez Aveyron", "Rodez", $bet_event_name);							
$bet_event_name = str_replace("Sannois-Saint-Gratien", "Entente SSG", $bet_event_name);							
$bet_event_name = str_replace("Jeanne d'Arc", "Jeanne Drancy", $bet_event_name);							
$bet_event_name = str_replace("FC Chambly Thelle", "Chambly Thelle FC", $bet_event_name);							
$bet_event_name = str_replace("Quevilly Rouen", "US Quevilly", $bet_event_name);							
$bet_event_name = str_replace("FC Villefranche", "Villefranche", $bet_event_name);							
$bet_event_name = str_replace("FC Rostov", "Rostov", $bet_event_name);							
$bet_event_name = str_replace("Union Magdalena", "Unión Magdalena", $bet_event_name);							
$bet_event_name = str_replace("CD Real San Andres", "Real San Andres", $bet_event_name);							
$bet_event_name = str_replace("KGHM Zaglebie Lubin", "Zaglebie Lubin", $bet_event_name);							
$bet_event_name = str_replace("KS Cracovia", "Cracovia Krakow", $bet_event_name);							
$bet_event_name = str_replace("Gimnasia y Esgrima Mendoza", "Gimnasia y Esgrima", $bet_event_name);							
$bet_event_name = str_replace("Coritiba PR", "Coritiba", $bet_event_name);
$bet_event_name = str_replace("Atlético-PR", "Athletico-PR", $bet_event_name);							
$bet_event_name = str_replace("Foz do Iguaçu PR", "Foz do Iguaçu", $bet_event_name);							
$bet_event_name = str_replace("Metropolitano Maringa PR", "Maringá FC", $bet_event_name);							
$bet_event_name = str_replace("Cascavel PR", "Cascavel CR", $bet_event_name);							
$bet_event_name = str_replace("Londrina PR", "Londrina", $bet_event_name);							
$bet_event_name = str_replace("Atlético Tubarão", "Tubarão", $bet_event_name);							
$bet_event_name = str_replace("Criciuma SC", "Criciúma", $bet_event_name);
$bet_event_name = str_replace("River PI", "River AC", $bet_event_name);							
$bet_event_name = str_replace("Altos PI", "AE Altos", $bet_event_name);							
$bet_event_name = str_replace("Hellas Verona", "Verona", $bet_event_name);							
$bet_event_name = str_replace("Padova", "Pádua", $bet_event_name);
$bet_event_name = str_replace("Atlético Lanus", "Lanús", $bet_event_name);
$bet_event_name = str_replace("Hebei China Fortune FC", "Hebei CFFC", $bet_event_name);
$bet_event_name = str_replace("- Feminino Bourg en Bresse Peronnas 01", "Bourg-Peronnas", $bet_event_name);
$bet_event_name = str_replace("Desportiva Perilima de Futebol", "Desportiva Perilima PB", $bet_event_name);
$bet_event_name = str_replace("CSP PB", "CSP", $bet_event_name);
$bet_event_name = str_replace("Universidad de Costa Rica", "UCR", $bet_event_name);
$bet_event_name = str_replace("Limon", "Limón FC", $bet_event_name);
$bet_event_name = str_replace("Santos de Guapiles", "Santos de Guápiles", $bet_event_name);
$bet_event_name = str_replace("Municipal Grecia", "AD Grecia", $bet_event_name);
$bet_event_name = str_replace("Guadalupe", "Guadalupe FC", $bet_event_name);
$bet_event_name = str_replace("Carmelita", "AD Carmelita", $bet_event_name);
$bet_event_name = str_replace("San Carlos", "AD San Carlos", $bet_event_name);
$bet_event_name = str_replace("Cartagines", "Cartaginés", $bet_event_name);
$bet_event_name = str_replace("Johor Darul Ta'zim II", "Johor Darul Takzim II", $bet_event_name);
$bet_event_name = str_replace("Pdrm", "PDRM", $bet_event_name);
$bet_event_name = str_replace("Chertanovo-2", "FK Chertanovo II", $bet_event_name);
$bet_event_name = str_replace("1 FK Pribram", "FK Pribram", $bet_event_name);				
$bet_event_name = str_replace("SFC Opava", "Opava", $bet_event_name);				
$bet_event_name = str_replace("Ask-Bsc Bruck/Leitha", "ASK/BSC Bruck Leitha", $bet_event_name);				
$bet_event_name = str_replace("Sturm Graz Am.", "SK Sturm Graz II", $bet_event_name);				
$bet_event_name = str_replace("Lendorf", "SV Lendorf", $bet_event_name);				
$bet_event_name = str_replace("Inter De Limeira SP", "Inter De Limeira", $bet_event_name);				
$bet_event_name = str_replace("Juventus SP", "CA Juventus", $bet_event_name);				
$bet_event_name = str_replace("Estudiantes Merida", "Estudiantes de Mérida", $bet_event_name);				
$bet_event_name = str_replace("Nacional Asuncion", "Nacional Asunción", $bet_event_name);
$bet_event_name = str_replace("South West Queensland Thunder", "SWQ Thunder", $bet_event_name);
$bet_event_name = str_replace("Eastern Suburbs Brisbane", "Eastern Suburbs", $bet_event_name);
$bet_event_name = str_replace("Kubanochka Krasnodar - Feminino", "K. Krasnodar - Feminino", $bet_event_name);
$bet_event_name = str_replace("Cayb Club Athletic Youssoufia Berrechid", "Youssoufia Berrechid", $bet_event_name);
$bet_event_name = str_replace("Wydad Casablanca", "WAC Casablanca", $bet_event_name);
$bet_event_name = str_replace("Mat Maghrib Association Tetouan", "MAT Tetouan", $bet_event_name);
$bet_event_name = str_replace("Hassania Agadir", "HUSA Agadir", $bet_event_name);
$bet_event_name = str_replace("Dhj Difaa Hassani Jdidi", "Difaa El Jadida", $bet_event_name);
$bet_event_name = str_replace("Cra de Hoceima", "Chabab Rif Hoceima", $bet_event_name);
$bet_event_name = str_replace("Khouribga", "OC Khourigba", $bet_event_name);
$bet_event_name = str_replace("Países Baixos", "Holanda", $bet_event_name);
$bet_event_name = str_replace(" M Sub-17", "M U17", $bet_event_name);
$bet_event_name = str_replace("Rigas Futbola Skola", "Rigas FS", $bet_event_name);
$bet_event_name = str_replace("FS Metta LU", "Metta/LU", $bet_event_name);
$bet_event_name = str_replace("Akhmat Grózni Juventude", "Akhmat Grozny (Res)", $bet_event_name);
$bet_event_name = str_replace("CSKA Moscovo J.", "CSKA Moscovo (Res)", $bet_event_name);
$bet_event_name = str_replace("Krylya Sovetov J.", "Krylia Sovetov (Res)", $bet_event_name);
$bet_event_name = str_replace("Rigas Futbola Skola", "Rigas FS", $bet_event_name);
$bet_event_name = str_replace("Spartak Moscovo Juventude", "Spartak de Moscovo (Res)", $bet_event_name);
$bet_event_name = str_replace("FC Orenburg J.", "FK Orenburg (Res)", $bet_event_name);
$bet_event_name = str_replace("Ufa Juventude", "FC Ufa - Reservas", $bet_event_name);
$bet_event_name = str_replace("FC Anif / Salzburg II", "USK FC Anif", $bet_event_name);
$bet_event_name = str_replace("Grodig", "SV Grodig", $bet_event_name);
$bet_event_name = str_replace("Traiskirchen Fcm", "FCM Traiskirchen", $bet_event_name);
$bet_event_name = str_replace("Jeonbuk Hyundai Motors", "Jeonbuk Motors", $bet_event_name);				
$bet_event_name = str_replace("Jeunesse Sportive Kairouanaise", "Jeunesse Sportive", $bet_event_name);				
$bet_event_name = str_replace("Juventude RS", "Juventude", $bet_event_name);				
$bet_event_name = str_replace("Polonia Warszawa", "Polônia Varsóvia", $bet_event_name);				
$bet_event_name = str_replace("Sokol Aleksandrow Lodzki", "Sokol Aleksandrow", $bet_event_name);				
$bet_event_name = str_replace("New York Red Bulls II", "NY Red Bulls II", $bet_event_name);				
$bet_event_name = str_replace("Esperance Sportive Tunis", "Esperance de Tunis", $bet_event_name);				
$bet_event_name = str_replace("KSZO Ostrowiec Swietokrzyski", "KSZO Ostrowiec", $bet_event_name);				
$bet_event_name = str_replace("MKS Podlasie Biala Podlaska", "Podlasie Biala Podlaska", $bet_event_name);				
$bet_event_name = str_replace("KS Hutnik Krakow SSA", "Hutnik Krakow", $bet_event_name);
$bet_event_name = str_replace("Club Deportivo Hispano Americano", "CD Hispano", $bet_event_name);	
$bet_event_name = str_replace("Floresta CE", "Floresta EC", $bet_event_name);
$bet_event_name = str_replace("Vitória PE", "Vitória das Tabocas", $bet_event_name);
$bet_event_name = str_replace("Jacuipense BA", "Jacuipense", $bet_event_name);
$bet_event_name = str_replace("Campinense", "Campinense Clube", $bet_event_name);
$bet_event_name = str_replace("Fluminense de Feira BA", "Fluminense de Feira", $bet_event_name);
$bet_event_name = str_replace("Sergipe SE", "CS Sergipe", $bet_event_name);
$bet_event_name = str_replace("Novorizontino SP", "Grêmio Novorizontino", $bet_event_name);
$bet_event_name = str_replace("Sao Caetano SP", "São Caetano", $bet_event_name);
$bet_event_name = str_replace("Caxias RS", "Caxias", $bet_event_name);
$bet_event_name = str_replace("Ferroviario", "Ferroviário", $bet_event_name);
$bet_event_name = str_replace("Etoile Sportive du Sahel", "Etoile Sportive Sahel", $bet_event_name);
$bet_event_name = str_replace("Bragantino Clube do Pará", "Bragantino PA", $bet_event_name);				
$bet_event_name = str_replace("Letónia", "Letônia", $bet_event_name);				
$bet_event_name = str_replace("Estónia", "Estônia", $bet_event_name);				
$bet_event_name = str_replace("Polónia", "Polônia", $bet_event_name);
$bet_event_name = str_replace("Incheon United", "Incheon Utd", $bet_event_name);				
$bet_event_name = str_replace("Suwon Samsung Bluewings", "Suwon Bluewings", $bet_event_name);				
$bet_event_name = str_replace("Seongnam Ilhwa", "Seongnam FC", $bet_event_name);				
$bet_event_name = str_replace("Gyeongnam", "Gyeongnam FC", $bet_event_name);				
$bet_event_name = str_replace("República Checa", "República Tcheca", $bet_event_name);				
$bet_event_name = str_replace("Sangju Sangmu Phoenix", "Sangju Sangmu", $bet_event_name);				
$bet_event_name = str_replace("APIA Leichhardt Tigers", "Apia L Tigers", $bet_event_name);				
$bet_event_name = str_replace("Hakoah", "Hakoah Sydney City East", $bet_event_name);				
$bet_event_name = str_replace("Kajaanin Haka", "Kajha", $bet_event_name);				
$bet_event_name = str_replace("Vaajakoski", "FC Vaajakoski", $bet_event_name);				
$bet_event_name = str_replace("Brann", "SK Brann", $bet_event_name);				
$bet_event_name = str_replace("Lillestrøm SK", "Lillestrøm", $bet_event_name);				
$bet_event_name = str_replace("Rosenborg BK", "Rosenborg", $bet_event_name);				
$bet_event_name = str_replace("Odds BK", "Odd BK", $bet_event_name);
$bet_event_name = str_replace("Tromsø IL", "Tromsø", $bet_event_name);				
$bet_event_name = str_replace("Strømsgodset IF", "Strømsgodset", $bet_event_name);
$bet_event_name = str_replace("Byaasen Toppfotball", "Byåsen", $bet_event_name);				
$bet_event_name = str_replace("Stjørdals-Blink", "Stjørdals/Blink", $bet_event_name);
$bet_event_name = str_replace("Kvik Halden", "Kvik Halden FK", $bet_event_name);
$bet_event_name = str_replace("Egersund IK", "Egersund", $bet_event_name);				
$bet_event_name = str_replace("Fram Larvik", "Fram", $bet_event_name);
$bet_event_name = str_replace("Sotra SK", "Sotra", $bet_event_name);				
$bet_event_name = str_replace("Oppsal", "Stjørdals/Blink", $bet_event_name);
$bet_event_name = str_replace("Port Melbourne Sharks", "Port Melbourne SC", $bet_event_name);
$bet_event_name = str_replace("Green Gully Cavaliers", "Green Gully", $bet_event_name);				
$bet_event_name = str_replace("Altona Magic SC", "Altona Magic", $bet_event_name);
$bet_event_name = str_replace("Heidelberg United", "Heidelberg Utd", $bet_event_name);				
$bet_event_name = str_replace("Kingston City FC", "Kingston City", $bet_event_name);
$bet_event_name = str_replace("Avondale Heights", "Avondale", $bet_event_name);
$bet_event_name = str_replace("B 93 Copenhagen", "B93 Copenhagen", $bet_event_name);
$bet_event_name = str_replace("Ringkobing", "Ringkøbing IF", $bet_event_name);
$bet_event_name = str_replace("Bronshoj", "Brønshøj", $bet_event_name);
$bet_event_name = str_replace("Hellerup IK", "HIK", $bet_event_name);
$bet_event_name = str_replace("Vejgaard BSK", "Vejgaard B", $bet_event_name);
$bet_event_name = str_replace("FC Sydvest 05", "FC Sydvest", $bet_event_name);
$bet_event_name = str_replace("AB Gladsaxe", "AB", $bet_event_name);
$bet_event_name = str_replace("- Feminino", "(Feminino)", $bet_event_name);
$bet_event_name = str_replace(" - ", " v ", $bet_event_name);
$bet_event_name = str_replace("FK Krasnodar Juventude", "Krasnodar (Res)", $bet_event_name);
$bet_event_name = str_replace("FC Sóchi J", "FC Sóchi (Res)", $bet_event_name);
$bet_event_name = str_replace("FC Tambov J", "FC Tambov (Res)", $bet_event_name);
$bet_event_name = str_replace("Armadale SC", "Armadale", $bet_event_name);
$bet_event_name = str_replace("Perth Glory FC Youth", "Perth Glory Sub-21", $bet_event_name);
$bet_event_name = str_replace("Adelaide United Youth", "Adelaide United Sub-21", $bet_event_name);
$bet_event_name = str_replace("West Adelaide FC", "West Adelaide", $bet_event_name);
$bet_event_name = str_replace("South Adelaide FC", "South Adelaide", $bet_event_name);
$bet_event_name = str_replace("Sunshine Coast Fire", "Sunshine Coast", $bet_event_name);
$bet_event_name = str_replace("Gold Coast Knights SC", "Gold Coast Knights", $bet_event_name);
$bet_event_name = str_replace("Eltersdorf", "SC Eltersdorf", $bet_event_name);
$bet_event_name = str_replace("FC Eintracht Bamberg 2010", "FC Eintracht Bamberg", $bet_event_name);
$bet_event_name = str_replace("JK Welco Elekter", "Tartu JK Welco", $bet_event_name);
$bet_event_name = str_replace("Flora Tallinn Sub-21", "Flora Tallinn II", $bet_event_name);
$bet_event_name = str_replace("Tallinna", "Tallinna JK Legion", $bet_event_name);
$bet_event_name = str_replace("JK Tallinna Kalev U21", "JK Tallinna Kalev II", $bet_event_name);
$bet_event_name = str_replace("Jalgpallikool Tammeka", "JK Tammeka Tartu", $bet_event_name);
$bet_event_name = str_replace("Utrecht v Reservas", "Utrecht - Reservas", $bet_event_name);
$bet_event_name = str_replace("PSV v Reservas", "PSV - Reservas", $bet_event_name);
$bet_event_name = str_replace("Ajax v Reservas", "Ajax - Reservas", $bet_event_name);
$bet_event_name = str_replace("AZ v Reservas", "AZ - Reservas", $bet_event_name);
$bet_event_name = str_replace("FC Basel", "Basileia", $bet_event_name);
$bet_event_name = str_replace("Luzern", "Lucerne", $bet_event_name);
$bet_event_name = str_replace("Sion", "FC Sion", $bet_event_name);							
$bet_event_name = str_replace("Zurique", "FC Zurique", $bet_event_name);										
$bet_event_name = str_replace("Wil v ", "Wil 1900 v ", $bet_event_name);										
$bet_event_name = str_replace(" v Wil", " v Wil 1900", $bet_event_name);										
$bet_event_name = str_replace("Grasshopper", "Grasshoppers", $bet_event_name);										
$bet_event_name = str_replace("PS Tira", "PS TIRA", $bet_event_name);										
$bet_event_name = str_replace("Persepar Kalteng Putra", "Kalteng Putra FC", $bet_event_name);	
$bet_event_name = str_replace("Arema Indonesia", "Arema FC", $bet_event_name);										
$bet_event_name = str_replace("Bhayangkara Surabaya United", "Bhayangkara FC", $bet_event_name);
$bet_event_name = str_replace("Oita Trinita", "Oita", $bet_event_name);										
$bet_event_name = str_replace("JEF United Chiba", "JEF Utd Chiba", $bet_event_name);										
$bet_event_name = str_replace("Tochigi", "Tochigi SC", $bet_event_name);										
$bet_event_name = str_replace("Ehime", "Ehime FC", $bet_event_name);										
$bet_event_name = str_replace("Gifu", "FC Gifu", $bet_event_name);										
$bet_event_name = str_replace("Kyoto", "Kyoto Sanga FC", $bet_event_name);										
$bet_event_name = str_replace("Tombense MG", "Tombense", $bet_event_name);										
$bet_event_name = str_replace("Paysandu PA", "Paysandu", $bet_event_name);										
$bet_event_name = str_replace("Sao Jose RS", "São José PA", $bet_event_name);										
$bet_event_name = str_replace("Remo", "Clube do Remo", $bet_event_name);										
$bet_event_name = str_replace("Globo RN", "Globo FC", $bet_event_name);										
$bet_event_name = str_replace("Santa Cruz", "Santa Cruz FC", $bet_event_name);										
$bet_event_name = str_replace("Manaus Fc/Am", "Manaus", $bet_event_name);										
$bet_event_name = str_replace("Latvia", "Letônia", $bet_event_name);										
$bet_event_name = str_replace("Republic of Korea ", "Coreia do Sul", $bet_event_name);										
$bet_event_name = str_replace("Hungary", "Hungria", $bet_event_name);										
$bet_event_name = str_replace("Thailand", "Tailândia", $bet_event_name);										
$bet_event_name = str_replace("Talk N Text Tropang Texters", "TNT KaTropa", $bet_event_name);							$bet_event_name = str_replace("Barangay Ginebra Kings", "Barangay Ginebra San Miguel", $bet_event_name);			$bet_event_name = str_replace("W.", "(Feminino)", $bet_event_name);										
$bet_event_name = str_replace("Sunshine Coast Rip", "USC Rip City", $bet_event_name);								$bet_event_name = str_replace("South West Metro Pirates", "SW Metro Pirates", $bet_event_name);
$bet_event_name = str_replace("Wil 1900lem II", "Willem II", $bet_event_name);				
$bet_event_name = str_replace(" (Virtual)", " SRL", $bet_event_name);
$bet_event_name = str_replace(" Fifa", " ", $bet_event_name);
$bet_event_name = str_replace("CFC Catalonia FC", "Catalonia FC (CFC)", $bet_event_name);
$bet_event_name = str_replace("Ez1 Ez1D 11", "Vamos Ez1d (EZ1)", $bet_event_name);
$bet_event_name = str_replace("Nfc Nyancat FC", "Nyancat (NFC)", $bet_event_name);
$bet_event_name = str_replace("Fin Fints", "Fints (FIN)", $bet_event_name);
$bet_event_name = str_replace("Cfi Confession", "Confession (CFI)", $bet_event_name);
$bet_event_name = str_replace("Nht Newton Heath", "Newton Heath (NHT)", $bet_event_name);
$bet_event_name = str_replace("Tbr Tabula Rasa", "Tabula Rasa (TBR)", $bet_event_name);
$bet_event_name = str_replace("Pnz Im Not Over", "Im Not Over (PNZ)", $bet_event_name);
$bet_event_name = str_replace("Aru Arukonda", "Arukonda (ARU)", $bet_event_name);
$bet_event_name = str_replace("Own Owned", "Owned (OWN)", $bet_event_name);
$bet_event_name = str_replace("Meg Megapolice", "Megapolice (MEG)", $bet_event_name);
$bet_event_name = str_replace("Xal Xalinho FC", "Xalinho FC (XAL)", $bet_event_name);

if ($event_name == 'Copa Libertadores') {
$bet_event_name = str_replace("Guarani", "Club Guarani", $bet_event_name);
}
				
if ($event_name == 'Copa Sulamericana') {
$bet_event_name = str_replace("Guarani", "Club Guarani", $bet_event_name);
}
				
if ($event_name == 'Paraguai - Division Profesional') {
$bet_event_name = str_replace("Guarani", "Club Guarani", $bet_event_name);
}				

if ($event_name == 'Brasil - Campeonato Carioca') {
$bet_event_name = str_replace("Boavista", "Boavista SC Saquarema", $bet_event_name);
}
				
if ($event_name == 'Portugal - Liga Zon Sagres') {							
$bet_event_name = str_replace("Nacional", "CD Nacional", $bet_event_name);
}

if ($event_name == 'Colômbia - Primera B') {							
$bet_event_name = str_replace("Atlético", "Atlético Cali", $bet_event_name);
}	
				
$bet_event_name = str_replace("Central PE", "Central SC", $bet_event_name);
$bet_event_name = str_replace("Macae Esporte RJ", "Macaé Esporte RJ", $bet_event_name);
$bet_event_name = str_replace("Unirb FC BA", "UNIRB FC", $bet_event_name);
$bet_event_name = str_replace("Frei Paulistano SE", "AD Frei Paulistano", $bet_event_name);							
$bet_event_name = str_replace("Cs Maruinense Se", "CS Maruinense", $bet_event_name);							
$bet_event_name = str_replace("Afogados Ingazeira PE", "Afogados da Ingazeira FC", $bet_event_name);
$bet_event_name = str_replace("GE Juventus SC", "Juventus SC", $bet_event_name);							
$bet_event_name = str_replace("Doce Mel EC BA", "Doce Mel Esporte Clube", $bet_event_name);							
$bet_event_name = str_replace("Alagoinhas Atlético Clube", "Alagoinhas AC", $bet_event_name);							
$bet_event_name = str_replace("SE Juventude MA", "Juventude MA", $bet_event_name);							
$bet_event_name = str_replace("Pouso Alegre MG", "Pouso Alegre", $bet_event_name);							
$bet_event_name = str_replace("Coimbra EC MG", "Coimbra EC", $bet_event_name);							
$bet_event_name = str_replace("Patrocinense MG", "CA Patrocinense", $bet_event_name);							
$bet_event_name = str_replace("Athletic Club Sjdr MG", "Athletic Club MG", $bet_event_name);							
$bet_event_name = str_replace("Aimore RS", "Aimoré", $bet_event_name);							
$bet_event_name = str_replace("CE Bento Goncalves RS", "Esportivo Bento Goncalves", $bet_event_name);
$bet_event_name = str_replace("Novo Hamburgo RS", "Novo Hamburgo", $bet_event_name);							
$bet_event_name = str_replace("Urt MG", "União Recreativa dos Trabalhadores", $bet_event_name);							
$bet_event_name = str_replace("Uberlandia", "Uberlândia", $bet_event_name);							
$bet_event_name = str_replace("Itabaiana SE", "Itabaiana", $bet_event_name);							
$bet_event_name = str_replace("Boca Junior SE", "Sociedade Boca Júnior", $bet_event_name);							
$bet_event_name = str_replace("Anapolis GO", "Anapolis FC", $bet_event_name);							
$bet_event_name = str_replace("Grêmio Anapolis GO", "Gremio Anapolis", $bet_event_name);							
$bet_event_name = str_replace("Jataiense GO", "AE Jataiense", $bet_event_name);							
$bet_event_name = str_replace("EC Prospera SC", "EC Prospera", $bet_event_name);							
$bet_event_name = str_replace("Jaragua EC", "Jaraguá EC", $bet_event_name);
$bet_event_name = str_replace("Crac GO", "CRAC", $bet_event_name);				
$bet_event_name = str_replace("Western WS Wanderers (Feminino)", "WS Wanderers (Feminino)", $bet_event_name);
$bet_event_name = str_replace("Dorense SE", "Dorense", $bet_event_name);
$bet_event_name = str_replace("Ad Atlético Gloriense Se", "AD Atlética Gloriense", $bet_event_name);
$bet_event_name = str_replace("Lagarto SE", "Lagarto FC", $bet_event_name);
$bet_event_name = str_replace("Serc MS", "SERC (Chapadão)", $bet_event_name);
$bet_event_name = str_replace("Novoperario", "Novoperario FC", $bet_event_name);
$bet_event_name = str_replace("Dourados Ac Ms", "Dourados AC", $bet_event_name);
$bet_event_name = str_replace("Se Tiradentes Pi", "Tiradentes PI", $bet_event_name);
$bet_event_name = str_replace("Fluminense Ec Pi", "Fluminense PI", $bet_event_name);				

if ($sport_name == 'Basquetebol') {
$bet_event_name = str_replace("Dijon", "JDA Dijon Basket", $bet_event_name);
$bet_event_name = str_replace("Estrasburgo", "SIG Strasbourg", $bet_event_name);
$bet_event_name = str_replace("Groningen", "Donar Groningen", $bet_event_name);
$bet_event_name = str_replace("Rotterdam Basketbal College", "Feyenoord Basketbal", $bet_event_name);
$bet_event_name = str_replace("Nurnberg", "Nürnberg Falcons BC", $bet_event_name);
$bet_event_name = str_replace("Heidelberg", "Academics Heidelberg", $bet_event_name);
$bet_event_name = str_replace("Leicester", "Leicester Riders", $bet_event_name);
$bet_event_name = str_replace("Plymouth", "Plymouth Raiders", $bet_event_name);
$bet_event_name = str_replace("Verona", "Tezenis Verona", $bet_event_name);
$bet_event_name = str_replace("Torino", "Basket Torino", $bet_event_name);
$bet_event_name = str_replace("Spm Shoeters Den Bosch", "Heroes Den Bosch", $bet_event_name);
$bet_event_name = str_replace("Newcastle", "Newcastle Eagles", $bet_event_name);
$bet_event_name = str_replace("Panathinaikos", "Panathinaikos BC", $bet_event_name);
$bet_event_name = str_replace("PAOK Thessaloniki", "PAOK BC", $bet_event_name);
$bet_event_name = str_replace("Sheffield", "Sheffield Sharks", $bet_event_name);
$bet_event_name = str_replace("Bayern Munique", "Bayern München Basket", $bet_event_name);
$bet_event_name = str_replace("Real Madrid", "Real Madrid Basket", $bet_event_name);
$bet_event_name = str_replace("CSKA Moscow", "CSKA Moscow Basket", $bet_event_name);
$bet_event_name = str_replace("Monaco", "AS Monaco Basket", $bet_event_name);
$bet_event_name = str_replace("Trapani", "Pallacanestro", $bet_event_name);
$bet_event_name = str_replace("Valência", "Valencia Basket", $bet_event_name);
$bet_event_name = str_replace("Murcia", "UCAM Murcia Basket", $bet_event_name);
$bet_event_name = str_replace("London", "London Lions", $bet_event_name);
$bet_event_name = str_replace("Chemnitz", "BV Chemnitz 99", $bet_event_name);
$bet_event_name = str_replace("Caceres Patrimonio", "Pallacanestro", $bet_event_name);
$bet_event_name = str_replace("Huesca", "Peñas Huesca", $bet_event_name);
$bet_event_name = str_replace("Granada", "CB Granada", $bet_event_name);
$bet_event_name = str_replace("Melilla", "Melilla Baloncesto", $bet_event_name);
$bet_event_name = str_replace("Cb Tizona", "Ford Burgos", $bet_event_name);
$bet_event_name = str_replace("Akasvayu Girona", "Basquet Girona", $bet_event_name);
$bet_event_name = str_replace("Nantes", "Nantes Atlantique", $bet_event_name);
$bet_event_name = str_replace("Denain", "AS Denain", $bet_event_name);
$bet_event_name = str_replace("Union Poitiers Basket 86", "Poitiers Basket 86", $bet_event_name);
$bet_event_name = str_replace("Lille", "Lille Métropole", $bet_event_name);
$bet_event_name = str_replace("MMT Estudiantes", "Movistar Estudiantes", $bet_event_name);
$bet_event_name = str_replace("Fuenlabrada", "Urbas Fuenlabrada", $bet_event_name);
}					
				?>
                <?php
					$table_name = $wpdb->prefix . "bp_bet_events";
                    $querystr = "SELECT * FROM $table_name WHERE bet_event_name= '$bet_event_name'";
                    $pageposts = $wpdb->get_results($querystr, OBJECT);
					if (count($pageposts) <= 0):
                ?>
                <div class="event-<?php echo $bet_events['id']; ?>" style="padding-left:15px;display:none">
 
                    <input 
                        type="checkbox"
                        id="bet-event-<?php echo $categories['id']; ?>"
                        name="bet_events[]"
                        value="<?php echo $categories['id'] . '/' . $bet_events['id']; ?>"
                        />

                    <label for="bet-event-<?php echo $categories['id']; ?>"><?php echo $bet_event_name;?> (<?php echo (isset($pageposts[0]->deadline) && $pageposts[0]->deadline!='')?betpress_local_tz_time($pageposts[0]->deadline):''?>)</label>

                </div>
                	<?php endif; ?>
                    <?php endif; ?>
                    <?php if (is_array($categories)): ?>
                
                        <?php foreach ($categories as $category_name => $bet_options): ?>
<?php if ('id' != $category_name):
				
$category_name = str_replace("Resultado duplo", "Dupla Hipótese", $category_name);
$category_name = str_replace("Resultado sem empate", "Empate Anula Aposta", $category_name);
$category_name = str_replace("Resultado", "Resultado Final", $category_name);
$category_name = str_replace("Resultado Final correcto", "Resultado Correto", $category_name);
$category_name = str_replace("Resultado Final ao intervalo", "Intervalo - Resultado", $category_name);						$category_name = str_replace("As duas equipas marcam", "Para Ambos os Times Marcarem", $category_name);
$category_name = str_replace("Total de golos - acima/abaixo", "Gols Mais/Menos", $category_name);
$category_name = str_replace("1.ª equipa a marcar", "Primeiro Time a Marcar", $category_name);
$category_name = str_replace("Resultado Final handicap", "Handicap - Resultado", $category_name);
$category_name = str_replace("Oportunidade dupla - 1.ª parte", "1° Tempo - Dupla Hipótese", $category_name);
$category_name = str_replace("Oportunidade dupla - 2.ª parte", "2° Tempo - Dupla Hipótese", $category_name);
$category_name = str_replace("Resultado Final da 2.ª parte", "Resultado do 2° Tempo", $category_name);
$category_name = str_replace("Total de golos na 1.ª parte", "1º Tempo - Total de Gols", $category_name);
$category_name = str_replace("Ambas as equipas marcam na 1.ª parte", "1º Tempo - Para Ambos os Times Marcarem", $category_name);
$category_name = str_replace("Intervalo - Resultado/final", "Intervalo / Final do Jogo", $category_name);
$category_name = str_replace("Vencedor do jogo", "Para Vencer a Partida", $category_name);
$category_name = str_replace("Vencedor do encontro", "Para Ganhar a Partida", $category_name);
$category_name = str_replace("Total de pontos", "Totais do Jogo", $category_name);
$category_name = str_replace("Resultado Final final", "Resultado Final", $category_name);
$category_name = str_replace("Vencedor do combate", "Para Ganhar a Luta", $category_name);
$category_name = str_replace("Tempo do 1.º golo", "Momento do Primeiro Gol", $category_name);
$category_name = str_replace("1.º marcador", "Primeiro Jogador a Marcar um Gol", $category_name);
$category_name = str_replace("Último marcador", "Último Jogador a Marcar um Gol", $category_name);
$category_name = str_replace("Marcador", "Para Marcar a Qualquer Momento", $category_name);
$category_name = str_replace("Total de jogos", "Total de Jogos", $category_name);
$category_name = str_replace("Total de sets", "Total de Sets", $category_name);
$category_name = str_replace("Aposta de sets", "Apostas no Set", $category_name);
$category_name = str_replace("Handicap - Resultado de runs", "Handicap Alternativo", $category_name);
$category_name = str_replace("Total de runs", "Totais do Jogo", $category_name);
              if ($sport_name == 'Basquetebol') {
$category_name = str_replace("Resultado Final", "Odds - Incluíndo Empate", $category_name); 
			   }				
              ?>
                
                <div class="bet-event-<?php echo $categories['id']; ?>" style="padding-left:30px;display:none; color: #dd0000; font-family: Verdana !important; font-size: 16px!important">

                    <input 
                        type="checkbox"
                        id="category-<?php echo $bet_options['id']; ?>"
                        name="categories[]"
                        value="<?php echo $bet_options['id'] . '/' . $categories['id']; ?>"
                        />

                    <label for="category-<?php echo $bet_options['id']; ?>"><?php echo $category_name; ?></label>

                </div>
                
                            <?php endif; ?>
                        <?php endforeach; ?>
                
                    <?php endif; ?>
                
                <?php endforeach; ?>
                
            <?php endif; ?>
                
        <?php endforeach; ?>
                
    <?php endif; ?>
                
<?php endforeach; ?>

            </td>

        </tr>
        
        <tr>
            
            <th>
                
                <input type="submit" name="inserting_xml_data" value="<?php esc_attr_e('Inserir dados selecionados', 'betpress'); ?>" class="button-primary" />
                
            </th>
            
        </tr>

    </table>
    
    </form>
    
</div>