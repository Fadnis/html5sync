<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


echo("Pruebas");

?>
<html>
    <head>
        <title>html5sync</title>
        
        <script src="scripts/jquery-2.1.0.min.js"></script>
        
        <link rel="stylesheet" type="text/css" href="scripts/base.css">
        
        <script type="text/javascript">
            $( document ).ready(function(){
                if(window.indexedDB === undefined) {
                    console.log("Este navegador no soporta indexDB");
                }else{
                    debug("Base de datos indexDB soportada");
                    
                    
                    
                    
                    var db;
                    var request = window.indexedDB.open("html5sync",9);
                    /*
                     * Evento del request para manejar los errores. Se dispara si
                     * por ejemplo, un usuario no permite que se usen bases de
                     * datos indexedDB en el navegador.
                     */
                    request.onerror = function(e) {
                        debug("No es posible conectar con la base de datos local");
                    };
                    /*
                     * Evento del request cuando es posible usar la base de datos
                     * indexedDB en el navegador.
                     */
                    request.onsuccess = function(e) {
                        debug("Request completo a la base de datos");
                        db = request.result;
                    };
                    
                    /*
                     * Evento del request que se dispara cuando la base de datos
                     * necesita ser modificada (la estructura de la base de datos).
                     * Si en la línea:
                     *      var request = window.indexedDB.open("html5sync",3);
                     * se cambia la versión, es decir el segundo parámetro de la
                     * función open() a 4, se dispara este evento.
                     * Si se hace un downgrade, se pone en 2, genera un error.
                     */
                    request.onupgradeneeded = function(e) {
                        //Se crea o reemplaza la base de datos
                        db = e.target.result;
                        
                        /*
                         * Crea un almacén de objetos que se puede definir de la
                         * siguiente manera:
                         * |Key Path    Key Generator 	Description
                         * |(keyPath)   (autoIncrement)
                         * |__________________________________________________________________________________
                         *  No          No              This object store can hold any kind of value, 
                         *                              even primitive values like numbers and strings. 
                         *                              You must supply a separate key argument whenever 
                         *                              you want to add a new value.
                         *  Yes     	No              This object store can only hold JavaScript objects. 
                         *                              The objects must have a property with the same name 
                         *                              as the key path.
                         *  No          Yes             This object store can hold any kind of value. The 
                         *                              key is generated for you automatically, or you can 
                         *                              supply a separate key argument if you want to use a 
                         *                              specific key.
                         *  Yes         Yes             This object store can only hold JavaScript objects. 
                         *                              Usually a key is generated and the value of the 
                         *                              generated key is stored in the object in a property 
                         *                              with the same name as the key path. However, if such 
                         *                              a property already exists, the value of that property 
                         *                              is used as key rather than generating a new key.
                         */
                        var store = db.createObjectStore("music", {keyPath: "id"});
                        
                        
                        /*
                         * Se crea el conjunto de índices para la base de datos
                         */
                        var songIndex = store.createIndex("by_song", "song", {unique: true});
                        var interpreterIndex = store.createIndex("by_interpreter", "interpreter", { unique: false });
                        var albumIndex = store.createIndex("by_album", "album", { unique: false });
                        debug("Índices creados");
                        
                        /*
                         * Este evento se ejecuta cuando se ha creado el almacén
                         * de objetos. Se usa para agregar de manera segura los 
                         * datos.
                         */
                        store.transaction.oncomplete = function(e) {
                            //Se crea una transacción para leer y escribir
                            var tx = db.transaction("music", "readwrite");
                            //Se obtiene el almacén de objetos
                            var store = tx.objectStore("music");
                            
                            
                            store.add({id: 1, interpreter: "Tom Yorke", song: "Analyse", album: "The eraser"});
                            store.add({id: 2, interpreter: "Bob Marly", song: "One love", album: "Legend"});
                            store.add({id: 3, interpreter: "Alice in Chains", song: "Angry Chair", album: "Unplugged"});
                            
                            debug("Información de prueba inicial insertada");
                            
                        };

                        

                        db.onerror = function(event) {
                            // Error en el acceso a la base de datos
                            debug("Error de base de datos: " + event.target.errorCode);
                        };
                    };



                    
                    
                    
                }






//                        // Populate with initial data.
//                        store.put({id: 1, interpreter: "Tom Yorke", song: "Analyse", album: "The eraser"});
//                        store.put({id: 2, interpreter: "Bob Marly", song: "One love", album: "Legend"});
//                        store.put({id: 3, interpreter: "Alice in Chains", song: "Angry Chair", album: "Unplugged"});
//                        
//                        
//                        
//                        var tx = db.transaction("music", "readwrite");
//                        var store = tx.objectStore("music");
//    
//                        store.put({id: 4, interpreter: "Fito Paez", song: "Circo Beat", album: "Circo Beat"});
//                        store.put({id: 5, interpreter: "Urge Overkill", song: "Girl you'll be a woman soon", album: "Stull"});
//    
//                        tx.oncomplete = function() {
//                          // All requests have succeeded and the transaction has committed.
//                        };
                
                
                
                
                
                
                
                
                




                

















                function debug(message){
                    $("#debug").append(message+"<br>");
                }
                
            });
        </script>
    </head>
    <body>
        <section id="debug"></section>
    </body>
</html>
