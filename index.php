<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.css" />
		<script src="http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.js"></script>
		<style>
			html, body {
				width: 100%;
				height: 100%;
			}
			.pointToolTip {
				border: 1px black solid;
				background: white;
				border-radius: 5px;
			}
			.pointToolTip * {
				box-shadow: none;
			}
			.pointToolTip .leaflet-popup-tip-container {
				display: none;
			}
		</style>
		<script>
			var lastPolyLine;
			var arRoadPropsNames;
			var map;
			var popupPoint;

			function initMap() {
				arRoadPropsNames = {
					adm_reg : 'Регион',
					tmp : 'Конец дороги',
					length_km : 'Длина дороги',
					name : 'Наименование',
					road_id : 'идентификатор дороги',
					fmp : 'Начало дороги',
					asp_cov : 'Длина асфальтобетонного покрытия'
				}
				map = L.map('roadsMap', {
					fadeAnimation : false,
					zoomAnimation : false,
					//zoomAnimationThreshold: 0,
					markerZoomAnimation : false
				}).setView([45.02, 38.59], 7);
				popupPoint = L.popup({
					offset : L.point(0, -20),
					zoomAnimation : false,
					closeButton : false,
					className : 'pointToolTip',
					autoPan : false
				});

				L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
					attribution : '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
				}).addTo(map);
			}

			function loadRoads() {
				var req = getXmlHttp();
				//var statusElem = document.getElementById('loadStatus');
				req.onreadystatechange = function() {
					if (req.readyState == 4) {
						//statusElem.innerHTML = req.statusText// показать статус (Not Found, ОК..)
						if (req.status == 200) {
							var oRoads = eval('(' + req.responseText + ')');
							L.geoJson(oRoads, {
								style : function(feature) {

									//id
									//type
									//properties
									//geometry
									//statusElem.innerHTML = JSON.stringify(feature)+"<hr />";

									if (/^03(.*)$/ig.test(feature.properties.road_id)) {
										return {
											color : '#03f',
											weight : 2,
											opacity : 0.5
										};
									} else if (/^[АМ](.*)$/ig.test(feature.properties.road_id)) {
										return {
											color : '#f30',
											weight : 3,
											opacity : 1
										};
									}
									return {
										color : '#000',
										weight : 2,
										stroke : true,
										opacity : 1
									};
								},
								onEachFeature : function(feature, layer) {
									var elTable = document.createElement('table');
									var arProps = feature.properties;
									for (var i in arProps) {
										var elTr = document.createElement('tr');
										elTable.appendChild(elTr);

										var elTd = document.createElement('td');
										elTd.style.borderBottom = "1px dashed #777777";
										elTr.appendChild(elTd);
										elTd.innerHTML = arRoadPropsNames[i];

										var elTd = document.createElement('td');
										elTd.style.borderBottom = "1px dashed #777777";
										elTr.appendChild(elTd);
										elTd.innerHTML = arProps[i];
									}

									var arPoints = new Array();
									var arLongLats = new Array();

									if (feature.geometry.type == 'MultiLineString') {
										for (var i in feature.geometry.coordinates) {
											var arPoints = feature.geometry.coordinates[i];
											for (var j in arPoints) {
												var arPoint = arPoints[j];
												arPoints[arPoints.length] = L.point(arPoint[0], arPoint[1]);
												arLongLats[arLongLats.length] = L.latLng(arPoint[1], arPoint[0]);
											}
										}

										// Ссылка для просмотра дороги
										var elTr = document.createElement('tr');
										elTable.appendChild(elTr);

										var elTd = document.createElement('td');
										//elTd.style.borderBottom = "1px dashed #777777";
										elTd.style.textAlign = "center";
										elTd.style.color = "blue";
										elTd.style.fontWeight = "bold";
										elTd.style.cursor = "pointer";

										elTr.appendChild(elTd);
										elTd.innerHTML = "Просмотр";
										elTd.setAttribute('colspan', 2);

										elTd.onclick = function() {
											if (lastPolyLine != null) {
												map.removeLayer(lastPolyLine);
											}

											lastPolyLine = L.polyline(arLongLats, {
												color : '#0f0',
												weight : 6,
												opacity : 0.5
											}).addTo(map);
											map.fitBounds(lastPolyLine.getBounds());
										}
									}

									layer.bindPopup(elTable).on('mouseover', function(ev) {
										popupPoint.openOn(map);
									}).on('mouseout', function(ev) {
										//popupPoint._close();
									}).on('mousemove', function(ev) {

										// layer
										// target
										// latlng
										// layerPoint
										// containerPoint
										// originalEvent
										// type

										var lat = ev.latlng.lat + 1;
										var lng = ev.latlng.lng + 1;

										var titleText = lat + " >> " + lng;
										popupPoint.setLatLng(ev.latlng).setContent(titleText);
										popupPoint.update();
									});
								}
							}).addTo(map);
						}
					}
				}
				req.open('GET', '/roads.json', true);
				req.send(null);
				//statusElem.innerHTML = 'Ожидаю ответа сервера...'
			}

			function getXmlHttp() {
				var xmlhttp;
				try {
					xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
				} catch (e) {
					try {
						xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
					} catch (E) {
						xmlhttp = false;
					}
				}
				if (!xmlhttp && typeof XMLHttpRequest != 'undefined') {
					xmlhttp = new XMLHttpRequest();
				}
				return xmlhttp;
			}

		</script>
	</head>
	<body style="margin: 0; padding: 0;" >
		<div id="roadsMap" style="width: 100%; height: 100%;" ></div>
		<!--
			<div id="loadStatus" ></div>
		-->
	</body>
	<script>
		initMap();
		loadRoads();
	</script>
</html>