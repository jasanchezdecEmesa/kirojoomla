//Scrip que se genera cuando queremos crear visitas en bloque, donde mostraremos unos bloques u otros
//document.addEventListener("DOMContentLoaded", function() {
window.onload = function(){
    var calendario = document.getElementById('calendario-puntual').getElementsByClassName('js-calendar')[0];

    document.getElementById('jform_dias_puntuales-lbl').style.display = 'block';
    document.getElementById('jform_dias_puntuales').style.display = 'block';
    document.getElementById('jform_dias_puntuales_btn').style.display = 'block';
    document.getElementById('diaspuntuales').style.display = 'block';
    calendario.classList.add('blockimpt');

    var diasclick = document.getElementById('calendario-puntual').getElementsByClassName('day');

    for(i=0;i<diasclick.length;i++){
        diasclick[i].addEventListener('click', function(){
            nuevaFecha();
        });
    }

    document.getElementById('jform_diaspuntuales').value = '';

};


function nuevaFecha(){
    var fecha = document.getElementById('jform_dias_puntuales').value;
    var node = document.createElement("LI");
    node.id = fecha;

    var fechaspa = fecha.substring(8,10)+'-'+fecha.substring(5,7)+'-'+fecha.substring(0,4);

    node.innerHTML = ' <button type="button" onclick="eliminarFecha(\''+fecha+'\')">'+fechaspa+'<span class="fa fa-remove"></span></button>';
    if(fecha.length == '10') {
        document.getElementById("diaspuntuales").appendChild(node);
        inputFechas(fecha,'crear');
    }
}

function eliminarFecha(fecha) {
    document.getElementById(fecha).remove();
    inputFechas(fecha,'eliminar');
}

function inputFechas(fecha,tipo){
    var dias = document.getElementById('jform_diaspuntuales').value.split(',');
    dias = dias.filter(d => d.length>0);

    if(tipo == 'crear') {
        dias.push(fecha);
        document.getElementById('jform_diaspuntuales').value = dias.toString();
    }

    if(tipo == 'eliminar') {
        console.log(dias);
        alert(dias.indexOf(fecha));
        dias.splice(dias.indexOf(fecha),1);
        console.log(dias);
        document.getElementById('jform_diaspuntuales').value = dias.toString();
    }
}