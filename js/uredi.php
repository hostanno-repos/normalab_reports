<script>

    <?php

    if (!isset($h) || !isset($t) || !isset($o)) {
        $h = "";
        $t = "";
        $o = "";
        $i = "";
    }
    if (!isset($itemToSelect)) {
        $itemToSelect = "";
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
            window.open($("#opetPdf").attr("pdfToOpen"), "_blank");
        }
    }

    function setSelectionFromCheckbox($checkbox) {
        var h = $checkbox.attr("h");
        var t = $checkbox.attr("t");
        var o = $checkbox.attr("o");
        var i = $checkbox.attr("i");
        var m = $checkbox.attr("m");
        $("#editItem").attr("itemToEdit", "uredi.php?h="+h+"&t="+t+"&o="+o);
        $("#addRadniNalog").attr("mjeriloToProvide", "dodajradninalog.php?mjerilo="+o);
        $("#opetPdf").attr("pdfToOpen", "pregledradnognaloga.php?radninalog="+o);
        $("#openReport").attr("reportToShow", i);
        $("#deleteItem").attr("itemToDelete", "ukloni.php?t="+t+"&o="+o);
        $("#linkToDelete").attr("href", "ukloni.php?t="+t+"&o="+o);
        $("#deleteItem>button").attr("data-target","#deleteModal");
        if ($("#openReportZavod").length) {
            $("#openReportZavod").attr('href', 'izvjestajmpdf.php?uredjaj='+m+'&izvjestaj='+o);
        }
        if ($("#openReportBataNovi").length) {
            $("#openReportBataNovi").attr('href', 'izvjestajmpdfbata.php?uredjaj='+m+'&izvjestaj='+o);
        }
    }

    function clearSelectionState() {
        $("#editItem").attr("itemToEdit", "");
        $("#addRadniNalog").attr("mjeriloToProvide", "");
        $("#opetPdf").attr("pdfToOpen", "");
        $("#openReport").attr("reportToShow", "");
        $("#deleteItem").attr("itemToDelete", "");
        $("#deleteItem>button").attr("data-target","");
        $("#linkToDelete").attr("href", "");
        if ($("#openReportZavod").length) {
            $("#openReportZavod").attr("href", "");
        }
        if ($("#openReportBataNovi").length) {
            $("#openReportBataNovi").attr("href", "");
        }
    }

    function refreshMultiSelectionState() {
        var $checked = $(".selectItemButton:checked");
        $(".selectItemButton").each(function () {
            $(this).closest("tr").toggleClass("selectedRow", this.checked);
        });
        if ($checked.length === 0) {
            clearSelectionState();
            return;
        }
        setSelectionFromCheckbox($checked.last());
    }
    function kreirajOtvoriIzvjestaj() {
        if ($("#openReport").attr("reportToShow") == "") {
            alert('Molimo označite <?php echo $itemToSelect ?> za kreiranje ili pregled izvještaja.');
        } else {
            window.location.replace($("#openReport").attr("reportToShow"));
        }
              }
          // Delegacija događaja da radi i za dinamički učitane redove (npr. nakon pretrage izvještaja)
          $(document).on("click", ".selectItemButton", function () {
          var multiSelect = $(this).closest("table").data("multiSelect") == 1;
          if ($(this)[0].checked == true) {
            if (multiSelect) {
                refreshMultiSelectionState();
                return;
            }
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
            if ($("#openReportBataNovi").length) $("#openReportBataNovi").attr('href', 'izvjestajmpdfbata.php?uredjaj='+m+'&izvjestaj='+o);
        } else {
            if (multiSelect) {
                refreshMultiSelectionState();
                return;
            }
            //console.log("?");
            $("#editItem").attr("itemToEdit", "");
            $("#opetPdf").attr("pdfToOpen", "");
            $("#openReport").attr("reportToShow", "");
            $(this).parent().parent().removeClass("selectedRow");
            //$(".selectItemButton").click();
            $("#deleteItem").attr("itemToDelete", "");
            $("#deleteItem>button").attr("data-target","");
            $("#linkToDelete").attr("href", "");
            $("#openReportZavod").attr("href", "");
            if ($("#openReportBataNovi").length) $("#openReportBataNovi").attr("href", "");
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