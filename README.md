# Streamberry-API

Esta é a documentação para a API Streamberry, que trata-se de um desafio fornecido para a vaga de desenvolvedor PHP com Laravel.   

### 1. Consultar Disponibilidade de Streaming de um Filme

**Rota:** `/movies-streamings`  
**Método:** `GET`

Para verificar em quantos streamings um filme está disponível, utilize este endpoint e passe o `movie_id` no formulário para pesquisar.

### 2. Média de Avaliação de Cada Filme

**Rota:** `/avarage-rating/{id}`  
**Método:** `GET`

Informe o `ID` do filme desejado para obter a média de avaliação correspondente.

### 3. Filmes Lançados por Ano

**Rota:** `/movies-year`  
**Método:** `GET`

Se quiser saber quantos filmes e quais foram lançados em cada ano, utilize este endpoint. Pode-se passar o parâmetro `year_release` para pesquisar por um ano específico.

### 4. Localizar Filmes por Avaliação e Comentários

**Rota:** `/movie`  
**Método:** `GET`

Utilize este endpoint para encontrar filmes com base em diferentes critérios como nome, gênero, sinopse, mês/ano de lançamento, avaliação e comentário. Preencha o formulário conforme necessário para obter os resultados desejados.

```json
{
    "ativo": 1,
    "name": "",
    "genre_movie_id": "",
    "synopsis": "",
    "month_release": "",
    "year_release": "",
    "assessment": "",
    "comment": ""
}
````


### 5. Média de Avaliações de Filmes por Gênero e Ano de Lançamento

**Rota:** `/avarage-rating-gender`  
**Método:** `GET`

Informe o `year_release` desejado e os filmes ativos para descobrir a avaliação média agrupada por gênero.

```json
{
    "ativo": 1,
    "year_release": ""
}

