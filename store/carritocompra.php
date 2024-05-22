<?php
session_start();
require_once("ClassesStore/BEProducto.php");
require_once("ClassesStore/BEPedidoItem.php");
require_once("ClassesStore/BECliente.php");
require_once("ClassesStore/DACliente.php");
require_once("ClassesStore/BECuponImagen.php");
require_once("ClassesStore/DACuponImagen.php");
require_once("header.php");
$header = new header();


$title = "Distinct - Carrito de compras";
$descripcion = "Carrito de compras de productos de calidad en Distinct - Tienda Online";

$header->headerSet($title, $descripcion);
?>

	<section class="max-w-screen-xl m-auto my-10">
		<div class="p-3">
			<?php $linkRegresar = "index.php"; ?>
			<button class="w-auto m-auto mb-3 block text-center py-2 px-3 rounded-md bg-[#EDDFDB] text-[#97847d] border-[#EDDFDB] hover:bg-[#C4B8B5] hover:text-white  hover:border-[#c4b8b5] transition-colors" onclick="<?php echo "window.location.href='" . $linkRegresar . "'" ?>">
				Seguir comprando
			</button>

			<h1 class="font-bold text-xl">Carrito de compras</h1>
			<div class="flex gap-3 sm:flex-row flex-col">
				<div class="sm:w-7/12 w-full">
					<div id="DivProductos"></div>
				</div>
				<div class="sm:w-5/12 w-full gap-5 flex flex-col">
					<div id="DivClientes"></div>
					<div id="DivCupon"></div>
					<div id="DivEntrega"></div>
					<div id="DivMonto"></div>
				</div>

			</div>


		</div>
	</section>

	<style>
                                                    .whatsapp-icon {
                                                        position: fixed;
                                                        bottom: 20px;
                                                        right: 20px;
                                                        width: 60px;
                                                        height: 60px;
                                                        background-color: #25D366;
                                                        border-radius: 50%;
                                                        text-align: center;
                                                        line-height: 60px;
                                                        color: #fff;
                                                        font-size: 30px;
                                                        z-index: 1000; /* Asegura que el ícono esté sobre otros elementos */
                                                        cursor: pointer;
                                                    }
                                                </style>

<a href="https://wa.link/tc9xfn" target="_blank" ><img class="whatsapp-icon" src="<?php echo BASE_URL_STORE ?>imagenes/wsp.png" alt="LogoWsp"  ></a>

	<?php
	require('footer.php');
	?>


	<script type="text/javascript" charset="utf-8">
		
		//view myproduction
		function mostrarProducto() {
			//alert('inicio mostrarProducto');

			var parametros = {
				"operacion": 4
			};

			$.ajax({
				data: parametros,
				url: 'carritocompra_ope.php',
				type: 'post',
				beforeSend: function() {
					//mostrar_mensaje("Procesando, espere por favor...");
					//alert('3');
				},
				success: function(response) {
					//alert(response);
					//mostrar_mensaje(response);
					$("#DivProductos").html(response);
					//alert('termino actualizar DivProductos')
				}
			});



		}



		function cambioCantidadProd(id) {
			var nameID = 'nuevaCant' + id;
			var cantidad = document.getElementById(nameID).value;
			//alert(cantidad);
			var nCantidad = parseInt(cantidad);
			//alert(nCantidad);

			var idCampoStock = 'stockMaximo' + id;
			var stock = document.getElementById(idCampoStock).value;
			var nStock = parseInt(stock);
			//alert(stock);

			var idCampoPrecio = 'preProd' + id;
			var precioPro = document.getElementById(idCampoPrecio).value;
			var precioPro = parseFloat(precioPro);


			var idCampoAhorro = 'ahoProd' + id;
			var ahorroPro = document.getElementById(idCampoAhorro).value;
			var ahorroPro = parseFloat(ahorroPro);

			var divPrecio = '#preProdHtml' + id;
			var divAhorro = '#ahoProdHtml' + id;
			//alert(ahorroPro);

			var actualizarSession = 0;

			if (nCantidad <= 0) {
				alert('Hermos@ utilice la opción eliminar producto.');
				document.getElementById(nameID).value = 1;
				nCantidad = 1;
				actualizarSession = 1;
			}

			if (nCantidad > nStock) {
				alert('Hermos@ contamos con ' + stock + ' unidad(es).');
				document.getElementById(nameID).value = stock;
				nCantidad = stock;
				actualizarSession = 1;
			}

			if (nCantidad <= nStock) {
				actualizarSession = 1;
			}

			//alert(actualizarSession);

			if (actualizarSession == 1) {
				var nameIDCodProd = 'codeProde' + id;
				var IdCodProd = document.getElementById(nameIDCodProd).value;

				//alert('IdCodProd '+IdCodProd );

				var parametros = {
					"operacion": 5,
					"codeProde": IdCodProd,
					"nuevaCantidad": nCantidad
				};

				$.ajax({
					data: parametros,
					url: 'carritocompra_ope.php',
					type: 'post',
					beforeSend: function() {
						//mostrar_mensaje("Procesando, espere por favor...");
						//alert('3');
					},
					success: function(response) {
						//alert(response);
						//mostrar_mensaje(response);
						//$("#DivClientes").html(response);
						//alert(response);
						ReevaluarCuponExistente();
						mostrarCupon();
						actualizarMontos();
					}
				});

				//Actualizar el monto en el html
				var nuevoPrecio = precioPro * nCantidad;
				nuevoPrecio = nuevoPrecio.toFixed(2);
				var nomNuevoPrecio = "S/ " + nuevoPrecio; 
				//alert(nomNuevoPrecio);

				var nuevoAhorro = ahorroPro * nCantidad;
				nuevoAhorro = nuevoAhorro.toFixed(2);
				var nomNuevoAhorro = "S/ " + nuevoAhorro; 
				
				if(nuevoAhorro == 0)
				{
					nomNuevoAhorro="";
				}

				$(divPrecio).html(nomNuevoPrecio);
				$(divAhorro).html(nomNuevoAhorro);

			}
			//fin else


			//var cantidad = document.getElementById(id).value;
			//alert('Cantidad:'+cantidad);

		}

		//delete_product
		function eliminarProducto(numCorrel) {

			//alert(numCorrel);

			var parametros = {
				"operacion": 2,
				"numCorrelDelete": numCorrel
			};

			$.ajax({
				data: parametros,
				url: 'carritocompra_ope.php',
				type: 'post',
				beforeSend: function() {
					//mostrar_mensaje("Procesando, espere por favor...");
					//alert('3');
				},
				success: function(response) {
					//alert(response);
					//mostrar_mensaje(response);
					//$("#DivClientes").html(response);
					//alert(response);

					//alert('a');
					//borrarOpcionEntrega();
					//alert('antes mostrar producto');
					mostrarProducto();
					//alert('llamo')
					//alert('después mostrar producto');
					//mostrarCliente();
					//mostrarCupon();
					//mostrarUbigeo();

					ReevaluarCuponExistente();
					mostrarCupon();
					actualizarMontos();
					//alert('b');

				}
			});

			

		}
	</script>


	<script type="text/javascript">
		//view client
		function mostrarCliente() {


			var parametros = {
				"operacion": 1
			};

			$.ajax({
				data: parametros,
				url: 'procesar_cliente.php',
				type: 'post',
				beforeSend: function() {
					//mostrar_mensaje("Procesando, espere por favor...");
					//alert('3');
				},
				success: function(response) {
					//alert(response);
					//mostrar_mensaje(response);
					$("#DivClientes").html(response);
					//alert(response);
				}
			});


		}
		
		//search client
		function buscarCliente() {

			var numDocCliente = document.getElementById("numdocbuscar").value;
			//alert(numDocCliente);

			var parametros = {
				"operacion": 2,
				"numdocclientebuscar": numDocCliente
			};

			$.ajax({
				data: parametros,
				url: 'procesar_cliente.php',
				type: 'post',
				beforeSend: function() {
					//mostrar_mensaje("Procesando, espere por favor...");
					//alert('3');
				},
				success: function(response) {
					//alert(response);
					//mostrar_mensaje(response);


					if (response == "") {
						borrarOpcionEntrega();
						mostrarCliente();
						mostrarUbigeo();
						actualizarMontos();
					} else {
						$("#DivClientesMsjError").html(response);
					}

					//alert(response);
				}
			});



		}
		//delete client
		function eliminarCliente() {

			//alert('holaaaa');


			var parametros = {
				"operacion": 3
			};

			$.ajax({
				data: parametros,
				url: 'procesar_cliente.php',
				type: 'post',
				beforeSend: function() {
					//mostrar_mensaje("Procesando, espere por favor...");
					//alert('3');
				},
				success: function(response) {
					//alert(response);
					//mostrar_mensaje(response);


					if (response == "") {
						borrarOpcionEntrega();
						$("#DivEntrega").html('');

						mostrarCliente();
						mostrarCupon();
						mostrarUbigeo();
						actualizarMontos();


					} else {
						alert(response);
					}

					//alert(response);
				}
			});

		}

		//view cupon
		function mostrarCupon() {

			//alert('c1');

			var parametros = {
				"operacion": 1
			};

			$.ajax({
				data: parametros,
				url: 'procesar_cupon.php',
				type: 'post',
				beforeSend: function() {
					//mostrar_mensaje("Procesando, espere por favor...");
					//alert('3');
				},
				success: function(response) {
					//alert(response);
					//mostrar_mensaje(response);
					$("#DivCupon").html(response);
					//alert(response);
				}
			});


		}
		//apply cupon
		function AplicaCupon() {

			var codigocuponbuscar = document.getElementById("codcuponbuscar").value;

			var parametros = {
				"operacion": 2,
				"codigocuponbuscar": codigocuponbuscar
			};

			$.ajax({
				data: parametros,
				url: 'procesar_cupon.php',
				type: 'post',
				beforeSend: function() {
					//mostrar_mensaje("Procesando, espere por favor...");
					//alert('3');
				},
				success: function(response) {
					//alert(response);
					//mostrar_mensaje(response);


					if (response == "") {
						mostrarCupon();
						actualizarMontos();
					} else {
						//alert(response);
						$("#DivCuponMsjError").html(response);
					}

					//alert(response);
				}
			});



		}

		// delete cupon
		function EliminarCupon() {

			//alert('Eliminar cupón');

			var parametros = {
				"operacion": 3
			};

			$.ajax({
				data: parametros,
				url: 'procesar_cupon.php',
				type: 'post',
				beforeSend: function() {
					//mostrar_mensaje("Procesando, espere por favor...");
					//alert('3');
				},
				success: function(response) {
					//alert(response);
					//mostrar_mensaje(response);


					if (response == "") {
						//alert('Antes de actualizar cliente');
						//mostrarCliente();
						//alert('Antes de actualizar cupón');
						mostrarCupon();
						actualizarMontos();
					} else {
						//alert(response);
						$("#DivCuponMsjError").html(response);
					}

					//alert(response);
				}
			});



		}


		//Si no mantiene condiciones se quita el cupón
		// delete cupon in case not match condition.
		function ReevaluarCuponExistente() {

			//alert('Eliminar cupón');

			var parametros = {
				"operacion": 4
			};

			$.ajax({
				data: parametros,
				url: 'procesar_cupon.php',
				type: 'post',
				beforeSend: function() {
					//mostrar_mensaje("Procesando, espere por favor...");
					//alert('3');
				},
				success: function(response) {

					//alert(response);
					//mostrar_mensaje(response);


					if (response == "") {
						//alert('Antes de actualizar cliente');
						//mostrarCliente();
						//alert('Antes de actualizar cupón');
						//mostrarCupon();
						//actualizarMontos();
					} else {
						//alert(response);
						$("#DivCuponMsjError").html(response);
					}

					//alert(response);
				}
			});



		}


		//show Ubigeo
		function mostrarUbigeo() {

			//alert('c1');

			var CodeRpta = "0";
			var HtmlRpta = "";


			var parametros = {
				"operacion": 0
			};

			$.ajax({
				data: parametros,
				url: 'procesar_ubigeo.php',
				type: 'post',
				beforeSend: function() {
					//mostrar_mensaje("Procesando, espere por favor...");
					//alert('3');
				},
				success: function(response) {

					//alert(response);
					CodeRpta = response.substr(0, 1);
					HtmlRpta = response.substr(1, response.length);

					//sin productos en carrito
					if (CodeRpta == 0) {

					}

					//cliente no logeado debe elegir ubigeo
					if (CodeRpta == 1) {
						$("#DivEntrega").html(HtmlRpta);
						obtenerDpto();
					}

					//cliente logeado muestra 
					if (CodeRpta == 2) {
						$("#DivEntrega").html(HtmlRpta);
						//obtenerDpto();
					}
					//alert(CodeRpta);
					//alert(HtmlRpta);
					//mostrar_mensaje(response);
					//$("#DivCupon").html(response);
					//alert(response);
				}
			});


		}





		// Search new address
		function buscarNuevaDireccion() {



			var parametros = {
				"operacion": 4
			};

			$.ajax({
				data: parametros,
				url: 'procesar_ubigeo.php',
				type: 'post',
				beforeSend: function() {
					//mostrar_mensaje("Procesando, espere por favor...");
					//alert('3');
				},
				success: function(response) {
					//alert(response);
					borrarOpcionEntrega();
					$("#DivEntrega").html(response);
					obtenerDpto();

				}
			});

		}




		function obtenerDpto() {
			//alert('inicio obt dpto');
			var parametros = {
				"operacion": 1,
				"iddptopredet": 15
			};

			$.ajax({
				data: parametros,
				url: 'procesar_ubigeo.php',
				type: 'post',
				beforeSend: function() {
					//mostrar_mensaje("Procesando, espere por favor...");
					//alert('3');
				},
				success: function(response) {
					//alert(response);
					//mostrar_mensaje(response);
					$("#DivDpto").html(response);
					cambioDpto();
				}
			});
		}

		function cambioDpto() {
			var IdDpto = document.getElementById("listaDpto").value;
			$("#DivConsideracionesEntrega").html('');
			obtenerProv(IdDpto);
		}

		//Get verification
		function obtenerProv(IdDpto) {
			//alert(IdDpto);

			var nameidProvSelectDefaul = 'ProvIDDefault' + IdDpto;
			console.log(nameidProvSelectDefaul);
			var valueidProvSelectDefaul = document.getElementById(nameidProvSelectDefaul).value;
			console.log(valueidProvSelectDefaul);


			//alert('inicio obt dpto');
			var parametros = {
				"operacion": 2,
				"iddpto": IdDpto,
				"idprovpredet": valueidProvSelectDefaul
			};

			$.ajax({
				data: parametros,
				url: 'procesar_ubigeo.php',
				type: 'post',
				beforeSend: function() {
					//mostrar_mensaje("Procesando, espere por favor...");
					//alert('3');
				},
				success: function(response) {
					//alert(response);
					//mostrar_mensaje(response);
					$("#DivProv").html(response);
					cambioProv();
				}
			});
		}

		function cambioProv() {
			var IdProv = document.getElementById("listaProv").value;
			//alert(IdProv);
			$("#DivConsideracionesEntrega").html('');
			obtenerDist(IdProv);
		}

		//get transaction
		function obtenerDist(IdProv) {
			//alert(IdProv);

			var IdDpto = document.getElementById('listaDpto').value;
			//alert(IdDpto);

			var nameidDistSelectDefaul = 'DistIDDefault' + IdDpto;
			//alert(nameidDistSelectDefaul);
			var valueidDistSelectDefaul = document.getElementById(nameidDistSelectDefaul).value;
			//alert(valueidDistSelectDefaul);

			//alert('inicio obt dpto');
			var parametros = {
				"operacion": 3,
				"idprov": IdProv,
				"iddistpredet": valueidDistSelectDefaul
			};

			$.ajax({
				data: parametros,
				url: 'procesar_ubigeo.php',
				type: 'post',
				beforeSend: function() {
					//mostrar_mensaje("Procesando, espere por favor...");
					//alert('3');
				},
				success: function(response) {
					//alert(response);
					//mostrar_mensaje(response);
					//borrarOpcionEntrega();
					$("#DivDist").html(response);
					cambioDist();
				}
			});
		}

		//chage range
		function cambioDist() {
			//var IdDist = document.getElementById("listaDist").value;
			//alert(IdDist);
			//obtenerProv(IdDpto);
			borrarOpcionEntrega();
			$("#DivConsideracionesEntrega").html('');
			obtenerTipoEntrega();
		}

		// Get shipping type
		function obtenerTipoEntrega() {


			var IdDist = document.getElementById("listaDist").value;
			var IdProv = document.getElementById("listaProv").value;
			var IdDpto = document.getElementById("listaDpto").value;

			//alert(IdDpto);
			//alert(IdProv);
			//alert(IdDist);

			//alert('inicio obt dpto');
			var parametros = {
				"operacion": 1,
				"IdDist": IdDist,
				"IdProv": IdProv,
				"IdDpto": IdDpto
			};

			$.ajax({
				data: parametros,
				url: 'procesar_tipoentrega.php',
				type: 'post',
				beforeSend: function() {
					//mostrar_mensaje("Procesando, espere por favor...");
					//alert('3');
				},
				success: function(response) {
					//alert(response);
					//mostrar_mensaje(response);
					$("#DivTipoEntrega").html(response);
				}
			});


		}

		function eligioRecojoSurquilloClienteLogeado() {

			//actualizar en session la opción de envío.
			$("#DivConsideracionesEntrega").html('');

			var codeOpcionEntrega = 135;
			var IdDist = 1321;
			var IdProv = 1501;
			var IdDpto = 15;
			var DireccionElegida = '';
			var EligioDireccionAnterior = 1;

			var idCorrelativo = document.getElementById('listaDireccionesAnt').value;
			


			var parametros = {
				"operacion": 5,
				"codeOpcionEntregaUbic": codeOpcionEntrega,
				"IdDist": IdDist,
				"IdProv": IdProv,
				"IdDpto": IdDpto,
				"DireccionElegida": DireccionElegida,
				"EligioDireccionAnterior": EligioDireccionAnterior,
				"idCorrelativo": idCorrelativo
			};

			$.ajax({
				data: parametros,
				url: 'procesar_tipoentrega.php',
				type: 'post',
				beforeSend: function() {
					//mostrar_mensaje("Procesando, espere por favor...");
					//alert('3');
				},
				success: function(response) {
					//alert(response);
					//mostrar_mensaje(response);
					$("#DivConsideracionesEntrega").html(response);
				}
			});

			actualizarMontos();
			//alert('aca');
		}

		// Select shipping option
		function eligioOpcionEntrega() {

			//alert('function javascript');

			var ele = document.getElementsByName('opcEntr');
			var codeOpcionEntrega = 0;
			var idComentario = "consideraciones";
			var valueComentario = "";
			var cabezera = "<p class='font-bold mb-2'>Consideraciones de entrega</p>";


			for (i = 0; i < ele.length; i++) {
				if (ele[i].checked)
					codeOpcionEntrega = ele[i].value;
			}

			idComentario = idComentario + codeOpcionEntrega;
			valueComentario = document.getElementById(idComentario).value;
			valueComentario = cabezera + valueComentario;

			//actualizar en session la opción de envío.
			$("#DivConsideracionesEntrega").html(valueComentario);

			//Datos de envio
			//alert(codeOpcionEntrega);


			//alert('hola inicial');
			var IdDist = document.getElementById("listaDistEle").value;
			//alert('hola dist:'+IdDist);
			var IdProv = document.getElementById("listaProvEle").value;
			//alert('hola prov:'+IdProv);
			var IdDpto = document.getElementById("listaDptoEle").value;
			//alert('hola dpto:'+IdDpto);
			var DireccionElegida = document.getElementById("DireccionElegida").value;

			var EligioDireccionAnterior = document.getElementById("EligioDireccionAnterior").value;
			//alert(EligioDireccionAnterior);

			//alert('hola direccion:'+DireccionElegida);
			//alert(IdDpto);
			//alert(IdProv);
			//alert(IdDist);

			var parametros = {
				"operacion": 3,
				"codeOpcionEntregaUbic": codeOpcionEntrega,
				"IdDist": IdDist,
				"IdProv": IdProv,
				"IdDpto": IdDpto,
				"DireccionElegida": DireccionElegida,
				"EligioDireccionAnterior": EligioDireccionAnterior
			};

			$.ajax({
				data: parametros,
				url: 'procesar_tipoentrega.php',
				type: 'post',
				beforeSend: function() {
					//mostrar_mensaje("Procesando, espere por favor...");
					//alert('3');
				},
				success: function(response) {
					//alert(response);
					//mostrar_mensaje(response);
					//$("#DivTipoEntrega").html(response);
				}
			});

			actualizarMontos();
		}


		// Delete shipping option
		function borrarOpcionEntrega() {



			var parametros = {
				"operacion": 4
			};

			$.ajax({
				data: parametros,
				url: 'procesar_tipoentrega.php',
				type: 'post',
				beforeSend: function() {
					//mostrar_mensaje("Procesando, espere por favor...");
					//alert('3');
				},
				success: function(response) {
					//alert(response);
					//mostrar_mensaje(response);
					//$("#DivTipoEntrega").html(response);
				}
			});

			actualizarMontos();

		}

		// Send before option
		function quieroEnviarOpcionUsadaAntes() {

			borrarOpcionEntrega();
			mostrarUbigeo();

		}


		// Selete before address
		function elegirDireccionAnt() {

			//alert('function javascript');
			$("#DivTipoEntrega").html('');
			$("#DivConsideracionesEntrega").html('');

			var idCorrelativo = document.getElementById('listaDireccionesAnt').value;

			//alert(idCorrelativo);

			//En caso sea entrega con recojo en Surquillo se invoca una función especifica.
			if (idCorrelativo == "10") {
				eligioRecojoSurquilloClienteLogeado();
			} else {
				var idDpto = 'DptoAntEleg' + idCorrelativo;
				var idProv = 'ProvAntEleg' + idCorrelativo;
				var idDist = 'DistAntEleg' + idCorrelativo;

				//alert(idDist);

				var nameDpto = 'NameDptoAntEleg' + idCorrelativo;
				var nameProv = 'NameProvAntEleg' + idCorrelativo;
				var nameDist = 'NameDistAntEleg' + idCorrelativo;

				var idDireccionAntes = 'DireccionAntEleg' + idCorrelativo;

				//alert(nameDist);

				var idTipoEntrega = 'TipoEntAntEleg' + idCorrelativo;

				var valueDpto = document.getElementById(idDpto).value;
				var valueProv = document.getElementById(idProv).value;
				var valueDist = document.getElementById(idDist).value;

				//alert(valueDist);

				var valueNameDpto = document.getElementById(nameDpto).value;
				var valueNameProv = document.getElementById(nameProv).value;
				var valueNameDist = document.getElementById(nameDist).value;

				//alert(valueNameDist);

				var valueTipoEntrega = document.getElementById(idTipoEntrega).value;
				var valueDireccionAntes = document.getElementById(idDireccionAntes).value;

				//alert(idDireccionAntes);
				//alert(valueDireccionAntes);

				//alert(valueDpto);
				//alert(valueProv);
				//alert(valueDist);
				//alert(valueTipoEntrega);

				var parametros = {
					"operacion": 2,
					"IdDist": valueDist,
					"IdProv": valueProv,
					"IdDpto": valueDpto,
					"NameDist": valueNameDist,
					"NameProv": valueNameProv,
					"NameDpto": valueNameDpto,
					"IdTipoEntrega": valueTipoEntrega,
					"valueDireccionAntes": valueDireccionAntes
				};

				$.ajax({
					data: parametros,
					url: 'procesar_tipoentrega.php',
					type: 'post',
					beforeSend: function() {
						//mostrar_mensaje("Procesando, espere por favor...");
						//alert('3');
					},
					success: function(response) {
						//alert(response);
						//mostrar_mensaje(response);
						$("#DivTipoEntrega").html(response);

						//Confirmar si debo seleccionar un radio
						var RadioSeleccionar = document.getElementById('radioPorElegir').value;

						if (RadioSeleccionar != "-1") {

							RadioSeleccionar = 'opcEntr' + RadioSeleccionar;
							//alert(RadioSeleccionar);
							document.getElementById(RadioSeleccionar).checked = true;
							eligioOpcionEntrega();
						}



					}
				});
			}
			//fin else if(idCorrelativo=="10")



			//alert(idCorrelativo);



		}

		//Update money
		function actualizarMontos() {

			//alert('Pendiente actualizar montos');


			//alert('inicio obt dpto');
			var parametros = {
				"operacion": 6
			};

			$.ajax({
				data: parametros,
				url: 'carritocompra_ope.php',
				type: 'post',
				beforeSend: function() {
					//mostrar_mensaje("Procesando, espere por favor...");
					//alert('3');
				},
				success: function(response) {
					//alert(response);
					//mostrar_mensaje(response);
					$("#DivMonto").html(response);
				}
			});

		}

		/*
		tony_task_1
		Complete cart
		*/
		function finalizarCarrito() {

			//alert('Pendiente finalizar Carrito');

			$.redirect("checkout.php", {
					"operacion": 1
				},
				"POST");


		}

		// Select default shipping type
		function faltaElegirTipoEntrega() {

		// alert('Pendiente finalizar Carrito');
			
			alert('Para finalizar la compra elige la opción de entrega');

		}
	</script>




	<script type="text/javascript" charset="utf-8">
		//Fancy Lightbox
		$(document).ready(function() {
			$(".fancybox").fancybox();
		});

		$(document).ready(function() {

			

			borrarOpcionEntrega();

			mostrarProducto();

			mostrarCliente();

			mostrarCupon();
			
			mostrarUbigeo();

			//obtenerTipoEntrega();

			actualizarMontos();

		});
	</script>





</body>

</html>