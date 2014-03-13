/*
 * html5sync Plugin v.0.0.3 (https://github.com/maparrar/html5sync)
 * Feb 2014
 * - maparrar: http://maparrar.github.io
 * - jomejia: https://github.com/jomejia
 */
/**
 * Clase para la sincronización con el servidor [constructor pattern]
 * @param {object} params Objeto con los parámetros de la sincronización
 * @param {function} callback Función a la que se retornan los resultados
 */
var Html5Sync = function(params,callback){
    /**************************************************************************/
    /******************************* ATTRIBUTES *******************************/
    /**************************************************************************/
    var self = this;
    self.state;         //{bool} Estado de la conexión con el servidor.
    /**************************************************************************/
    /********************* CONFIGURATION AND CONSTRUCTOR **********************/
    /**************************************************************************/
    //Se mezclan los parámetros por defecto con los proporcionados por el usuario
    //y se agregan a la variable self (this del objeto)
    var def = {
        debugging:false,
        html5syncFolder:"html5sync/",
        stateTimer: 10000,
        showState:false
    };
    self.params = $.extend(def, params);
    /**
     * Método privado que se ejecuta automáticamente. Hace las veces de constructor
     */
    var Html5Sync = function() {
        //Estructura el código HTML5
        setStructure();
        
        //Inicia el proceso de sincronización
        startSync();
        
        //Hace la carga inicial de datos
//        loadData();
        
        
        $("#checkChanges").click(function(){
            checkChanges();
        });
        $("#loadData").click(function(){
            loadData();
        });
    }();
    
    /**************************************************************************/
    /**************************** PRIVATE METHODS *****************************/
    /**************************************************************************/
    /**
     * Inicia el proceso de sincronización.
     */
    function startSync(){
        sync();
        setInterval(function(){
            try{
                sync();
            }catch(e){
                setState(false); 
            }
        },self.params.stateTimer);
    };
    /**
     * Verifica si la conexión con el servidor está activa y actualiza el 
     * indicador de estado. Verifica si hay cambios en las tablas para el usuario,
     * si los hay, retorna los cambios.
     */
    function sync(){
        $.ajax({
            url: self.params.html5syncFolder+"server/ajax/sync.php"
        }).done(function(response) {
            setState(Boolean(JSON.parse(response).state));
        }).fail(function(){
            setState(false);
        });
    };
    /**
     * Establece el estado de la conexión con el servidor
     * @param {bool} state Estado de la conexión
     */
    function setState(state){
        self.state=true;
        if(self.params.showState){
            if(state){
                self.stateLabel.removeClass("offline").addClass("online");
                self.stateLabel.find("#state").text("on line");
            }else{
                self.stateLabel.removeClass("online").addClass("offline");
                self.stateLabel.find("#state").text("off line");
            }
        }
    };
    /**
     * Crea la estructura de la aplicación en HTML5. Define si se muestra el área
     * de debugging y/o de estado.
     */
    function setStructure(){
        //Create the exist() function for any selector. i.e: $("selector").exist()
        $.fn.exist=function(){return this.length>0;};
        //Starts without connection
        self.state=false;
        var state='';
        if(self.params.showState){
            state='<div id="html5sync_state">'+
                        '<div class="html5sync_spinner"></div>'+
                        '<div id="state">checking</div>'+
                        
                    '</div>';
        }
        $("body").prepend(
                '<div id="html5sync_info">'+
                    state+
                '</div>'
            );
        self.stateLabel=$("#html5sync_state");
        self.loadingLabel=$(".html5sync_spinner");
        window.debug=function(message){
            if(self.params.debugging){
                if(!$("#html5sync_debug").exist()){
                    $("body").prepend('<div id="html5sync_debug"></div>');
                }
                $("#html5sync_debug").append(message+"<br>");
                $("#html5sync_debug").scrollTop($('#html5sync_debug').get(0).scrollHeight);
            }
        };
    };
    
    
    /**
     * Carga los datos de las tablas permitidas. Toda la información de carga
     * está especificada en el archivo de configuración:
     * html5sync/server/config.php
     */
    function loadData(){
        try{
            showLoading(true);
            $.ajax({
                url: self.params.html5syncFolder+"server/ajax/loadData.php"
            }).done(function(response) {
//                console.debug(response);
                showLoading(false);
            }).fail(function(){
                showLoading(false);
            });
        }catch(e){
            setState(false); 
        }
    };
    
    
    
    
    /**
     * Pruebas para detectar cambios en tablas grandes
     * está especificada en el archivo de configuración:
     * html5sync/server/config.php
     */
    function checkChanges(){
        try{
            showLoading(true);
            $.ajax({
                url: self.params.html5syncFolder+"server/ajax/checkChanges.php"
            }).done(function(response) {
//                console.debug(response);
                showLoading(false);
            }).fail(function(){
                showLoading(false);
            });
        }catch(e){
            setState(false); 
        }
    };
    
    
    
    
    /**
     * Muestra u oculta el loader de la librería
     * @param {boolean} state Estado en que se quiere poner la imagen del loader
     */
    function showLoading(state){
        if(state){
            self.loadingLabel.show();
        }else{
            self.loadingLabel.hide();
        }
    }
    /**************************************************************************/
    /***************************** PUBLIC METHODS *****************************/
    /**************************************************************************/
    /**
     * Inserta objetos en un almacén. Recibe un objeto o un array de objetos
     * @param {string} storeName Nombre del almacén de datos donde se quiere insertar la información
     * @param {object[]} data Objeto o array de objetos
     * @param {function} callback Función a la que se retornan los resultados
     */
    self.publicFunction=function(storeName,data,callback){
        
    };
    
    
    
};