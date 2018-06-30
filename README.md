2014 год

Тестовое задание 
Даны данные в GeoJSON (файл roads.jsn) в составе:
Линейные объекты, геометрия которых представлена последовательностью точек. Система координат проекционная для Web, выбрана в Leaflet по умолчанию
Начало дороги (поле fmp)
Конец дороги (поле tmp)
Длина дороги (поле length_km)
Наименование и идентификатор дороги (поля name и road_id)
Длина асфальтобетонного покрытия (поле asp_cov)

Требуется:
Создать на основе приведенного GeoJSON таблицу tbl_roads в БД
C помощью библиотеки Leaflet выбрать и визуализировать данные из tbl_roads:
Региональные дороги (если road_id начинается с 03)
Федеральные дороги жирной яркой линией(если road_id начинается с А или М)
Записи с иным значением road_id отбросить
В качестве картографической подложки использовать любой публичный сервис (google, bing, etc.)
Для каждого объекта задать обработку событий:
При наведении мыши — подсветку, вывод во всплывающем тултипе географической координаты позиции курсора
При щелчке мыши — выпадающую таблицу с атрибутивной информацией, масштабирование карты к выбранному объекту
