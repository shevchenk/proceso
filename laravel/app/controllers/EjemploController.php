<?php
use PhpOffice\PhpWord\SimpleType\DocProtect;
class EjemploController extends BaseController
{
    public function getEje1()
    {
        error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
        error_reporting($error_level);
        $wordTest = new \PhpOffice\PhpWord\PhpWord();
 
        $newSection = $wordTest->addSection();
     
        $desc1 = "The Portfolio details is a very useful feature of the web page. You can establish your archived details and the works to the entire web community. It was outlined to bring in extra clients, get you selected based on this details.";

        $desc2 = "Probando otro texto :V.<br>\n Hola otra vez :D";
     
        $newSection->addText($desc1, array('name' => 'Tahoma', 'size' => 15, 'color' => 'red'));
        $newSection->addText($desc2);
        $wordTest->save('results/TestWordFile.docx','Word2007');
        $wordTest->save('results/TestWordFile.pdf','PDF');

        return Redirect::to('/results/TestWordFile.pdf');
        //  PDF settings
        /*
        $pdf = App::make('dompdf');
        $pdf->loadHTML('results/TestWordFile.html');
        $pdf->setPaper('a4')->setOrientation('portrait');

        return $pdf->stream();*/
        
        //$wordTest->save('results/TestWordFile.pdf','PDF',true);
        
        try {
        } catch (Exception $e) {
        }
        
        //return Redirect::to('results/TestWordFile.docx');
    }

    public function getEje2()
    {
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('templates/resources/Sample_07_TemplateCloneRow.docx');

        // Variables on different parts of document
        $templateProcessor->setValue('weekday', date('l'));            // On section/content
        $templateProcessor->setValue('time', date('H:i'));             // On footer
        $templateProcessor->setValue('serverName', realpath(__DIR__)); // On header

        $templateProcessor->setValue('nombre', 'Jorge');
        $templateProcessor->setValue('apellido', 'Salcedo');
        // Simple table
        $templateProcessor->cloneRow('rowValue', 10);

        $templateProcessor->setValue('rowValue#1', 'Sun');
        $templateProcessor->setValue('rowValue#2', 'Mercury');
        $templateProcessor->setValue('rowValue#3', 'Venus');
        $templateProcessor->setValue('rowValue#4', 'Earth');
        $templateProcessor->setValue('rowValue#5', 'Mars');
        $templateProcessor->setValue('rowValue#6', 'Jupiter');
        $templateProcessor->setValue('rowValue#7', 'Saturn');
        $templateProcessor->setValue('rowValue#8', 'Uranus');
        $templateProcessor->setValue('rowValue#9', 'Neptun');
        $templateProcessor->setValue('rowValue#10', 'Pluto');

        $templateProcessor->setValue('rowNumber#1', '1');
        $templateProcessor->setValue('rowNumber#2', '2');
        $templateProcessor->setValue('rowNumber#3', '3');
        $templateProcessor->setValue('rowNumber#4', '4');
        $templateProcessor->setValue('rowNumber#5', '5');
        $templateProcessor->setValue('rowNumber#6', '6');
        $templateProcessor->setValue('rowNumber#7', '7');
        $templateProcessor->setValue('rowNumber#8', '8');
        $templateProcessor->setValue('rowNumber#9', '9');
        $templateProcessor->setValue('rowNumber#10', '10');

        // Table with a spanned cell
        $templateProcessor->cloneRow('userId', 3);

        $templateProcessor->setValue('userId#1', '1');
        $templateProcessor->setValue('userFirstName#1', 'James');
        $templateProcessor->setValue('userName#1', 'Taylor');
        $templateProcessor->setValue('userPhone#1', '+1 428 889 773');

        $templateProcessor->setValue('userId#2', '2');
        $templateProcessor->setValue('userFirstName#2', 'Robert');
        $templateProcessor->setValue('userName#2', 'Bell');
        $templateProcessor->setValue('userPhone#2', '+1 428 889 774');

        $templateProcessor->setValue('userId#3', '3');
        $templateProcessor->setValue('userFirstName#3', 'Michael');
        $templateProcessor->setValue('userName#3', 'Ray');
        $templateProcessor->setValue('userPhone#3', '+1 428 889 775');

        $source='results/EjemploClonado.docx';
        $templateProcessor->saveAs($source);

        $phpWord = \PhpOffice\PhpWord\IOFactory::load($source);
        $documentProtection = $phpWord->getSettings()->getDocumentProtection();
        $documentProtection->setEditing(DocProtect::READ_ONLY);
        $documentProtection->setPassword('hola');

        $section = $phpWord->addSection();
        $section->addText('this document is password protected');
        //$phpWord->save($source);
        //return Redirect::to($source);        
        $phpWord->save('results/EjemploClonado.pdf','PDF');
        return Redirect::to('/results/EjemploClonado.pdf');
    }
}
