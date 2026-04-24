<?php
include('../function/function.php');
$conn = connect_aglt();
session_start();
require("../pages/PHPMailer-5.1.0/class.phpmailer.php");
switch ($_POST['post_type']) {

    case 'save_supplierdelivery':
        $conn = connect_aglt();
        $url = "http://45.136.236.233:3000/api/vendor/create";

        $data = [
            "data" => [$_POST['data']]
        ];

        //json_encode($data, JSON_PRETTY_PRINT);
         $jsonData = json_encode($data, JSON_PRETTY_PRINT);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        $response = curl_exec($ch);
        $responseData = json_decode($response, true);

        $sqlGetdate = "SELECT 
		UPPER(FORMAT(GETDATE(),'MMMM dd,yyyy HH:mm')) AS 'GETDATE' ";
        $paramsGetdate = array();
        $queryGetdate = sqlsrv_query($conn, $sqlGetdate, $paramsGetdate);
        $resultGetdate = sqlsrv_fetch_array($queryGetdate, SQLSRV_FETCH_ASSOC);

        $mail = new PHPMailer();
        $mail->CharSet = "utf-8";

        $mail->isSMTP();
        $mail->Host = "mail.aglt.co.th";
        $mail->SMTPAuth = true;
        $mail->Username = "wittawat_it@aglt.co.th";
        $mail->Password = "Wittawat_2532";

        $mail->setFrom("wittawat_it@aglt.co.th", "Mr.Kitz");
        $mail->isHTML(true);
        $mail->Subject = 'PO APPROVAL';

        // โหลด template HTML
        $template = file_get_contents('../pages/email_delivery.php');

        // แปลง JSON เป็น array
        $data_rt = json_decode($response, true);

        // จัดกลุ่มตาม Email
        $grouped = [];
        foreach ($data_rt['data'] as $row) {
            $grouped[$row['Email']][] = $row;
        }

        // ส่งเมลครั้งเดียวต่อ Email
        foreach ($grouped as $email => $rows) {
            // ใช้ชื่อจาก record แรกของ email นั้น
            $toName = $rows[0]['EmpName'];

            // สร้าง Content เป็นแถวของตาราง
            $Content = '';
            $no = 1;
            foreach ($rows as $row) {
                $Content .= '<tr>
            <td style="text-align:center;">' . $no++ . '</td>
            <td>' . $row['PlantNo'] . '</td>
            <td>' . $row['PurOrdNo'] . '</td>
        </tr>';
            }

            // สร้าง body จาก template
            $htmlContent = $template;
            $htmlContent = str_replace('{{ReferentNo}}', mt_rand(100000, 999999), $htmlContent);
            $htmlContent = str_replace('{{To}}', $toName, $htmlContent);
            $htmlContent = str_replace('{{Name}}', 'Mr.Kitz', $htmlContent);
            $htmlContent = str_replace('{{DateTime}}', $resultGetdate['GETDATE'], $htmlContent);
            $htmlContent = str_replace('{{Detail}}', '
        <table style="font-size:18px;width:100%;border-collapse:collapse;" border="1">
            <tr>
                <td style="text-align:center;"><b>No</b></td>
                <td><b>Plant No</b></td>
                <td><b>PO</b></td>
             
            </tr>' . $Content . '
        </table>', $htmlContent);

            // ตั้งค่า body และผู้รับ
            $mail->Body = $htmlContent;
            $mail->clearAddresses(); // ล้าง address เดิมก่อน
            $mail->addAddress($email, $toName);

            // ส่งเมล
            //if (!$mail->send()) {
            //     echo "Error to $email :: " . $mail->ErrorInfo . "<br>";
            // } else {
            //     echo "KMS :: Message sent to $email<br>";
            // }
        }



        curl_close($ch);
        echo $responseData['success'] . '|' . $responseData['message'];

        sqlsrv_close($conn);
        break;
    case 'select_addinstallment':
        $conn = connect_aglt();
        
        
                 $sqlRound = "SELECT MAX(RoundNo)+1 AS 'RoundNo' FROM [dbo].[tbt_DeliveryMaster] 
WHERE PurOrderNo='" . $_POST['PurOrderNo'] . "' ";
$paramsRound = array();
$queryRound = sqlsrv_query($conn, $sqlRound, $paramsRound);
$resultRound = sqlsrv_fetch_array($queryRound, SQLSRV_FETCH_ASSOC);    
$round=($resultRound['RoundNo']=='') ? '1' : $resultRound['RoundNo'];
        ?>
        <div class="modal-header">
            <b>แผนการจัดส่ง</b>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="col-md-3">
                    <label class="form-label">PO Number :</label> <?= $_POST['PurOrderNo'] ?>
                </div>
                <div class="col-md-3">
                    <label class="form-label">สถานะการจัดส่ง :</label> เตรียมจัดส่ง
                </div>
                <div class="col-md-6 text-right">
                    <button type="button" class="btn btn-primary" onclick="add_supplierdelivery()"  >เพิ่มงวด <i class="fa fa-plus"></i></button>
                    <button type="button" class="btn btn-primary" onclick="save_supplierdelivery('<?= $_POST['PurOrderNo'] ?>','<?=$round?>')"  id=btn_sendsupplierdelivery name="btn_sendsupplierdelivery">จัดส่งสินค้า <i class="fa fa-arrow-right"></i></button>
                </div>
            </div>
            <div class="row">

                <div class="col-md-6">
                    <label class="form-label">วันที่จัดส่ง</label>
                    <input type="date" class="form-control" id="txt_deliverydate" name="txt_deliverydate" placeholder="mm/dd/yyyy">
                </div>
                <div class="col-md-6">
                    <label class="form-label">เลขล๊อต</label>
                    <input type="text" class="form-control" id="txt_lot" name="txt_lot">
                     <input type="text" class="form-control" id="txt_roundstart" name="txt_roundstart" value="<?=$round?>" style="display: none">
                </div>
            </div>
            <div class="row">&nbsp;</div>
            <div id="roundContainer">
                <div class="card card-outline card-primary delivery-round">
                    <div class="card-header">
                        <h3 class="card-title">งวดการส่งที่ <?=$round?></h3>
                       
                    </div>
                    <div class="card-body">

                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered" style="background-color: white">
                                    <thead>

                                        <tr>
                                            <th style="text-align: center;">No</th>
                                            <th style="text-align: center;">Item</th>
                                            <th style="text-align: center;">Description</th>
                                            <th style="text-align: center;">Qty</th>
                                            <th style="text-align: center;">Remain Qty</th>
                                            <th style="text-align: center;">Delivery Qty</th>
                                            <th style="text-align: center;">Unit Price</th>
                                            <th style="text-align: center;">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody style="font-size: 14px">
                                        <?php
                                        $i = 1;
                                        $sqlPreparation2 = "SELECT a.ItemNo,b.ItemName,SUM(a.OrderQty) AS 'OrderQty',SUM(NetPrice) AS 'NetPrice',SUM(NetAmount) AS 'NetAmount' 
                                    FROM [dbo].[tbt_PurchOrderDetail] a
                                    INNER JOIN [dbo].[tbm_ItemMaster] b ON a.ItemNo=b.ItemNo
                                    WHERE a.PurOrderNo='" . $_POST['PurOrderNo'] . "' GROUP BY a.ItemNo,b.ItemName
                                    ";
                                        $paramsPreparation2 = array();
                                        $queryPreparation2 = sqlsrv_query($conn, $sqlPreparation2, $paramsPreparation2);
                                        while ($resultPreparation2 = sqlsrv_fetch_array($queryPreparation2, SQLSRV_FETCH_ASSOC)) {
                                            ?>
                                            <tr>
                                                <td style="text-align: center"><?= $i ?></td>
                                                <td style="text-align: left"><?= $resultPreparation2['ItemNo'] ?></td>
                                                <td style="text-align: left"><?= $resultPreparation2['ItemName'] ?></td>
                                                <td style="text-align: right"><?= number_format($resultPreparation2['OrderQty']) ?></td>
                                                <td style="text-align: right"></td>
                                                <td style="text-align: right"><input type="number" class="form-control" name="txt_<?= $resultPreparation2['ItemNo'] ?>deliveryqty" id="txt_<?= $resultPreparation2['ItemNo'] ?>deliveryqty"  max="<?=$resultPreparation2['OrderQty']?>"   style="text-align: center"></td>
                                                <td style="text-align: right"><?= number_format($resultPreparation2['NetPrice'], 4) ?></td>
                                                <td style="text-align: right"><?= number_format($resultPreparation2['NetAmount'], 4) ?></td>
                                            </tr>
                                            <?php
                                            $sumtotal = $sumtotal + $resultPreparation2['NetAmount'];
                                            $i++;
                                        }
                                        ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="6">&nbsp;</td>
                                            <td style="text-align: right">Total Amount</td>
                                            <td style="text-align: right"><?= number_format($sumtotal, 4) ?></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
           
        </div>
        <!--end::Body-->
        <!--begin::Footer-->
        <div class="modal-footer" style="text-align: right">
            <button class="btn btn-danger"  data-dismiss="modal">Close <i class="fa fa-times"></i></button>
        </div>


        <?php
        sqlsrv_close($conn);
        break;

    case 'select_supcompleteddetail':
        $conn = connect_aglt();

        $sqlPreparation1 = "SELECT d.Email,
        CONVERT(NVARCHAR(10),b.PurOrderDate,23) AS 'PurOrderDate',d.VendName,SUM(a.NetAmount) AS 'NetAmount',c.StatName,c.FontColor 
        FROM [dbo].[tbt_PurchOrderDetail] a 
        INNER JOIN [dbo].[tbt_PurchOrderMaster] b ON a.PurOrderNo=b.PurOrderNo 
        INNER JOIN [dbo].[tbm_Status] c ON b.OrderStatus=c.StatNo 
        LEFT JOIN [dbo].[tbm_Vendor] d ON b.VendNo=d.VendNo
        WHERE a.PurOrderNo='" . $_POST['PurOrderNo'] . "' GROUP BY d.Email,b.PurOrderDate,d.VendName,c.StatName,c.FontColor";
        $paramsPreparation1 = array();
        $queryPreparation1 = sqlsrv_query($conn, $sqlPreparation1, $paramsPreparation1);
        $resultPreparation1 = sqlsrv_fetch_array($queryPreparation1, SQLSRV_FETCH_ASSOC);

        $sqlPreparation2 = "SELECT a.ItemNo,b.ItemName,SUM(a.OrderQty) AS 'OrderQty',SUM(NetPrice) AS 'NetPrice',SUM(NetAmount) AS 'NetAmount' 
        FROM [dbo].[tbt_PurchOrderDetail] a
        INNER JOIN [dbo].[tbm_ItemMaster] b ON a.ItemNo=b.ItemNo
        WHERE a.PurOrderNo='" . $_POST['PurOrderNo'] . "' GROUP BY a.ItemNo,b.ItemName
        ";
        $paramsPreparation2 = array();
        $queryPreparation2 = sqlsrv_query($conn, $sqlPreparation2, $paramsPreparation2);
        
        



        ?>

        <!--begin::Header-->
        <div class="modal-header"><b>PO Number : <?= $_POST['PurOrderNo'] ?></b></div>
        <!--end::Header-->
        <!--begin::Form-->
        <!--begin::Body-->
        <div class="modal-body">
            <div class="card card-outline card-primary">

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">Date  : </label><br> <?= $resultPreparation1['PurOrderDate'] ?>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Supplier : </label><br><?= $resultPreparation1['VendName'] ?>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Amount : </label><br> $<?= number_format($resultPreparation1['NetAmount'], 4) ?>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status : </label><br> <font style='color: <?= $resultPreparation1['FontColor'] ?>'><?= $resultPreparation1['StatName'] ?></font>
                        </div>
                    </div>

                </div>
            </div>
            <div class="card card-outline">
                <div class="card-header">
                    <h3 class="card-title">PO Items</h3>
                </div> 
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered bg-light" id="table8">
                                <thead>
                                    <tr>
                                        <th style="text-align: center;">No</th>
                                        <th style="text-align: center;">Item</th>
                                        <th style="text-align: center;">Description</th>
                                        <th style="text-align: center;">Qty</th>
                                        <th style="text-align: center;">Unit Price</th>
                                        <th style="text-align: center;">Total</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 14px">
                                    <?php
                                    $i = 1;
                                    $sumtotal = 0;
                                    while ($resultPreparation2 = sqlsrv_fetch_array($queryPreparation2, SQLSRV_FETCH_ASSOC)) {
                                        ?>
                                        <tr>
                                            <td style="text-align: center"><?= $i ?></td>
                                            <td style="text-align: left"><?= $resultPreparation2['ItemNo'] ?></td>
                                            <td style="text-align: left"><?= $resultPreparation2['ItemName'] ?></td>
                                            <td style="text-align: right"><?= number_format($resultPreparation2['OrderQty']) ?></td>
                                            <td style="text-align: right"><?= number_format($resultPreparation2['NetPrice'], 4) ?></td>
                                            <td style="text-align: right"><?= number_format($resultPreparation2['NetAmount'], 4) ?></td>
                                        </tr>
                                        <?php
                                        $i++;
                                        $sumtotal = $sumtotal + $resultPreparation2['NetAmount'];
                                    }
                                    ?>

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4">&nbsp;</td>
                                        <td style="text-align: right">Total Amount</td>
                                        <td style="text-align: right"><?= number_format($sumtotal, 4) ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Body-->
        <!--begin::Footer-->
        <div class="modal-footer" style="text-align: right">
            <!--<button class="btn btn-primary" onclick="" ><?//= select_language($_POST['lag'], 'L0431') ?> <i class="fa fa-check"></i></button>-->
            <button class="btn btn-danger" onclick="select_suppliercompleted();"  data-dismiss="modal">Close <i class="fa fa-times"></i></button>
        </div>
        <?php
        sqlsrv_close($conn);
        break;

    case 'select_supreceivingdetail':
        $conn = connect_aglt();

        $sqlPreparation1 = "SELECT d.Email,
        CONVERT(NVARCHAR(10),b.PurOrderDate,23) AS 'PurOrderDate',d.VendName,SUM(a.NetAmount) AS 'NetAmount',c.StatName,c.FontColor 
        FROM [dbo].[tbt_PurchOrderDetail] a 
        INNER JOIN [dbo].[tbt_PurchOrderMaster] b ON a.PurOrderNo=b.PurOrderNo 
        INNER JOIN [dbo].[tbm_Status] c ON b.OrderStatus=c.StatNo 
        LEFT JOIN [dbo].[tbm_Vendor] d ON b.VendNo=d.VendNo
        WHERE a.PurOrderNo='" . $_POST['PurOrderNo'] . "' GROUP BY d.Email,b.PurOrderDate,d.VendName,c.StatName,c.FontColor";
        $paramsPreparation1 = array();
        $queryPreparation1 = sqlsrv_query($conn, $sqlPreparation1, $paramsPreparation1);
        $resultPreparation1 = sqlsrv_fetch_array($queryPreparation1, SQLSRV_FETCH_ASSOC);

        $sqlPreparation2 = "SELECT a.ItemNo,b.ItemName,SUM(a.OrderQty) AS 'OrderQty',SUM(NetPrice) AS 'NetPrice',SUM(NetAmount) AS 'NetAmount' 
        FROM [dbo].[tbt_PurchOrderDetail] a
        INNER JOIN [dbo].[tbm_ItemMaster] b ON a.ItemNo=b.ItemNo
        WHERE a.PurOrderNo='" . $_POST['PurOrderNo'] . "' GROUP BY a.ItemNo,b.ItemName
        ";
        $paramsPreparation2 = array();
        $queryPreparation2 = sqlsrv_query($conn, $sqlPreparation2, $paramsPreparation2);
        ?>

        <!--begin::Header-->
        <div class="modal-header"><b>PO Number : <?= $_POST['PurOrderNo'] ?></b></div>
        <!--end::Header-->
        <!--begin::Form-->
        <!--begin::Body-->
        <div class="modal-body">
            <div class="card card-outline card-primary">

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">Date  : </label><br> <?= $resultPreparation1['PurOrderDate'] ?>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Supplier : </label><br><?= $resultPreparation1['VendName'] ?>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Amount : </label><br> $<?= number_format($resultPreparation1['NetAmount'], 4) ?>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status : </label><br> <font style='color: <?= $resultPreparation1['FontColor'] ?>'><?= $resultPreparation1['StatName'] ?></font>
                        </div>
                    </div>

                </div>
            </div>
            <div class="card card-outline">
                <div class="card-header">
                    <h3 class="card-title">PO Items</h3>
                </div> 
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered bg-light" id="table7">
                                <thead>
                                    <tr>
                                        <th style="text-align: center;">No</th>
                                        <th style="text-align: center;">Item</th>
                                        <th style="text-align: center;">Description</th>
                                        <th style="text-align: center;">Qty</th>
                                        <th style="text-align: center;">Unit Price</th>
                                        <th style="text-align: center;">Total</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 14px">
                                    <?php
                                    $i = 1;
                                    $sumtotal = 0;
                                    while ($resultPreparation2 = sqlsrv_fetch_array($queryPreparation2, SQLSRV_FETCH_ASSOC)) {
                                        ?>
                                        <tr>
                                            <td style="text-align: center"><?= $i ?></td>
                                            <td style="text-align: left"><?= $resultPreparation2['ItemNo'] ?></td>
                                            <td style="text-align: left"><?= $resultPreparation2['ItemName'] ?></td>
                                            <td style="text-align: right"><?= number_format($resultPreparation2['OrderQty']) ?></td>
                                            <td style="text-align: right"><?= number_format($resultPreparation2['NetPrice'], 4) ?></td>
                                            <td style="text-align: right"><?= number_format($resultPreparation2['NetAmount'], 4) ?></td>
                                        </tr>
                                        <?php
                                        $i++;
                                        $sumtotal = $sumtotal + $resultPreparation2['NetAmount'];
                                    }
                                    ?>

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4">&nbsp;</td>
                                        <td style="text-align: right">Total Amount</td>
                                        <td style="text-align: right"><?= number_format($sumtotal, 4) ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Body-->
        <!--begin::Footer-->
        <div class="modal-footer" style="text-align: right">
            <!--<button class="btn btn-primary" onclick="" ><?//= select_language($_POST['lag'], 'L0431') ?> <i class="fa fa-check"></i></button>-->
            <button class="btn btn-danger" onclick="select_supplierreceiving()"  data-dismiss="modal">Close <i class="fa fa-times"></i></button>
        </div>
        <?php
        sqlsrv_close($conn);
        break;
    case 'select_supprogressdetail':
        $conn = connect_aglt();

        $sqlPreparation1 = "SELECT d.Email,
        CONVERT(NVARCHAR(10),b.PurOrderDate,23) AS 'PurOrderDate',d.VendName,SUM(a.NetAmount) AS 'NetAmount',c.StatName,c.FontColor 
        FROM [dbo].[tbt_PurchOrderDetail] a 
        INNER JOIN [dbo].[tbt_PurchOrderMaster] b ON a.PurOrderNo=b.PurOrderNo 
        INNER JOIN [dbo].[tbm_Status] c ON b.OrderStatus=c.StatNo 
        LEFT JOIN [dbo].[tbm_Vendor] d ON b.VendNo=d.VendNo
        WHERE a.PurOrderNo='" . $_POST['PurOrderNo'] . "' GROUP BY d.Email,b.PurOrderDate,d.VendName,c.StatName,c.FontColor";
        $paramsPreparation1 = array();
        $queryPreparation1 = sqlsrv_query($conn, $sqlPreparation1, $paramsPreparation1);
        $resultPreparation1 = sqlsrv_fetch_array($queryPreparation1, SQLSRV_FETCH_ASSOC);

        $sqlPreparation2 = "SELECT a.ItemNo,b.ItemName,SUM(a.OrderQty) AS 'OrderQty',SUM(NetPrice) AS 'NetPrice',SUM(NetAmount) AS 'NetAmount' 
        FROM [dbo].[tbt_PurchOrderDetail] a
        INNER JOIN [dbo].[tbm_ItemMaster] b ON a.ItemNo=b.ItemNo
        WHERE a.PurOrderNo='" . $_POST['PurOrderNo'] . "' GROUP BY a.ItemNo,b.ItemName
        ";
        $paramsPreparation2 = array();
        $queryPreparation2 = sqlsrv_query($conn, $sqlPreparation2, $paramsPreparation2);
        
        $sqlRound1 = "SELECT DeliveryNo,CONVERT(NVARCHAR(10),PlanDate,23) AS 'DeliveryDate' FROM [dbo].[tbt_DeliveryMaster] 
        WHERE PurOrderNo='" . $_POST['PurOrderNo'] . "'
        GROUP BY DeliveryNo,CONVERT(NVARCHAR(10),PlanDate,23) ";
         $paramsRound1 = array();
        $queryRound1 = sqlsrv_query($conn, $sqlRound1, $paramsRound1);
        $resultRound1 = sqlsrv_fetch_array($queryRound1, SQLSRV_FETCH_ASSOC);
                                                                                
                                                                            
        ?>

        <!--begin::Header-->
        <div class="modal-header"><b>PO Number : <?= $_POST['PurOrderNo'] ?></b></div>
        <!--end::Header-->
        <!--begin::Form-->
        <!--begin::Body-->
        <div class="modal-body">
            <div class="card card-outline card-primary">

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">Date  : </label><br> <?= $resultPreparation1['PurOrderDate'] ?>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Supplier : </label><br><?= $resultPreparation1['VendName'] ?>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Amount : </label><br> $<?= number_format($resultPreparation1['NetAmount'], 4) ?>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status : </label><br> <font style='color: <?= $resultPreparation1['FontColor'] ?>'><?= $resultPreparation1['StatName'] ?></font>
                        </div>
                    </div>
                    <div class="row">&nbsp;</div>
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">Delivery No  : </label><br> <?= $resultRound1['DeliveryNo'] ?>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Delivery Date : </label><br><?= $resultRound1['DeliveryDate'] ?>
                        </div>
                       
                    </div>

                </div>
            </div>







            <div class="card card-outline">
                <div class="card-header">
                    <ul class="nav nav-pills">
                        <li class="nav-item"><a class="nav-link active" href="#poitems" data-toggle="tab">รายการสินค้า (Items)</a></li>
                        <li class="nav-item"><a class="nav-link" href="#installment" data-toggle="tab">ประวัติการจัดส่ง (Delivery History)</a></li>
                    </ul>
                </div> 
                <div class="card-body">
                    <div class="tab-content">

                        <!-- /.tab-pane -->
                        <div class="active tab-pane" id="poitems">
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-bordered bg-light" id="table6">
                                        <thead>
                                            <tr>
                                                <th style="text-align: center;">No</th>
                                                <th style="text-align: center;">Item</th>
                                                <th style="text-align: center;">Description</th>
                                                <th style="text-align: center;">Qty</th>
                                                <th style="text-align: center;">Qty Delivery</th>
                                                <th style="text-align: center;">Unit Price</th>
                                                <th style="text-align: center;">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody style="font-size: 14px">
                                            <?php
                                            $i = 1;
                                            $sumtotal = 0;
                                            while ($resultPreparation2 = sqlsrv_fetch_array($queryPreparation2, SQLSRV_FETCH_ASSOC)) {
                                                ?>
                                                <tr>
                                                    <td style="text-align: center"><?= $i ?></td>
                                                    <td style="text-align: left"><?= $resultPreparation2['ItemNo'] ?></td>
                                                    <td style="text-align: left"><?= $resultPreparation2['ItemName'] ?></td>
                                                    <td style="text-align: right"><?= number_format($resultPreparation2['OrderQty']) ?></td>
                                                    <td style="text-align: right"><?= number_format($resultPreparation2['OrderQty']) ?></td>
                                                    <td style="text-align: right"><?= number_format($resultPreparation2['NetPrice'], 4) ?></td>
                                                    <td style="text-align: right"><?= number_format($resultPreparation2['NetAmount'], 4) ?></td>
                                                </tr>
                                                <?php
                                                $i++;
                                                $sumtotal = $sumtotal + $resultPreparation2['NetAmount'];
                                            }
                                            ?>

                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="4">&nbsp;</td>
                                                <td style="text-align: right">Total Amount</td>
                                                <td style="text-align: right"><?= number_format($sumtotal, 4) ?></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>



                        </div>
                        <div class="tab-pane" id="installment">
                            <div class="row">
                                <div class="col-12">
                                    <div class="timeline timeline-inverse">
<?php
                                            $sqlRound = "SELECT RoundNo FROM [dbo].[tbt_DeliveryMaster] 
                                            WHERE PurOrderNo='" . $_POST['PurOrderNo'] . "'
                                            GROUP BY RoundNo ORDER BY RoundNo ASC";
                                             $paramsRound = array();
                                                                        $queryRound = sqlsrv_query($conn, $sqlRound, $paramsRound);
                                                                        while ($resultRound = sqlsrv_fetch_array($queryRound, SQLSRV_FETCH_ASSOC))
                                                                        {
                                                    ?>
                                        <div class="time-label">
                                            <span class="bg-info">
                                                งวดที่ <?=$resultRound['RoundNo']?>
                                            </span>
                                        </div>

                                        <div>
                                            
                                            <i class="fas fa-dollar-sign bg-info"></i>
                                            <div class="timeline-item" style="background-color: white;border: 0px solid">
                                                <table class="table table-bordered" style="background-color: white">
                                                    <thead>
                                                        <tr>
                                                            <th style="text-align: center;">No</th>
                                                            <th style="text-align: center;">Item</th>
                                                            <th style="text-align: center;">Description</th>
                                                            <th style="text-align: center;">Delivery Qty</th>
                                                            <th style="text-align: center;">Unit Price</th>
                                                            <th style="text-align: center;">Total</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody style="font-size: 14px">
                                                        <?php
                                                        $i2=1;
                                                           $sqlInstallment = "SELECT a.PurOrderNo,b.[DeliveryNo], b.[DeliveryDate], b.[RoundNo], b.[ItemNo], c.ItemName,SUM(NetPrice) AS 'NetPrice',
                                                                            b.[LotNo], b.[OrderQty], b.[PendingQty], b.[DeliveryQty], b.[ReceiveQty], b.[RemainQty], SUM(NetPrice)*b.[DeliveryQty] AS 'NetAmount',
                                                                            b.[Reference], b.[Remarks], b.[ItemStatus] FROM [dbo].[tbt_DeliveryMaster] a
                                                                            INNER JOIN [dbo].[tbt_DeliveryDetail] b ON a.DeliveryNo=b.DeliveryNo
                                                                           INNER JOIN [dbo].[tbm_ItemMaster] c ON b.ItemNo=c.ItemNo
                                                                            INNER JOIN [dbo].[tbt_PurchOrderDetail] d ON a.PurOrderNo=d.PurOrderNo
                                                                            WHERE a.PurOrderNo='" . $_POST['PurOrderNo'] . "' AND b.[RoundNo]='".$resultRound['RoundNo']."'
                                                                            GROUP BY a.PurOrderNo,b.[DeliveryNo], b.[DeliveryDate], b.[RoundNo], b.[ItemNo], c.ItemName,
                                                                            b.[LotNo], b.[OrderQty], b.[PendingQty], b.[DeliveryQty], b.[ReceiveQty], b.[RemainQty], 
                                                                            b.[Reference], b.[Remarks], b.[ItemStatus]
                                                                        ";
                                                                        $paramsInstallment = array();
                                                                        $queryInstallment = sqlsrv_query($conn, $sqlInstallment, $paramsInstallment);
                                                                        while ($resultInstallment = sqlsrv_fetch_array($queryInstallment, SQLSRV_FETCH_ASSOC))
                                                                        {
                                                                        ?>
                                                                                                                        <tr>
                                                            <td style="text-align: center"><?=$i2?></td>
                                                            <td style="text-align: left"><?=$resultInstallment['ItemNo']?></td>
                                                            <td style="text-align: left"><?=$resultInstallment['ItemName']?></td>
                                                            <td style="text-align: right"><?=number_format($resultInstallment['DeliveryQty'])?></td>
                                                            <td style="text-align: right"><?= number_format($resultInstallment['NetPrice'],4)?></td>
                                                            <td style="text-align: right"><?= number_format($resultInstallment['NetAmount'],4)?></td>
                                                        </tr>
                                                        <?php
                                                        $i2++;
                                                        $netamount=$netamount+$resultInstallment['NetAmount'];
                                                                        }
                                                                        ?>

                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <td colspan="4">&nbsp;</td>
                                                            <td style="text-align: right">Total Amount</td>
                                                            <td style="text-align: right"><?=number_format($netamount,4)?></td>
                                                        </tr>
                                                    </tfoot>
                                                </table>

                                            </div>
                                        </div>
                                    

                                        <?php
                                                                        }
                                                                        ?>


                                    </div>

                                </div><!-- /.col -->
                            </div>
                        </div>

                        <!-- /.tab-pane -->
                    </div>

                </div>
            </div>
        </div>
        <!--end::Body-->
        <!--begin::Footer-->
        <div class="modal-footer" style="text-align: right">
            <button class="btn btn-primary" data-toggle="modal" data-backdrop="static" data-target="#modal_addinstallment" onclick="select_addinstallment('<?= $_POST['PurOrderNo'] ?>')" >แผนการจัดส่ง <i class="fa fa-truck"></i></button>
            <button class="btn btn-danger" onclick="select_supplierinprogress()"  data-dismiss="modal">Close <i class="fa fa-times"></i></button>
        </div>
        <?php
        sqlsrv_close($conn);
        break;
    case 'select_supactionreqdetail':
        $conn = connect_aglt();

        $sqlPreparation1 = "SELECT d.Email,
        CONVERT(NVARCHAR(10),b.PurOrderDate,23) AS 'PurOrderDate',d.VendName,SUM(a.NetAmount) AS 'NetAmount',c.StatName,c.FontColor 
        FROM [dbo].[tbt_PurchOrderDetail] a 
        INNER JOIN [dbo].[tbt_PurchOrderMaster] b ON a.PurOrderNo=b.PurOrderNo 
        INNER JOIN [dbo].[tbm_Status] c ON b.OrderStatus=c.StatNo 
        LEFT JOIN [dbo].[tbm_Vendor] d ON b.VendNo=d.VendNo
        WHERE a.PurOrderNo='" . $_POST['PurOrderNo'] . "' GROUP BY d.Email,b.PurOrderDate,d.VendName,c.StatName,c.FontColor";
        $paramsPreparation1 = array();
        $queryPreparation1 = sqlsrv_query($conn, $sqlPreparation1, $paramsPreparation1);
        $resultPreparation1 = sqlsrv_fetch_array($queryPreparation1, SQLSRV_FETCH_ASSOC);

        $sqlPreparation2 = "SELECT a.ItemNo,b.ItemName,SUM(a.OrderQty) AS 'OrderQty',SUM(NetPrice) AS 'NetPrice',SUM(NetAmount) AS 'NetAmount' 
        FROM [dbo].[tbt_PurchOrderDetail] a
        INNER JOIN [dbo].[tbm_ItemMaster] b ON a.ItemNo=b.ItemNo
        WHERE a.PurOrderNo='" . $_POST['PurOrderNo'] . "' GROUP BY a.ItemNo,b.ItemName
        ";
        $paramsPreparation2 = array();
        $queryPreparation2 = sqlsrv_query($conn, $sqlPreparation2, $paramsPreparation2);
        ?>

        <!--begin::Header-->
        <div class="modal-header"><b>PO Number : <?= $_POST['PurOrderNo'] ?></b></div>
        <!--end::Header-->
        <!--begin::Form-->
        <!--begin::Body-->
        <div class="modal-body">
            <div class="card card-outline card-primary">

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">Date  : </label><br> <?= $resultPreparation1['PurOrderDate'] ?>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Supplier : </label><br><?= $resultPreparation1['VendName'] ?>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Amount : </label><br> $<?= number_format($resultPreparation1['NetAmount'], 4) ?>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status : </label><br> <font style='color: <?= $resultPreparation1['FontColor'] ?>'><?= $resultPreparation1['StatName'] ?></font>
                        </div>
                    </div>

                </div>
            </div>
            <div class="card card-outline">
                <div class="card-header">
                    <h3 class="card-title">PO Items</h3>
                </div> 
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered bg-light" id="table5">
                                <thead>
                                    <tr>
                                        <th style="text-align: center;">No</th>
                                        <th style="text-align: center;">Item</th>
                                        <th style="text-align: center;">Description</th>
                                        <th style="text-align: center;">Qty</th>
                                        <th style="text-align: center;">Unit Price</th>
                                        <th style="text-align: center;">Total</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 14px">
                                    <?php
                                    $i = 1;
                                    $sumtotal = 0;
                                    while ($resultPreparation2 = sqlsrv_fetch_array($queryPreparation2, SQLSRV_FETCH_ASSOC)) {
                                        ?>
                                        <tr>
                                            <td style="text-align: center"><?= $i ?></td>
                                            <td style="text-align: left"><?= $resultPreparation2['ItemNo'] ?></td>
                                            <td style="text-align: left"><?= $resultPreparation2['ItemName'] ?></td>
                                            <td style="text-align: right"><?= number_format($resultPreparation2['OrderQty']) ?></td>
                                            <td style="text-align: right"><?= number_format($resultPreparation2['NetPrice'], 4) ?></td>
                                            <td style="text-align: right"><?= number_format($resultPreparation2['NetAmount'], 4) ?></td>
                                        </tr>
                                        <?php
                                        $i++;
                                        $sumtotal = $sumtotal + $resultPreparation2['NetAmount'];
                                    }
                                    ?>

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4">&nbsp;</td>
                                        <td style="text-align: right">Total Amount</td>
                                        <td style="text-align: right"><?= number_format($sumtotal, 4) ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Body-->
        <!--begin::Footer-->
        <div class="modal-footer" style="text-align: right">
            <!--<button class="btn btn-primary" onclick="" ><?//= select_language($_POST['lag'], 'L0431') ?> <i class="fa fa-check"></i></button>-->
            <button class="btn btn-danger" onclick="select_supplieractionreg()"  data-dismiss="modal">Close <i class="fa fa-times"></i></button>
        </div>
        <?php
        sqlsrv_close($conn);
        break;
    case 'select_completeddetail':
        $conn = connect_aglt();

        $sqlPreparation1 = "SELECT d.Email,
        CONVERT(NVARCHAR(10),b.PurOrderDate,23) AS 'PurOrderDate',d.VendName,SUM(a.NetAmount) AS 'NetAmount',c.StatName,c.FontColor 
        FROM [dbo].[tbt_PurchOrderDetail] a 
        INNER JOIN [dbo].[tbt_PurchOrderMaster] b ON a.PurOrderNo=b.PurOrderNo 
        INNER JOIN [dbo].[tbm_Status] c ON b.OrderStatus=c.StatNo 
        LEFT JOIN [dbo].[tbm_Vendor] d ON b.VendNo=d.VendNo
        WHERE a.PurOrderNo='" . $_POST['PurOrderNo'] . "' GROUP BY d.Email,b.PurOrderDate,d.VendName,c.StatName,c.FontColor";
        $paramsPreparation1 = array();
        $queryPreparation1 = sqlsrv_query($conn, $sqlPreparation1, $paramsPreparation1);
        $resultPreparation1 = sqlsrv_fetch_array($queryPreparation1, SQLSRV_FETCH_ASSOC);

        $sqlPreparation2 = "SELECT a.ItemNo,b.ItemName,SUM(a.OrderQty) AS 'OrderQty',SUM(NetPrice) AS 'NetPrice',SUM(NetAmount) AS 'NetAmount' 
        FROM [dbo].[tbt_PurchOrderDetail] a
        INNER JOIN [dbo].[tbm_ItemMaster] b ON a.ItemNo=b.ItemNo
        WHERE a.PurOrderNo='" . $_POST['PurOrderNo'] . "' GROUP BY a.ItemNo,b.ItemName
        ";
        $paramsPreparation2 = array();
        $queryPreparation2 = sqlsrv_query($conn, $sqlPreparation2, $paramsPreparation2);
        ?>

        <!--begin::Header-->
        <div class="modal-header"><b>PO Number : <?= $_POST['PurOrderNo'] ?></b></div>
        <!--end::Header-->
        <!--begin::Form-->
        <!--begin::Body-->
        <div class="modal-body">
            <div class="card card-outline card-primary">

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">Date  : </label><br> <?= $resultPreparation1['PurOrderDate'] ?>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Supplier : </label><br><?= $resultPreparation1['VendName'] ?>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Amount : </label><br> $<?= number_format($resultPreparation1['NetAmount'], 4) ?>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status : </label><br> <font style='color: <?= $resultPreparation1['FontColor'] ?>'><?= $resultPreparation1['StatName'] ?></font>
                        </div>
                    </div>

                </div>
            </div>
            <div class="card card-outline">
                <div class="card-header">
                    <h3 class="card-title">PO Items</h3>
                </div> 
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered bg-light" id="table4">
                                <thead>
                                    <tr>
                                        <th style="text-align: center;">No</th>
                                        <th style="text-align: center;">Item</th>
                                        <th style="text-align: center;">Description</th>
                                        <th style="text-align: center;">Qty</th>
                                        <th style="text-align: center;">Unit Price</th>
                                        <th style="text-align: center;">Total</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 14px">
                                    <?php
                                    $i = 1;
                                    $sumtotal = 0;
                                    while ($resultPreparation2 = sqlsrv_fetch_array($queryPreparation2, SQLSRV_FETCH_ASSOC)) {
                                        ?>
                                        <tr>
                                            <td style="text-align: center"><?= $i ?></td>
                                            <td style="text-align: left"><?= $resultPreparation2['ItemNo'] ?></td>
                                            <td style="text-align: left"><?= $resultPreparation2['ItemName'] ?></td>
                                            <td style="text-align: right"><?= number_format($resultPreparation2['OrderQty']) ?></td>
                                            <td style="text-align: right"><?= number_format($resultPreparation2['NetPrice'], 4) ?></td>
                                            <td style="text-align: right"><?= number_format($resultPreparation2['NetAmount'], 4) ?></td>
                                        </tr>
                                        <?php
                                        $i++;
                                        $sumtotal = $sumtotal + $resultPreparation2['NetAmount'];
                                    }
                                    ?>

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4">&nbsp;</td>
                                        <td style="text-align: right">Total Amount</td>
                                        <td style="text-align: right"><?= number_format($sumtotal, 4) ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Body-->
        <!--begin::Footer-->
        <div class="modal-footer" style="text-align: right">
            <!--<button class="btn btn-primary" onclick="" ><?//= select_language($_POST['lag'], 'L0431') ?> <i class="fa fa-check"></i></button>-->
            <button class="btn btn-danger" onclick="select_approvalcompleted()"  data-dismiss="modal">Close <i class="fa fa-times"></i></button>
        </div>
        <?php
        sqlsrv_close($conn);
        break;
    case 'select_progressdetail':
        $conn = connect_aglt();

        $sqlPreparation1 = "SELECT d.Email,
        CONVERT(NVARCHAR(10),b.PurOrderDate,23) AS 'PurOrderDate',d.VendName,SUM(a.NetAmount) AS 'NetAmount',c.StatName,c.FontColor 
        FROM [dbo].[tbt_PurchOrderDetail] a 
        INNER JOIN [dbo].[tbt_PurchOrderMaster] b ON a.PurOrderNo=b.PurOrderNo 
        INNER JOIN [dbo].[tbm_Status] c ON b.OrderStatus=c.StatNo 
        LEFT JOIN [dbo].[tbm_Vendor] d ON b.VendNo=d.VendNo
        WHERE a.PurOrderNo='" . $_POST['PurOrderNo'] . "' GROUP BY d.Email,b.PurOrderDate,d.VendName,c.StatName,c.FontColor";
        $paramsPreparation1 = array();
        $queryPreparation1 = sqlsrv_query($conn, $sqlPreparation1, $paramsPreparation1);
        $resultPreparation1 = sqlsrv_fetch_array($queryPreparation1, SQLSRV_FETCH_ASSOC);

        $sqlPreparation2 = "SELECT a.ItemNo,b.ItemName,SUM(a.OrderQty) AS 'OrderQty',SUM(NetPrice) AS 'NetPrice',SUM(NetAmount) AS 'NetAmount' 
        FROM [dbo].[tbt_PurchOrderDetail] a
        INNER JOIN [dbo].[tbm_ItemMaster] b ON a.ItemNo=b.ItemNo
        WHERE a.PurOrderNo='" . $_POST['PurOrderNo'] . "' GROUP BY a.ItemNo,b.ItemName
        ";
        $paramsPreparation2 = array();
        $queryPreparation2 = sqlsrv_query($conn, $sqlPreparation2, $paramsPreparation2);
        ?>

        <!--begin::Header-->
        <div class="modal-header"><b>PO Number : <?= $_POST['PurOrderNo'] ?></b></div>
        <!--end::Header-->
        <!--begin::Form-->
        <!--begin::Body-->
        <div class="modal-body">
            <div class="card card-outline card-primary">

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">Date  : </label><br> <?= $resultPreparation1['PurOrderDate'] ?>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Supplier : </label><br><?= $resultPreparation1['VendName'] ?>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Amount : </label><br> $<?= number_format($resultPreparation1['NetAmount'], 4) ?>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status : </label><br> <font style='color: <?= $resultPreparation1['FontColor'] ?>'><?= $resultPreparation1['StatName'] ?></font>
                        </div>
                    </div>

                </div>
            </div>
            <div class="card card-outline">
                <div class="card-header">
                    <h3 class="card-title">PO Items</h3>
                </div> 
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered bg-light" id="table3">
                                <thead>
                                    <tr>
                                        <th style="text-align: center;">No</th>
                                        <th style="text-align: center;">Item</th>
                                        <th style="text-align: center;">Description</th>
                                        <th style="text-align: center;">Qty</th>
                                        <th style="text-align: center;">Unit Price</th>
                                        <th style="text-align: center;">Total</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 14px">
                                    <?php
                                    $i = 1;
                                    $sumtotal = 0;
                                    while ($resultPreparation2 = sqlsrv_fetch_array($queryPreparation2, SQLSRV_FETCH_ASSOC)) {
                                        ?>
                                        <tr>
                                            <td style="text-align: center"><?= $i ?></td>
                                            <td style="text-align: left"><?= $resultPreparation2['ItemNo'] ?></td>
                                            <td style="text-align: left"><?= $resultPreparation2['ItemName'] ?></td>
                                            <td style="text-align: right"><?= number_format($resultPreparation2['OrderQty']) ?></td>
                                            <td style="text-align: right"><?= number_format($resultPreparation2['NetPrice'], 4) ?></td>
                                            <td style="text-align: right"><?= number_format($resultPreparation2['NetAmount'], 4) ?></td>
                                        </tr>
                                        <?php
                                        $i++;
                                        $sumtotal = $sumtotal + $resultPreparation2['NetAmount'];
                                    }
                                    ?>

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4">&nbsp;</td>
                                        <td style="text-align: right">Total Amount</td>
                                        <td style="text-align: right"><?= number_format($sumtotal, 4) ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Body-->
        <!--begin::Footer-->
        <div class="modal-footer" style="text-align: right">
            <!--<button class="btn btn-primary" onclick="" ><?//= select_language($_POST['lag'], 'L0431') ?> <i class="fa fa-check"></i></button>-->
            <button class="btn btn-danger" onclick="select_approvalpending();"  data-dismiss="modal">Close <i class="fa fa-times"></i></button>
        </div>
        <?php
        sqlsrv_close($conn);
        break;
    case 'select_reworkdetail':
        $conn = connect_aglt();

        $sqlPreparation1 = "SELECT d.Email,
        CONVERT(NVARCHAR(10),b.PurOrderDate,23) AS 'PurOrderDate',d.VendName,SUM(a.NetAmount) AS 'NetAmount',c.StatName,c.FontColor 
        FROM [dbo].[tbt_PurchOrderDetail] a 
        INNER JOIN [dbo].[tbt_PurchOrderMaster] b ON a.PurOrderNo=b.PurOrderNo 
        INNER JOIN [dbo].[tbm_Status] c ON b.OrderStatus=c.StatNo 
        LEFT JOIN [dbo].[tbm_Vendor] d ON b.VendNo=d.VendNo
        WHERE a.PurOrderNo='" . $_POST['PurOrderNo'] . "' GROUP BY d.Email,b.PurOrderDate,d.VendName,c.StatName,c.FontColor";
        $paramsPreparation1 = array();
        $queryPreparation1 = sqlsrv_query($conn, $sqlPreparation1, $paramsPreparation1);
        $resultPreparation1 = sqlsrv_fetch_array($queryPreparation1, SQLSRV_FETCH_ASSOC);

        $sqlPreparation2 = "SELECT a.ItemNo,b.ItemName,SUM(a.OrderQty) AS 'OrderQty',SUM(NetPrice) AS 'NetPrice',SUM(NetAmount) AS 'NetAmount' 
        FROM [dbo].[tbt_PurchOrderDetail] a
        INNER JOIN [dbo].[tbm_ItemMaster] b ON a.ItemNo=b.ItemNo
        WHERE a.PurOrderNo='" . $_POST['PurOrderNo'] . "' GROUP BY a.ItemNo,b.ItemName
        ";
        $paramsPreparation2 = array();
        $queryPreparation2 = sqlsrv_query($conn, $sqlPreparation2, $paramsPreparation2);
        ?>

        <!--begin::Header-->
        <div class="modal-header"><b><?= select_language($_POST['lag'], 'L0291'); ?> : <?= $_POST['PurOrderNo'] ?></b></div>
        <!--end::Header-->
        <!--begin::Form-->
        <!--begin::Body-->
        <div class="modal-body">
            <div class="card card-outline card-primary">

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label"><?= select_language($_POST['lag'], 'L0240'); ?> : </label><br> <?= $resultPreparation1['PurOrderDate'] ?>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"><?= select_language($_POST['lag'], 'L0052'); ?> : </label><br><?= $resultPreparation1['VendName'] ?>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"><?= select_language($_POST['lag'], 'L0310'); ?> : </label><br> $<?= number_format($resultPreparation1['NetAmount'], 4) ?>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"><?= select_language($_POST['lag'], 'L0156'); ?> : </label><br> <font style='color: <?= $resultPreparation1['FontColor'] ?>'><?= $resultPreparation1['StatName'] ?></font>
                        </div>
                    </div>

                </div>
            </div>
            <div class="card card-outline ">
                <div class="card-header">
                    <h3 class="card-title"><?= select_language($_POST['lag'], 'L0433'); ?></h3>
                </div> 
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered bg-light" id="table4">
                                <thead>
                                    <tr>
                                        <th style="text-align: center;"><?= select_language($_POST['lag'], 'L0039') ?></th>
                                        <th style="text-align: center;"><?= select_language($_POST['lag'], 'L0277') ?></th>
                                        <th style="text-align: center;"><?= select_language($_POST['lag'], 'L0268') ?></th>
                                        <th style="text-align: center;"><?= select_language($_POST['lag'], 'L0233') ?></th>
                                        <th style="text-align: center;"><?= select_language($_POST['lag'], 'L0278') ?></th>
                                        <th style="text-align: center;"><?= select_language($_POST['lag'], 'L0297') ?></th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 14px">
                                    <?php
                                    $i = 1;
                                    $sumtotal = 0;
                                    while ($resultPreparation2 = sqlsrv_fetch_array($queryPreparation2, SQLSRV_FETCH_ASSOC)) {
                                        ?>
                                        <tr>
                                            <td style="text-align: center"><?= $i ?></td>
                                            <td style="text-align: left"><?= $resultPreparation2['ItemNo'] ?></td>
                                            <td style="text-align: left"><?= $resultPreparation2['ItemName'] ?></td>
                                            <td style="text-align: right"><?= number_format($resultPreparation2['OrderQty']) ?></td>
                                            <td style="text-align: right"><?= number_format($resultPreparation2['NetPrice'], 4) ?></td>
                                            <td style="text-align: right"><?= number_format($resultPreparation2['NetAmount'], 4) ?></td>
                                        </tr>
                                        <?php
                                        $i++;
                                        $sumtotal = $sumtotal + $resultPreparation2['NetAmount'];
                                    }
                                    ?>

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4">&nbsp;</td>
                                        <td style="text-align: right"><?= select_language($_POST['lag'], 'L0440') ?></td>
                                        <td style="text-align: right"><?= number_format($sumtotal, 4) ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>




        </div>
        <!--end::Body-->
        <!--begin::Footer-->
        <div class="modal-footer" style="text-align: right">
            <!--<button class="btn btn-primary" onclick="" ><?//= select_language($_POST['lag'], 'L0431') ?> <i class="fa fa-check"></i></button>-->
            <button class="btn btn-danger" onclick="select_total('', '', '', '');"  data-dismiss="modal"><?= select_language($_POST['lag'], 'L0023') ?> <i class="fa fa-times"></i></button>
        </div>
        <?php
        sqlsrv_close($conn);
        break;
    case 'select_totaldetail':
        $conn = connect_aglt();

        $sqlPreparation1 = "SELECT d.Email,
        CONVERT(NVARCHAR(10),b.PurOrderDate,23) AS 'PurOrderDate',d.VendName,SUM(a.NetAmount) AS 'NetAmount',c.StatName,c.FontColor 
        FROM [dbo].[tbt_PurchOrderDetail] a 
        INNER JOIN [dbo].[tbt_PurchOrderMaster] b ON a.PurOrderNo=b.PurOrderNo 
        INNER JOIN [dbo].[tbm_Status] c ON b.OrderStatus=c.StatNo 
        LEFT JOIN [dbo].[tbm_Vendor] d ON b.VendNo=d.VendNo
        WHERE a.PurOrderNo='" . $_POST['PurOrderNo'] . "' GROUP BY d.Email,b.PurOrderDate,d.VendName,c.StatName,c.FontColor";
        $paramsPreparation1 = array();
        $queryPreparation1 = sqlsrv_query($conn, $sqlPreparation1, $paramsPreparation1);
        $resultPreparation1 = sqlsrv_fetch_array($queryPreparation1, SQLSRV_FETCH_ASSOC);

        $sqlPreparation2 = "SELECT a.ItemNo,b.ItemName,SUM(a.OrderQty) AS 'OrderQty',SUM(NetPrice) AS 'NetPrice',SUM(NetAmount) AS 'NetAmount' 
        FROM [dbo].[tbt_PurchOrderDetail] a
        INNER JOIN [dbo].[tbm_ItemMaster] b ON a.ItemNo=b.ItemNo
        WHERE a.PurOrderNo='" . $_POST['PurOrderNo'] . "' GROUP BY a.ItemNo,b.ItemName
        ";
        $paramsPreparation2 = array();
        $queryPreparation2 = sqlsrv_query($conn, $sqlPreparation2, $paramsPreparation2);
        ?>

        <!--begin::Header-->
        <div class="modal-header"><b><?= select_language($_POST['lag'], 'L0291'); ?> : <?= $_POST['PurOrderNo'] ?></b></div>
        <!--end::Header-->
        <!--begin::Form-->
        <!--begin::Body-->
        <div class="modal-body">
            <div class="card card-outline card-primary">

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label"><?= select_language($_POST['lag'], 'L0240'); ?> : </label><br> <?= $resultPreparation1['PurOrderDate'] ?>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"><?= select_language($_POST['lag'], 'L0052'); ?> : </label><br><?= $resultPreparation1['VendName'] ?>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"><?= select_language($_POST['lag'], 'L0310'); ?> : </label><br> $<?= number_format($resultPreparation1['NetAmount'], 4) ?>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"><?= select_language($_POST['lag'], 'L0156'); ?> : </label><br> <font style='color: <?= $resultPreparation1['FontColor'] ?>'><?= $resultPreparation1['StatName'] ?></font>
                        </div>
                    </div>

                </div>
            </div>
            <div class="card card-outline">
                <div class="card-header">
                    <h3 class="card-title"><?= select_language($_POST['lag'], 'L0433'); ?></h3>
                </div> 
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered bg-light" id="table3">
                                <thead>
                                    <tr>
                                        <th style="text-align: center;"><?= select_language($_POST['lag'], 'L0039') ?></th>
                                        <th style="text-align: center;"><?= select_language($_POST['lag'], 'L0277') ?></th>
                                        <th style="text-align: center;"><?= select_language($_POST['lag'], 'L0268') ?></th>
                                        <th style="text-align: center;"><?= select_language($_POST['lag'], 'L0233') ?></th>
                                        <th style="text-align: center;"><?= select_language($_POST['lag'], 'L0278') ?></th>
                                        <th style="text-align: center;"><?= select_language($_POST['lag'], 'L0297') ?></th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 14px">
                                    <?php
                                    $i = 1;
                                    $sumtotal = 0;
                                    while ($resultPreparation2 = sqlsrv_fetch_array($queryPreparation2, SQLSRV_FETCH_ASSOC)) {
                                        ?>
                                        <tr>
                                            <td style="text-align: center"><?= $i ?></td>
                                            <td style="text-align: left"><?= $resultPreparation2['ItemNo'] ?></td>
                                            <td style="text-align: left"><?= $resultPreparation2['ItemName'] ?></td>
                                            <td style="text-align: right"><?= number_format($resultPreparation2['OrderQty']) ?></td>
                                            <td style="text-align: right"><?= number_format($resultPreparation2['NetPrice'], 4) ?></td>
                                            <td style="text-align: right"><?= number_format($resultPreparation2['NetAmount'], 4) ?></td>
                                        </tr>
                                        <?php
                                        $i++;
                                        $sumtotal = $sumtotal + $resultPreparation2['NetAmount'];
                                    }
                                    ?>

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4">&nbsp;</td>
                                        <td style="text-align: right"><?= select_language($_POST['lag'], 'L0440') ?></td>
                                        <td style="text-align: right"><?= number_format($sumtotal, 4) ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>




        </div>
        <!--end::Body-->
        <!--begin::Footer-->
        <div class="modal-footer" style="text-align: right">
            <!--<button class="btn btn-primary" onclick="" ><?//= select_language($_POST['lag'], 'L0431') ?> <i class="fa fa-check"></i></button>-->
            <button class="btn btn-danger" onclick="select_total('', '', '', '');"  data-dismiss="modal"><?= select_language($_POST['lag'], 'L0023') ?> <i class="fa fa-times"></i></button>
        </div>
        <?php
        sqlsrv_close($conn);
        break;
    case 'select_sendmaildetail':
        $conn = connect_aglt();

        $sqlPreparation1 = "SELECT d.Email,
        CONVERT(NVARCHAR(10),b.PurOrderDate,23) AS 'PurOrderDate',d.VendName,SUM(a.NetAmount) AS 'NetAmount',c.StatName,c.FontColor 
        FROM [dbo].[tbt_PurchOrderDetail] a 
        INNER JOIN [dbo].[tbt_PurchOrderMaster] b ON a.PurOrderNo=b.PurOrderNo 
        INNER JOIN [dbo].[tbm_Status] c ON b.OrderStatus=c.StatNo 
        LEFT JOIN [dbo].[tbm_Vendor] d ON b.VendNo=d.VendNo
        WHERE a.PurOrderNo='" . $_POST['PurOrderNo'] . "' GROUP BY d.Email,b.PurOrderDate,d.VendName,c.StatName,c.FontColor";
        $paramsPreparation1 = array();
        $queryPreparation1 = sqlsrv_query($conn, $sqlPreparation1, $paramsPreparation1);
        $resultPreparation1 = sqlsrv_fetch_array($queryPreparation1, SQLSRV_FETCH_ASSOC);

        $sqlPreparation2 = "SELECT a.ItemNo,b.ItemName,SUM(a.OrderQty) AS 'OrderQty',SUM(NetPrice) AS 'NetPrice',SUM(NetAmount) AS 'NetAmount' 
        FROM [dbo].[tbt_PurchOrderDetail] a
        INNER JOIN [dbo].[tbm_ItemMaster] b ON a.ItemNo=b.ItemNo
        WHERE a.PurOrderNo='" . $_POST['PurOrderNo'] . "' GROUP BY a.ItemNo,b.ItemName
        ";
        $paramsPreparation2 = array();
        $queryPreparation2 = sqlsrv_query($conn, $sqlPreparation2, $paramsPreparation2);
        ?>

        <!--begin::Header-->
        <div class="modal-header"><b><?= select_language($_POST['lag'], 'L0291'); ?> : <?= $_POST['PurOrderNo'] ?></b></div>
        <!--end::Header-->
        <!--begin::Form-->
        <!--begin::Body-->
        <div class="modal-body">
            <div class="card card-outline card-primary">

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label"><?= select_language($_POST['lag'], 'L0240'); ?> : </label><br> <?= $resultPreparation1['PurOrderDate'] ?>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"><?= select_language($_POST['lag'], 'L0052'); ?> : </label><br><?= $resultPreparation1['VendName'] ?>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"><?= select_language($_POST['lag'], 'L0310'); ?> : </label><br> $<?= number_format($resultPreparation1['NetAmount'], 4) ?>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"><?= select_language($_POST['lag'], 'L0156'); ?> : </label><br> <font style='color: <?= $resultPreparation1['FontColor'] ?>'><?= $resultPreparation1['StatName'] ?></font>
                        </div>
                    </div>

                </div>
            </div>
            <div class="card card-outline">
                <div class="card-header">
                    <h3 class="card-title"><?= select_language($_POST['lag'], 'L0433'); ?></h3>
                </div> 
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered bg-light" id="table4">
                                <thead>
                                    <tr>
                                        <th style="text-align: center;"><?= select_language($_POST['lag'], 'L0039') ?></th>
                                        <th style="text-align: center;"><?= select_language($_POST['lag'], 'L0277') ?></th>
                                        <th style="text-align: center;"><?= select_language($_POST['lag'], 'L0268') ?></th>
                                        <th style="text-align: center;"><?= select_language($_POST['lag'], 'L0233') ?></th>
                                        <th style="text-align: center;"><?= select_language($_POST['lag'], 'L0278') ?></th>
                                        <th style="text-align: center;"><?= select_language($_POST['lag'], 'L0297') ?></th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 14px">
                                    <?php
                                    $i = 1;
                                    $sumtotal = 0;
                                    while ($resultPreparation2 = sqlsrv_fetch_array($queryPreparation2, SQLSRV_FETCH_ASSOC)) {
                                        ?>
                                        <tr>
                                            <td style="text-align: center"><?= $i ?></td>
                                            <td style="text-align: left"><?= $resultPreparation2['ItemNo'] ?></td>
                                            <td style="text-align: left"><?= $resultPreparation2['ItemName'] ?></td>
                                            <td style="text-align: right"><?= number_format($resultPreparation2['OrderQty']) ?></td>
                                            <td style="text-align: right"><?= number_format($resultPreparation2['NetPrice'], 4) ?></td>
                                            <td style="text-align: right"><?= number_format($resultPreparation2['NetAmount'], 4) ?></td>
                                        </tr>
                                        <?php
                                        $i++;
                                        $sumtotal = $sumtotal + $resultPreparation2['NetAmount'];
                                    }
                                    ?>

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4">&nbsp;</td>
                                        <td style="text-align: right"><?= select_language($_POST['lag'], 'L0440') ?></td>
                                        <td style="text-align: right"><?= number_format($sumtotal, 4) ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>




        </div>
        <!--end::Body-->
        <!--begin::Footer-->
        <div class="modal-footer" style="text-align: right">
            <!--<button class="btn btn-primary" onclick="" ><?//= select_language($_POST['lag'], 'L0431') ?> <i class="fa fa-check"></i></button>-->
            <button class="btn btn-danger" onclick="select_sendmail('', '', '', '');"  data-dismiss="modal"><?= select_language($_POST['lag'], 'L0023') ?> <i class="fa fa-times"></i></button>
        </div>
        <?php
        sqlsrv_close($conn);
        break;
    case 'select_total':
        $conn = connect_aglt();
        $condition = '';
        if ($_POST['datefrom'] != '' && $_POST['dateto'] != '') {
            $condition = $condition . " AND CONVERT(NVARCHAR(10),pd.[DeliveryDate],121) BETWEEN CONVERT(NVARCHAR(10),'" . $_POST['datefrom'] . "',121) AND CONVERT(NVARCHAR(10),'" . $_POST['dateto'] . "',121)";
        }
        if ($_POST['ponumber'] != '') {
            $condition = $condition . " AND pm.PurOrderNo = '" . $_POST['ponumber'] . "' ";
        }
        if ($_POST['vendor'] != '') {
            $condition = $condition . " AND pm.VendNo = '" . $_POST['vendor'] . "' ";
        }
        //echo $condition;
        $url2 = "http://45.136.236.233:3000/api/po/poapprove";
        $data2 = [
            "flag" => "se_purchaseorder",
            "cond" => $condition
        ];
        $jsonData2 = json_encode($data2);

        $ch2 = curl_init($url2);
        curl_setopt($ch2, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch2, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);

        curl_setopt($ch2, CURLOPT_POST, true);
        curl_setopt($ch2, CURLOPT_POSTFIELDS, $jsonData2);

        $response2 = curl_exec($ch2);

        $responseData2 = json_decode($response2, true);

        if ($responseData2['success'] == '1') {
            $i = 1;
            ?>


            <table class="table table-bordered bg-light" id="table1" style="width: 100%">
                <thead >
                    <tr>

                        <th style="text-align: center">
                            <?= select_language($_POST['lag'], 'L0039') ?>
                        </th>
                        <th style="text-align: center"><?= select_language($_POST['lag'], 'L0291') ?></th>
                        <th style="text-align: center"><?= select_language($_POST['lag'], 'L0292') ?></th>
                        <th style="text-align: center"><?= select_language($_POST['lag'], 'L0293') ?></th>
                        <th style="text-align: center"><?= select_language($_POST['lag'], 'L0197') ?></th>
                        <th style="text-align: center"><?= select_language($_POST['lag'], 'L0200') ?></th>
                        <th style="text-align: center"><?= select_language($_POST['lag'], 'L0233') ?></th>
                        <th style="text-align: center"><?= select_language($_POST['lag'], 'L0286') ?></th>
                        <th style="text-align: center"><?= select_language($_POST['lag'], 'L0297') ?></th>
                        <th style="text-align: center"><?= select_language($_POST['lag'], 'L0120') ?></th>
                        <th style="text-align: center"><?= select_language($_POST['lag'], 'L0235') ?></th>
                    </tr>
                </thead>
                <tbody style="font-size: 14px">
                    <?php
                    foreach ($responseData2['data'] as $dataResult2) {
                        ?>
                        <tr>
                            <td style="text-align: center;"><?= $i ?></td>
                            <td style="text-align: center"><?= $dataResult2['PurOrderNo'] ?></td>
                            <td style="text-align: left"></td>
                            <td style="text-align: left"><?= $dataResult2['PurOrderDate'] ?></td>
                            <td style="text-align: left"><?= $dataResult2['PlantNo'] ?></td>
                            <td style="text-align: left"><?= $dataResult2['VendName'] ?></td>
                            <td style="text-align: right"><?= number_format($dataResult2['OrderQty']) ?></td>
                            <td style="text-align: right"><?= number_format($dataResult2['NetPrice'], 2) ?></td>
                            <td style="text-align: right"><?= number_format($dataResult2['NetAmount'], 2) ?></td>
                            <td style="text-align: center;color: <?= $dataResult2['FontColor'] ?>"><?= $dataResult2['OrderStatus'] ?></td>
                            <td style="text-align: center"><a href="#" data-toggle="modal" data-backdrop="static" data-target="#modal_totaldetail"  onclick="select_totaldetail('<?= $dataResult2['PurOrderNo'] ?>')"><i class="fa fa-eye"></i></a></td>
                        </tr>
                        <?php
                        $i++;
                    }
                    ?>
                </tbody>
            </table>

            <?php
        } else {
            echo $responseData2['message'];
        }

        curl_close($ch2);

        sqlsrv_close($conn);
        break;
    case 'select_rework':
        $conn = connect_aglt();
        $condition = '';
        if ($_POST['datefrom'] != '' && $_POST['dateto'] != '') {
            $condition = $condition . " AND CONVERT(NVARCHAR(10),pd.[DeliveryDate],121) BETWEEN CONVERT(NVARCHAR(10),'" . $_POST['datefrom'] . "',121) AND CONVERT(NVARCHAR(10),'" . $_POST['dateto'] . "',121)";
        }
        if ($_POST['ponumber'] != '') {
            $condition = $condition . " AND pm.PurOrderNo = '" . $_POST['ponumber'] . "' ";
        }
        if ($_POST['vendor'] != '') {
            $condition = $condition . " AND pm.VendNo = '" . $_POST['vendor'] . "' ";
        }
        //echo $condition;
        $url2 = "http://45.136.236.233:3000/api/po/poapprove";
        $data2 = [
            "flag" => "se_purchaseorder",
            "cond" => $condition . " AND pm.OrderStatus=14 "
        ];
        $jsonData2 = json_encode($data2);

        $ch2 = curl_init($url2);
        curl_setopt($ch2, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch2, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);

        curl_setopt($ch2, CURLOPT_POST, true);
        curl_setopt($ch2, CURLOPT_POSTFIELDS, $jsonData2);

        $response2 = curl_exec($ch2);

        $responseData2 = json_decode($response2, true);

        if ($responseData2['success'] == '1') {
            $i = 1;
            ?>


            <table class="table table-bordered bg-light" id="table2" style="width: 100%">
                <thead >
                    <tr>
                        <th style="text-align: center;width: 10%">
                            <input type="checkbox" id="select_all" placeholder="Check All">
                        </th>
                        <th style="text-align: center">
                            <?= select_language($_POST['lag'], 'L0039') ?>
                        </th>
                        <th style="text-align: center"><?= select_language($_POST['lag'], 'L0291') ?></th>
                        <th style="text-align: center"><?= select_language($_POST['lag'], 'L0292') ?></th>
                        <th style="text-align: center"><?= select_language($_POST['lag'], 'L0293') ?></th>
                        <th style="text-align: center"><?= select_language($_POST['lag'], 'L0197') ?></th>
                        <th style="text-align: center"><?= select_language($_POST['lag'], 'L0200') ?></th>
                        <th style="text-align: center"><?= select_language($_POST['lag'], 'L0233') ?></th>
                        <th style="text-align: center"><?= select_language($_POST['lag'], 'L0286') ?></th>
                        <th style="text-align: center"><?= select_language($_POST['lag'], 'L0297') ?></th>
                        <th style="text-align: center"><?= select_language($_POST['lag'], 'L0120') ?></th>
                        <th style="text-align: center"><?= select_language($_POST['lag'], 'L0235') ?></th>
                    </tr>
                </thead>
                <tbody style="font-size: 14px">
                    <?php
                    foreach ($responseData2['data'] as $dataResult2) {
                        ?>
                        <tr>
                            <td style="text-align: center;">
                                <input type="checkbox" id="<?= $dataResult2['PurOrderNo'] ?>"  class="po_checkbox"  data-po-id="<?= $dataResult2['PurOrderNo'] ?>"> 
                            </td>
                            <td style="text-align: center;"><?= $i ?></td>
                            <td style="text-align: center"><?= $dataResult2['PurOrderNo'] ?></td>
                            <td style="text-align: left"></td>
                            <td style="text-align: left"><?= $dataResult2['PurOrderDate'] ?></td>
                            <td style="text-align: left"><?= $dataResult2['PlantNo'] ?></td>
                            <td style="text-align: left"><?= $dataResult2['VendName'] ?></td>
                            <td style="text-align: right"><?= number_format($dataResult2['OrderQty']) ?></td>
                            <td style="text-align: right"><?= number_format($dataResult2['NetPrice'], 2) ?></td>
                            <td style="text-align: right"><?= number_format($dataResult2['NetAmount'], 2) ?></td>
                            <td style="text-align: center;color: <?= $dataResult2['FontColor'] ?>"><?= $dataResult2['OrderStatus'] ?></td>
                            <td style="text-align: center"><a href="#" data-toggle="modal" data-backdrop="static" data-target="#modal_reworkdetail"  onclick="select_reworkdetail('<?= $dataResult2['PurOrderNo'] ?>')"><i class="fa fa-eye"></i></a></td>
                        </tr>
                        <?php
                        $i++;
                    }
                    ?>
                </tbody>
            </table>

            <?php
        } else {
            echo $responseData2['message'];
        }

        curl_close($ch2);
        ?>
        <?php
        sqlsrv_close($conn);
        break;
    case 'select_pomanagement':
        $conn = connect_aglt();
        ?>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header p-2">


                        <ul class="nav nav-pills">
                            <li class="nav-item"><a class="nav-link active" href="#total" data-toggle="tab"><?= select_language($_POST['lag'], 'L0297'); ?></a></li>
                            <li class="nav-item"><a class="nav-link" href="#rework" data-toggle="tab"><?= select_language($_POST['lag'], 'L0441'); ?></a></li>
                        </ul>
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane active" id="total">


                                <div class="row">
                                    <div class="col-md-12">

                                        <div class="card card-outline">
                                            <div class="card-header">
                                                <h3 class="card-title"><?= select_language($_POST['lag'], 'L0461'); ?></h3>
                                            </div> 
                                            <div class="card-body">
                                                <div class="row">


                                                    <div class="col-md-2">
                                                        <label class="form-label"><?= select_language($_POST['lag'], 'L0311'); ?> :</label>
                                                        <input type="date" class="form-control" id="txt_datefromsr_1" name="txt_datefromsr_1" placeholder="mm/dd/yyyy">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label"><?= select_language($_POST['lag'], 'L0312'); ?> :</label>
                                                        <input type="date" class="form-control" id="txt_datetosr_1" name="txt_datetosr_1" placeholder="mm/dd/yyyy">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label"><?= select_language($_POST['lag'], 'L0291'); ?> :</label>
                                                        <input type="text" class="form-control" id="txt_ponumbersr_1" name="txt_ponumbersr_1">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label"><?= select_language($_POST['lag'], 'L0200'); ?> :</label>
                                                        <select class="form-control" id="se_vendorsr_1" name="se_vendorsr_1">
                                                            <option value="">---Select Vendor---</option>
                                                            <?php
                                                            $sqlVendor = "SELECT [VendNo],[VendCode] FROM [tbm_Vendor]
                        ORDER BY VendNo ASC";
                                                            $paramsVendor = array();
                                                            $queryVendor = sqlsrv_query($conn, $sqlVendor, $paramsVendor);
                                                            while ($resultVendor = sqlsrv_fetch_array($queryVendor, SQLSRV_FETCH_ASSOC)) {
                                                                ?>
                                                                <option value="<?= $resultVendor['VendNo'] ?>" ><?= $resultVendor['VendCode'] ?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-1">
                                                        <label class="form-label">&emsp;&emsp;&emsp;</label>
                                                        <button type="button" class="btn btn-secondary" id="btn_search_1"><?= select_language($_POST['lag'], 'L0006'); ?>  <i class="fa fa-search"></i></button>
                                                    </div>

                                                </div>
                                                <div class="row">&nbsp;</div>
                                                <div class="row">
                                                    <div class="col-md-12">

                                                        <div id="div_total">
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>






                                    </div>
                                    <!--end::Row-->
                                </div>

                            </div>
                            <!-- /.tab-pane -->
                            <div class="tab-pane " id="rework">
                                <div class="card card">
                                    <div class="card-header">


                                        <h3 class="card-title"><?= select_language($_POST['lag'], 'L0460'); ?></h3>


                                    </div>
                                    <div class="card-body">
                                        <!-- Color Picker -->
                                        <div class="row">


                                            <div class="col-md-2">
                                                <label class="form-label"><?= select_language($_POST['lag'], 'L0311'); ?> :</label>
                                                <input type="date" class="form-control" id="txt_datefromsr_2" name="txt_datefromsr_2" placeholder="mm/dd/yyyy">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label"><?= select_language($_POST['lag'], 'L0312'); ?> :</label>
                                                <input type="date" class="form-control" id="txt_datetosr_2" name="txt_datetosr_2" placeholder="mm/dd/yyyy">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label"><?= select_language($_POST['lag'], 'L0291'); ?> :</label>
                                                <input type="text" class="form-control" id="txt_ponumbersr_2" name="txt_ponumbersr_2">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label"><?= select_language($_POST['lag'], 'L0200'); ?> :</label>
                                                <select class="form-control" id="se_vendorsr_2" name="se_vendorsr_2">
                                                    <option value="">---Select Vendor---</option>
                                                    <?php
                                                    $sqlVendor2 = "SELECT [VendNo],[VendCode] FROM [tbm_Vendor]
                        ORDER BY VendNo ASC";
                                                    $paramsVendor2 = array();
                                                    $queryVendor2 = sqlsrv_query($conn, $sqlVendor, $paramsVendor2);
                                                    while ($resultVendor2 = sqlsrv_fetch_array($queryVendor2, SQLSRV_FETCH_ASSOC)) {
                                                        ?>
                                                        <option value="<?= $resultVendor2['VendNo'] ?>" ><?= $resultVendor2['VendCode'] ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-md-1">
                                                <label class="form-label">&emsp;&emsp;&emsp;</label>
                                                <button type="button" class="btn btn-secondary" id="btn_search_2"><?= select_language($_POST['lag'], 'L0006'); ?>  <i class="fa fa-search"></i></button>
                                            </div>
                                            <div class="col-md-3 text-right">
                                                <label class="form-label">&emsp;</label><br>
                                                <button type="button" class="btn btn-warning" disabled="" id="btn_renew" name="btn_renew" fdprocessedid="p71ea"><?= select_language($_POST['lag'], 'L0465'); ?> <i class="fa fa-reply"></i></button>
                                                <button type="button" class="btn btn-danger" id="btn_reject" name="btn_reject" fdprocessedid="p71ea"><?= select_language($_POST['lag'], 'L0435'); ?> <i class="fa fa-reply"></i></button>
                                            </div>
                                        </div>


                                        <div class="row">&nbsp;</div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div id="div_rework">
                                                </div>

                                            </div>
                                            <!--end::Row-->
                                        </div>
                                    </div>

                                    <!-- /.form group -->

                                    <!-- time Picker -->

                                </div>
                            </div>
                            <!-- /.tab-pane -->


                            <!-- /.tab-pane -->
                        </div>
                        <!-- /.tab-content -->
                    </div><!-- /.card-body -->
                </div>

                <!-- /.card -->

            </div>
        </div>




        <?php
        sqlsrv_close($conn);
        break;
    case 'select_potimeline':
        $conn = connect_aglt();
        ?>

        <div class="modal-header"><b>PO Number : <?= $_POST['ponumber'] ?></b></div>
        <div class="modal-body">
            <?php
            $i = 1;
            $sqlTimeline = "SELECT c.PosName,CONCAT(b.TitleNameEN,b.FirstNameEN,' ',b.LastNameEN) AS 'FLName',
            CONCAT(CONVERT(NVARCHAR(15),a.CreateDate,106),' ',CONVERT(NVARCHAR(5),a.CreateDate,108)) AS 'CreateDate' FROM [dbo].[tbt_PurchOrderApproval] a
            INNER JOIN [dbo].[tbm_Employee] b ON a.ApprovalNo=b.EmpCode
            INNER JOIN [dbo].[tbm_Position] c ON b.PosNo=c.PosNo
            WHERE a.PurOrderNo='" . $_POST['ponumber'] . "' 
            ORDER BY a.Levels DESC";
            $paramsTimeline = array();
            $queryTimeline = sqlsrv_query($conn, $sqlTimeline, $paramsTimeline);
            ?>
            <div class="timeline timeline-inverse">
                <?php
                while ($resultTimeline = sqlsrv_fetch_array($queryTimeline, SQLSRV_FETCH_ASSOC)) {
                    ?>
                    <div class="time-label">
                        <span class="bg-primary">
                            Approver R<?= 1 + $i ?>
                        </span>
                    </div>

                    <div>
                        <i class="fa fa-user bg-primary" aria-hidden="true"></i>


                        <div class="timeline-item">
                            <span class="time"><i class="far fa-clock"></i> <?= $resultTimeline['CreateDate'] ?></span>

                            <h3 style="font-size: 18px" class="timeline-header"><?= $resultTimeline['FLName'] ?></h3>

                            <div class="timeline-body">
                                <?= $resultTimeline['PosName'] ?>
                            </div>

                        </div>
                    </div>




                    <?php
                    $i++;
                }
                ?>
                <div>
                    <i class="fa fa-check bg-success"></i>
                </div>
            </div>
        </div>
        <div class="modal-footer" style="text-align: right">
            <button class="btn btn-danger"  data-dismiss="modal">Close <i class="fa fa-times"></i></button>
        </div>

        <?php
        sqlsrv_close($conn);
        break;
    case 'save_supplierconfirm':
        $conn = connect_aglt();

        $url = "http://45.136.236.233:3000/api/vendor/confirm";
        $input = $_POST['ponumber'];
        $purOrderNos = explode(",", $input);
        $data = [
            "data" => array_map(fn($no) => ["PurOrderNo" => $no, "ConfirmBy" => $_SESSION["EmployeeCode"]], $purOrderNos)
        ];

        //json_encode($data, JSON_PRETTY_PRINT);
        $jsonData = json_encode($data, JSON_PRETTY_PRINT);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        $response = curl_exec($ch);
        $responseData = json_decode($response, true);

        $sqlGetdate = "SELECT 
		UPPER(FORMAT(GETDATE(),'MMMM dd,yyyy HH:mm')) AS 'GETDATE' ";
        $paramsGetdate = array();
        $queryGetdate = sqlsrv_query($conn, $sqlGetdate, $paramsGetdate);
        $resultGetdate = sqlsrv_fetch_array($queryGetdate, SQLSRV_FETCH_ASSOC);

        $mail = new PHPMailer();
        $mail->CharSet = "utf-8";

        $mail->isSMTP();
        $mail->Host = "mail.aglt.co.th";
        $mail->SMTPAuth = true;
        $mail->Username = "wittawat_it@aglt.co.th";
        $mail->Password = "Wittawat_2532";

        $mail->setFrom("wittawat_it@aglt.co.th", "Mr.Vendor");
        $mail->isHTML(true);
        $mail->Subject = "PO CONFIRM BY SUPPLIER";

        $template = file_get_contents('../pages/email_vendorconfirm.php');

        $json1 = '{"success":true,"message":"Insert completed","data":[{"PurOrdNo":"5070380164","PlantNo":"Z502","EmpCode":"A0044","EmpName":"Wittawat Khamjundee","Email":"wittawat_it@aglt.co.th"}]}';

        $json2 = '{
              "success": true,
              "message": "Insert completed",
              "data": [
                {
                  "PurOrdNo": "5070380122",
                  "PlantNo": "Z502",
                  "EmpCode": "A0044",
                  "EmpName": "Wittawat Khamjundee",
                  "Email": "wittawat_it@aglt.co.th"
                },
                {
                  "PurOrdNo": "5070380129",
                  "PlantNo": "Z502",
                  "EmpCode": "A0044",
                  "EmpName": "Wittawat Khamjundee",
                  "Email": "wittawat_it@aglt.co.th"
                }
              ]
        }';

        // แปลง JSON เป็น array
        ///echo $response;
        $data_rt = json_decode($response, true);

        // จัดกลุ่มตาม Email
        $grouped = [];
        foreach ($data_rt['data'] as $row) {
            $grouped[$row['Email']][] = $row;
        }

        // ส่งเมลครั้งเดียวต่อ Email
        foreach ($grouped as $email => $rows) {
            // ใช้ชื่อจาก record แรกของ email นั้น
            $toName = $rows[0]['EmpName'];
            $toCode = $rows[0]['EmpCode'];
            // สร้าง Content เป็นแถวของตาราง
            $Content = '';
            $no = 1;
            foreach ($rows as $row) {
                $Content .= '<tr>
            <td style="text-align:center;">' . $no++ . '</td>
            <td>' . $row['PlantNo'] . '</td>
            <td>' . $row['PurOrderNo'] . '</td>
        </tr>';
            }

            // สร้าง body จาก template
            $htmlContent = $template;
            $htmlContent = str_replace('{{ReferentNo}}', mt_rand(100000, 999999), $htmlContent);
            $htmlContent = str_replace('{{userNo}}', $toCode, $htmlContent);
            $htmlContent = str_replace('{{To}}', $toName, $htmlContent);
            $htmlContent = str_replace('{{Name}}', 'Mr.Vendor', $htmlContent);
            $htmlContent = str_replace('{{DateTime}}', $resultGetdate['GETDATE'], $htmlContent);
            $htmlContent = str_replace('{{Detail}}', '
        <table style="font-size:18px;width:100%;border-collapse:collapse;" border="1">
            <tr>
                <td style="text-align:center;"><b>No</b></td>
                <td><b>Plant No</b></td>
                <td><b>PO</b></td>
               
            </tr>' . $Content . '
        </table>', $htmlContent);

            // ตั้งค่า body และผู้รับ
            $mail->Body = $htmlContent;
            $mail->clearAddresses(); // ล้าง address เดิมก่อน
            $mail->addAddress($email, $toName);
            $mail->send();
            // ส่งเมล
            // if (!$mail->send()) {
            //      echo "Error to $email :: " . $mail->ErrorInfo . "<br>";
            // } else {
            //      echo "KMS :: Message sent to $email<br>";
            //  }
        }

        echo $responseData['success'] . '|' . $responseData['message'];
        curl_close($ch);
        sqlsrv_close($conn);
        break;

    case 'select_suppliercompleted':
        $conn = connect_aglt();
        $sqlPlant = "SELECT PlantNo FROM [dbo].[tbm_Employee] WHERE EmpCode='" . $_SESSION["EmployeeCode"] . "'";
        $paramsPlant = array();
        $queryPlant = sqlsrv_query($conn, $sqlPlant, $paramsPlant);
        $resultPlant = sqlsrv_fetch_array($queryPlant, SQLSRV_FETCH_ASSOC);

        $url = "http://45.136.236.233:3000/api/po/master";
        $data = [
            "data" => [[
            "PlantNo" => $resultPlant['PlantNo'],
            "VendorNo" => $_POST['VendNo'],
            "OrderStatus" => '12'
                ]]
        ];
        $jsonData = json_encode($data);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        $response = curl_exec($ch);

        $responseData = json_decode($response, true);

        if ($responseData['success'] == '1') {
            $i = 1;
            ?>
            <div class="card card-outline">

                <div class="card-body">

                    <table class="table table-bordered bg-light" id="table9" style="width: 100%">
                        <thead >
                            <tr>
                                <th style="text-align: center">No</th>
                                <th style="text-align: center">Order Date</th>
                                <th style="text-align: center">Delivery Date</th>
                                <th style="text-align: center">PO Number</th>
                                <th style="text-align: center">Qty</th>
                                <th style="text-align: center">Unit Price</th>
                                <th style="text-align: center">Net Amount</th>
                                <th style="text-align: center">Order Status</th>
                                <th style="text-align: center">Action</th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 14px">
                            <?php
                            foreach ($responseData['data'] as $dataResult) {
                                ?>


                                <tr>
                                    <td style="text-align: center;"><?= $i ?></td>
                                    <td style="text-align: center"><?= $dataResult['PurOrderDate'] ?></td>
                                    <td style="text-align: center"><?= $dataResult['DeliveryDate'] ?></td>
                                    <td style="text-align: center"><a href="#" data-toggle="modal" data-backdrop="static" data-target="#modal_potimeline"  onclick="select_potimeline('<?= $dataResult['PurOrderNo'] ?>')" title="Time Line"><?= $dataResult['PurOrderNo'] ?></a></td>
                                    <td style="text-align: right"><?= number_format($dataResult['OrderQty']) ?></td>
                                    <td style="text-align: right"><?= number_format($dataResult['NetPrice'], 2) ?></td>
                                    <td style="text-align: right"><?= number_format($dataResult['NetAmount'], 2) ?></td>
                                    <td style="text-align: center;color: <?= $dataResult['FontColor'] ?>"><?= $dataResult['OrderStatus'] ?></td>
                                    <td style="text-align: center"><a href="#" data-toggle="modal" data-backdrop="static" data-target="#modal_supcompleteddetail"  onclick="select_supcompleteddetail('<?= $dataResult['PurOrderNo'] ?>')"><i class="fa fa-eye"></i></a></td>

                                </tr>

                                <?php
                                $i++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php
        } else {
            echo $responseData['message'];
        }


        curl_close($ch);
        sqlsrv_close($conn);
        break;
    case 'select_supplierreceiving':
        $conn = connect_aglt();
        $sqlPlant = "SELECT PlantNo FROM [dbo].[tbm_Employee] WHERE EmpCode='" . $_SESSION["EmployeeCode"] . "'";
        $paramsPlant = array();
        $queryPlant = sqlsrv_query($conn, $sqlPlant, $paramsPlant);
        $resultPlant = sqlsrv_fetch_array($queryPlant, SQLSRV_FETCH_ASSOC);

        $url = "http://45.136.236.233:3000/api/po/master";
        $data = [
            "data" => [[
            "PlantNo" => $resultPlant['PlantNo'],
            "VendorNo" => $_POST['VendNo'],
            "OrderStatus" => '11'
                ]]
        ];
        $jsonData = json_encode($data);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        $response = curl_exec($ch);

        $responseData = json_decode($response, true);

        if ($responseData['success'] == '1') {
            $i = 1;
            ?>
            <div class="card card-outline">

                <div class="card-body">

                    <table class="table table-bordered bg-light" id="table4" style="width: 100%">
                        <thead >
                            <tr>
                                <th style="text-align: center">No</th>
                                <th style="text-align: center">Order Date</th>
                                <th style="text-align: center">Delivery Date</th>
                                <th style="text-align: center">PO Number</th>
                                <th style="text-align: center">Qty</th>
                                <th style="text-align: center">Unit Price</th>
                                <th style="text-align: center">Net Amount</th>
                                <th style="text-align: center">Order Status</th>
                                <th style="text-align: center">Action</th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 14px">
                            <?php
                            foreach ($responseData['data'] as $dataResult) {
                                ?>


                                <tr>
                                    <td style="text-align: center;"><?= $i ?></td>
                                    <td style="text-align: center"><?= $dataResult['PurOrderDate'] ?></td>
                                    <td style="text-align: center"><?= $dataResult['DeliveryDate'] ?></td>
                                    <td style="text-align: center"><a href="#" data-toggle="modal" data-backdrop="static" data-target="#modal_potimeline"  onclick="select_potimeline('<?= $dataResult['PurOrderNo'] ?>')" title="Time Line"><?= $dataResult['PurOrderNo'] ?></a></td>
                                    <td style="text-align: right"><?= number_format($dataResult['OrderQty']) ?></td>
                                    <td style="text-align: right"><?= number_format($dataResult['NetPrice'], 2) ?></td>
                                    <td style="text-align: right"><?= number_format($dataResult['NetAmount'], 2) ?></td>
                                    <td style="text-align: center;color: <?= $dataResult['FontColor'] ?>"><?= $dataResult['OrderStatus'] ?></td>
                                    <td style="text-align: center"><a href="#" data-toggle="modal" data-backdrop="static" data-target="#modal_supreceivingdetail"  onclick="select_supreceivingdetail('<?= $dataResult['PurOrderNo'] ?>')"><i class="fa fa-eye"></i></a></td>

                                </tr>

                                <?php
                                $i++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php
        } else {
            echo $responseData['message'];
        }


        curl_close($ch);
        sqlsrv_close($conn);
        break;
    case 'select_supplierinprogress':
        $conn = connect_aglt();
        $sqlPlant = "SELECT PlantNo FROM [dbo].[tbm_Employee] WHERE EmpCode='" . $_SESSION["EmployeeCode"] . "'";
        $paramsPlant = array();
        $queryPlant = sqlsrv_query($conn, $sqlPlant, $paramsPlant);
        $resultPlant = sqlsrv_fetch_array($queryPlant, SQLSRV_FETCH_ASSOC);

        $url = "http://45.136.236.233:3000/api/po/master";
        $data = [
            "data" => [[
            "PlantNo" => $resultPlant['PlantNo'],
            "VendorNo" => $_POST['VendNo'],
            "OrderStatus" => '10'
                ]]
        ];
        $jsonData = json_encode($data);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        $response = curl_exec($ch);

        $responseData = json_decode($response, true);

        if ($responseData['success'] == '1') {
            $i = 1;
            ?>
            <div class="card card-outline">

                <div class="card-body">

                    <table class="table table-bordered bg-light" id="table3" style="width: 100%">
                        <thead >
                            <tr>
                                <th style="text-align: center">No</th>
                                <th style="text-align: center">Order Date</th>
                                <th style="text-align: center">Delivery Date</th>
                                <th style="text-align: center">PO Number</th>
                                <th style="text-align: center">Qty</th>
                                <th style="text-align: center">Unit Price</th>
                                <th style="text-align: center">Net Amount</th>
                                <th style="text-align: center">Order Status</th>
                                <th style="text-align: center">Action</th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 14px">
                            <?php
                            foreach ($responseData['data'] as $dataResult) {
                                ?>


                                <tr>
                                    <td style="text-align: center;"><?= $i ?></td>
                                    <td style="text-align: center"><?= $dataResult['PurOrderDate'] ?></td>
                                    <td style="text-align: center"><?= $dataResult['DeliveryDate'] ?></td>
                                    <td style="text-align: center"><a href="#" data-toggle="modal" data-backdrop="static" data-target="#modal_potimeline"  onclick="select_potimeline('<?= $dataResult['PurOrderNo'] ?>')" title="Time Line"><?= $dataResult['PurOrderNo'] ?></a></td>
                                    <td style="text-align: right"><?= number_format($dataResult['OrderQty']) ?></td>
                                    <td style="text-align: right"><?= number_format($dataResult['NetPrice'], 2) ?></td>
                                    <td style="text-align: right"><?= number_format($dataResult['NetAmount'], 2) ?></td>
                                    <td style="text-align: center;color: <?= $dataResult['FontColor'] ?>"><?= $dataResult['OrderStatus'] ?></td>
                                    <td style="text-align: center"><a href="#" data-toggle="modal" data-backdrop="static" data-target="#modal_supprogressdetail"  onclick="select_supprogressdetail('<?= $dataResult['PurOrderNo'] ?>')"><i class="fa fa-eye"></i></a></td>

                                </tr>

                                <?php
                                $i++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php
        } else {
            echo $responseData['message'];
        }


        curl_close($ch);
        sqlsrv_close($conn);
        break;
    case 'select_supplieractionreg':
        $conn = connect_aglt();
        $sqlPlant = "SELECT PlantNo FROM [dbo].[tbm_Employee] WHERE EmpCode='" . $_SESSION["EmployeeCode"] . "'";
        $paramsPlant = array();
        $queryPlant = sqlsrv_query($conn, $sqlPlant, $paramsPlant);
        $resultPlant = sqlsrv_fetch_array($queryPlant, SQLSRV_FETCH_ASSOC);

        $url = "http://45.136.236.233:3000/api/po/master";
        $data = [
            "data" => [[
            "PlantNo" => $resultPlant['PlantNo'],
            "VendorNo" => $_POST['VendNo'],
            "OrderStatus" => '9'
                ]]
        ];

        $jsonData = json_encode($data);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        $response = curl_exec($ch);

        $responseData = json_decode($response, true);

        if ($responseData['success'] == '1') {
            $i = 1;
            ?>
            <div class="card card-outline">
                <div class="card-header text-right">
                    <button type="button" class="btn btn-primary" id="btn_confirmselected" name="btn_confirmselected" fdprocessedid="f3tcwn">Confirm Selected <i class="fa fa-arrow-right"></i></button>
                </div> 
                <div class="card-body">




                    <table class="table table-bordered bg-light" id="table2" style="width: 100%">
                        <thead >
                            <tr>
                                <th style="text-align: center;width: 10%">
                                    <input type="checkbox" id="select_all" placeholder="Check All">
                                </th>
                                <th style="text-align: center">No</th>
                                <th style="text-align: center">Order Date</th>
                                <th style="text-align: center">Delivery Date</th>
                                <th style="text-align: center">PO Number</th>
                                <th style="text-align: center">Qty</th>
                                <th style="text-align: center">Unit Price</th>
                                <th style="text-align: center">Net Amount</th>
                                <th style="text-align: center">Order Status</th>
                                <th style="text-align: center">Action</th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 14px">
                            <?php
                            foreach ($responseData['data'] as $dataResult) {
                                ?>


                                <tr>
                                    <td style="text-align: center;">
                                        <input type="checkbox" id="<?= $dataResult['PurOrderNo'] ?>"  class="po_checkbox"  data-po-id="<?= $dataResult['PurOrderNo'] ?>"> 
                                    </td>
                                    <td style="text-align: center;"><?= $i ?></td>
                                    <td style="text-align: center"><?= $dataResult['PurOrderDate'] ?></td>
                                    <td style="text-align: center"><?= $dataResult['DeliveryDate'] ?></td>
                                    <td style="text-align: center"><a href="#" data-toggle="modal" data-backdrop="static" data-target="#modal_potimeline"  onclick="select_potimeline('<?= $dataResult['PurOrderNo'] ?>')" title="Time Line"><?= $dataResult['PurOrderNo'] ?></a></td>
                                    <td style="text-align: right"><?= number_format($dataResult['OrderQty']) ?></td>
                                    <td style="text-align: right"><?= number_format($dataResult['NetPrice'], 2) ?></td>
                                    <td style="text-align: right"><?= number_format($dataResult['NetAmount'], 2) ?></td>
                                    <td style="text-align: center;color: <?= $dataResult['FontColor'] ?>"><?= $dataResult['OrderStatus'] ?></td>
                                    <td style="text-align: center"><a href="#" data-toggle="modal" data-backdrop="static" data-target="#modal_supactionreqdetail"  onclick="select_supactionreqdetail('<?= $dataResult['PurOrderNo'] ?>')"><i class="fa fa-eye"></i></a></td>

                                </tr>

                                <?php
                                $i++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php
        } else {
            echo $responseData['message'];
        }


        curl_close($ch);
        sqlsrv_close($conn);
        break;

    case 'select_approvalcompleted':
        $conn = connect_aglt();
        $url = "http://45.136.236.233:3000/api/po/poapprove";
        $data = [
            "flag" => "se_poapprove",
            "cond" => ''
        ];
        $jsonData = json_encode($data);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        $response = curl_exec($ch);

        $responseData = json_decode($response, true);

        if ($responseData['success'] == '1') {
            $i = 1;
            ?>
            <div class="card card-outline">

                <div class="card-body">

                    <table class="table table-bordered bg-light" id="table2" style="width: 100%">
                        <thead >
                            <tr>

                                <th style="text-align: center">No</th>
                                <th style="text-align: center">Order Date</th>
                                <th style="text-align: center">PO Number</th>
                                <th style="text-align: center">Qty</th>
                                <th style="text-align: center">Unit Price</th>
                                <th style="text-align: center">Net Amount</th>
                                <th style="text-align: center">Order Status</th>
                                <th style="text-align: center">Active</th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 14px">
                            <?php
                            foreach ($responseData['data'] as $dataResult) {
                                ?>


                                <tr>
                                    <td style="text-align: center;"><?= $i ?></td>
                                    <td style="text-align: center"><?= $dataResult['PurOrderDate'] ?></td>
                                    <td style="text-align: center"><a href="#" data-toggle="modal" data-backdrop="static" data-target="#modal_potimeline"  onclick="select_potimeline('<?= $dataResult['PurOrderNo'] ?>')" title="Time Line"><?= $dataResult['PurOrderNo'] ?></a></td>
                                    <td style="text-align: right"><?= number_format($dataResult['OrderQty']) ?></td>
                                    <td style="text-align: right"><?= number_format($dataResult['NetPrice'], 2) ?></td>
                                    <td style="text-align: right"><?= number_format($dataResult['NetAmount'], 2) ?></td>
                                    <td style="text-align: center;color: <?= $dataResult['FontColor'] ?>"><?= $dataResult['OrderStatus'] ?></td>
                                    <td style="text-align: center"><a href="#" data-toggle="modal" data-backdrop="static" data-target="#modal_completeddetail"  onclick="select_completeddetail('<?= $dataResult['PurOrderNo'] ?>')"><i class="fa fa-eye"></i></a></td>

                                </tr>

                                <?php
                                $i++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php
        } else {
            echo $responseData['message'];
        }


        curl_close($ch);
        sqlsrv_close($conn);
        break;
    case 'select_approvalconfirm':
        $conn = connect_aglt();
        $url = "http://45.136.236.233:3000/api/po/poapprove";
        $data = [
            "flag" => "se_waitpoapprove",
            "cond" => " AND pav.ApprovalNo='" . $_SESSION["EmployeeCode"] . "' AND pav.ItemStatus='2' AND pm.OrderStatus=7"
        ];
        $jsonData = json_encode($data);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        $response = curl_exec($ch);

        $responseData = json_decode($response, true);

        if ($responseData['success'] == '1') {
            $i = 1;
            ?>
            <div class="card card-outline">

                <div class="card-body">

                    <table class="table table-bordered bg-light" id="table5" style="width: 100%">
                        <thead >
                            <tr>

                                <th style="text-align: center">No</th>
                                <th style="text-align: center">Order Date</th>
                                <th style="text-align: center">Delivery Date</th>
                                <th style="text-align: center">PO Number</th>
                                <th style="text-align: center">Qty</th>
                                <th style="text-align: center">Unit Price</th>
                                <th style="text-align: center">Net Amount</th>
                                <th style="text-align: center">Order Status</th>
                                <th style="text-align: center">Active</th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 14px">
                            <?php
                            foreach ($responseData['data'] as $dataResult) {
                                ?>


                                <tr>
                                    <td style="text-align: center;"><?= $i ?></td>
                                    <td style="text-align: center"><?= $dataResult['PurOrderDate'] ?></td>
                                    <td style="text-align: center"><?= $dataResult['DeliveryDate'] ?></td>
                                    <td style="text-align: center"><a href="#" data-toggle="modal" data-backdrop="static" data-target="#modal_potimeline"  onclick="select_potimeline('<?= $dataResult['PurOrderNo'] ?>')" title="Time Line"><?= $dataResult['PurOrderNo'] ?></a></td>
                                    <td style="text-align: right"><?= number_format($dataResult['OrderQty']) ?></td>
                                    <td style="text-align: right"><?= number_format($dataResult['NetPrice'], 2) ?></td>
                                    <td style="text-align: right"><?= number_format($dataResult['NetAmount'], 2) ?></td>
                                    <td style="text-align: center;color: <?= $dataResult['FontColor'] ?>"><?= $dataResult['OrderStatus'] ?></td>
                                    <td style="text-align: center"><a href="#" data-toggle="modal" data-backdrop="static" data-target="#modal_completeddetail"  onclick="select_completeddetail('<?= $dataResult['PurOrderNo'] ?>')"><i class="fa fa-eye"></i></a></td>

                                </tr>

                                <?php
                                $i++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php
        } else {
            echo $responseData['message'];
        }


        curl_close($ch);
        sqlsrv_close($conn);
        break;
    case 'select_approvalreject':
        $conn = connect_aglt();
        $url = "http://45.136.236.233:3000/api/po/poapprove";
        $data = [
            "flag" => "se_waitpoapprove",
            "cond" => " AND pav.ApprovalNo='" . $_SESSION["EmployeeCode"] . "' AND pav.ItemStatus='4' AND pm.OrderStatus IN ('7','15')"
        ];
        $jsonData = json_encode($data);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        $response = curl_exec($ch);

        $responseData = json_decode($response, true);

        if ($responseData['success'] == '1') {
            $i = 1;
            ?>
            <div class="card card-outline">

                <div class="card-body">

                    <table class="table table-bordered bg-light" id="table7" style="width: 100%">
                        <thead >
                            <tr>

                                <th style="text-align: center">No</th>
                                <th style="text-align: center">Order Date</th>
                                <th style="text-align: center">Delivery Date</th>
                                <th style="text-align: center">PO Number</th>
                                <th style="text-align: center">Qty</th>
                                <th style="text-align: center">Unit Price</th>
                                <th style="text-align: center">Net Amount</th>
                                <th style="text-align: center">Order Status</th>
                                <th style="text-align: center">Active</th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 14px">
                            <?php
                            foreach ($responseData['data'] as $dataResult) {
                                ?>


                                <tr>
                                    <td style="text-align: center;"><?= $i ?></td>
                                    <td style="text-align: center"><?= $dataResult['PurOrderDate'] ?></td>
                                    <td style="text-align: center"><?= $dataResult['DeliveryDate'] ?></td>
                                    <td style="text-align: center"><a href="#" data-toggle="modal" data-backdrop="static" data-target="#modal_potimeline"  onclick="select_potimeline('<?= $dataResult['PurOrderNo'] ?>')" title="Time Line"><?= $dataResult['PurOrderNo'] ?></a></td>
                                    <td style="text-align: right"><?= number_format($dataResult['OrderQty']) ?></td>
                                    <td style="text-align: right"><?= number_format($dataResult['NetPrice'], 2) ?></td>
                                    <td style="text-align: right"><?= number_format($dataResult['NetAmount'], 2) ?></td>
                                    <td style="text-align: center;color: <?= $dataResult['FontColor'] ?>"><?= $dataResult['OrderStatus'] ?></td>
                                    <td style="text-align: center"><a href="#" data-toggle="modal" data-backdrop="static" data-target="#modal_completeddetail"  onclick="select_completeddetail('<?= $dataResult['PurOrderNo'] ?>')"><i class="fa fa-eye"></i></a></td>

                                </tr>

                                <?php
                                $i++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php
        } else {
            echo $responseData['message'];
        }


        curl_close($ch);
        sqlsrv_close($conn);
        break;
    case 'select_approvalrecheck':
        $conn = connect_aglt();
        $url = "http://45.136.236.233:3000/api/po/poapprove";
        $data = [
            "flag" => "se_waitpoapprove",
            "cond" => " AND pav.ApprovalNo='" . $_SESSION["EmployeeCode"] . "' AND pav.ItemStatus='3' AND pm.OrderStatus IN ('7','14')"
        ];
        $jsonData = json_encode($data);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        $response = curl_exec($ch);

        $responseData = json_decode($response, true);

        if ($responseData['success'] == '1') {
            $i = 1;
            ?>
            <div class="card card-outline">

                <div class="card-body">

                    <table class="table table-bordered bg-light" id="table6" style="width: 100%">
                        <thead >
                            <tr>

                                <th style="text-align: center">No</th>
                                <th style="text-align: center">Order Date</th>
                                <th style="text-align: center">Delivery Date</th>
                                <th style="text-align: center">PO Number</th>
                                <th style="text-align: center">Qty</th>
                                <th style="text-align: center">Unit Price</th>
                                <th style="text-align: center">Net Amount</th>
                                <th style="text-align: center">Order Status</th>
                                <th style="text-align: center">Active</th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 14px">
                            <?php
                            foreach ($responseData['data'] as $dataResult) {
                                ?>


                                <tr>
                                    <td style="text-align: center;"><?= $i ?></td>
                                    <td style="text-align: center"><?= $dataResult['PurOrderDate'] ?></td>
                                    <td style="text-align: center"><?= $dataResult['DeliveryDate'] ?></td>
                                    <td style="text-align: center"><a href="#" data-toggle="modal" data-backdrop="static" data-target="#modal_potimeline"  onclick="select_potimeline('<?= $dataResult['PurOrderNo'] ?>')" title="Time Line"><?= $dataResult['PurOrderNo'] ?></a></td>
                                    <td style="text-align: right"><?= number_format($dataResult['OrderQty']) ?></td>
                                    <td style="text-align: right"><?= number_format($dataResult['NetPrice'], 2) ?></td>
                                    <td style="text-align: right"><?= number_format($dataResult['NetAmount'], 2) ?></td>
                                    <td style="text-align: center;color: <?= $dataResult['FontColor'] ?>"><?= $dataResult['OrderStatus'] ?></td>
                                    <td style="text-align: center"><a href="#" data-toggle="modal" data-backdrop="static" data-target="#modal_completeddetail"  onclick="select_completeddetail('<?= $dataResult['PurOrderNo'] ?>')"><i class="fa fa-eye"></i></a></td>

                                </tr>

                                <?php
                                $i++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php
        } else {
            echo $responseData['message'];
        }


        curl_close($ch);
        sqlsrv_close($conn);
        break;
    case 'update_sendconfirm':
        $conn = connect_aglt();

        $url = "http://45.136.236.233:3000/api/po/sendingconfirm";
        $input = $_POST['PurOrderNo'];
        $purOrderNos = explode(",", $input);
        $data = [
            "createBy" => $_SESSION["EmployeeCode"],
            "data" => array_map(fn($no) => ["PurOrderNo" => $no], $purOrderNos)
        ];

        //json_encode($data, JSON_PRETTY_PRINT);
        $jsonData = json_encode($data, JSON_PRETTY_PRINT);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        $response = curl_exec($ch);
        $responseData = json_decode($response, true);

        $sqlGetdate = "SELECT 
		UPPER(FORMAT(GETDATE(),'MMMM dd,yyyy HH:mm')) AS 'GETDATE' ";
        $paramsGetdate = array();
        $queryGetdate = sqlsrv_query($conn, $sqlGetdate, $paramsGetdate);
        $resultGetdate = sqlsrv_fetch_array($queryGetdate, SQLSRV_FETCH_ASSOC);

        $mail = new PHPMailer();
        $mail->CharSet = "utf-8";

        $mail->isSMTP();
        $mail->Host = "mail.aglt.co.th";
        $mail->SMTPAuth = true;
        $mail->Username = "wittawat_it@aglt.co.th";
        $mail->Password = "Wittawat_2532";

        $mail->setFrom("wittawat_it@aglt.co.th", "Mr.Kitz");
        $mail->isHTML(true);
        $mail->Subject = "PO CONFIRM";

        $template = file_get_contents('../pages/email_confirm.php');

        $json1 = '{"success":true,"message":"Insert completed","data":[{"PurOrdNo":"5070380164","PlantNo":"Z502","EmpCode":"A0044","EmpName":"Wittawat Khamjundee","Email":"wittawat_it@aglt.co.th"}]}';

        $json2 = '{
              "success": true,
              "message": "Insert completed",
              "data": [
                {
                  "PurOrdNo": "5070380122",
                  "PlantNo": "Z502",
                  "EmpCode": "A0044",
                  "EmpName": "Wittawat Khamjundee",
                  "Email": "wittawat_it@aglt.co.th"
                },
                {
                  "PurOrdNo": "5070380129",
                  "PlantNo": "Z502",
                  "EmpCode": "A0044",
                  "EmpName": "Wittawat Khamjundee",
                  "Email": "wittawat_it@aglt.co.th"
                }
              ]
        }';

        // แปลง JSON เป็น array
        $data_rt = json_decode($response, true);

        // จัดกลุ่มตาม Email
        $grouped = [];
        foreach ($data_rt['data'] as $row) {
            $grouped[$row['Email']][] = $row;
        }

        // ส่งเมลครั้งเดียวต่อ Email
        foreach ($grouped as $email => $rows) {
            // ใช้ชื่อจาก record แรกของ email นั้น
            $VendName = $rows[0]['VendName'];
            $VendNo = $rows[0]['VendNo'];
            // สร้าง Content เป็นแถวของตาราง
            $Content = '';
            $no = 1;
            foreach ($rows as $row) {
                $Content .= '<tr>
            <td style="text-align:center;">' . $no++ . '</td>
            <td>' . $row['PlantNo'] . '</td>
            <td>' . $row['PurOrderNo'] . '</td>
        </tr>';
            }

            $sqlUser = "SELECT TOP 1 Username,Password FROM tba_Account WHERE EmpCode='" . $VendNo . "' ";
            $paramsUser = array();
            $queryUser = sqlsrv_query($conn, $sqlUser, $paramsUser);
            $resultUser = sqlsrv_fetch_array($queryUser, SQLSRV_FETCH_ASSOC);

            // สร้าง body จาก template
            $htmlContent = $template;
            $htmlContent = str_replace('{{ReferentNo}}', mt_rand(100000, 999999), $htmlContent);
            $htmlContent = str_replace('{{Username}}', $resultUser['Username'], $htmlContent);
            $htmlContent = str_replace('{{Password}}', $resultUser['Password'], $htmlContent);
            $htmlContent = str_replace('{{VendNo}}', $VendNo, $htmlContent);
            $htmlContent = str_replace('{{To}}', $VendName, $htmlContent);
            $htmlContent = str_replace('{{Name}}', 'Mr.Kitz', $htmlContent);
            $htmlContent = str_replace('{{DateTime}}', $resultGetdate['GETDATE'], $htmlContent);
            $htmlContent = str_replace('{{Detail}}', '
        <table style="font-size:18px;width:100%;border-collapse:collapse;" border="1">
            <tr>
                <td style="text-align:center;"><b>No</b></td>
                <td><b>Plant No</b></td>
                <td><b>PO</b></td>
               
            </tr>' . $Content . '
        </table>', $htmlContent);

            // ตั้งค่า body และผู้รับ
            $mail->Body = $htmlContent;
            $mail->clearAddresses(); // ล้าง address เดิมก่อน
            $mail->addAddress($email, $toName);
            $mail->send();
            // ส่งเมล
            // if (!$mail->send()) {
            //      echo "Error to $email :: " . $mail->ErrorInfo . "<br>";
            // } else {
            //      echo "KMS :: Message sent to $email<br>";
            //  }
        }

        echo $responseData['success'] . '|' . $responseData['message'];
        curl_close($ch);
        sqlsrv_close($conn);
        break;
    case 'select_confirmdetail':
        $conn = connect_aglt();

        $sqlPreparation1 = "SELECT d.Email,
        CONVERT(NVARCHAR(10),b.PurOrderDate,23) AS 'PurOrderDate',d.VendName,SUM(a.NetAmount) AS 'NetAmount',c.StatName,c.FontColor 
        FROM [dbo].[tbt_PurchOrderDetail] a 
        INNER JOIN [dbo].[tbt_PurchOrderMaster] b ON a.PurOrderNo=b.PurOrderNo 
        INNER JOIN [dbo].[tbm_Status] c ON b.OrderStatus=c.StatNo 
        LEFT JOIN [dbo].[tbm_Vendor] d ON b.VendNo=d.VendNo
        WHERE a.PurOrderNo='" . $_POST['PurOrderNo'] . "' GROUP BY d.Email,b.PurOrderDate,d.VendName,c.StatName,c.FontColor";
        $paramsPreparation1 = array();
        $queryPreparation1 = sqlsrv_query($conn, $sqlPreparation1, $paramsPreparation1);
        $resultPreparation1 = sqlsrv_fetch_array($queryPreparation1, SQLSRV_FETCH_ASSOC);

        $sqlPreparation2 = "SELECT a.ItemNo,b.ItemName,SUM(a.OrderQty) AS 'OrderQty',SUM(NetPrice) AS 'NetPrice',SUM(NetAmount) AS 'NetAmount' 
        FROM [dbo].[tbt_PurchOrderDetail] a
        INNER JOIN [dbo].[tbm_ItemMaster] b ON a.ItemNo=b.ItemNo
        WHERE a.PurOrderNo='" . $_POST['PurOrderNo'] . "' GROUP BY a.ItemNo,b.ItemName
        ";
        $paramsPreparation2 = array();
        $queryPreparation2 = sqlsrv_query($conn, $sqlPreparation2, $paramsPreparation2);
        ?>

        <!--begin::Header-->
        <div class="modal-header"><b><?= select_language($_POST['lag'], 'L0291'); ?> : <?= $_POST['PurOrderNo'] ?></b></div>
        <!--end::Header-->
        <!--begin::Form-->
        <!--begin::Body-->
        <div class="modal-body">
            <div class="card card-outline card-primary">

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label"><?= select_language($_POST['lag'], 'L0240'); ?> : </label><br> <?= $resultPreparation1['PurOrderDate'] ?>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"><?= select_language($_POST['lag'], 'L0052'); ?> : </label><br><?= $resultPreparation1['VendName'] ?>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"><?= select_language($_POST['lag'], 'L0310'); ?> : </label><br> $<?= number_format($resultPreparation1['NetAmount'], 4) ?>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"><?= select_language($_POST['lag'], 'L0156'); ?> : </label><br> <font style='color: <?= $resultPreparation1['FontColor'] ?>'><?= $resultPreparation1['StatName'] ?></font>
                        </div>
                    </div>

                </div>
            </div>
            <div class="card card-outline">
                <div class="card-header">
                    <h3 class="card-title"><?= select_language($_POST['lag'], 'L0433'); ?></h3>
                </div> 
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered bg-light" id="table3">
                                <thead>
                                    <tr>
                                        <th style="text-align: center;"><?= select_language($_POST['lag'], 'L0039') ?></th>
                                        <th style="text-align: center;"><?= select_language($_POST['lag'], 'L0277') ?></th>
                                        <th style="text-align: center;"><?= select_language($_POST['lag'], 'L0268') ?></th>
                                        <th style="text-align: center;"><?= select_language($_POST['lag'], 'L0233') ?></th>
                                        <th style="text-align: center;"><?= select_language($_POST['lag'], 'L0278') ?></th>
                                        <th style="text-align: center;"><?= select_language($_POST['lag'], 'L0297') ?></th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 14px">
                                    <?php
                                    $i = 1;
                                    $sumtotal = 0;
                                    while ($resultPreparation2 = sqlsrv_fetch_array($queryPreparation2, SQLSRV_FETCH_ASSOC)) {
                                        ?>
                                        <tr>
                                            <td style="text-align: center"><?= $i ?></td>
                                            <td style="text-align: left"><?= $resultPreparation2['ItemNo'] ?></td>
                                            <td style="text-align: left"><?= $resultPreparation2['ItemName'] ?></td>
                                            <td style="text-align: right"><?= number_format($resultPreparation2['OrderQty']) ?></td>
                                            <td style="text-align: right"><?= number_format($resultPreparation2['NetPrice'], 4) ?></td>
                                            <td style="text-align: right"><?= number_format($resultPreparation2['NetAmount'], 4) ?></td>
                                        </tr>
                                        <?php
                                        $i++;
                                        $sumtotal = $sumtotal + $resultPreparation2['NetAmount'];
                                    }
                                    ?>

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4">&nbsp;</td>
                                        <td style="text-align: right"><?= select_language($_POST['lag'], 'L0440') ?></td>
                                        <td style="text-align: right"><?= number_format($sumtotal, 4) ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-primary card-outline">

                <div class="card-header">
                    <h3 class="card-title"><?= select_language($_POST['lag'], 'L0235'); ?></h3>
                </div> 
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-primary" id="btn_confirmsend" name="btn_confirmsend"><?= select_language($_POST['lag'], 'L0458') ?> <i class="fa fa-check"></i></button>

                        </div>
                    </div>
                    <div class="row">&nbsp;</div>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label"><?= select_language($_POST['lag'], 'L0455'); ?> :</label>

                            <input type="text" class="form-control" id="txt_sendto" name="">

                        </div>

                        <div class="col-md-6">
                            <label class="form-label"><?= select_language($_POST['lag'], 'L0456'); ?> :</label>
                            <select class="form-control" id="se_ccmail" name="se_ccmail">
                                <option value="" >---Select team---</option>
                                <option value="Procurement" >Procurement</option>
                                <option value="Finance" >Finance</option>
                                <option value="Logistics" >Logistics</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <label class="form-label"><?= select_language($_POST['lag'], 'L0457'); ?> :</label>
                            <textarea class="form-control" id="txt_messageoptional" name="txt_messageoptional" rows="5"></textarea>
                        </div>


                    </div>
                </div>
            </div>


        </div>
        <!--end::Body-->
        <!--begin::Footer-->
        <div class="modal-footer" style="text-align: right">
            <!--<button class="btn btn-primary" onclick="" ><?//= select_language($_POST['lag'], 'L0431') ?> <i class="fa fa-check"></i></button>-->
            <button class="btn btn-danger" onclick="select_confirmpo()"  data-dismiss="modal"><?= select_language($_POST['lag'], 'L0023') ?> <i class="fa fa-times"></i></button>
        </div>
        <?php
        sqlsrv_close($conn);
        break;
    case 'select_confirmpo':
        $conn = connect_aglt();

        $url = "http://45.136.236.233:3000/api/po/poapprove";
        $data = [
            "flag" => "se_poapprove",
            "cond" => ""
        ];

        $jsonData = json_encode($data);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        $response = curl_exec($ch);

        $responseData = json_decode($response, true);

        if (isset($responseData['data'])) {
            $count = count($responseData['data']);
        } else {
            $count = 0;
        }
        ?>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header p-2">


                        <ul class="nav nav-pills">
                            <li class="nav-item"><a class="nav-link active" href="#Pending" data-toggle="tab"><?= select_language($_POST['lag'], 'L0332'); ?></a></li>
                            <li class="nav-item"><a class="nav-link" href="#sendmail" data-toggle="tab"><?= select_language($_POST['lag'], 'L0333'); ?></a></li>
                        </ul>
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane active" id="Pending">

                                <div class="row">
                                    <div class="col-md-12 text-right">
                                        <button type="button" class="btn btn-primary" id="btn_sendselectedpo" name="btn_sendselectedpo"><?= select_language($_POST['lag'], 'L0428'); ?> <i class="fa fa-arrow-right"></i></button>

                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">



                                        <?php
                                        if ($responseData['success'] == '1') {
                                            $i = 1;
                                            ?>
                                            <div class="card card-outline">
                                                <div class="card-header">
                                                    <h3 class="card-title"><?= select_language($_POST['lag'], 'L0315'); ?></h3>
                                                </div> 
                                                <div class="card-body">

                                                    <table class="table table-bordered bg-light" id="table1" style="width: 100%">
                                                        <thead >
                                                            <tr>
                                                                <th style="text-align: center;width: 10%">
                                                                    <input type="checkbox" id="select_all" placeholder="Check All">
                                                                </th>
                                                                <th style="text-align: center">
                                                                    <?= select_language($_POST['lag'], 'L0039') ?>
                                                                </th>
                                                                <th style="text-align: center"><?= select_language($_POST['lag'], 'L0291') ?></th>
                                                                <th style="text-align: center"><?= select_language($_POST['lag'], 'L0292') ?></th>
                                                                <th style="text-align: center"><?= select_language($_POST['lag'], 'L0293') ?></th>
                                                                <th style="text-align: center"><?= select_language($_POST['lag'], 'L0197') ?></th>
                                                                <th style="text-align: center"><?= select_language($_POST['lag'], 'L0200') ?></th>
                                                                <th style="text-align: center"><?= select_language($_POST['lag'], 'L0233') ?></th>
                                                                <th style="text-align: center"><?= select_language($_POST['lag'], 'L0286') ?></th>
                                                                <th style="text-align: center"><?= select_language($_POST['lag'], 'L0297') ?></th>
                                                                <th style="text-align: center"><?= select_language($_POST['lag'], 'L0120') ?></th>
                                                                <th style="text-align: center"><?= select_language($_POST['lag'], 'L0235') ?></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody style="font-size: 14px">
                                                            <?php
                                                            foreach ($responseData['data'] as $dataResult) {
                                                                ?>
                                                                <tr>
                                                                    <td style="text-align: center;">
                                                                        <input type="checkbox" id="<?= $dataResult['PurOrderNo'] ?>"  class="po_checkbox"  data-po-id="<?= $dataResult['PurOrderNo'] ?>"> 
                                                                    </td>
                                                                    <td style="text-align: center;"><?= $i ?></td>
                                                                    <td style="text-align: center"><?= $dataResult['PurOrderNo'] ?></td>
                                                                    <td style="text-align: left"></td>
                                                                    <td style="text-align: left"><?= $dataResult['PurOrderDate'] ?></td>
                                                                    <td style="text-align: left"><?= $dataResult['PlantNo'] ?></td>
                                                                    <td style="text-align: left"><?= $dataResult['VendName'] ?></td>
                                                                    <td style="text-align: right"><?= number_format($dataResult['OrderQty']) ?></td>
                                                                    <td style="text-align: right"><?= number_format($dataResult['NetPrice'], 2) ?></td>
                                                                    <td style="text-align: right"><?= number_format($dataResult['NetAmount'], 2) ?></td>
                                                                    <td style="text-align: center;color:<?= $dataResult['FontColor'] ?> "><?= $dataResult['OrderStatus'] ?></td>
                                                                    <td style="text-align: center"><a href="#" data-toggle="modal" data-backdrop="static" data-target="#modal_confirmdetail"  onclick="select_confirmdetail('<?= $dataResult['PurOrderNo'] ?>')"><i class="fa fa-eye"></i></a></td>
                                                                </tr>

                                                                <?php
                                                                $i++;
                                                            }
                                                            ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <?php
                                        } else {
                                            echo $responseData['message'];
                                        }

                                        curl_close($ch);
                                        ?>





                                    </div>
                                    <!--end::Row-->
                                </div>

                            </div>
                            <!-- /.tab-pane -->
                            <div class="tab-pane " id="sendmail">
                                <div class="card card">
                                    <div class="card-header">
                                        <div class="row">

                                            <div class="col-md-12 ">
                                                <?= select_language($_POST['lag'], 'L0316'); ?>


                                            </div>

                                        </div>

                                    </div>
                                    <div class="card-body">
                                        <!-- Color Picker -->
                                        <div class="row">


                                            <div class="col-md-2">
                                                <label class="form-label"><?= select_language($_POST['lag'], 'L0311'); ?> :</label>
                                                <input type="date" class="form-control" id="txt_datefromsr" name="txt_datefromsr" placeholder="mm/dd/yyyy">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label"><?= select_language($_POST['lag'], 'L0312'); ?> :</label>
                                                <input type="date" class="form-control" id="txt_datetosr" name="txt_datetosr" placeholder="mm/dd/yyyy">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label"><?= select_language($_POST['lag'], 'L0291'); ?> :</label>
                                                <input type="text" class="form-control" id="txt_ponumbersr" name="txt_ponumbersr">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label"><?= select_language($_POST['lag'], 'L0200'); ?> :</label>
                                                <select class="form-control" id="se_vendorsr" name="se_vendorsr">
                                                    <option value="">---Select Vendor---</option>
                                                    <?php
                                                    $sqlVendor = "SELECT [VendNo],[VendCode] FROM [tbm_Vendor]
                        ORDER BY VendNo ASC";
                                                    $paramsVendor = array();
                                                    $queryVendor = sqlsrv_query($conn, $sqlVendor, $paramsVendor);
                                                    while ($resultVendor = sqlsrv_fetch_array($queryVendor, SQLSRV_FETCH_ASSOC)) {
                                                        ?>
                                                        <option value="<?= $resultVendor['VendNo'] ?>" ><?= $resultVendor['VendCode'] ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-md-1">
                                                <label class="form-label">&emsp;&emsp;&emsp;</label>
                                                <button type="button" class="btn btn-secondary" id="btn_search"><?= select_language($_POST['lag'], 'L0006'); ?>  <i class="fa fa-search"></i></button>
                                            </div>

                                        </div>


                                        <div class="row">&nbsp;</div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div id="div_sendmail">


                                                </div>

                                            </div>
                                            <!--end::Row-->
                                        </div>
                                    </div>

                                    <!-- /.form group -->

                                    <!-- time Picker -->

                                </div>
                            </div>
                            <!-- /.tab-pane -->


                            <!-- /.tab-pane -->
                        </div>
                        <!-- /.tab-content -->
                    </div><!-- /.card-body -->
                </div>

                <!-- /.card -->

            </div>
        </div>




        <?php
        sqlsrv_close($conn);
        break;
    case 'select_sendmail':
        $conn = connect_aglt();
        $condition = '';
        if ($_POST['datefrom'] != '' && $_POST['dateto'] != '') {
            $condition = $condition . " AND CONVERT(NVARCHAR(10),pd.[DeliveryDate],121) BETWEEN CONVERT(NVARCHAR(10),'" . $_POST['datefrom'] . "',121) AND CONVERT(NVARCHAR(10),'" . $_POST['dateto'] . "',121)";
        }
        if ($_POST['ponumber'] != '') {
            $condition = $condition . " AND pm.PurOrderNo = '" . $_POST['ponumber'] . "' ";
        }
        if ($_POST['vendor'] != '') {
            $condition = $condition . " AND pm.VendNo = '" . $_POST['vendor'] . "' ";
        }
        //echo $condition;
        $url2 = "http://45.136.236.233:3000/api/po/poapprove";
        $data2 = [
            "flag" => "se_confirmpoapprove",
            "cond" => $condition
        ];
        $jsonData2 = json_encode($data2);

        $ch2 = curl_init($url2);
        curl_setopt($ch2, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch2, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);

        curl_setopt($ch2, CURLOPT_POST, true);
        curl_setopt($ch2, CURLOPT_POSTFIELDS, $jsonData2);

        $response2 = curl_exec($ch2);

        $responseData2 = json_decode($response2, true);

        if ($responseData2['success'] == '1') {
            $i = 1;
            ?>


            <table class="table table-bordered bg-light" id="table2" style="width: 100%">
                <thead >
                    <tr>

                        <th style="text-align: center">
                            <?= select_language($_POST['lag'], 'L0039') ?>
                        </th>
                        <th style="text-align: center"><?= select_language($_POST['lag'], 'L0291') ?></th>
                        <th style="text-align: center"><?= select_language($_POST['lag'], 'L0292') ?></th>
                        <th style="text-align: center"><?= select_language($_POST['lag'], 'L0293') ?></th>
                        <th style="text-align: center"><?= select_language($_POST['lag'], 'L0197') ?></th>
                        <th style="text-align: center"><?= select_language($_POST['lag'], 'L0200') ?></th>
                        <th style="text-align: center"><?= select_language($_POST['lag'], 'L0233') ?></th>
                        <th style="text-align: center"><?= select_language($_POST['lag'], 'L0286') ?></th>
                        <th style="text-align: center"><?= select_language($_POST['lag'], 'L0297') ?></th>
                        <th style="text-align: center"><?= select_language($_POST['lag'], 'L0120') ?></th>
                        <th style="text-align: center"><?= select_language($_POST['lag'], 'L0235') ?></th>
                    </tr>
                </thead>
                <tbody style="font-size: 14px">
                    <?php
                    foreach ($responseData2['data'] as $dataResult2) {
                        ?>
                        <tr>
                            <td style="text-align: center;"><?= $i ?></td>
                            <td style="text-align: center"><?= $dataResult2['PurOrderNo'] ?></td>
                            <td style="text-align: left"></td>
                            <td style="text-align: left"><?= $dataResult2['PurOrderDate'] ?></td>
                            <td style="text-align: left"><?= $dataResult2['PlantNo'] ?></td>
                            <td style="text-align: left"><?= $dataResult2['VendName'] ?></td>
                            <td style="text-align: right"><?= number_format($dataResult2['OrderQty']) ?></td>
                            <td style="text-align: right"><?= number_format($dataResult2['NetPrice'], 2) ?></td>
                            <td style="text-align: right"><?= number_format($dataResult2['NetAmount'], 2) ?></td>
                            <td style="text-align: center;color: <?= $dataResult2['FontColor'] ?>"><?= $dataResult2['OrderStatus'] ?></td>
                            <td style="text-align: center">
                                <a href="#" data-toggle="modal" data-backdrop="static" data-target="#modal_sendmaildetail" onclick="select_sendmaildetail('<?= $dataResult2['PurOrderNo'] ?>')"><i class="fa fa-eye"></i></a>                            
                            </td>

                        </tr>
                        <?php
                        $i++;
                    }
                    ?>
                </tbody>
            </table>

            <?php
        } else {
            echo $responseData2['message'];
        }

        curl_close($ch2);
        ?>
        <?php
        sqlsrv_close($conn);
        break;

        $dataConfirm = [];

        foreach ($itemsPost as $item) {
            $parts = explode('-L-', $item);

            $po = trim($parts[0] ?? "");
            $level = trim($parts[1] ?? "");

            if ($po !== "") {
                $dataConfirm[] = [
                    "PurOrderNo" => $po,
                    "Levels" => $level,
                    "Result" => $dataResult,
                    "Remarks" => $dataRemark,
                    "ApprovalBy" => $dataUser
                ];
            }
        }
        $resultData = [
            "data" => $dataConfirm
        ];
        $sendData = json_encode($resultData, JSON_PRETTY_PRINT);

        $routeAPI = $urlAPI . $_POST['route'];
        $setAuthorization = "Bearer " . $_POST['accessToken'];

        $requestAPI = curl_init($routeAPI);
        curl_setopt($requestAPI, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($requestAPI, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($requestAPI, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json', 'Authorization: ' . $setAuthorization
        ]);
        curl_setopt($requestAPI, CURLOPT_POST, true);
        curl_setopt($requestAPI, CURLOPT_POSTFIELDS, $sendData);

        $response = curl_exec($requestAPI);
        //$responseData = json_decode($response, true);
        //echo $responseData['success'] . '||' . $responseData['message'] . '||'.$responseData['data'];
        echo $response;
        curl_close($requestAPI);

        $connFunctionClose;
        break;

    case 'save_approvalconfirm':
        $conn = connect_aglt();
        $url = "http://45.136.236.233:3000/api/po/approvalconfirm";

        $condition = " AND a.[PurOrderNo] IN ('" . str_replace(",", "','", trim($_POST['ponumber'])) . "')";

        $sqlPo = "SELECT a.PurOrderNo,b.Levels,b.ApprovalNo
        FROM [dbo].[tbt_PurchOrderMaster] a
        INNER JOIN [dbo].[tbt_PurchOrderApproval] b ON a.PurOrderNo=b.PurOrderNo
        WHERE 1=1  " . $condition . " AND b.ApprovalNo='" . $_POST['userNo'] . "'
        GROUP BY a.PurOrderNo,b.Levels,b.ApprovalNo";
        $paramsPo = array();
        $queryPo = sqlsrv_query($conn, $sqlPo, $paramsPo);

        while ($resultPo = sqlsrv_fetch_array($queryPo, SQLSRV_FETCH_ASSOC)) {
            $datar[] = [
                "PurOrderNo" => $resultPo['PurOrderNo'],
                "Levels" => $resultPo['Levels'],
                "Result" => 'AC',
                "Remarks" => '',
                "ApprovalBy" => $resultPo['ApprovalNo']
            ];
        }
        $data = [
            "data" => $datar
        ];
        //json_encode($data, JSON_PRETTY_PRINT);
        $jsonData = json_encode($data, JSON_PRETTY_PRINT);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        $response = curl_exec($ch);

        $sqlGetdate = "SELECT 
		UPPER(FORMAT(GETDATE(),'MMMM dd,yyyy HH:mm')) AS 'GETDATE' ";
        $paramsGetdate = array();
        $queryGetdate = sqlsrv_query($conn, $sqlGetdate, $paramsGetdate);
        $resultGetdate = sqlsrv_fetch_array($queryGetdate, SQLSRV_FETCH_ASSOC);

        $mail = new PHPMailer();
        $mail->CharSet = "utf-8";

        $mail->isSMTP();
        $mail->Host = "mail.aglt.co.th";
        $mail->SMTPAuth = true;
        $mail->Username = "wittawat_it@aglt.co.th";
        $mail->Password = "Wittawat_2532";

        $mail->setFrom("wittawat_it@aglt.co.th", "Mr.Kitz");
        $mail->isHTML(true);
        $mail->Subject = 'PO APPROVAL';

        // โหลด template HTML
        $template = file_get_contents('../pages/email_preparation.php');

        // แปลง JSON เป็น array
        $data_rt = json_decode($response, true);

        // จัดกลุ่มตาม Email
        $grouped = [];
        foreach ($data_rt['data'] as $row) {
            $grouped[$row['Email']][] = $row;
        }

        // ส่งเมลครั้งเดียวต่อ Email
        foreach ($grouped as $email => $rows) {
            // ใช้ชื่อจาก record แรกของ email นั้น
            $toName = $rows[0]['EmpName'];

            // สร้าง Content เป็นแถวของตาราง
            $Content = '';
            $no = 1;
            foreach ($rows as $row) {
                $Content .= '<tr>
            <td style="text-align:center;">' . $no++ . '</td>
            <td>' . $row['PlantNo'] . '</td>
            <td>' . $row['PurOrdNo'] . '</td>
        </tr>';
            }

            // สร้าง body จาก template
            $htmlContent = $template;
            $htmlContent = str_replace('{{ReferentNo}}', mt_rand(100000, 999999), $htmlContent);
            $htmlContent = str_replace('{{To}}', $toName, $htmlContent);
            $htmlContent = str_replace('{{Name}}', 'Mr.Kitz', $htmlContent);
            $htmlContent = str_replace('{{DateTime}}', $resultGetdate['GETDATE'], $htmlContent);
            $htmlContent = str_replace('{{Detail}}', '
        <table style="font-size:18px;width:100%;border-collapse:collapse;" border="1">
            <tr>
                <td style="text-align:center;"><b>No</b></td>
                <td><b>Plant No</b></td>
                <td><b>PO</b></td>
             
            </tr>' . $Content . '
        </table>', $htmlContent);

            // ตั้งค่า body และผู้รับ
            $mail->Body = $htmlContent;
            $mail->clearAddresses(); // ล้าง address เดิมก่อน
            $mail->addAddress($email, $toName);

            // ส่งเมล
            //if (!$mail->send()) {
            //     echo "Error to $email :: " . $mail->ErrorInfo . "<br>";
            // } else {
            //     echo "KMS :: Message sent to $email<br>";
            // }
        }

        echo $responseData['success'] . '|' . $responseData['message'];

        curl_close($ch);
        sqlsrv_close($conn);
        break;
    case 'select_approvalpending':
        $conn = connect_aglt();
        $url = "http://45.136.236.233:3000/api/po/poapprove";
        $data = [
            "flag" => "se_waitpoapprove",
            "cond" => " AND pav.ApprovalNo='" . $_SESSION["EmployeeCode"] . "' AND pav.ItemStatus='1' AND pm.OrderStatus=7"
        ];
        $jsonData = json_encode($data);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        $response = curl_exec($ch);

        $responseData = json_decode($response, true);

        if ($responseData['success'] == '1') {
            $i = 1;
            ?>
            <div class="card card-outline">
                <div class="card-header text-right">
                    <button type="button" class="btn btn-warning" id="btn_recheck" name="btn_recheck" fdprocessedid="p71ea">Recheck <i class="fa fa-reply"></i></button>
                    <button type="button" class="btn btn-danger" id="btn_reject" name="btn_reject" fdprocessedid="p71ea">Reject <i class="fa fa-reply"></i></button>
                    <button type="button" class="btn btn-primary" id="btn_approvedselected" name="btn_approvedselected" fdprocessedid="f3tcwn">Approved Selected <i class="fa fa-arrow-right"></i></button>
                </div> 
                <div class="card-body">

                    <table class="table table-bordered bg-light" id="table1" style="width: 100%">
                        <thead >
                            <tr>
                                <th style="text-align: center"><input type="checkbox" id="select_all" placeholder="Check All"></th>
                                <th style="text-align: center">No</th>
                                <th style="text-align: center">Order Date</th>
                                <th style="text-align: center">Delivery Date</th>
                                <th style="text-align: center">PO Number</th>
                                <th style="text-align: center">Qty</th>
                                <th style="text-align: center">Unit Price</th>
                                <th style="text-align: center">Net Amount</th>
                                <th style="text-align: center">Order Status</th>
                                <th style="text-align: center">Action</th>

                            </tr>
                        </thead>
                        <tbody style="font-size: 14px">
                            <?php
                            foreach ($responseData['data'] as $dataResult) {
                                ?>


                                <tr>
                                    <td style="text-align: center;"><input type="checkbox"  id="<?= $dataResult['PurOrderNo'] ?>"  class="po_checkbox"  data-po-id="<?= $dataResult['PurOrderNo'] ?>"></td>
                                    <td style="text-align: center;"><?= $i ?></td>
                                    <td style="text-align: center"><?= $dataResult['PurOrderDate'] ?></td>
                                    <td style="text-align: center"><?= $dataResult['DeliveryDate'] ?></td>
                                    <td style="text-align: center"><a href="#" data-toggle="modal" data-backdrop="static" data-target="#modal_potimeline"  onclick="select_potimeline('<?= $dataResult['PurOrderNo'] ?>')" title="Time Line"><?= $dataResult['PurOrderNo'] ?></a></td>
                                    <td style="text-align: right"><?= number_format($dataResult['OrderQty']) ?></td>
                                    <td style="text-align: right"><?= number_format($dataResult['NetPrice'], 2) ?></td>
                                    <td style="text-align: right"><?= number_format($dataResult['NetAmount'], 2) ?></td>
                                    <td style="text-align: center;color: <?= $dataResult['FontColor'] ?>"><?= $dataResult['OrderStatus'] ?></td>
                                    <td style="text-align: center"><a href="#" data-toggle="modal" data-backdrop="static" data-target="#modal_progressdetail"  onclick="select_progressdetail('<?= $dataResult['PurOrderNo'] ?>')"><i class="fa fa-eye"></i></a></td>

                                </tr>

                                <?php
                                $i++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php
        } else {
            echo $responseData['message'];
        }


        curl_close($ch);
        sqlsrv_close($conn);
        break;
    case 'update_renew':
        $conn = connect_aglt();
        $url = "http://45.136.236.233:3000/api/po/approvalconfirm";

        /*
          $data = [
          "data" => [[
          "PurOrderNo" => $_POST['PurOrderNo'],
          "Levels" => '2',
          "Result" => 'RN',
          "Remark" => '',
          "ApprovalBy" => $_SESSION["EmployeeCode"]
          ]]
          ];
         */
        $input = $_POST['PurOrderNo'];
        $purOrderNos = explode(",", $input);
        $data = [
            "data" => array_map(fn($no) => ["PurOrderNo" => $no, "Levels" => '', "Result" => 'R์N', "Remarks" => "TEST", "ApprovalBy" => $_SESSION["EmployeeCode"]], $purOrderNos)
        ];

        $jsonData = json_encode($data);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        $response = curl_exec($ch);
        $responseData = json_decode($response, true);
        curl_close($ch);

        ///////////////////////////////

        $sqlGetdate = "SELECT 
		UPPER(FORMAT(GETDATE(),'MMMM dd,yyyy HH:mm')) AS 'GETDATE' ";
        $paramsGetdate = array();
        $queryGetdate = sqlsrv_query($conn, $sqlGetdate, $paramsGetdate);
        $resultGetdate = sqlsrv_fetch_array($queryGetdate, SQLSRV_FETCH_ASSOC);

        $mail = new PHPMailer();
        $mail->CharSet = "utf-8";

        $mail->isSMTP();
        $mail->Host = "mail.aglt.co.th";
        $mail->SMTPAuth = true;
        $mail->Username = "wittawat_it@aglt.co.th";
        $mail->Password = "Wittawat_2532";

        $mail->setFrom("wittawat_it@aglt.co.th", "Mr.Kitz");
        $mail->isHTML(true);
        $mail->Subject = 'PO RENEW';

        // โหลด template HTML
        $template = file_get_contents('../pages/email_renew.php');

        // แปลง JSON เป็น array
        $data_rt = json_decode($response, true);

        // จัดกลุ่มตาม Email
        $grouped = [];
        foreach ($data_rt['data'] as $row) {
            $grouped[$row['Email']][] = $row;
        }

        // ส่งเมลครั้งเดียวต่อ Email
        foreach ($grouped as $email => $rows) {
            // ใช้ชื่อจาก record แรกของ email นั้น
            $toName = $rows[0]['EmpName'];

            // สร้าง Content เป็นแถวของตาราง
            $Content = '';
            $no = 1;
            foreach ($rows as $row) {
                $Content .= '<tr>
            <td style="text-align:center;">' . $no++ . '</td>
            <td>' . $row['PlantNo'] . '</td>
            <td>' . $row['PurOrdNo'] . '</td>
        </tr>';
            }

            // สร้าง body จาก template
            $htmlContent = $template;
            $htmlContent = str_replace('{{ReferentNo}}', mt_rand(100000, 999999), $htmlContent);
            $htmlContent = str_replace('{{To}}', $toName, $htmlContent);
            $htmlContent = str_replace('{{Name}}', 'Mr.Kitz', $htmlContent);
            $htmlContent = str_replace('{{DateTime}}', $resultGetdate['GETDATE'], $htmlContent);
            $htmlContent = str_replace('{{Detail}}', '
        <table style="font-size:18px;width:100%;border-collapse:collapse;" border="1">
            <tr>
                <td style="text-align:center;"><b>No</b></td>
                <td><b>Plant No</b></td>
                <td><b>PO</b></td>
             
            </tr>' . $Content . '
        </table>', $htmlContent);

            // ตั้งค่า body และผู้รับ
            $mail->Body = $htmlContent;
            $mail->clearAddresses(); // ล้าง address เดิมก่อน
            $mail->addAddress($email, $toName);

            // ส่งเมล
            //if (!$mail->send()) {
            //     echo "Error to $email :: " . $mail->ErrorInfo . "<br>";
            // } else {
            //     echo "KMS :: Message sent to $email<br>";
            // }
        }

        echo $responseData['success'] . '|' . $responseData['message'];

        sqlsrv_close($conn);
        break;
    case 'update_renew2':
        $conn = connect_aglt();
        $url = "http://45.136.236.233:3000/api/po/renew";

        /*
          $data = [
          "data" => [[
          "PurOrderNo" => $_POST['PurOrderNo'],
          "Levels" => '2',
          "Result" => 'RN',
          "Remark" => '',
          "ApprovalBy" => $_SESSION["EmployeeCode"]
          ]]
          ];
         */
        $input = $_POST['PurOrderNo'];
        $purOrderNos = explode(",", $input);
        $data = [
            "data" => array_map(fn($no) => ["PurOrderNo" => $no, "Levels" => '', "Result" => 'R์N', "Remarks" => "TEST", "ApprovalBy" => $_SESSION["EmployeeCode"]], $purOrderNos)
        ];

        $jsonData = json_encode($data);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        $response = curl_exec($ch);
        $responseData = json_decode($response, true);

        curl_close($ch);

        ///////////////////////////////

        $sqlGetdate = "SELECT 
		UPPER(FORMAT(GETDATE(),'MMMM dd,yyyy HH:mm')) AS 'GETDATE' ";
        $paramsGetdate = array();
        $queryGetdate = sqlsrv_query($conn, $sqlGetdate, $paramsGetdate);
        $resultGetdate = sqlsrv_fetch_array($queryGetdate, SQLSRV_FETCH_ASSOC);

        $mail = new PHPMailer();
        $mail->CharSet = "utf-8";

        $mail->isSMTP();
        $mail->Host = "mail.aglt.co.th";
        $mail->SMTPAuth = true;
        $mail->Username = "wittawat_it@aglt.co.th";
        $mail->Password = "Wittawat_2532";

        $mail->setFrom("wittawat_it@aglt.co.th", "Mr.Kitz");
        $mail->isHTML(true);
        $mail->Subject = 'PO RENEW';

        // โหลด template HTML
        $template = file_get_contents('../pages/email_renew.php');

        // แปลง JSON เป็น array
        $data_rt = json_decode($response, true);

        // จัดกลุ่มตาม Email
        $grouped = [];
        foreach ($data_rt['data'] as $row) {
            $grouped[$row['Email']][] = $row;
        }

        // ส่งเมลครั้งเดียวต่อ Email
        foreach ($grouped as $email => $rows) {
            // ใช้ชื่อจาก record แรกของ email นั้น
            $toName = $rows[0]['EmpName'];

            // สร้าง Content เป็นแถวของตาราง
            $Content = '';
            $no = 1;
            foreach ($rows as $row) {
                $Content .= '<tr>
            <td style="text-align:center;">' . $no++ . '</td>
            <td>' . $row['PlantNo'] . '</td>
            <td>' . $row['PurOrdNo'] . '</td>
        </tr>';
            }

            // สร้าง body จาก template
            $htmlContent = $template;
            $htmlContent = str_replace('{{ReferentNo}}', mt_rand(100000, 999999), $htmlContent);
            $htmlContent = str_replace('{{To}}', $toName, $htmlContent);
            $htmlContent = str_replace('{{Name}}', 'Mr.Kitz', $htmlContent);
            $htmlContent = str_replace('{{DateTime}}', $resultGetdate['GETDATE'], $htmlContent);
            $htmlContent = str_replace('{{Detail}}', '
        <table style="font-size:18px;width:100%;border-collapse:collapse;" border="1">
            <tr>
                <td style="text-align:center;"><b>No</b></td>
                <td><b>Plant No</b></td>
                <td><b>PO</b></td>
             
            </tr>' . $Content . '
        </table>', $htmlContent);

            // ตั้งค่า body และผู้รับ
            $mail->Body = $htmlContent;
            $mail->clearAddresses(); // ล้าง address เดิมก่อน
            $mail->addAddress($email, $toName);

            // ส่งเมล
            //if (!$mail->send()) {
            //     echo "Error to $email :: " . $mail->ErrorInfo . "<br>";
            // } else {
            //     echo "KMS :: Message sent to $email<br>";
            // }
        }

        echo $responseData['success'] . '|' . $responseData['message'];

        sqlsrv_close($conn);
        break;
    case 'update_reject2':
        $conn = connect_aglt();
        $url = "http://45.136.236.233:3000/api/po/reject";

        /* $data = [
          "data" => [[
          "PurOrderNo" => $_POST['PurOrderNo'],
          "Levels" => '2',
          "Result" => 'RJ',
          "Remark" => '',
          "ApprovalBy" => $_SESSION["EmployeeCode"]
          ]]
          ];
         */
        $input = $_POST['PurOrderNo'];
        $purOrderNos = explode(",", $input);
        $data = [
            "data" => array_map(fn($no) => ["PurOrderNo" => $no, "Levels" => '1', "Result" => 'RJ', "Remarks" => 'TEST', "ApprovalBy" => $_SESSION["EmployeeCode"]], $purOrderNos)
        ];

        $jsonData = json_encode($data);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        $response = curl_exec($ch);
        $responseData = json_decode($response, true);
        curl_close($ch);

        ///////////////////////////////

        $sqlGetdate = "SELECT 
		UPPER(FORMAT(GETDATE(),'MMMM dd,yyyy HH:mm')) AS 'GETDATE' ";
        $paramsGetdate = array();
        $queryGetdate = sqlsrv_query($conn, $sqlGetdate, $paramsGetdate);
        $resultGetdate = sqlsrv_fetch_array($queryGetdate, SQLSRV_FETCH_ASSOC);

        $mail = new PHPMailer();
        $mail->CharSet = "utf-8";

        $mail->isSMTP();
        $mail->Host = "mail.aglt.co.th";
        $mail->SMTPAuth = true;
        $mail->Username = "wittawat_it@aglt.co.th";
        $mail->Password = "Wittawat_2532";

        $mail->setFrom("wittawat_it@aglt.co.th", "Mr.Kitz");
        $mail->isHTML(true);
        $mail->Subject = 'PO REJECT';

        // โหลด template HTML
        $template = file_get_contents('../pages/email_reject.php');

        // แปลง JSON เป็น array
        $data_rt = json_decode($response, true);

        // จัดกลุ่มตาม Email
        $grouped = [];
        foreach ($data_rt['data'] as $row) {
            $grouped[$row['Email']][] = $row;
        }

        // ส่งเมลครั้งเดียวต่อ Email
        foreach ($grouped as $email => $rows) {
            // ใช้ชื่อจาก record แรกของ email นั้น
            $toName = $rows[0]['EmpName'];

            // สร้าง Content เป็นแถวของตาราง
            $Content = '';
            $no = 1;
            foreach ($rows as $row) {
                $Content .= '<tr>
            <td style="text-align:center;">' . $no++ . '</td>
            <td>' . $row['PlantNo'] . '</td>
            <td>' . $row['PurOrdNo'] . '</td>
        </tr>';
            }

            // สร้าง body จาก template
            $htmlContent = $template;
            $htmlContent = str_replace('{{ReferentNo}}', mt_rand(100000, 999999), $htmlContent);
            $htmlContent = str_replace('{{To}}', $toName, $htmlContent);
            $htmlContent = str_replace('{{Name}}', 'Mr.Kitz', $htmlContent);
            $htmlContent = str_replace('{{DateTime}}', $resultGetdate['GETDATE'], $htmlContent);
            $htmlContent = str_replace('{{Detail}}', '
        <table style="font-size:18px;width:100%;border-collapse:collapse;" border="1">
            <tr>
                <td style="text-align:center;"><b>No</b></td>
                <td><b>Plant No</b></td>
                <td><b>PO</b></td>
             
            </tr>' . $Content . '
        </table>', $htmlContent);

            // ตั้งค่า body และผู้รับ
            $mail->Body = $htmlContent;
            $mail->clearAddresses(); // ล้าง address เดิมก่อน
            $mail->addAddress($email, $toName);

            // ส่งเมล
            //if (!$mail->send()) {
            //     echo "Error to $email :: " . $mail->ErrorInfo . "<br>";
            // } else {
            //     echo "KMS :: Message sent to $email<br>";
            // }
        }

        echo $responseData['success'] . '|' . $responseData['message'];

        sqlsrv_close($conn);
        break;
    case 'update_reject':
        $conn = connect_aglt();
        $url = "http://45.136.236.233:3000/api/po/approvalconfirm";

        /* $data = [
          "data" => [[
          "PurOrderNo" => $_POST['PurOrderNo'],
          "Levels" => '2',
          "Result" => 'RJ',
          "Remark" => '',
          "ApprovalBy" => $_SESSION["EmployeeCode"]
          ]]
          ];
         */
        $input = $_POST['PurOrderNo'];
        $purOrderNos = explode(",", $input);
        $data = [
            "data" => array_map(fn($no) => ["PurOrderNo" => $no, "Levels" => '1', "Result" => 'RJ', "Remarks" => "TEST", "ApprovalBy" => $_SESSION["EmployeeCode"]], $purOrderNos)
        ];

        $jsonData = json_encode($data);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        $response = curl_exec($ch);
        $responseData = json_decode($response, true);
        curl_close($ch);

        ///////////////////////////////

        $sqlGetdate = "SELECT 
		UPPER(FORMAT(GETDATE(),'MMMM dd,yyyy HH:mm')) AS 'GETDATE' ";
        $paramsGetdate = array();
        $queryGetdate = sqlsrv_query($conn, $sqlGetdate, $paramsGetdate);
        $resultGetdate = sqlsrv_fetch_array($queryGetdate, SQLSRV_FETCH_ASSOC);

        $mail = new PHPMailer();
        $mail->CharSet = "utf-8";

        $mail->isSMTP();
        $mail->Host = "mail.aglt.co.th";
        $mail->SMTPAuth = true;
        $mail->Username = "wittawat_it@aglt.co.th";
        $mail->Password = "Wittawat_2532";

        $mail->setFrom("wittawat_it@aglt.co.th", "Mr.Kitz");
        $mail->isHTML(true);
        $mail->Subject = 'PO REJECT';

        // โหลด template HTML
        $template = file_get_contents('../pages/email_reject.php');

        // แปลง JSON เป็น array
        $data_rt = json_decode($response, true);

        // จัดกลุ่มตาม Email
        $grouped = [];
        foreach ($data_rt['data'] as $row) {
            $grouped[$row['Email']][] = $row;
        }

        // ส่งเมลครั้งเดียวต่อ Email
        foreach ($grouped as $email => $rows) {
            // ใช้ชื่อจาก record แรกของ email นั้น
            $toName = $rows[0]['EmpName'];

            // สร้าง Content เป็นแถวของตาราง
            $Content = '';
            $no = 1;
            foreach ($rows as $row) {
                $Content .= '<tr>
            <td style="text-align:center;">' . $no++ . '</td>
            <td>' . $row['PlantNo'] . '</td>
            <td>' . $row['PurOrdNo'] . '</td>
        </tr>';
            }

            // สร้าง body จาก template
            $htmlContent = $template;
            $htmlContent = str_replace('{{ReferentNo}}', mt_rand(100000, 999999), $htmlContent);
            $htmlContent = str_replace('{{To}}', $toName, $htmlContent);
            $htmlContent = str_replace('{{Name}}', 'Mr.Kitz', $htmlContent);
            $htmlContent = str_replace('{{DateTime}}', $resultGetdate['GETDATE'], $htmlContent);
            $htmlContent = str_replace('{{Detail}}', '
        <table style="font-size:18px;width:100%;border-collapse:collapse;" border="1">
            <tr>
                <td style="text-align:center;"><b>No</b></td>
                <td><b>Plant No</b></td>
                <td><b>PO</b></td>
             
            </tr>' . $Content . '
        </table>', $htmlContent);

            // ตั้งค่า body และผู้รับ
            $mail->Body = $htmlContent;
            $mail->clearAddresses(); // ล้าง address เดิมก่อน
            $mail->addAddress($email, $toName);

            // ส่งเมล
            //if (!$mail->send()) {
            //     echo "Error to $email :: " . $mail->ErrorInfo . "<br>";
            // } else {
            //     echo "KMS :: Message sent to $email<br>";
            // }
        }

        echo $responseData['success'] . '|' . $responseData['message'];

        sqlsrv_close($conn);
        break;
    case 'update_rework':
        $conn = connect_aglt();
        $url = "http://45.136.236.233:3000/api/po/approvalconfirm";

        $data = [
            "data" => [[
            "PurOrderNo" => $_POST['PurOrderNo'],
            "Levels" => '2',
            "Result" => 'RW',
            "Remark" => '',
            "ApprovalBy" => $_SESSION["EmployeeCode"]
                ]]
        ];

        $jsonData = json_encode($data);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        $response = curl_exec($ch);
        $responseData = json_decode($response, true);
        curl_close($ch);

        ///////////////////////////////

        $sqlGetdate = "SELECT 
		UPPER(FORMAT(GETDATE(),'MMMM dd,yyyy HH:mm')) AS 'GETDATE' ";
        $paramsGetdate = array();
        $queryGetdate = sqlsrv_query($conn, $sqlGetdate, $paramsGetdate);
        $resultGetdate = sqlsrv_fetch_array($queryGetdate, SQLSRV_FETCH_ASSOC);

        $mail = new PHPMailer();
        $mail->CharSet = "utf-8";

        $mail->isSMTP();
        $mail->Host = "mail.aglt.co.th";
        $mail->SMTPAuth = true;
        $mail->Username = "wittawat_it@aglt.co.th";
        $mail->Password = "Wittawat_2532";

        $mail->setFrom("wittawat_it@aglt.co.th", "Mr.Kitz");
        $mail->isHTML(true);
        $mail->Subject = 'PO REWORK';

        // โหลด template HTML
        $template = file_get_contents('../pages/email_rework.php');

        // แปลง JSON เป็น array
        $data_rt = json_decode($response, true);

        // จัดกลุ่มตาม Email
        $grouped = [];
        foreach ($data_rt['data'] as $row) {
            $grouped[$row['Email']][] = $row;
        }

        // ส่งเมลครั้งเดียวต่อ Email
        foreach ($grouped as $email => $rows) {
            // ใช้ชื่อจาก record แรกของ email นั้น
            $toName = $rows[0]['EmpName'];

            // สร้าง Content เป็นแถวของตาราง
            $Content = '';
            $no = 1;
            foreach ($rows as $row) {
                $Content .= '<tr>
            <td style="text-align:center;">' . $no++ . '</td>
            <td>' . $row['PlantNo'] . '</td>
            <td>' . $row['PurOrdNo'] . '</td>
        </tr>';
            }

            // สร้าง body จาก template
            $htmlContent = $template;
            $htmlContent = str_replace('{{ReferentNo}}', mt_rand(100000, 999999), $htmlContent);
            $htmlContent = str_replace('{{To}}', $toName, $htmlContent);
            $htmlContent = str_replace('{{Name}}', 'Mr.Kitz', $htmlContent);
            $htmlContent = str_replace('{{DateTime}}', $resultGetdate['GETDATE'], $htmlContent);
            $htmlContent = str_replace('{{Detail}}', '
        <table style="font-size:18px;width:100%;border-collapse:collapse;" border="1">
            <tr>
                <td style="text-align:center;"><b>No</b></td>
                <td><b>Plant No</b></td>
                <td><b>PO</b></td>
             
            </tr>' . $Content . '
        </table>', $htmlContent);

            // ตั้งค่า body และผู้รับ
            $mail->Body = $htmlContent;
            $mail->clearAddresses(); // ล้าง address เดิมก่อน
            $mail->addAddress($email, $toName);

            // ส่งเมล
            //if (!$mail->send()) {
            //     echo "Error to $email :: " . $mail->ErrorInfo . "<br>";
            // } else {
            //     echo "KMS :: Message sent to $email<br>";
            // }
        }

        echo $responseData['success'] . '|' . $responseData['message'];

        sqlsrv_close($conn);
        break;
    case 'delete_preparation':
        $conn = connect_aglt();
        $url = "http://45.136.236.233:3000/api/po/deletepreparation";

        $data = [
            "data" => [[
            "flag" => "del_preparation",
            "cond" => $_POST['PurOrderNo']
                ]]
        ];

        $jsonData = json_encode($data);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        $response = curl_exec($ch);
        $responseData = json_decode($response, true);
        echo $responseData['success'] . '|' . $responseData['message'];
        curl_close($ch);

        sqlsrv_close($conn);
        break;
    case 'update_confirmsend':
        $conn = connect_aglt();
        $url = "http://45.136.236.233:3000/api/po/createapproval";

        $data = [
            "createBy" => $_SESSION["EmployeeCode"],
            "data" => [
                ["PurOrderNo" => $_POST['PurOrderNo'], "Levels" => '2', "ApprovalNo" => $_POST['nextapprover2']],
                ["PurOrderNo" => $_POST['PurOrderNo'], "Levels" => '1', "ApprovalNo" => $_POST['nextapprover3']],
                ["PurOrderNo" => $_POST['PurOrderNo'], "Levels" => '0', "ApprovalNo" => $_POST['nextapprover4']]
            ]
        ];

        //json_encode($data, JSON_PRETTY_PRINT);
        $jsonData = json_encode($data, JSON_PRETTY_PRINT);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        $response = curl_exec($ch);
        $responseData = json_decode($response, true);

        curl_close($ch);

        $sqlGetdate = "SELECT 
		UPPER(FORMAT(GETDATE(),'MMMM dd,yyyy HH:mm')) AS 'GETDATE' ";
        $paramsGetdate = array();
        $queryGetdate = sqlsrv_query($conn, $sqlGetdate, $paramsGetdate);
        $resultGetdate = sqlsrv_fetch_array($queryGetdate, SQLSRV_FETCH_ASSOC);

        $mail = new PHPMailer();
        $mail->CharSet = "utf-8";

        $mail->isSMTP();
        $mail->Host = "mail.aglt.co.th";
        $mail->SMTPAuth = true;
        $mail->Username = "wittawat_it@aglt.co.th";
        $mail->Password = "Wittawat_2532";

        $mail->setFrom("wittawat_it@aglt.co.th", "Mr.Kitz");
        $mail->isHTML(true);
        $mail->Subject = 'PO APPROVAL';

        // โหลด template HTML
        $template = file_get_contents('../pages/email_preparation.php');

        // แปลง JSON เป็น array
        $data_rt = json_decode($response, true);

        // จัดกลุ่มตาม Email
        $grouped = [];
        foreach ($data_rt['data'] as $row) {
            $grouped[$row['Email']][] = $row;
        }

        // ส่งเมลครั้งเดียวต่อ Email
        foreach ($grouped as $email => $rows) {
            // ใช้ชื่อจาก record แรกของ email นั้น
            $toName = $rows[0]['EmpName'];

            // สร้าง Content เป็นแถวของตาราง
            $Content = '';
            $no = 1;
            foreach ($rows as $row) {
                $Content .= '<tr>
            <td style="text-align:center;">' . $no++ . '</td>
            <td>' . $row['PlantNo'] . '</td>
            <td>' . $row['PurOrdNo'] . '</td>
        </tr>';
            }

            // สร้าง body จาก template
            $htmlContent = $template;
            $htmlContent = str_replace('{{ReferentNo}}', mt_rand(100000, 999999), $htmlContent);
            $htmlContent = str_replace('{{To}}', $toName, $htmlContent);
            $htmlContent = str_replace('{{Name}}', 'Mr.Kitz', $htmlContent);
            $htmlContent = str_replace('{{DateTime}}', $resultGetdate['GETDATE'], $htmlContent);
            $htmlContent = str_replace('{{Detail}}', '
        <table style="font-size:18px;width:100%;border-collapse:collapse;" border="1">
            <tr>
                <td style="text-align:center;"><b>No</b></td>
                <td><b>Plant No</b></td>
                <td><b>PO</b></td>
             
            </tr>' . $Content . '
        </table>', $htmlContent);

            // ตั้งค่า body และผู้รับ
            $mail->Body = $htmlContent;
            $mail->clearAddresses(); // ล้าง address เดิมก่อน
            $mail->addAddress($email, $toName);

            // ส่งเมล
            //if (!$mail->send()) {
            //     echo "Error to $email :: " . $mail->ErrorInfo . "<br>";
            // } else {
            //     echo "KMS :: Message sent to $email<br>";
            // }
        }

        echo $responseData['success'] . '|' . $responseData['message'];

        sqlsrv_close($conn);
        break;

    case 'select_sendmail_r2':
        $conn = connect_aglt();
        $sqlMail = "SELECT TOP 1 Email
        FROM [tbm_Employee] 
        WHERE EmpCode='" . $_POST['empcode'] . "'";
        $paramsMail = array();
        $queryMail = sqlsrv_query($conn, $sqlMail, $paramsMail);
        $resultMail = sqlsrv_fetch_array($queryMail, SQLSRV_FETCH_ASSOC);
        echo $resultMail['Email'];
        sqlsrv_close($conn);
        break;
    case 'select_approvaldetail':
        $conn = connect_aglt();

        $sqlPreparation1 = "SELECT b.ProdType,d.Email,
        CONVERT(NVARCHAR(10),b.PurOrderDate,23) AS 'PurOrderDate',d.VendName,SUM(a.NetAmount) AS 'NetAmount',c.StatName,c.FontColor 
        FROM [dbo].[tbt_PurchOrderDetail] a 
        INNER JOIN [dbo].[tbt_PurchOrderMaster] b ON a.PurOrderNo=b.PurOrderNo 
        INNER JOIN [dbo].[tbm_Status] c ON b.OrderStatus=c.StatNo 
        LEFT JOIN [dbo].[tbm_Vendor] d ON b.VendNo=d.VendNo
        WHERE a.PurOrderNo='" . $_POST['PurOrderNo'] . "' GROUP BY b.ProdType,d.Email,b.PurOrderDate,d.VendName,c.StatName,c.FontColor";
        $paramsPreparation1 = array();
        $queryPreparation1 = sqlsrv_query($conn, $sqlPreparation1, $paramsPreparation1);
        $resultPreparation1 = sqlsrv_fetch_array($queryPreparation1, SQLSRV_FETCH_ASSOC);

        $sqlPreparation2 = "SELECT a.ItemNo,b.ItemName,SUM(a.OrderQty) AS 'OrderQty',SUM(NetPrice) AS 'NetPrice',SUM(NetAmount) AS 'NetAmount' 
        FROM [dbo].[tbt_PurchOrderDetail] a
        INNER JOIN [dbo].[tbm_ItemMaster] b ON a.ItemNo=b.ItemNo
        WHERE a.PurOrderNo='" . $_POST['PurOrderNo'] . "' GROUP BY a.ItemNo,b.ItemName
        ";
        $paramsPreparation2 = array();
        $queryPreparation2 = sqlsrv_query($conn, $sqlPreparation2, $paramsPreparation2);

        $sqlDeflevel2 = "SELECT TOP 1 b.EmpCode,b.FirstNameEN,b.LastNameEN,b.Email FROM [dbo].[tbt_PurchOrderApproval] a 
        INNER JOIN [dbo].[tbm_Employee] b ON a.ApprovalNo=b.EmpCode
        INNER JOIN [dbo].[tbm_Position] c ON b.PosNo=c.PosNo
        WHERE a.PurOrderNo='" . $_POST['PurOrderNo'] . "' AND a.Levels=1 ORDER BY EmpCode ASC";
        $paramsDeflevel2 = array();
        $queryDeflevel2 = sqlsrv_query($conn, $sqlDeflevel2, $paramsDeflevel2);
        $resultDeflevel2 = sqlsrv_fetch_array($queryDeflevel2, SQLSRV_FETCH_ASSOC);

        $sqlDeflevel3 = "SELECT TOp 1 b.EmpCode,b.FirstNameEN,b.LastNameEN,b.Email FROM [dbo].[tbt_PurchOrderApproval] a 
        INNER JOIN [dbo].[tbm_Employee] b ON a.ApprovalNo=b.EmpCode
        INNER JOIN [dbo].[tbm_Position] c ON b.PosNo=c.PosNo
        WHERE a.PurOrderNo='" . $_POST['PurOrderNo'] . "' AND a.Levels=0 ORDER BY EmpCode ASC";
        $paramsDeflevel3 = array();
        $queryDeflevel3 = sqlsrv_query($conn, $sqlDeflevel3, $paramsDeflevel3);
        $resultDeflevel3 = sqlsrv_fetch_array($queryDeflevel3, SQLSRV_FETCH_ASSOC);
        ?>

        <!--begin::Header-->
        <div class="modal-header"><b><?= select_language($_POST['lag'], 'L0291'); ?> : <?= $_POST['PurOrderNo'] ?></b></div>
        <!--end::Header-->
        <!--begin::Form-->
        <!--begin::Body-->
        <div class="modal-body">
            <div class="card card-outline card-primary">

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label"><?= select_language($_POST['lag'], 'L0240'); ?> : </label><br> <?= $resultPreparation1['PurOrderDate'] ?>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"><?= select_language($_POST['lag'], 'L0052'); ?> : </label><br><?= $resultPreparation1['VendName'] ?>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"><?= select_language($_POST['lag'], 'L0310'); ?> : </label><br> $<?= number_format($resultPreparation1['NetAmount'], 4) ?>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"><?= select_language($_POST['lag'], 'L0156'); ?> : </label><br> <font style="color:<?= $resultPreparation1['FontColor'] ?> "><?= $resultPreparation1['StatName'] ?></font>
                        </div>
                    </div>

                </div>
            </div>
            <div class="card card-outline">
                <div class="card-header">
                    <h3 class="card-title"><?= select_language($_POST['lag'], 'L0433'); ?></h3>
                </div> 
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered bg-light" id="table4">
                                <thead>
                                    <tr>
                                        <th style="text-align: center;"><?= select_language($_POST['lag'], 'L0039') ?></th>
                                        <th style="text-align: center;"><?= select_language($_POST['lag'], 'L0277') ?></th>
                                        <th style="text-align: center;"><?= select_language($_POST['lag'], 'L0268') ?></th>
                                        <th style="text-align: center;"><?= select_language($_POST['lag'], 'L0233') ?></th>
                                        <th style="text-align: center;"><?= select_language($_POST['lag'], 'L0278') ?></th>
                                        <th style="text-align: center;"><?= select_language($_POST['lag'], 'L0297') ?></th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 14px">
                                    <?php
                                    $i = 1;
                                    $sumtotal = 0;
                                    while ($resultPreparation2 = sqlsrv_fetch_array($queryPreparation2, SQLSRV_FETCH_ASSOC)) {
                                        ?>
                                        <tr>
                                            <td style="text-align: center"><?= $i ?></td>
                                            <td style="text-align: left"><?= $resultPreparation2['ItemNo'] ?></td>
                                            <td style="text-align: left"><?= $resultPreparation2['ItemName'] ?></td>
                                            <td style="text-align: right"><?= number_format($resultPreparation2['OrderQty']) ?></td>
                                            <td style="text-align: right"><?= number_format($resultPreparation2['NetPrice'], 4) ?></td>
                                            <td style="text-align: right"><?= number_format($resultPreparation2['NetAmount'], 4) ?></td>
                                        </tr>
                                        <?php
                                        $i++;
                                        $sumtotal = $sumtotal + $resultPreparation2['NetAmount'];
                                    }
                                    ?>

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4">&nbsp;</td>
                                        <td style="text-align: right"><?= select_language($_POST['lag'], 'L0440') ?></td>
                                        <td style="text-align: right"><?= number_format($sumtotal, 4) ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-primary card-outline">

                <div class="card-header">
                    <h3 class="card-title"><?= select_language($_POST['lag'], 'L0235'); ?></h3>
                </div> 
                <div class="card-body">

                    <?php
                    if ($resultPreparation1['ProdType'] == '3RUI') {
                        $sqlDeflevel1 = "SELECT TOP 1 b.EmpCode,b.FirstNameEN,b.LastNameEN,b.Email FROM [dbo].[tbt_PurchOrderApproval] a 
        INNER JOIN [dbo].[tbm_Employee] b ON a.ApprovalNo=b.EmpCode
        INNER JOIN [dbo].[tbm_Position] c ON b.PosNo=c.PosNo
        WHERE a.PurOrderNo='" . $_POST['PurOrderNo'] . "' AND a.Levels=1 ORDER BY EmpCode ASC";
                        $paramsDeflevel1 = array();
                        $queryDeflevel1 = sqlsrv_query($conn, $sqlDeflevel1, $paramsDeflevel1);
                        $resultDeflevel1 = sqlsrv_fetch_array($queryDeflevel1, SQLSRV_FETCH_ASSOC);
                        ?>
                        <div class="row">


                            <div class="col-md-6">
                                <label class="form-label"><?= select_language($_POST['lag'], 'L0436'); ?> :</label><br>
                                (<?= $resultDeflevel2['EmpCode'] ?>)-<?= $resultDeflevel2['FirstNameEN'] ?> <?= $resultDeflevel2['LastNameEN'] ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label"><?= select_language($_POST['lag'], 'L0437'); ?> :</label><br>
                                (<?= $resultDeflevel3['EmpCode'] ?>)-<?= $resultDeflevel3['FirstNameEN'] ?> <?= $resultDeflevel3['LastNameEN'] ?>
                            </div>

                            <div class="col-md-6">
                                &nbsp;<br>
                                <input type="checkbox" checked="" disabled=""> 
                                <?= select_language($_POST['lag'], 'L0443'); ?> <u><?= $resultDeflevel1['Email'] ?></u> <i class="fa fa-mail-bulk"></i>
                            </div>
                        </div>
                        <?php
                    } else {
                        $sqlDeflevel1 = "SELECT TOP 1 b.EmpCode,b.FirstNameEN,b.LastNameEN,b.Email FROM [dbo].[tbt_PurchOrderApproval] a 
        INNER JOIN [dbo].[tbm_Employee] b ON a.ApprovalNo=b.EmpCode
        INNER JOIN [dbo].[tbm_Position] c ON b.PosNo=c.PosNo
        WHERE a.PurOrderNo='" . $_POST['PurOrderNo'] . "' AND a.Levels=2 ORDER BY EmpCode ASC";
                        $paramsDeflevel1 = array();
                        $queryDeflevel1 = sqlsrv_query($conn, $sqlDeflevel1, $paramsDeflevel1);
                        $resultDeflevel1 = sqlsrv_fetch_array($queryDeflevel1, SQLSRV_FETCH_ASSOC);
                        ?>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label"><?= select_language($_POST['lag'], 'L0436'); ?> :</label><br>
                                (<?= $resultDeflevel1['EmpCode'] ?>)-<?= $resultDeflevel1['FirstNameEN'] ?> <?= $resultDeflevel1['LastNameEN'] ?>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label"><?= select_language($_POST['lag'], 'L0437'); ?> :</label><br>
                                (<?= $resultDeflevel2['EmpCode'] ?>)-<?= $resultDeflevel2['FirstNameEN'] ?> <?= $resultDeflevel2['LastNameEN'] ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label"><?= select_language($_POST['lag'], 'L0438'); ?> :</label><br>
                                (<?= $resultDeflevel3['EmpCode'] ?>)-<?= $resultDeflevel3['FirstNameEN'] ?> <?= $resultDeflevel3['LastNameEN'] ?>
                            </div>

                            <div class="col-md-6">
                                &nbsp;<br>
                                <input type="checkbox" checked="" disabled=""> 
                                <?= select_language($_POST['lag'], 'L0443'); ?> <u><?= $resultDeflevel1['Email'] ?></u> <i class="fa fa-mail-bulk"></i>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>


        </div>
        <!--end::Body-->
        <!--begin::Footer-->
        <div class="modal-footer" style="text-align: right">
            <button class="btn btn-danger" onclick="select_uploadspo()"  data-dismiss="modal"><?= select_language($_POST['lag'], 'L0023') ?> <i class="fa fa-times"></i></button>
        </div>

        <?php
        sqlsrv_close($conn);
        break;
    case 'select_pendingdetail':
        $conn = connect_aglt();

        $sqlPreparation1 = "SELECT b.ProdType,d.Email,
        CONVERT(NVARCHAR(10),b.PurOrderDate,23) AS 'PurOrderDate',d.VendName,SUM(a.NetAmount) AS 'NetAmount',c.StatName,c.FontColor 
        FROM [dbo].[tbt_PurchOrderDetail] a 
        INNER JOIN [dbo].[tbt_PurchOrderMaster] b ON a.PurOrderNo=b.PurOrderNo 
        INNER JOIN [dbo].[tbm_Status] c ON b.OrderStatus=c.StatNo 
        LEFT JOIN [dbo].[tbm_Vendor] d ON b.VendNo=d.VendNo
        WHERE a.PurOrderNo='" . $_POST['PurOrderNo'] . "' GROUP BY b.ProdType,d.Email,b.PurOrderDate,d.VendName,c.StatName,c.FontColor";
        $paramsPreparation1 = array();
        $queryPreparation1 = sqlsrv_query($conn, $sqlPreparation1, $paramsPreparation1);
        $resultPreparation1 = sqlsrv_fetch_array($queryPreparation1, SQLSRV_FETCH_ASSOC);

        $sqlPreparation2 = "SELECT a.ItemNo,b.ItemName,SUM(a.OrderQty) AS 'OrderQty',SUM(NetPrice) AS 'NetPrice',SUM(NetAmount) AS 'NetAmount' 
        FROM [dbo].[tbt_PurchOrderDetail] a
        INNER JOIN [dbo].[tbm_ItemMaster] b ON a.ItemNo=b.ItemNo
        WHERE a.PurOrderNo='" . $_POST['PurOrderNo'] . "' GROUP BY a.ItemNo,b.ItemName
        ";
        $paramsPreparation2 = array();
        $queryPreparation2 = sqlsrv_query($conn, $sqlPreparation2, $paramsPreparation2);
        ?>

        <!--begin::Header-->
        <div class="modal-header"><b><?= select_language($_POST['lag'], 'L0291'); ?> : <?= $_POST['PurOrderNo'] ?></b></div>
        <!--end::Header-->
        <!--begin::Form-->
        <!--begin::Body-->
        <div class="modal-body">
            <div class="card card-outline card-primary">

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label"><?= select_language($_POST['lag'], 'L0240'); ?> : </label><br> <?= $resultPreparation1['PurOrderDate'] ?>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"><?= select_language($_POST['lag'], 'L0052'); ?> : </label><br><?= $resultPreparation1['VendName'] ?>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"><?= select_language($_POST['lag'], 'L0310'); ?> : </label><br> $<?= number_format($resultPreparation1['NetAmount'], 4) ?>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"><?= select_language($_POST['lag'], 'L0156'); ?> : </label><br> <font style='color: <?= $resultPreparation1['FontColor'] ?>'><?= $resultPreparation1['StatName'] ?></font>
                        </div>
                    </div>

                </div>
            </div>
            <div class="card card-outline">
                <div class="card-header">
                    <h3 class="card-title"><?= select_language($_POST['lag'], 'L0433'); ?></h3>
                </div> 
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered bg-light" id="table3">
                                <thead>
                                    <tr>
                                        <th style="text-align: center;"><?= select_language($_POST['lag'], 'L0039') ?></th>
                                        <th style="text-align: center;"><?= select_language($_POST['lag'], 'L0277') ?></th>
                                        <th style="text-align: center;"><?= select_language($_POST['lag'], 'L0268') ?></th>
                                        <th style="text-align: center;"><?= select_language($_POST['lag'], 'L0233') ?></th>
                                        <th style="text-align: center;"><?= select_language($_POST['lag'], 'L0278') ?></th>
                                        <th style="text-align: center;"><?= select_language($_POST['lag'], 'L0297') ?></th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 14px">
                                    <?php
                                    $i = 1;
                                    $sumtotal = 0;
                                    while ($resultPreparation2 = sqlsrv_fetch_array($queryPreparation2, SQLSRV_FETCH_ASSOC)) {
                                        ?>
                                        <tr>
                                            <td style="text-align: center"><?= $i ?></td>
                                            <td style="text-align: left"><?= $resultPreparation2['ItemNo'] ?></td>
                                            <td style="text-align: left"><?= $resultPreparation2['ItemName'] ?></td>
                                            <td style="text-align: right"><?= number_format($resultPreparation2['OrderQty']) ?></td>
                                            <td style="text-align: right"><?= number_format($resultPreparation2['NetPrice'], 4) ?></td>
                                            <td style="text-align: right"><?= number_format($resultPreparation2['NetAmount'], 4) ?></td>
                                        </tr>
                                        <?php
                                        $i++;
                                        $sumtotal = $sumtotal + $resultPreparation2['NetAmount'];
                                    }
                                    ?>

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4">&nbsp;</td>
                                        <td style="text-align: right"><?= select_language($_POST['lag'], 'L0440') ?></td>
                                        <td style="text-align: right"><?= number_format($sumtotal, 4) ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-primary card-outline">

                <div class="card-header">
                    <h3 class="card-title"><?= select_language($_POST['lag'], 'L0235'); ?></h3>
                </div> 
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-primary" id="btn_confirmsend" name="btn_confirmsend"><?= select_language($_POST['lag'], 'L0434') ?> <i class="fa fa-check"></i></button>
                            <button class="btn btn-warning" disabled=""  ><?= select_language($_POST['lag'], 'L0441') ?> <i class="fa fa-reply"></i></button>
                            <!--<button class="btn btn-danger" disabled="" ><?//= select_language($_POST['lag'], 'L0435') ?> <i class="fa fa-reply"></i></button>-->
                        </div>
                    </div>
                    <?php
                    if ($resultPreparation1['ProdType'] == '3RUI') {
                        $sqlPosition_3 = "SELECT TOP 1 EmpCode,a.Email FROM [dbo].[tbm_Employee] a 
                        INNER JOIN [dbo].[tbm_PositionPattern] b ON a.PosNo=b.PosNo
                        WHERE b.Levels =1 ORDER BY a.EmpCode ASC";
                        $paramsPosition_3 = array();
                        $queryPositionr_3 = sqlsrv_query($conn, $sqlPosition_3, $paramsPosition_3);
                        $resultPositionr_3 = sqlsrv_fetch_array($queryPositionr_3, SQLSRV_FETCH_ASSOC);
                        ?>
                        <div class="row">&nbsp;</div>
                        <div class="row">


                            <div class="col-md-6">
                                <label class="form-label"><?= select_language($_POST['lag'], 'L0437'); ?> :</label>
                                <select class="form-control" id="se_nextapprover3" name="se_nextapprover3" >
                                    <?php
                                    $sqlPosition3 = "SELECT DISTINCT a.EmpCode,a.FirstNameEN,a.LastNameEN FROM [dbo].[tbm_Employee] a 
                                INNER JOIN [dbo].[tbm_PositionPattern] b ON a.PosNo=b.PosNo
                                WHERE b.Levels =1 ORDER BY EmpCode ASC";
                                    $paramsPosition3 = array();
                                    $queryPositionr3 = sqlsrv_query($conn, $sqlPosition3, $paramsPosition3);
                                    while ($resultPositionr3 = sqlsrv_fetch_array($queryPositionr3, SQLSRV_FETCH_ASSOC)) {
                                        ?>
                                        <option value="<?= $resultPositionr3['EmpCode'] ?>" >(<?= $resultPositionr3['EmpCode'] ?>)-<?= $resultPositionr3['FirstNameEN'] ?> <?= $resultPositionr3['LastNameEN'] ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label"><?= select_language($_POST['lag'], 'L0438'); ?> :</label>
                                <select class="form-control" id="se_nextapprover4" name="se_nextapprover4">
                                    <?php
                                    $sqlPosition4 = "SELECT DISTINCT a.EmpCode,a.FirstNameEN,a.LastNameEN FROM [dbo].[tbm_Employee] a 
                                INNER JOIN [dbo].[tbm_PositionPattern] b ON a.PosNo=b.PosNo
                                WHERE b.Levels = 0 ORDER BY EmpCode ASC";
                                    $paramsPosition4 = array();
                                    $queryPositionr4 = sqlsrv_query($conn, $sqlPosition4, $paramsPosition4);
                                    while ($resultPositionr4 = sqlsrv_fetch_array($queryPositionr4, SQLSRV_FETCH_ASSOC)) {
                                        ?>
                                        <option value="<?= $resultPositionr4['EmpCode'] ?>" >(<?= $resultPositionr4['EmpCode'] ?>)-<?= $resultPositionr4['FirstNameEN'] ?> <?= $resultPositionr4['LastNameEN'] ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                &nbsp;<br>
                                <input type="checkbox" checked=""> <?= select_language($_POST['lag'], 'L0443'); ?> <u><?= $resultPositionr_3['Email'] ?></u> <i class="fa fa-mail-bulk"></i>
                            </div>
                        </div>
                        <?php
                    } else {
                        $sqlPosition_not3 = "SELECT TOP 1 a.EmpCode,a.Email FROM [dbo].[tbm_Employee] a 
                        INNER JOIN [dbo].[tbm_PositionPattern] b ON a.PosNo=b.PosNo
                        WHERE b.Levels = 2 ORDER BY a.EmpCode ASC";
                        $paramsPosition_not3 = array();
                        $queryPositionr_not3 = sqlsrv_query($conn, $sqlPosition_not3, $paramsPosition_not3);
                        $resultPositionr_not3 = sqlsrv_fetch_array($queryPositionr_not3, SQLSRV_FETCH_ASSOC);
                        ?>
                        <div class="row">&nbsp;</div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label"><?= select_language($_POST['lag'], 'L0436'); ?> :</label>
                                <select class="form-control" id="se_nextapprover2" name="se_nextapprover2" >
                                    <?php
                                    $sqlPosition2 = "SELECT DISTINCT a.EmpCode,a.FirstNameEN,a.LastNameEN FROM [dbo].[tbm_Employee] a 
                                    INNER JOIN [dbo].[tbm_PositionPattern] b ON a.PosNo=b.PosNo
                                    WHERE b.Levels =2 ORDER BY EmpCode ASC";
                                    $paramsPosition2 = array();
                                    $queryPositionr2 = sqlsrv_query($conn, $sqlPosition2, $paramsPosition2);
                                    while ($resultPositionr2 = sqlsrv_fetch_array($queryPositionr2, SQLSRV_FETCH_ASSOC)) {
                                        ?>
                                        <option value="<?= $resultPositionr2['EmpCode'] ?>" >(<?= $resultPositionr2['EmpCode'] ?>)-<?= $resultPositionr2['FirstNameEN'] ?> <?= $resultPositionr2['LastNameEN'] ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>

                            </div>

                            <div class="col-md-6">
                                <label class="form-label"><?= select_language($_POST['lag'], 'L0437'); ?> :</label>
                                <select class="form-control" id="se_nextapprover3" name="se_nextapprover3">
                                    <?php
                                    $sqlPosition3 = "SELECT DISTINCT a.EmpCode,a.FirstNameEN,a.LastNameEN FROM [dbo].[tbm_Employee] a 
                                INNER JOIN [dbo].[tbm_PositionPattern] b ON a.PosNo=b.PosNo
                                WHERE b.Levels =1 ORDER BY EmpCode ASC";
                                    $paramsPosition3 = array();
                                    $queryPositionr3 = sqlsrv_query($conn, $sqlPosition3, $paramsPosition3);
                                    while ($resultPositionr3 = sqlsrv_fetch_array($queryPositionr3, SQLSRV_FETCH_ASSOC)) {
                                        ?>
                                        <option value="<?= $resultPositionr3['EmpCode'] ?>" >(<?= $resultPositionr3['EmpCode'] ?>)-<?= $resultPositionr3['FirstNameEN'] ?> <?= $resultPositionr3['LastNameEN'] ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label"><?= select_language($_POST['lag'], 'L0438'); ?> :</label>
                                <select class="form-control" id="se_nextapprover4" name="se_nextapprover4">
                                    <?php
                                    $sqlPosition4 = "SELECT  DISTINCT a.EmpCode,a.FirstNameEN,a.LastNameEN FROM [dbo].[tbm_Employee] a 
                                INNER JOIN [dbo].[tbm_PositionPattern] b ON a.PosNo=b.PosNo
                                WHERE b.Levels = 0 ORDER BY EmpCode ASC";
                                    $paramsPosition4 = array();
                                    $queryPositionr4 = sqlsrv_query($conn, $sqlPosition4, $paramsPosition4);
                                    while ($resultPositionr4 = sqlsrv_fetch_array($queryPositionr4, SQLSRV_FETCH_ASSOC)) {
                                        ?>
                                        <option value="<?= $resultPositionr4['EmpCode'] ?>" >(<?= $resultPositionr4['EmpCode'] ?>)-<?= $resultPositionr4['FirstNameEN'] ?> <?= $resultPositionr4['LastNameEN'] ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                &nbsp;<br>
                                <input type="checkbox" checked=""> <?= select_language($_POST['lag'], 'L0443'); ?> <u><?= $resultPositionr_not3['Email'] ?></u> <i class="fa fa-mail-bulk"></i>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>


        </div>
        <!--end::Body-->
        <!--begin::Footer-->
        <div class="modal-footer" style="text-align: right">
            <!--<button class="btn btn-primary" onclick="" ><?//= select_language($_POST['lag'], 'L0431') ?> <i class="fa fa-check"></i></button>-->
            <button class="btn btn-danger" onclick="select_uploadspo()"  data-dismiss="modal"><?= select_language($_POST['lag'], 'L0023') ?> <i class="fa fa-times"></i></button>
        </div>

        <?php
        sqlsrv_close($conn);
        break;
    case 'update_sendselected':
        $conn = connect_aglt();

        $url = "http://45.136.236.233:3000/api/po/createapproval";
        $input = $_POST['PurOrderNo'];
        $purOrderNos = explode(",", $input);
        $data = [
            "createBy" => $_SESSION["EmployeeCode"],
            "data" => array_map(fn($no) => ["PurOrderNo" => $no], $purOrderNos)
        ];

        //json_encode($data, JSON_PRETTY_PRINT);
        $jsonData = json_encode($data, JSON_PRETTY_PRINT);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        $response = curl_exec($ch);
        $responseData = json_decode($response, true);

        $sqlGetdate = "SELECT 
		UPPER(FORMAT(GETDATE(),'MMMM dd,yyyy HH:mm')) AS 'GETDATE' ";
        $paramsGetdate = array();
        $queryGetdate = sqlsrv_query($conn, $sqlGetdate, $paramsGetdate);
        $resultGetdate = sqlsrv_fetch_array($queryGetdate, SQLSRV_FETCH_ASSOC);

        $mail = new PHPMailer();
        $mail->CharSet = "utf-8";

        $mail->isSMTP();
        $mail->Host = "mail.aglt.co.th";
        $mail->SMTPAuth = true;
        $mail->Username = "wittawat_it@aglt.co.th";
        $mail->Password = "Wittawat_2532";

        $mail->setFrom("wittawat_it@aglt.co.th", "Mr.Kitz");
        $mail->isHTML(true);
        $mail->Subject = "PO APPROVAL";

        $template = file_get_contents('../pages/email_preparation.php');

        $json1 = '{"success":true,"message":"Insert completed","data":[{"PurOrdNo":"5070380164","PlantNo":"Z502","EmpCode":"A0044","EmpName":"Wittawat Khamjundee","Email":"wittawat_it@aglt.co.th"}]}';

        $json2 = '{
              "success": true,
              "message": "Insert completed",
              "data": [
                {
                  "PurOrdNo": "5070380122",
                  "PlantNo": "Z502",
                  "EmpCode": "A0044",
                  "EmpName": "Wittawat Khamjundee",
                  "Email": "wittawat_it@aglt.co.th"
                },
                {
                  "PurOrdNo": "5070380129",
                  "PlantNo": "Z502",
                  "EmpCode": "A0044",
                  "EmpName": "Wittawat Khamjundee",
                  "Email": "wittawat_it@aglt.co.th"
                }
              ]
        }';

        // แปลง JSON เป็น array
        $data_rt = json_decode($response, true);

        // จัดกลุ่มตาม Email
        $grouped = [];
        foreach ($data_rt['data'] as $row) {
            $grouped[$row['Email']][] = $row;
        }

        // ส่งเมลครั้งเดียวต่อ Email
        foreach ($grouped as $email => $rows) {
            // ใช้ชื่อจาก record แรกของ email นั้น
            $toName = $rows[0]['EmpName'];
            $toCode = $rows[0]['EmpCode'];
            // สร้าง Content เป็นแถวของตาราง
            $Content = '';
            $no = 1;
            foreach ($rows as $row) {
                $Content .= '<tr>
            <td style="text-align:center;">' . $no++ . '</td>
            <td>' . $row['PlantNo'] . '</td>
            <td>' . $row['PurOrdNo'] . '</td>
        </tr>';
            }

            // สร้าง body จาก template
            $htmlContent = $template;
            $htmlContent = str_replace('{{ReferentNo}}', mt_rand(100000, 999999), $htmlContent);
            $htmlContent = str_replace('{{userNo}}', $toCode, $htmlContent);
            $htmlContent = str_replace('{{To}}', $toName, $htmlContent);
            $htmlContent = str_replace('{{Name}}', 'Mr.Kitz', $htmlContent);
            $htmlContent = str_replace('{{DateTime}}', $resultGetdate['GETDATE'], $htmlContent);
            $htmlContent = str_replace('{{Detail}}', '
        <table style="font-size:18px;width:100%;border-collapse:collapse;" border="1">
            <tr>
                <td style="text-align:center;"><b>No</b></td>
                <td><b>Plant No</b></td>
                <td><b>PO</b></td>
               
            </tr>' . $Content . '
        </table>', $htmlContent);

            // ตั้งค่า body และผู้รับ
            $mail->Body = $htmlContent;
            $mail->clearAddresses(); // ล้าง address เดิมก่อน
            $mail->addAddress($email, $toName);
            $mail->send();
            // ส่งเมล
            // if (!$mail->send()) {
            //      echo "Error to $email :: " . $mail->ErrorInfo . "<br>";
            // } else {
            //      echo "KMS :: Message sent to $email<br>";
            //  }
        }

        echo $responseData['success'] . '|' . $responseData['message'];
        curl_close($ch);
        sqlsrv_close($conn);
        break;
    case 'save_uploadspo':
        $conn = connect_aglt();

        $url = "http://45.136.236.233:3000/api/po/upload";

        /*
          $obj = json_decode($_POST["myData"]);
          foreach ($obj as $dataResult) {
          $data = [
          "data" => [[
          "PurOrderDate" => DateTime::createFromFormat('d.m.Y', $dataResult->DocumentDate)->format('Y-m-d'),
          "DeliveryDate" => DateTime::createFromFormat('d.m.Y', $dataResult->DeliveryDate)->format('Y-m-d'),
          "PurOrderNo" => $dataResult->PurchaseDoc,
          "ItemId" => $dataResult->Item,
          "VendNo" => $dataResult->Vendor,
          "VendName" => $dataResult->Name,
          "ItemNo" => $dataResult->Material,
          "ItemName" => $dataResult->MaterialNumber,
          "OrderQty" => $dataResult->POQuantity,
          "Uom" => $dataResult->UOM,
          "NetPrice" => $dataResult->NetPrice,
          "PerQty" => $dataResult->Per,
          "CrcyCode" => $dataResult->Currency,
          "NetAmount" => $dataResult->NetValue,
          "SalesDocNo" => $dataResult->SalesDoc,
          "SalesDocItem" => $dataResult->SalesDocItem,
          "StatDelD" => DateTime::createFromFormat('d.m.Y', $dataResult->StartDueDate)->format('Y-m-d'),
          "CreateBy" => $dataResult->CreatedBy,
          "Device" => getenv('COMPUTERNAME'),
          ]]
          ];
          }

          echo $jsonData = json_encode($data);
         */
        //$jsonData = json_encode($data);
        $jsonData = '{"data":' . $_POST["myData2"] . '}';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        $response = curl_exec($ch);
        $responseData = json_decode($response, true);
        echo $responseData['success'] . '|' . $responseData['message'];
        curl_close($ch);

        sqlsrv_close($conn);
        break;
    case 'select_confuploadpos':
        $conn = connect_aglt();
        ?>
        <div class="row">
            <div class="col-12">

                <table class="table table-bordered bg-light" id="table5">
                    <thead>
                        <tr>

                            <th style="text-align: center;font-size: 12px;"><?= select_language($_POST['lag'], 'L0039') ?></th>
                            <th style="text-align: center;font-size: 12px;"><?= select_language($_POST['lag'], 'L0407') ?></th>
                            <th style="text-align: center;font-size: 12px;"><?= select_language($_POST['lag'], 'L0328') ?></th>
                            <th style="text-align: center;font-size: 12px;"><?= select_language($_POST['lag'], 'L0426') ?></th>
                            <th style="text-align: center;font-size: 12px;"><?= select_language($_POST['lag'], 'L0277') ?></th>
                            <th style="text-align: center;font-size: 12px;"><?= select_language($_POST['lag'], 'L0200') ?></th>
                            <th style="text-align: center;font-size: 12px;"><?= select_language($_POST['lag'], 'L0008') ?></th>
                            <th style="text-align: center;font-size: 12px;"><?= select_language($_POST['lag'], 'L0231') ?></th>
                            <th style="text-align: center;font-size: 12px;"><?= select_language($_POST['lag'], 'L0410') ?></th>
                            <th style="text-align: center;font-size: 12px;"><?= select_language($_POST['lag'], 'L0411') ?></th>
                            <th style="text-align: center;font-size: 12px;"><?= select_language($_POST['lag'], 'L0269') ?></th>
                            <th style="text-align: center;font-size: 12px;"><?= select_language($_POST['lag'], 'L0412') ?></th>
                            <th style="text-align: center;font-size: 12px;"><?= select_language($_POST['lag'], 'L0413') ?></th>
                            <th style="text-align: center;font-size: 12px;"><?= select_language($_POST['lag'], 'L0194') ?></th>
                            <th style="text-align: center;font-size: 12px;"><?= select_language($_POST['lag'], 'L0415') ?></th>
                            <th style="text-align: center;font-size: 12px;"><?= select_language($_POST['lag'], 'L0416') ?></th>
                            <th style="text-align: center;font-size: 12px;"><?= select_language($_POST['lag'], 'L0450') ?></th>
                            <th style="text-align: center;font-size: 12px;"><?= select_language($_POST['lag'], 'L0427') ?></th>
                            <th style="text-align: center;font-size: 12px;"><?= select_language($_POST['lag'], 'L0419') ?></th>
                            <th style="text-align: center;font-size: 12px;"><?= select_language($_POST['lag'], 'L0043') ?></th>
                        </tr>

                    </thead>

                </table>
            </div>
        </div>
        <?php
        sqlsrv_close($conn);
        break;
    case 'select_transactionlogs':
        $conn = connect_aglt();
        $condition = '';
        ?>
        <div class="card card-outline">
            <div class="card-body">
                <table class="table table-bordered bg-light" id="table1" style="width: 100%">
                    <thead >
                        <tr>
                            <th style="text-align: center">
                                <i class="fa fa-cog"></i>
                            </th>

                            <th style="text-align: center"><?= select_language($_POST['lag'], 'L0291') ?></th>
                            <th style="text-align: center"><?= select_language($_POST['lag'], 'L0344') ?></th>
                            <th style="text-align: center"><?= select_language($_POST['lag'], 'L0235') ?></th>
                            <th style="text-align: center"><?= select_language($_POST['lag'], 'L0229') ?></th>
                            <th style="text-align: center"><?= select_language($_POST['lag'], 'L0449') ?></th>
                        </tr>
                    </thead>
                </table>
            </div><!-- /.card-body -->
        </div>
        <?php
        sqlsrv_close($conn);
        break;
    case 'select_postatus':
        $conn = connect_aglt();
        $condition = '';
        if ($_POST['datefrom'] != '' && $_POST['dateto'] != '') {
            $condition = $condition . " AND CONVERT(NVARCHAR(10),pd.[DeliveryDate],121) BETWEEN CONVERT(NVARCHAR(10),'" . $_POST['datefrom'] . "',121) AND CONVERT(NVARCHAR(10),'" . $_POST['dateto'] . "',121)";
        }
        if ($_POST['plant'] != '') {
            $condition = $condition . " AND pm.PlantNo = '" . $_POST['plant'] . "' ";
        }
        if ($_POST['vendor'] != '') {
            $condition = $condition . " AND pm.VendNo = '" . $_POST['vendor'] . "' ";
        }
        if ($_POST['orderstatus'] != '') {
            $condition = $condition . " AND pm.OrderStatus = '" . $_POST['orderstatus'] . "' ";
        }
        //echo $condition;
        $url2 = "http://45.136.236.233:3000/api/po/poapprove";
        $data2 = [
            "flag" => "se_purchaseorder",
            "cond" => $condition
        ];
        $jsonData2 = json_encode($data2);

        $ch2 = curl_init($url2);
        curl_setopt($ch2, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch2, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);

        curl_setopt($ch2, CURLOPT_POST, true);
        curl_setopt($ch2, CURLOPT_POSTFIELDS, $jsonData2);

        $response2 = curl_exec($ch2);

        $responseData2 = json_decode($response2, true);

        if ($responseData2['success'] == '1') {
            $i = 1;
            ?>


            <table class="table table-bordered bg-light" id="table1" style="width: 100%">
                <thead >
                    <tr>

                        <th style="text-align: center">
                            <?= select_language($_POST['lag'], 'L0039') ?>
                        </th>
                        <th style="text-align: center"><?= select_language($_POST['lag'], 'L0291') ?></th>
                        <th style="text-align: center"><?= select_language($_POST['lag'], 'L0292') ?></th>
                        <th style="text-align: center"><?= select_language($_POST['lag'], 'L0293') ?></th>
                        <th style="text-align: center"><?= select_language($_POST['lag'], 'L0197') ?></th>
                        <th style="text-align: center"><?= select_language($_POST['lag'], 'L0200') ?></th>
                        <th style="text-align: center"><?= select_language($_POST['lag'], 'L0233') ?></th>
                        <th style="text-align: center"><?= select_language($_POST['lag'], 'L0286') ?></th>
                        <th style="text-align: center"><?= select_language($_POST['lag'], 'L0297') ?></th>
                        <th style="text-align: center"><?= select_language($_POST['lag'], 'L0120') ?></th>
                        <th style="text-align: center"><?= select_language($_POST['lag'], 'L0342') ?></th>
                        <th style="text-align: center"><?= select_language($_POST['lag'], 'L0343') ?></th>
                        <th style="text-align: center"><?= select_language($_POST['lag'], 'L0235') ?></th>
                    </tr>
                </thead>
                <tbody style="font-size: 14px">
                    <?php
                    foreach ($responseData2['data'] as $dataResult2) {
                        ?>
                        <tr>
                            <td style="text-align: center;"><?= $i ?></td>
                            <td style="text-align: center"><?= $dataResult2['PurOrderNo'] ?></td>
                            <td style="text-align: left"></td>
                            <td style="text-align: left"><?= $dataResult2['PurOrderDate'] ?></td>
                            <td style="text-align: left"><?= $dataResult2['PlantNo'] ?></td>
                            <td style="text-align: left"><?= $dataResult2['VendName'] ?></td>
                            <td style="text-align: right"><?= number_format($dataResult2['OrderQty']) ?></td>
                            <td style="text-align: right"><?= number_format($dataResult2['NetPrice'], 2) ?></td>
                            <td style="text-align: right"><?= number_format($dataResult2['NetAmount'], 2) ?></td>
                            <td style="text-align: center;color: <?= $dataResult2['FontColor'] ?>"><?= $dataResult2['OrderStatus'] ?></td>
                            <td style="text-align: center"><?= $dataResult2['Levels'] ?></td>
                            <td style="text-align: left"><?= $dataResult2['Duration'] ?></td>
                            <td style="text-align: center"><a href="#" data-toggle="modal" data-backdrop="static" data-target="#modal_totaldetail"  onclick="select_totaldetail('<?= $dataResult2['PurOrderNo'] ?>')"><i class="fa fa-eye"></i></a></td>
                        </tr>
                        <?php
                        $i++;
                    }
                    ?>
                </tbody>
            </table>

            <?php
        } else {
            echo $responseData2['message'];
        }

        curl_close($ch2);
        ?>
        <?php
        sqlsrv_close($conn);
        break;

    case 'select_vandorconfirmed':
        $conn = connect_aglt();

        sqlsrv_close($conn);
        break;
    case 'select_approvalslist':
        $conn = connect_aglt();
        $condition = '';

        sqlsrv_close($conn);
        break;

    case 'select_preparation':
        $conn = connect_aglt();
        $condition = '';
        ?>
        <div class="row">
            <div class="col-md-12">

                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                            <li class="nav-item"><a class="nav-link active" href="#Pending" data-toggle="tab"><?= select_language($_POST['lag'], 'L0334'); ?></a></li>
                            <li class="nav-item"><a class="nav-link" href="#SentforApproval" data-toggle="tab"><?= select_language($_POST['lag'], 'L0431'); ?></a></li>
                        </ul>
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane active" id="Pending">

                                <div class="card card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title"><?= select_language($_POST['lag'], 'L0309'); ?></h3>
                                    </div> <!-- /.card-body -->
                                    <div class="card-body">
                                        <table class="table table-bordered bg-light" id="table1">
                                            <thead >
                                                <tr>

                                                    <th style="text-align: center"><input type="checkbox"></th>
                                                    <th style="text-align: center"><?= select_language($_POST['lag'], 'L0291') ?></th>
                                                    <th style="text-align: center"><?= select_language($_POST['lag'], 'L0293') ?></th>
                                                    <th style="text-align: center"><?= select_language($_POST['lag'], 'L0197') ?></th>
                                                    <th style="text-align: center"><?= select_language($_POST['lag'], 'L0200') ?></th>
                                                    <th style="text-align: center"><?= select_language($_POST['lag'], 'L0310') ?></th>
                                                    <th style="text-align: center"><?= select_language($_POST['lag'], 'L0120') ?></th>
                                                    <th style="text-align: center"><?= select_language($_POST['lag'], 'L0235') ?></th>
                                                </tr>
                                            </thead>

                                        </table>
                                    </div><!-- /.card-body -->
                                </div>

                            </div>
                            <!-- /.tab-pane -->
                            <div class="tab-pane" id="SentforApproval">
                                <div class="card card">
                                    <div class="card-header">
                                        <div class="row">

                                            <div class="col-md-12 ">
                                                <?= select_language($_POST['lag'], 'L0308'); ?>


                                            </div>

                                        </div>

                                    </div>
                                    <div class="card-body">
                                        <!-- Color Picker -->
                                        <div class="row">


                                            <div class="col-md-2">
                                                <label class="form-label"><?= select_language($_POST['lag'], 'L0311'); ?> :</label>
                                                <input type="date" id="" name="" class="form-control" placeholder="mm/dd/yyyy">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label"><?= select_language($_POST['lag'], 'L0312'); ?> :</label>
                                                <input type="date" id="" name="" class="form-control" placeholder="mm/dd/yyyy">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label"><?= select_language($_POST['lag'], 'L0291'); ?> :</label>
                                                <input type="text" id="" name="" class="form-control">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label"><?= select_language($_POST['lag'], 'L0200'); ?> :</label>
                                                <input type="text" id="" name="" class="form-control">
                                            </div>
                                            <div class="col-md-1">
                                                <label class="form-label">&emsp;&emsp;&emsp;</label>
                                                <button type="button" class="btn btn-secondary" ><?= select_language($_POST['lag'], 'L0006'); ?>  <i class="fa fa-search"></i></button>
                                            </div>

                                        </div>


                                        <div class="row">&nbsp;</div>
                                        <div class="row">
                                            <div class="col-md-12">

                                                <table class="table table-bordered bg-light" id="table2">
                                                    <thead >
                                                        <tr>


                                                            <th style="text-align: center"><?= select_language($_POST['lag'], 'L0291') ?></th>
                                                            <th style="text-align: center"><?= select_language($_POST['lag'], 'L0293') ?></th>
                                                            <th style="text-align: center"><?= select_language($_POST['lag'], 'L0197') ?></th>
                                                            <th style="text-align: center"><?= select_language($_POST['lag'], 'L0200') ?></th>
                                                            <th style="text-align: center"><?= select_language($_POST['lag'], 'L0310') ?></th>
                                                            <th style="text-align: center"><?= select_language($_POST['lag'], 'L0313') ?></th>
                                                            <th style="text-align: center"><?= select_language($_POST['lag'], 'L0314') ?></th>
                                                            <th style="text-align: center"><?= select_language($_POST['lag'], 'L0235') ?></th>
                                                        </tr>
                                                    </thead>

                                                </table>

                                            </div>
                                            <!--end::Row-->
                                        </div>
                                    </div>

                                    <!-- /.form group -->

                                    <!-- time Picker -->

                                </div>
                            </div>
                            <!-- /.tab-pane -->


                            <!-- /.tab-pane -->
                        </div>
                        <!-- /.tab-content -->
                    </div><!-- /.card-body -->
                </div>

            </div>
        </div>
        <?php
        sqlsrv_close($conn);
        break;
    case 'select_createpo':
        $conn = connect_aglt();
        //echo $_SESSION["EmployeeCode"].'888888888888';
        ?>

        <div class="row">
            <div class="col-md-12">
                <div class="card card">
                    <div class="card-header">
                        <div class="row">

                            <div class="col-md-6 ">
                                <?= select_language($_POST['lag'], 'L0300'); ?><br><font style="font-size: 14px;color: #5a5c5a">Create PO from existing PR</font>

                            </div>
                            <div class="col-md-6 text-right">


                                <button type="button" class="btn btn-primary" disabled=""><?= select_language($_POST['lag'], 'L0211'); ?> <i class="fa fa-plus"></i></button>
                                <button type="button" class="btn btn-success" disabled=""><?= select_language($_POST['lag'], 'L0042'); ?> <i class="fa fa-arrow-down"></i></button>
                                <button type="button" class="btn btn-info" disabled=""><?= select_language($_POST['lag'], 'L0305'); ?> <i class="fa fa-arrow-up"></i></button>
                            </div>
                        </div>

                    </div>
                    <div class="card-body">
                        <!-- Color Picker -->
                        <div class="row">


                            <div class="col-md-3">
                                <label class="form-label"><?= select_language($_POST['lag'], 'L0302'); ?> :</label>
                                <select class="form-control">
                                    <option value="">---<?= select_language($_POST['lag'], 'L0303'); ?>---</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label"><?= select_language($_POST['lag'], 'L0291'); ?> :</label>
                                <input type="text" id="" name="" class="form-control" readonly="">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label"><?= select_language($_POST['lag'], 'L0240'); ?> :</label>
                                <input type="date" id="" name="" class="form-control" placeholder="mm/dd/yyyy">
                            </div>

                        </div>
                        <div class="row">&nbsp;</div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label"><?= select_language($_POST['lag'], 'L0052'); ?> :</label>
                                <input type="text" id="" name="" class="form-control">
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered bg-light" id="table1">
                                    <thead >
                                        <tr>

                                            <th style="text-align: center">
                                                <?= select_language($_POST['lag'], 'L0039') ?>
                                            </th>
                                            <th style="text-align: center"><?= select_language($_POST['lag'], 'L0444') ?></th>
                                            <th style="text-align: center"><?= select_language($_POST['lag'], 'L0268') ?></th>
                                            <th style="text-align: center"><?= select_language($_POST['lag'], 'L0233') ?></th>
                                            <th style="text-align: center"><?= select_language($_POST['lag'], 'L0286') ?></th>
                                            <th style="text-align: center"><?= select_language($_POST['lag'], 'L0287') ?></th>
                                            <th style="text-align: center"><?= select_language($_POST['lag'], 'L0156') ?></th>
                                        </tr>
                                    </thead>

                                </table>
                            </div>
                        </div>
                        <div class="row">&nbsp;</div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card card-default" >
                                    <div class="card-header">
                                        <?= select_language($_POST['lag'], 'L0345'); ?>
                                    </div>
                                    <div class="card-body">
                                        <div id="actions" class="row">
                                            <div class="col-lg-6">
                                                <div class="btn-group w-100">
                                                    <span class="btn btn-success col fileinput-button">
                                                        <i class="fas fa-plus"></i>
                                                        <span>Add files</span>
                                                    </span>
                                                    <button type="submit" class="btn btn-primary col start">
                                                        <i class="fas fa-upload"></i>
                                                        <span>Start upload</span>
                                                    </button>
                                                    <button type="reset" class="btn btn-warning col cancel">
                                                        <i class="fas fa-times-circle"></i>
                                                        <span>Cancel upload</span>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 d-flex align-items-center">
                                                <div class="fileupload-process w-100">
                                                    <div id="total-progress" class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                                                        <div class="progress-bar progress-bar-success" style="width:0%;" data-dz-uploadprogress></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="table table-striped files" id="previews">
                                            <div id="template" class="row mt-2">
                                                <div class="col-auto">
                                                    <span class="preview"><img src="data:," alt="" data-dz-thumbnail /></span>
                                                </div>
                                                <div class="col d-flex align-items-center">
                                                    <p class="mb-0">
                                                        <span class="lead" data-dz-name></span>
                                                        (<span data-dz-size></span>)
                                                    </p>
                                                    <strong class="error text-danger" data-dz-errormessage></strong>
                                                </div>
                                                <div class="col-4 d-flex align-items-center">
                                                    <div class="progress progress-striped active w-100" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                                                        <div class="progress-bar progress-bar-success" style="width:0%;" data-dz-uploadprogress></div>
                                                    </div>
                                                </div>
                                                <div class="col-auto d-flex align-items-center">
                                                    <div class="btn-group">
                                                        <button class="btn btn-primary start">
                                                            <i class="fas fa-upload"></i>
                                                            <span>Start</span>
                                                        </button>
                                                        <button data-dz-remove class="btn btn-warning cancel">
                                                            <i class="fas fa-times-circle"></i>
                                                            <span>Cancel</span>
                                                        </button>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.card-body -->

                                </div>
                                <!-- /.card -->
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title"><?= select_language($_POST['lag'], 'L0304'); ?></h3>
                                    </div> <!-- /.card-body -->
                                    <div class="card-body">
                                        <table class="table table-bordered bg-light" id="table2">
                                            <thead >
                                                <tr>

                                                    <th style="text-align: center">
                                                        <?= select_language($_POST['lag'], 'L0039') ?>
                                                    </th>
                                                    <th style="text-align: center"><?= select_language($_POST['lag'], 'L0291') ?></th>
                                                    <th style="text-align: center"><?= select_language($_POST['lag'], 'L0052') ?></th>
                                                    <th style="text-align: center"><?= select_language($_POST['lag'], 'L0240') ?></th>
                                                    <th style="text-align: center"><?= select_language($_POST['lag'], 'L0286') ?></th>
                                                    <th style="text-align: center"><?= select_language($_POST['lag'], 'L0297') ?></th>
                                                    <th style="text-align: center"><?= select_language($_POST['lag'], 'L0156') ?></th>
                                                    <th style="text-align: center"><?= select_language($_POST['lag'], 'L0235') ?></th>
                                                </tr>
                                            </thead>

                                        </table>
                                    </div><!-- /.card-body -->
                                </div>


                                <!-- /.col -->

                                <!-- /.col -->
                            </div>
                            <!--end::Row-->
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">




                            <div class="col-md-12 text-right">
                                <button type="button" class="btn btn-primary" disabled=""><?= select_language($_POST['lag'], 'L0209'); ?> <i class="fa fa-plus"></i></button>
                                <button type="button" class="btn btn-success" disabled=""><?= select_language($_POST['lag'], 'L0042'); ?> <i class="fa fa-arrow-down"></i></button>
                                <button type="button" class="btn btn-info" disabled=""><?= select_language($_POST['lag'], 'L0305'); ?> <i class="fa fa-arrow-up"></i></button>
                            </div>
                        </div>
                    </div>
                    <!-- /.form group -->

                    <!-- time Picker -->

                </div>
                <!-- /.card -->

            </div>
        </div>




        <?php
        sqlsrv_close($conn);
        break;
    case 'select_uploadspo':
        $conn = connect_aglt();

        //echo $_SESSION["EmployeeCode"].'888888888888';
        $url = "http://45.136.236.233:3000/api/po/poapprove";
        $data = [
            "flag" => "se_pendingpoapprove",
            "cond" => ''
        ];

        $jsonData = json_encode($data);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        $response = curl_exec($ch);

        $responseData = json_decode($response, true);

        if (isset($responseData['data'])) {
            $count = count($responseData['data']);
        } else {
            $count = 0;
        }
        ?>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header p-2">


                        <ul class="nav nav-pills">
                            <li class="nav-item"><a class="nav-link active" href="#Pending" data-toggle="tab"><?= select_language($_POST['lag'], 'L0334'); ?></a></li>
                            <li class="nav-item"><a class="nav-link" href="#SendforApproval" data-toggle="tab"><?= select_language($_POST['lag'], 'L0431'); ?></a></li>
                        </ul>
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane active" id="Pending">

                                <div class="row">
                                    <div class="col-md-12 text-right">
                                        <button type="button" class="btn btn-primary" onclick="newWindow('uploadconf_uploadspo.php?lag=<?= $_POST['lag'] ?>&menu=<?= $_POST['menu'] ?>')"><?= select_language($_POST['lag'], 'L0429'); ?> <i class="fa fa-arrow-up"></i></button>
                                        <button type="button" class="btn btn-primary" id="btn_sendselectedpo" name="btn_sendselectedpo"><?= select_language($_POST['lag'], 'L0428'); ?> <i class="fa fa-arrow-right"></i></button>

                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">



                                        <?php
                                        if ($responseData['success'] == '1') {
                                            $i = 1;
                                            ?>
                                            <div class="card card-outline">
                                                <div class="card-header">
                                                    <h3 class="card-title"><?= select_language($_POST['lag'], 'L0309'); ?></h3>
                                                </div> 
                                                <div class="card-body">

                                                    <table class="table table-bordered bg-light" id="table1" style="width: 100%">
                                                        <thead >
                                                            <tr>
                                                                <th style="text-align: center;width: 10%">
                                                                    <input type="checkbox" id="select_all" placeholder="Check All">
                                                                </th>
                                                                <th style="text-align: center">
                                                                    <?= select_language($_POST['lag'], 'L0039') ?>
                                                                </th>
                                                                <th style="text-align: center"><?= select_language($_POST['lag'], 'L0291') ?></th>
                                                                <th style="text-align: center"><?= select_language($_POST['lag'], 'L0292') ?></th>
                                                                <th style="text-align: center"><?= select_language($_POST['lag'], 'L0293') ?></th>
                                                                <th style="text-align: center"><?= select_language($_POST['lag'], 'L0197') ?></th>
                                                                <th style="text-align: center"><?= select_language($_POST['lag'], 'L0200') ?></th>
                                                                <th style="text-align: center"><?= select_language($_POST['lag'], 'L0233') ?></th>
                                                                <th style="text-align: center"><?= select_language($_POST['lag'], 'L0286') ?></th>
                                                                <th style="text-align: center"><?= select_language($_POST['lag'], 'L0297') ?></th>
                                                                <th style="text-align: center"><?= select_language($_POST['lag'], 'L0120') ?></th>
                                                                <th style="text-align: center"><?= select_language($_POST['lag'], 'L0235') ?></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody style="font-size: 14px">
                                                            <?php
                                                            foreach ($responseData['data'] as $dataResult) {
                                                                ?>
                                                                <tr>
                                                                    <td style="text-align: center;">
                                                                        <input type="checkbox" id="<?= $dataResult['PurOrderNo'] ?>"  class="po_checkbox"  data-po-id="<?= $dataResult['PurOrderNo'] ?>"> |
                                                                        <a href="#" style="pointer-events: none; display: inline-block;color: #81a3fa" data-toggle="modal" data-backdrop="static" data-target="#modal_updplant"  onclick="upd_plant('<?= $dataResult['PlantNo'] ?>', '<?= $dataResult['PlantName'] ?>', '<?= $dataResult['IsActive'] ?>')"><i class="fa fa-edit"></i></a> | 
                                                                        <a href="#" onclick="delete_preparation('<?= $dataResult['PurOrderNo'] ?>')"><i class="fa fa-archive" style="color: red"></i></a> 
                                                                    </td>
                                                                    <td style="text-align: center;"><?= $i ?></td>
                                                                    <td style="text-align: center"><?= $dataResult['PurOrderNo'] ?></td>
                                                                    <td style="text-align: left"></td>
                                                                    <td style="text-align: left"><?= $dataResult['PurOrderDate'] ?></td>
                                                                    <td style="text-align: left"><?= $dataResult['PlantNo'] ?></td>
                                                                    <td style="text-align: left"><?= $dataResult['VendName'] ?></td>
                                                                    <td style="text-align: right"><?= number_format($dataResult['OrderQty']) ?></td>
                                                                    <td style="text-align: right"><?= number_format($dataResult['NetPrice'], 2) ?></td>
                                                                    <td style="text-align: right"><?= number_format($dataResult['NetAmount'], 2) ?></td>
                                                                    <td style="text-align: center;color:<?= $dataResult['FontColor'] ?> "><?= $dataResult['OrderStatus'] ?></td>
                                                                    <td style="text-align: center"><a href="#" data-toggle="modal" data-backdrop="static" data-target="#modal_pendingdetail"  onclick="select_pendingdetail('<?= $dataResult['PurOrderNo'] ?>')"><i class="fa fa-eye"></i></a></td>
                                                                </tr>

                                                                <?php
                                                                $i++;
                                                            }
                                                            ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <?php
                                        } else {
                                            echo $responseData['message'];
                                        }

                                        curl_close($ch);
                                        ?>





                                    </div>
                                    <!--end::Row-->
                                </div>

                            </div>
                            <!-- /.tab-pane -->
                            <div class="tab-pane " id="SendforApproval">
                                <div class="card card">
                                    <div class="card-header">
                                        <div class="row">

                                            <div class="col-md-12 ">
                                                <?= select_language($_POST['lag'], 'L0308'); ?>


                                            </div>

                                        </div>

                                    </div>
                                    <div class="card-body">
                                        <!-- Color Picker -->
                                        <div class="row">


                                            <div class="col-md-2">
                                                <label class="form-label"><?= select_language($_POST['lag'], 'L0311'); ?> :</label>
                                                <input type="date" class="form-control" id="txt_datefromsr" name="txt_datefromsr" placeholder="mm/dd/yyyy">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label"><?= select_language($_POST['lag'], 'L0312'); ?> :</label>
                                                <input type="date" class="form-control" id="txt_datetosr" name="txt_datetosr" placeholder="mm/dd/yyyy">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label"><?= select_language($_POST['lag'], 'L0291'); ?> :</label>
                                                <input type="text" class="form-control" id="txt_ponumbersr" name="txt_ponumbersr">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label"><?= select_language($_POST['lag'], 'L0200'); ?> :</label>
                                                <select class="form-control" id="se_vendorsr" name="se_vendorsr">
                                                    <option value="">---Select Vendor---</option>
                                                    <?php
                                                    $sqlVendor = "SELECT [VendNo],[VendCode] FROM [tbm_Vendor]
                        ORDER BY VendNo ASC";
                                                    $paramsVendor = array();
                                                    $queryVendor = sqlsrv_query($conn, $sqlVendor, $paramsVendor);
                                                    while ($resultVendor = sqlsrv_fetch_array($queryVendor, SQLSRV_FETCH_ASSOC)) {
                                                        ?>
                                                        <option value="<?= $resultVendor['VendNo'] ?>" ><?= $resultVendor['VendCode'] ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-md-1">
                                                <label class="form-label">&emsp;&emsp;&emsp;</label>
                                                <button type="button" class="btn btn-secondary" id="btn_search"><?= select_language($_POST['lag'], 'L0006'); ?>  <i class="fa fa-search"></i></button>
                                            </div>

                                        </div>


                                        <div class="row">&nbsp;</div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div id="div_senforapproval">


                                                </div>

                                            </div>
                                            <!--end::Row-->
                                        </div>
                                    </div>

                                    <!-- /.form group -->

                                    <!-- time Picker -->

                                </div>
                            </div>
                            <!-- /.tab-pane -->


                            <!-- /.tab-pane -->
                        </div>
                        <!-- /.tab-content -->
                    </div><!-- /.card-body -->
                </div>

                <!-- /.card -->

            </div>
        </div>




        <?php
        sqlsrv_close($conn);
        break;
    case 'select_senforapproval':
        $conn = connect_aglt();
        $condition = '';
        if ($_POST['datefrom'] != '' && $_POST['dateto'] != '') {
            $condition = $condition . " AND CONVERT(NVARCHAR(10),pd.[DeliveryDate],121) BETWEEN CONVERT(NVARCHAR(10),'" . $_POST['datefrom'] . "',121) AND CONVERT(NVARCHAR(10),'" . $_POST['dateto'] . "',121)";
        }
        if ($_POST['ponumber'] != '') {
            $condition = $condition . " AND pm.PurOrderNo = '" . $_POST['ponumber'] . "' ";
        }
        if ($_POST['vendor'] != '') {
            $condition = $condition . " AND pm.VendNo = '" . $_POST['vendor'] . "' ";
        }
        //echo $condition;
        $url2 = "http://45.136.236.233:3000/api/po/poapprove";
        $data2 = [
            "flag" => "se_waitpoapprove",
            "cond" => $condition . ' AND pm.OrderStatus =7 '
        ];
        $jsonData2 = json_encode($data2);

        $ch2 = curl_init($url2);
        curl_setopt($ch2, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch2, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);

        curl_setopt($ch2, CURLOPT_POST, true);
        curl_setopt($ch2, CURLOPT_POSTFIELDS, $jsonData2);

        $response2 = curl_exec($ch2);

        $responseData2 = json_decode($response2, true);

        if ($responseData2['success'] == '1') {
            $i = 1;
            ?>
            <div class="card card-outline">
                <div class="card-header">
                    <h3 class="card-title"><?= select_language($_POST['lag'], 'L0212'); ?></h3>
                </div> 
                <div class="card-body">

                    <table class="table table-bordered bg-light" id="table2" style="width: 100%">
                        <thead >
                            <tr>

                                <th style="text-align: center">
                                    <?= select_language($_POST['lag'], 'L0039') ?>
                                </th>
                                <th style="text-align: center"><?= select_language($_POST['lag'], 'L0291') ?></th>
                                <th style="text-align: center"><?= select_language($_POST['lag'], 'L0292') ?></th>
                                <th style="text-align: center"><?= select_language($_POST['lag'], 'L0293') ?></th>
                                <th style="text-align: center"><?= select_language($_POST['lag'], 'L0197') ?></th>
                                <th style="text-align: center"><?= select_language($_POST['lag'], 'L0200') ?></th>
                                <th style="text-align: center"><?= select_language($_POST['lag'], 'L0233') ?></th>
                                <th style="text-align: center"><?= select_language($_POST['lag'], 'L0286') ?></th>
                                <th style="text-align: center"><?= select_language($_POST['lag'], 'L0297') ?></th>
                                <th style="text-align: center"><?= select_language($_POST['lag'], 'L0120') ?></th>
                                <th style="text-align: center"><?= select_language($_POST['lag'], 'L0235') ?></th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 14px">
                            <?php
                            foreach ($responseData2['data'] as $dataResult2) {
                                ?>


                                <tr>


                                    <td style="text-align: center;"><?= $i ?></td>
                                    <td style="text-align: center"><?= $dataResult2['PurOrderNo'] ?></td>
                                    <td style="text-align: left"></td>
                                    <td style="text-align: left"><?= $dataResult2['PurOrderDate'] ?></td>
                                    <td style="text-align: left"><?= $dataResult2['PlantNo'] ?></td>
                                    <td style="text-align: left"><?= $dataResult2['VendName'] ?></td>
                                    <td style="text-align: right"><?= number_format($dataResult2['OrderQty']) ?></td>
                                    <td style="text-align: right"><?= number_format($dataResult2['NetPrice'], 2) ?></td>
                                    <td style="text-align: right"><?= number_format($dataResult2['NetAmount'], 2) ?></td>
                                    <td style="text-align: center;color: <?= $dataResult2['FontColor'] ?>"><?= $dataResult2['OrderStatus'] ?></td>
                                    <td style="text-align: center"><a href="#" data-toggle="modal" data-backdrop="static" data-target="#modal_approvaldetail"  onclick="select_approvaldetail('<?= $dataResult2['PurOrderNo'] ?>')"><i class="fa fa-eye"></i></a></td>

                                </tr>

                                <?php
                                $i++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php
        } else {
            echo $responseData2['message'];
        }

        curl_close($ch2);
        ?>
        <?php
        sqlsrv_close($conn);
        break;
    case 'select_createpr':
        $conn = connect_aglt();
        $condition = '';
        ?>

        <div class="row">
            <div class="col-md-12">
                <div class="card card">
                    <div class="card-header">
                        <div class="row">

                            <div class="col-md-6 ">
                                <?= select_language($_POST['lag'], 'L0306'); ?>

                            </div>
                            <div class="col-md-6 text-right">
                                <button type="button" class="btn btn-primary" disabled=""><?= select_language($_POST['lag'], 'L0281'); ?> <i class="fa fa-plus"></i></button>
                            </div>
                        </div>

                    </div>
                    <div class="card-body">
                        <!-- Color Picker -->
                        <div class="row">


                            <div class="col-md-3">
                                <label class="form-label"><?= select_language($_POST['lag'], 'L0282'); ?> :</label>
                                <input type="text" id="" name="" class="form-control" readonly="">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label"><?= select_language($_POST['lag'], 'L0240'); ?> :</label>
                                <input type="date" id="" name="" class="form-control" placeholder="mm/dd/yyyy">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label"><?= select_language($_POST['lag'], 'L0052'); ?> :</label>
                                <select class="form-control">
                                    <option value="">---Select Supplier---</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label"><?= select_language($_POST['lag'], 'L0283'); ?> :</label>
                                <input type="text" id="" name="" class="form-control">
                            </div>
                        </div>
                        <div class="row">&nbsp;</div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card card-default">
                                    <div class="card-header">
                                        <?= select_language($_POST['lag'], 'L0345'); ?>
                                    </div>
                                    <div class="card-body">
                                        <div id="actions" class="row">
                                            <div class="col-lg-6">
                                                <div class="btn-group w-100">
                                                    <span class="btn btn-success col fileinput-button">
                                                        <i class="fas fa-plus"></i>
                                                        <span>Add files</span>
                                                    </span>
                                                    <button type="submit" class="btn btn-primary col start">
                                                        <i class="fas fa-upload"></i>
                                                        <span>Start upload</span>
                                                    </button>
                                                    <button type="reset" class="btn btn-warning col cancel">
                                                        <i class="fas fa-times-circle"></i>
                                                        <span>Cancel upload</span>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 d-flex align-items-center">
                                                <div class="fileupload-process w-100">
                                                    <div id="total-progress" class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                                                        <div class="progress-bar progress-bar-success" style="width:0%;" data-dz-uploadprogress></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="table table-striped files" id="previews">
                                            <div id="template" class="row mt-2">
                                                <div class="col-auto">
                                                    <span class="preview"><img src="data:," alt="" data-dz-thumbnail /></span>
                                                </div>
                                                <div class="col d-flex align-items-center">
                                                    <p class="mb-0">
                                                        <span class="lead" data-dz-name></span>
                                                        (<span data-dz-size></span>)
                                                    </p>
                                                    <strong class="error text-danger" data-dz-errormessage></strong>
                                                </div>
                                                <div class="col-4 d-flex align-items-center">
                                                    <div class="progress progress-striped active w-100" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                                                        <div class="progress-bar progress-bar-success" style="width:0%;" data-dz-uploadprogress></div>
                                                    </div>
                                                </div>
                                                <div class="col-auto d-flex align-items-center">
                                                    <div class="btn-group">
                                                        <button class="btn btn-primary start">
                                                            <i class="fas fa-upload"></i>
                                                            <span>Start</span>
                                                        </button>
                                                        <button data-dz-remove class="btn btn-warning cancel">
                                                            <i class="fas fa-times-circle"></i>
                                                            <span>Cancel</span>
                                                        </button>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.card-body -->

                                </div>
                                <!-- /.card -->
                            </div>
                        </div>

                        <div class="row">&nbsp;</div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title"><?= select_language($_POST['lag'], 'L0284'); ?></h3>
                                    </div> <!-- /.card-body -->
                                    <div class="card-body">
                                        <table class="table table-bordered bg-light" id="table1">
                                            <thead >
                                                <tr>

                                                    <th style="text-align: center">
                                                        <?= select_language($_POST['lag'], 'L0039') ?>
                                                    </th>
                                                    <th style="text-align: center"><?= select_language($_POST['lag'], 'L0284') ?></th>
                                                    <th style="text-align: center"><?= select_language($_POST['lag'], 'L0285') ?></th>
                                                    <th style="text-align: center"><?= select_language($_POST['lag'], 'L0233') ?></th>
                                                    <th style="text-align: center"><?= select_language($_POST['lag'], 'L0286') ?></th>
                                                    <th style="text-align: center"><?= select_language($_POST['lag'], 'L0287') ?></th>
                                                    <th style="text-align: center"><?= select_language($_POST['lag'], 'L0235') ?></th>
                                                </tr>
                                            </thead>

                                        </table>
                                    </div><!-- /.card-body -->
                                </div>


                                <!-- /.col -->

                                <!-- /.col -->
                            </div>
                            <!--end::Row-->
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">




                            <div class="col-md-12 text-right">
                                <button type="button" class="btn btn-primary" disabled=""><?= select_language($_POST['lag'], 'L0209'); ?> <i class="fa fa-plus"></i></button>
                                <button type="button" class="btn btn-success" disabled=""><?= select_language($_POST['lag'], 'L0042'); ?> <i class="fa fa-arrow-down"></i></button>
                                <button type="button" class="btn btn-info" disabled=""><?= select_language($_POST['lag'], 'L0305'); ?> <i class="fa fa-arrow-up"></i></button>

                            </div>
                        </div>
                    </div>
                    <!-- /.form group -->

                    <!-- time Picker -->

                </div>
                <!-- /.card -->

            </div>
        </div>




        <?php
        sqlsrv_close($conn);
        break;
    case 'select_deliverynotes':
        $conn = connect_aglt();
        $condition = '';
        ?>

        <div class="row">
            <div class="col-md-12">
                <div class="card card">
                    <div class="card-header">
                        <div class="row">

                            <div class="col-md-6 ">
                                <?= select_language($_POST['lag'], 'L0262'); ?><br>
                                <font style="font-size: 12px;color: #BEBEBE"><?= select_language($_POST['lag'], 'L0263'); ?></font>

                            </div>
                            <div class="col-md-6 text-right">
                                <button type="button" class="btn btn-primary" disabled=""><?= select_language($_POST['lag'], 'L0266'); ?> <i class="fa fa-plus"></i></button>
                            </div>
                        </div>

                    </div>
                    <div class="card-body">
                        <!-- Color Picker -->
                        <div class="row">


                            <div class="col-md-3">
                                <label class="form-label"><?= select_language($_POST['lag'], 'L0265'); ?> :</label>
                                <input type="text" id="" name="" class="form-control" readonly="">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label"><?= select_language($_POST['lag'], 'L0240'); ?> :</label>
                                <input type="date" id="" name="" class="form-control" placeholder="mm/dd/yyyy">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label"><?= select_language($_POST['lag'], 'L0066'); ?> :</label>
                                <select class="form-control">
                                    <option value="">---Select Type---</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label"><?= select_language($_POST['lag'], 'L0264'); ?> :</label>
                                <input type="text" id="" name="" class="form-control">
                            </div>
                        </div>

                        <div class="row">


                            <div class="col-md-3">
                                <label class="form-label"><?= select_language($_POST['lag'], 'L0267'); ?> :</label>
                                <input type="text" id="" name="" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"><?= select_language($_POST['lag'], 'L0268'); ?> :</label>
                                <input type="text" id="" name="" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label"><?= select_language($_POST['lag'], 'L0233'); ?> :</label>
                                <input type="number" id="" name="" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label"><?= select_language($_POST['lag'], 'L0269'); ?> :</label>
                                <input type="number" id="" name="" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label"><?= select_language($_POST['lag'], 'L0270'); ?> :</label>
                                <input type="number" id="" name="" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label"><?= select_language($_POST['lag'], 'L0235'); ?> :</label><br>
                                <input type="checkbox" id="" name="" >
                            </div>
                        </div>
                        <div class="row">&nbsp;</div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title"><?= select_language($_POST['lag'], 'L0206'); ?></h3>
                                    </div> <!-- /.card-body -->
                                    <div class="card-body">
                                        <table class="table table-bordered bg-light" id="table1">
                                            <thead >
                                                <tr>

                                                    <th style="text-align: center">
                                                        <?= select_language($_POST['lag'], 'L0039') ?>
                                                    </th>
                                                    <th style="text-align: center"><?= select_language($_POST['lag'], 'L0271') ?></th>
                                                    <th style="text-align: center"><?= select_language($_POST['lag'], 'L0066') ?></th>
                                                    <th style="text-align: center"><?= select_language($_POST['lag'], 'L0272') ?></th>
                                                    <th style="text-align: center"><?= select_language($_POST['lag'], 'L0240') ?></th>
                                                    <th style="text-align: center"><?= select_language($_POST['lag'], 'L0273') ?></th>
                                                    <th style="text-align: center"><?= select_language($_POST['lag'], 'L0156') ?></th>
                                                    <th style="text-align: center"><?= select_language($_POST['lag'], 'L0235') ?></th>
                                                </tr>
                                            </thead>

                                        </table>
                                    </div><!-- /.card-body -->
                                </div>


                                <!-- /.col -->

                                <!-- /.col -->
                            </div>
                            <!--end::Row-->
                        </div>
                    </div>
                    <!-- /.form group -->

                    <!-- time Picker -->

                </div>
                <!-- /.card -->

            </div>
        </div>




        <?php
        sqlsrv_close($conn);
        break;
    case 'select_quatationslist':
        $conn = connect_aglt();
        $condition = '';
        ?>

        <div class="card card-outline">

            <div class="card-body">
                <table class="table table-bordered bg-light" id="table1" style="width: 100%">
                    <thead >
                        <tr>

                            <th style="text-align: center"><?= select_language($_POST['lag'], 'L0275') ?></th>
                            <th style="text-align: center"><?= select_language($_POST['lag'], 'L0276') ?></th>
                            <th style="text-align: center"><?= select_language($_POST['lag'], 'L0277') ?></th>
                            <th style="text-align: center"><?= select_language($_POST['lag'], 'L0052') ?></th>
                            <th style="text-align: center"><?= select_language($_POST['lag'], 'L0193') ?></th>
                            <th style="text-align: center"><?= select_language($_POST['lag'], 'L0286') ?></th>
                            <th style="text-align: center"><?= select_language($_POST['lag'], 'L0279') ?></th>
                            <th style="text-align: center"><?= select_language($_POST['lag'], 'L0156') ?></th>
                            <th style="text-align: center"><?= select_language($_POST['lag'], 'L0280') ?></th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 14px">
                        <tr>
                            <td style="text-align: center">RFQ-2601-12345</td>
                            <td style="text-align: center">SPEC-2026-001</td>
                            <td>Widget Pro X Assembly</td>
                            <td>Alpha Precision</td>
                            <td style="text-align: center"><font style="background-color: #f0f9ff;color:#1e40af ">Mechanical</font></td>
                            <td style="text-align: center"><font style="color: #3b82f6"><b>฿1,150</b></font></td>
                            <td style="text-align: center">14 days</td>
                            <td style="text-align: center"><font style="background-color: #e5f8f3;color: #047857">Received</font></td>
                            <td style="text-align: center">2026-01-15</td>

                        </tr>
                    </tbody>
                </table>
            </div><!-- /.card-body -->
        </div>


        <?php
        sqlsrv_close($conn);
        break;

    case 'select_quatationrequest':
        $conn = connect_aglt();
        ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-12 ">
                                <?= select_language($_POST['lag'], 'L0204'); ?>

                            </div>

                        </div>

                    </div>
                    <div class="card-body">
                        <!-- Color Picker -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="stepper" id="stepper">
                                    <div class="step active"><span><?= select_language($_POST['lag'], 'L0251') ?></span></div>
                                    <div class="step"><span><?= select_language($_POST['lag'], 'L0252') ?></span></div>
                                    <div class="step"><span><?= select_language($_POST['lag'], 'L0253') ?></span></div>
                                    <div class="step"><span><?= select_language($_POST['lag'], 'L0254') ?></span></div>
                                </div>

                                <!-- Card Form Step 1 -->
                                <div class="card card-outline card-primary" id="formStep1">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-6 "><h3 class="card-title"><?= select_language($_POST['lag'], 'L0251') ?></h3> </div>
                                            <div class="col-6 text-right"></div>
                                        </div>

                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="form-label"><?= select_language($_POST['lag'], 'L0255'); ?> :</label>
                                                <select class="form-control">
                                                    <option value="">---Select Specification---</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">&nbsp;</label><br>
                                                <button type="button" class="btn btn-primary" disabled=""><?= select_language($_POST['lag'], 'L0257') ?> <i class="fa fa-arrow-down"></i></button>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer" style="background-color: white">
                                        <div class="row">
                                            <div class="col-md-12 text-right">
                                                <button type="button" class="btn btn-primary" onclick="nextStep('1')"><?= select_language($_POST['lag'], 'L0117') ?> <i class="fas fa-arrow-right"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card Form Step 2 -->

                                <div class="card d-none card-outline" id="formStep2">
                                    <div class="card-header">
                                        <h3 class="card-title"><?= select_language($_POST['lag'], 'L0252') ?></h3>
                                    </div>
                                    <div class="card-body">

                                    </div>
                                    <div class="card-footer" style="background-color: white">
                                        <div class="row">
                                            <div class="col-md-6 text-left">
                                                <button type="button" class="btn btn-secondary" onclick="prevStep()"><i class="fas fa-arrow-left"></i> <?= select_language($_POST['lag'], 'L0149') ?></button>                     
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <button type="button" class="btn btn-primary" onclick="nextStep('2')"><?= select_language($_POST['lag'], 'L0117') ?> <i class="fas fa-arrow-right"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="card d-none card-outline" id="formStep3">
                                    <div class="card-header">
                                        <h3 class="card-title"><?= select_language($_POST['lag'], 'L0253') ?></h3>
                                    </div>
                                    <div class="card-body">


                                    </div>
                                    <div class="card-footer" style="background-color: white">
                                        <div class="row">
                                            <div class="col-md-6 text-left">
                                                <button type="button" class="btn btn-secondary" onclick="prevStep()"><i class="fas fa-arrow-left"></i> <?= select_language($_POST['lag'], 'L0149') ?></button>                     
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <button type="button" class="btn btn-primary" onclick="nextStep('3')"><?= select_language($_POST['lag'], 'L0117') ?> <i class="fas fa-arrow-right"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card d-none card-outline" id="formStep4">
                                    <div class="card-header">
                                        <h3 class="card-title"><?= select_language($_POST['lag'], 'L0147') ?></h3>
                                    </div>
                                    <div class="card-body">


                                    </div>
                                    <div class="card-footer" style="background-color: white">
                                        <div class="row">
                                            <div class="col-md-6 text-left">
                                                <button type="button" class="btn btn-secondary" onclick="prevStep()"><i class="fas fa-arrow-left"></i> <?= select_language($_POST['lag'], 'L0149') ?></button>                     
                                            </div>

                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <?php
        sqlsrv_close($conn);
        break;

    case 'select_specificationentrydetail':
        $conn = connect_aglt();
        $condition = '';
        ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6 ">
                                <button type="button" class="btn btn-primary" disabled=""><?= select_language($_POST['lag'], 'L0237'); ?> <i class="fa fa-check"></i></button>
                                <button type="button" class="btn btn-primary" disabled=""><?= select_language($_POST['lag'], 'L0238'); ?> <i class="fa fa-arrow-right"></i></button>
                            </div>
                            <div class="col-md-6 text-right">
                                <font style="font-size: 12px;color: #BEBEBE"><?= select_language($_POST['lag'], 'L0236'); ?></font>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Color Picker -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title"><?= select_language($_POST['lag'], 'L0250'); ?></h3>
                                    </div> <!-- /.card-body -->
                                    <div class="card-body">
                                        <table class="table table-bordered bg-light" id="table2">
                                            <thead >
                                                <tr>

                                                    <th style="text-align: center">
                                                        <?= select_language($_POST['lag'], 'L0039') ?>
                                                    </th>
                                                    <th style="text-align: center"><?= select_language($_POST['lag'], 'L0239') ?></th>
                                                    <th style="text-align: center"><?= select_language($_POST['lag'], 'L0240') ?></th>
                                                    <th style="text-align: center"><?= select_language($_POST['lag'], 'L0241') ?></th>
                                                    <th style="text-align: center"><?= select_language($_POST['lag'], 'L0242') ?></th>
                                                    <th style="text-align: center"><?= select_language($_POST['lag'], 'L0243') ?></th>
                                                    <th style="text-align: center"><?= select_language($_POST['lag'], 'L0156') ?></th>
                                                    <th style="text-align: center"><?= select_language($_POST['lag'], 'L0235') ?></th>
                                                </tr>
                                            </thead>

                                        </table>
                                    </div>
                                </div>

                                <!-- /.col -->

                                <!-- /.col -->
                            </div>
                            <!--end::Row-->
                        </div>
                    </div>
                    <!-- /.form group -->

                    <!-- time Picker -->

                </div>
                <!-- /.card -->

            </div>
        </div>


        <?php
        sqlsrv_close($conn);
        break;
    case 'select_specificationentry':
        $conn = connect_aglt();
        $condition = '';
        ?>

        <div class="row">
            <div class="col-md-12">
                <div class="card card">
                    <div class="card-header">
                        <div class="row">

                            <div class="col-md-6 ">
                                <?= select_language($_POST['lag'], 'L0223'); ?><br>
                                <font style="font-size: 12px;color: #BEBEBE"><?= select_language($_POST['lag'], 'L0224'); ?></font>

                            </div>
                            <div class="col-md-6 text-right">
                                <button type="button" class="btn btn-primary" disabled=""><?= select_language($_POST['lag'], 'L0244'); ?> <i class="fa fa-plus"></i></button>
                            </div>
                        </div>

                    </div>
                    <div class="card-body">
                        <!-- Color Picker -->
                        <div class="row">


                            <div class="col-md-3">
                                <label class="form-label"><?= select_language($_POST['lag'], 'L0225'); ?> :</label>
                                <input type="text" id="" name="" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label"><?= select_language($_POST['lag'], 'L0226'); ?> :</label>
                                <input type="date" id="" name="" class="form-control" placeholder="mm/dd/yyyy">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label"><?= select_language($_POST['lag'], 'L0227'); ?> :</label>
                                <input type="text" id="" name="" class="form-control">
                            </div>
                        </div>

                        <div class="row">


                            <div class="col-md-3">
                                <label class="form-label"><?= select_language($_POST['lag'], 'L0024'); ?> :</label>
                                <select class="form-control">
                                    <option value="">---<?= select_language($_POST['lag'], 'L0249'); ?>---</option>

                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label"><?= select_language($_POST['lag'], 'L0228'); ?> :</label>
                                <select class="form-control">
                                    <option value="">---Select Priority---</option>
                                    <option value="Normal"><?= select_language($_POST['lag'], 'L0246'); ?></option>
                                    <option value="Urgent"><?= select_language($_POST['lag'], 'L0247'); ?></option>
                                    <option value="Critical"><?= select_language($_POST['lag'], 'L0248'); ?></option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label"><?= select_language($_POST['lag'], 'L0156'); ?> :</label>
                                <input type="text" id="" name="" class="form-control" value="Draft" disabled="">
                            </div>
                        </div>
                        <div class="row">&nbsp;</div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title"><?= select_language($_POST['lag'], 'L0229'); ?></h3>
                                    </div> <!-- /.card-body -->
                                    <div class="card-body">
                                        <table class="table table-bordered bg-light" id="table1">
                                            <thead >
                                                <tr>

                                                    <th style="text-align: center">
                                                        <?= select_language($_POST['lag'], 'L0039') ?>
                                                    </th>
                                                    <th style="text-align: center"><?= select_language($_POST['lag'], 'L0230') ?></th>
                                                    <th style="text-align: center"><?= select_language($_POST['lag'], 'L0231') ?></th>
                                                    <th style="text-align: center"><?= select_language($_POST['lag'], 'L0232') ?></th>
                                                    <th style="text-align: center"><?= select_language($_POST['lag'], 'L0233') ?></th>
                                                    <th style="text-align: center"><?= select_language($_POST['lag'], 'L0199') ?></th>
                                                    <th style="text-align: center"><?= select_language($_POST['lag'], 'L0234') ?></th>
                                                    <th style="text-align: center"><?= select_language($_POST['lag'], 'L0156') ?></th>
                                                    <th style="text-align: center"><?= select_language($_POST['lag'], 'L0235') ?></th>
                                                </tr>
                                            </thead>

                                        </table>
                                    </div><!-- /.card-body -->
                                </div>


                                <!-- /.col -->

                                <!-- /.col -->
                            </div>
                            <!--end::Row-->
                        </div>
                    </div>
                    <!-- /.form group -->

                    <!-- time Picker -->

                </div>
                <!-- /.card -->

            </div>
        </div>




        <?php
        sqlsrv_close($conn);
        break;

    default: {
            echo "Data Not Found !";
        }
}
?>