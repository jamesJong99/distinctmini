

function validar(valor , tipo) {
		  var revision = false;

		  //alert(tipo);
		  
		  //texto obligatorio
		  if( tipo==1 ) {
			  revision = validarobligatorio(valor);
		  }
		  
		  //email obligatorio
		  if( tipo==2 ) {
			  revision = validarobligatorio(valor);
			  if( revision ) {
			  revision = validarEmail(valor);
			  }
		  }
		  
		  //celular obligatorio
		  if( tipo==3 ) {
			  revision = validarobligatorio(valor);
			  if( revision ) {
			  revision = validarnumero(valor);
				  
				  if( revision ) {
				  revision = validarRango(valor,900000000,999999999);
				  
				  }
			  
			  }
			  
		  }
		  
		  //password a guardar
		  if( tipo==4 ) {
			  //alert(valor);
			  revision = validarobligatorio(valor);
			  if( revision ) {
				 revision = validarLongitud(valor,5,16); 
					if( revision ) {
					 //revision = /^(?=\w*\d)(?=\w*[A-Z])(?=\w*[a-z])\S{5,16}/.test(valor);
					 revision = /^[A-Za-z0-9]{1,16}$/.test(valor);
					 
					}
			  //return /^(?=\w*\d)(?=\w*[A-Z])(?=\w*[a-z])\S{5,16}/.test(valor);
			  }
			  
		  }
		  
		  //password login
		  if( tipo==5 ) {
			  //alert(valor);
			  revision = validarobligatorio(valor);
			  if( revision ) {
				 revision = validarLongitud(valor,5,16); 
					if( revision ) {
					 //revision = /^(?=\w*\d[A-Z][a-z])\S{5,16}/.test(valor);
					 revision = /^[A-Za-z0-9]{1,16}$/.test(valor);
					}
			  //return /^(?=\w*\d)(?=\w*[A-Z])(?=\w*[a-z])\S{5,16}/.test(valor);
			  }
			  
		  }

		  //password login parte 2
		  if( tipo==6 ) {
			//alert('1');
			revision = validarobligatorio(valor);
			//alert('2');
			if( revision ) {
				//alert('3');
			   revision = validarLongitud(valor,5,16); 
				  
			//return /^(?=\w*\d)(?=\w*[A-Z])(?=\w*[a-z])\S{5,16}/.test(valor);
			}
			
		}
		  
		  
		  return revision;
		}
		
		
		
		
		function validarnumero(valor) {
		  //console.log("Paso 3");
		  if( isNaN(valor) ) {
			  return false;
		  }
		  return true;
		  
		}
		
		function validarEmail(mail) {
		  return /^\w+([\.\+\-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,4})+$/.test(mail);
		}
		
		
		function validarobligatorio(valor) {
		  //console.log("Paso 4");
		  if( valor == null || valor.length == 0 || /^\s+$/.test(valor) ) {
			  return false;
		  }
		  return true;
		  
		}
		
		
		
		function validarRango(valor,minimo,maximo) {
		  //console.log("Paso 5");
		  if( minimo > valor || valor > maximo  ) {
		      //console.log("Paso 6");
			  return false;
		  }
		  return true;
		  
		}
		
		function validarLongitud(palabra,minimo,maximo) {
		  //console.log("Paso 5");
		  val = palabra.length;


		  if( minimo > val || val > maximo  ) {
		      //console.log("Paso 6");
			  return false;
		  }
		  return true;
		  
		}
		
	