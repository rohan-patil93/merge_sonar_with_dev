    <div class="width-800 m-auto" id="printDiv" style="width: 800px; display: block; margin: 0 auto !important; background: #ffffff;"> 
        <div id="printDiv">
            <!-- <link rel="stylesheet" type="text/css" href="../assets/css/custom_qo_print_aditya_industries.css"> -->


            <table style="width: 100%; border-collapse: collapse; border: 1px solid #cccccc; font-family: 'Tahoma';">
                <thead>
                    <tr>
                        <td style="width: 30%; border: 1px solid #cccccc; vertical-align: top; padding: 5px;">
                            <h4 style="font-size: 11px; margin: 0; color: #333333; font-weight: 600;">Regd. Office</h4>
                            <h3 style="font-size: 12px; margin: 5px 0 0 0; font-weight: 600;"><?php echo $bill_by_name; ?></h3>
                            <p style="font-size: 11px; color: #363636; font-weight: normal;line-height: 17px; margin: 5px 0 0 0;"><?php echo $bill_by_billing_address; ?>
                            </p>
                        </td>
                        <td style="width: 40%; border: 1px solid #cccccc; text-align: center; padding: 5px;">
                            <img src="uploads/all/<?php echo $config['display_logo']; ?>" style="width:80px;">
                        </td>
                        <td style="width: 30%;border: 1px solid #cccccc; vertical-align: top; padding: 5px;">
                            <h4 style="font-size: 12px; margin: 0 0 10px 0; font-weight: 600;">PURCHASE ORDER(DOM)</h4>
                            <h6 style="font-size: 11px; margin: 5px 0 0 0; font-weight: 500;">PO NO : <?php echo $transaction['transaction_number']; ?></h6>
                            <h6 style="font-size: 11px; margin: 5px 0 0 0; font-weight: 500;">PO Date : <?php echo date("d M Y",strtotime($transaction['transaction_date'])); ?></h6>
                            <h6 style="font-size: 11px; margin: 5px 0 0 0; font-weight: 500;">PO Value : <?php echo currencyFormat($transaction['total_amount'],$config); ?></h6>
                        </td>
                    </tr>
                </thead>
                <tr>
                    <td colspan="2" style="border-right: 1px solid #cccccc; vertical-align: top; padding: 5px;">
                        <p style="margin:5px 0; line-height: 15px;">
                            <span style="font-size:11px;color: #363636;">Vendor Code:</span>
                            <span style="font-size:11px;color: #363636;">NA</span>
                        </p>
                        <p style="margin:5px 0; line-height: 15px;">
                            <span style="font-size:11px;color: #363636;">Vendor Name:</span>
                            <span style="font-size:11px;color: #363636;"><?php echo $Billed_to_party_name; ?></span>
                        </p>
                        <p style="margin:5px 0; white-space: normal; line-height: 15px;">
                            <span style="font-size:11px;color: #363636;">Vendor Address:</span>
                            <span style="font-size:11px;color: #363636; white-space: normal;">
                               <?php echo $Billed_to_party_address; ?>
                            </span>
                        </p>
                        <p style="font-size: 11px; color: #363636; margin: 5px 0;">
                            <span style="font-size:11px;color: #363636;">State Code:</span>
                            <span style="font-size:11px;color: #363636;">NA</span>
                        </p>
                        <p style="font-size: 11px; color: #363636; margin: 5px 0;">
                            <span style="font-size:11px;color: #363636;">GSTIN:</span>
                            <span style="font-size:11px;color: #363636;"><?php echo $Billed_to_gstin; ?></span>
                        </p>
                        <p style="font-size: 11px; color: #363636; margin: 5px 0;">
                            <span style="font-size:11px;color: #363636;">Tel:</span>
                            <span style="font-size:11px;color: #363636;">NA</span>
                        </p>
                        <p style="font-size: 11px; color: #363636; margin: 5px 0;">
                            <span style="font-size:11px;color: #363636;">Email:</span>
                            <span style="font-size:11px;color: #363636;">NA</span>
                        </p>
                    </td>
                    <td style="padding: 0 !important; vertical-align: top;">
                        <table style="width: 100%;">
                            <tr>
                                <td style="font-size: 11px; color: #363636; font-weight: normal; padding: 5px;">Your Reference</td>
                                <td style="font-size: 11px; color: #363636; font-weight: normal; padding: 5px;">:</td>
                                <td style="font-size: 11px; color: #363636; font-weight: normal; padding: 5px;">NA</td>
                            </tr>
                            <tr style="border-bottom: 1px solid #cccccc;">
                                <td style="font-size: 11px; color: #363636; font-weight: normal; padding: 5px;">Revision No & Date</td>
                                <td style="font-size: 11px; color: #363636; font-weight: normal; padding: 5px;">:</td>
                                <td style="font-size: 11px; color: #363636; font-weight: normal; padding: 5px;">NA</td>
                            </tr>
                            <tr>
                                <td style="font-size: 11px; color: #363636; font-weight: normal; padding: 5px;">State Code</td>
                                <td style="font-size: 11px; color: #363636; font-weight: normal; padding: 5px;">:</td>
                                <td style="font-size: 11px; color: #363636; font-weight: normal; padding: 5px;">36</td>
                            </tr>
                            <tr>
                                <td style="font-size: 11px; color: #363636; font-weight: normal; padding: 5px;">GSTIN</td>
                                <td style="font-size: 11px; color: #363636; font-weight: normal; padding: 5px;">:</td>
                                <td style="font-size: 11px; color: #363636; font-weight: normal; padding: 5px;"><?php echo $bill_by_gst_number; ?></td>
                            </tr>
                            <tr>
                                <td style="font-size: 11px; color: #363636; font-weight: normal; padding: 5px;">CIN NO</td>
                                <td style="font-size: 11px; color: #363636; font-weight: normal; padding: 5px;">:</td>
                                <td style="font-size: 11px; color: #363636; font-weight: normal; padding: 5px;">NA</td>
                            </tr>
                            <tr>
                                <td style="font-size: 11px; color: #363636; font-weight: normal; padding: 5px;">Drug License No.</td>
                                <td style="font-size: 11px; color: #363636; font-weight: normal; padding: 5px;">:</td>
                                <td style="font-size: 11px; color: #363636; font-weight: normal; padding: 5px;">NA</td>
                            </tr>
                            <tr>
                                <td style="font-size: 11px; color: #363636; font-weight: normal; padding: 5px;">ECC No</td>
                                <td style="font-size: 11px; color: #363636; font-weight: normal; padding: 5px;">:</td>
                                <td style="font-size: 11px; color: #363636; font-weight: normal; padding: 5px;">NA</td>
                            </tr>
                            <tr>
                                <td style="font-size: 11px; color: #363636; font-weight: normal; padding: 5px;">Range</td>
                                <td style="font-size: 11px; color: #363636; font-weight: normal; padding: 5px;">:</td>
                                <td style="font-size: 11px; color: #363636; font-weight: normal; padding: 5px;">NA</td>
                            </tr>
                            <tr>
                                <td style="font-size: 11px; color: #363636; font-weight: normal; padding: 5px;">Division</td>
                                <td style="font-size: 11px; color: #363636; font-weight: normal; padding: 5px;">:</td>
                                <td style="font-size: 11px; color: #363636; font-weight: normal; padding: 5px;">NA</td>
                            </tr>
                            <tr>
                                <td style="font-size: 11px; color: #363636; font-weight: normal; padding: 5px;">Commissionerate</td>
                                <td style="font-size: 11px; color: #363636; font-weight: normal; padding: 5px;">:</td>
                                <td style="font-size: 11px; color: #363636; font-weight: normal; padding: 5px;">NA</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style="border: 1px solid #cccccc; white-space: normal; padding: 5px;">
                        <p style="font-size: 12px; font-weight: 500; white-space: normal; margin: 0;">Please deliver under mentioned materials in good condition subject to terms and conditions printed on the annexure. Quote this order number, item code and your code number in all deliverable challans, bills and correspondence.</p>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style="padding: 0 !important; vertical-align: top;">
                        <table style="width: 100%">
                            <tr>
                                <th style="font-size: 11px; border: 1px solid #cccccc; padding: 5px;">S.N.</th>
                                <th style="font-size: 11px; border: 1px solid #cccccc; padding: 5px;">Goods / Service Description</th>
                                <th style="font-size: 11px; border: 1px solid #cccccc; padding: 5px;">Qty(UOM)</th>
                                <th style="font-size: 11px; border: 1px solid #cccccc; text-align: center; padding: 5px;">Unit Rate</th>
                                <th style="font-size: 11px; border: 1px solid #cccccc; text-align: center; padding: 5px;">Discount % per Qty</th>
                                <th style="font-size: 11px; border: 1px solid #cccccc; text-align: center; padding: 5px;">CGST/SGST(50:50)(%)</th>
                                <th style="font-size: 11px; border: 1px solid #cccccc; text-align: center; padding: 5px;">CGST/SGST Amount</th>
                                <th style="font-size: 11px; border: 1px solid #cccccc; text-align: center; padding: 5px;">Value</th>
                            </tr>
                            <?php $seq=1; foreach($transaction_items as $ta){ ?>
                            <tr style="vertical-align: top;">
                                <td style="font-size: 11px; border: 1px solid #cccccc; padding: 5px;"><?php echo $seq; ?></td>
                                <td style="border: 1px solid #cccccc; white-space: normal; padding: 5px;">
                                    <p style="font-size: 11px; margin: 0;"><?= nl2br(wordwrap(htmlspecialchars($ta['description']), 50, "\n", true)); ?></p>
                                    <p style="font-size: 11px; margin: 5px 0 0 0;">Material No. - NA HSN Code - <?php echo emptyCheck($parentrowidwithtax[$ta['id']]['tax_value']); ?></p>
                                    <p style="font-size: 11px; margin: 5px 0 0 0;">DelvrySched- NA, Delivery Qty- <?php echo emptyCheck($ta['item_quantity']); ?></p>
                                </td>
                                <td align="right" style="border: 1px solid #cccccc; padding: 5px;">
                                    <p style="font-size: 11px;"><?php echo emptyCheck($ta['item_quantity']); ?></p>
                                </td>
                                <td align="right" style="border: 1px solid #cccccc; padding: 5px;">
                                    <p style="font-size: 11px;"><?php echo currencyFormat($ta['item_price'],$config); ?></p>
                                </td>
                                <td align="right" style="border: 1px solid #cccccc; padding: 5px;">
                                    <p style="font-size: 11px;"><?php echo currencyFormat($ta['discount_amount'],$config); ?></p>
                                </td>
                                <td align="right" style="border: 1px solid #cccccc; padding: 5px;">
                                    <p style="font-size: 11px;"><?php echo emptyCheck($parentrowidwithtax[$ta['id']]['tax_value']); ?></p>
                                </td>
                                <td align="right" style="border: 1px solid #cccccc; padding: 5px;">
                                    <p style="font-size: 11px;"><?php echo currencyFormat($ta['tax_amount'],$config); ?></p>
                                </td>
                                <td align="right" style="border: 1px solid #cccccc; padding: 5px;">
                                    <p style="font-size: 11px; margin: 0;"><?php echo currencyFormat($ta['total_amount'],$config); ?></p>
                                </td>
                            </tr>
                        <?php } ?>
                            <tr style="page-break-before: always; break-before: always;">
                                <td colspan="5" style="border-right: 1px solid #cccccc; vertical-align: top; padding: 5px;">
                                    <h6 style="font-size: 11px; margin: 0; font-weight: 600;">Special Instructions :</h6>
                                    <p style="font-size: 11px; margin: 0;">Quote: Repeat order Ref PO: 4400117671 & 4400116838</p>
                                </td>
                                <td colspan="3" style="padding: 0 !important;">
                                    <table style="width: 100%;">
                                        <tr>
                                            <th style="font-size:11px;border: 1px solid #cccccc; padding: 5px;">Terms</th>
                                            <th style="font-size:11px;border: 1px solid #cccccc; text-align: right; padding: 5px;">Amount</th>
                                        </tr>
                                        <tr>
                                            <th align="left" style="font-size: 11px;border: 1px solid #cccccc; padding: 5px;">Sub Total</th>
                                            <td align="right" style="font-size: 11px;border: 1px solid #cccccc; padding: 5px;"><?php echo currencyFormat($transaction['amount'],$config); ?></td>
                                        </tr>
                                        <tr>
                                            <th align="left" style="font-size: 11px;border: 1px solid #cccccc; padding: 5px;">Discount</th>
                                            <td align="right" style="font-size: 11px;border: 1px solid #cccccc; padding: 5px;"><?php echo currencyFormat($transaction['discount_amount'],$config); ?></td>
                                        </tr>
                                        
                                        <?php  $additional_charges = json_decode($transaction['additional_charges'], 1);
                                         if(count((array)$additional_charges) !="0"){ 
                                            foreach($additional_charges as $adcharge){ ?>
                                        <tr>
                                            <th align="left" style="font-size: 11px;border: 1px solid #cccccc; padding: 5px;"><?php echo $adcharge['charge_name']; ?></th>
                                            <td align="right" style="font-size: 11px;border: 1px solid #cccccc; padding: 5px;"><?php echo currencyFormat(($adcharge['shipping_amount']+$adcharge['shipping_tax_amount']),$config); ?></td>
                                        </tr>
                                        <?php } } ?>

                                        <tr>
                                            <th align="left" style="font-size: 11px;border: 1px solid #cccccc; padding: 5px;">CGST & SGST / IGST</th>
                                            <td align="right" style="font-size: 11px;border: 1px solid #cccccc; padding: 5px;"><?php echo currencyFormat($transaction['tax_amount'],$config); ?></td>
                                        </tr>
                                        <tr>
                                            <th align="left" style="font-size: 11px;border: 1px solid #cccccc; padding: 5px;">Total PO Value (INR)</th>
                                            <td align="right" style="font-size: 11px;border: 1px solid #cccccc; padding: 5px;"><?php echo currencyFormat($transaction['total_amount'],$config); ?></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="1" style="border: 1px solid #cccccc; padding: 5px;">
                        <span style="font-size: 11px; font-weight: 600;">Value in words:</span>
                    </td>
                    <td colspan="2" style="border: 1px solid #cccccc; white-space: normal; padding: 5px;">
                        <span style="font-size: 11px;"><?php echo numberToWordFormate($transaction['total_amount']);?> </span>
                    </td>
                </tr>
                <tr>
                    <td colspan="1" style="border: 1px solid #cccccc; padding: 5px;">
                        <span style="font-size: 11px; font-weight: 600;">Reverse Charge:</span>
                    </td>
                    <td colspan="2" style="border: 1px solid #cccccc; padding: 5px;">
                        <span style="font-size: 11px;">NA</span>
                    </td>
                </tr>
                
                <tr>
                    <td colspan="1" style="border: 1px solid #cccccc; padding: 5px;">
                        <span style="font-size: 11px; font-weight: 600;">Payment Terms:</span>
                    </td>
                    <td colspan="2" style="border: 1px solid #cccccc; padding: 5px;">
                        <span style="font-size: 11px;"><?php echo $payment_terms[$transaction['payment_terms']]['payment_term_name']; ?></span>
                    </td>
                </tr>
                <tr>
                    <td colspan="1" style="border: 1px solid #cccccc; padding: 5px;">
                        <span style="font-size: 11px; font-weight: 600;">Terms of Delivery:</span>
                    </td>
                    <td colspan="2" style="border: 1px solid #cccccc; padding: 5px;">
                        <span style="font-size: 11px;">NA</span>
                    </td>
                </tr>
                <tr>
                    <td style="border: 1px solid #cccccc; vertical-align: top; padding: 5px;">
                        <p style="font-size: 11px; margin: 5px 0; white-space: normal;">
                            <span style="font-weight: 600;">Billing address & Place of delivery :</span>
                            <span ><?php echo $Shipped_to_party_name; ?></span>
                            <span><?php echo $Shipped_to_party_ship_adderss; ?></span>
                        </p>
                        <p style="font-size: 11px; margin: 5px 0;">
                            <span>Pin:</span>
                            <span>NA</span>
                        </p>
                        <p style="font-size: 11px; margin: 5px 0;">
                            <span>Contact No:</span>
                            <span>NA</span>
                        </p>
                    </td>
                    <td style="border: 1px solid #cccccc; vertical-align: top; padding: 5px;">
                        <p style="font-size: 11px; margin: 5px 0; font-weight: 600;">Prepared By</p>
                        <p style="font-size: 11px; margin: 5px 0;"><?php echo $bill_by['display_name']; ?></p>
                        <p style="font-size: 11px; margin: 5px 0;"><?php echo emptyCheck($bill_by['user_email']); ?></p>
                        <p style="font-size:11px;margin: 5px 0;">This is a system generated document</p>
                        <p style="font-size:11px;margin: 5px 0;">
                            <span>Date:</span> 
                            <span><?php echo date("d M Y"); ?></span>
                        </p>
                        <p style="font-size: 11px; margin: 5px 0;">
                            <span>Time:</span>
                            <span><?php echo date("h:m:s"); ?></span>
                        </p>
                    </td>
                    <td style="border: 1px solid #cccccc; vertical-align: top; padding: 5px;">
                        <p style="font-size: 11px; margin: 0;">For <?php echo $config['name']; ?></p>
                        <p style="font-size: 11px; margin: 0;">&nbsp;</p>
                        <p style="font-size: 11px; margin: 0;">&nbsp;</p>
                        <p style="font-size: 11px; margin: 0;">Authorized Signature</p>
                    </td>
                </tr>
                
                <tr style="page-break-before: always; break-before: always;">
                    <td colspan="3" style="border: 1px solid #cccccc; padding: 5px;">
                        <h6 style="font-size: 12px; margin: 0; font-weight: 600;">Additional Item Information</h6>
                        <?php $seq=1; foreach($transaction_items as $ta){ ?>
                        <p style="font-size: 11px; margin: 5px 0; font-weight: 600;"><?= nl2br(wordwrap(htmlspecialchars($ta['description']), 50, "\n", true)); ?></p>
                        <?php } ?>
                    </td>
                </tr>

                <tr style="page-break-before: always; break-before: always;">
                    <td colspan="3" style="border: 1px solid #cccccc; white-space: normal; padding: 5px;">
                        <h6 style="font-size: 11px; margin: 0 0 10px 0; text-align: center; font-weight: 600;">Terms and Conditions</h6>
                        <p style="font-size: 8px; margin: 5px 0;">The Materials or Services specified in this PURCHASE ORDER are subject to the following terms & conditions & are in addition to the instructions & specifications on the Purchase Order</p>
                        <p style="font-size: 8px; margin: 5px 0;">1) Vendor makes all warranties contained in the Uniform Commercial Code and in addition vendor warrants and represents that the materials and/or services described herein shall be of the quality and specifications stated herein. Said materials shall be free from all defects in design and workmanship. All purchases are subject to inspection and rejection notwithstanding prior payment Rejected material will be returned at Vendor's expense for transportation both ways and all related labour and packing costs. No goods returned as defective shall be replaced without the written permission of an authorized agent of BE.
                        </p>
                        <p style="font-size: 8px; margin: 5px 0;">2) Vendor hereby agrees to indemnify and hold harmless BE from all losses, liability, damages and/or expenses which may be sustained or claimed against BE(i)arising out of the user of negligent services or defective material hereby ordered; (ii) arising from injury to vendor employees while in the course of providing services or materials to BE; or (iii) arising from vendor's use of automobiles, trucks, heavy equipment, or other vehicles in the course of providing services or materials to BE Vendor shall at the request of BE defend any such claim action or lawsuit.
                        </p>
                        <p style="font-size: 8px; margin: 5px 0;">3) Vendor agrees to hold in confidence all methods, processes, techniques, shop practices, formulae, compounds, compositions, organisms, equipment, designs, drawings, blueprints, specifications, research data, marketing and sales information, customer lists, plans and information provided or know-how and trade secrets owned by BE or in BE's possession and disclosed to Vendor as a result of this order. Until such information has been published or disclosed to the general public, vendor agrees not to use such information for itself or others or to disclose such information to others and then only with BE's prior written consent.
                        </p>
                        <p style="font-size: 8px; margin: 5px 0;">4) Vendor warrants that all materials furnished under this purchase order, when used by BE in the ordinary manner for which they are intended, shall not violate the provisions of the Occupational Safety and Health Laws or any standards or regulations issued there under.
                        </p>
                        <p style="font-size: 8px; margin: 5px 0;">5) Delivery time constitutes the essence of the contract and Vendor supply the material as per delivery schedule mentioned in the PO. All raw materials , packing materials and consumables should be supplied in single or, minimum possible no. of batches and the total quantities should not vary 10% of the ordered quantities due to pack /lot size. Supplied material should have minimum 80% of the shelflife at the time of shipment unless allowed otherwise in writing.
                        </p>
                        <p style="font-size: 8px; margin: 5px 0;">6) In case of delay in supply of be goods in one lot or of any instalment in case of supply in instalments, BE reserve the right to cancel, modify or change the order, in relation to whole or part of the goods remaining undelivered and/or to claim damages for loss, expenses etc. BE also reserves the right to himself to purchase the goods in the open market and to change the Vendor with any loss incurred as a result thereof. BE may at any time postpone delivery of any of the articles ordered herein for a reasonable time.
                        </p>
                        <p style="font-size: 8px; margin: 5px 0;">7) Further BE may cancel or suspend the order without liability if such ancellation or suspension is caused by non-compliance of any law, regulation, request or imminent action by any government entity.
                        </p>
                        <p style="font-size: 8px; margin: 5px 0;">8) Vendor shall promptly notify BE if Vendor decides or is forced to cease its operations or to discontinue the manufacture of supply of the materials ordered hereunder. In such event, BE may, to the extend possible, purchase additional materials from Vendor under this purchase order in order to meet BE's needs for such materials.
                        </p>
                        <p style="font-size: 8px; margin: 5px 0;">9) Cash discounts, if any, shall be computed as commencing with receipt of the invoice or materials, whichever is later.
                        </p>
                        <p style="font-size: 8px; margin: 5px 0;">10) Vendor guarantees that no article delivered hereunder is adultered or misbranded within the meaning of the Drugs & Cosmetics Act 1940 & The Prevention of Food Adulteration Act 1954, & Rules made hereunder or within the meaning of any applicable state or municipal laws.
                        </p>
                        <p style="font-size: 8px; margin: 5px 0;">11) Any other statement, notice or agreement to the contrary, not withstanding, any written acknowledgement of this purchase order or the shipment of any material or the furnishing of any services pursuant to this purchase order shall constitute acceptance by Vendor of each and all of the expressed terms and conditions stated herein and on the face hereof. This purchase order shall constitute the entire agreement between the Vendor and BE. It may not be modified without prior written acceptance from any authorized buyer of BE. There may be no substitutes, or variations from specifications or instructions or partial shipments, without prior written approval from an authorized buyer of BE. In any or all of the terms and conditions contained herein are not applicable. THE VENDOR SHALL ADVISE BE IN WRITING upon receipt of this order and shall withhold shipment until the matter is adjusted.
                        </p>
                        <p style="font-size: 8px; margin: 5px 0;">12) The Validity and interpretation of the agreement between BE and the Vendor shall include the terms and conditions of this purchase order and shall be governed by the laws applicable in India. All matters, claims and disputes, in respect of the order or the transaction herein shall be subject to the jurisdiction of courts in Hyderabad.
                        </p>
                        <p style="font-size: 8px; margin: 5px 0;">13) Taxes as applicable at the time of supply should be charged in full, if not included the price BE accepts no liability for taxes not charged or undercharged at the time of supply. Wherever applicable, excise gate pass should accompany the consignment.
                        </p>
                        <p style="font-size: 8px; margin: 5px 0;">14) Purchase order reference must be quoted on all challans, invoices etc.</p>
                    </td>
                </tr>
                <tr style="page-break-before: always; break-before: always;">
                    <td colspan="3" style="border: 1px solid #cccccc; white-space: normal; padding: 5px;">
                        <p style="font-size: 8px; margin: 5px 0;">15) Deliveries accepted up to 4.30 pm only on working days.</p>
                        <p style="font-size: 8px; margin: 5px 0;">16) Waiver by BE of any of our rights under these conditions shall not preclude us from acting upon any breach of failure on the part of the vendor in fulfilment of any contract or order.</p>
                        <p style="font-size: 8px; margin: 5px 0;">17) Please note that the Vendor shall cover his employees under ESI Act and Provident Fund Act and the Vendor shall promptly disclose his ESI and P.F Code numbers along with Invoice.</p>
                        <p style="font-size: 8px; margin: 5px 0;">18) As per Clause 171 in the GST bill which provides that it is mandatory to pass on the benefit due to reduction in rate of tax or from input tax credit to the consumer by way of commensurate reduction in prices.</p>
                        <p style="font-size: 8px; margin: 5px 0;">19) In case of failure by the supplier in uploading their invoice in the GST portal within specified timeline or, uploading / providing incorrect information leading to rejection of input tax credit to Biological E ltd, the GST amount charged in the invoice shall be debited to the supplier account.</p>
                        <p style="font-size: 8px; margin: 5px 0;">20) Prevention of Child and Forced Labor: The Company has corporate policies against hiring of workers below the age of 18 years and forced labor. These requirements also apply to all the temporary workers hired by third party contractors. (Support and respect the protection of human rights) and labour (uphold the freedom of association, elimination of forced labour, abolition of child labour and elimination of discrimination).</p>
                        <p style="font-size: 8px; margin: 5px 0;">21) All the Goods should be suitably packed to avoid damage in transit or in storage. Packages must be labelled / marked with the Purchase Order number, destination, contents, quantity, and date, mode of dispatch and weight of each package</p>
                        <p style="font-size: 8px; margin: 5px 0;">22) The certificate of analysis (COA), Material Safety Data Sheet (MSDS) and any other relevant technical document must accompany the material.</p>
                        <p style="font-size: 8px; margin: 5px 0;">23) STATUTORY LAWS: The Supplier ensures that they comply with all central and state laws, rules and regulations related to Environment, occupational Health and Safety, Labour and Human Rights (forced labour and Child labour in particular) and community (health, safety and security) applicable to their business including processing and supply.</p>
                        <p style="font-size: 8px; margin: 5px 0;">24) SOCIAL COMPLIANCE: The Supplier ensures that they comply with all Environment, occupational Health and Safety, Labour and Human Rights (forced labour and Child labour in particular) and community (health, safety and security) laws applicable to their business including processing and supply.</p>
                        <p style="font-size:8px; margin: 10px 0 0 0; font-weight: 600;">ALL WARRANTIES HEREUNDER SHALL ALSO BE CONSTRUED AS CONDITIONS.</p>
                    </td>
                </tr>
            </table>
        </div> 
    </div>
