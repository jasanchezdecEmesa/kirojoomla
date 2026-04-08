<?php
use Joomla\CMS\MVC\Controller\BaseController;

require 'libraries/ereservas/PhpSpreadsheet/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
defined('_JEXEC') or die;
class EreservasControllerExcel extends BaseController
{
    public function exportarReservas(){
        $visitaid=JFactory::getApplication()->input->get('idvisita');
        $reservas = $this->getReservas($visitaid);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $cabeceras=array("Nombre", "Mail", "Personas", "Pagado", "Estado", "Observaciones");
        $num_pos_letras=1;
        $num_pos=1;
        foreach ($cabeceras as $cabecera){
            $spreadsheet->getActiveSheet()->getCellByColumnAndRow($num_pos_letras, $num_pos)->setValue($cabecera);
            $num_pos_letras++;
        }

        $num_pos++;
        foreach ($reservas as $resultado) {
            $num_pos_letras=1;
           foreach ($resultado as $reserva){
               $spreadsheet->getActiveSheet()->getCellByColumnAndRow($num_pos_letras, $num_pos)->setValue($reserva);
               $num_pos_letras++;
           }
            $num_pos++;
        }

        //Creamos Headers
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename=reservas'.$visitaid.".xls");
        header('Cache-Control: max-age=0');
        // Header para IE9
        header('Cache-Control: max-age=1');

        $writer = IOFactory::createWriter($spreadsheet, 'Xls');

        $writer->save('php://output');
    }


    protected function getReservas($visitaid){
        $db2 = JFactory::getDbo();
        $query = $db2->getQuery(true);
        $query->select(array('a.nombre','a.email','a.personas','a.pagado','a.estado','a.observaciones'));
        $query->from($db2->quoteName('#__ereservas_reserva','a'));
        $query->where($db2->quoteName('a.id_visita') . ' = ' . $db2->quote($visitaid));
        $query->where($db2->quoteName('a.estado') . ' <> ' . $db2->quote('cancelado'));
        $query->where($db2->quoteName('a.state') . ' = ' . $db2->quote('1'));
        $query->order($db2->quoteName('a.nombre') . ' ASC');
        $db2->setQuery($query);
        $resultado = $db2->loadAssocList();
        return $resultado;
    }
}
