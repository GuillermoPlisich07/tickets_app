# TicketsAPP

## Decisiones de Arq
Opte por una Arquitectura en capas o Layers Architecture, en lugar de un MVC o Hexagonal, pero por practicidad. La Hexagonal creo que era complicarme mucho para esta prueba y MVC era muy simple.


Y una aclaracion sobre el estado cerrado, una vez que un ticket pasa a closed, no es posible cambiar su estado, editar su título ni agregar o modificar mensajes. 
Esta restricción es va un poco mas de lo que la documentacion exige, pero lo considere coherente con lo de "cierre definitivo".

A lo ultimo decidi agregar Swagger para facilitar la doc de los endpoints.

## Requisitos
- Docker y docker compose

## Instalacion y ejecucion

```
1. Clonar el repositorio y moverte a la carpeta
git clone https://github.com/GuillermoPlisich07/tickets_app.git
cd tickets_app

2. Construir el archivo env
cp .env.example .env

3. Levantar los contenedores
docker compose up -d

4. Instalar las dependencias de composer
docker exec tickets_app composer install

5. Generar la key de la app
docker exec tickets_app php artisan key:generate

6. Levantar las migraciones
docker exec tickets_app php artisan migrate

7. Generar la documentación Swagger
docker exec tickets_app php artisan l5-swagger:generate
```
Todo esto queda expuesto en `http://localhost:8000/api`


## Endpoints
### Tickets
|Metodo  | Ruta|
|--|--|
| GET |/api/tickets  |
| POST |/api/tickets  |
| GET |/api/tickets/{ID}  |
| PUT |/api/tickets/{ID}  |
| PATCH |/api/tickets/{ID}/status |
| DELETE |/api/tickets/{ID} |

### Mensajes
|Metodo  | Ruta|
|--|--|
| POST |/api/tickets/{ID}/messages |
| PUT |/api/tickets/{ID}/messages/{ID}  |
| DELETE |/api/tickets/{ID}/messages/{ID}  |

###  Valores para Author y Status

-  `author_type`: `customer` | `operator`

-  `status`: `open` | `operator_reply` | `customer_reply` | `closed`




## La documentación de los endpoints esta en `http://localhost:8000/api/documentation`
## El archivo `App-ticket.postman_collection.json` sirve para importar la pruebas a postman!

