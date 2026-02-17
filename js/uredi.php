<script>

    <?php

    if (!isset($h) || !isset($t) || !isset($o)) {
        $h = "";
        $t = "";
        $o = "";
        $i = "";
    }

    ?>

    function editItem() {
        if ($("#editItem").attr("itemToEdit") == "") {
            alert('Molimo označite <?php echo $itemToSelect ?> za uređivanje.');
        } else {
            window.location.replace($("#editItem").attr("itemToEdit"));
        }
    }

    function addRadniNalog() {
        if ($("#addRadniNalog").attr("mjeriloToProvide") == "") {
            alert('Molimo označite <?php echo $itemToSelect ?> za koji želite kreirati radni nalog.');
        } else {
            window.location.replace($("#addRadniNalog").attr("mjeriloToProvide"));
        }
    }

    function openPdfRadniNalog() {
        if ($("#opetPdf").attr("pdfToOpen") == "") {
            alert('Molimo označite <?php echo $itemToSelect ?> za generisanje pdf dokumenta.');
        } else {
            window.location.replace($("#opetPdf").attr("pdfToOpen"));
    }
    }
    function kreirajOtvoriIzvjestaj() {
        if ($("#openReport").attr("reportToShow") == "") {
            alert('Molimo označite <?php echo $itemToSelect ?> za kreiranje ili pregled izvještaja.');
        } else {
            window.location.replace($("#openReport").attr("reportToShow"));
        }
              }
          $(".selectItemButton").click(function () {
          if ($(this)[0].checked == true) {
            //console.log("!");
            $(".selectedRow").removeClass("selectedRow").prop('checked',false);
            var klasaSvih = $(".selectItemButton");
            klasaSvih.prop('checked',false);
            $(this).prop('checked',true);
            //console.log("true");
            var h = $(this).attr("h");
            var t = $(this).attr("t");
            var o = $(this).attr("o");
            var i = $(this).attr("i");
            var m = $(this).attr("m");
            $("#editItem").attr("itemToEdit", "uredi.php?h="+h+"&t="+t+"&o="+o);
            $("#addRadniNalog").attr("mjeriloToProvide", "dodajradninalog.php?mjerilo="+o);
            $("#opetPdf").attr("pdfToOpen", "pregledradnognaloga.php?radninalog="+o);
            $("#openReport").attr("reportToShow", i);
            $(this).parent().parent().addClass("selectedRow");
            $("#deleteItem").attr("itemToDelete", "ukloni.php?t="+t+"&o="+o);
            $("#linkToDelete").attr("href", "ukloni.php?t="+t+"&o="+o);
            $("#deleteItem>button").attr("data-target","#deleteModal");
            $("#openReportZavod").attr('href', 'izvjestajmpdf.php?uredjaj='+m+'&izvjestaj='+o);
        } else {
            //console.log("?");
            $("#editItem").attr("itemToEdit", "");
            $("#opetPdf").attr("pdfToOpen", "");
            $("#openReport").attr("reportToShow", "");
            $(this).parent().parent().removeClass("selectedRow");
            //$(".selectItemButton").click();
            $("#deleteItem").attr("itemToDelete", "");
            $("#deleteItem>button").attr("data-target","");
            $("#linkToDelete").attr("href", "");
        }
    });

    function deleteItem() {
        if ($("#deleteItem").attr("itemToDelete") == "") {
            alert('Molimo označite <?php echo $itemToSelect ?> za uklanjanje iz sistema.');
        }
    }

              

    $(document).ready(function () {

        if($('input[name="mjerila_vrstauredjajaid"]').val() == 11 || $('input[name="mjerila_vrstauredjajaid"]').val() == 12){
            $("#hiddenLabel").removeAttr("hidden");
            $("#hiddenSelect").removeAttr("hidden");
            var mjerila_djeca = $('input[name="mjerila_djeca"]').val();
            $('#hiddenSelect option[value="'+mjerila_djeca+'"]').prop('selected', true);
        }

        $(".selectElement_").change(function () {
            var selectValue = $(this).val();
            $(this).prev().val(selectValue);
            //console.log();
            if($('input[name="mjerila_vrstauredjajaid"]').length > 0 && ($('input[name="mjerila_vrstauredjajaid"]').val() == 11 || $('input[name="mjerila_vrstauredjajaid"]').val() == 12)){
                $("#hiddenLabel").removeAttr("hidden");
                $("#hiddenSelect").removeAttr("hidden");
            }else if($('input[name="mjerila_vrstauredjajaid"]').length > 0 && ($('input[name="mjerila_vrstauredjajaid"]').val() != 11 && $('input[name="mjerila_vrstauredjajaid"]').val() != 12)){
                $("#hiddenLabel").attr("hidden",1);
                $("#hiddenSelect").attr("hidden",1);
                $("#hiddenSelect").val("");
                $('input[name="mjerila_djeca"]').val("");
            }
        });
    });

</script>