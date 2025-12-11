<?php
function documentHeader($ship)
{
    return '
    <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <title>pdf</title>
    <style>
    *{
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: sans-serif;
    }
    header{
   position: relative;
    }
    .wrapper{
        width: 80%;
        margin: 20px 50px;
        top: 0;

    }
    .logo-left{
      position: absolute;
      left: 50px;
    }
    .logo-right{
      position: absolute;
      right: 50px;
      top: 0;
    }

    .headings{
        text-align: center;
        line-height: 2;
        color: #3a3a3a;
    }

    .main-table{
        width: 100%;
        /* border-spacing: 0;  */
        text-align: left;
        color: #3a3a3a;
        border-collapse: collapse;
    }

    th{
        padding: 10px;
    }

    td{
        padding: 8px;

    }
     tr{
        border-bottom: 1px solid #535353 ;

    }
    .contacts{
        text-align: center;
    }
    .time{
        width: 100%;
        text-align: left;
    }

    .time td{
        padding:0;
        margin:3px 0;

    }
    .doc-footer{
        width: 100%;
        text-align: center;
    }
    h3{
        text-align: center;
        text-decoration:underline;
    }
    </style>
</head>

<body>
    <header>
       <div class="wrapper">
            <div class="logo-left">
                 <img src="data:image/jpeg;base64,' . getImage('assets/images/wma1.png') . '" alt="">
            </div>
            <div class="headings">
                <h5><b>THE UNITED REPUBLIC OF TANZANIA</b></h5>
                <h5><b>MINISTRY OF INDUSTRY AND TRADE </b></h5>
                <h5>WEIGHTS AND MEASURES AGENCY </h5>


            </div>
            <div class="logo-right">
                <img src="data:image/jpeg;base64,' . getImage('assets/images/wma1.png') . '" alt="">
            </div>
        </div>

    </header>
   <p class="contacts">  Tel: ' . $ship[0]->phone_number . ' Fax: ' . $ship[0]->fax . ' , ' . $ship[0]->postal_address . ',' . $ship[0]->email . '</p>
   <div class="wrapper">
       <table  class="time">
          <tr><td>Vessel Name: ' . $ship[0]->ship_name . '</td> </tr>
          <tr><td>Cargo Name: ' . $ship[0]->cargo . '</td> </tr>
          <tr><td>Port Name: ' . $ship[0]->port . '</td> </tr>
          <tr><td>Terminal Name: ' . $ship[0]->terminal . '</td> </tr>
          <tr><td>Arrival Date: ' . $ship[0]->arrival_date . '</td> </tr>
       </table>

   <h3>ONBOARD INSPECTION</h3>
   </div>
    ';
}

function timeLogHeader($logs)
{
    return '
    <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <title>pdf</title>
    <style>
    *{
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: sans-serif;
    }
    header{
   position: relative;
    }
    .wrapper{
        width: 80%;
        margin: 20px 50px;
        top: 0;

    }
    .logo-left{
      position: absolute;
      left: 50px;
    }
    .logo-right{
      position: absolute;
      right: 50px;
      top: 0;
    }

    .headings{
        text-align: center;
        line-height: 2;
        color: #3a3a3a;
    }

    .main-table{
        width: 100%;
        /* border-spacing: 0;  */
        text-align: left;
        color: #3a3a3a;
        border-collapse: collapse;
    }

    th{
        padding: 12px;
    }

    td{
        padding: 10px;

    }

     tr{
        border-bottom: 1px solid #535353 ;

    }
    .contacts{
        text-align: center;
    }
    .time{
        width: 100%;
        text-align: lef;
    }

    .time td{
        margin:3px 0;
        padding:0;
    }
    .doc-footer{
        width: 100%;
        text-align: center;
    }
    h3{
        text-align: center;
        text-decoration:underline;
    }
    </style>
</head>

<body>
    <header>
      <div class="wrapper">
            <div class="logo-left">
                 <img src="data:image/jpeg;base64,' .  getImage('assets/images/wma1.png'). '" alt="">
            </div>
            <div class="headings">
                <h5><b>THE UNITED REPUBLIC OF TANZANIA</b></h5>
                <h5><b>MINISTRY OF INDUSTRY AND TRADE </b></h5>
                <h5>WEIGHTS AND MEASURES AGENCY </h5>


            </div>
            <div class="logo-right">
                <img src="data:image/jpeg;base64,' .  getImage('assets/images/wma1.png') . '" alt="">
            </div>
        </div>

    </header>

    <p class="contacts">  Tel: ' . $logs[0]->phone_number . ' Fax: ' . $logs[0]->fax . ' , ' . $logs[0]->postal_address . ',' . $logs[0]->email . '</p>
   <div class="wrapper">
       <table  class="time">

              <tr><td><p class="para">Vessel Name: ' . $logs[0]->ship_name . '</p></td></tr>
              <tr><td><p class="para">Cargo Name: ' . $logs[0]->cargo . '</p></td></tr>
              <tr><td><p class="para">Port Name: ' . $logs[0]->port . '</p></td></tr>
              <tr><td><p class="para">Terminal Name: ' . $logs[0]->terminal . '</p></td></tr>
              <tr><td><p class="para">Arrival Date: ' . dateFormatter($logs[0]->arrival_date) . '</p></td></tr>


       </table>

   <h3>TIME LOG</h3>
   </div>
    ';
}
function ullageB4Header($shipName, $arrivalDate, $terminal, $port, $fax, $email, $postalAddress, $tel, $title, $draft, $aftr, $trim, $list, $cargo)
{
    $wma =  getImage('assets/images/wma1.png');
    getImage('assets/images/wma1.png');
    return '
    <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <title>pdf</title>
    <style>
    *{
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: sans-serif;
    }
    body{
        font-size:12px;
    }
    header{
   position: relative;
    }
    .wrapper{
        width: 90%;
        margin: 20px 40px;
        top: 0;

    }
    .logo-left{
      position: absolute;
      left: 50px;
    }
    .logo-right{
      position: absolute;
      right: 50px;
      top: 0;
    }

    .headings{
        text-align: center;
        line-height: 2;

    }

    .main-table{
        width: 100%;
        /* border-spacing: 0;  */
        text-align: left;

        border-collapse: collapse;
    }

    th{
        padding: 6px;

    }

    td{
        padding: 5px;

    }
     tr{
        border-bottom: 1px solid #535353 ;

    }
    .contacts{
        text-align: center;

        line-height:1;
    }
    .time{
        width: 100%;
        text-align: center;
    }
    .doc-footer{
        width: 100%;
        text-align: center;
    }
    .center{
        text-align: center;
        text-decoration:underline;
    }
    .ullageTable {
        width: 70%;
        border-collapse:collapse;
        margin: 0 auto;
      }

      .ullageTable td, .ullageTable th {
        padding: 5px;
      }
      .para{
        font_size:13px;
        line-height:0.5;
        text-align:left;


    }
    </style>
</head>

<body>
    <header>
       <div class="wrapper">
            <div class="logo-left">
                 <img src="data:image/jpeg;base64,' .  getImage('assets/images/wma1.png') . '" alt="">
            </div>
            <div class="headings">
                <h5><b>THE UNITED REPUBLIC OF TANZANIA</b></h5>
                <h5><b>MINISTRY OF INDUSTRY AND TRADE </b></h5>
                <h5>WEIGHTS AND MEASURES AGENCY </h5>


            </div>
            <div class="logo-right">
                <img src="data:image/jpeg;base64,' .  getImage('assets/images/wma1.png') . '" alt="">
            </div>
        </div>

    </header>
    <p class="contacts">  Tel: ' . $tel . ' Fax: ' . $fax . ' , ' . $postalAddress . ',' . $email . '</p>
    <h5 class="center">' . $title . '</h5>
    <div class="wrapper">
    <table  class="time">
    <tr>
        <td><p class = "para">DRAFT: ' . $draft . '</p></td>
        <td><p class = "para">AFTR: ' . $aftr . '</p></td>
        <td><p class = "para">TRIM: ' . round(abs($trim), 2) . '</p></td>
        <td><p class = "para">LIST:' . $list . ' &deg;C </p></td>

    </tr>
    <tr>
        <td><p class="para">Vessel Name: ' . $shipName . '</p></td>
        <td><p class="para">Cargo Name: ' . $cargo . '</p></td>
        <td><p class="para">Arrival Date: ' . dateFormatter($arrivalDate) . '</p></td>

    </tr>

 </table>
 <table  class="time">
          <tr>
              <td class="para"><p>PORT: ' . $port . '</p></td>
              <td class="para"><p>UTI: G1174</p></td>
              <td class="para"><p>BERTH: ' . $terminal . '</p></td>
              <td class="para"><p>IMO: 995646</p></td>
              <td class="para"><p>STARTING TIME: 10:54</p></td>
              <td class="para"><p>FINISHING TIME: 2:10</p></td>


          </tr>
       </table>
    </div>


    ';
}

function documentFooter($firstName, $lastName, $captain)
{
    return '

    <div class="wrapper">
         <table class="doc-footer">
             <tr>
                 <th>CAPTAIN/CHIEF OFFICER </th>
                 <th>WEIGHTS AND MEASURES OFFICER  </th>
                 <tr>
                     <td><b>' . $captain . '</b></td>
                     <td><b>' . $firstName . ' ' . $lastName . '</b></td>
                 </tr>
                 <tr>
                     <td>
                         <p>Signature & Stamp</p>
                         <br>
                         ......................
                     </td>
                     <td><p>Signature & Stamp</p>
                         <br>
                         ......................
                     </td>
                 </tr>
             </tr>

         </table>
     </div>

     </body>

     </html>
    ';
}

function certificateOfQuantityHeader($shipName, $arrivalDate, $terminal, $product, $port, $tel, $fax, $postal, $email)
{
    return '
    <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <title>pdf</title>
    <style>
    *{
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: sans-serif;
    }
    body{
        font-size:13.5px;
    }
    header{
   position: relative;
    }
    .wrapper{
        width: 80%;
        margin: 20px 50px;
        top: 0;

    }
    .logo-left{
      position: absolute;
      left: 50px;
    }
    .logo-right{
      position: absolute;
      right: 50px;
      top: 0;
    }

    .headings{
        text-align: center;
        line-height: 2;

    }

    .main-table{
        width: 100%;
        /* border-spacing: 0;  */
        text-align: left;

        border-collapse: collapse;
    }

    th{
        padding: 12px;
    }

    td{
        padding: 10px;

    }

     tr{
        border-bottom: 1px solid #535353 ;

    }
    .contacts{
        text-align: center;
    }
    .time{
        width: 100%;
        text-align: center;
    }
    .doc-footer{
        width: 100%;
        text-align: center;
    }
    h3{
        text-align: center;
        text-decoration:underline;
    }
    </style>
</head>

<body>
    <header>
       <div class="wrapper">
            <div class="logo-left">
                 <img src="data:image/jpeg;base64,' .  getImage('assets/images/wma1.png') . '" alt="">
            </div>
            <div class="headings">
                <h5><b>THE UNITED REPUBLIC OF TANZANIA</b></h5>
                <h5><b>MINISTRY OF INDUSTRY AND TRADE </b></h5>
                <h5>WEIGHTS AND MEASURES AGENCY </h5>


            </div>
            <div class="logo-right">
                <img src="data:image/jpeg;base64,' .  getImage('assets/images/wma1.png') . '" alt="">
            </div>
        </div>

    </header>
    <p class="contacts">  Tel: ' . $tel . ' Fax: ' . $fax . ', ' . $postal . ', ' . $email . '</p>
   <div class="wrapper">
              <p class="para">Vessel Name: ' . $shipName . '</p><br>
              <p class="para">Port Name: ' . $port . '</p><br>
              <p class="para">Cargo Name: ' . $product . '</p><br>
              <p class="para">Arrival Date: ' . dateFormatter($arrivalDate) . '</p>
   <h3>CERTIFICATE OF QUANTITY</h3>
   <p>The following certificate of quantity of <b>' . $product . '</b> established onboard <b>' . $shipName . '</b>  at <b>' . $terminal . '</b> port of <b>' . $port . '</b> on <b>' . dateFormatter($arrivalDate) . '</b> based on arrival figures as follows:   </p>
   </div>
    ';
}