<?php
/*
funcion para obtener la zona horaria, no me daba la hora de colombia ya que tenia Europe/Berlin
echo "Esto es la zona horaria" . date_default_timezone_get();
entonces tengo que asignarle una zona, en este caso America/Bogota
*/

date_default_timezone_set("America/Bogota");

//usuarios predeterminados

$clientes = [
    ["ID"=>1,"nombre"=>"user1","numCuenta"=>123,"saldo"=>1000,"password"=>"1234"],
    ["ID"=>2,"nombre"=>"user2","numCuenta"=>234,"saldo"=>2000,"password"=>"2345"],
    ["ID"=>3,"nombre"=>"user3","numCuenta"=>345,"saldo"=>3000,"password"=>"3456"],
    ["ID"=>4,"nombre"=>"user4","numCuenta"=>456,"saldo"=>4000,"password"=>"4567"],
    ["ID"=>5,"nombre"=>"user5","numCuenta"=>567,"saldo"=>5000,"password"=>"5678"]
];

//lista de registro de retiros
$retiros = [];

//lista de registro de transferencias
$transferencias = []; 

//funcionalidades
/*
1 - Iniciar sesion
2 - Consultar saldo
3 - Realizar retiro (Se debe registrar el retiro)
4 - Consultar informacion de los retiros
5 - Realizar transferencias (Se debe registrar las transferencias)
6 - Consultar informacion de la transferencia
*/

//----------------[Funciones]----------------
function verificarExistenciaCuenta($cuenta_ingresada,$listaClientes): int{
    $encontrado = 0;
    foreach($listaClientes as $cliente){
        if($cliente["numCuenta"]==$cuenta_ingresada){
            $encontrado++;
        }
    }
    return $encontrado;
}
//-------------------------------------------------------------------------------
function verificarPassword($numCuenta,$passwordIngresado,$listaClientes): bool {
    $coincide = FALSE;
    foreach($listaClientes as $cliente){
        if($cliente["numCuenta"]==$numCuenta){
            $passwordReal = $cliente["password"];
            if($passwordReal==$passwordIngresado){
                $coincide = TRUE;
            }
        }
    }

    return $coincide;
}
//-------------------------------------------------------------------------------
function obtenerNombrePorCuenta($numCuenta, $listaClientes): string{
    $nombre = "";
    foreach($listaClientes as $cliente){
        if($cliente["numCuenta"] == $numCuenta){
            $nombre = $cliente["nombre"];
        }
    }

    return $nombre;
}
//-------------------------------------------------------------------------------
function mostrarOpciones(){
    echo "==============[Menu De Opciones]================\n";
    echo "1 consultar saldo\n";
    echo "2 realizar retiro\n";
    echo "3 consultar info de retiros realizados\n";
    echo "4 realizar transferencia\n";
    echo "5 consultar info de transferencias realizadas\n";
    echo "6 salir del programa\n";
    echo "================================================\n";
}
//-----------------[Funciones de menu]--------------------------------------------
function consultarSaldo($numCuenta,$listaClientes){
    foreach($listaClientes as $cliente){
        if($cliente["numCuenta"]==$numCuenta){
            echo "=========================\n";
            echo "saldo actual: " . $cliente["saldo"] . " USD\n";
            echo "=========================\n";
        }
    }
}
//--------------------------------------------------------------------------------
function realizarRetiro($numCuenta,&$listaClientes,$monto_retiro): bool{
    $estado = false;

    foreach($listaClientes as &$cliente){
        if($cliente["numCuenta"] ==$numCuenta){
            $saldo_actual = $cliente["saldo"];
            if($saldo_actual >= $monto_retiro){
		        if($monto_retiro>0){
                  $cliente["saldo"] = $cliente["saldo"] - $monto_retiro;
                  $estado = true;
		        }
            }
        }
    }
    
    return $estado;
}
//-------------------------------------------------------------------------
function realizarTransferencia($numCuentaIngreso,&$listaClientes,$monto_transfer,$numCuentaTransfer): bool{
    $estado = false;

    foreach($listaClientes as &$cliente){
        if($cliente["numCuenta"] ==$numCuentaIngreso){
            $saldo_actual = $cliente["saldo"];
            if($saldo_actual >= $monto_transfer){
		        if($monto_transfer>0){
                  $cliente["saldo"] = $cliente["saldo"] - $monto_transfer;
                  //recorriendo para sumar el monto a la cuenta a transferir
                  foreach($listaClientes as &$cTransfer){
                    if($cTransfer["numCuenta"]==$numCuentaTransfer){
                        $cTransfer["saldo"] = $cTransfer["saldo"] + $monto_transfer;
                    }
                  }
                  //fin del foreach para sumar monto de cuenta a transferir
                  $estado = true;
		        }
            }
        }
    }
    
    return $estado;   
}

//------------------------------------------------------------------------------------
function mostrarInfoGeneralRetiro($listaRetiros){
    echo "\n=========[Info General De Retiros]============\n";
    
    foreach($listaRetiros as $ret){
        echo "Usuario: " . $ret["usuario"] . "\n";
        echo "Valor retirado: " . $ret["valor_retirado"] . "\n";
        echo "Fecha de retiro: " . $ret["fecha_retiro"];
        echo "\n=======================================\n";
    }
}
//-----------------------------------------------------------------------------------

function mostrarInfoDetalladaRetiro($listaRetiros){
    $numTotalRetiro = 0;
    $valorTotalRetirado = 0;

    foreach($listaRetiros as $retiro){
        $numTotalRetiro++;
        $valorTotalRetirado += $retiro["valor_retirado"];
    }
    echo "\n=========[Info Detallada De Retiros]============\n";
    echo "Numero total de retiros: {$numTotalRetiro} veces\n";
    echo "Valor total retirado: {$valorTotalRetirado} USD\n";
    echo "\n================================================\n";
}
//---------------------------------------------------------------------------
function mostrarInfoGeneralTransferencias($listaTransferencias){
    echo "\n=========[Info General De Transferencias]============\n";

    foreach($listaTransferencias as $transfer){
        echo "Cuenta origen: " . $transfer["cuenta_origen"] . "\n";
        echo "Cuenta destino: " . $transfer["cuenta_destino"] . "\n";
        echo "Valor transferido: " . $transfer["valor_transferido"] . " USD\n";
        echo "Fecha de la transferencia: " . $transfer["fecha_transferencia"] . "\n";
        echo "\n=============================================================\n";
    }
}
//-----------------------------------------------------------------------------------
function mostrarInfoDetalladaTransferencia($listaTransferencias){
    $numTotalTransferencias = 0;
    $valorTotalTransferido = 0;

    foreach($listaTransferencias as $transfer){
        $numTotalTransferencias++;
        $valorTotalTransferido += $transfer["valor_transferido"];
    }
    echo "\n=========[Info Detallada De Transferencias]============\n";
    echo "Numero total de transferencias: {$numTotalTransferencias} veces\n";
    echo "Valor total tranferido: {$valorTotalTransferido} USD\n";
    echo "\n========================================================\n";
}
//----------------[FIN Funciones]-----------------------

//----------------[   INICIO    ]-----------------------
$encontrado = 0;
$intentos = 0;
//----------------[   Validacion de numero de cuenta  ]-----------------------
do {
    if($encontrado == 0 && $intentos != 0){
        echo "Cuenta no encontrada\n";
    }

    $cuenta_ingr = readline("Ingrese su cuenta: ");
    $encontrado = verificarExistenciaCuenta($cuenta_ingr,$clientes);
    $intentos++;
} while ($encontrado == 0);
//----------------[   Validacion de password   ]-----------------------
$intentosPass = 0;
$estado = false;

do{
    
    if($intentosPass != 0 && !$estado){
        echo "Password incorrecta, Llevas {$intentosPass} intentos\n";
    }

    $password_ingr = readline("Ingrese password:");
    $estado = verificarPassword($cuenta_ingr,$password_ingr,$clientes);
    $intentosPass++;
    echo "\n";

}while(!$estado);
//----------------[   MOSTRAR MENÚ    ]-----------------------
echo "Bienvenido " . obtenerNombrePorCuenta($cuenta_ingr,$clientes) . "\n";
mostrarOpciones();

do{  
    $intentos_menu = 0;
    do{
        if($intentos_menu != 0){
            echo "Error, opcion escogida invalido\n";
        }
        echo "================================\n";
        $opcion_escogida = readline("ingrese el numero de su opcion: ");
        echo "================================\n";
        $intentos_menu++;
    }while($opcion_escogida <= 0 || $opcion_escogida >= 7);
//--------------------------------[   Inicio de opciones - Casos   ]-----------------------
    switch ($opcion_escogida) {
//--------------------------------[Caso 1: Consultar saldo]------------------------------------
        case 1:
            echo "elegiste la opcion 1\n";
                consultarSaldo($cuenta_ingr,$clientes);
            break;
//--------------------------------[Caso 2: Hacer retiro]------------------------------------  
        case 2:
            echo "elegiste la opcion 2, debes volver a ingresar tu password\n";
//-------------------------------[Validando la password otra vez, es retiro]------------------
            $intentosPass = 0;
            $estado = false;
            
            do{
                if($intentosPass != 0 && !$estado){
                    echo "Password incorrecta, Llevas {$intentosPass} intentos\n";
                }

                $password_ingr = readline("Ingrese password:");
                $estado = verificarPassword($cuenta_ingr,$password_ingr,$clientes);
                $intentosPass++;
                echo "\n";
            }while(!$estado);

//-------------------------------[Se validó la password, ahora a validar el monto]-----------------
            $sin_saldo = false;
            $saldo_negativo = false;
            foreach($clientes as $c){
                if($c["numCuenta"] == $cuenta_ingr && $c["saldo"] == 0){
                    $sin_saldo = true;   
                }
            }
            if($sin_saldo){
                echo "No hay saldo en la cuenta\n";
            }else{
                
                do{
                    $monto_retiro = readline("Ingrese el monto a retirar: ");
                    $estado_retiro = realizarRetiro($cuenta_ingr,$clientes,$monto_retiro);
                        if($monto_retiro <= 0){
                            $saldo_negativo = true;
                            echo "\nMonto no valido, ingrese un monto superior a 0\n";
                        
                        }else{
                            $saldo_negativo = false;
                            if(!$estado_retiro){
                                echo "\nSaldo insuficiente para realizar esta acción\n";
                                $sin_saldo = true;
                            }else{
				                $sin_saldo = false;
                                echo "Retiro exitoso\n";
                                $retiro = ["usuario"=>obtenerNombrePorCuenta($cuenta_ingr,$clientes),
                                "valor_retirado"=>$monto_retiro,
                                "fecha_retiro"=>date("d/m/Y H:i:s")];

                                $retiros[] = $retiro;
                            }
                        }
                }while((!$estado_retiro && !$sin_saldo)||$saldo_negativo);
            }
            break;
//--------------------------------[Caso 3: Consultar info de retiros]------------------------------------        
        case 3:
            echo "elegiste la opcion 3\n";
            $cantidadRetiros = count($retiros);
            if($cantidadRetiros == 0){
                echo "No haz realizado retiros aun\n";
                echo "============================\n";
            }else{
                mostrarInfoGeneralRetiro($retiros);
                $eleccion_correcta = false;
                do{
                    $eleccion_info = readline("¿Quieres ver info mas detallada de los retiros(S para si o N para no)?: ");
                    if($eleccion_info == "S"){
                        mostrarInfoDetalladaRetiro($retiros);
                        $eleccion_correcta = true;
                    }elseif($eleccion_info == "N") {
                        $eleccion_correcta = true;
                    }else{
                        echo "\nOpcion escogida no valida, ingresa S o N\n";
                        echo "=========================================\n";
                    }
                }while(!$eleccion_correcta);
            }
            break;
//--------------------------------[Caso 4: Hacer transferencias]------------------------------------  
        case 4:
            echo "elegiste la opcion 4\n";
            echo "Nota: Prueba con cuenta 234, tiene 2000 USD y se imprime al final\n";
            $encontrado = 0;
            $intentos = 0;
            //----------------[   Validacion existencia de numero de cuenta a transferir  ]-----------------------
            do {
                if($encontrado == 0 && $intentos != 0){
                echo "Cuenta no encontrada\n";
                }

                $cuenta_transfer = readline("Ingrese la cuenta a transferir: ");
                $encontrado = verificarExistenciaCuenta($cuenta_transfer,$clientes);
                $intentos++;
                if($cuenta_ingr == $cuenta_transfer){
                    echo "\nNo te puedes transferir a tu misma cuenta\n";
                    $encontrado = 0;
                    $intentos = 0;
                }              
            } while ($encontrado == 0);
//-------------------------------[Validando la password otra vez, es transferencia]------------------
            $intentosPass = 0;
            $estado = false;
            
            do{
                if($intentosPass != 0 && !$estado){
                    echo "Password incorrecta, Llevas {$intentosPass} intentos\n";
                }

                $password_ingr = readline("Ingrese password:");
                $estado = verificarPassword($cuenta_ingr,$password_ingr,$clientes);
                $intentosPass++;
                echo "\n";
            }while(!$estado);
//-------------------------------[Se validó la password, ahora a validar el monto]-----------------
            $sin_saldo = false;
            $saldo_negativo = false;
            foreach($clientes as $c){
                if($c["numCuenta"] == $cuenta_ingr && $c["saldo"] == 0){
                    $sin_saldo = true;   
                }
            }
            if($sin_saldo){
                echo "No hay saldo en la cuenta\n";
            }else{
                do{
                    $monto_transfer = readline("Ingrese el monto a transferir: ");            
                    $estado_transfer = realizarTransferencia($cuenta_ingr,$clientes,$monto_transfer,$cuenta_transfer);
                        if($monto_transfer <= 0){
                            $saldo_negativo = true;
                            echo "\nMonto no valido, ingrese un monto superior a 0\n";
                        
                        }else{
                            $saldo_negativo = false;
                            if(!$estado_transfer){
                                echo "\nSaldo insuficiente para realizar esta acción\n";
                                $sin_saldo = true;
                            }else{
				                $sin_saldo = false;
                                echo "transferencia exitosa\n";
                                //imprimir la prueba
                                foreach($clientes as $ci){
                                    if($ci["numCuenta"]==$cuenta_transfer){
                                        echo "=========================================\n";
                                        echo "Saldo cuenta prueba:" . $ci["saldo"] . "\n";
                                        echo "=========================================\n";
                                        
                                    }
                                }
                                $transferencia = ["cuenta_origen"=>$cuenta_ingr,
                                                  "cuenta_destino"=>$cuenta_transfer,
                                                  "valor_transferido"=>$monto_transfer,
                                                  "fecha_transferencia"=>date("d/m/Y H:i:s")];
                                $transferencias[] = $transferencia;
                            }
                        }
                }while((!$estado_transfer && !$sin_saldo)||$saldo_negativo);
            }
            break;
//--------------------------------[Caso 5: Consultar info de transferencias]------------------------------------  
        case 5:
            echo "elegiste la opcion 5\n";
            $cantidadTransferencias = count($transferencias);
            if($cantidadTransferencias == 0){
                echo "No haz realizado transferencias aun\n";
                echo "===================================\n";
            }else{
                mostrarInfoGeneralTransferencias($transferencias);
                $eleccion_correcta = false;
                do{
                    $eleccion_info = readline("¿Quieres ver info mas detallada de las transferencias(S para si o N para no)?:   ");
                    if($eleccion_info == "S"){
                        mostrarInfoDetalladaTransferencia($transferencias);
                        $eleccion_correcta = true;
                    }elseif($eleccion_info == "N") {
                        $eleccion_correcta = true;
                    }else{
                        echo "Opcion escogida no valida, ingresa S o N\n";
                        echo "=========================================\n";
                    }
                }while(!$eleccion_correcta);
            }
            break;
//--------------------------------[Caso default - salida]------------------------------------  
        default:
            if($opcion_escogida != 6){
                echo "Error, opcion escogida invalido\n";
            }else{
                echo "Hasta pronto";
            }
            break;
    }
}while($opcion_escogida !=6);

?>
