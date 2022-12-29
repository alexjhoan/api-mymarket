# Doc para JWT en php

1. ## Instalacion

   - Instalamos composer(manejador de paquetes de php), https://getcomposer.org/
   - en la raiz del proyecto abrimos la consola y corremos `componser require firebase/php-jwt` para instalar
   - se crea una carpeta vendor/componser el cual se usara para correr JWT

2. ## Crear funcion con parametro deonde se le pasara un array

   - Creamos una funcion a la cual le pasamos unos parametros y con esta generamos el token. Puede ser dentro de utils y le pasamos un array de datos a subir en la variable token

   - dentro del array de token metemos 2 valore adicionales el `iat` que es el tiempo en que se creo y el `exp` que es el tiempo de expiracion, nos ayudamos en php con la funcion de time que nos devuelve en tiempo en unix o en segundos desde 1970, NOTA: para el `exp` solo usamos el `time()` que es en segundos y le sumamos 60 para segundos y del resto multiplicamos 60 para minutos 24 para un dia y asi sucesivamente ej. `time() + (60 * 60 * 24)` para un dia y `time() + (60 * 60 * 24 * 30)` para un mes

3. ## Encode

   - importamos donde se va a usar desde vendor/autoload.php NOTA: el autoload carga todas las librerias que tenga

   - instanciamos la clase con `use Firebase\JWT\JWT`

   - llamamos la clase `JWT::encode()` dentro de ua variable y le pasamos 3 parametros el primero es da data a encrpitar o el token, el 2do es un key, un key unico para usar en el app, y el 3ero es el tipo de encriptacion
     https://github.com/firebase/php-jwt

4. ## Decode
