<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

if(isset(getallheaders()['X-Forwarded-For'])) {
	$ips = explode(',', getallheaders()['X-Forwarded-For']);
}



$user = [];

if(isset($_COOKIE[$sessionName]))
{
    $sql = "
 SELECT
	id,
	nome_completo,
	cpf,
	celular,
	objeto AS tipo,
	objeto AS setor,
	perm as perms,
    marcas,
	token,
	'' AS prescricao_sem_receita,
	'' AS inicio_ag,
	'' AS fim_ag
FROM
	USUARIOS AS U 
	WHERE 
	MD5(U.token) = :token
 UNION ALL
 SELECT
	id,
	nome_completo,
	cpf,
	celular,
	objeto AS tipo,
	objeto AS setor,
	perm as perms,
    '[]' AS marcas,
	token,
	prescricao_sem_receita,
	inicio_ag,
	fim_ag
FROM
	MEDICOS AS M 
	WHERE 
	MD5(M.token) = :token
	UNION ALL
	(
	SELECT
	id,
	nome_completo,
	cpf,
	celular,
	objeto AS tipo,
	objeto AS setor,
	perm as perms,
    '[]' AS marcas,
	token,
	'' AS prescricao_sem_receita,
	'' AS inicio_ag,
	'' AS fim_ag
	FROM
		PACIENTES AS P
		WHERE 
		MD5(P.token) = :token
	) UNION ALL
	(
	SELECT
	id,
	nome_completo,
	cpf,
	celular,
	objeto AS tipo,
	setor,
	perm as perms,
    '[]' AS marcas,
	token,
	'' AS prescricao_sem_receita,
	'' AS inicio_ag,
	'' AS fim_ag
	FROM
	FUNCIONARIOS AS F
	WHERE 
	MD5(F.token) = :token
	)";


  if(isset($_COOKIE[$sessionName]))
  {
	$stmt = $pdo->prepare($sql);

    $stmt->bindValue(':token', $_COOKIE[$sessionName]);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_OBJ);

	$sql = "SELECT * FROM PERMISSOES WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $user->perms);
    $stmt->execute();

	$sqlx = "
 SELECT
	id,
	nome_completo,
	cpf,
	celular,
	objeto AS tipo,
	objeto AS setor,
	perm as perms,
    marcas,
	token,
	'' AS prescricao_sem_receita,
	'' AS inicio_ag,
	'' AS fim_ag,
	'' AS perms_adicional,
	objeto AS esp
FROM
	USUARIOS AS U 
	WHERE 
	MD5(U.token) = '{$_COOKIE[$sessionName]}'
 UNION ALL
 SELECT
	id,
	nome_completo,
	cpf,
	celular,
	objeto AS tipo,
	objeto AS setor,
	perm as perms,
    '[]' AS marcas,
	token,
	prescricao_sem_receita,
	inicio_ag,
	fim_ag,
	'' AS perms_adicional,
	(SELECT nome FROM ESPECIALIDADES WHERE id = especialidade) AS esp
FROM
	MEDICOS AS M 
	WHERE 
	MD5(M.token) = '{$_COOKIE[$sessionName]}'
	UNION ALL
	(
	SELECT
	id,
	nome_completo,
	cpf,
	celular,
	objeto AS tipo,
	objeto AS setor,
	perm as perms,
    '[]' AS marcas,
	token,
	'' AS prescricao_sem_receita,
	'' AS inicio_ag,
	'' AS fim_ag,
	'' AS perms_adicional,
	esp
	FROM
		PACIENTES AS P
		WHERE 
		MD5(P.token) = '{$_COOKIE[$sessionName]}'
	) UNION ALL
	(
	SELECT
	id,
	nome_completo,
	cpf,
	celular,
	objeto AS tipo,
	setor,
	perm as perms,
    '[]' AS marcas,
	token,
	'' AS prescricao_sem_receita,
	'' AS inicio_ag,
	'' AS fim_ag,
	perms_adicional,
	objeto AS esp
	FROM
	FUNCIONARIOS AS F
	WHERE 
	MD5(F.token) = '{$_COOKIE[$sessionName]}'
	)";
    

	if($stmt->rowCount() > 0){
		$user->perms = $stmt->fetch(PDO::FETCH_OBJ);
    
		if(isset($user->marcas)) {
			$user->marcas = json_decode($user->marcas, true);
		}
	}
  }
}


function generateBreadcrumb($path): string
{
    $pathArray = explode('/', trim($path, '/'));
    $breadcrumb = [];
    $linkPath = '';
	$breadcrumb[] = '<img src="/assets/images/ico-home-breadcrumbs.svg" class="ico-home-breadcrumbs" alt=""><li><a href="/">Home</a></li>';

	$prev = '';

    foreach ($pathArray as $segment) {
        $linkPath .= '/' . $segment;

        if($segment != 'index.php' && strlen(trim($segment)) > 3) {
			if(!str_contains($segment, '?')) {
			if(strtolower($prev) == 'perfil'){
				$breadcrumb[] = '<li><a href="' . $linkPath . '">Meu Perfil</a></li>';
			} else if($segment == 'editar') {
                $array = explode('/', $path);
                $breadcrumb[] = '<li><a href="' . $linkPath.'/'.end($array) . '">Editar</a></li>';
				break;
			}
			else {
				$breadcrumb[] = '<li><a href="' . $linkPath . '">' . ucfirst($segment) . '</a></li>';
			}

			$prev = $segment;
		    }
		}
    }

    return implode('', $breadcrumb);
}


function getStringBetween($str, $user)
{
   $result = $str;

  foreach ($user as $key => $value) {
    $result = str_replace("{{".$key."}}", $value, $result);
  }

  return $result;
}


$notificacoesMsg = [
	'cadastro' => "Bem-vindo(a) à nossa clínica!

	Olá *{{nome_completo}}*,

	Senha Provisória: *{{senha}}*
	
	É com grande satisfação que recebemos você em nossa clínica. Agradecemos por confiar em nossa equipe para cuidar da sua saúde e bem-estar. Estamos comprometidos em oferecer o melhor atendimento, com profissionais qualificados e dedicados a proporcionar uma experiência acolhedora e eficiente.
	
	Nosso objetivo é garantir que você se sinta confortável e seguro(a) durante todas as suas visitas. Se tiver qualquer dúvida ou precisar de informações adicionais, não hesite em nos contatar. Estamos aqui para ajudar!
	
	Desejamos muita saúde e esperamos que sua experiência conosco seja sempre positiva.
	
	Atenciosamente,
	
	CLINABS CENTRO DE TELEMEDICINA
	(41) 3300-0790",
	'cadastro_medico' => "Bem-vindo(a) à nossa Clínica!

	Prezado(a) Dr(a). *{{nome_completo}}*,
	
	É com grande satisfação que recebemos o seu cadastro em nossa clínica. Estamos entusiasmados em tê-lo(a) como parte de nossa equipe e confiamos que sua experiência e dedicação serão fundamentais para proporcionar um atendimento de excelência aos nossos pacientes.
	
	Aqui na CLINABS, valorizamos a colaboração e o crescimento profissional. Estamos comprometidos em oferecer um ambiente de trabalho acolhedor e recursos para apoiar sua prática médica. Nossa missão é proporcionar o melhor cuidado possível aos nossos pacientes, e acreditamos que sua contribuição será essencial para alcançarmos esse objetivo.
	
	Nos próximos dias, nossa equipe entrará em contato para fornecer todas as informações necessárias sobre a integração e os recursos disponíveis. Caso tenha qualquer dúvida ou precise de assistência, por favor, não hesite em nos contactar.
	
	Mais uma vez, seja bem-vindo(a) à CLINABS. Estamos ansiosos para trabalhar juntos e alcançar grandes realizações!
	
	Atenciosamente,
	
	CLINABS CENTRO DE TELEMEDICINA
	(41) 3300-0790
	https://www.clinabs.com
	"
];




$ALLOWED_IP = [];

$ipstmt = $pdo->query('SELECT * FROM `IPS_PERMITIDOS`');

foreach($ipstmt->fetchAll(PDO::FETCH_OBJ) as $ip) {
	$ALLOWED_IP[] = $ip->ip;
}


try {
	$stmt = $pdo->prepare("INSERT INTO
  `ACCESS_LOGS` (
    `page`,
    `user`, 
    `ip`,
	`method`,
	`data`
  )
values
  (
    :page, 
    :user, 
    :ip,
	:method,
	:data
  );");


  $stmt->bindValue(":page", explode('?', $_SERVER['REQUEST_URI'])[0], PDO::PARAM_STR);
  $stmt->bindValue(":user", $user->nome_completo ?? '', PDO::PARAM_STR);
  $stmt->bindValue(":ip", $_SERVER['REMOTE_ADDR'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'], PDO::PARAM_STR);
  $stmt->bindValue(':method', $_SERVER['REQUEST_METHOD'] ?? 'GET', PDO::PARAM_STR);
  $stmt->bindValue(':data', file_get_contents('php://input'), PDO::PARAM_STR);

  $ignore_scripts = [
	'/cron/crontab-exec.php', 
	'/carrinho/calcprodutos.php', 
	'/forms/session.sync.php', 
	'/forms/whereby.sync.php', 
	'/api/webhook/asaas.php', 
	'/forms/schedule.calendar.php', 
	'/perfil/wa-status.php',
	'/dashboard/api.php'
  ];
  
  if(!in_array($_SERVER['SCRIPT_NAME'], $ignore_scripts) && file_get_contents('php://input') != '[object Object]' && file_get_contents('php://input') != '') {
	$stmt->execute();
  }
} catch (PDOException $e) {
  file_put_contents('last_erros_logs.txt', print_r([
  'ip' => $_SERVER['REMOTE_ADDR'] ?? $_SERVER['HTTP`_X_FORWARDED_FOR'],
  'method' => $_SERVER['REQUEST_METHOD'] ?? 'GET',
  'data' => file_get_contents('php://input'),
  'error' => $e->getMessage()
  ], true));
}


if(isset($user) && isset($_REQUEST)) {
	try {
		$ip = getallheaders()['X-Forwarded-For'] ?? $_SERVER['REMOTE_ADDR'];	
		$ut = $_SERVER['SCRIPT_NAME'];
		$b64 = base64_encode(json_encode($_REQUEST, JSON_PRETTY_PRINT));
		$pdo->query("INSERT INTO `USER_LOGS` (`nome_completo`, `tipo_usuario`, `page`, `user_id`, `data`, `ip`) VALUES ('{$user->nome_completo}', '{$user->tipo}', '{$ut}', '{$user->token}', '{$b64}', '{$ip}');");
		} catch (PDOException $e) {
			file_put_contents('last_erros_logs.txt', print_r([
			'ip' => $_SERVER['REMOTE_ADDR'] ?? $_SERVER['HTTP`_X_FORWARDED_FOR'],
			'method' => $_SERVER['REQUEST_METHOD'] ?? 'GET',
			'data' => $_REQUEST,
			'error' => $e->getMessage()
			], true),
			FILE_APPEND);
	}
}