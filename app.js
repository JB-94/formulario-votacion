$(document).ready(function () {
    console.log("Document Ready");
    loadCandidates();
    loadRegions();

    $("#votarButton").on("click", function() {
        validateForm();
    });

    $("#id_region").change(function () {
        var idRegion = $(this).val();
        loadComunne(idRegion);
    });

    $("#voting-form").submit(function (event) {
        event.preventDefault();
        validateForm();
    });

    function loadCandidates() {
        $.ajax({
            url: "get_candidates.php",
            type: "GET",
            dataType: "json",
            success: function (response) {
                var select = $("#id_candidato");
                select.empty();
                select.append($('<option>', {
                    value: "",
                    text: "Seleccione su Candidato",
                    disabled: true,
                    selected: true
                }));
                $.each(response, function (index, candidate) {
                    select.append($('<option>', {
                        value: candidate.ID_CANDIDATO,
                        text: candidate.NOMBRE_CANDIDATO
                    }));
                });
            },
            error: function (xhr, status, error) {
                console.log("Error al cargar candidatos: " + error);
            }
        });
    }

    function loadRegions() {
        $.ajax({
            url: "get_regions.php",
            type: "GET",
            dataType: "json",
            success: function (response) {
                var select = $("#id_region");
                select.empty();
                select.append($('<option>', {
                    value: "",
                    text: "Seleccione su Región",
                    disabled: true,
                    selected: true
                }));
                $.each(response, function (index, regions) {
                    select.append($('<option>', {
                        value: regions.ID_REGION,
                        text: regions.NOMBRE_REGION
                    }));
                });
            },
            error: function (xhr, status, error) {
                console.log("Error al cargar las regiones: " + error);
            }
        });
    }

    function loadComunne(idRegion) {
        $.ajax({
            url: "get_comunne.php",
            type: "GET",
            data: { ID_REGION: idRegion },
            dataType: "json",
            success: function (response) {
                var select = $("#id_comuna");
                select.empty();
                select.append($('<option>', {
                    value: "",
                    text: "Seleccione su Comuna",
                    disabled: true,
                    selected: true
                }));
                $.each(response, function (index, comuna) {
                    select.append($('<option>', {
                        value: comuna.ID_COMUNA,
                        text: comuna.NOMBRE_COMUNA
                    }));
                });
            },
            error: function (xhr, status, error) {
                console.log("Error al cargar las comunas: " + error);
            }
        });
    }
    
    function validateForm() { //Aquí va la Función que válida el formulario
        var nombre = document.getElementsByName('nombre')[0].value;
        var alias = document.getElementsByName('alias')[0].value;
        var rut = document.getElementsByName('rut')[0].value;
        var email = document.getElementsByName('email')[0].value;
        var idRegion = document.getElementsByName('id_region')[0].value;
        var idComuna = document.getElementsByName('id_comuna')[0].value;
        var idCandidato = document.getElementsByName('id_candidato')[0].value;
        var comoSeEntero = document.querySelectorAll('input[name="como_se_entero[]"]:checked').length;

        if (nombre === '') {
            alert("Nombre y Apellido no debe quedar en blanco");
            return;
        }

        if (alias.length < 6 || !/^[a-zA-Z0-9]+$/.test(alias)) {
            alert("Alias debe tener al menos 6 caracteres y contener letras y números");
            return;
        }

        if (!/\d{1,8}-[\d|kK]{1}/.test(rut)) {
            alert("Ingrese un RUT válido en formato chileno");
            return;
        }

        if (!/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/.test(email)) {
            alert("Ingrese un correo electrónico válido");
            return;
        }

        if (idRegion === '') {
            alert("Debe seleccionar una Región");
            return;
        }

        if (idComuna === '') {
            alert("Debe seleccionar una Comuna");
            return;
        }

        if (idCandidato === '') {
            alert("Debe seleccionar un Candidato");
            return;
        }

        if (comoSeEntero < 2) {
            alert("Debe elegir al menos dos opciones en 'Como se enteró de nosotros'");
            return;
        }
        var rut = document.getElementsByName('rut')[0].value;
        console.log("RUT: " + rut);
        checkDuplicateRUT(rut);
    }


    function checkDuplicateRUT(rut) {//Aquí va la función que válida que el rut no se duplique en el voto
        $.ajax({
            url: "check_duplicate_rut.php",
            type: "POST",
            data: { rut: rut },
            success: function (response) {
                response = response.trim();
                if (response.indexOf('"exists":true') !== -1) {
                    alert("Ya se ha registrado un voto con este RUT");
                } else {
                    submitForm();
                }
            },
            error: function (xhr, status, error) {
                alert("Error al verificar duplicados: " + error);
            }
        });
    }


    function submitForm() {//Aquí va la función que guarda el voto en la Base De Datos
        var formData = $("#voting-form").serialize();
        $.ajax({
            url: "add_vote.php",
            type: "POST",
            data: formData,
            success: function (response) {
                alert("Voto registrado exitosamente");
            },
            error: function (xhr, status, error) {
                alert("Error al registrar el voto " + error);
            }
        });
    }
});
