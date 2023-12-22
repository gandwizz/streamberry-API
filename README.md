# streamberry-API

Rota principal para pegar os dados entre filmes e streamings:  

form completo:
{
    "ativo": true,
    "movie_id": "",
    "streaming_id": "",
    "assessments": "",
    "comment": "",
    "month_release": "",
    "year_release": ""
}

OBS: O ativo refere-se aos filmes que estão ativos no streaming. 


1. Em quantos Streamings um filme está disponível?

ROTA: /movies-streamings    MÉTODO:GET
Passar no form { movie_id } que deseja procurar


2. Qual a média de avaliação de cada filme?

Rota: /avarage-rating/{id}  MÉTODO:GET

Do qual informa-se o ID do filme que quer saber a avaliação


3. Quantos filmes e quais foram lançados em cada ano?

Rota: /movies-year  MÉTODO:GET
Pode-se informar o year_release caso queira saber, entretanto, se não passar no form será trazido todos os filmes


4. Localizar filmes conforme avaliação e seus respectivos comentários

ROTA: /movie  MÉTODO:GET

basta utilizar o form da seguinte maneira e os filmes serão informados

{
    "ativo": 1,
    "name": "",
    "genre_movie_id": "",
    "synopsis": "",
    "month_release" :"",
    "year_release" : "",
    "assessment": "",
    "comment": ""
}

5. Quais são as avaliações médias de filmes agrupados por gênero conforme a época de lançamento

Rota:  avarage-rating-gender   MÉTODO:GET

Basta informar o ano e os filmes ativos para descorbri a avaliação média

{
    "ativo": 1,
    "year_release" : ""
}


