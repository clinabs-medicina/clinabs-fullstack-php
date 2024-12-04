<?php
$post = array(
    array(
        'id' => 1,
        'date' => '2024-05-29 19:01:17',
        'image' => 'https://static.wixstatic.com/media/8a9379c91e6042f887fd3637603c3f6b.jpg/v1/fit/w_100,h_67,al_c,q_80/file.webp',
        'subject' => 'Saúde Infantil',
        'text_desc' => 'Descreva a categoria do seu fórum. Chame atenção dos seus leitores e incentive-os a ler.',
        'nome_completo' => 'SEBASTIÃO DANILO VIEIRA',
        'medico_token' => '65fb460097bb5',
        'especialidade' => 'CIRURGIA GERAL',
        'posts' => array(
            array(
                'id' => 1,
                'post_id' => 1,
                'medico_token' => '65fb460097bb5',
                'post_type' => 'POST',
                'content' => 'PHA+dGVzdGUgZGUgcG9zdCBjb20gaW1hZ2VtLjwvcD4KPHA+Jm5ic3A7PC9wPgo8cD48aW1nIHNyYz0iLi4vLi4vZGF0YS9ibG9nL2ltYWdlcy84ODZhNmEwY2EzNmQ5MjhkZWZhMmM2ZTk1MGJmNDQ3OC5wbmciIHdpZHRoPSIxNTIiIGhlaWdodD0iMTUyIj48L3A+'
            ),
            array(
                'id' => 1,
                'post_id' => 1,
                'medico_token' => '65fb460097bb5',
                'post_type' => 'ANSWER',
                'content' => 'PHA+b25kZSBjb21wcm8gZXN0YSA8c3Ryb25nPnJlbSZlYWN1dGU7ZGlvPC9zdHJvbmc+PzwvcD4='
            ),
            array(
                'id' => 1,
                'post_id' => 1,
                'medico_token' => '65fb460097bb5',
                'post_type' => 'ANSWER',
                'content' => 'PHA+b25kZSBjb21wcm8gZXN0YSA8c3Ryb25nPnJlbSZlYWN1dGU7ZGlvPC9zdHJvbmc+PzwvcD4='
            ),
            array(
                'id' => 1,
                'post_id' => 1,
                'medico_token' => '65fb460097bb5',
                'post_type' => 'ANSWER',
                'content' => 'PHA+b25kZSBjb21wcm8gZXN0YSA8c3Ryb25nPnJlbSZlYWN1dGU7ZGlvPC9zdHJvbmc+PzwvcD4='
            ),
            array(
                'id' => 1,
                'post_id' => 1,
                'medico_token' => '65fb460097bb5',
                'post_type' => 'RESPONSE',
                'content' => 'PHA+bm8gc2l0ZSA8c3Ryb25nPmNsaW5hYnMuY29tPC9zdHJvbmc+PC9wPg=='
            ),
        ),
        'token' => '59e6e56t5t6',
    )
);



header('Content-Type: application/json');
print(json_encode($post, JSON_PRETTY_PRINT));