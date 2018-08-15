# TAPIR (тестовое задание с фильтрацией б/у авто)

## Первоначальная настройка
- в файле "**application/.settings.php**" настроить параметры подключения к БД
- выполнить через cli команду "**php {путь_до_файла/console/init.php} cars:create-table**" для создания таблицы
- выполнить через cli команду "**php {путь_до_файла/console/init.php} cars:import**" для импорта данных в таблицу
- *при желании можно поднять вагрант*

## Получение отфильтрованных данных через http(s)
- через get-параметры передаём необходимые данные согласно ТЗ (`filter` в URL необязателен)



##### Примечания
*Пришлось отойти от некоторых требований ТЗ (из-за своих личных убеждений, понимания ТЗ и видения конечного результата :) )*
- поля `price`, `km`, `engine_capacity` определены как *float*;
- поля `owners`, `power` определены как *int*;
- значения для полей с диапазонами можно передавать в любом порядке (возрастания или убывания)
- добавил суррогатный ключ `id` как первичный ключ
- добавил уникальный ключ `vin` и определил его как *char(17)*
- поле `data` в отфильтрованных данных возвращается как *массив*, а не *объект*
- на всё ушло примерно 5 часов (довольно большая часть времени ушла на изучение *symfony/console* (т.к. с симфони не работал вообще) и на продумывание архитектуры)
