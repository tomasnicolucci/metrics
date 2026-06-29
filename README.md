# Metrics

Plugin de analítica para WordPress que permite monitorear el comportamiento de los visitantes mediante un dashboard integrado, sin depender de servicios externos.

## Objetivo

Metrics fue desarrollado para ofrecer una alternativa simple y totalmente integrada a WordPress, permitiendo conocer el comportamiento de los visitantes sin necesidad de utilizar plataformas externas de analítica.

El enfoque está puesto en brindar información clara, relevante y fácil de interpretar para administradores y clientes finales mediante una interfaz limpia y unificada.

## Estado del proyecto

El plugin se encuentra en desarrollo activo y continúa incorporando nuevas funcionalidades y mejoras de rendimiento.

## Funcionalidades

### Dashboard interactivo

* Panel de métricas integrado en el área de administración de WordPress.
* Indicadores en tiempo real y métricas históricas.
* Filtros por período:

  * Hoy
  * Últimos 7 días
  * Últimos 30 días
  * Mes actual
  * Último año

### Métricas principales

* Visitas totales.
* Usuarios únicos.
* Visitantes activos.
* Tiempo total de permanencia en el sitio.
* Tiempo promedio por página.
* Aperturas de documentos PDF.
* Recursos con mayor interacción.

### Estadísticas de navegación

* Páginas más visitadas.
* Recursos más interactuados.
* Países de origen de los visitantes.
* Fuentes de tráfico.
* Tipo de dispositivo utilizado.

### Visualización de datos

* Gráficos dinámicos adaptados al período seleccionado.
* Visualización por hora, día o mes según el rango elegido.
* Gráficos de líneas, barras y torta para facilitar la interpretación de la información.

### Exportación

* Exportación de reportes en formato CSV.
* El reporte incluye las principales métricas y rankings del período seleccionado.

### Personalización

* Etiquetas configurables para adaptar el dashboard a diferentes proyectos.
* Compatible con múltiples sitios WordPress reutilizando la misma base del plugin.


## Sobre la implementación

Para que el registro de las métricas de localizaciones es necesario crear la carpeta [geo] y colocar dentro el archivo [GeoLite2-City.mmdb] que se puede obtener de la página de maxmind.com

### Autenticación
El plugin no implementa autenticación. Requiere que el sitio gestione el acceso mediante el sistema de usuarios de WordPress.