<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Ereservas
 * @author     Equipos Mecanizados,S.L. <rsainz@emesa.com>
 * @copyright  2019 Equipos Mecanizados,S.L.
 * @license    Licencia Pública General GNU versión 2 o posterior. Consulte LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');
jimport('phpexcel.PHPExcel');
defined('JPATH_PLATFORM') or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;

class EreservasExportacionexcel{
    public static function GenerarInforme($datos, $archivo_nombre){

        $claves = array_keys($datos[0]);
        $letras = range('A', 'Z');
        $conteo= min(count($claves), count($letras));
        $claves_letras=array_combine(array_slice($letras, 0, $conteo), array_slice($claves, 0, $conteo));


        // Instantiate a new PHPExcel object
        $objPHPExcel = new PHPExcel();
        // Set the active Excel worksheet to sheet 0
        $objPHPExcel->setActiveSheetIndex(0);


        foreach ($claves_letras as $letra => $clave) {

            if($clave == 'id_factura') $clave = 'id_pedido';

            $objPHPExcel->getActiveSheet()->SetCellValue($letra . '1', str_replace("_", " ",ucfirst($clave)));
        }
        $rowcount = 2;
        foreach ($datos as $key => $fila) {

            if(@$fila['Código Postal'] == ""){
                if(@$fila['Pais'] === "España"){
                    $fila['cpostalmostrar'] = "Sin código postal";
                }
                else{
                    $fila['cpostalmostrar'] = "Extranjero";
                }
            }else{
                $fila['cpostalmostrar'] = $fila['Código Postal'];
            }

            if(@$fila['fecha_creacion']) {
                $fila['fecha_creacion'] = date('d/m/Y',strtotime($fila['fecha_creacion']));
            }

            foreach ($claves_letras as $letra => $clave) {
                if ($clave == 'Código Postal') {
                    $objPHPExcel->getActiveSheet()->SetCellValue($letra . $rowcount, $fila['cpostalmostrar']);
                } else {
                    $objPHPExcel->getActiveSheet()->SetCellValue($letra . $rowcount, $fila[$clave]);
                }
            }

            $rowcount++;
        }

        $style = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );
        $bordesencabezados = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM
                )
            )
        );
        $bordesnormales = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );
        //Bordes negros
        $FilasaRellenar= $rowcount-1;
        $primera_letra= array_key_first($claves_letras);
        $ultima_letra = array_key_last($claves_letras);

        $objPHPExcel->getDefaultStyle()->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle($primera_letra.'1:'. $ultima_letra .'1')->applyFromArray($bordesencabezados);
        $objPHPExcel->getActiveSheet()->getStyle($primera_letra.'2:'.$ultima_letra.$FilasaRellenar)->applyFromArray($bordesnormales);


        foreach ($claves_letras as $letra => $clave) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
        }

        $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(-1);

        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setFitToHeight(1);

        $archivo = 'exportaciones/'.$archivo_nombre.date('d-m-Y H i').'.xlsx';
        if(file_exists($archivo)) unlink($archivo);
        $archivodesc ='exportaciones/'.$archivo_nombre.'.xlsx';
        if(file_exists($archivodesc)) unlink($archivodesc);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($archivo);
        $objWriter->save($archivodesc);
        return $archivo_nombre;
    }
}