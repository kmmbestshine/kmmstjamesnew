<?php
if ($_FILES['file_source']['tmp_name']) {   

        // Get our import file extension
        ${'Extension'} = strtolower(array_pop(explode('.', $_FILES['file_source']['name'])));

        // Store our content array
        ${'Accounts'} = array();

        if (${'Extension'} == 'xls' || ${'Extension'} == 'xlsx') {

            // Create a new Excel instance
            if (${'Extension'} == 'xlsx') {
                $objReader = PHPExcel_IOFactory::createReader('Excel2007');
            } else {
                $objReader = PHPExcel_IOFactory::createReader('Excel5');
            }
            $objReader->setReadDataOnly(false);
            $objPHPExcel = $objReader->load($_FILES['file_source']['tmp_name']);
            $objWorksheet = $objPHPExcel->getActiveSheet();

            // Check for the columns and column titles
            if($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, 1)->getValue() != 'Post Title' || $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(1, 1)->getValue() != 'Content' || $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(2, 1)->getValue() != 'Link' || $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(3, 1)->getValue() != 'Link Title' || $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(4, 1)->getValue() != 'Link Description' || $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(5, 1)->getValue() != 'Image'){
                echo json_encode(array('response'=>'fail', 'reason'=>'column_error'));
                exit;   
            }

            // Get the total number of rows in the spreadsheet
            $rows = $objWorksheet->getHighestRow();

            // Check that each row contains all the required data
            $row_errors = 0;
            $row = 1;
            // skip the first row if it has our column names
            for (((($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $row)->getValue()) == 'Post Title') ? $row = 2 :  $row = 1); $row <= $rows; ++$row) {

                // Sanitize all our & add them to the accounts array
                if($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $row)->getValue() == '') { $row_errors++; }
                if($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(1, $row)->getValue() == '') { $row_errors++; }

            }

            if($row_errors>0){
                echo json_encode(array('response'=>'fail', 'reason'=>'required_data'));
                exit;   
            }

            // Loop through all the rows (line items)
            $row = 1;
            ${'Iterator'} = 0;
            // skip the first row if it has our column names
            for (((($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $row)->getValue()) == 'Post Title') ? $row = 2 :  $row = 1); $row <= $rows; ++$row) {

                // Sanitize all our & add them to the accounts array
                ${'Accounts'}[${'Iterator'}] = array('Post Title'       => inputCleanSQL($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $row)->getValue()),
                                                    'Content'           => inputCleanSQL($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(1, $row)->getValue()),
                                                    'Link'              => inputCleanSQL($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(2, $row)->getValue()),
                                                    'Link Title'        => inputCleanSQL($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(3, $row)->getValue()),
                                                    'Link Description'  => inputCleanSQL($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(4, $row)->getValue())
                                             );

                // Check if there is a corresponding image with this row
                foreach ($objPHPExcel->getActiveSheet()->getDrawingCollection() as $drawing) {
                    if ($drawing instanceof PHPExcel_Worksheet_MemoryDrawing) {
                        $cellID = $drawing->getCoordinates();
                        if($cellID == PHPExcel_Cell::stringFromColumnIndex(5).$row){
                            ob_start();
                            call_user_func(
                                $drawing->getRenderingFunction(),
                                $drawing->getImageResource()
                            );
                            $imageContents = ob_get_contents();
                            ob_end_clean();

                            $filetype = $drawing->getMimeType();
                            $filename = md5(microtime());                   

                            switch ($filetype) {

                                case 'image/gif':
                                    $image = imagecreatefromstring($imageContents);
                                    imagegif($image, "/var/www/social/uploads/i/$filename.gif", 100);
                                    $new_file = "$filename.gif";
                                    break;

                                case 'image/jpeg':
                                    $image = imagecreatefromstring($imageContents);
                                    imagejpeg($image, "/var/www/social/uploads/i/$filename.jpeg", 100);
                                    $new_file = "$filename.jpeg";
                                    break;

                                case 'image/png':
                                    $image = imagecreatefromstring($imageContents);
                                    imagepng($image, "/var/www/social/uploads/i/$filename.png", 100);
                                    $new_file = "$filename.png";
                                    break;

                                default:
                                    continue 2;

                            }

                            // Add our image location to the array
                            ${'Accounts'}[${'Iterator'}]['image'] = array('link'=>'http://IMAGECDN/'.$new_file, 'type'=>$filetype, 'name'=>$new_file, 'size'=>filesize('/'.$new_file));

                        }

                    }

                }
                // increase our interator
                ${'Iterator'}++;
            }

        // if not proper file type throw an error   
        } else {
            echo json_encode(array('response'=>'fail', 'reason'=>'invalid_type'));
            exit;   
        }
}

?>