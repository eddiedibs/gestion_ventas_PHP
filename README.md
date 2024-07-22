
# Documentación del Proyecto "gestion_ventas_PHP"


## Índice
1. Introducción
2. Instalación y Configuración
2.1. Requisitos Previos
2.2. Instalación
2.3. Ejecución
3. Estructura del Proyecto
3.1. Algunos Archivos Principales
4. Uso de la Plataforma
4.1. Secciones Principales
4.2. Funcionalidades
4.3. Consideraciones
6. Capturas

## 1. Introducción
Buen día, a continuación se llevará a cabo indicaciones y comentarios relacionados a la prueba del sistema de gestión de ventas desarrollado en PHP, Jquery para ciertos comportamientos y procesos, y Bootstrap para estilos.  La plataforma permite a los usuarios gestionar productos, clientes, ventas y vendedores. Realizar compras a través del formulario, y observar estadísticas de ventas. Utiliza MySQL como sistema de gestión de bases de datos.

## 2. Instalación y Configuración
### 2.1. Requisitos Previos
Antes de comenzar con la instalación, asegúrese de tener los siguientes requisitos previos instalados en su sistema:
- PHP
- Servidor web (Apache recomendado)
- MySQL
- Navegador web

### 2.2. Instalación
1. Clone el repositorio desde GitHub:
   ```
   git clone https://github.com/eddiedibs/gestion_ventas_PHP.git
   ```
2. Navegue al directorio del proyecto:
   ```
   cd gestion_ventas_PHP
   ```
3. Copie el contenido a su servidor web (por ejemplo, /var/www/html para Apache)

### 2.3. Ejecución
1. Inicie el servidor MySQL y cree una base de datos nueva:
   ```
   CREATE DATABASE gestion_ventas;
   ```
2. Importe el archivo database.sql incluido en el repositorio para poblar la base de datos:
   ```
   mysql -u [usuario] -p gestion_ventas < database.sql
   ```
3. Configure el archivo de conexión a la base de datos en `conexion.php` con sus credenciales de MySQL.

![Screenshot from 2024-07-22 06-26-52](https://github.com/user-attachments/assets/44066340-07e4-4c25-9fe6-fe50e90b4e5b)

5. Acceda a la aplicación a través de su navegador web.

## 3. Estructura del Proyecto
El proyecto está organizado en los siguientes archivos principales:


![Screenshot from 2024-07-22 06-30-10](https://github.com/user-attachments/assets/916fc832-89b5-4af1-b744-15b4f4de6f95)



### 3.1. Algunos Archivos Principales
- `index.php`: Contiene el código HTML del diseño y funciones con JQuery.
- `conexion.php`: Contiene la lógica de conexión con la base de datos mySQL.
- `database.sql`: Contiene las tablas e inyecciones de datos para la base de datos.
- `handler_producto.php`: Se encarga de agregar o eliminar items del carrito, y realizar las validaciones respectivas.
- `guardar_cliente.php`: Se encarga de guardar la información ingresada del cliente en la base de datos.
- `cargar_productos.php`: Su función es la de cargar los productos en el formulario.
- `error_handler.php`: Se encarga de validar errores comunes que puede presentar el usuario.
- `validator_handler.php`: Se encarga de validar ciertos inputs que puede presentar el usuario, como por ejemplo: Validar la Cédula/RIF que ingrese el usuario.
- `obtener_estadisticas.php`: Su función es la de mostrar las estadísticas de los vendedores, tales como el Total de Ventas, Número de ventas y Productos Más Vendidos.

## 4. Uso de la Plataforma
### 4.1. Secciones Principales
La plataforma contiene varias secciones principales, incluyendo:
- Registro del usuario
- Registro de items
- Seccion con subtotal, IVA, y total
- Modal de estadísticas

### 4.2. Funcionalidades
Las principales funcionalidades de la aplicación incluyen:
- Gestión, registro y compra de productos por categoría.
- Visualización de datos del cliente.
- Visualización de datos del vendedor.
  
### 4.3. Consideraciones
- El formato de Cédula/RIF debe ser caracter identificador + serie de numeros,
por ejemplo: V14154895
- El formato de Teléfono debe ser una serie de números sin símbolos especiales,
por ejemplo: 04121465976
- Como característica, está el botón Seleccionar Cliente, éste es capaz de rellenar
los demás campos si sólo se llena la cédula/RIF y si existe el registro.

## 6. Capturas

![Screenshot from 2024-07-22 06-52-17](https://github.com/user-attachments/assets/a5513bb5-78c4-4e62-8855-7720d3294c37)
![Screenshot from 2024-07-22 06-52-43](https://github.com/user-attachments/assets/efc5ce4d-28e4-466a-8c4a-a552d6ab2431)
![Screenshot from 2024-07-22 06-53-11](https://github.com/user-attachments/assets/36b99df5-6f63-45ef-bb38-962ec57a0f5b)


