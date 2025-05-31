"# avaliacao-final-iss" 
-
- João Pedro de Oliveira dos Santos Costa
-
-
## api-sensores ## 

### dependencias: axios, express e redis ###

Primeiramente, na api-sensores, voce pode inicia-la com um **node app.js**, ela possui 2 endpoints, o GET/sensor-data que retorna uma simulação de dados presentes em memória, estes dados são cacheados no Redis por 10 segundos, caso a chave sensorData existir no Redis, os dados são retornados diretamente do cache, otimizando a consulta, já a rota POST/alert envia um alerta para a **api-eventos**, que armazena este evento exibindo uma mensagem exemplo: "Temperatura muito alta" e o nível critico desta mensagem como "CRITICAL", ela envia o alerta para a **api-eventos** via HTTP, existe no arquivo **.env**, a variável **PYTHON_API_URL=http://localhost:3002/event** retornando uma mensagem de sucesso ou erro.

## api-eventos ##

### dependencias: flask, redis e pika ###

Primeiramente, na api-eventos, voce pode inicia=la com um **python app.py**, ela possui também 2 endpoints, o POST/event que recebe um alerta no formato json da **api-sensores** e em seguida atualiza o cache do Redis com a lista completa de eventos, exibe uma mensagem mostrando que o evento foi registrado "event stored", ja no endpoint /events, como dito acima é retornado todos os eventos registrados e estes dados vem diretamente do cache do **Redis**, esta api também consome o RabbitMQ, que ao enviarmos a mensagem da nossa **api-logistica**, é exibido no terminal uma mensagem: "mensagem recebida", porém, a mensagem e guarda na fila no rabbitMQ e consumida rapidamente, não dando tempo de visualiza-la pela interface do rabbitMQ, caso queira ve-la, comente a linha "start_consumer_thread()", do arquivo app.py, caso não queira ver, será exibido no terminal a mensagem citada acima. A fila utilizada se chama "alerts" e está definida no arquivo **.env**.

## api-logistica ##

### dependencias: composer e php-amqplib/php-amqplib": "^2.8 ###

Primeiramente, api-logistica, voce pode incia-la por meio do comando php -S **localhost:8000**, ela possui também 2 endpoints, a GET/equipments que retorna uma lista que simula equipamentos, e por último possui o POST/dispatch que publica uma mensagem na fila **alerts** do rabbitMQ, e que é consumida pela **api-eventos** que está escutando esta fila.






