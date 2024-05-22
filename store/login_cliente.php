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

$tituloseo = "Login";
$descripcionSeo = "Login de clientes";

$header->headerSet($tituloseo, $descripcionSeo);
?>


<section class="max-w-screen-sm m-auto my-10 ">
    
    <div class="p-3">
        <h1 class="font-bold text-xl text-center mb-5">Login</h1>
        <div class="">
            <div id="DivClientes"></div>
        </div>
    </div>
    
</section>
<style>
    .logincontent {
        max-width: 350px;
        margin: auto;
    }
    .logueado {
        display: grid;
        grid-template-columns: repeat(1, 1fr);
        align-items: center;
        justify-content: center;
        gap: 1rem;
    }

    .logueado #cuponescar {
        grid-column: span 2 / span 2;
    }

    .logueado h3 {
        text-align: center !important;
    }

    @media screen and (max-width: 768px) {
        .logueado {
            grid-template-columns: 1fr;
        }

        .logueado h3 {
            text-align: center !important;
        }
    }

    h3 {
        text-align: center !important;
    }

    .login_cliente {
        border: 1px solid #e5e7eb;

    }

    .login_cliente label {
        max-width: 100% !important;
        grid-column: span 7 / span 7 !important;
    }

    .login_cliente button {
        grid-column: span 7 / span 7 !important;
        margin: 0 !important;
    }

    .login_cliente input {
        grid-column: span 7 / span 7 !important;
        max-width: 100% !important;
    }
</style>

<?php
require('footer.php');
?>

<script type="text/javascript">
    function mostrarCliente() {


        var parametros = {
            "operacion": 1
        };

        $.ajax({
            data: parametros,
            url: 'procesar_logincliente.php',
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
                //eliminar tituto h1
                $("h1").remove();

            }
        });


    }

    function buscarCliente() {

        var numDocCliente = document.getElementById("numdocbuscar").value;
        //alert(numDocCliente);

        var parametros = {
            "operacion": 2,
            "numdocclientebuscar": numDocCliente
        };

        $.ajax({
            data: parametros,
            url: 'procesar_logincliente.php',
            type: 'post',
            beforeSend: function() {
                //mostrar_mensaje("Procesando, espere por favor...");
                //alert('3');
            },
            success: function(response) {
                //alert(response);
                //mostrar_mensaje(response);


                if (response == "") {
                    
                    mostrarCliente();
                    
                } else {
                    $("#DivClientesMsjError").html(response);
                }

                //alert(response);
            }
        });



    }

    function eliminarCliente() {

        //alert('holaaaa');


        var parametros = {
            "operacion": 3
        };

        $.ajax({
            data: parametros,
            url: 'procesar_logincliente.php',
            type: 'post',
            beforeSend: function() {
                //mostrar_mensaje("Procesando, espere por favor...");
                //alert('3');
            },
            success: function(response) {
                //alert(response);
                //mostrar_mensaje(response);


                if (response == "") {


                    mostrarCliente();
                    mostrarCupon();
                    


                } else {
                    alert(response);
                }

                //alert(response);
            }
        });

    }


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


</script>




<script type="text/javascript" charset="utf-8">
    //Fancy Lightbox
    $(document).ready(function() {
        $(".fancybox").fancybox();
    });

    $(document).ready(function() {


        mostrarCliente();

        mostrarCupon();


    });
</script>





</body>

</html>