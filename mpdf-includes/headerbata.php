<?php 
    include_once('./connection.php');
    include_once('./class/getObject.php');

    //FETCH IZVJEŠTAJ
    $izvjestaj = new singleObject;
    $izvjestaj = $izvjestaj->fetch_single_object('izvjestaji', 'izvjestaji_id', $_GET['izvjestaj']);

    //FETCH TIP IZVJEŠTAJA
    $tipizvjestaja = new singleObject;
    $tipizvjestaja = $tipizvjestaja->fetch_single_object("tipoviizvjestaja", "tipoviizvjestaja_id", $izvjestaj['izvjestaji_tipizvjestajaid']);

    //FETCH VRSTA UREĐAJA
    $vrstauredjaja = new singleObject;
    $vrstauredjaja = $vrstauredjaja->fetch_single_object("vrsteuredjaja", "vrsteuredjaja_id", $tipizvjestaja['tipoviizvjestaja_vrstauredjajaid']);
        
?>

<htmlpageheader name="header">
    <div class="header-logo" style="width: 33%;float: left;">
        <img src="./images/logoBez.png" alt="" style="width: 160px;">
    </div>
    <div class="header-middle" style="width: 34%;float: left;padding-top: 10px;">
        <p style="font-family: 'Times New Roman', Times, serif;font-size: 11px;text-align: center;margin:0;">
            <strong><?php echo $tipizvjestaja['tipoviizvjestaja_naziv'] ?></strong>
            <br>
            <?php echo $vrstauredjaja['vrsteuredjaja_naziv'] ?>
        </p>
    </div>
    <div class="header-info" style="width: 33%;float: right;">
        <p style="font-family: 'Times New Roman', Times, serif;font-size: 11px;text-align: right;margin:0;">"NormaLab" d.o.o. Banja Luka</p>
        <p style="font-family: 'Times New Roman', Times, serif;font-size: 11px;text-align: right;margin:0;">Srpska 99, II sprat, 8б</p>
        <p style="font-family: 'Times New Roman', Times, serif;font-size: 11px;text-align: right;margin:0;">office@normalab.ba</p>
        <p style="font-family: 'Times New Roman', Times, serif;font-size: 11px;text-align: right;margin:0;">+387 66 76 67 81</p>
    </div>
</htmlpageheader>