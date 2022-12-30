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

   - es muy importante tener el key que se uso para hacer el encode, y el tipo de hash que se uso, si no no funciona, esto nos devolvera un objeto el cual hay que hacer entendible para PHP

     ```php
     $key = "Key_de_seguridad_para_usar_en_mi_App_con_caracteresYNumeros";

        // esto nos devuelve un objeto
        $decoded = JWT::decode($tokenEncode, new Key($key, 'HS512'));

        //  con esto lo convertimos a un array entendible para php
        $decoded_array = (array) $decoded;
     ```

5. ## pasando cookies con parametro httpOnly

   - Desde el backend en los headers donde esta el cors tenemos que permitirles el attributo credetials, para que lo reciba `header("Access-Control-Allow-Credentials: true");` NOTA: el se habilita el `Access-Control-Allow-Origin` ya no puede ser `*`, tiene que ser a juro el dominio donde se usara ej. en cors
   - Desde el front hay que agregar el parametro de `credentials: 'include'` en los fetch, NOTA: fuera de los headers ej.
     ```jsx
     const getApi = await fetch(urlApi, {
       method: method,
       headers: HEADERS,
       credentials: "include",
       body: JSON.stringify(data),
     })
     ```
   - y desde el back se vuelven a recibir con `$_COOKIE` en un array con todas cookies

   - con esto lo que tenemos es que verificarlo con el token que esta en la base de datos y si considen pasar a hacer la consulta de correspondiente en la DB
