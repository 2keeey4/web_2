<?php
namespace App\Controller;

use App\Entity\Book;
use App\Entity\Order;
use App\Entity\Orders;
use App\Repository\BookRepository;
use App\Repository\OrdersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Mpdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class BookController extends AbstractController
{
    #[Route('/admin/book', name: 'admin_book')]
    public function adminBook(Request $request, BookRepository $bookRepo): Response
    {
        $session = $request->getSession();
        if (!$session->get('user_role') || !in_array('admin', $session->get('user_role'))) {
            return $this->redirectToRoute('login_form');
        }

        $filter = $request->query->get('filter', '');
        $books = $bookRepo->getFiltered($filter);

        return $this->render('form.twig', [
            'books' => $books,
            'filter' => $filter,
        ]);
    }

    
    #[Route('/user/reports', name: 'user_reports', methods: ['GET'])]
    public function userReports(Request $request, BookRepository $bookRepo): Response
    {
        $session = $request->getSession();
        if (!$session->get('user_role') || !in_array('user', $session->get('user_role'))) {
            return $this->redirectToRoute('login_form');
        }

        $availableBooks = $bookRepo->findAvailableBooks();

        return $this->render('reports.twig', [
            'available_books' => $availableBooks
        ]);
    }

    #[Route('/book/store', name: 'book_store', methods: ['POST'])]
    public function store(Request $request, BookRepository $bookRepo): Response
    {
        $fields = ['book_title', 'book_genre', 'book_author'];
        $data = [];
        $errors = [];

        foreach ($fields as $field) {
            $value = trim($request->request->get($field));
            $data[$field] = $value;
            if (empty($value) || !preg_match('/^[а-яА-ЯёЁa-zA-Z\d,\s\.]+$/u', $value)) {
                $errors[] = "Введите корректное значение для $field.";
            }
        }

        $book_price = trim($request->request->get('book_price'));
        if (!ctype_digit($book_price) || $book_price < 1 || $book_price > 5000) {
            $errors[] = "Введите допустимую цену.";
        } else {
            $data['book_price'] = (int)$book_price;
        }

        $quantity = trim($request->request->get('quantity', '1'));
        if (!ctype_digit($quantity) || $quantity < 1) {
            $errors[] = "Введите допустимое количество (минимум 1).";
        } else {
            $data['quantity'] = (int)$quantity;
        }

        if ($errors) {
            return new Response('<script>alert("' . implode('; ', $errors) . '"); window.location.href="/admin/book";</script>');
        }

        $bookRepo->save(
            $data['book_title'],
            $data['book_genre'],
            $data['book_author'],
            $data['book_price'],
            $data['quantity']
        );

        return $this->render('success.twig');
    }

    #[Route('/order/store', name: 'order_store', methods: ['POST'])]
    public function orderStore(Request $request, BookRepository $bookRepo, OrdersRepository $orderRepo): Response
    {
        $bookId = $request->request->get('book_id');
        $customerName = trim($request->request->get('customer_name'));
        $customerEmail = trim($request->request->get('customer_email'));

        $errors = [];
        
        if (empty($customerName)) {
            $errors[] = "Введите ваше имя.";
        }
        
        if (empty($customerEmail) || !filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Введите корректный email.";
        }
        
        $book = $bookRepo->find($bookId);
        if (!$book || $book->getQuantity() < 1) {
            $errors[] = "Выбранная книга недоступна для заказа.";
        }

        if ($errors) {
            return new Response('<script>alert("' . implode('; ', $errors) . '"); window.location.href="/user/reports";</script>');
        }

        $order = new Orders();
        $order->setCustomerName($customerName);
        $order->setCustomerEmail($customerEmail);
        $order->setBookTitle($book->getBookTitle());
        $order->setBook($book);

        // Уменьшаем количество книг
        $book->setQuantity($book->getQuantity() - 1);

        $orderRepo->save($order);

        return $this->render('order_success.twig');
    }

    #[Route('/export/pdf', name: 'export_pdf')]
    public function exportPdf(BookRepository $bookRepo): Response
    {
        $data = $bookRepo->getAll();

        $mpdf = new Mpdf();

        $html = '<h1>Book Report</h1><table border="1" cellpadding="5"><thead><tr><th>Название</th><th>Жанр</th><th>Автор</th><th>Цена</th><th>Количество</th></tr></thead><tbody>';
        foreach ($data as $book) {
            $html .= sprintf(
                '<tr><td>%s</td><td>%s</td><td>%s</td><td>%d</td><td>%d</td></tr>',
                htmlspecialchars($book->getBookTitle()),
                htmlspecialchars($book->getBookGenre()),
                htmlspecialchars($book->getBookAuthor()),
                $book->getBookPrice(),
                $book->getQuantity()
            );
        }
        $html .= '</tbody></table>';

        $mpdf->WriteHTML($html);

        return new Response(
            $mpdf->Output('', 'S'),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="book_report.pdf"',
            ]
        );
    }

    #[Route('/export/xlsx', name: 'export_xlsx')]
    public function exportXlsx(BookRepository $bookRepo): Response
    {
        $data = $bookRepo->getAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Book');

        $sheet->setCellValue('A1', 'Название');
        $sheet->setCellValue('B1', 'Жанр');
        $sheet->setCellValue('C1', 'Автор');
        $sheet->setCellValue('D1', 'Цена');
        $sheet->setCellValue('E1', 'Количество');

        $row = 2;
        foreach ($data as $book) {
            $sheet->setCellValue("A$row", $book->getBookTitle());
            $sheet->setCellValue("B$row", $book->getBookGenre());
            $sheet->setCellValue("C$row", $book->getBookAuthor());
            $sheet->setCellValue("D$row", $book->getBookPrice());
            $sheet->setCellValue("E$row", $book->getQuantity());
            $row++;
        }

        $writer = new Xlsx($spreadsheet);

        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="book_report.xlsx"');

        return $response;
    }

    #[Route('/export/csv', name: 'export_csv')]
    public function exportCsv(BookRepository $bookRepo): Response
    {
        $data = $bookRepo->getAll();

        $response = new StreamedResponse(function () use ($data) {
            $handle = fopen('php://output', 'w');

            fwrite($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($handle, ['Название', 'Жанр', 'Автор', 'Цена', 'Количество']);

            foreach ($data as $book) {
                fputcsv($handle, [
                    $book->getBookTitle(),
                    $book->getBookGenre(),
                    $book->getBookAuthor(),
                    $book->getBookPrice(),
                    $book->getQuantity()
                ]);
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="book_report.csv"');

        return $response;
    }
}