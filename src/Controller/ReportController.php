<?php

namespace App\Controller;

use App\Model\Book;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportController 
{
    public function generateCSV() 
    {
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="books_'.date('Y-m-d').'.csv"');
        
 
        $output = fopen('php://output', 'w');
        
    
        fwrite($output, "\xEF\xBB\xBF");
        
    
        fputcsv($output, [
            'Название',
            'Автор', 
            'Год',
            'Жанр',
            'Цена',
            'Количество'
        ], ';');
        
        $books = Book::getAll();
        
        
        foreach ($books as $book) {
            fputcsv($output, [
                $book['title'],
                $book['author'],
                $book['year'],
                $book['genre'],
                number_format($book['price'], 2, ',', ''),
                $book['quantity']
            ], ';');
        }
        
        fclose($output);
        exit;
    }

    public function generateXLS() 
    {
        $books = Book::getAll();
        
        if (ob_get_level()) ob_end_clean();
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
       
        $sheet->setCellValue('A1', 'Название')
              ->setCellValue('B1', 'Автор')
              ->setCellValue('C1', 'Год')
              ->setCellValue('D1', 'Жанр')
              ->setCellValue('E1', 'Цена')
              ->setCellValue('F1', 'Количество');
        
     
        $row = 2;
        foreach ($books as $book) {
            $sheet->setCellValue('A'.$row, $book['title'])
                  ->setCellValue('B'.$row, $book['author'])
                  ->setCellValue('C'.$row, $book['year'])
                  ->setCellValue('D'.$row, $book['genre'])
                  ->setCellValue('E'.$row, $book['price'])
                  ->setCellValue('F'.$row, $book['quantity']);
            $row++;
        }
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="books_'.date('Y-m-d').'.xlsx"');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    public function generatePDF() {
        $books = Book::getAll();
        
        $html = '<h1>Список книг</h1>
        <table border="1" cellpadding="5">
            <tr>
                <th>Название</th>
                <th>Автор</th>
                <th>Год</th>
                <th>Жанр</th>
                <th>Цена</th>
                <th>Кол-во</th>
            </tr>';
        
        foreach ($books as $book) {
            $html .= '<tr>
                <td>'.htmlspecialchars($book['title']).'</td>
                <td>'.htmlspecialchars($book['author']).'</td>
                <td>'.$book['year'].'</td>
                <td>'.htmlspecialchars($book['genre']).'</td>
                <td>'.$book['price'].'</td>
                <td>'.$book['quantity'].'</td>
            </tr>';
        }
        
        $html .= '</table>';
        
      
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="books_report.pdf"');
        
        $mpdf = new \Mpdf\Mpdf();
        $mpdf->WriteHTML($html);
        $mpdf->Output();
        exit;
    }
}