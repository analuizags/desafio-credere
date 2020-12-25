# Teste da Credere

## Descrição
Uma sonda exploradora da NASA pousou em marte. O pouso se deu em uma área retangular, na qual a sonda pode navegar usando uma interface web. A posição da sonda é representada pelo seu eixo x e y, e a direção que ele está apontado pela letra inicial, sendo as direções válidas:

- `E` - Esquerda
- `D` - Direita
- `C` - Cima
- `B` - Baixo

A sonda aceita três comandos:

- `GE` - girar 90 graus à esquerda
- `GD` - girar 90 graus à direta
- `M` - movimentar. Para cada comando `M` a sonda se move uma posição na direção à qual sua face está apontada.

## Endpoints

Esperamos três endpoints, um que envie a sonda para a posição inicial (0,0); outro deve receber o movimento da sonda e responder com as coordenadas finais, caso o movimento seja válido ou erro caso o movimento seja inválido; e o terceiro deve responder apenas com as coordenadas atuais x e y da sonda.

## Instalação/Execução

### Configurando credenciais
Primeiramente, é necessário configurar a conexão com o banco de dados MySQL local, para isso crie o arquivo ```config/config-dev.json```, adicionando credenciais válidas.

```json
{
  "database": {
    "host": "localhost",
    "user": "usuario",
    "password": "senha",
    "db": "nome_banco",
    "drive": "mysql"
  }
}
```

### Sem Docker
Para a execução do projeto sem Docker é necessário ter um servidor local na máquina com:
- Apache
- MySQL
Para a configuração da base de dados é necessário rodar os seguintes comandos:
```bash
$ mysql -u root -p <nome_da_base> < /config/scriptBanco.sql
```

### Com Docker 🐳
Para a execução do projeto com Docker é necessário ter o mesmo instalado na máquina. Para inicia-lo:
```bash
$ docker-compose build
$ docker-compose up -d
```

Para a configuração da base de dados é necessário rodar os seguintes comandos:
```bash
$ docker exec -it <nome_do_container> bash
$ mysql -u root -p desafio-credere < /backup/scriptBanco.sql
```

Após a execução dos comandos, o servidor estará disponível em [localhost/](http://localhost/)

## Utilização

HTTP requisição                      | Descrição                            | Exemplo
------------------------------------ | ------------------------------------ | ------------------------
**POST** /sonda                      | Cria uma sonda                       | 
**GET** /sonda/{id}/verificarPosicao | Exibe a posição atual da sonda       | 
**PATCH** /sonda/{id}/movimentar     | Move a sonda                         | 
**PUT** /sonda/{id}/reposicionar     | Retorna a sonda para posição inicial |

### POST /sonda
Cria sonda na posição padrão (0,0).
Exemplo de resposta:
```json
{
    "id": 1
}
```

### GET /sonda/{id}/verificarPosicao
Exibe a posição atual da sonda.
Exemplo de resposta:
```json
{
  "eixoX": "0",
  "eixoY": "0",
  "direcao": "D"
}
```

### PATCH /sonda/{id}/movimentar
Move a sonda de acordo com os comandos passados.

### Parametros
Nome           | Tipo        | Descrição         | Exemplo
-------------- | ----------- | ----------------- | --------------------------------
 **id**        | **Integer** | requerida na URI  | 
 **movements** | **Array**   | requerido no body | "movements": ["GE", "M", "M", "M", "GD", "M"]

Exemplo de resposta:
```json
{
  "x": 1,
  "y": 3
}
```

### PUT /sonda/{id}/reposicionar
Retorna a sonda para sua posição padrão (0,0).

### Parametros

Nome    | Tipo        | Descrição        | Exemplo
------- | ----------- | ---------------- | -------------------------
 **id** | **Integer** | requerida na URI | 

Exemplo de resposta:
```json
{
  "Sucesso": "Sonda enviada para nas cordenadas iniciais (0,0)."
}
```

## Desenvolvido com
* PHP 7.2
* MySQL 5.7 

---

<p align="center">
    Desenvolvido com :heart: por <b>Ana Luiza</b>
</p>